<?php

namespace Database\Factories;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Permission>
 */
class PermissionFactory extends Factory
{
    protected $model = Permission::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word() . '.' . $this->faker->word(),
            'module' => $this->faker->randomElement(['products', 'orders', 'users', 'settings', 'inventory']),
            'created_at' => now(),
        ];
    }
}
