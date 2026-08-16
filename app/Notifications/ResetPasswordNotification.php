<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Contracts\EmailNotificationInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class ResetPasswordNotification extends Notification implements EmailNotificationInterface
{
    use Queueable;

    public function __construct(
        private readonly string $token,
    ) {}

    public function via(object $notifiable): array
    {
        return [
            'mail',
        ];
    }

    public function notificationType(): string
    {
        return 'password_reset';
    }

    public function notificationSubject(): string
    {
        return 'إعادة تعيين كلمة المرور - ورقة وجذع';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->notificationSubject())
            ->view(
                'emails.customer.password-reset',
                [
                    'customer' => $notifiable,
                    'token' => $this->token,
                ],
            );
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
