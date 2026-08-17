<?php

namespace App\Repositories\Contracts;

use App\Models\ProductMedia;

interface ProductMediaRepositoryInterface
{
    public function all();

    public function find(int $id): ProductMedia;

    public function create(array $data): ProductMedia;

    public function update(
        ProductMedia $media,
        array $data
    ): bool;

    public function delete(
        ProductMedia $media
    ): bool;

    public function reorder(
        int $productId,
        array $orderedIds
    ): void;

    public function setPrimary(
        int $productId,
        int $mediaId
    ): void;
}
