<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderProductionStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 50, 1000);
        $shippingFee = $this->faker->randomFloat(2, 10, 50);

        return [
            'order_number' => strtoupper($this->faker->bothify('ORD-######')),
            'customer_id' => Customer::factory(),
            'address_id' => Address::factory(),
            'shipping_recipient_name' => $this->faker->name(),
            'shipping_phone' => $this->faker->numerify('77#######'),
            'shipping_address_full' => $this->faker->address(),
            'shipping_city' => $this->faker->city(),
            'shipping_country' => 'Yemen',
            'order_type' => $this->faker->randomElement(['ready_made', 'custom', 'mixed']),
            'status' => $this->faker->randomElement(['received', 'in_production', 'in_transit', 'cancelled']),
            'current_production_stage_id' => OrderProductionStage::factory(),
            'expected_delivery_date' => $this->faker->optional()->dateTimeBetween('+3 days', '+2 weeks'),
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'total_amount' => $subtotal + $shippingFee,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
