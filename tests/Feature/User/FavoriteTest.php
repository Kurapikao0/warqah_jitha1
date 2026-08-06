<?php

namespace Tests\Feature\User;

use App\Models\Customer;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    protected Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء عميل وتوثيقه عبر guard الخاص بالعملاء
        $this->customer = Customer::factory()->create();
        Sanctum::actingAs($this->customer, ['*'], 'sanctum');
    }

    #[Test]
    public function customer_can_list_their_favorite_products(): void
    {
        $products = Product::factory()->count(3)->create();

        foreach ($products as $product) {
            Favorite::factory()->create([
                'customer_id' => $this->customer->id,
                'product_id'  => $product->id,
            ]);
        }

        $response = $this->getJson('/api/customer/favorites');

        $response->assertStatus(200);
    }

    #[Test]
    public function customer_can_toggle_product_to_add_and_remove_from_favorites(): void
    {
        $product = Product::factory()->create();

        // 1. التبديل الأول (إضافة للمفضلة)
        $responseAdd = $this->postJson("/api/customer/favorites/{$product->id}");

        $responseAdd->assertStatus(200);

        $this->assertDatabaseHas('favorites', [
            'customer_id' => $this->customer->id,
            'product_id'  => $product->id,
        ]);

        // 2. التبديل الثاني (إزالة من المفضلة)
        $responseRemove = $this->postJson("/api/customer/favorites/{$product->id}");

        $responseRemove->assertStatus(200);

        $this->assertDatabaseMissing('favorites', [
            'customer_id' => $this->customer->id,
            'product_id'  => $product->id,
        ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_access_favorites(): void
    {
        // إلغاء التوثيق لهذا الاختبار
        $this->app['auth']->forgetGuards();

        $response = $this->getJson('/api/customer/favorites');

        $response->assertStatus(401);
    }
}