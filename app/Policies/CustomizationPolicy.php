<?php

namespace App\Policies;

use App\Enums\ProductCustomizationRequestStatus;
use App\Models\Customer;
use App\Models\ProductCustomizationRequest;

class CustomizationPolicy
{
    /**
     * View all customization requests.
     */
    public function viewAny(
        Customer $customer
    ): bool {
        return true;
    }

    /**
     * View customization request.
     */
    public function view(
        Customer $customer,
        ProductCustomizationRequest $customization
    ): bool {

        return $customization->customer_id === $customer->id;

    }

    /**
     * Create customization request.
     */
    public function create(
        Customer $customer
    ): bool {
        return true;
    }

    /**
     * Update customization request.
     */
    public function update(
        Customer $customer,
        ProductCustomizationRequest $customization
    ): bool {

        return
            $customization->customer_id === $customer->id
            &&
            $customization->status === ProductCustomizationRequestStatus::PendingApproval;

    }

    /**
     * Delete customization request.
     */
    public function delete(
        Customer $customer,
        ProductCustomizationRequest $customization
    ): bool {

        return
            $customization->customer_id === $customer->id
            &&
            $customization->status === ProductCustomizationRequestStatus::PendingApproval;

    }
}
