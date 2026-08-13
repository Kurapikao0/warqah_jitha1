<?php

declare(strict_types=1);

namespace App\Enums;

enum EmailNotificationType: string
{
    case Welcome = 'welcome';

    case EmailVerificationOtp = 'email_verification_otp';

    case PasswordResetLink = 'password_reset_link';

    case PasswordChanged = 'password_changed';

    case EmailChanged = 'email_changed';
}
