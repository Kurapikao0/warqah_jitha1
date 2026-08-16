<?php

namespace Tests\Unit\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Services\RolePermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RolePermissionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(RolePermissionService::class);
    }

    public function test_can_list_permissions_for_role(): void
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();

        $role->permissions()->attach($permission->id);

        $permissions = $this->service->list($role);

        $this->assertCount(1, $permissions);
        $this->assertTrue($permissions->contains('id', $permission->id));
    }

    public function test_can_attach_permission_to_role(): void
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();

        $this->service->attach($role, $permission);

        $this->assertTrue($role->fresh()->permissions->contains('id', $permission->id));
    }

    public function test_can_detach_permission_from_role(): void
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();

        $role->permissions()->attach($permission->id);

        $this->service->detach($role, $permission);

        $this->assertFalse($role->fresh()->permissions->contains('id', $permission->id));
    }
}