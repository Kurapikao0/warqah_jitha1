<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Customer;
use App\Models\Review;

class ReviewPolicy
{
    /**
     * تحديد صلاحيات الأدمن
     */
    public function viewAny(AdminUser|Customer $user): bool
    {
        return true;
    }

    public function updateStatus(AdminUser $admin): bool
    {
        return true; // أو التحقق من صلاحية معينة لدى الأدمن
    }

    public function reply(AdminUser $admin): bool
    {
        return true;
    }

    /**
     * تحديد صلاحيات العميل/المستخدم
     */
    public function update(Customer $customer, Review $review): bool
    {
        return $customer->id === $review->customer_id;
    }

    public function delete(AdminUser|Customer $user, Review $review): bool
    {
        if ($user instanceof AdminUser) {
            return true;
        }

        return $user->id === $review->customer_id;
    }
}
