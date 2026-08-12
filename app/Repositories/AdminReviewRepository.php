<?php

namespace App\Repositories;

use App\Models\Review;
use App\Repositories\Contracts\AdminReviewRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminReviewRepository implements AdminReviewRepositoryInterface
{
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Review::with(['customer', 'product', 'images'])
            ->latest()
            ->paginate($perPage);
    }

    public function updateStatus(Review $review, string $status): Review
    {
        $review->update([
            'status' => $status,
        ]);

        return $review->fresh();
    }

    public function addReply(Review $review, string $reply): Review
    {
        $review->update([
            'admin_reply'    => $reply,
            'admin_reply_at' => now(),
        ]);

        return $review->fresh();
    }

    public function delete(Review $review): bool
    {
        return (bool) $review->delete();
    }
}