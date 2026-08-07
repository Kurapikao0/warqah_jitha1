<?php

namespace Tests\Feature\API\Admin;

use App\Models\AdminPasswordReset;
use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AdminPasswordResetControllerTest extends TestCase
{
    use RefreshDatabase;

    protected AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. إنشاء المستخدم الحالي وتوثيق دخوله
        $this->admin = AdminUser::factory()->create();
        Sanctum::actingAs($this->admin, ['*']);

        // 2. تجاوز فحص الصلاحيات للمرور المباشر إلى الـ Logic
        Gate::before(fn () => true);
    }

    #[Test]
    public function it_can_generate_a_password_reset_code_for_an_admin(): void
    {
        // Arrange
        $targetAdmin = AdminUser::factory()->create();

        // Act
        $response = $this->postJson("/api/admin/admin-users/{$targetAdmin->id}/password-reset");

        // Assert
        $response->assertStatus(Response::HTTP_CREATED)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Password reset code generated.',
                 ])
                 ->assertJsonStructure(['data']);

        $this->assertDatabaseHas('admin_password_resets', [
            'admin_user_id' => $targetAdmin->id,
            'contact_value' => $targetAdmin->email,
        ]);
    }

    #[Test]
    public function it_can_successfully_consume_a_valid_password_reset_token(): void
    {
        // Arrange: إنشاء رمز صالح بضبط صريح للـ Carbon Date في المستقبل (addDay)
        $resetCode = AdminPasswordReset::factory()->create([
            'admin_user_id' => $this->admin->id,
            'consumed_at'   => null,
            'expires_at'    => now()->addDay(),
        ]);

        // Act
        $response = $this->deleteJson("/api/admin/password-resets/{$resetCode->id}");

        // Assert
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Reset token consumed.',
                 ]);

        // التأكد من أن حقل consumed_at تم تحديثه في قاعدة البيانات
        $this->assertNotNull($resetCode->fresh()->consumed_at);
    }

    #[Test]
    public function it_returns_invalid_when_trying_to_consume_an_already_consumed_token(): void
    {
        // Arrange: رمز تم استهلاكه سابقاً
        $resetCode = AdminPasswordReset::factory()->create([
            'admin_user_id' => $this->admin->id,
            'consumed_at'   => now()->subMinute(),
        ]);

        // Act
        $response = $this->deleteJson("/api/admin/password-resets/{$resetCode->id}");

        // Assert
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Reset token invalid.',
                 ]);
    }

    #[Test]
    public function it_returns_invalid_when_trying_to_consume_an_expired_token(): void
    {
        // Arrange: رمز منتهي الصلاحية
        $resetCode = AdminPasswordReset::factory()->create([
            'admin_user_id' => $this->admin->id,
            'expires_at'    => now()->subMinutes(10),
        ]);

        // Act
        $response = $this->deleteJson("/api/admin/password-resets/{$resetCode->id}");

        // Assert
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Reset token invalid.',
                 ]);
    }
}