<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\ProductAttribute;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| ملاحظات مهمة قبل التشغيل
|--------------------------------------------------------------------------
|
| 1) الرابط المستخدم أدناه هو /api/admin/product-attributes، مطابق
|    للـ apiResource الموجود في api.php.
|
| 2) ملف App\Http\Requests\ProductAttribute\UpdateProductAttributeRequest
|    لم يصل فعلياً (الملف المرسل بنفس الاسم كان في الحقيقة
|    UpdateProductMediaRequest بالخطأ). الاختبارات أدناه المتعلقة بالتحديث
|    مبنية على افتراض منطقي (name و input_type بصيغة "sometimes")
|    مطابق لنمط StoreProductAttributeRequest. تحقق من الملف الفعلي
|    وأرسله إن اختلفت النتيجة.
|
| 3) input_type هو enum (ProductAttributeInputType) يُتحقق منه عبر
|    Illuminate\Validation\Rules\Enum - القيم الفعلية غير معروفة لنا
|    (الملف enum لم يُرسل)، لذا نستخدم قيمة نفترض أنها صالحة "text"
|    في الحالات الناجحة. إن فشل الاختبار بسبب هذه القيمة تحديداً،
|    أرسل محتوى App\Enums\ProductAttributeInputType.
|
*/

class ProductAttributeControllerTest extends TestCase
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

    /*
    |--------------------------------------------------------------------------
    | Authentication / Authorization
    |--------------------------------------------------------------------------
    */

    public function test_guest_cannot_access_product_attributes_endpoints(): void
    {
        $attribute = ProductAttribute::factory()->create();

        $this->getJson('/api/admin/product-attributes')->assertUnauthorized();
        $this->postJson('/api/admin/product-attributes', [])->assertUnauthorized();
        $this->getJson("/api/admin/product-attributes/{$attribute->id}")->assertUnauthorized();
        $this->putJson("/api/admin/product-attributes/{$attribute->id}", [])->assertUnauthorized();
        $this->deleteJson("/api/admin/product-attributes/{$attribute->id}")->assertUnauthorized();
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_list_product_attributes(): void
    {
        $this->actingAsAdmin();

        ProductAttribute::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/product-attributes');

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_create_a_product_attribute(): void
    {
        $this->actingAsAdmin();

        $payload = [
            'name' => 'Color',
            'input_type' => 'text',
        ];

        $response = $this->postJson('/api/admin/product-attributes', $payload);

        $response
            ->assertCreated()
            ->assertJson([
                'message' => 'Product attribute created successfully.',
            ])
            ->assertJsonPath('data.name', 'Color');

        $this->assertDatabaseHas('product_attributes', [
            'name' => 'Color',
        ]);
    }

    public function test_creating_a_product_attribute_fails_without_a_name(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/admin/product-attributes', [
            'input_type' => 'text',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_creating_a_product_attribute_fails_with_a_duplicate_name(): void
    {
        $this->actingAsAdmin();

        ProductAttribute::factory()->create(['name' => 'Size']);

        $response = $this->postJson('/api/admin/product-attributes', [
            'name' => 'Size',
            'input_type' => 'text',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_creating_a_product_attribute_fails_with_an_invalid_input_type(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/admin/product-attributes', [
            'name' => 'Material',
            'input_type' => 'not-a-real-type',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['input_type']);
    }

    public function test_creating_a_product_attribute_fails_without_an_input_type(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/admin/product-attributes', [
            'name' => 'Material',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['input_type']);
    }

    public function test_admin_can_create_a_select_product_attribute_with_options(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/admin/product-attributes', [
            'name' => 'Color',
            'display_name' => 'اللون',
            'input_type' => 'select',
            'is_required' => true,
            'options' => [
                'أحمر',
                'أزرق',
                'أخضر',
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.name', 'Color')
            ->assertJsonPath('data.input_type', 'select')
            ->assertJsonPath('data.options.0', 'أحمر')
            ->assertJsonPath('data.options.1', 'أزرق')
            ->assertJsonPath('data.options.2', 'أخضر');

        $this->assertDatabaseHas('product_attributes', [
            'name' => 'Color',
        ]);
    }
    public function test_creating_a_product_attribute_rejects_empty_option(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/admin/product-attributes', [
            'name' => 'Size',
            'display_name' => 'المقاس',
            'input_type' => 'select',
            'options' => [
                'صغير',
                '',
            ],
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['options.1']);
    }   
    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_view_a_single_product_attribute(): void
    {
        $this->actingAsAdmin();

        $attribute = ProductAttribute::factory()->create();

        $response = $this->getJson("/api/admin/product-attributes/{$attribute->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $attribute->id);
    }

    public function test_viewing_a_non_existent_product_attribute_returns_404(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/admin/product-attributes/999999');

        $response->assertNotFound();
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_update_a_product_attribute(): void
    {
        $this->actingAsAdmin();

        $attribute = ProductAttribute::factory()->create(['name' => 'Old Name']);

        // UpdateProductAttributeRequest يتطلب كل الحقول (name و input_type)
        // معاً حتى في التحديث - تصميم PUT كامل الاستبدال، وليس تحديثاً جزئياً.
        $response = $this->putJson("/api/admin/product-attributes/{$attribute->id}", [
            'name' => 'New Name',
            'input_type' => 'text',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Product attribute updated successfully.',
            ])
            ->assertJsonPath('data.name', 'New Name');

        $this->assertDatabaseHas('product_attributes', [
            'id' => $attribute->id,
            'name' => 'New Name',
        ]);
    }

    public function test_updating_a_product_attribute_fails_without_an_input_type(): void
    {
        $this->actingAsAdmin();

        $attribute = ProductAttribute::factory()->create();

        $response = $this->putJson("/api/admin/product-attributes/{$attribute->id}", [
            'name' => 'New Name',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['input_type']);
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_delete_a_product_attribute(): void
    {
        $this->actingAsAdmin();

        $attribute = ProductAttribute::factory()->create();

        $response = $this->deleteJson("/api/admin/product-attributes/{$attribute->id}");

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Product attribute deleted successfully.',
            ]);

        $this->assertDatabaseMissing('product_attributes', [
            'id' => $attribute->id,
        ]);
    }

    public function test_deleting_a_non_existent_product_attribute_returns_404(): void
    {
        $this->actingAsAdmin();

        $response = $this->deleteJson('/api/admin/product-attributes/999999');

        $response->assertNotFound();
    }
}
