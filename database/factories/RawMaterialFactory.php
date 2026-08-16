<?php

namespace Database\Factories;

use App\Models\RawMaterial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RawMaterial>
 */
class RawMaterialFactory extends Factory
{
    protected $model = RawMaterial::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(2, true),
            'unit' => $this->faker->randomElement(['kg', 'meter', 'piece', 'bundle']),
            'quantity_available' => $this->faker->randomFloat(2, 10, 500),
            'reorder_point' => $this->faker->randomFloat(2, 5, 50),
            'status' => $this->faker->randomElement(['in_stock', 'low_stock', 'out_of_stock']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
