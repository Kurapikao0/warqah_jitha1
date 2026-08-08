<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            ALTER TABLE verification_codes
            DROP CONSTRAINT IF EXISTS verification_codes_purpose_check
        ');

        DB::statement("
            ALTER TABLE verification_codes
            ADD CONSTRAINT verification_codes_purpose_check
            CHECK (
                purpose IN (
                    'signup_email_verification',
                    'signup_phone_verification',
                    'password_reset_email_link',
                    'password_reset_phone_otp',
                    'change_email_verification'
                )
            )
        ");
    }

    public function down(): void
    {
        DB::statement('
            ALTER TABLE verification_codes
            DROP CONSTRAINT IF EXISTS verification_codes_purpose_check
        ');

        DB::statement("
            ALTER TABLE verification_codes
            ADD CONSTRAINT verification_codes_purpose_check
            CHECK (
                purpose IN (
                    'signup_email_verification',
                    'signup_phone_verification',
                    'password_reset_email_link',
                    'password_reset_phone_otp'
                )
            )
        ");
    }
};

