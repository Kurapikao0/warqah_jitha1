<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Contracts\BaseRepositoryInterface;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $relations = []): Collection
    {
        return $this->model
            ->with($relations)
            ->latest()
            ->get();
    }

    public function paginate(
        int $perPage = 15,
        array $relations = []
    ) {
        return $this->model
            ->with($relations)
            ->latest()
            ->paginate($perPage);
    }

    public function find(
        int $id,
        array $relations = []
    ): Model {
        return $this->model
            ->with($relations)
            ->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(
        Model $model,
        array $data
    ): bool {
        return $model->update($data);
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}