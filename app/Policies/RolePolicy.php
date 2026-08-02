<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the admin can view any roles.
     */
    public function viewAny(AdminUser $admin): bool
    {
        return $this->can($admin, 'roles.view');
    }

    /**
     * Determine whether the admin can view a specific role.
     */
    public function view(AdminUser $admin, Role $role): bool
    {
        return $this->can($admin, 'roles.view');
    }

    /**
     * Determine whether the admin can create roles.
     */
    public function create(AdminUser $admin): bool
    {
        return $this->can($admin, 'roles.create');
    }

    /**
     * Determine whether the admin can update a role.
     */
    public function update(AdminUser $admin, Role $role): bool
    {
        return $this->can($admin, 'roles.update');
    }

    /**
     * Determine whether the admin can delete a role.
     */
    public function delete(AdminUser $admin, Role $role): bool
    {
        // Prevent deleting roles assigned to admin users.
        if ($role->adminUsers()->exists()) {
            return false;
        }

        return $this->can($admin, 'roles.delete');
    }

    /**
     * Restoring roles is not supported.
     */
    public function restore(AdminUser $admin, Role $role): bool
    {
        return false;
    }

    /**
     * Force deleting roles is not allowed.
     */
    public function forceDelete(AdminUser $admin, Role $role): bool
    {
        return false;
    }

    /**
     * Check whether the admin's role contains the required permission.
     */
    protected function can(AdminUser $admin, string $permission): bool
    {
        if (!$admin->role) {
            return false;
        }

        return $admin->role
            ->permissions()
            ->where('name', $permission)
            ->exists();
    }
}