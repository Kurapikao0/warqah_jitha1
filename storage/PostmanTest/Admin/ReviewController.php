<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Review\UpdateReviewStatusRequest;
use App\Http\Requests\Admin\Review\ReplyToReviewRequest;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use App\Services\AdminReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ReviewController extends Controller
{
    public function __construct(
        protected AdminReviewService $reviewService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Review::class);

        $reviews = $this->reviewService->getAllReviews();

        return ReviewResource::collection($reviews);
    }

    public function updateStatus(UpdateReviewStatusRequest $request, Review $review): JsonResponse
    {
        $this->authorize('updateStatus', $review);

        $updatedReview = $this->reviewService->changeStatus(
            $review, 
            $request->validated('status')
        );

        return response()->json([
            'message' => 'تم تحديث حالة المراجعة بنجاح.',
            'data'    => new ReviewResource($updatedReview),
        ], 200);
    }

    public function reply(ReplyToReviewRequest $request, Review $review): JsonResponse
    {
        $this->authorize('reply', $review);

        $updatedReview = $this->reviewService->addAdminReply(
            $review, 
            $request->validated('admin_reply')
        );

        return response()->json([
            'message' => 'تم إضافة رد الأدمن بنجاح.',
            'data'    => new ReviewResource($updatedReview),
        ], 200);
    }
}