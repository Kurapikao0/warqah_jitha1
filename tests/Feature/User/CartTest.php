<?php

namespace Tests\Feature\User;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء عميل جديد وتوثيقه
        $this->customer = Customer::factory()->create();
    }

    #[Test]
    public function unauthenticated_customer_cannot_access_cart_endpoints(): void
    {
        // محاولة عرض السلة بدون تسجيل دخول
        $response = $this->getJson('/api/customer/cart');

        $response->assertStatus(401);
    }

    #[Test]
public function authenticated_customer_can_view_their_cart(): void
{
    Sanctum::actingAs($this->customer, ['*'], 'sanctum');

    // إنشاء سلة للعميل
    Cart::factory()->create([
        'customer_id' => $this->customer->id,
    ]);

    $response = $this->getJson('/api/customer/cart');

    $response->assertStatus(200);
}

    #[Test]
    public function authenticated_customer_can_add_product_to_cart(): void
    {
        Sanctum::actingAs($this->customer, ['*'], 'sanctum');

        $product = Product::factory()->create([
            'status' => 'active',
        ]);

        $payload = [
            'product_id' => $product->id,
            'quantity'   => 2,
        ];

        $response = $this->postJson('/api/customer/cart/items', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);
    }

    #[Test]
    public function authenticated_customer_can_update_cart_item_quantity(): void
    {
        Sanctum::actingAs($this->customer, ['*'], 'sanctum');

        $cart = Cart::factory()->create(['customer_id' => $this->customer->id]);
        $product = Product::factory()->create();
        $cartItem = CartItem::factory()->create([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
            'quantity'   => 1,
        ]);

        $payload = [
            'quantity' => 5,
        ];

        $response = $this->putJson("/api/customer/cart/items/{$cartItem->id}", $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('cart_items', [
            'id'       => $cartItem->id,
            'quantity' => 5,
        ]);
    }

    #[Test]
    public function authenticated_customer_can_remove_an_item_from_cart(): void
    {
        Sanctum::actingAs($this->customer, ['*'], 'sanctum');

        $cart = Cart::factory()->create(['customer_id' => $this->customer->id]);
        $product = Product::factory()->create();
        $cartItem = CartItem::factory()->create([
            'cart_id'    => $cart->id,
            'product_id' => $product->id,
        ]);

        $response = $this->deleteJson("/api/customer/cart/items/{$cartItem->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    }
}