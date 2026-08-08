<?php

declare(strict_types=1);

namespace App\Contracts;

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
}
