<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use App\Models\OrderItem;

interface OrderRepositoryInterface
{
    public function getAll();

    public function getCustomerOrders($customerId);

    public function findById($id);

    public function create(array $data);

    public function createItem(array $data): OrderItem;

    public function update(Order $order, array $data);

    public function findCustomerOrder(int $customerId, int $orderId);

    public function statistics();
}
