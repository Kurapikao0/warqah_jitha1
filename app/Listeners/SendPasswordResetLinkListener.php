<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PasswordResetRequested;
use App\Enums\VerificationPurpose;
use App\Services\VerificationCodeService;
use App\Services\EmailNotificationService;
use App\Notifications\ResetPasswordNotification;


final class SendPasswordResetLinkListener
{
    public function __construct(
        protected VerificationCodeService $verificationCodeService,
        protected EmailNotificationService $emailNotificationService
    ) {
    }


    public function handle(
        PasswordResetRequested $event
    ): void {


        $verification = $this->verificationCodeService->generate(
            $event->customer,
            VerificationPurpose::PasswordResetEmailLink,
            $event->customer->email
        );


        $notification = new ResetPasswordNotification(
            $verification->code_or_token
        );


        $this->emailNotificationService->dispatch(
            customer: $event->customer,
            notificationClass: $notification::class,
            notificationType: 'password_reset',
            subject: $notification->subject(),
            payload: [
                'token' => $verification->code_or_token,
            ],
        );

    }
}
