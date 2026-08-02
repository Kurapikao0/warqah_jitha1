<?php

namespace App\Repositories\Contracts;

use App\Models\Order;

interface OrderProductionRepositoryInterface
{
    public function history(Order $order);

    public function updateOrderStage(
    Order $order,
    int $stageId
    );

    public function createHistory(
        Order $order,
        int $stageId,
        ?int $changedBy = null
    );
}