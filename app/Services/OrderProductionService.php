<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderProductionStage;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\OrderProductionRepositoryInterface;

class OrderProductionService
{
    public function __construct(
        protected OrderProductionRepositoryInterface $repository
    ) {
    }


    public function history(Order $order)
    {
        return $this->repository->history($order);
    }


    public function changeStage(Order $order)
    {
        return DB::transaction(function () use ($order) {

            $currentStage = $order->currentProductionStage;

            $nextStage = $currentStage instanceof OrderProductionStage
                ? $currentStage->next()
                : OrderProductionStage::orderBy('sort_order')->first();
                
            if (!$nextStage) {
                return null;
            }


            $this->repository->createHistory(
                $order,
                $nextStage->id,
                auth('admin')->id()
            );


            return $this->repository->updateOrderStage(
                $order,
                $nextStage->id
            );
        });
    }


    public function updateStage(
        Order $order,
        int $stageId
    ) {
        return DB::transaction(function () use ($order, $stageId) {


            $this->repository->createHistory(
                $order,
                $stageId,
                auth('admin')->id()
            );


            return $this->repository->updateOrderStage(
                $order,
                $stageId
            );
        });
    }
}
