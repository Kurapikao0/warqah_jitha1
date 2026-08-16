<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\ProductAttribute;

class ProductAttributePolicy
{
    public function viewAny(AdminUser $admin): bool
    {
        return true;
    }

    public function view(
        AdminUser $admin,
        ProductAttribute $attribute
    ): bool {
        return true;
    }

    public function create(AdminUser $admin): bool
    {
        return true;
    }

    public function update(
        AdminUser $admin,
        ProductAttribute $attribute
    ): bool {
        return true;
    }

    public function delete(
        AdminUser $admin,
        ProductAttribute $attribute
    ): bool {
        return true;
    }
}
