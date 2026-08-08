<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\VerificationPurpose;
use App\Events\PasswordChanged;
use App\Models\Customer;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

final class PasswordResetService
{
    public function reset(
        Customer $customer,
        string $codeOrToken,
        string $password,
    ): void {
        DB::transaction(function () use (
            $customer,
            $codeOrToken,
            $password
        ): void {
            $verification = VerificationCode::query()
                ->where('customer_id', $customer->id)
                ->where(
                    'purpose',
                    VerificationPurpose::PasswordResetEmailLink->value
                )
                ->where('code_or_token', $codeOrToken)
                ->whereNull('consumed_at')
                ->where('expires_at', '>', now())
                ->latest('id')
                ->lockForUpdate()
                ->first();

            if ($verification === null) {
                throw new RuntimeException(
                    'Invalid or expired reset token.'
                );
            }

            $customer->update([
                'password_hash' => Hash::make($password),
            ]);

            $verification->update([
                'consumed_at' => now(),
            ]);

            PasswordChanged::dispatch(
                customer: $customer->fresh(),
            );
        });
    }
}
