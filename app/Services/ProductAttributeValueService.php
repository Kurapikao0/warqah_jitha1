<?php

namespace App\Services;

use App\Models\ProductAttributeValue;
use Illuminate\Support\Facades\DB;
use App\Repositories\Contracts\ProductAttributeValueRepositoryInterface;

class ProductAttributeValueService
{
    public function __construct(
        protected ProductAttributeValueRepositoryInterface $repository
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
        ProductAttributeValue $attributeValue,
        array $data
    ) {
        return DB::transaction(function () use ($attributeValue, $data) {

            return $this->repository->update(
                $attributeValue,
                $data
            );

        });
    }

    public function delete(ProductAttributeValue $attributeValue)
    {
        return DB::transaction(function () use ($attributeValue) {

            return $this->repository->delete($attributeValue);

        });
    }
}