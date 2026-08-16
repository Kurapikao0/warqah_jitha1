<?php

namespace App\Services;

use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ReviewImageService
{
    public function upload(
        Review $review,
        UploadedFile $image
    ): ReviewImage {

        $path = $image->store(
            'reviews',
            'public'
        );

        return ReviewImage::create([

            'review_id' => $review->id,

            'image_url' => $path,

        ]);

    }

    public function delete(
        ReviewImage $image
    ): bool {

        if ($image->image_url) {

            Storage::disk('public')
                ->delete($image->image_url);

        }

        return $image->delete();

    }
}
