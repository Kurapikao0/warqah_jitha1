<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\CustomerRegistered;
use App\Notifications\WelcomeNotification;
use App\Services\EmailNotificationService;

final class SendWelcomeEmailListener
{
    public function __construct(
        protected EmailNotificationService $emailNotificationService,
    ) {
    }

    public function handle(
        CustomerRegistered $event
    ): void {
        $notification = new WelcomeNotification();

        $this->emailNotificationService->dispatch(
            user: $event->customer,
            notificationClass: $notification::class,
            notificationType: $notification->notificationType(),
            subject: $notification->notificationSubject(),
        );
    }
}

