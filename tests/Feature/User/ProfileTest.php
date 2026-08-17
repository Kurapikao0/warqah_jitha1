<?php

namespace Tests\Feature\User;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private string $profileUrl = '/api/customer/profile';

    #[Test]
    public function customer_can_view_his_profile(): void
    {
        $customer = Customer::factory()->create();

        Sanctum::actingAs($customer, ['*'], 'customer');

        $response = $this->getJson(
            $this->profileUrl
        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Profile fetched successfully',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'errors',
            ]);
    }

    #[Test]
    public function customer_can_update_his_profile(): void
    {
        $customer = Customer::factory()->create();

        Sanctum::actingAs($customer, ['*'], 'customer');

        $payload = [

            'full_name' => 'أحمد محمد',

            'phone_country_code' => '+967',

            'phone' => '771111111',

        ];

        $response = $this->putJson(
            $this->profileUrl,
            $payload
        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Profile updated successfully',
            ]);

        $this->assertDatabaseHas('customers', [

            'id' => $customer->id,

            'full_name' => 'أحمد محمد',

            'phone' => '771111111',

        ]);
    }

    #[Test]
    public function customer_can_change_password(): void
    {
        $customer = Customer::factory()->create([

            'password_hash' => Hash::make(
                'OldPassword123!'
            ),

        ]);

        Sanctum::actingAs($customer, ['*'], 'customer');

        $payload = [

            'current_password' => 'OldPassword123!',

            'password' => 'NewPassword123!',

            'password_confirmation' => 'NewPassword123!',

        ];

        $response = $this->putJson(

            '/api/customer/profile/password',

            $payload

        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Password changed successfully',
            ]);

        $customer->refresh();

        $this->assertTrue(

            Hash::check(

                'NewPassword123!',

                $customer->password_hash

            )

        );
    }

    #[Test]
    public function customer_can_logout(): void
    {
        $customer = Customer::factory()->create();

        Sanctum::actingAs($customer, ['*'], 'customer');

        $response = $this->postJson(

            '/api/customer/logout'

        );

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logged out successfully.',
            ]);
    }

    #[Test]
    public function unauthenticated_customer_cannot_access_profile(): void
    {

        $response = $this->getJson(

            $this->profileUrl

        );

        $response->assertStatus(401);

    }
}
