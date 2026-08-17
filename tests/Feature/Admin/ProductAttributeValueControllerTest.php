<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| ملاحظات مهمة قبل التشغيل
|--------------------------------------------------------------------------
|
| 1) الرابط المستخدم أدناه هو /api/admin/product-attribute-values، مطابق
|    للـ apiResource الموجود في api.php.
|
| 2) الموديل $timestamps = false، لذا لا نتحقق من created_at/updated_at.
|
| 3) UpdateProductAttributeValueRequest يجعل "value" مطلوباً دائماً
|    (وليس sometimes) - نفس نمط ProductAttribute، تصميم PUT كامل.
|
*/

class ProductAttributeValueControllerTest extends TestCase
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
        $product = Product::factory()->create();
        $attribute = ProductAttribute::factory()->create();

        return array_merge([
            'product_id' => $product->id,
            'attribute_id' => $attribute->id,
            'value' => 'Red',
        ], $overrides);
    }

    /*
    |--------------------------------------------------------------------------
    | Authentication / Authorization
    |--------------------------------------------------------------------------
    */

    public function test_guest_cannot_access_product_attribute_values_endpoints(): void
    {
        $value = ProductAttributeValue::factory()->create();

        $this->getJson('/api/admin/product-attribute-values')->assertUnauthorized();
        $this->postJson('/api/admin/product-attribute-values', [])->assertUnauthorized();
        $this->getJson("/api/admin/product-attribute-values/{$value->id}")->assertUnauthorized();
        $this->putJson("/api/admin/product-attribute-values/{$value->id}", [])->assertUnauthorized();
        $this->deleteJson("/api/admin/product-attribute-values/{$value->id}")->assertUnauthorized();
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_list_product_attribute_values(): void
    {
        $this->actingAsAdmin();

        ProductAttributeValue::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/product-attribute-values');

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_create_a_product_attribute_value(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload();

        $response = $this->postJson('/api/admin/product-attribute-values', $payload);

        $response
            ->assertCreated()
            ->assertJson([
                'message' => 'Product attribute value created successfully.',
            ])
            ->assertJsonPath('data.value', 'Red');

        $this->assertDatabaseHas('product_attribute_values', [
            'product_id' => $payload['product_id'],
            'attribute_id' => $payload['attribute_id'],
            'value' => 'Red',
        ]);
    }

    public function test_creating_a_product_attribute_value_fails_without_a_product_id(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload();
        unset($payload['product_id']);

        $response = $this->postJson('/api/admin/product-attribute-values', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['product_id']);
    }

    public function test_creating_a_product_attribute_value_fails_when_product_id_does_not_exist(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload(['product_id' => 999999]);

        $response = $this->postJson('/api/admin/product-attribute-values', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['product_id']);
    }

    public function test_creating_a_product_attribute_value_fails_without_an_attribute_id(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload();
        unset($payload['attribute_id']);

        $response = $this->postJson('/api/admin/product-attribute-values', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['attribute_id']);
    }

    public function test_creating_a_product_attribute_value_fails_when_attribute_id_does_not_exist(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload(['attribute_id' => 999999]);

        $response = $this->postJson('/api/admin/product-attribute-values', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['attribute_id']);
    }

    public function test_creating_a_product_attribute_value_fails_without_a_value(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload();
        unset($payload['value']);

        $response = $this->postJson('/api/admin/product-attribute-values', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['value']);
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_view_a_single_product_attribute_value(): void
    {
        $this->actingAsAdmin();

        $value = ProductAttributeValue::factory()->create();

        $response = $this->getJson("/api/admin/product-attribute-values/{$value->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $value->id);
    }

    public function test_viewing_a_non_existent_product_attribute_value_returns_404(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/admin/product-attribute-values/999999');

        $response->assertNotFound();
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_update_a_product_attribute_value(): void
    {
        $this->actingAsAdmin();

        $value = ProductAttributeValue::factory()->create(['value' => 'Old Value']);

        $response = $this->putJson("/api/admin/product-attribute-values/{$value->id}", [
            'value' => 'New Value',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Product attribute value updated successfully.',
            ])
            ->assertJsonPath('data.value', 'New Value');

        $this->assertDatabaseHas('product_attribute_values', [
            'id' => $value->id,
            'value' => 'New Value',
        ]);
    }

    public function test_updating_a_product_attribute_value_fails_without_a_value(): void
    {
        $this->actingAsAdmin();

        $value = ProductAttributeValue::factory()->create();

        $response = $this->putJson("/api/admin/product-attribute-values/{$value->id}", []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['value']);
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_delete_a_product_attribute_value(): void
    {
        $this->actingAsAdmin();

        $value = ProductAttributeValue::factory()->create();

        $response = $this->deleteJson("/api/admin/product-attribute-values/{$value->id}");

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Product attribute value deleted successfully.',
            ]);

        $this->assertDatabaseMissing('product_attribute_values', [
            'id' => $value->id,
        ]);
    }

    public function test_deleting_a_non_existent_product_attribute_value_returns_404(): void
    {
        $this->actingAsAdmin();

        $response = $this->deleteJson('/api/admin/product-attribute-values/999999');

        $response->assertNotFound();
    }
}
