<?php

namespace App\Repositories;

use App\Models\ProductMedia;
use App\Repositories\Contracts\ProductMediaRepositoryInterface;

class ProductMediaRepository implements ProductMediaRepositoryInterface
{
    public function all()
    {
        return ProductMedia::with('product')
            ->orderBy('sort_order')
            ->get();
    }

    public function find(int $id): ProductMedia
    {
        return ProductMedia::with('product')
            ->findOrFail($id);
    }

    public function create(array $data): ProductMedia
    {
        return ProductMedia::create($data);
    }

    public function update(
        ProductMedia $media,
        array $data
    ): bool {
        return $media->update($data);
    }

    public function delete(
        ProductMedia $media
    ): bool {
        return $media->delete();
    }
}