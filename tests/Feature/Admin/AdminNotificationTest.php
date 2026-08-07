<?php

namespace Tests\Feature\Admin;

use App\Enums\AdminNotificationType;
use App\Models\AdminNotification;
use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AdminNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Gate::before(fn () => true);

        $this->admin = AdminUser::factory()->create();

        $token = $this->admin->createToken('admin-token')->plainTextToken;
        $this->withHeader('Authorization', "Bearer {$token}");
    }

    public function test_admin_can_list_their_notifications(): void
    {
        AdminNotification::factory()->count(3)->create([
            'admin_user_id' => $this->admin->id,
        ]);

        $response = $this->getJson("/api/admin/admin-users/{$this->admin->id}/notifications");

        $response->assertStatus(200);
    }

    public function test_admin_can_mark_notification_as_read(): void
    {
        $notification = AdminNotification::factory()->create([
            'admin_user_id' => $this->admin->id,
            'is_read'       => false,
        ]);

        $response = $this->putJson("/api/admin/notifications/{$notification->id}/read");

        $response->assertStatus(200);

        $this->assertDatabaseHas('admin_notifications', [
            'id'      => $notification->id,
            'is_read' => true,
        ]);
    }

    public function test_notification_belongs_to_admin_user(): void
    {
        $notification = AdminNotification::factory()->create([
            'admin_user_id' => $this->admin->id,
        ]);

        $this->assertInstanceOf(AdminUser::class, $notification->adminUser);
        $this->assertEquals($this->admin->id, $notification->adminUser->id);
    }

    public function test_notification_casts_type_enum_and_boolean_correctly(): void
    {
        $notification = AdminNotification::factory()->create([
            'admin_user_id' => $this->admin->id,
            'is_read'       => 1,
        ]);

        $this->assertIsBool($notification->is_read);
        $this->assertTrue($notification->is_read);
        $this->assertInstanceOf(AdminNotificationType::class, $notification->type);
    }

    public function test_unauthenticated_user_cannot_access_admin_notifications(): void
    {
        $response = $this->flushHeaders()->getJson("/api/admin/admin-users/{$this->admin->id}/notifications");

        $response->assertStatus(401);
    }
}