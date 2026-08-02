<?php

namespace App\Repositories\Contracts;

use App\Models\OrderProductionStage;

interface OrderProductionStageRepositoryInterface
{
    public function all();

    public function find(int $id): OrderProductionStage;

    public function create(array $data): OrderProductionStage;

    public function update(
        OrderProductionStage $stage,
        array $data
    ): OrderProductionStage;

    public function delete(OrderProductionStage $stage): bool;
}