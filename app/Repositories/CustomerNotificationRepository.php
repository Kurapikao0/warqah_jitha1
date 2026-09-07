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

    public function unreadCount(int $customerId): int
    {
        return CustomerNotification::where('customer_id', $customerId)
            ->where('is_read', false)
            ->count();
    }

    public function markAllAsRead(int $customerId): Collection
    {
        CustomerNotification::where('customer_id', $customerId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $this->allByCustomer($customerId);
    }

    public function delete(
        CustomerNotification $notification
    ): bool {

        return $notification->delete();
    }

    public function create(array $data): CustomerNotification
    {
        return CustomerNotification::create($data);
    }
}
