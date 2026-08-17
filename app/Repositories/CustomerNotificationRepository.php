<?php

namespace App\Repositories;

use App\Models\CustomerNotification;
use Illuminate\Database\Eloquent\Collection;

class CustomerNotificationRepository
{
    public function allByCustomer(int $customerId): Collection
    {
        return CustomerNotification::where(
            'customer_id',
            $customerId
        )
            ->latest()
            ->get();
    }

    public function find(
        int $id,
        int $customerId
    ): CustomerNotification {

        return CustomerNotification::where(
            'customer_id',
            $customerId
        )
            ->findOrFail($id);
    }

    public function markAsRead(
        CustomerNotification $notification
    ): CustomerNotification {

        $notification->update([
            'is_read' => true,
        ]);

        return $notification->refresh();
    }

    public function delete(
        CustomerNotification $notification
    ): bool {

        return $notification->delete();
    }
}
