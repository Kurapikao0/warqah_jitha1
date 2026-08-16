<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Contracts\EmailNotificationInterface;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use InvalidArgumentException;

final class EmailNotification extends Notification
{
    private EmailNotificationInterface $notification;

    public function __construct(
        string $notificationClass,
        private readonly object $owner,
        array $payload = [],
    ) {
        if (
            ! class_exists($notificationClass)
            || ! is_a(
                $notificationClass,
                EmailNotificationInterface::class,
                true
            )
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid email notification class: %s',
                    $notificationClass
                )
            );
        }

        $this->notification = app()->make(
            $notificationClass,
            $payload
        );
    }

    public function via(object $notifiable): array
    {
        return [
            'mail',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return $this->notification->toMail($this->owner);
    }

    public function toArray(object $notifiable): array
    {
        return $this->notification->toArray($this->owner);
    }
}

