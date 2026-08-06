<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface
{

    /**
     * Get categories with filters
     */
    public function getAll(
        array $filters = []
    ): LengthAwarePaginator;



    /**
     * Find category by ID
     */
    public function findById(
        int $id
    ): ?Category;



    /**
     * Create category
     */
    public function create(
        array $data
    ): Category;



    /**
     * Update category
     */
    public function update(
        Category $category,
        array $data
    ): Category;



    /**
     * Delete category
     */
    public function delete(
        Category $category
    ): bool;



    /**
     * Restore category
     */
    public function restore(
        Category $category
    ): bool;



    /**
     * Change category status
     */
    public function changeStatus(
        Category $category,
        string $status
    ): Category;



    /**
     * Load relations
     */
    public function loadRelations(
        Category $category
    ): Category;

}