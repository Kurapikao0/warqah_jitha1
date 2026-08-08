<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Contracts\EmailNotificationInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class PasswordChangedNotification extends Notification implements EmailNotificationInterface
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return [
            'mail',
        ];
    }

    public function notificationType(): string
    {
        return 'password_changed';
    }

    public function notificationSubject(): string
    {
        return 'تم تغيير كلمة المرور - ورقة وجذع';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->notificationSubject())
            ->view(
                'emails.security.password-changed',
                [
                    'user' => $notifiable,
                ]
            );
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
