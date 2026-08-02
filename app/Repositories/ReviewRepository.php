<?php

namespace App\Repositories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Collection;

class ReviewRepository
{
    public function all(): Collection
    {
        return Review::with([
            'customer',
            'product',
            'orderItem'
        ])
        ->latest()
        ->get();
    }


    public function find(int $id): Review
    {
        return Review::with([
            'customer',
            'product',
            'orderItem',
            'images'
        ])
        ->findOrFail($id);
    }


    public function create(array $data): Review
    {
        return Review::create($data);
    }


    public function update(
        Review $review,
        array $data
    ): Review {

        $review->update($data);

        return $review->refresh();
    }


    public function delete(
        Review $review
    ): bool {

        return $review->delete();
    }
}