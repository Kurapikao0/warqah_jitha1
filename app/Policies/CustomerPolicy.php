<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Customer;

class CustomerPolicy
{
    /**
     * عرض قائمة العملاء (للأدمن فقط)
     */
    public function viewAny(AdminUser $admin): bool
    {
        return true;
    }

    /**
     * عرض تفاصيل عميل (الأدمن، أو العميل نفسه)
     */
    public function view(AdminUser|Customer $user, Customer $customer): bool
    {
        if ($user instanceof AdminUser) {
            return true;
        }

        return $user->id === $customer->id;
    }

    /**
     * إنشاء عميل جديد عبر لوحة الأدمن
     */
    public function create(AdminUser $admin): bool
    {
        return true;
    }

    /**
     * تعديل بيانات العميل أو الـ Avatar (الأدمن، أو العميل نفسه)
     */
    public function update(AdminUser|Customer $user, Customer $customer): bool
    {
        if ($user instanceof AdminUser) {
            return true;
        }

        return $user->id === $customer->id;
    }

    /**
     * حذف العميل Soft Delete (للأدمن فقط)
     */
    public function delete(AdminUser $admin, Customer $customer): bool
    {
        return true;
    }

    /**
     * استعادة العميل (للأدمن فقط)
     */
    public function restore(AdminUser $admin, Customer $customer): bool
    {
        return true;
    }

    /**
     * حذف نهائي (مغلق)
     */
    public function forceDelete(AdminUser $admin, Customer $customer): bool
    {
        return false;
    }

    /**
     * تغيير حالة العميل (للأدمن فقط)
     */
    public function changeStatus(AdminUser $admin, Customer $customer): bool
    {
        return true;
    }

    /**
     * تفعيل حساب العميل (للأدمن فقط)
     */
    public function verify(AdminUser $admin, Customer $customer): bool
    {
        return true;
    }
}