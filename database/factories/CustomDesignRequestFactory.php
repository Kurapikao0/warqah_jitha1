<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\CustomDesignRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomDesignRequest>
 */
class CustomDesignRequestFactory extends Factory
{
    protected $model = CustomDesignRequest::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['new', 'in_review', 'quoted', 'converted', 'rejected']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
