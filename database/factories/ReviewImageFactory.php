<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\ReviewImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReviewImage>
 */
class ReviewImageFactory extends Factory
{
    protected $model = ReviewImage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'review_id' => Review::factory(),
            'image_url' => $this->faker->imageUrl(),
            'created_at' => now(),
        ];
    }
}
