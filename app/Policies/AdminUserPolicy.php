<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Role;

class AdminUserPolicy
{
    public function viewAny(AdminUser $admin): bool
    {
        return $this->can(
            $admin,
            'admin_users.view'
        );
    }

    public function view(
        AdminUser $admin,
        AdminUser $model
    ): bool {

        return $this->can(
            $admin,
            'admin_users.view'
        );
    }

    public function create(
        AdminUser $admin
    ): bool {

        return $this->can(
            $admin,
            'admin_users.create'
        );
    }

    public function update(
        AdminUser $admin,
        AdminUser $model
    ): bool {

        return $this->can(
            $admin,
            'admin_users.update'
        );
    }

    public function delete(
        AdminUser $admin,
        AdminUser $model
    ): bool {

        if (
            AdminUser::count() <= 1
        ) {
            return false;
        }

        return $this->can(
            $admin,
            'admin_users.delete'
        );
    }

    protected function can(
        AdminUser $admin,
        string $permission
    ): bool {

        $role = $admin->role;

        if (! $role instanceof Role) {
            return false;
        }

        if ($role->name === 'super-admin') {
            return true;
        }

        return $role
            ->permissions()
            ->where('name', $permission)
            ->exists();
    }
}
