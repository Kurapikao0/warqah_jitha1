<?php

namespace Tests\Feature\Admin;

use App\Models\AdminUser;
use App\Models\Color;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class ColorTest extends TestCase
{
    use RefreshDatabase;

    protected AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. تجاوز السياسات (Policies)
        Gate::before(fn () => true);

        // 2. إنشاء المشرف وتوليد توكن Sanctum مخصص
        $this->admin = AdminUser::factory()->create();

        // تمرير التوكن في Headers لكل الطلبات
        $token = $this->admin->createToken('admin-token')->plainTextToken;
        $this->withHeader('Authorization', "Bearer {$token}");
    }

    public function test_admin_can_list_all_colors(): void
    {
        Color::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/colors');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_a_new_color(): void
    {
        $payload = [
            'name'     => 'أزرق ملكي',
            'hex_code' => '#4169E1',
        ];

        $response = $this->postJson('/api/admin/colors', $payload);

        $response->assertStatus(201);

        $this->assertDatabaseHas('colors', [
            'name'     => 'أزرق ملكي',
            'hex_code' => '#4169E1',
        ]);
    }

    public function test_admin_can_update_a_color(): void
    {
        $color = Color::factory()->create([
            'name'     => 'أخضر',
            'hex_code' => '#008000',
        ]);

        $payload = [
            'name'     => 'أخضر غامق',
            'hex_code' => '#006400',
        ];

        $response = $this->putJson("/api/admin/colors/{$color->id}", $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('colors', [
            'id'       => $color->id,
            'name'     => 'أخضر غامق',
            'hex_code' => '#006400',
        ]);
    }

    public function test_admin_can_delete_a_color(): void
    {
        $color = Color::factory()->create();

        $response = $this->deleteJson("/api/admin/colors/{$color->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('colors', [
            'id' => $color->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_manage_colors(): void
    {
        $response = $this->flushHeaders()->getJson('/api/admin/colors');

        $response->assertStatus(401);
    }
}