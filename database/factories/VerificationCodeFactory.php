<?php

namespace Database\Factories;

use App\Enums\VerificationPurpose;
use App\Models\Customer;
use App\Models\VerificationCode;
use Illuminate\Database\Eloquent\Factories\Factory;

class VerificationCodeFactory extends Factory
{
    protected $model = VerificationCode::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'purpose' => VerificationPurpose::SignupEmailVerification->value,
            'code_or_token' => (string) fake()->numberBetween(100000, 999999),
            'contact_value' => fake()->safeEmail(),
            'expires_at' => now()->addMinutes(10),
            'consumed_at' => null,
        ];
    }
}
