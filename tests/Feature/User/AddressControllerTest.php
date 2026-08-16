<?php

namespace Tests\Feature\User;

use App\Models\Address;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;
    // البادئة الصحيحة بناءً على routes/api.php
    protected string $baseUri = '/api/customer/addresses';

    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء عميل وتوثيق الدخول به
        $this->customer = Customer::factory()->create();
        $this->actingAs($this->customer, 'customer');
    }

    #[Test]
    public function customer_can_list_their_addresses(): void
    {
        Address::factory()->count(3)->create(['customer_id' => $this->customer->id]);

        $response = $this->getJson($this->baseUri);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data',
                    'errors'
                ]);
    }

    #[Test]
    public function customer_can_create_a_new_address_successfully(): void
    {
        $payload = [
            'recipient_name' => 'أحمد علي',
            'phone'          => '967770000000',
            'country'        => 'اليمن',
            'city'           => 'صنعاء',
            'district'       => 'السبعين',
            'street'         => 'شارع حدة',
            'postal_code'    => '12345',
            'is_default'     => true,
        ];

        $response = $this->postJson($this->baseUri, $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Address created successfully',
                 ]);

        $this->assertDatabaseHas('addresses', [
            'customer_id'    => $this->customer->id,
            'recipient_name' => 'أحمد علي',
        ]);
    }

    #[Test]
    public function fails_validation_when_creating_address_without_required_fields(): void
    {
        $response = $this->postJson($this->baseUri, []);

        $response->assertStatus(422);
    }

    #[Test]
    public function customer_can_update_their_existing_address(): void
    {
        $address = Address::factory()->create([
            'customer_id' => $this->customer->id,
            'city'        => 'صنعاء',
        ]);

        $payload = [
            'recipient_name' => $address->recipient_name,
            'phone'          => $address->phone,
            'country'        => $address->country,
            'city'           => 'عدن',
            'street'         => 'شارع المعلا',
        ];

        $response = $this->putJson("{$this->baseUri}/{$address->id}", $payload);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Address updated successfully',
                 ]);

        $this->assertDatabaseHas('addresses', [
            'id'   => $address->id,
            'city' => 'عدن',
        ]);
    }

    #[Test]
    public function customer_can_delete_an_address(): void
    {
        $address = Address::factory()->create(['customer_id' => $this->customer->id]);

        $response = $this->deleteJson("{$this->baseUri}/{$address->id}");

        // الـ Controller يرجع 200 مع JSON وليس 204
        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Address deleted successfully',
                 ]);

        $this->assertSoftDeleted($address);
    }

    #[Test]
    public function customer_can_set_default_address(): void
    {
        $address = Address::factory()->create([
            'customer_id' => $this->customer->id,
            'is_default'  => false,
        ]);

        $response = $this->patchJson("{$this->baseUri}/{$address->id}/default");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Default address updated successfully',
                 ]);
    }
}
