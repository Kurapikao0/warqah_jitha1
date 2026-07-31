<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\Favorite;

class FavoritePolicy
{
    /**
     * View favorites.
     */
    public function viewAny(
        Customer $customer
    ): bool {
        return true;
    }

    /**
     * View favorite.
     */
    public function view(
        Customer $customer,
        Favorite $favorite
    ): bool {
        return $favorite->customer_id === $customer->id;
    }

    /**
     * Add favorite.
     */
    public function create(
        Customer $customer
    ): bool {
        return true;
    }

    /**
     * Remove favorite.
     */
    public function delete(
        Customer $customer,
        Favorite $favorite
    ): bool {
        return $favorite->customer_id === $customer->id;
    }
}