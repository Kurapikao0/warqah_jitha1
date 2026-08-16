<?php

namespace Tests\Feature\API\Admin;

use App\Models\ActivityLog;
use App\Models\AdminUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ActivityLogControllerTest extends TestCase
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
    public function it_can_list_paginated_activity_logs(): void
    {
        // Arrange
        ActivityLog::factory()->count(3)->create([
            'admin_user_id' => $this->admin->id,
        ]);

        // Act
        $response = $this->getJson('/api/admin/activity-logs');

        // Assert
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'action', 'entity_type', 'created_at']
                     ],
                     'links',
                     'meta',
                 ]);
    }

    #[Test]
    public function it_can_show_a_specific_activity_log(): void
    {
        // Arrange
        $log = ActivityLog::factory()->create([
            'admin_user_id' => $this->admin->id,
        ]);

        // Act
        $response = $this->getJson("/api/admin/activity-logs/{$log->id}");

        // Assert
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJsonPath('data.id', $log->id)
                 ->assertJsonPath('data.action', $log->action);
    }
}
