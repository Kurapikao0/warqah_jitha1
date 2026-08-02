<?php

namespace App\Services;

use App\Models\Role;
use App\Repositories\RoleRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RoleService
{
    public function __construct(
        protected RoleRepository $roleRepository
    ) {
    }

    /**
     * Return paginated roles with permissions.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->roleRepository->paginate($perPage);
    }

    /**
     * Return a single role with relationships.
     */
    public function show(Role $role): Role
    {
        return $this->roleRepository->findWithRelations($role);
    }

    /**
     * Create a new role and sync permissions.
     *
     * Expected payload:
     * [
     *     'name' => string,
     *     'description' => ?string,
     *     'permissions' => [1,2,3]
     * ]
     */
    public function store(array $data): Role
    {
        return DB::transaction(function () use ($data) {

            $permissions = $data['permissions'] ?? [];
            unset($data['permissions']);

            $role = $this->roleRepository->create($data);

            if (! empty($permissions)) {
                $role->permissions()->sync($permissions);
            }

            return $this->roleRepository->findWithRelations($role);
        });
    }

    /**
     * Update role and synchronize permissions.
     */
    public function update(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data) {

            $permissions = $data['permissions'] ?? null;
            unset($data['permissions']);

            $this->roleRepository->update($role, $data);

            if ($permissions !== null) {
                $role->permissions()->sync($permissions);
            }

            return $this->roleRepository->findWithRelations($role->fresh());
        });
    }

    /**
     * Delete a role.
     *
     * Business Rule:
     * A role assigned to any admin user cannot be deleted.
     */
    public function delete(Role $role): void
    {
        if ($role->adminUsers()->exists()) {
            throw ValidationException::withMessages([
                'role' => [
                    'This role cannot be deleted because it is assigned to one or more administrators.'
                ],
            ]);
        }

        DB::transaction(function () use ($role) {

            $role->permissions()->detach();

            $this->roleRepository->delete($role);
        });
    }
}