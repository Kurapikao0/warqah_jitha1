<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\UploadReviewImageRequest;
use App\Http\Resources\ReviewImageResource;
use App\Models\Review;
use App\Models\ReviewImage;
use App\Services\ReviewImageService;
use Illuminate\Http\JsonResponse;

class ReviewImageController extends Controller
{
    public function __construct(
        protected ReviewImageService $service
    ) {
    }


    public function store(
        UploadReviewImageRequest $request,
        Review $review
    ): ReviewImageResource {

        $image = $this->service->upload(
            $review,
            $request->file('image')
        );

        return new ReviewImageResource($image);
    }


    public function destroy(
        ReviewImage $reviewImage
    ): JsonResponse {

        $this->service->delete($reviewImage);

        return response()->json([
            'message' => 'Review image deleted successfully'
        ]);
    }
}