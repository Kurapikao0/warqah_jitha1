<?php

namespace Database\Factories;

use App\Models\AdminUser;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderStatusHistory>
 */
class OrderStatusHistoryFactory extends Factory
{
    protected $model = OrderStatusHistory::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'status' => $this->faker->randomElement(['received', 'in_production', 'in_transit', 'cancelled']),
            'note' => $this->faker->optional()->sentence(),
            'changed_by' => AdminUser::factory(),
            'created_at' => now(),
        ];
    }
}
