<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\DesignPattern;

class DesignPatternPolicy
{
    /**
     * View all design patterns.
     */
    public function viewAny(AdminUser $admin): bool
    {
        return true;
    }

    /**
     * View a design pattern.
     */
    public function view(
        AdminUser $admin,
        DesignPattern $designPattern
    ): bool {
        return true;
    }

    /**
     * Create a design pattern.
     */
    public function create(AdminUser $admin): bool
    {
        return true;
    }

    /**
     * Update a design pattern.
     */
    public function update(
        AdminUser $admin,
        DesignPattern $designPattern
    ): bool {
        return true;
    }

    /**
     * Delete a design pattern.
     */
    public function delete(
        AdminUser $admin,
        DesignPattern $designPattern
    ): bool {
        return true;
    }
}