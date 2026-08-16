<?php

namespace App\Services;

use App\Models\CustomerNotification;
use App\Repositories\CustomerNotificationRepository;
use Illuminate\Database\Eloquent\Collection;

class CustomerNotificationService
{
    public function __construct(
        protected CustomerNotificationRepository $repository
    ) {}

    public function getAll(
        int $customerId
    ): Collection {

        return $this->repository
            ->allByCustomer($customerId);
    }

    public function getById(
        int $id,
        int $customerId
    ): CustomerNotification {

        return $this->repository
            ->find($id, $customerId);
    }

    public function markAsRead(
        CustomerNotification $notification
    ): CustomerNotification {

        return $this->repository
            ->markAsRead($notification);
    }

    public function delete(
        CustomerNotification $notification
    ): bool {

        return $this->repository
            ->delete($notification);
    }
}
