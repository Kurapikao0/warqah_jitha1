<?php

namespace Database\Factories;

use App\Models\ProductAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductAttribute>
 */
class ProductAttributeFactory extends Factory
{
    protected $model = ProductAttribute::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word();

        return [
            'name' => $name,
            'display_name' => $name,
            'input_type' => $this->faker->randomElement([
                'text',
                'number',
                'select',
                'color',
                'boolean',
            ]),
            'is_required' => false,
            'options' => null,
            'created_at' => now(),
        ];
    }
}
