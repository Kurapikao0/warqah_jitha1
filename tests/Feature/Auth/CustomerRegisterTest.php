<?php

namespace Tests\Feature\Auth;

use App\Enums\CustomerCategory;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerRegisterTest extends TestCase
{
    use RefreshDatabase;

    // عدل المسار حسب routes/api.php
    private string $registerUrl = '/api/register';

    #[Test]
    public function customer_can_register_successfully(): void
    {
        $categoryValue = CustomerCategory::cases()[0]->value;

        $payload = [
            'full_name' => 'أحمد علي',
            'email' => 'ahmed@example.com',
            'phone_country_code' => '+967',
            'phone' => '770000000',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'category' => $categoryValue,
        ];

        $response = $this->postJson(
            $this->registerUrl,
            $payload
        );

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Customer registered successfully',
                'errors' => null,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'full_name',
                    'email',
                ],
                'errors',
            ]);

        $this->assertDatabaseHas('customers', [
            'email' => 'ahmed@example.com',
            'full_name' => 'أحمد علي',
            'phone' => '770000000',
        ]);

        $customer = Customer::where(
            'email',
            'ahmed@example.com'
        )->first();

        $this->assertNotNull($customer);

        // حسب اسم عمود كلمة المرور عندك
        $this->assertTrue(
            Hash::check(
                'Password123!',
                $customer->password_hash
            )
        );
    }

    #[Test]
    public function registration_fails_with_invalid_or_missing_data(): void
    {
        $response = $this->postJson(
            $this->registerUrl,
            []
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'full_name',
                'email',
                'phone',
                'password',
            ]);
    }

    #[Test]
    public function customer_cannot_register_with_duplicate_email_or_phone(): void
    {

        Customer::factory()->create([
            'email' => 'existing@example.com',
            'phone' => '770000000',
        ]);

        $payload = [
            'full_name' => 'عميل جديد',
            'email' => 'existing@example.com',
            'phone_country_code' => '+967',
            'phone' => '770000000',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'category' => CustomerCategory::cases()[0]->value,
        ];

        $response = $this->postJson(
            $this->registerUrl,
            $payload
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
                'phone',
            ]);
    }
}
