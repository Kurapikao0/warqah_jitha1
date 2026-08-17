<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\VerificationPurpose;
use App\Events\PasswordResetRequested;
use App\Notifications\ResetPasswordNotification;
use App\Services\EmailNotificationService;
use App\Services\VerificationCodeService;

final class SendPasswordResetLinkListener
{
    public function __construct(
        private readonly VerificationCodeService $verificationCodeService,
        private readonly EmailNotificationService $emailNotificationService,
    ) {}

    public function handle(PasswordResetRequested $event): void
    {
        $verification = $this->verificationCodeService->generate(
            $event->customer,
            VerificationPurpose::PasswordResetEmailLink,
            $event->customer->email,
        );

        $notification = new ResetPasswordNotification(
            $verification->code_or_token,
        );

        $this->emailNotificationService->dispatch(
            user: $event->customer,
            notificationClass: $notification::class,
            notificationType: $notification->notificationType(),
            subject: $notification->notificationSubject(),
            payload: [
                'token' => $verification->code_or_token,
            ],
            recipient: $event->customer->email,
        );
    }
}
