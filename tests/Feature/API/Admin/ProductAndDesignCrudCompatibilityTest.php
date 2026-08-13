<?php

namespace Tests\Feature\API\Admin;

use App\Models\AdminUser;
use App\Models\DesignPattern;
use App\Models\ProductAttribute;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductAndDesignCrudCompatibilityTest extends TestCase
{
    use RefreshDatabase;

    protected AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = AdminUser::factory()->create();
        Gate::before(fn () => true);
        Sanctum::actingAs($this->admin, ['*'], 'admin');
        $this->actingAs($this->admin, 'admin');
    }

    public function test_product_attribute_accepts_frontend_alias_fields(): void
    {
        $response = $this->postJson('/api/admin/product-attributes', [
            'display_name' => 'Size',
            'type' => 'select',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Size')
            ->assertJsonPath('data.display_name', 'Size')
            ->assertJsonPath('data.type', 'select');

        $this->assertDatabaseHas('product_attributes', [
            'name' => 'Size',
            'input_type' => 'select',
        ]);
    }

    public function test_design_pattern_accepts_image_upload_and_alias_fields(): void
    {
        $file = UploadedFile::fake()->create('pattern-preview.png', 10, 'image/png');

        $response = $this->postJson('/api/admin/design-patterns', [
            'name' => 'Modern Lines',
            'description' => 'Pattern description',
            'image' => $file,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Modern Lines')
            ->assertJsonPath('data.image_url', $response->json('data.preview_image_url'));

        $this->assertDatabaseHas('design_patterns', [
            'name' => 'Modern Lines',
        ]);
        $this->assertNotNull($response->json('data.preview_image_url'));
    }

    public function test_design_pattern_delete_returns_success_and_removes_record(): void
    {
        $pattern = DesignPattern::factory()->create();

        $response = $this->deleteJson('/api/admin/design-patterns/' . $pattern->id);

        $response->assertStatus(200)
            ->assertJsonPath('message', 'Design pattern deleted successfully.');

        $this->assertDatabaseMissing('design_patterns', [
            'id' => $pattern->id,
        ]);
    }

    public function test_product_attribute_update_supports_frontend_alias_fields(): void
    {
        $attribute = ProductAttribute::factory()->create([
            'name' => 'Color',
            'input_type' => 'text',
        ]);

        $response = $this->putJson('/api/admin/product-attributes/' . $attribute->id, [
            'display_name' => 'Color Family',
            'type' => 'color',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Color Family')
            ->assertJsonPath('data.display_name', 'Color Family')
            ->assertJsonPath('data.type', 'color');

        $this->assertDatabaseHas('product_attributes', [
            'id' => $attribute->id,
            'name' => 'Color Family',
            'input_type' => 'color',
        ]);
    }

    public function test_product_attribute_update_allows_partial_change_without_name(): void
    {
        $attribute = ProductAttribute::factory()->create([
            'name' => 'Color',
            'input_type' => 'text',
        ]);

        $response = $this->putJson('/api/admin/product-attributes/' . $attribute->id, [
            'type' => 'select',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.type', 'select')
            ->assertJsonPath('data.name', 'Color');

        $this->assertDatabaseHas('product_attributes', [
            'id' => $attribute->id,
            'name' => 'Color',
            'input_type' => 'select',
        ]);
    }

    public function test_design_pattern_update_allows_partial_change_without_name(): void
    {
        $pattern = DesignPattern::factory()->create([
            'name' => 'Classic',
            'description' => 'Old version',
        ]);

        $file = UploadedFile::fake()->create('updated-pattern.png', 10, 'image/png');

        $response = $this->putJson('/api/admin/design-patterns/' . $pattern->id, [
            'image' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Classic');

        $this->assertDatabaseHas('design_patterns', [
            'id' => $pattern->id,
            'name' => 'Classic',
        ]);
    }
}
