<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\EmailStatus;
use App\Models\AdminUser;
use App\Models\Customer;
use App\Models\EmailLog;
use App\Notifications\EmailNotification;
use App\Repositories\Contracts\EmailLogRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Throwable;

final class SendEmailNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        public readonly int $emailLogId,
    ) {
    }

    public function handle(
        EmailLogRepositoryInterface $emailLogRepository,
    ): void {
        $emailLog = $emailLogRepository->findByIdWithOwner(
            $this->emailLogId
        );

        if ($emailLog === null) {
            return;
        }

        if ($emailLog->status === EmailStatus::Sent) {
            return;
        }

        $emailLogRepository->update($emailLog, [
            'attempts' => $emailLog->attempts + 1,
        ]);

        try {
            $owner = $emailLog->owner;

            if (
                ! $owner instanceof Customer
                && ! $owner instanceof AdminUser
            ) {
                throw new RuntimeException(
                    'Email log owner could not be resolved.'
                );
            }

            Notification::route('mail', $emailLog->recipient)
                ->notify(
                    new EmailNotification(
                        notificationClass: $emailLog->notification,
                        payload: $emailLog->payload ?? [],
                        owner: $owner,
                    )
                );

            $emailLogRepository->update($emailLog, [
                'status' => EmailStatus::Sent,
                'sent_at' => now(),
                'failed_at' => null,
                'error_message' => null,
            ]);
        } catch (Throwable $exception) {
            $emailLogRepository->update($emailLog, [
                'status' => EmailStatus::Failed,
                'failed_at' => now(),
                'error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }
}
