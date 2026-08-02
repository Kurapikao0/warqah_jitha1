<?php

namespace App\Services;

use App\Models\ActivityLog;

class ActivityLogService
{

    public function paginate()
    {
        return ActivityLog::with('adminUser')
            ->latest('created_at')
            ->paginate();
    }


    public function show(
        ActivityLog $activityLog
    ): ActivityLog {

        return $activityLog->load(
            'adminUser'
        );
    }
}