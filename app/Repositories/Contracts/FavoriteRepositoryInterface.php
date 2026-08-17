<?php

namespace App\Repositories\Contracts;

interface FavoriteRepositoryInterface
{
    /**
     * Add or remove product from favorites
     */
    public function toggle(
        int $customerId,
        int $productId
    ): bool;

    /**
     * Get all customer favorite products
     *
     * @return mixed
     */
    public function getFavorites(
        int $customerId
    );

    /**
     * Remove product from favorites
     */
    public function remove(
        int $customerId,
        int $productId
    ): bool;
}
