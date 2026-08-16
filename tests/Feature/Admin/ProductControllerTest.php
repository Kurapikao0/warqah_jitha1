<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| ملاحظات مهمة قبل التشغيل
|--------------------------------------------------------------------------
|
| 1) الرابط المستخدم أدناه هو /api/admin/products، مطابق للـ apiResource
|    الموجود في api.php.
|
| 2) StoreProductRequest (كما أُرسل سابقاً) لا يحتوي قاعدة تحقق لعمود
|    "slug" رغم أنه NOT NULL + unique في الجدول. الاختبارات أدناه ترسل
|    slug صراحة؛ إن ظهر خطأ 500 بدل 201/422 فهذا يؤكد وجود نفس مشكلة
|    ProductCategory سابقاً ويحتاج نفس الإصلاح (إضافة قاعدة slug).
|
| 3) الحذف يستخدم SoftDeletes (الموديل يستخدم SoftDeletes trait)، لذا
|    نتحقق بـ assertSoftDeleted وليس assertDatabaseMissing.
|
| 4) status هو enum('active','inactive') على مستوى القاعدة، لكن القاعدة
|    في StoreProductRequest هي فقط "required|string" بدون in:active,inactive.
|    اختبار test_creating_a_product_fails_with_invalid_status يكشف هذه الفجوة.
|
*/

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin(): AdminUser
    {
        $role = Role::factory()->create();

        $admin = AdminUser::factory()->create([
            'role_id' => $role->id,
        ]);

        Sanctum::actingAs($admin, ['*'], 'admin');

        return $admin;
    }

    protected function validPayload(array $overrides = []): array
    {
        $category = ProductCategory::factory()->create();

        return array_merge([
            'category_id' => $category->id,
            'name' => 'Handmade Vase',
            'slug' => 'handmade-vase',
            'sku' => 'SKU-' . uniqid(),
            'description' => 'A beautiful handmade ceramic vase.',
            'price' => 49.99,
            'stock_quantity' => 10,
            'is_customizable' => false,
            'status' => 'active',
        ], $overrides);
    }

    /*
    |--------------------------------------------------------------------------
    | Authentication / Authorization
    |--------------------------------------------------------------------------
    */

    public function test_guest_cannot_access_products_endpoints(): void
    {
        $product = Product::factory()->create();

        $this->getJson('/api/admin/products')->assertUnauthorized();
        $this->postJson('/api/admin/products', [])->assertUnauthorized();
        $this->getJson("/api/admin/products/{$product->id}")->assertUnauthorized();
        $this->putJson("/api/admin/products/{$product->id}", [])->assertUnauthorized();
        $this->deleteJson("/api/admin/products/{$product->id}")->assertUnauthorized();
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_list_products(): void
    {
        $this->actingAsAdmin();

        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/products');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'sku', 'price'],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_create_a_product(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload();

        $response = $this->postJson('/api/admin/products', $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', 'Handmade Vase')
            ->assertJsonPath('data.sku', $payload['sku']);

        $this->assertDatabaseHas('products', [
            'sku' => $payload['sku'],
            'name' => 'Handmade Vase',
        ]);
    }

    public function test_creating_a_product_fails_without_a_name(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload();
        unset($payload['name']);

        $response = $this->postJson('/api/admin/products', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_creating_a_product_fails_with_a_duplicate_sku(): void
    {
        $this->actingAsAdmin();

        Product::factory()->create(['sku' => 'DUPLICATE-SKU']);

        $payload = $this->validPayload(['sku' => 'DUPLICATE-SKU']);

        $response = $this->postJson('/api/admin/products', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['sku']);
    }

    public function test_creating_a_product_fails_when_category_id_does_not_exist(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload(['category_id' => 999999]);

        $response = $this->postJson('/api/admin/products', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['category_id']);
    }

    public function test_creating_a_product_fails_with_a_negative_price(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload(['price' => -10]);

        $response = $this->postJson('/api/admin/products', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['price']);
    }

    public function test_creating_a_product_fails_with_invalid_status(): void
    {
        $this->actingAsAdmin();

        // status عمود من نوع enum('active','inactive') في قاعدة البيانات.
        // إن لم تتحقق StoreProductRequest من القيمة عبر in:active,inactive
        // فسيصل هذا الطلب لقاعدة البيانات ويفشل بخطأ 500 بدل 422 المتوقع.
        $payload = $this->validPayload(['status' => 'not-a-real-status']);

        $response = $this->postJson('/api/admin/products', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_view_a_single_product(): void
    {
        $this->actingAsAdmin();

        $product = Product::factory()->create();

        $response = $this->getJson("/api/admin/products/{$product->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $product->id);
    }

    public function test_viewing_a_non_existent_product_returns_404(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/admin/products/999999');

        $response->assertNotFound();
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_update_a_product_name(): void
    {
        $this->actingAsAdmin();

        $product = Product::factory()->create(['name' => 'Old Name']);

        $response = $this->putJson("/api/admin/products/{$product->id}", [
            'name' => 'New Name',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Product updated successfully',
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Name',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_delete_a_product(): void
    {
        $this->actingAsAdmin();

        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/admin/products/{$product->id}");

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Product deleted successfully',
            ]);

        // الموديل يستخدم SoftDeletes، لذا الصف يبقى موجوداً فعلياً
        // في الجدول لكن بقيمة deleted_at مملوءة.
        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    }

    public function test_deleting_a_non_existent_product_returns_404(): void
    {
        $this->actingAsAdmin();

        $response = $this->deleteJson('/api/admin/products/999999');

        $response->assertNotFound();
    }
}
