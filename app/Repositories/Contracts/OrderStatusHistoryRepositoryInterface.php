<?php

namespace App\Repositories\Contracts;

interface OrderStatusHistoryRepositoryInterface
{
    public function getByOrder($orderId);

    public function create(array $data);
}
