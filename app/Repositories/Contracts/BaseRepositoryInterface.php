<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function all(array $relations = []): Collection;

    public function paginate(
        int $perPage = 15,
        array $relations = []
    );

    public function find(
        int $id,
        array $relations = []
    ): Model;

    public function create(array $data): Model;

    public function update(
        Model $model,
        array $data
    ): bool;

    public function delete(Model $model): bool;
}
