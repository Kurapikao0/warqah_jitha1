<?php

namespace App\Repositories\Contracts;

interface CartRepositoryInterface
{
    public function getCustomerCart($customerId);

    public function createCart($customerId);

    public function addItem(array $data);

    public function updateItem($item, array $data);

    public function removeItem($item);

    public function clearCart($customerId);

    public function renewReservation($customerId, int $durationMinutes = 5);

    public function renewItemReservation($item, int $durationMinutes = 5);

    public function syncCart($customerId, array $items);
}
