<?php

namespace App\Services;

use App\Repositories\Contracts\OrderStatusHistoryRepositoryInterface;

class OrderStatusHistoryService
{
    public function __construct(
        protected OrderStatusHistoryRepositoryInterface $repository
    ) {}

    public function orderHistory($orderId)
    {

        return $this->repository
            ->getByOrder($orderId);

    }

    public function create(array $data)
    {

        return $this->repository
            ->create($data);

    }
}
