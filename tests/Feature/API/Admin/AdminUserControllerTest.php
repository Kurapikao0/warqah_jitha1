<?php

namespace Tests\Feature\API\Admin;

use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AdminUserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. إنشاء المستخدم الحالي وتوثيق دخوله
        $this->admin = AdminUser::factory()->create();
        Sanctum::actingAs($this->admin, ['*'], 'admin');

        // 2. تجاوز فحص الصلاحيات للمرور المباشر إلى الـ Logic
        Gate::before(fn () => true);
    }

    #[Test]
    public function it_can_list_paginated_admin_users(): void
    {
        // Arrange
        AdminUser::factory()->count(3)->create();

        // Act
        $response = $this->getJson('/api/admin/admin-users');

        // Assert
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'full_name', 'email']
                     ],
                     'links',
                     'meta'
                 ]);
    }

    #[Test]
    public function it_can_create_a_new_admin_user(): void
    {
        // Arrange
        $role = Role::factory()->create();
        $payload = [
            'role_id'   => $role->id,
            'full_name' => 'New Admin User',
            'email'     => 'newadmin@example.com',
            'password'  => 'password123',
            'phone'     => '771234567',
        ];

        // Act
        $response = $this->postJson('/api/admin/admin-users', $payload);

        // Assert
        $response->assertStatus(Response::HTTP_CREATED)
                ->assertJson([
                    'success' => true,
                    'message' => 'Admin user created successfully.',
                ])
                ->assertJsonStructure(['data' => ['id', 'full_name', 'email', 'role']]);

        $this->assertDatabaseHas('admin_users', [
            'email'     => 'newadmin@example.com',
            'full_name' => 'New Admin User',
            'role_id'   => $role->id,
        ]);
    }

    #[Test]
    public function it_fails_validation_when_creating_admin_user_with_missing_fields(): void
    {
        // Act
        $response = $this->postJson('/api/admin/admin-users', []);

        // Assert
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['role_id', 'full_name', 'email', 'password']);
    }

    #[Test]
    public function it_can_show_a_specific_admin_user(): void
    {
        // Arrange
        $targetAdmin = AdminUser::factory()->create();

        // Act
        $response = $this->getJson("/api/admin/admin-users/{$targetAdmin->id}");

        // Assert
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'success' => true,
                     'data'    => [
                         'id'    => $targetAdmin->id,
                         'email' => $targetAdmin->email,
                     ]
                 ]);
    }

    #[Test]
    public function it_can_update_an_existing_admin_user(): void
    {
        // Arrange
        $targetAdmin = AdminUser::factory()->create();
        $updateData = [
            'full_name' => 'Updated Name',
            'phone'     => '731234567',
        ];

        // Act
        $response = $this->putJson("/api/admin/admin-users/{$targetAdmin->id}", $updateData);

        // Assert
        $response->assertStatus(Response::HTTP_OK)
                ->assertJson([
                    'success' => true,
                    'message' => 'Admin user updated successfully.',
                ]);

        $this->assertDatabaseHas('admin_users', [
            'id'        => $targetAdmin->id,
            'full_name' => 'Updated Name',
            'phone'     => '731234567',
        ]);
    }

    #[Test]
    public function it_fails_validation_when_updating_with_existing_email_of_another_user(): void
    {
        // Arrange
        $otherAdmin  = AdminUser::factory()->create(['email' => 'other@example.com']);
        $targetAdmin = AdminUser::factory()->create();

        // Act
        $response = $this->putJson("/api/admin/admin-users/{$targetAdmin->id}", [
            'email' => 'other@example.com',
        ]);

        // Assert
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                 ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function it_can_delete_an_admin_user(): void
    {
        // Arrange
        $targetAdmin = AdminUser::factory()->create();

        // Act
        $response = $this->deleteJson("/api/admin/admin-users/{$targetAdmin->id}");

        // Assert
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Admin user deleted successfully.'
                 ]);

        // الفحص يدعم كل من Soft Deletes أو الحذف النهائي في حال عدم تفعيله
        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(AdminUser::class))) {
            $this->assertSoftDeleted('admin_users', ['id' => $targetAdmin->id]);
        } else {
            $this->assertDatabaseMissing('admin_users', ['id' => $targetAdmin->id]);
        }
    }
}
