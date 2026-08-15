<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Models\Customer;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewService $service
    ) {
    }


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
                        'images'
                    ])
                    ->latest()
                    ->get()
            )
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
            )
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
                    'images'
                ])
            )
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
            'data' => new ReviewResource($review)
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
            'message' =>
                'Review deleted successfully'
        ]);
    }
}