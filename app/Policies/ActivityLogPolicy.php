<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\ActivityLog;

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