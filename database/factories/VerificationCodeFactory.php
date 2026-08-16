<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\VerificationCode;
use Illuminate\Database\Eloquent\Factories\Factory;

class VerificationCodeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VerificationCode::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(), // ينشئ عميل تلقائياً ويأخذ id الخاص به
            'code' => (string) rand(100000, 999999),
            'expires_at' => now()->addMinutes(10),
            'is_used' => false,
        ];
    }
}
