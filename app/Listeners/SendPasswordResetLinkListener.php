<?php

namespace App\Listeners;

use App\Events\PasswordResetRequested;
use App\Enums\VerificationPurpose;
use App\Services\VerificationCodeService;
use App\Services\NotificationService;
use App\Notifications\ResetPasswordLinkNotification;

class SendPasswordResetLinkListener
{


    public function __construct(
        protected VerificationCodeService $verificationCodeService,
        protected NotificationService $notificationService
    ) {
    }



    public function handle(
        PasswordResetRequested $event
    ): void {


        $verification =
            $this->verificationCodeService->generate(
                $event->customer,
                VerificationPurpose::PasswordResetEmailLink,
                $event->customer->email
            );



        $this->notificationService->sendPasswordResetLink(
            $event->customer,
            $verification->code_or_token
        );


    }

}