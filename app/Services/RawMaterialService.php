<?php

namespace App\Services;

use App\Models\RawMaterial;
use App\Repositories\RawMaterialRepository;
use Illuminate\Database\Eloquent\Collection;

class RawMaterialService
{
    public function __construct(
        protected RawMaterialRepository $repository
    ) {
    }


    public function getAll(): Collection
    {
        return $this->repository->all();
    }


    public function getById(int $id): RawMaterial
    {
        return $this->repository->find($id);
    }


    public function create(array $data): RawMaterial
    {
        return $this->repository->create($data);
    }


    public function update(
        RawMaterial $rawMaterial,
        array $data
    ): RawMaterial {

        return $this->repository->update(
            $rawMaterial,
            $data
        );
    }


    public function delete(RawMaterial $rawMaterial): bool
    {
        return $this->repository->delete($rawMaterial);
    }
}