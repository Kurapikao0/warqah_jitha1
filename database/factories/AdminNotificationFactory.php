<?php

namespace Database\Factories;

use App\Models\AdminNotification;
use App\Models\AdminUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdminNotification>
 */
class AdminNotificationFactory extends Factory
{
    protected $model = AdminNotification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_user_id' => AdminUser::factory(),
            'type' => $this->faker->randomElement(['new_order', 'new_customization_request', 'system']),
            'title' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
            'is_read' => $this->faker->boolean(),
            'created_at' => now(),
        ];
    }
}
