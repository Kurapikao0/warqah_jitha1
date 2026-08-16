<?php

namespace App\Repositories;

use App\Models\Favorite;
use App\Repositories\Contracts\FavoriteRepositoryInterface;

class FavoriteRepository implements FavoriteRepositoryInterface
{
    /**
     * Toggle Favorite
     */
    public function toggle(
        int $customerId,
        int $productId
    ): bool {
        $favorite = Favorite::where([
            'customer_id' => $customerId,
            'product_id' => $productId,
        ])->first();

        if ($favorite) {
            $favorite->delete();

            return false;
        }

        Favorite::create([
            'customer_id' => $customerId,
            'product_id' => $productId,
        ]);

        return true;
    }

    /**
     * Get Customer Favorites
     */
    public function getFavorites($customerId)
    {

        return Favorite::with([

            'product',
            'product.media',
            'product.category',

        ])
            ->where(
                'customer_id',
                $customerId
            )
            ->latest()
            ->get();

    }

    /**
     * Remove Favorite
     */
    public function remove(
        int $customerId,
        int $productId
    ): bool {
        return (bool) Favorite::where([
            'customer_id' => $customerId,
            'product_id' => $productId,
        ])->delete();
    }
}
