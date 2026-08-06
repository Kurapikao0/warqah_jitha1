<?php

namespace App\Listeners;

use App\Events\CustomerRegistered;
use App\Enums\VerificationPurpose;
use App\Services\VerificationCodeService;
use App\Notifications\VerifyEmailOtpNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use App\Enums\EmailNotificationType;
class SendVerificationOtpListener
{

    public function __construct(
        protected VerificationCodeService $verificationCodeService,
                protected NotificationService $notificationService
    ) {
    }


    public function handle(CustomerRegistered $event): void
    {

        \Log::info('CustomerRegistered listener executed', [
            'customer_id' => $event->customer->id,
            'email' => $event->customer->email,
        ]);


        $verification = $this->verificationCodeService->generate(
            $event->customer,
            VerificationPurpose::SignupEmailVerification,
            $event->customer->email
        );
        \Log::info('Generated verification code', [
            'customer_id' => $event->customer->id,
            'code' => $verification->code_or_token,
        ]);

        $this->notificationService->send(

            $event->customer,

            new VerifyEmailOtpNotification(
                $verification->code_or_token
            ),

        );


        \Log::info('Verification email notification sent', [
            'customer_id' => $event->customer->id,
        ]);
    }
}