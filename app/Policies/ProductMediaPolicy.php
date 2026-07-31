<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\ProductMedia;

class ProductMediaPolicy
{
    public function viewAny(AdminUser $admin): bool
    {
        return true;
    }

    public function view(
        AdminUser $admin,
        ProductMedia $media
    ): bool {
        return true;
    }

    public function create(
        AdminUser $admin
    ): bool {
        return true;
    }

    public function update(
        AdminUser $admin,
        ProductMedia $media
    ): bool {
        return true;
    }

    public function delete(
        AdminUser $admin,
        ProductMedia $media
    ): bool {
        return true;
    }
}