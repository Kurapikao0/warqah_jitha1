<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\AdminUser;

class AdminNotificationService
{

    public function paginate(
        AdminUser $adminUser
    ) {

        return AdminNotification::where(
                'admin_user_id',
                $adminUser->id
            )
            ->latest('created_at')
            ->paginate();
    }



    public function markAsRead(
        AdminNotification $notification
    ): AdminNotification {


        $notification->update([

            'is_read'=>true

        ]);


        return $notification;
    }
}