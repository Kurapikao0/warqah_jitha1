<?php

namespace App\Services;

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
            'customer_id' => $customer->id, // إسناد المفتاح الأجنبي هنا ضروري
            'code'        => (string) rand(100000, 999999),
            'expires_at'  => now()->addMinutes(10),
            'is_used'     => false,
        ]);
    }
}