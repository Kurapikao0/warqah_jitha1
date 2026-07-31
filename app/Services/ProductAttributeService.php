<?php

namespace App\Services;

use App\Models\ProductAttribute;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\ProductAttributeRepositoryInterface;

class ProductAttributeService
{
    public function __construct(
        protected ProductAttributeRepositoryInterface $repository
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
        ProductAttribute $attribute,
        array $data
    ) {
        return DB::transaction(function () use ($attribute, $data) {

            return $this->repository->update(
                $attribute,
                $data
            );

        });
    }

    public function delete(ProductAttribute $attribute)
    {
        return DB::transaction(function () use ($attribute) {

            return $this->repository->delete($attribute);

        });
    }
}