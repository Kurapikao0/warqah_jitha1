<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AdminUser;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{

    protected $model = ActivityLog::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_user_id' => AdminUser::factory(),
            'action' => $this->faker->randomElement(['created_product', 'updated_order', 'deleted_category']),
            'entity_type' => 'Product',
            'entity_id' => $this->faker->randomNumber(),
            'meta' => ['ip' => $this->faker->ipv4(), 'user_agent' => $this->faker->userAgent()],
            'created_at' => now(),
        ];
    }
}
