<?php

namespace App\Repositories;

use App\Models\Review;
use App\Repositories\Contracts\AdminReviewRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminReviewRepository implements AdminReviewRepositoryInterface
{
    public function getAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $search = trim((string) ($filters['search'] ?? ''));
        $status = $filters['status'] ?? null;
        $rating = $filters['rating'] ?? null;
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo = $filters['date_to'] ?? null;

        return Review::query()
            ->with(['customer', 'product', 'images'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('comment', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('full_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('product', function ($productQuery) use ($search) {
                            $productQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($rating !== null && $rating !== '', function ($query) use ($rating) {
                $query->where('rating', (int) $rating);
            })
            ->when($dateFrom !== null && $dateFrom !== '', function ($query) use ($dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($dateTo !== null && $dateTo !== '', function ($query) use ($dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
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
