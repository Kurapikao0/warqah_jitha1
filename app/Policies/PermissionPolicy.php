<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Permission;

class PermissionPolicy
{
    public function viewAny(AdminUser $admin): bool
    {
        return $this->can($admin, 'permissions.view');
    }


    public function view(AdminUser $admin, Permission $permission): bool
    {
        return $this->can($admin, 'permissions.view');
    }


    public function create(AdminUser $admin): bool
    {
        return $this->can($admin, 'permissions.create');
    }


    public function update(AdminUser $admin, Permission $permission): bool
    {
        return $this->can($admin, 'permissions.update');
    }


    public function delete(AdminUser $admin, Permission $permission): bool
    {
        return $this->can($admin, 'permissions.delete');
    }


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