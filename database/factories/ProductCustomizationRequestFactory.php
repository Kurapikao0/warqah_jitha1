<?php

namespace Database\Factories;

use App\Models\Color;
use App\Models\Customer;
use App\Models\DesignPattern;
use App\Models\Product;
use App\Models\ProductCustomizationRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductCustomizationRequest>
 */
class ProductCustomizationRequestFactory extends Factory
{
    protected $model = ProductCustomizationRequest::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $basePrice = $this->faker->randomFloat(2, 50, 300);
        $customFee = $this->faker->randomFloat(2, 10, 50);
        $shippingFee = $this->faker->randomFloat(2, 5, 20);
        return [
            'request_code' => strtoupper($this->faker->bothify('REQ-#####')),
            'customer_id' => Customer::factory(),
            'base_product_id' => Product::factory(),
            'color_id' => Color::factory(),
            'design_pattern_id' => DesignPattern::factory(),
            'quantity' => $this->faker->numberBetween(1, 5),
            'length_cm' => $this->faker->randomFloat(2, 10, 200),
            'width_cm' => $this->faker->randomFloat(2, 10, 200),
            'height_cm' => $this->faker->randomFloat(2, 5, 150),
            'customer_notes' => $this->faker->sentence(),
            'craftsman_notes' => $this->faker->optional()->sentence(),
            'base_price' => $basePrice,
            'customization_fee' => $customFee,
            'packaging_shipping_fee' => $shippingFee,
            'total_price' => $basePrice + $customFee + $shippingFee,
            'status' => $this->faker->randomElement(['pending_approval', 'in_production', 'completed']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
