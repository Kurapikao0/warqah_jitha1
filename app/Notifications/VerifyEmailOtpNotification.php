<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailOtpNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $otp
    ) {
    }


    public function via(object $notifiable): array
    {
        return ['mail'];
    }


    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('تأكيد البريد الإلكتروني - ورقة وجذع')
            ->greeting('مرحباً بك في ورقة وجذع 🌿')
            ->line('شكراً لتسجيلك في منصتنا.')
            ->line('رمز تأكيد البريد الإلكتروني الخاص بك هو:')
            ->line('# ' . $this->otp)
            ->line('صلاحية الرمز 10 دقائق فقط.')
            ->line('إذا لم تقم بإنشاء هذا الحساب، يمكنك تجاهل هذه الرسالة.')
            ->salutation('مع تحيات فريق ورقة وجذع');
    }


    public function toArray(object $notifiable): array
    {
        return [];
    }
}