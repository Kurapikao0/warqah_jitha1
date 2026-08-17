<?php

namespace App\Repositories;

use App\Models\ProductAttribute;
use App\Repositories\Contracts\ProductAttributeRepositoryInterface;

class ProductAttributeRepository implements ProductAttributeRepositoryInterface
{
    public function all()
    {
        return ProductAttribute::with('values')
            ->latest()
            ->get();
    }

    public function find(int $id): ProductAttribute
    {
        return ProductAttribute::with('values')
            ->findOrFail($id);
    }

    public function create(array $data): ProductAttribute
    {
        return ProductAttribute::create($data);
    }

    public function update(
        ProductAttribute $attribute,
        array $data
    ): bool {
        return $attribute->update($data);
    }

    public function delete(
        ProductAttribute $attribute
    ): bool {
        return $attribute->delete();
    }
}
