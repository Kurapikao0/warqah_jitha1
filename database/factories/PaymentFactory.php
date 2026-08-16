<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'payment_method' => $this->faker->randomElement(['jawali', 'jeeb', 'al_kuraimi']),
            'amount' => $this->faker->randomFloat(2, 50, 1000),
            'status' => $this->faker->randomElement(['paid', 'unpaid', 'failed']),
            'paid_at' => $this->faker->optional()->dateTime(),
            'created_at' => now(),
        ];
    }
}
