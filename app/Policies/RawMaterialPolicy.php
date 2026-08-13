<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\RawMaterial;

class RawMaterialPolicy
{
    /**
     * Display all raw materials.
     */
    public function viewAny(AdminUser $admin): bool
    {
        return true;
    }

    /**
     * Display raw material.
     */
    public function view(
        AdminUser $admin,
        RawMaterial $rawMaterial
    ): bool {
        return true;
    }

    /**
     * Create raw material.
     */
    public function create(AdminUser $admin): bool
    {
        return true;
    }

    /**
     * Update raw material.
     */
    public function update(
        AdminUser $admin,
        RawMaterial $rawMaterial
    ): bool {
        return true;
    }

    /**
     * Delete raw material.
     */
    public function delete(
        AdminUser $admin,
        RawMaterial $rawMaterial
    ): bool {
        return true;
    }
}
