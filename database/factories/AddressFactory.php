<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
class AddressFactory extends Factory
{
    protected $model = Address::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'recipient_name' => $this->faker->name(),
            'phone' => $this->faker->numerify('77#######'),
            'country' => 'Yemen',
            'city' => $this->faker->city(),
            'district' => $this->faker->streetName(),
            'street' => $this->faker->streetAddress(),
            'postal_code' => $this->faker->postcode(),
            'is_default' => $this->faker->boolean(),
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}
