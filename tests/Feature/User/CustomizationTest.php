<?php

declare(strict_types=1);

namespace Tests\Feature\User;

use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductCustomizationAttributeValue;
use App\Models\ProductCustomizationRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CustomizationTest extends TestCase
{
    use RefreshDatabase;

    protected function createProductWithAttributes(
        int $count = 2
    ): array {
        $product = Product::factory()->create([
            'is_customizable' => true,
        ]);

        $attributes = ProductAttribute::factory()
            ->count($count)
            ->create([
                'input_type' => 'text',
                'is_required' => false,
                'options' => null,
            ]);

        foreach ($attributes as $index => $attribute) {
            $product->attributes()->attach(
                $attribute->id,
                [
                    'value' => 'test-value',
                ]
            );
        }

        return [$product, $attributes];
    }

    public function test_unauthenticated_customer_cannot_access_customizations(): void
    {
        $this->getJson('/api/customer/customizations')
            ->assertUnauthorized();

        $this->postJson('/api/customer/customizations', [])
            ->assertUnauthorized();
    }

    public function test_customer_can_create_customization_with_attribute_values(): void
    {
        $customer = Customer::factory()->create();

        Sanctum::actingAs(
            $customer,
            ['*'],
            'customer'
        );

        [$product, $attributes] = $this->createProductWithAttributes();

        $payload = [
            'base_product_id' => $product->id,
            'quantity' => 1,
            'attribute_values' => [
                [
                    'attribute_id' => $attributes[0]->id,
                    'value' => '50',
                ],
                [
                    'attribute_id' => $attributes[1]->id,
                    'value' => 'true',
                ],
            ],
        ];

        $response = $this->postJson(
            '/api/customer/customizations',
            $payload
        );

        $response
            ->assertCreated()
            ->assertJsonPath(
                'data.id',
                fn ($id) => is_numeric($id)
            )
            ->assertJsonCount(
                2,
                'data.attributes'
            );

        $customizationId = (int) $response->json('data.id');

        $this->assertDatabaseHas(
            'product_customization_requests',
            [
                'id' => $customizationId,
                'customer_id' => $customer->id,
                'base_product_id' => $product->id,
            ]
        );

        $this->assertDatabaseHas(
            'product_customization_attribute_values',
            [
                'customization_request_id' => $customizationId,
                'attribute_id' => $attributes[0]->id,
                'value' => '50',
            ]
        );

        $this->assertDatabaseHas(
            'product_customization_attribute_values',
            [
                'customization_request_id' => $customizationId,
                'attribute_id' => $attributes[1]->id,
                'value' => 'true',
            ]
        );

        $response->assertJsonCount(
            2,
            'data.attributes'
        );
    }

    public function test_customer_cannot_submit_attribute_not_assigned_to_selected_product(): void
    {
        $customer = Customer::factory()->create();

        Sanctum::actingAs(
            $customer,
            ['*'],
            'customer'
        );

        [$product, $productAttributes] = $this->createProductWithAttributes(1);

        $unassignedAttribute = ProductAttribute::factory()->create();

        $response = $this->postJson(
            '/api/customer/customizations',
            [
                'base_product_id' => $product->id,
                'quantity' => 1,
                'attribute_values' => [
                    [
                        'attribute_id' => $unassignedAttribute->id,
                        'value' => '50',
                    ],
                ],
            ]
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'attribute_values',
            ]);

        $this->assertDatabaseCount(
            'product_customization_requests',
            0
        );

        $this->assertDatabaseCount(
            'product_customization_attribute_values',
            0
        );
    }

    public function test_customer_cannot_view_another_customers_customization(): void
    {
        $customer = Customer::factory()->create();
        $otherCustomer = Customer::factory()->create();

        Sanctum::actingAs(
            $customer,
            ['*'],
            'customer'
        );

        $customization = ProductCustomizationRequest::factory()->create([
            'customer_id' => $otherCustomer->id,
        ]);

        $response = $this->getJson(
            "/api/customer/customizations/{$customization->id}"
        );

        $response->assertForbidden();
    }

    public function test_customer_can_view_their_own_customization_with_attributes(): void
    {
        $customer = Customer::factory()->create();

        Sanctum::actingAs(
            $customer,
            ['*'],
            'customer'
        );

        [$product, $attributes] = $this->createProductWithAttributes();

        $customization = ProductCustomizationRequest::factory()->create([
            'customer_id' => $customer->id,
            'base_product_id' => $product->id,
        ]);

        ProductCustomizationAttributeValue::create([
            'customization_request_id' => $customization->id,
            'attribute_id' => $attributes[0]->id,
            'value' => '75',
        ]);
        
        $response = $this->getJson(
            "/api/customer/customizations/{$customization->id}"
        );

        $response
            ->assertOk()
            ->assertJsonPath(
                'data.id',
                $customization->id
            )
            ->assertJsonCount(
                1,
                'data.attributes'
            );
    }

    public function test_customer_can_list_only_their_customizations(): void
    {
        $customer = Customer::factory()->create();
        $otherCustomer = Customer::factory()->create();

        Sanctum::actingAs(
            $customer,
            ['*'],
            'customer'
        );

        $ownCustomization = ProductCustomizationRequest::factory()->create([
            'customer_id' => $customer->id,
        ]);

        ProductCustomizationRequest::factory()->create([
            'customer_id' => $otherCustomer->id,
        ]);

        $response = $this->getJson(
            '/api/customer/customizations'
        );

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.id',
                $ownCustomization->id
            );
    }
}
