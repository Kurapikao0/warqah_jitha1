<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        $price = $this->faker->randomFloat(2, 20, 500);

        return [
            'category_id' => ProductCategory::factory(),
            'sku' => strtoupper($this->faker->bothify('WRK-####-??')),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->paragraph(),
            'price' => $price,
            'compare_at_price' => $this->faker->optional(0.3)->randomFloat(2, $price, $price + 50),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'reserved_quantity' => 0,
            'length_cm' => $this->faker->randomFloat(2, 10, 200),
            'width_cm' => $this->faker->randomFloat(2, 10, 200),
            'height_cm' => $this->faker->randomFloat(2, 5, 150),
            'is_customizable' => $this->faker->boolean(),
            'is_handmade' => true,
            'is_new' => $this->faker->boolean(),
            'is_bestseller' => $this->faker->boolean(),
            'is_limited_edition' => $this->faker->boolean(),
            'average_rating' => $this->faker->optional()->randomFloat(1, 1, 5),
            'reviews_count' => 0,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
