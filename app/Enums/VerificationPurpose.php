<?php

namespace App\Enums;

enum VerificationPurpose: string
{
    case SignupEmailVerification = 'signup_email_verification';

    case SignupPhoneVerification = 'signup_phone_verification';

    case PasswordResetEmailLink = 'password_reset_email_link';

    case PasswordResetPhoneOtp = 'password_reset_phone_otp';

    case ChangeEmailVerification = 'change_email_verification';
}