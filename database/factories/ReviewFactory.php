<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'product_id' => Product::factory(),
            'order_item_id' => OrderItem::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->optional()->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'published', 'rejected']),
            'admin_reply' => $this->faker->optional()->sentence(),
            'admin_reply_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
