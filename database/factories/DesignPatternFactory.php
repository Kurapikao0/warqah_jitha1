<?php

namespace Database\Factories;

use App\Models\DesignPattern;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DesignPattern>
 */
class DesignPatternFactory extends Factory
{
    protected $model = DesignPattern::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'preview_image_url' => $this->faker->imageUrl(),
            'created_at' => now(),
        ];
    }
}
