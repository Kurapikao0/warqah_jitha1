<?php

namespace App\Services;

use App\Models\ProductAttribute;
use App\Repositories\Contracts\ProductAttributeRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProductAttributeService
{
    public function __construct(
        protected ProductAttributeRepositoryInterface $repository
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

        if (isset($data['type'])) {
            $data['input_type'] = $data['type'];
            unset($data['type']);
        }

        return $this->repository->create($data);

    });
}

    public function update(
    ProductAttribute $attribute,
    array $data
) {
    return DB::transaction(function () use ($attribute, $data) {

        if (isset($data['type'])) {
            $data['input_type'] = $data['type'];
            unset($data['type']);
        }

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
