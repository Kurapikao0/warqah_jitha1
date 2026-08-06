<?php

namespace Tests\Unit\Services;

use App\Models\Product;
use App\Models\RawMaterial;
use App\Services\RawMaterialService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RawMaterialServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RawMaterialService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(RawMaterialService::class);
    }

    public function test_can_get_all_raw_materials(): void
    {
        RawMaterial::factory()->count(3)->create();

        $result = $this->service->getAll();

        if ($result instanceof LengthAwarePaginator) {
            $this->assertGreaterThanOrEqual(3, $result->total());
        } else {
            $this->assertInstanceOf(Collection::class, $result);
            $this->assertCount(3, $result);
        }
    }

    public function test_can_create_raw_material(): void
    {
        $product = Product::factory()->create();

        $data = [
            'name' => 'خيزران طبيعي',
            'product_id' => $product->id,
            'quantity_available' => 100.00,
            'unit' => 'kg',
            'reorder_point' => 10.00,
            'status' => 'in_stock',
        ];

        $rawMaterial = $this->service->create($data);

        $this->assertInstanceOf(RawMaterial::class, $rawMaterial);
        $this->assertDatabaseHas('raw_materials', [
            'id' => $rawMaterial->id,
            'name' => 'خيزران طبيعي',
            'product_id' => $product->id,
        ]);
    }

    public function test_can_update_raw_material(): void
    {
        $rawMaterial = RawMaterial::factory()->create([
            'quantity_available' => 50.00,
        ]);

        $updateData = [
            'quantity_available' => 150.00,
        ];

        $updatedRawMaterial = $this->service->update($rawMaterial, $updateData);

        $this->assertEquals(150.00, $updatedRawMaterial->quantity_available);
        $this->assertDatabaseHas('raw_materials', [
            'id' => $rawMaterial->id,
            'quantity_available' => 150.00,
        ]);
    }

    public function test_can_delete_raw_material(): void
    {
        $rawMaterial = RawMaterial::factory()->create();

        $this->service->delete($rawMaterial);

        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(RawMaterial::class))) {
            $this->assertSoftDeleted('raw_materials', [
                'id' => $rawMaterial->id,
            ]);
        } else {
            $this->assertDatabaseMissing('raw_materials', [
                'id' => $rawMaterial->id,
            ]);
        }
    }
}