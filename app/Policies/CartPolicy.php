<?php

namespace App\Policies;

use App\Models\Cart;
use App\Models\Customer;

class CartPolicy
{
    /**
     * Determine whether the customer can view the cart.
     */
    public function view(
        Customer $customer,
        Cart $cart
    ): bool {
        return $cart->customer_id === $customer->id;
    }

    /**
     * Determine whether the customer can create a cart.
     */
    public function create(
        Customer $customer
    ): bool {
        return true;
    }

    /**
     * Determine whether the customer can update the cart.
     */
    public function update(
        Customer $customer,
        Cart $cart
    ): bool {
        return $cart->customer_id === $customer->id;
    }

    /**
     * Determine whether the customer can delete the cart.
     */
    public function delete(
        Customer $customer,
        Cart $cart
    ): bool {
        return $cart->customer_id === $customer->id;
    }
}
