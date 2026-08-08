<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\VerificationPurpose;
use App\Events\EmailChanged;
use App\Notifications\VerifyEmailOtpNotification;
use App\Services\EmailNotificationService;
use App\Services\VerificationCodeService;

final class SendChangeEmailVerificationOtpListener
{
    public function __construct(
        private readonly VerificationCodeService $verificationCodeService,
        private readonly EmailNotificationService $emailNotificationService,
    ) {
    }

    public function handle(EmailChanged $event): void
    {
        $verification = $this->verificationCodeService->generate(
            $event->customer,
            VerificationPurpose::ChangeEmailVerification,
            $event->newEmail,
        );

        $notification = new VerifyEmailOtpNotification(
            $verification->code_or_token,
        );

        $this->emailNotificationService->dispatch(
            user: $event->customer,
            notificationClass: $notification::class,
            notificationType: $notification->notificationType(),
            subject: $notification->notificationSubject(),
            payload: [
                'otp' => $verification->code_or_token,
            ],
            recipient: $event->newEmail,
        );
    }
}
