<?php

namespace Database\Factories;

use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<AdminUser>
 */
class AdminUserFactory extends Factory
{
    protected $model = AdminUser::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role_id' => Role::factory(),
            'full_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'avatar_url' => $this->faker->imageUrl(),
            'password_hash' => Hash::make('password'),
            'two_factor_enabled' => $this->faker->boolean(),
            'last_login_at' => $this->faker->optional()->dateTime(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
