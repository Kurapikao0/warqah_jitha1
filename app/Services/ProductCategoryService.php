<?php

namespace App\Services;

use App\Models\ProductCategory;
use App\Repositories\ProductCategoryRepository;

class ProductCategoryService
{

    public function __construct(
        protected ProductCategoryRepository $repository
    ) {
    }


    public function paginate()
    {
        return $this->repository->paginate();
    }


    public function store(array $data)
    {
        return $this->repository->create($data);
    }


    public function update(
        ProductCategory $category,
        array $data
    ) {

        return $this->repository->update(
            $category,
            $data
        );
    }


    public function delete(
        ProductCategory $category
    ) {

        return $this->repository->delete(
            $category
        );
    }
}