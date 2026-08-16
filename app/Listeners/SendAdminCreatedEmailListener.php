<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\AdminCreated;
use App\Notifications\AdminCreatedNotification;
use App\Services\EmailNotificationService;

final class SendAdminCreatedEmailListener
{
    public function __construct(
        protected EmailNotificationService $emailNotificationService,
    ) {}

    public function handle(
        AdminCreated $event
    ): void {
        $notification = new AdminCreatedNotification;

        $this->emailNotificationService->dispatch(
            user: $event->adminUser,
            notificationClass: $notification::class,
            notificationType: $notification->notificationType(),
            subject: $notification->notificationSubject(),
        );
    }
}
