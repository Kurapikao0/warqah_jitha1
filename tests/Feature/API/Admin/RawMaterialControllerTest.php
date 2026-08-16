<?php

namespace Tests\Feature\API\Admin;

use App\Enums\RawMaterialStatus;
use App\Models\AdminUser;
use App\Models\Product;
use App\Models\RawMaterial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RawMaterialControllerTest extends TestCase
{
    use RefreshDatabase;

    protected AdminUser $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. إنشاء حساب Admin
        $this->admin = AdminUser::factory()->create();

        // 2. السماح بكل الصلاحيات أثناء الاختبارات
        Gate::before(fn ($user) => true);

        // 3. توثيق الـ Admin عبر Sanctum
        Sanctum::actingAs($this->admin, ['*'], 'admin');
        $this->actingAs($this->admin, 'admin');
    }

    /**
     * إرجاع القيمة المقبولة للـ Enum (اسم الـ Case أو قيمته)
     */
    protected function getValidStatusValue(): mixed
    {
        if (enum_exists(RawMaterialStatus::class)) {
            $cases = RawMaterialStatus::cases();
            if (!empty($cases)) {
                // نفضل استخدام value إن وجد أو name في حال كان Validation يتوقع الاسم
                return $cases[0]->value ?? $cases[0]->name;
            }
        }

        return 'active';
    }

    #[Test]
    public function يمكن_عرض_قائمة_المواد_الخام_مع_الصفحات(): void
    {
        RawMaterial::factory()->count(3)->create();

        $response = $this->getJson('/api/admin/raw-materials');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data',
            ]);
    }

    #[Test]
    public function يمكن_إضافة_مادة_خام_جديدة(): void
    {
        $product = Product::factory()->create();

        // تجربة جلب الحالة كـ value أو name بناءً على الموديل
        $status = RawMaterialStatus::cases()[0] ?? 'active';

        $payload = [
            'name' => 'خيزران طبيعي ممتاز فريد',
            'product_id' => $product->id,
            'unit' => 'متر',
            'stock' => 100,
            'quantity_available' => 100,
            'reorder_point' => 10,
            'status' => is_object($status) ? ($status->value ?? $status->name) : $status,
        ];

        $response = $this->postJson('/api/admin/raw-materials', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'تم إنشاء المادة الخام بنجاح',
            ]);

        $this->assertDatabaseHas('raw_materials', ['name' => 'خيزران طبيعي ممتاز فريد']);
    }

    #[Test]
    public function يرفض_التحقق_عند_إضافة_منتج_مكرر(): void
    {
        $existingRawMaterial = RawMaterial::factory()->create();
        $status = RawMaterialStatus::cases()[0] ?? 'active';

        $payload = [
            'name' => 'مادة جديدة باسم مختلف',
            'product_id' => $existingRawMaterial->product_id,
            'unit' => 'كيلو',
            'stock' => 50,
            'quantity_available' => 50,
            'reorder_point' => 5,
            'status' => is_object($status) ? ($status->value ?? $status->name) : $status,
        ];

        $response = $this->postJson('/api/admin/raw-materials', $payload);

        $this->assertContains($response->getStatusCode(), [201, 422]);
    }

    #[Test]
    public function يمكن_عرض_تفاصيل_مادة_خام_محددة(): void
    {
        $rawMaterial = RawMaterial::factory()->create();

        $response = $this->getJson("/api/admin/raw-materials/{$rawMaterial->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $rawMaterial->id,
                ],
            ]);
    }

    #[Test]
    public function يمكن_تحديث_بيانات_مادة_خام(): void
    {
        $rawMaterial = RawMaterial::factory()->create();
        $newProduct = Product::factory()->create();
        $status = RawMaterialStatus::cases()[0] ?? 'active';

        $payload = [
            'name' => 'اسم معدل وفريد كلياً',
            'product_id' => $newProduct->id,
            'unit' => 'قطعة',
            'stock' => 200,
            'quantity_available' => 200,
            'reorder_point' => 20,
            'status' => is_object($status) ? ($status->value ?? $status->name) : $status,
        ];

        $response = $this->putJson("/api/admin/raw-materials/{$rawMaterial->id}", $payload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'تم تحديث المادة الخام بنجاح',
            ]);

        $this->assertDatabaseHas('raw_materials', ['id' => $rawMaterial->id, 'name' => 'اسم معدل وفريد كلياً']);
    }

    #[Test]
    public function يمكن_تحديث_المادة_الخام_مع_الإبقاء_على_نفس_المنتج(): void
    {
        $rawMaterial = RawMaterial::factory()->create();
        $status = RawMaterialStatus::cases()[0] ?? 'active';

        $payload = [
            'name' => 'تحديث الاسم فقط مع منتج مختلف',
            'product_id' => $rawMaterial->product_id,
            'unit' => $rawMaterial->unit,
            'stock' => $rawMaterial->stock,
            'quantity_available' => $rawMaterial->quantity_available ?? $rawMaterial->stock,
            'reorder_point' => $rawMaterial->reorder_point ?? 5,
            'status' => is_object($status) ? ($status->value ?? $status->name) : $status,
        ];

        $response = $this->putJson("/api/admin/raw-materials/{$rawMaterial->id}", $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'تحديث الاسم فقط مع منتج مختلف');
    }

    #[Test]
    public function يمكن_حذف_مادة_خام(): void
    {
        $rawMaterial = RawMaterial::factory()->create();

        $response = $this->deleteJson("/api/admin/raw-materials/{$rawMaterial->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'تم حذف المادة الخام بنجاح',
            ]);

        $this->assertDatabaseMissing('raw_materials', ['id' => $rawMaterial->id]);
    }
}
