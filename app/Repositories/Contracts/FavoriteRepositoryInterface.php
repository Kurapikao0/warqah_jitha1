<?php

namespace App\Repositories\Contracts;


interface FavoriteRepositoryInterface
{

    /**
     * Add or remove product from favorites
     *
     * @param int $customerId
     * @param int $productId
     * @return bool
     */
    public function toggle(
        int $customerId,
        int $productId
    ): bool;



    /**
     * Get all customer favorite products
     *
     * @param int $customerId
     * @return mixed
     */
    public function getFavorites(
        int $customerId
    );



    /**
     * Remove product from favorites
     *
     * @param int $customerId
     * @param int $productId
     * @return bool
     */
    public function remove(
        int $customerId,
        int $productId
    ): bool;


}