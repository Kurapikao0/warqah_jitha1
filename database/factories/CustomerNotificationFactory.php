<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomerNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomerNotification>
 */
class CustomerNotificationFactory extends Factory
{
    protected $model = CustomerNotification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'type' => $this->faker->randomElement(['order_update', 'promotion', 'system']),
            'title' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
            'is_read' => $this->faker->boolean(),
            'created_at' => now(),
        ];
    }
}
