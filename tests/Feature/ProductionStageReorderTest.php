<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\OrderProductionStage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductionStageReorderTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_reorder_all_production_stages(): void
    {
        Sanctum::actingAs(
            AdminUser::factory()->create(),
            ['*'],
            'admin'
        );

        $stages = collect([
            ['name' => 'تجهيز', 'sort_order' => 9],
            ['name' => 'حياكة', 'sort_order' => 19],
            ['name' => 'جاهز', 'sort_order' => 23],
            ['name' => 'تشطيب', 'sort_order' => 50],
            ['name' => 'قص', 'sort_order' => 57],
        ])->map(
            fn (array $data) => OrderProductionStage::create($data)
        );

        $stageIds = $stages
            ->pluck('id')
            ->reverse()
            ->values()
            ->all();

        $response = $this->postJson(
            '/api/admin/production-stages/reorder',
            [
                'stage_ids' => $stageIds,
            ]
        );

        $response
            ->assertOk()
            ->assertJson([
                'message' => 'Production stages reordered successfully.',
            ]);

        foreach ($stageIds as $index => $stageId) {
            $this->assertDatabaseHas('order_production_stages', [
                'id' => $stageId,
                'sort_order' => $index + 1,
            ]);
        }
    }

    public function test_reorder_rejects_duplicate_stage_ids(): void
    {
        Sanctum::actingAs(
            AdminUser::factory()->create(),
            ['*'],
            'admin'
        );

        $stages = collect([
            ['name' => 'تجهيز', 'sort_order' => 1],
            ['name' => 'حياكة', 'sort_order' => 2],
        ])->map(
            fn (array $data) => OrderProductionStage::create($data)
        );

        $response = $this->postJson(
            '/api/admin/production-stages/reorder',
            [
                'stage_ids' => [
                    $stages[0]->id,
                    $stages[0]->id,
                ],
            ]
        );

        $response->assertUnprocessable();
    }

    public function test_reorder_rejects_incomplete_stage_list(): void
    {
        Sanctum::actingAs(
            AdminUser::factory()->create(),
            ['*'],
            'admin'
        );

        $stages = collect([
            ['name' => 'تجهيز', 'sort_order' => 1],
            ['name' => 'حياكة', 'sort_order' => 2],
        ])->map(
            fn (array $data) => OrderProductionStage::create($data)
        );

        $response = $this->postJson(
            '/api/admin/production-stages/reorder',
            [
                'stage_ids' => [
                    $stages[0]->id,
                ],
            ]
        );

        $response->assertUnprocessable();
    }
}
