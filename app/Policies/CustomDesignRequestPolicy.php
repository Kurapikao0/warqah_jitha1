<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Customer;
use App\Models\CustomDesignRequest;

class CustomDesignRequestPolicy
{
    public function viewAny(AdminUser $admin): bool
    {
        return true;
    }

    public function view(AdminUser|Customer $user, CustomDesignRequest $customDesignRequest): bool
    {
        if ($user instanceof AdminUser) {
            return true;
        }

        return $user->id === $customDesignRequest->customer_id;
    }

    public function create(Customer $customer): bool
    {
        return true;
    }

    public function update(AdminUser|Customer $user, CustomDesignRequest $customDesignRequest): bool
    {
        if ($user instanceof AdminUser) {
            return true;
        }

        return $customDesignRequest->customer_id === $user->id
            && !in_array($customDesignRequest->status, ['converted', 'rejected']);
    }

    public function delete(AdminUser|Customer $user, CustomDesignRequest $customDesignRequest): bool
    {
        if ($user instanceof AdminUser) {
            return true;
        }

        return $customDesignRequest->customer_id === $user->id
            && !in_array($customDesignRequest->status, ['converted', 'rejected']);
    }
}
