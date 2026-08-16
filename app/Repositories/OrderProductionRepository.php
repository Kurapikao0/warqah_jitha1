<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contracts\OrderProductionRepositoryInterface;

class OrderProductionRepository implements OrderProductionRepositoryInterface
{
    public function history(Order $order)
    {
        return $order
            ->productionStageHistory()
            ->with('stage')
            ->get();
    }

    public function updateOrderStage(
        Order $order,
        int $stageId
    ) {
        $order->update([
            'current_production_stage_id' => $stageId,
        ]);

        return $order->refresh();
    }

    public function createHistory(
        Order $order,
        int $stageId,
        ?int $changedBy = null
    ) {
        return $order
            ->productionStageHistory()
            ->create([
                'stage_id' => $stageId,
                'changed_by' => $changedBy,
            ]);
    }
}
