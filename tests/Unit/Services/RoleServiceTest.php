<?php

namespace Tests\Unit\Services;

use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RoleService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(RoleService::class);
    }

    public function test_can_paginate_roles(): void
    {
        Role::factory()->count(5)->create();

        $result = $this->service->paginate();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertGreaterThanOrEqual(5, $result->total());
    }

    public function test_can_store_role(): void
    {
        $data = [
            'name' => 'super-admin',
            'description' => 'Super administrator role with all permissions',
        ];

        $role = $this->service->store($data);

        $this->assertInstanceOf(Role::class, $role);
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'super-admin',
        ]);
    }

    public function test_can_show_role_details(): void
    {
        $role = Role::factory()->create();

        $result = $this->service->show($role);

        $this->assertInstanceOf(Role::class, $result);
        $this->assertEquals($role->id, $result->id);
    }

    public function test_can_update_role(): void
    {
        $role = Role::factory()->create([
            'name' => 'old-role-name',
        ]);

        $updateData = [
            'name' => 'updated-role-name',
        ];

        $updatedRole = $this->service->update($role, $updateData);

        $this->assertEquals('updated-role-name', $updatedRole->name);
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'updated-role-name',
        ]);
    }

    public function test_can_delete_role(): void
    {
        $role = Role::factory()->create();

        $this->service->delete($role);

        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(Role::class))) {
            $this->assertSoftDeleted('roles', [
                'id' => $role->id,
            ]);
        } else {
            $this->assertDatabaseMissing('roles', [
                'id' => $role->id,
            ]);
        }
    }
}