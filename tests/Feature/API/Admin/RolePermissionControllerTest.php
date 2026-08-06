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

class RolePermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminUser::factory()->create();

        Gate::before(fn ($user) => true);

        Sanctum::actingAs($this->admin, ['*'], 'sanctum');
        $this->actingAs($this->admin, 'sanctum');
    }

    #[Test]
    public function يمكن_عرض_قائمة_صلاحيات_دور_معين(): void
    {
        $role = Role::factory()->create();
        $permissions = Permission::factory()->count(2)->create();

        $role->permissions()->attach($permissions->pluck('id'));

        $response = $this->getJson("/api/admin/roles/{$role->id}/permissions");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    #[Test]
    public function يمكن_إسناد_صلاحية_جديدة_لدور(): void
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();

        $payload = [
            'permission_id' => $permission->id,
        ];

        $response = $this->postJson("/api/admin/roles/{$role->id}/permissions", $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Permission assigned successfully.',
            ]);

        // التحقق عبر العلاقة مباشرة
        $this->assertTrue($role->fresh()->permissions->contains($permission->id));
    }

    #[Test]
    public function إعادة_إسناد_صلاحية_موجودة_بالفعل_لا_يسبب_تكراراً(): void
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();

        $role->permissions()->attach($permission->id);

        $payload = [
            'permission_id' => $permission->id,
        ];

        $response = $this->postJson("/api/admin/roles/{$role->id}/permissions", $payload);

        $response->assertStatus(201);

        // التأكد أن الصلاحيات لا تتكرر لنفس الدور
        $this->assertEquals(1, $role->fresh()->permissions()->where('permission_id', $permission->id)->count());
    }

    #[Test]
    public function يرفض_إسناد_صلاحية_بدون_تمرير_الحقول_المطلوبة(): void
    {
        $role = Role::factory()->create();

        $response = $this->postJson("/api/admin/roles/{$role->id}/permissions", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['permission_id']);
    }

    #[Test]
    public function يمكن_إزالة_صلاحية_من_دور(): void
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();

        $role->permissions()->attach($permission->id);

        $response = $this->deleteJson("/api/admin/roles/{$role->id}/permissions/{$permission->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Permission removed successfully.',
            ]);

        // التأكد من حذف الصلاحية من العلاقة
        $this->assertFalse($role->fresh()->permissions->contains($permission->id));
    }

    #[Test]
    public function يرجع_404_عند_استدعاء_دور_أو_صلاحية_غير_موجودة(): void
    {
        $role = Role::factory()->create();

        $response = $this->deleteJson("/api/admin/roles/{$role->id}/permissions/99999");

        $response->assertStatus(404);
    }
}