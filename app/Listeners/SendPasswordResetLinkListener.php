<?php

namespace App\Listeners;

use App\Events\PasswordResetRequested;
use App\Enums\VerificationPurpose;
use App\Services\VerificationCodeService;
use App\Notifications\ResetPasswordLinkNotification;


class SendPasswordResetLinkListener
{


    public function __construct(
        protected VerificationCodeService $verificationCodeService
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



        $event->customer->notify(
            new ResetPasswordLinkNotification(
                $verification->code_or_token
            )
        );


    }

}