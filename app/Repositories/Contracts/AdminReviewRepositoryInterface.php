<?php

namespace App\Repositories\Contracts;

use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AdminReviewRepositoryInterface
{
    public function getAllPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function updateStatus(Review $review, string $status): Review;

    public function addReply(Review $review, string $reply): Review;

    public function delete(Review $review): bool;
}
