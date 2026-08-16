<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\DesignPattern;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| ملاحظات مهمة قبل التشغيل
|--------------------------------------------------------------------------
|
| 1) الرابط المستخدم أدناه هو /api/admin/design-patterns، مطابق للـ
|    apiResource الموجود في api.php.
|
| 2) لم يصل ملف DesignPatternPolicy ولا DesignPatternRepository. إن لم
|    تكن الـ Policy موجودة أصلاً في المشروع، ستفشل كل الاختبارات بخطأ
|    403 (Unauthorized) بدل النتائج المتوقعة - وهذا سيكون مؤشراً واضحاً
|    يوجهنا لإنشائها.
|
| 3) UpdateDesignPatternRequest يجعل "name" مطلوباً دائماً (وليس sometimes)
|    - نفس نمط PUT كامل الاستبدال في الصفحات السابقة.
|
*/

class DesignPatternControllerTest extends TestCase
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

    public function test_guest_cannot_access_design_patterns_endpoints(): void
    {
        $pattern = DesignPattern::factory()->create();

        $this->getJson('/api/admin/design-patterns')->assertUnauthorized();
        $this->postJson('/api/admin/design-patterns', [])->assertUnauthorized();
        $this->getJson("/api/admin/design-patterns/{$pattern->id}")->assertUnauthorized();
        $this->putJson("/api/admin/design-patterns/{$pattern->id}", [])->assertUnauthorized();
        $this->deleteJson("/api/admin/design-patterns/{$pattern->id}")->assertUnauthorized();
    }

    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_list_design_patterns(): void
    {
        $this->actingAsAdmin();

        DesignPattern::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/design-patterns');

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_create_a_design_pattern(): void
    {
        $this->actingAsAdmin();

        $payload = [
            'name' => 'Floral Pattern',
            'description' => 'A delicate floral design.',
            'preview_image_url' => 'https://example.com/floral.jpg',
        ];

        $response = $this->postJson('/api/admin/design-patterns', $payload);

        $response
            ->assertCreated()
            ->assertJson([
                'message' => 'Design pattern created successfully.',
            ])
            ->assertJsonPath('data.name', 'Floral Pattern');

        $this->assertDatabaseHas('design_patterns', [
            'name' => 'Floral Pattern',
        ]);
    }

    public function test_admin_can_create_a_design_pattern_without_optional_fields(): void
    {
        $this->actingAsAdmin();

        $payload = [
            'name' => 'Minimal Pattern',
        ];

        $response = $this->postJson('/api/admin/design-patterns', $payload);

        $response->assertCreated();

        $this->assertDatabaseHas('design_patterns', [
            'name' => 'Minimal Pattern',
        ]);
    }

    public function test_creating_a_design_pattern_fails_without_a_name(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/admin/design-patterns', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_creating_a_design_pattern_fails_with_a_duplicate_name(): void
    {
        $this->actingAsAdmin();

        DesignPattern::factory()->create(['name' => 'Stripes']);

        $response = $this->postJson('/api/admin/design-patterns', [
            'name' => 'Stripes',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_creating_a_design_pattern_fails_with_an_invalid_preview_image_url(): void
    {
        $this->actingAsAdmin();

        $response = $this->postJson('/api/admin/design-patterns', [
            'name' => 'Geometric',
            'preview_image_url' => 'not-a-valid-url',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['preview_image_url']);
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_view_a_single_design_pattern(): void
    {
        $this->actingAsAdmin();

        $pattern = DesignPattern::factory()->create();

        $response = $this->getJson("/api/admin/design-patterns/{$pattern->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $pattern->id);
    }

    public function test_viewing_a_non_existent_design_pattern_returns_404(): void
    {
        $this->actingAsAdmin();

        $response = $this->getJson('/api/admin/design-patterns/999999');

        $response->assertNotFound();
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_update_a_design_pattern(): void
    {
        $this->actingAsAdmin();

        $pattern = DesignPattern::factory()->create(['name' => 'Old Name']);

        // UpdateDesignPatternRequest يجعل name مطلوباً دائماً.
        $response = $this->putJson("/api/admin/design-patterns/{$pattern->id}", [
            'name' => 'New Name',
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Design pattern updated successfully.',
            ])
            ->assertJsonPath('data.name', 'New Name');

        $this->assertDatabaseHas('design_patterns', [
            'id' => $pattern->id,
            'name' => 'New Name',
        ]);
    }

    public function test_updating_a_design_pattern_fails_without_a_name(): void
    {
        $this->actingAsAdmin();

        $pattern = DesignPattern::factory()->create();

        $response = $this->putJson("/api/admin/design-patterns/{$pattern->id}", []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function test_admin_can_delete_a_design_pattern(): void
    {
        $this->actingAsAdmin();

        $pattern = DesignPattern::factory()->create();

        $response = $this->deleteJson("/api/admin/design-patterns/{$pattern->id}");

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Design pattern deleted successfully.',
            ]);

        $this->assertDatabaseMissing('design_patterns', [
            'id' => $pattern->id,
        ]);
    }

    public function test_deleting_a_non_existent_design_pattern_returns_404(): void
    {
        $this->actingAsAdmin();

        $response = $this->deleteJson('/api/admin/design-patterns/999999');

        $response->assertNotFound();
    }
}
