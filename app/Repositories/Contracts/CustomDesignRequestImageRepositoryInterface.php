<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\CustomDesignRequestImage;

interface CustomDesignRequestImageRepositoryInterface
{
    public function create(array $data): CustomDesignRequestImage;

    public function delete(
        CustomDesignRequestImage $image
    ): bool;
}
