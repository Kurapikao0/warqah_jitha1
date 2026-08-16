<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
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
| 1) موديل الأدمن الفعلي هو App\Models\AdminUser (يستخدم HasApiTokens).
|    يتطلب role_id إجباري (belongsTo Role)، لذا ننشئ Role أولاً.
|
| 2) الرابط المستخدم أدناه هو /api/admin/product-categories، عدّله إذا
|    كان الـ prefix عندك مختلفاً.
|
*/

class ProductCategoryControllerTest extends TestCase
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

    public function test_guest_cannot_access_product_categories_endpoints(): void
    {
        $category = ProductCategory::factory()->create();

        $this->getJson('/api/admin/product-categories')->assertUnauthorized();
        $this->postJson('/api/admin/product-categories', [])->assertUnauthorized();
        $this->getJson("/api/admin/product-categories/{$category->id}")->assertUnauthorized();
        $this->putJson("/api/admin/product-categories/{$category->id}", [])->assertUnauthorized();
        $this->deleteJson("/api/admin/product-categories/{$category->id}")->assertUnauthorized();
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_list_product_categories(): void
    {
        $this->actingAsAdmin();

        ProductCategory::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/product-categories');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug', 'image_url', 'parent_id', 'created_at', 'updated_at'],
                ],
            ])
            ->assertJsonCount(3, 'data');
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_create_a_product_category(): void
    {
        $this->actingAsAdmin();

        $payload = [
            'name' => 'Electronics',
            'slug' => 'electronics',
            'parent_id' => null,
            'image_url' => 'https://example.com/images/electronics.png',
        ];

        $response = $this->postJson('/api/admin/product-categories', $payload);

        $response
            ->assertCreated()
            ->assertJson([
                'success' => true,
                'data' => [
                    'name' => 'Electronics',
                    'slug' => 'electronics',
                ],
            ]);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);
    }

    public function test_admin_can_create_a_nested_product_category_with_a_parent(): void
    {
        $this->actingAsAdmin();

        $parent = ProductCategory::factory()->create();

        $payload = [
            'name' => 'Laptops',
            'slug' => 'laptops',
            'parent_id' => $parent->id,
        ];

        $response = $this->postJson('/api/admin/product-categories', $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('data.parent_id', $parent->id);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'Laptops',
            'parent_id' => $parent->id,
        ]);
    }

    public function test_creating_a_product_category_fails_without_a_name(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/admin/product-categories', [
            'slug' => 'no-name-category',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_creating_a_product_category_fails_with_a_duplicate_slug(): void
    {
        $this->actingAsAdmin();

        ProductCategory::factory()->create(['slug' => 'accessories']);

        $response = $this->postJson('/api/admin/product-categories', [
            'name' => 'Accessories 2',
            'slug' => 'accessories',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['slug']);
    }

    public function test_creating_a_product_category_fails_when_parent_id_does_not_exist(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/admin/product-categories', [
            'name' => 'Orphan Category',
            'slug' => 'orphan-category',
            'parent_id' => 999999,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['parent_id']);
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_view_a_single_product_category(): void
    {
        $this->actingAsAdmin();

        $category = ProductCategory::factory()->create();

        $response = $this->getJson("/api/admin/product-categories/{$category->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $category->id)
            ->assertJsonPath('data.name', $category->name);
    }

    public function test_viewing_a_non_existent_product_category_returns_404(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/admin/product-categories/999999');

        $response->assertNotFound();
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_update_a_product_category_name(): void
    {
        $this->actingAsAdmin();

        $category = ProductCategory::factory()->create(['name' => 'Old Name']);

        $response = $this->putJson("/api/admin/product-categories/{$category->id}", [
            'name' => 'New Name',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.name', 'New Name');

        $this->assertDatabaseHas('product_categories', [
            'id' => $category->id,
            'name' => 'New Name',
        ]);
    }

    public function test_updating_a_product_category_with_an_invalid_name_fails(): void
    {
        $this->actingAsAdmin();

        $category = ProductCategory::factory()->create();

        $response = $this->putJson("/api/admin/product-categories/{$category->id}", [
            'name' => str_repeat('a', 300), // يتجاوز max:255
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_delete_a_product_category(): void
    {
        $this->actingAsAdmin();

        $category = ProductCategory::factory()->create();

        $response = $this->deleteJson("/api/admin/product-categories/{$category->id}");

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Category deleted successfully.',
            ]);

        $this->assertDatabaseMissing('product_categories', [
            'id' => $category->id,
        ]);
    }

    public function test_deleting_a_non_existent_product_category_returns_404(): void
    {
        $this->actingAsAdmin();

        $response = $this->deleteJson('/api/admin/product-categories/999999');

        $response->assertNotFound();
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function test_deleting_a_parent_category_does_not_fail_when_it_has_children(): void
    {
        $this->actingAsAdmin();

        $parent = ProductCategory::factory()->create();
        $child = ProductCategory::factory()->create(['parent_id' => $parent->id]);

        $this->deleteJson("/api/admin/product-categories/{$parent->id}")->assertOk();

        $this->assertDatabaseHas('product_categories', [
            'id' => $child->id,
            'parent_id' => null,
        ]);
    }
}
