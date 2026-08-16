<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\EmailChanged;
use App\Notifications\EmailChangedNotification;
use App\Services\EmailNotificationService;

final class SendEmailChangedEmail
{
    public function __construct(
        private readonly EmailNotificationService $emailNotificationService,
    ) {}

    public function handle(EmailChanged $event): void
    {
        $this->emailNotificationService->dispatch(
            user: $event->customer,
            notificationClass: EmailChangedNotification::class,
            notificationType: 'email_changed',
            subject: 'تم تغيير البريد الإلكتروني - ورقة وجذع',
            payload: [
                'old_email' => $event->oldEmail,
                'new_email' => $event->newEmail,
            ],
            recipient: $event->oldEmail,
        );
    }
}
