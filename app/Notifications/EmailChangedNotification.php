<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Contracts\EmailNotificationInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class EmailChangedNotification extends Notification implements EmailNotificationInterface
{
    use Queueable;

    public function __construct(
        private readonly string $old_email,
        private readonly string $new_email,
    ) {
    }

    public function via(object $notifiable): array
    {
        return [
            'mail',
        ];
    }

    public function notificationType(): string
    {
        return 'email_changed';
    }

    public function notificationSubject(): string
    {
        return 'تم تغيير البريد الإلكتروني - ورقة وجذع';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->notificationSubject())
            ->view(
                'emails.security.email-changed',
                [
                    'user'      => $notifiable,
                    'old_email' => $this->old_email,
                    'new_email' => $this->new_email,
                ]
            );
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}

