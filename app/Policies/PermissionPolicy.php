<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Permission;
use App\Models\Role;

class PermissionPolicy
{
    /**
     * Determine whether the admin can view any permissions.
     */
    public function viewAny(AdminUser $admin): bool
    {
        return $this->can($admin, 'permissions.view');
    }

    /**
     * Determine whether the admin can view a specific permission.
     */
    public function view(
        AdminUser $admin,
        Permission $permission
    ): bool {
        return $this->can($admin, 'permissions.view');
    }

    /**
     * Determine whether the admin can create permissions.
     */
    public function create(AdminUser $admin): bool
    {
        return $this->can($admin, 'permissions.create');
    }

    /**
     * Determine whether the admin can update a permission.
     */
    public function update(
        AdminUser $admin,
        Permission $permission
    ): bool {
        return $this->can($admin, 'permissions.update');
    }

    /**
     * Determine whether the admin can delete a permission.
     */
    public function delete(
        AdminUser $admin,
        Permission $permission
    ): bool {
        return $this->can($admin, 'permissions.delete');
    }

    /**
     * Determine whether the admin has the required permission.
     */
    protected function can(
        AdminUser $admin,
        string $permission
    ): bool {
        $role = $admin->role;

        if (! $role instanceof Role) {
            return false;
        }

        return $role
            ->permissions()
            ->where('name', $permission)
            ->exists();
    }
}
