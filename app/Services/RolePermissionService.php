<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;

class RolePermissionService
{
    public function list(Role $role)
    {
        return $role
            ->permissions()
            ->get();
    }

    public function attach(
        Role $role,
        Permission $permission
    ): void {

        $role
            ->permissions()
            ->syncWithoutDetaching([
                $permission->id,
            ]);
    }

    public function detach(
        Role $role,
        Permission $permission
    ): void {

        $role
            ->permissions()
            ->detach(
                $permission->id
            );
    }

    public function sync(
        Role $role,
        array $permissionIds
    ): void {
        $role->permissions()->sync($permissionIds);
    }
}
