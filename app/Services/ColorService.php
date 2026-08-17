<?php

namespace App\Services;

use App\Models\Color;
use App\Repositories\Contracts\ColorRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ColorService
{
    public function __construct(
        protected ColorRepositoryInterface $repository
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
        Color $color,
        array $data
    ) {
        return DB::transaction(function () use ($color, $data) {

            return $this->repository->update(
                $color,
                $data
            );

        });
    }

    public function delete(Color $color)
    {
        return DB::transaction(function () use ($color) {

            return $this->repository->delete($color);

        });
    }
}
