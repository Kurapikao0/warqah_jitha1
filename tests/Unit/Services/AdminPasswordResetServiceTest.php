<?php

namespace Tests\Unit\Services;

use App\Models\AdminPasswordReset;
use App\Models\AdminUser;
use App\Services\AdminPasswordResetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPasswordResetServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AdminPasswordResetService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AdminPasswordResetService::class);
    }

    public function test_can_create_password_reset_token_for_admin_user(): void
    {
        $adminUser = AdminUser::factory()->create();

        $reset = $this->service->create($adminUser);

        $this->assertNotNull($reset);
        $this->assertInstanceOf(AdminPasswordReset::class, $reset);
        
        // التحقق من ربطه بمستخدم الأدمن الصحيح
        $this->assertEquals($adminUser->id, $reset->admin_user_id ?? $reset->admin_id);
    }

    public function test_can_consume_valid_password_reset_token(): void
    {
        $adminUser = AdminUser::factory()->create();
        
        // إنشاء رمز إعادة تعيين بواسطة الخدمة
        $reset = $this->service->create($adminUser);

        // تنفيذ عملية الاستهلاك
        $consumed = $this->service->consume($reset);

        $this->assertTrue((bool)$consumed);
    }
}