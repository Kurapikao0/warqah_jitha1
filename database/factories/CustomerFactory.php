<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone_country_code' => '+967',
            'phone' => $this->faker->unique()->numerify('77#######'),
            'password_hash' => Hash::make('password'),
            'avatar_url' => $this->faker->imageUrl(),
            'category' => $this->faker->randomElement(['regular', 'vip']),
            'email_verified_at' => $this->faker->optional()->dateTime(),
            'phone_verified_at' => $this->faker->optional()->dateTime(),
            'total_orders' => 0,
            'total_purchases' => 0.00,
            'last_order_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
