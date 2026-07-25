<?php

namespace Database\Factories;

use App\Models\VerificationCode;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VerificationCode>
 */
class VerificationCodeFactory extends Factory
{
    protected $model = VerificationCode::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'purpose' => $this->faker->randomElement([
                'signup_phone_verification',
                'password_reset_email_link',
                'password_reset_phone_otp'
            ]),
            'code_or_token' => (string) $this->faker->numberBetween(100000, 999999),
            'contact_value' => $this->faker->safeEmail(),
            'expires_at' => now()->addMinutes(15),
            'consumed_at' => null,
            'created_at' => now(),
        ];
    }
}
