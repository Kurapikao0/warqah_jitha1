<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Order;

class OrderStatusPolicy
{
    /**
     * Update order status.
     */
    public function update(
        AdminUser $admin,
        Order $order
    ): bool {
        return true;
    }

    /**
     * Move order to production.
     */
    public function moveToProduction(
        AdminUser $admin,
        Order $order
    ): bool {
        return true;
    }

    /**
     * Mark order as ready.
     */
    public function markAsReady(
        AdminUser $admin,
        Order $order
    ): bool {
        return true;
    }

    /**
     * Deliver order.
     */
    public function deliver(
        AdminUser $admin,
        Order $order
    ): bool {
        return true;
    }

    /**
     * Cancel order.
     */
    public function cancel(
        AdminUser $admin,
        Order $order
    ): bool {
        return true;
    }
}