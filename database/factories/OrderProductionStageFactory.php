<?php

namespace Database\Factories;

use App\Models\OrderProductionStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderProductionStage>
 */
class OrderProductionStageFactory extends Factory
{
    protected $model = OrderProductionStage::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['جاهز', 'قص', 'تشطيب', 'حياكة', 'تجهيز']),
            'sort_order' => $this->faker->unique()->numberBetween(1, 100),
        ];
    }
}
