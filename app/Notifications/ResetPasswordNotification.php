<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;


class ResetPasswordNotification extends Notification
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



    public function type(): string
    {
        return 'password_reset';
    }



    public function subject(): string
    {
        return 'إعادة تعيين كلمة المرور - ورقة وجذع';
    }



    public function toMail(object $notifiable): MailMessage
    {

        return (new MailMessage)

            ->subject(
                $this->subject()
            )

            ->view(
                'emails.customer.password-reset',
                [
                    'customer'=>$notifiable,
                    'token'=>$this->token,
                ]
            );

    }



    public function toArray(object $notifiable): array
    {
        return [];
    }
}