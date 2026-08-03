<?php

namespace Tests\Unit\Services;

use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ActivityLogService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ActivityLogService::class);
    }

    public function test_can_paginate_activity_logs(): void
    {
        ActivityLog::factory()->count(5)->create();

        $result = $this->service->paginate();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertGreaterThanOrEqual(5, $result->total());
    }

    public function test_can_show_activity_log_details(): void
    {
        $log = ActivityLog::factory()->create();

        $result = $this->service->show($log);

        $this->assertInstanceOf(ActivityLog::class, $result);
        $this->assertEquals($log->id, $result->id);
    }
}