<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\OrderProductionStage;

class OrderProductionStagePolicy
{
    /**
     * Display all stages.
     */
    public function viewAny(
        AdminUser $admin
    ): bool {
        return true;
    }

    /**
     * Display stage.
     */
    public function view(
        AdminUser $admin,
        OrderProductionStage $stage
    ): bool {
        return true;
    }

    /**
     * Create stage.
     */
    public function create(
        AdminUser $admin
    ): bool {
        return true;
    }

    /**
     * Update stage.
     */
    public function update(
        AdminUser $admin,
        OrderProductionStage $stage
    ): bool {
        return true;
    }

    /**
     * Delete stage.
     */
    public function delete(
        AdminUser $admin,
        OrderProductionStage $stage
    ): bool {
        return true;
    }
}
