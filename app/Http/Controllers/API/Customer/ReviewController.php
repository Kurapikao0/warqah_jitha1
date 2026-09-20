<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewService $service
    ) {}

    /**
     * Customer reviews
     */
    public function index(
        Request $request
    ): JsonResponse {

        /** @var Customer $customer */
        $customer = $request->user();

        return response()->json([
            'data' => ReviewResource::collection(
                $customer->reviews()
                    ->with([
                        'product',
                        'images',
                    ])
                    ->latest()
                    ->get()
            ),
        ]);
    }

    /**
     * Store review
     */
    public function store(
        StoreReviewRequest $request
    ): JsonResponse {

        /** @var Customer $customer */
        $customer = $request->user();

        $review = $this->service->create([
            ...$request->validated(),
            'customer_id' => $customer->id,
        ]);

        return response()->json([
            'message' => 'Review created successfully',
            'data' => new ReviewResource(
                $review
            ),
        ], 201);
    }

    /**
     * Store a review using the product route used by the storefront.
     */
    public function storeForProduct(
        Request $request,
        Product $product
    ): JsonResponse {
        /** @var Customer $customer */
        $customer = $request->user();

        $validated = $request->validate([
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
        ]);

        if (!array_key_exists('rating', $validated) && !filled($validated['comment'] ?? null)) {
            throw ValidationException::withMessages([
                'review' => 'يجب اختيار تقييم أو كتابة تعليق.',
            ]);
        }

        $existingReview = $customer->reviews()
            ->withTrashed()
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview !== null) {
            if ($existingReview->trashed()) {
                $existingReview->restore();
            }

            $review = $this->service->update($existingReview, $validated);

            return response()->json([
                'message' => 'Review updated successfully',
                'data' => new ReviewResource($review->load('customer')),
            ]);
        }

        if (!array_key_exists('rating', $validated)) {
            throw ValidationException::withMessages([
                'rating' => 'يجب اختيار التقييم عند إرسال أول تعليق.',
            ]);
        }

        $orderItem = $customer->orders()
            ->where('status', '!=', 'cancelled')
            ->whereHas('items', function ($query) use ($product): void {
                $query->where('product_id', $product->id)
                    ->whereDoesntHave('review');
            })
            ->with(['items' => function ($query) use ($product): void {
                $query->where('product_id', $product->id)
                    ->whereDoesntHave('review');
            }])
            ->latest()
            ->get()
            ->flatMap->items
            ->first();

        if ($orderItem === null) {
            throw ValidationException::withMessages([
                'product_id' => 'يجب شراء المنتج قبل تقييمه.',
            ]);
        }

        try {
            $review = $this->service->create([
                ...$validated,
                'customer_id' => $customer->id,
                'product_id' => $product->id,
                'order_item_id' => $orderItem->id,
            ]);
        } catch (QueryException $exception) {
            if ($exception->getCode() !== '23505') {
                throw $exception;
            }

            $review = $customer->reviews()
                ->withTrashed()
                ->where('product_id', $product->id)
                ->where('order_item_id', $orderItem->id)
                ->first();

            if ($review === null) {
                throw $exception;
            }

            if ($review->trashed()) {
                $review->restore();
            }

            $review = $this->service->update($review, $validated);
        }

        return response()->json([
            'message' => 'Review created successfully',
            'data' => new ReviewResource($review->load('customer')),
        ], 201);
    }

    /**
     * Show review
     */
    public function show(
        Review $review
    ): JsonResponse {

        return response()->json([
            'data' => new ReviewResource(
                $review->load([
                    'customer',
                    'product',
                    'images',
                ])
            ),
        ]);
    }

    /**
     * Update review
     */
    public function update(
        UpdateReviewRequest $request,
        Review $review
    ): JsonResponse {
        $this->authorize('update', $review);

        $review = $this->service->update(
            $review,
            $request->validated()
        );

        return response()->json([
            'message' => 'Review updated successfully',
            'data' => new ReviewResource($review),
        ]);
    }

    /**
     * Delete review
     */
    public function destroy(
        Review $review
    ): JsonResponse {
        $this->authorize('delete', $review);

        $this->service->delete(
            $review
        );

        return response()->json([
            'message' => 'Review deleted successfully',
        ]);
    }
}
