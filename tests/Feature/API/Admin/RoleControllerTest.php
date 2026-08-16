<?php

namespace Tests\Feature\API\Admin;

use App\Models\AdminUser;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. إنشاء حساب Admin
        $this->admin = AdminUser::factory()->create();

        // 2. السماح بجميع الصلاحيات للـ Admin أثناء الاختبارات
        Gate::before(fn ($user) => true);

        // 3. توثيق الـ Admin عبر Sanctum والـ Guard المخصص
        Sanctum::actingAs($this->admin, ['*'], 'admin');
        $this->actingAs($this->admin, 'admin');
    }

    #[Test]
    public function can_list_roles_with_pagination(): void
    {
        Role::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/roles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name']
                ],
            ]);
    }

    #[Test]
    public function can_create_new_role_with_permissions(): void
    {
        $permissions = Permission::factory()->count(2)->create();

        $payload = [
            'name' => 'Editor',
            'permissions' => $permissions->pluck('id')->toArray(),
        ];

        $response = $this->postJson('/api/admin/roles', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Role created successfully.',
            ]);

        $this->assertDatabaseHas('roles', ['name' => 'Editor']);
    }

    #[Test]
    public function can_show_specific_role_details(): void
    {
        $role = Role::factory()->create();

        $response = $this->getJson("/api/admin/roles/{$role->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                ],
            ]);
    }

    #[Test]
    public function can_update_role_and_its_permissions(): void
    {
        $role = Role::factory()->create(['name' => 'Old Role']);
        $newPermissions = Permission::factory()->count(2)->create();

        $payload = [
            'name' => 'Updated Role',
            'permissions' => $newPermissions->pluck('id')->toArray(),
        ];

        $response = $this->putJson("/api/admin/roles/{$role->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Role updated successfully.',
            ]);

        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'Updated Role']);
    }

    #[Test]
    public function can_delete_role_not_assigned_to_any_admin(): void
    {
        $role = Role::factory()->create();

        $response = $this->deleteJson("/api/admin/roles/{$role->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Role deleted successfully.',
            ]);

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    #[Test]
    public function denies_deleting_role_assigned_to_admins(): void
    {
        // إعادة ضبط Gate للتحقق من المنع عند وجود ارتباط
        Gate::before(fn () => null);

        $role = Role::factory()->create();

        if (method_exists($this->admin, 'roles')) {
            $this->admin->roles()->attach($role->id);
        } elseif (method_exists($this->admin, 'role')) {
            $this->admin->role()->associate($role)->save();
        } else {
            $this->admin->update(['role_id' => $role->id]);
        }

        $response = $this->deleteJson("/api/admin/roles/{$role->id}");

        $this->assertNotEquals(200, $response->status());
        $this->assertTrue(
            in_array($response->status(), [400, 422, 403, 409]),
            "Expected error status (400, 422, 403, 409), got {$response->status()}"
        );
    }
}
