<?php

namespace App\Policies;

use App\Enums\CustomDesignRequestStatus;
use App\Models\AdminUser;
use App\Models\CustomDesignRequest;
use App\Models\Customer;

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

    public function create(AdminUser|Customer $user): bool
    {
        return true;
    }

    public function update(AdminUser|Customer $user, CustomDesignRequest $customDesignRequest): bool
    {
        if ($user instanceof AdminUser) {
            return true;
        }

        return $customDesignRequest->customer_id === $user->id
            && ! in_array($customDesignRequest->status, [
                CustomDesignRequestStatus::Converted,
                CustomDesignRequestStatus::Rejected,
            ], true);
    }

    public function delete(AdminUser|Customer $user, CustomDesignRequest $customDesignRequest): bool
    {
        if ($user instanceof AdminUser) {
            return true;
        }

        return $customDesignRequest->customer_id === $user->id
            && ! in_array($customDesignRequest->status, [
                CustomDesignRequestStatus::Converted,
                CustomDesignRequestStatus::Rejected,
            ], true);
    }
}
