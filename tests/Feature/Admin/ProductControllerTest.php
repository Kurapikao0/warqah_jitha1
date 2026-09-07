<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\ProductAttribute;

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
            'sku' => 'SKU-'.uniqid(),
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

    public function test_creating_a_product_without_attributes_still_works(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload();

        $response = $this->postJson('/api/admin/products', $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', $payload['name']);

        $product = Product::where('sku', $payload['sku'])->firstOrFail();

        $this->assertDatabaseMissing('product_attribute_values', [
            'product_id' => $product->id,
        ]);
    }

    public function test_creating_a_product_with_two_attributes_stores_both(): void
    {
        $this->actingAsAdmin();

        $attributeOne = ProductAttribute::factory()->create([
            'display_name' => 'اللون',
            'input_type' => 'text',
        ]);

        $attributeTwo = ProductAttribute::factory()->create([
            'display_name' => 'المقاس',
            'input_type' => 'text',
        ]);

        $payload = $this->validPayload([
            'attribute_values' => [
                [
                    'attribute_id' => $attributeOne->id,
                    'value' => 'أحمر',
                ],
                [
                    'attribute_id' => $attributeTwo->id,
                    'value' => 'كبير',
                ],
            ],
        ]);

        $response = $this->postJson('/api/admin/products', $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', $payload['name'])
            ->assertJsonFragment([
                'id' => $attributeOne->id,
                'display_name' => 'اللون',
                'value' => 'أحمر',
            ])
            ->assertJsonFragment([
                'id' => $attributeTwo->id,
                'display_name' => 'المقاس',
                'value' => 'كبير',
            ]);

        $product = Product::where('sku', $payload['sku'])->firstOrFail();

        $this->assertDatabaseHas('product_attribute_values', [
            'product_id' => $product->id,
            'attribute_id' => $attributeOne->id,
            'value' => 'أحمر',
        ]);

        $this->assertDatabaseHas('product_attribute_values', [
            'product_id' => $product->id,
            'attribute_id' => $attributeTwo->id,
            'value' => 'كبير',
        ]);
    }

    public function test_updating_product_changes_existing_attribute_value(): void
    {
        $this->actingAsAdmin();

        $attribute = ProductAttribute::factory()->create();

        $product = Product::factory()->create();

        $product->attributes()->attach($attribute->id, [
            'value' => 'قديم',
        ]);

        $response = $this->putJson("/api/admin/products/{$product->id}", [
            'attribute_values' => [
                [
                    'attribute_id' => $attribute->id,
                    'value' => 'جديد',
                ],
            ],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Product updated successfully')
            ->assertJsonFragment([
                'id' => $attribute->id,
                'value' => 'جديد',
            ]);

        $this->assertDatabaseHas('product_attribute_values', [
            'product_id' => $product->id,
            'attribute_id' => $attribute->id,
            'value' => 'جديد',
        ]);

        $this->assertDatabaseMissing('product_attribute_values', [
            'product_id' => $product->id,
            'attribute_id' => $attribute->id,
            'value' => 'قديم',
        ]);
    }

    public function test_updating_product_removes_attribute_no_longer_submitted(): void
    {
        $this->actingAsAdmin();

        $attributeOne = ProductAttribute::factory()->create();
        $attributeTwo = ProductAttribute::factory()->create();

        $product = Product::factory()->create();

        $product->attributes()->attach([
            $attributeOne->id => ['value' => 'قيمة 1'],
            $attributeTwo->id => ['value' => 'قيمة 2'],
        ]);

        $response = $this->putJson("/api/admin/products/{$product->id}", [
            'attribute_values' => [
                [
                    'attribute_id' => $attributeOne->id,
                    'value' => 'قيمة 1 محدثة',
                ],
            ],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Product updated successfully');

        $this->assertDatabaseHas('product_attribute_values', [
            'product_id' => $product->id,
            'attribute_id' => $attributeOne->id,
            'value' => 'قيمة 1 محدثة',
        ]);

        $this->assertDatabaseMissing('product_attribute_values', [
            'product_id' => $product->id,
            'attribute_id' => $attributeTwo->id,
        ]);
    }

    public function test_updating_product_adds_new_attribute(): void
    {
        $this->actingAsAdmin();

        $existingAttribute = ProductAttribute::factory()->create();
        $newAttribute = ProductAttribute::factory()->create();

        $product = Product::factory()->create();

        $product->attributes()->attach($existingAttribute->id, [
            'value' => 'قديم',
        ]);

        $response = $this->putJson("/api/admin/products/{$product->id}", [
            'attribute_values' => [
                [
                    'attribute_id' => $existingAttribute->id,
                    'value' => 'محدث',
                ],
                [
                    'attribute_id' => $newAttribute->id,
                    'value' => 'جديد',
                ],
            ],
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('message', 'Product updated successfully');

        $this->assertDatabaseHas('product_attribute_values', [
            'product_id' => $product->id,
            'attribute_id' => $existingAttribute->id,
            'value' => 'محدث',
        ]);

        $this->assertDatabaseHas('product_attribute_values', [
            'product_id' => $product->id,
            'attribute_id' => $newAttribute->id,
            'value' => 'جديد',
        ]);
    }

    public function test_duplicate_attribute_id_in_one_request_is_rejected(): void
    {
        $this->actingAsAdmin();

        $attribute = ProductAttribute::factory()->create();

        $payload = $this->validPayload([
            'attribute_values' => [
                [
                    'attribute_id' => $attribute->id,
                    'value' => 'الأول',
                ],
                [
                    'attribute_id' => $attribute->id,
                    'value' => 'الثاني',
                ],
            ],
        ]);

        $response = $this->postJson('/api/admin/products', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'attribute_values.1.attribute_id',
            ]);
    }

    public function test_invalid_attribute_id_is_rejected(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload([
            'attribute_values' => [
                [
                    'attribute_id' => 999999,
                    'value' => 'قيمة',
                ],
        ],
    ]);

    $response = $this->postJson('/api/admin/products', $payload);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            'attribute_values.0.attribute_id',
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
