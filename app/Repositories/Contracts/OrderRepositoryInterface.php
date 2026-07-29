<?php

namespace App\Repositories\Contracts;

use App\Models\Order;


interface OrderRepositoryInterface
{

    public function getAll();

    public function getCustomerOrders($customerId);

    public function findById($id);

    public function create(array $data);

    public function update(Order $order,array $data);

}