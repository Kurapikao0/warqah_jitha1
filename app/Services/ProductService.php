<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $repository
    ) {}

    public function getAll(?string $search = null)
    {
        return $this->repository->all($search);
    }

    public function getById($id)
    {
        return $this->repository->findById($id);
    }

    public function create(array $data)
    {

        return DB::transaction(function () use ($data) {

            return $this->repository
                ->create($data);

        });

    }

    public function update(
        Product $product,
        array $data
    ) {

        return DB::transaction(function () use ($product, $data) {

            return $this->repository
                ->update(
                    $product,
                    $data
                );

        });

    }

    public function delete(Product $product)
    {

        return DB::transaction(function () use ($product) {

            return $this->repository
                ->delete($product);

        });

    }
}
