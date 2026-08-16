<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\EmailNotificationInterface;
use App\Enums\EmailStatus;
use App\Jobs\SendEmailNotificationJob;
use App\Models\AdminUser;
use App\Models\Customer;
use App\Repositories\Contracts\EmailLogRepositoryInterface;

final readonly class EmailNotificationService
{
    public function __construct(
        private EmailLogRepositoryInterface $emailLogRepository,
    ) {}

    public function dispatch(
        Customer|AdminUser $user,
        string $notificationClass,
        string $notificationType,
        string $subject,
        array $payload = [],
        ?string $recipient = null,
    ): void {
        $recipient ??= $user->email;

        $emailLog = $this->emailLogRepository->create([
            'owner_type' => $user::class,
            'owner_id' => $user->id,
            'notification' => $notificationClass,
            'notification_type' => $notificationType,
            'payload' => $payload,
            'recipient' => $recipient,
            'subject' => $subject,
            'status' => EmailStatus::Pending,
            'attempts' => 0,
            'queued_at' => now(),
        ]);

        SendEmailNotificationJob::dispatch(
            emailLogId: $emailLog->id,
        );
    }

    public function send(
        Customer|AdminUser $user,
        EmailNotificationInterface $notification,
    ): void {
        $this->dispatch(
            user: $user,
            notificationClass: $notification::class,
            notificationType: $notification->notificationType(),
            subject: $notification->notificationSubject(),
            payload: [],
        );
    }
}
