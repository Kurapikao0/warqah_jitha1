<?php

namespace Tests\Unit\Services;

use App\Models\AdminUser;
use App\Models\Role;
use App\Services\AdminUserService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AdminUserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AdminUserService::class);
    }

    public function test_can_paginate_admin_users(): void
    {
        AdminUser::factory()->count(5)->create();

        $result = $this->service->paginate();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertGreaterThanOrEqual(5, $result->total());
    }

    public function test_can_create_admin_user_and_hashes_password(): void
    {
        $role = Role::factory()->create();

        $data = [
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
            'role_id' => $role->id,
        ];

        $adminUser = $this->service->create($data);

        $this->assertInstanceOf(AdminUser::class, $adminUser);

        $this->assertDatabaseHas('admin_users', [
            'id' => $adminUser->id,
            'email' => 'john@example.com',
            'full_name' => 'John Doe',
        ]);

        $this->assertTrue(Hash::check('secret123', $adminUser->password_hash));
    }

    public function test_can_update_admin_user_details_and_optional_password(): void
    {
        $adminUser = AdminUser::factory()->create([
            'full_name' => 'Old Name',
            'password_hash' => Hash::make('oldpassword'),
        ]);

        $updateData = [
            'full_name' => 'New Updated Name',
            'password' => 'newsecret123',
        ];

        $updatedAdmin = $this->service->update($adminUser, $updateData);

        $this->assertEquals('New Updated Name', $updatedAdmin->full_name);
        $this->assertTrue(Hash::check('newsecret123', $updatedAdmin->password_hash));
    }

    public function test_can_delete_admin_user(): void
    {
        $adminUser = AdminUser::factory()->create();

        $this->service->delete($adminUser);

        // إذا كان الجدول يستخدم Soft Deletes أو الحذف الفعلي
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(AdminUser::class))) {
            $this->assertSoftDeleted('admin_users', [
                'id' => $adminUser->id,
            ]);
        } else {
            $this->assertDatabaseMissing('admin_users', [
                'id' => $adminUser->id,
            ]);
        }
    }
}
