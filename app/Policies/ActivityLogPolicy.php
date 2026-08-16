<?php

namespace App\Policies;

use App\Models\ActivityLog;
use App\Models\AdminUser;

class ActivityLogPolicy
{
    public function viewAny(
        AdminUser $admin
    ): bool {

        return true;
    }

    public function view(
        AdminUser $admin,
        ActivityLog $activityLog
    ): bool {

        return true;
    }
}
