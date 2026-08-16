<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CustomerAuthAndProfileTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_register_a_new_customer()
    {
        $payload = [
            'full_name'             => 'Ahmed Ali',
            'email'                 => 'ahmed@example.com',
            'phone'                 => '770000000',
            'phone_country_code'    => '+967',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $payload);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'user' => ['id', 'full_name', 'email', 'phone'],
                    'token',
                ]);

        $this->assertDatabaseHas('customers', [
            'email' => 'ahmed@example.com',
            'phone' => '770000000',
        ]);
    }

    #[Test]
    public function it_can_login_customer()
    {
        $customer = Customer::factory()->create([
            'phone'         => '770000000',
            'password_hash' => bcrypt('password123'),
        ]);

        $payload = [
            'phone'    => '770000000',
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $payload);

        $response->assertStatus(200)
                ->assertJsonStructure(['success', 'token', 'user']);
    }

#[Test]
    public function it_can_update_customer_avatar()
    {
        Storage::fake('public');

        $customer = Customer::factory()->create();

        $file = UploadedFile::fake()->create('avatar.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($customer, 'customer')
                        ->postJson('/api/customer/profile/avatar', [
                            'avatar' => $file,
                        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Avatar updated successfully',
                ]);

        // ✅ استخراج المسار النسبي فقط دون الـ URL الكامل
        $avatarUrl  = $response->json('data.avatar_url');
        $parsedPath = parse_url($avatarUrl, PHP_URL_PATH); // يحصل على /storage/avatars/filename.jpg
        $cleanPath  = ltrim(str_replace('/storage/', '', $parsedPath), '/'); // يحصل على avatars/filename.jpg

        Storage::disk('public')->assertExists($cleanPath);
    }
}
