<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Notifications\Messages\MailMessage;

interface EmailNotificationInterface
{
    /**
     * Unique notification type stored in email_logs.notification_type.
     */
    public function notificationType(): string;

    /**
     * Email subject.
     */
    public function notificationSubject(): string;

    /**
     * Build the mail representation.
     */
    public function toMail(object $owner): MailMessage;

    /**
     * Build the array representation.
     */
    public function toArray(object $owner): array;
}
