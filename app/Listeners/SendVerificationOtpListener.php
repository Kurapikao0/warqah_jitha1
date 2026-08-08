<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Enums\VerificationPurpose;
use App\Events\CustomerRegistered;
use App\Notifications\VerifyEmailOtpNotification;
use App\Services\EmailNotificationService;
use App\Services\VerificationCodeService;
use Illuminate\Support\Facades\Log;

final class SendVerificationOtpListener
{
    public function __construct(
        protected VerificationCodeService $verificationCodeService,
        protected EmailNotificationService $emailNotificationService,
    ) {
    }

    public function handle(
        CustomerRegistered $event
    ): void {
        Log::info('CustomerRegistered verification listener executed', [
            'customer_id' => $event->customer->id,
        ]);

        $verification = $this->verificationCodeService->generate(
            $event->customer,
            VerificationPurpose::SignupEmailVerification,
            $event->customer->email,
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
        );

        Log::info('Verification email queued', [
            'customer_id' => $event->customer->id,
        ]);
    }
}

