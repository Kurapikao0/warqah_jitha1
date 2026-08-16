<?php

namespace App\Repositories;

use App\Models\Permission;

class PermissionRepository
{
    public function paginate(int $perPage = 15)
    {
        return Permission::paginate($perPage);
    }

    public function create(array $data): Permission
    {
        return Permission::create($data);
    }

    public function find(Permission $permission): Permission
    {
        return $permission;
    }

    public function update(
        Permission $permission,
        array $data
    ): Permission {

        $permission->update($data);

        return $permission->refresh();
    }

    public function delete(Permission $permission): bool
    {
        return $permission->delete();
    }
}
