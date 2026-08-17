<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Customer;
use App\Models\Order;

class OrderPolicy
{
    /*
    |--------------------------------------------------------------------------
    | Customer Permissions
    |--------------------------------------------------------------------------
    */

    public function view(
        Customer $customer,
        Order $order
    ): bool {
        return $order->customer_id === $customer->id;
    }

    public function create(
        Customer $customer
    ): bool {
        return true;
    }

    public function update(
        Customer $customer,
        Order $order
    ): bool {

        return
            $order->customer_id === $customer->id
            &&
            in_array(
                $order->status->value,
                [
                    'pending',
                    'received',
                ]
            );

    }

    public function delete(
        Customer $customer,
        Order $order
    ): bool {

        return
            $order->customer_id === $customer->id
            &&
            in_array(
                $order->status->value,
                [
                    'pending',
                    'received',
                ]
            );

    }

    /*
    |--------------------------------------------------------------------------
    | Admin Permissions
    |--------------------------------------------------------------------------
    */

    public function viewAny(
        AdminUser $admin
    ): bool {
        return true;
    }

    public function changeStatus(
        AdminUser $admin,
        Order $order
    ): bool {
        return true;
    }

    public function assignProductionStage(
        AdminUser $admin,
        Order $order
    ): bool {
        return true;
    }
}
