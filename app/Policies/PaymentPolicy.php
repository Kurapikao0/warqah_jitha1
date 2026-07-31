<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Customer;
use App\Models\Payment;

class PaymentPolicy
{
    /*
    |--------------------------------------------------------------------------
    | Customer
    |--------------------------------------------------------------------------
    */

    /**
     * View payment.
     */
    public function view(
        Customer $customer,
        Payment $payment
    ): bool {

        return
            $payment->order->customer_id === $customer->id;

    }

    /**
     * Upload payment.
     */
    public function create(
        Customer $customer
    ): bool {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    /**
     * View all payments.
     */
    public function viewAny(
        AdminUser $admin
    ): bool {
        return true;
    }

    /**
     * Approve payment.
     */
    public function approve(
        AdminUser $admin,
        Payment $payment
    ): bool {
        return true;
    }

    /**
     * Reject payment.
     */
    public function reject(
        AdminUser $admin,
        Payment $payment
    ): bool {
        return true;
    }

    /**
     * Update payment.
     */
    public function update(
        AdminUser $admin,
        Payment $payment
    ): bool {
        return true;
    }

    /**
     * Delete payment.
     */
    public function delete(
        AdminUser $admin,
        Payment $payment
    ): bool {
        return true;
    }
}