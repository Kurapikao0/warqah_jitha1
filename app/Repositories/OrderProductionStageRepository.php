<?php

namespace App\Repositories;

use App\Models\OrderProductionStage;
use App\Repositories\Contracts\OrderProductionStageRepositoryInterface;

class OrderProductionStageRepository implements OrderProductionStageRepositoryInterface
{
    public function all()
    {
        return OrderProductionStage::orderBy('sort_order')->get();
    }

    public function find(int $id): OrderProductionStage
    {
        return OrderProductionStage::findOrFail($id);
    }

    public function create(array $data): OrderProductionStage
    {
        return OrderProductionStage::create($data);
    }

    public function update(
        OrderProductionStage $stage,
        array $data
    ): bool {
        return $stage->update($data);
    }

    public function delete(OrderProductionStage $stage): bool
    {
        return $stage->delete();
    }
}