<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Color;

class ColorPolicy
{
    public function viewAny(AdminUser $admin): bool
    {
        return true;
    }

    public function view(
        AdminUser $admin,
        Color $color
    ): bool {
        return true;
    }

    public function create(AdminUser $admin): bool
    {
        return true;
    }

    public function update(
        AdminUser $admin,
        Color $color
    ): bool {
        return true;
    }

    public function delete(
        AdminUser $admin,
        Color $color
    ): bool {
        return true;
    }
}
