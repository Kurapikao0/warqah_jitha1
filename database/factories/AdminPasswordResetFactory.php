<?php

namespace Database\Factories;

use App\Models\AdminPasswordReset;
use App\Models\AdminUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdminPasswordReset>
 */
class AdminPasswordResetFactory extends Factory
{
    protected $model = AdminPasswordReset::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_user_id' => AdminUser::factory(),
            'code_or_token' => $this->faker->uuid(),
            'contact_value' => $this->faker->email(),
            'expires_at' => now()->addHours(2),
            'consumed_at' => null,
            'created_at' => now(),
        ];
    }
}
