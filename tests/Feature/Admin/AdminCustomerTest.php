<?php

namespace Tests\Feature\Admin;

use App\Enums\CustomerCategory;
use App\Models\AdminUser;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminCustomerTest extends TestCase
{
    use RefreshDatabase;

    private AdminUser $admin;
    private string $adminCustomerUrl = '/api/admin/customers';

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = AdminUser::factory()->create();
        Sanctum::actingAs($this->admin, ['*'], 'sanctum');
    }

    #[Test]
    public function admin_can_create_a_new_customer(): void
    {
        $categoryValue = defined(CustomerCategory::class . '::INDIVIDUAL') 
            ? CustomerCategory::INDIVIDUAL->value 
            : CustomerCategory::cases()[0]->value;

        $payload = [
            'full_name' => 'عميل مضاف بواسطة الآدمن',
            'email' => 'created_by_admin@example.com',
            'phone_country_code' => '+967',
            'phone' => '771111111',
            'password' => 'AdminSetPass123!',
            'category' => $categoryValue,
        ];

        $response = $this->postJson($this->adminCustomerUrl, $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('customers', [
            'email' => 'created_by_admin@example.com',
            'full_name' => 'عميل مضاف بواسطة الآدمن',
            'phone' => '771111111',
        ]);

        $customer = Customer::where('email', 'created_by_admin@example.com')->first();
        $this->assertTrue(Hash::check('AdminSetPass123!', $customer->password_hash));
    }

    #[Test]
    public function unauthenticated_user_cannot_create_customer_via_admin_api(): void
    {
        $this->app['auth']->forgetGuards();

        $payload = [
            'full_name' => 'محاولة غير مصرحة',
            'email' => 'unauthorized@example.com',
            'phone_country_code' => '+967',
            'phone' => '772222222',
            'password' => 'Password123!',
        ];

        $response = $this->postJson($this->adminCustomerUrl, $payload);

        $response->assertStatus(401);
    }

    #[Test]
    public function admin_customer_creation_fails_validation(): void
    {
        $response = $this->postJson($this->adminCustomerUrl, []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['full_name', 'email', 'phone', 'password']);
    }

    #[Test]
    public function admin_cannot_create_customer_with_duplicate_email_or_phone(): void
    {
        // Get valid category value
        $categoryValue = defined(CustomerCategory::class . '::INDIVIDUAL') 
            ? CustomerCategory::INDIVIDUAL->value 
            : CustomerCategory::cases()[0]->value;

        // Create an existing customer
        Customer::factory()->create([
            'email' => 'existing@example.com',
            'phone' => '770000000',
            'category' => $categoryValue,
        ]);

        // Try to create a duplicate with SAME email and phone
        $payload = [
            'full_name' => 'عميل مضاف بواسطة الآدمن',
            'email' => 'existing@example.com',  // ✅ SAME as existing
            'phone' => '770000000',              // ✅ SAME as existing
            'phone_country_code' => '+967',
            'password' => 'password123',
            'category' => $categoryValue,
        ];

        $response = $this->postJson($this->adminCustomerUrl, $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'phone']);
    }
}