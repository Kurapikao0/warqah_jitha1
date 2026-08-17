<?php

namespace Database\Factories;

use App\Models\AdminUser;
use App\Models\Order;
use App\Models\OrderProductionStage;
use App\Models\OrderProductionStageHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderProductionStageHistory>
 */
class OrderProductionStageHistoryFactory extends Factory
{
    protected $model = OrderProductionStageHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'stage_id' => OrderProductionStage::factory(),
            'changed_by' => AdminUser::factory(),
            'created_at' => now(),
        ];
    }
}
