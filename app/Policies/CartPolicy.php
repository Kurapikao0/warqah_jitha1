<?php

namespace App\Policies;


use App\Models\Cart;
use App\Models\User;



class CartPolicy
{


    /**
     * View Cart
     */
    public function view(
        User $user,
        Cart $cart
    ): bool
    {


        return
            $cart->customer_id === $user->id;


    }





    /**
     * Add Item To Cart
     */
    public function create(
        User $user
    ): bool
    {

        return auth()->check();

    }





    /**
     * Update Cart Item
     */
    public function update(
        User $user,
        Cart $cart
    ): bool
    {


        return
            $cart->customer_id === $user->id;


    }





    /**
     * Delete Cart Item
     */
    public function delete(
        User $user,
        Cart $cart
    ): bool
    {


        return
            $cart->customer_id === $user->id;


    }


}