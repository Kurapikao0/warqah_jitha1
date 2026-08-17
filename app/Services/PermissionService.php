<?php

namespace App\Services;

use App\Models\Permission;
use App\Repositories\PermissionRepository;

class PermissionService
{
    public function __construct(
        protected PermissionRepository $repository
    ) {}

    public function paginate()
    {
        return $this->repository->paginate();
    }

    public function store(array $data): Permission
    {
        return $this->repository->create($data);
    }

    public function show(Permission $permission): Permission
    {
        return $this->repository->find($permission);
    }

    public function update(
        Permission $permission,
        array $data
    ): Permission {

        return $this->repository->update(
            $permission,
            $data
        );
    }

    public function delete(Permission $permission): bool
    {
        return $this->repository->delete($permission);
    }
}
