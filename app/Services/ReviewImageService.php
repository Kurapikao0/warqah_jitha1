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
            'image_url' => Storage::url($path),
        ]);
    }


    public function delete(
        ReviewImage $image
    ): bool {

        if ($image->image_url) {

            $path = str_replace(
                '/storage/',
                '',
                $image->image_url
            );

            Storage::disk('public')->delete($path);
        }

        return $image->delete();
    }
}