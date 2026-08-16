<?php

namespace App\Repositories\Contracts;

interface CartRepositoryInterface
{
    public function getCustomerCart($customerId);

    public function createCart($customerId);

    public function addItem(array $data);

    public function updateItem($item, array $data);

    public function removeItem($item);
}
