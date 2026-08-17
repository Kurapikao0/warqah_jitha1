<?php

namespace Tests\Unit\Services;

use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PermissionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PermissionService::class);
    }

    public function test_can_paginate_permissions(): void
    {
        Permission::factory()->count(5)->create();

        $result = $this->service->paginate();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertGreaterThanOrEqual(5, $result->total());
    }

    public function test_can_store_permission(): void
    {
        $data = [
            'name' => 'manage-users',
            'module' => 'users',
            'description' => 'Allows managing all admin users',
        ];

        $permission = $this->service->store($data);

        $this->assertInstanceOf(Permission::class, $permission);
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'manage-users',
            'module' => 'users',
        ]);
    }

    public function test_can_update_permission(): void
    {
        $permission = Permission::factory()->create([
            'name' => 'old-permission-name',
        ]);

        $updateData = [
            'name' => 'updated-permission-name',
        ];

        $updatedPermission = $this->service->update($permission, $updateData);

        $this->assertEquals('updated-permission-name', $updatedPermission->name);
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'updated-permission-name',
        ]);
    }

    public function test_can_delete_permission(): void
    {
        $permission = Permission::factory()->create();

        $this->service->delete($permission);

        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(Permission::class))) {
            $this->assertSoftDeleted('permissions', [
                'id' => $permission->id,
            ]);
        } else {
            $this->assertDatabaseMissing('permissions', [
                'id' => $permission->id,
            ]);
        }
    }
}
