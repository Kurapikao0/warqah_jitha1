<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;


    public function __construct(
        protected string $token
    ) {
    }


    public function via(object $notifiable): array
    {
        return ['mail'];
    }


    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)

            ->subject('إعادة تعيين كلمة المرور - ورقة وجذع')

            ->greeting('مرحباً بك 🌿')

            ->line('تم طلب إعادة تعيين كلمة المرور لحسابك.')

            ->line(
                'رمز إعادة التعيين الخاص بك:'
            )

            ->line($this->token)

            ->line(
                'صلاحية الرمز 30 دقيقة فقط.'
            )

            ->line(
                'إذا لم تطلب إعادة التعيين، تجاهل هذه الرسالة.'
            );
    }
}