<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartItem;
use App\Repositories\Contracts\CartRepositoryInterface;

class CartRepository implements CartRepositoryInterface
{
    public function getCustomerCart($customerId)
    {

        return Cart::with([
            'items.product.media',
        ])
            ->where(
                'customer_id',
                $customerId
            )
            ->first();

    }

    public function createCart($customerId)
    {

        return Cart::create([

            'customer_id' => $customerId,

        ]);

    }

    public function addItem(array $data)
    {

        return CartItem::create($data);

    }

    public function updateItem(
        $item,
        array $data
    ) {

        return $item->update($data);

    }

    public function removeItem($item)
    {

        return $item->delete();

    }
}
