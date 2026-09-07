<?php

namespace App\Services;

use App\Models\OrderProductionStage;
use App\Repositories\Contracts\OrderProductionStageRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderProductionStageService
{
    public function __construct(
        protected OrderProductionStageRepositoryInterface $repository
    ) {}

    public function all()
    {
        return $this->repository->all();
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            return $this->repository->create($data);

        });
    }

    public function update(
        OrderProductionStage $stage,
        array $data
    ) {
        return DB::transaction(function () use ($stage, $data) {

            return $this->repository->update($stage, $data);

        });
    }

    public function delete(OrderProductionStage $stage)
    {
        return DB::transaction(function () use ($stage) {

            return $this->repository->delete($stage);

        });
    }

    public function reorder(array $stageIds): void
    {
        DB::transaction(function () use ($stageIds) {
            $currentIds = $this->repository
                ->all()
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values()
                ->all();

            $requestedIds = collect($stageIds)
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values()
                ->all();

            if ($currentIds !== $requestedIds) {
                throw ValidationException::withMessages([
                    'stage_ids' => [
                        'يجب أن يحتوي الترتيب على جميع مراحل الإنتاج الموجودة في النظام مرة واحدة فقط.',
                    ],
                ]);
            }

            $this->repository->reorder(
                array_map('intval', $stageIds)
            );
        });
    }
}
