<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\Product;

class ProductPolicy
{
    /**
     * Display all products.
     */
    public function viewAny(
        AdminUser $admin
    ): bool {
        return true;
    }

    /**
     * Display product.
     */
    public function view(
        AdminUser $admin,
        Product $product
    ): bool {
        return true;
    }

    /**
     * Create product.
     */
    public function create(
        AdminUser $admin
    ): bool {
        return true;
    }

    /**
     * Update product.
     */
    public function update(
        AdminUser $admin,
        Product $product
    ): bool {
        return true;
    }

    /**
     * Delete product.
     */
    public function delete(
        AdminUser $admin,
        Product $product
    ): bool {
        return true;
    }

    /**
     * Restore product.
     */
    public function restore(
        AdminUser $admin,
        Product $product
    ): bool {
        return true;
    }

    /**
     * Force delete product.
     */
    public function forceDelete(
        AdminUser $admin,
        Product $product
    ): bool {
        return true;
    }
}