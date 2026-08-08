<?php

declare(strict_types=1);

namespace App\Factories;

use App\Contracts\EmailNotificationInterface;
use App\Notifications\WelcomeNotification;
use App\Notifications\VerifyEmailOtpNotification;
use App\Notifications\ResetPasswordNotification;
use InvalidArgumentException;

final class NotificationFactory
{
    /**
     * Create notification instance by type.
     */
    public function make(
        string $notificationType,
        array $payload = []
    ): EmailNotificationInterface {

        return match ($notificationType) {

            'welcome_email' =>
                new WelcomeNotification(),


            'verification_otp' =>
                new VerifyEmailOtpNotification(
                    otp: $payload['otp']
                ),


            'password_reset' =>
                new ResetPasswordNotification(
                    token: $payload['token']
                ),


            default =>
                throw new InvalidArgumentException(
                    "Unsupported notification type: {$notificationType}"
                ),
        };
    }
}
