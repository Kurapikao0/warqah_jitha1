<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductMedia>
 */
class ProductMediaFactory extends Factory
{
    protected $model = ProductMedia::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'media_type' => 'image',
            'url' => $this->faker->imageUrl(),
            'sort_order' => $this->faker->numberBetween(0, 10),
            'is_primary' => false,
            'created_at' => now(),
        ];
    }
}
