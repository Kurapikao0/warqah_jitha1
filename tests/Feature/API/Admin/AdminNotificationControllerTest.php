<?php

namespace Tests\Feature\API\Admin;

use App\Models\AdminNotification;
use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AdminNotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    protected AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. إنشاء المستخدم الحالي وتوثيق دخوله
        $this->admin = AdminUser::factory()->create();
        Sanctum::actingAs($this->admin, ['*'], 'admin');
        // 2. تجاوز فحص الصلاحيات للمرور المباشر
        Gate::before(fn () => true);
    }

    #[Test]
    public function it_can_list_notifications_for_a_specific_admin_user(): void
    {
        // Arrange
        AdminNotification::factory()->count(3)->create([
            'admin_user_id' => $this->admin->id,
        ]);

        // Act
        $response = $this->getJson("/api/admin/admin-users/{$this->admin->id}/notifications");

        // Assert
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'is_read', 'created_at'],
                ],
                'links',
                'meta',
            ]);
    }

    #[Test]
    public function it_can_mark_a_notification_as_read(): void
    {
        // Arrange
        $notification = AdminNotification::factory()->create([
            'admin_user_id' => $this->admin->id,
            'is_read' => false,
        ]);

        // Act - جربي putJson (أو postJson إذا استمر الخطأ)
        $response = $this->putJson("/api/admin/notifications/{$notification->id}/read");

        // Assert
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'Notification marked as read.',
            ]);

        $this->assertDatabaseHas('admin_notifications', [
            'id' => $notification->id,
            'is_read' => true,
        ]);
    }
}
