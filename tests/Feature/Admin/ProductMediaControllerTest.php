<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| ملاحظات مهمة قبل التشغيل
|--------------------------------------------------------------------------
|
| 1) الرابط المستخدم أدناه هو /api/admin/product-media، مطابق للـ
|    apiResource الموجود في api.php. اسم المعامل في route model binding
|    هو {productMedia} (اسم الكلاس ProductMedia بصيغة camelCase).
|
| 2) قاعدة "url" في StoreProductMediaRequest هي required|url بدون max:255،
|    بينما العمود في القاعدة VARCHAR(255). الاختبار
|    test_creating_media_fails_with_an_overly_long_url يكشف ما إذا كان
|    هذا يسبب خطأ 500 بدل 422 المتوقع.
|
| 3) sort_order قاعدته nullable|integer|min:1 بينما الافتراضي في القاعدة 0،
|    لذا القيمة 0 الصريحة سترفض (هذا سلوك متعمد على ما يبدو، وليس بحثاً
|    عن خطأ، لكن موثّق هنا في حال أردت تغييره لاحقاً).
|
*/

class ProductMediaControllerTest extends TestCase
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

        return array_merge([
            'product_id' => $product->id,
            'media_type' => 'image',
            'url' => 'https://example.com/images/product-1.jpg',
            'sort_order' => 1,
            'is_primary' => false,
        ], $overrides);
    }

    /*
    |--------------------------------------------------------------------------
    | Authentication / Authorization
    |--------------------------------------------------------------------------
    */

    public function test_guest_cannot_access_product_media_endpoints(): void
    {
        $media = ProductMedia::factory()->create();

        $this->getJson('/api/admin/product-media')->assertUnauthorized();
        $this->postJson('/api/admin/product-media', [])->assertUnauthorized();
        $this->getJson("/api/admin/product-media/{$media->id}")->assertUnauthorized();
        $this->putJson("/api/admin/product-media/{$media->id}", [])->assertUnauthorized();
        $this->deleteJson("/api/admin/product-media/{$media->id}")->assertUnauthorized();
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_list_product_media(): void
    {
        $this->actingAsAdmin();

        ProductMedia::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/product-media');

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_create_product_media(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload();

        $response = $this->postJson('/api/admin/product-media', $payload);

        $response
            ->assertCreated()
            ->assertJson([
                'message' => 'Media created successfully.',
            ])
            ->assertJsonPath('data.url', $payload['url']);

        $this->assertDatabaseHas('product_media', [
            'product_id' => $payload['product_id'],
            'url' => $payload['url'],
        ]);
    }

    public function test_creating_media_fails_without_a_product_id(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload();
        unset($payload['product_id']);

        $response = $this->postJson('/api/admin/product-media', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['product_id']);
    }

    public function test_creating_media_fails_when_product_id_does_not_exist(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload(['product_id' => 999999]);

        $response = $this->postJson('/api/admin/product-media', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['product_id']);
    }

    public function test_creating_media_fails_with_an_invalid_media_type(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload(['media_type' => 'not-a-real-type']);

        $response = $this->postJson('/api/admin/product-media', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['media_type']);
    }

    public function test_creating_media_fails_with_an_invalid_url(): void
    {
        $this->actingAsAdmin();

        $payload = $this->validPayload(['url' => 'not-a-valid-url']);

        $response = $this->postJson('/api/admin/product-media', $payload);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['url']);
    }

    public function test_creating_media_fails_with_an_overly_long_url(): void
    {
        $this->actingAsAdmin();

        // العمود url هو VARCHAR(255) لكن القاعدة الحالية "required|url" بدون
        // max:255. هذا الاختبار يكشف إن كان الطلب سيمر من التحقق (422 متوقع)
        // لكن يفشل عند الإدخال الفعلي في قاعدة البيانات (500 غير متوقع).
        $longPath = str_repeat('a', 300);

        $payload = $this->validPayload([
            'url' => "https://example.com/{$longPath}.jpg",
        ]);

        $response = $this->postJson('/api/admin/product-media', $payload);

        $response->assertUnprocessable();
    }

    public function test_setting_a_media_as_primary_unsets_other_primary_media_for_the_same_product(): void
    {
        $this->actingAsAdmin();

        $product = Product::factory()->create();

        $existingPrimary = ProductMedia::factory()->create([
            'product_id' => $product->id,
            'is_primary' => true,
        ]);

        $payload = $this->validPayload([
            'product_id' => $product->id,
            'is_primary' => true,
        ]);

        $this->postJson('/api/admin/product-media', $payload)->assertCreated();

        $this->assertDatabaseHas('product_media', [
            'id' => $existingPrimary->id,
            'is_primary' => false,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_view_a_single_product_media(): void
    {
        $this->actingAsAdmin();

        $media = ProductMedia::factory()->create();

        $response = $this->getJson("/api/admin/product-media/{$media->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $media->id);
    }

    public function test_viewing_a_non_existent_product_media_returns_404(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/admin/product-media/999999');

        $response->assertNotFound();
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_update_product_media_sort_order(): void
    {
        $this->actingAsAdmin();

        $media = ProductMedia::factory()->create(['sort_order' => 1]);

        $response = $this->putJson("/api/admin/product-media/{$media->id}", [
            'sort_order' => 5,
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Media updated successfully.',
            ])
            ->assertJsonPath('data.sort_order', 5);

        $this->assertDatabaseHas('product_media', [
            'id' => $media->id,
            'sort_order' => 5,
        ]);
    }

    public function test_updating_media_fails_with_an_invalid_media_type(): void
    {
        $this->actingAsAdmin();

        $media = ProductMedia::factory()->create();

        $response = $this->putJson("/api/admin/product-media/{$media->id}", [
            'media_type' => 'not-a-real-type',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['media_type']);
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_delete_product_media(): void
    {
        $this->actingAsAdmin();

        $media = ProductMedia::factory()->create();

        $response = $this->deleteJson("/api/admin/product-media/{$media->id}");

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Media deleted successfully.',
            ]);

        // لا يوجد SoftDeletes على هذا الموديل، لذا الحذف فعلي من الجدول.
        $this->assertDatabaseMissing('product_media', [
            'id' => $media->id,
        ]);
    }

    public function test_deleting_a_non_existent_product_media_returns_404(): void
    {
        $this->actingAsAdmin();

        $response = $this->deleteJson('/api/admin/product-media/999999');

        $response->assertNotFound();
    }
}
