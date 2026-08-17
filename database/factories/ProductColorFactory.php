<?php

namespace Database\Factories;

use App\Models\Color;
use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductColor>
 */
class ProductColorFactory extends Factory
{
    protected $model = ProductColor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'color_id' => Color::factory(),
        ];
    }
}
