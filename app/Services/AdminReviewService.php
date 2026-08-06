<?php

namespace App\Services;

use App\Models\Review;
use App\Repositories\Contracts\AdminReviewRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AdminReviewService
{
    public function __construct(
        protected AdminReviewRepositoryInterface $reviewRepository
    ) {}

    public function getAllReviews(): LengthAwarePaginator
    {
        return $this->reviewRepository->getAllPaginated();
    }

    public function changeStatus(Review $review, string $status): Review
    {
        return DB::transaction(function () use ($review, $status) {
            return $this->reviewRepository->updateStatus($review, $status);
        });
    }

    public function addAdminReply(Review $review, string $reply): Review
    {
        return DB::transaction(function () use ($review, $reply) {
            return $this->reviewRepository->addReply($review, $reply);
        });
    }
}