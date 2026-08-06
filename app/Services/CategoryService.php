<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Throwable;

class CategoryService
{

    public function __construct(
        protected CategoryRepositoryInterface $categoryRepository
    ) {
    }



    /**
     * Get categories
     */
    public function getCategories(
        array $filters = []
    )
    {

        return $this->categoryRepository
            ->getAll($filters);

    }





    /**
     * Create Category
     */
    public function createCategory(
        array $data
    ): Category
    {

        return DB::transaction(function () use ($data) {


            return $this->categoryRepository
                ->create($data);


        });

    }





    /**
     * Update Category
     */
    public function updateCategory(
        Category $category,
        array $data
    ): Category
    {

        return DB::transaction(function () use (
            $category,
            $data
        ) {


            return $this->categoryRepository
                ->update(
                    $category,
                    $data
                );


        });

    }





    /**
     * Delete Category
     */
    public function deleteCategory(
        Category $category
    ): bool
    {

        return DB::transaction(function () use ($category) {


            return $this->categoryRepository
                ->delete($category);


        });

    }





    /**
     * Restore Category
     */
    public function restoreCategory(
        Category $category
    ): bool
    {

        return DB::transaction(function () use ($category) {


            return $this->categoryRepository
                ->restore($category);


        });

    }





    /**
     * Change Category Status
     */
    public function changeStatus(
        Category $category,
        string $status
    ): Category
    {

        return DB::transaction(function () use (
            $category,
            $status
        ) {


            return $this->categoryRepository
                ->changeStatus(
                    $category,
                    $status
                );


        });

    }





    /**
     * Get Category Details
     */
    public function getCategory(
        int $id
    ): ?Category
    {

        return $this->categoryRepository
            ->findById($id);

    }





    /**
     * Load Category Relations
     */
    public function loadRelations(
        Category $category
    ): Category
    {

        return $this->categoryRepository
            ->loadRelations($category);

    }


}