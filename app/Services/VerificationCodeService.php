<?php

namespace App\Services;

use App\Enums\VerificationPurpose;
use App\Models\Customer;
use App\Models\VerificationCode;

class VerificationCodeService
{
    /**
     * إنشاء كود تحقق جديد وربطه بالعميل
     */
    public function generateCode(Customer $customer): VerificationCode
    {
        return VerificationCode::create([
            'customer_id' => $customer->id,
            'code'        => (string) rand(100000, 999999),
            'expires_at'  => now()->addMinutes(10),
            'is_used'     => false,
        ]);
    }

    /**
     * التحقق من صحة الكود/التوكن وتأكيده
     */
    public function verify(
        Customer $customer,
        VerificationPurpose $purpose,
        string $contactValue,
        string $codeOrToken
    ): bool {
        $verification = VerificationCode::where('customer_id', $customer->id)
            ->where('code', $codeOrToken)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (! $verification) {
            return false;
        }

        // تعليم الكود بأنه تم استخدامه بنجاح
        $verification->update([
            'is_used' => true,
        ]);

        return true;
    }
}