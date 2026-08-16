<?php

namespace Tests\Feature\API\Admin;

use App\Models\AdminUser;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. إنشاء وتوثيق دفق المستخدم الحالي
        $this->admin = AdminUser::factory()->create();
        Sanctum::actingAs($this->admin, ['*'], 'admin');

        // 2. تجاوز فحص الصلاحيات للمرور المباشر للـ Controller
        Gate::before(fn () => true);
    }

    #[Test]
    public function it_can_list_paginated_permissions(): void
    {
        // Arrange
        Permission::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/api/admin/permissions');

        // Assert
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'name']
                     ],
                     'links',
                     'meta',
                 ]);
    }

    #[Test]
    public function it_can_create_a_new_permission(): void
    {
        // Arrange: إرسال module المطلوبة في قاعدة البيانات
        $payload = [
            'name'   => 'products.manage',
            'module' => 'products',
        ];

        // Act
        $response = $this->postJson('/api/admin/permissions', $payload);

        // Assert
        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Permission created successfully.',
                 ])
                 ->assertJsonStructure(['data' => ['id', 'name']]);

        $this->assertDatabaseHas('permissions', [
            'name'   => 'products.manage',
            'module' => 'products',
        ]);
    }

    #[Test]
    public function it_fails_validation_when_creating_permission_without_required_fields(): void
    {
        // Act
        $response = $this->postJson('/api/admin/permissions', []);

        // Assert
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['name']);
    }

    #[Test]
    public function it_can_show_a_specific_permission(): void
    {
        // Arrange
        $permission = Permission::factory()->create();

        // Act
        $response = $this->getJson("/api/admin/permissions/{$permission->id}");

        // Assert
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'success' => true,
                     'data'    => [
                         'id'   => $permission->id,
                         'name' => $permission->name,
                     ]
                 ]);
    }

    #[Test]
    public function it_can_update_an_existing_permission(): void
    {
        // Arrange
        $permission = Permission::factory()->create();
        $updateData = [
            'name'   => 'orders.update_status',
            'module' => 'orders',
        ];

        // Act
        $response = $this->putJson("/api/admin/permissions/{$permission->id}", $updateData);

        // Assert
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Permission updated successfully.',
                 ]);

        $this->assertDatabaseHas('permissions', [
            'id'     => $permission->id,
            'name'   => 'orders.update_status',
            'module' => 'orders',
        ]);
    }

    #[Test]
    public function it_fails_validation_when_updating_with_existing_permission_name(): void
    {
        // Arrange
        $existingPermission = Permission::factory()->create(['name' => 'existing_permission']);
        $permissionToUpdate = Permission::factory()->create(['name' => 'original_permission']);

        // Act
        $response = $this->putJson("/api/admin/permissions/{$permissionToUpdate->id}", [
            'name'   => 'existing_permission',
            'module' => 'settings',
        ]);

        // Assert
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['name']);
    }

    #[Test]
    public function it_can_delete_a_permission(): void
    {
        // Arrange
        $permission = Permission::factory()->create();

        // Act
        $response = $this->deleteJson("/api/admin/permissions/{$permission->id}");

        // Assert
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Permission deleted successfully.',
                 ]);

        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(Permission::class))) {
            $this->assertSoftDeleted('permissions', ['id' => $permission->id]);
        } else {
            $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
        }
    }
}
