<?php

namespace Tests\Unit\Services;

use App\Models\AdminNotification;
use App\Models\AdminUser;
use App\Services\AdminNotificationService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AdminNotificationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AdminNotificationService::class);
    }

    public function test_can_paginate_notifications_for_specific_admin_user(): void
    {
        $adminUser1 = AdminUser::factory()->create();
        $adminUser2 = AdminUser::factory()->create();

        // إنشاء إشعارات للأدمن الأول
        AdminNotification::factory()->count(3)->create([
            'admin_user_id' => $adminUser1->id,
        ]);

        // إنشاء إشعار للأدمن الثاني لتأكيد العزل
        AdminNotification::factory()->create([
            'admin_user_id' => $adminUser2->id,
        ]);

        $result = $this->service->paginate($adminUser1);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(3, $result->total());
    }

    public function test_can_mark_notification_as_read(): void
    {
        $adminUser = AdminUser::factory()->create();
        
        $notification = AdminNotification::factory()->create([
            'admin_user_id' => $adminUser->id,
            'is_read' => false,
        ]);

        $updatedNotification = $this->service->markAsRead($notification);

        $this->assertTrue((bool) $updatedNotification->is_read);
        $this->assertDatabaseHas('admin_notifications', [
            'id' => $notification->id,
            'is_read' => true,
        ]);
    }
}