<?php

namespace App\Notifications;

use App\Contracts\EmailNotificationInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailOtpNotification extends Notification implements EmailNotificationInterface
{
    use Queueable;

    public function __construct(
        protected string $otp
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function notificationType(): string
    {
        return 'verification_otp';
    }

    public function notificationSubject(): string
    {
        return 'تأكيد البريد الإلكتروني - ورقة وجذع';
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->notificationSubject())
            ->view(
                'emails.customer.verification-otp',
                [
                    'customer' => $notifiable,
                    'otp' => $this->otp,
                ]
            );
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
