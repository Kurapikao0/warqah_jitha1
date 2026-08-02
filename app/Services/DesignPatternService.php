<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\DesignPattern;
use App\Repositories\Contracts\DesignPatternRepositoryInterface;

class DesignPatternService
{
    public function __construct(
        protected DesignPatternRepositoryInterface $repository
    ) {
    }

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
        DesignPattern $designPattern,
        array $data
    ) {
        return DB::transaction(function () use ($designPattern, $data) {
            return $this->repository->update($designPattern, $data);
        });
    }

    public function delete(
        DesignPattern $designPattern
    ) {
        return DB::transaction(function () use ($designPattern) {
            return $this->repository->delete($designPattern);
        });
    }
}