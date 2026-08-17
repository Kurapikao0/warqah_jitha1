<?php

namespace App\Repositories;

use App\Models\Role;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RoleRepository
{
    /**
     * Paginate roles with their permissions.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Role::query()
            ->with('permissions')
            ->withCount('adminUsers')
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Retrieve a role with all required relationships.
     */
    public function findWithRelations(Role $role): Role
    {
        return $role->load([
            'permissions',
            'adminUsers',
        ]);
    }

    /**
     * Create a new role.
     */
    public function create(array $attributes): Role
    {
        return Role::create([
            'name' => $attributes['name'],
            'description' => $attributes['description'] ?? null,
        ]);
    }

    /**
     * Update an existing role.
     */
    public function update(Role $role, array $attributes): Role
    {
        $role->update([
            'name' => $attributes['name'] ?? $role->name,
            'description' => $attributes['description'] ?? $role->description,
        ]);

        return $role->refresh();
    }

    /**
     * Delete the role.
     */
    public function delete(Role $role): bool
    {
        return (bool) $role->delete();
    }

    /**
     * Check if another role already uses the given name.
     */
    public function nameExists(string $name, ?int $ignoreId = null): bool
    {
        return Role::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('name', $name)
            ->exists();
    }

    /**
     * Find a role by its primary key.
     */
    public function findById(int $id): ?Role
    {
        return Role::query()
            ->with(['permissions', 'adminUsers'])
            ->find($id);
    }
}
