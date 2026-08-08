<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PasswordChanged;
use App\Notifications\PasswordChangedNotification;
use App\Services\EmailNotificationService;

final class SendPasswordChangedEmail
{
    public function __construct(
        private readonly EmailNotificationService $emailNotificationService,
    ) {
    }

    public function handle(PasswordChanged $event): void
    {
        $this->emailNotificationService->send(
            user: $event->customer,
            notification: new PasswordChangedNotification(),
        );
    }
}

