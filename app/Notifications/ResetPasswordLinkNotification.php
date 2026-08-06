<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class ResetPasswordLinkNotification extends Notification 
{
    use Queueable;


    public function __construct(
        protected string $token
    ) {
    }



    public function via(object $notifiable): array
    {
        return [
            'mail'
        ];
    }



    public function toMail(object $notifiable): MailMessage
    {

        $url = config('app.frontend_url')
            . '/reset-password?token='
            . $this->token
            . '&email='
            . urlencode($notifiable->email);



        return (new MailMessage)

            ->subject(
                'إعادة تعيين كلمة المرور - ورقة وجذع'
            )

            ->greeting(
                'مرحباً بك في ورقة وجذع 🌿'
            )

            ->line(
                'لقد تلقينا طلباً لإعادة تعيين كلمة المرور.'
            )

            ->action(
                'إعادة تعيين كلمة المرور',
                $url
            )

            ->line(
                'الرابط صالح لمدة 30 دقيقة فقط.'
            )

            ->line(
                'إذا لم تطلب ذلك، يمكنك تجاهل هذه الرسالة.'
            );

    }



    public function toArray(object $notifiable): array
    {
        return [];
    }

}