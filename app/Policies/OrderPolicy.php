<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;


class OrderPolicy
{


    /**
     * Admin / Customer can view orders list
     */
    public function viewAny(User $user): bool
    {
        return true;
    }



    /**
     * View single order
     */
    public function view(User $user, Order $order): bool
    {

        // Admin
        if ($user->is_admin ?? false) {
            return true;
        }


        // Customer owns order
        return $order->customer_id === $user->id;

    }




    /**
     * Create new order
     */
    public function create(User $user): bool
    {
        return auth()->check();
    }




    /**
     * Update order
     */
    public function update(User $user, Order $order): bool
    {

        // Admin
        if ($user->is_admin ?? false) {
            return true;
        }


        // Customer can update only pending orders
        return
            $order->customer_id === $user->id
            &&
            $order->status === 'received';

    }





    /**
     * Cancel/Delete order
     */
    public function delete(User $user, Order $order): bool
    {


        if ($user->is_admin ?? false) {
            return true;
        }



        return
            $order->customer_id === $user->id
            &&
            in_array(
                $order->status,
                [
                    'received',
                    'pending'
                ]
            );


    }




    /**
     * Change order status
     * Admin only
     */
    public function changeStatus(User $user): bool
    {

        return $user->is_admin ?? false;

    }


}