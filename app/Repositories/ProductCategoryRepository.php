<?php

namespace App\Repositories;

use App\Models\ProductCategory;

class ProductCategoryRepository
{

    public function paginate()
    {
        return ProductCategory::with('parent')
            ->latest()
            ->paginate();
    }


    public function create(array $data): ProductCategory
    {
        return ProductCategory::create($data);
    }


    public function update(
        ProductCategory $category,
        array $data
    ): ProductCategory {

        $category->update($data);

        return $category;
    }


    public function delete(
        ProductCategory $category
    ): bool {

        return $category->delete();
    }
}