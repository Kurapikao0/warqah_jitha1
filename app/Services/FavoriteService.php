<?php

namespace App\Services;

use App\Models\Favorite;

class FavoriteService
{
    public function toggle(
        $customerId,
        $productId
    ) {

        $fav =
        Favorite::where([
            'customer_id' => $customerId,
            'product_id' => $productId,
        ])
            ->first();

        if ($fav) {

            $fav->delete();

            return false;

        }

        Favorite::create([

            'customer_id' => $customerId,

            'product_id' => $productId,

        ]);

        return true;

    }

    public function all($customerId)
    {

        return Favorite::with([
            'product.media',
            'product.category',
        ])
            ->where(
                'customer_id',
                $customerId
            )
            ->get();

    }
}
