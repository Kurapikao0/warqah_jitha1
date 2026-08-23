<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\CustomDesignRequest;
use App\Models\CustomDesignRequestImage;

interface CustomDesignRequestRepositoryInterface
{
    public function getAll(array $filters = []);

    public function findById(int $id): CustomDesignRequest;

    public function create(array $data): CustomDesignRequest;

    public function update(
        CustomDesignRequest $request,
        array $data
    ): bool;

    public function delete(
        CustomDesignRequest $request
    ): bool;

    public function createImage(
        array $data
    ): CustomDesignRequestImage;

    public function deleteImage(
        CustomDesignRequestImage $image
    ): bool;
}
