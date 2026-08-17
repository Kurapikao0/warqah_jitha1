<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\ProductAttributeValue;

class ProductAttributeValuePolicy
{
    public function viewAny(AdminUser $admin): bool
    {
        return true;
    }

    public function view(
        AdminUser $admin,
        ProductAttributeValue $attributeValue
    ): bool {
        return true;
    }

    public function create(AdminUser $admin): bool
    {
        return true;
    }

    public function update(
        AdminUser $admin,
        ProductAttributeValue $attributeValue
    ): bool {
        return true;
    }

    public function delete(
        AdminUser $admin,
        ProductAttributeValue $attributeValue
    ): bool {
        return true;
    }
}
