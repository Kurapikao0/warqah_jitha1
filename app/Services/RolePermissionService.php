<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Permission;

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
                $permission->id
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
}