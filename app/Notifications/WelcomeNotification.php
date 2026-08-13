<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Contracts\EmailNotificationInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class WelcomeNotification extends Notification implements EmailNotificationInterface
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
        return 'welcome_email';
    }


    public function notificationSubject(): string
    {
        return 'مرحباً بك في ورقة وجذع 🌿';
    }


    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)

            ->subject(
                $this->notificationSubject()
            )

            ->view(
                'emails.customer.welcome',
                [
                    'customer' => $notifiable,
                ]
            );
    }


    public function toArray(object $notifiable): array
    {
        return [];
    }
}
