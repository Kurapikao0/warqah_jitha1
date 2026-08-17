<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Models\Review;
use App\Repositories\ReviewRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class ReviewService
{
    public function __construct(
        protected ReviewRepository $repository
    ) {}

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function getById(int $id): Review
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Review
    {
        $orderItem = OrderItem::findOrFail($data['order_item_id']);

        if ($orderItem->order->customer_id !== $data['customer_id']) {
            throw new Exception('Order item does not belong to customer');
        }

        return $this->repository->create($data);
    }

    public function update(
        Review $review,
        array $data
    ): Review {

        return $this->repository->update(
            $review,
            $data
        );
    }

    public function delete(
        Review $review
    ): bool {

        return $this->repository->delete($review);
    }
}
