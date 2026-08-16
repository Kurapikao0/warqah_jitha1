<?php

namespace App\Repositories;

use App\Models\ProductAttributeValue;
use App\Repositories\Contracts\ProductAttributeValueRepositoryInterface;

class ProductAttributeValueRepository implements ProductAttributeValueRepositoryInterface
{
    public function all()
    {
        return ProductAttributeValue::with([
            'product',
            'attribute',
        ])->latest()->get();
    }

    public function find(int $id): ProductAttributeValue
    {
        return ProductAttributeValue::with([
            'product',
            'attribute',
        ])->findOrFail($id);
    }

    public function create(array $data): ProductAttributeValue
    {
        return ProductAttributeValue::create($data);
    }

    public function update(
        ProductAttributeValue $attributeValue,
        array $data
    ): bool {
        return $attributeValue->update($data);
    }

    public function delete(
        ProductAttributeValue $attributeValue
    ): bool {
        return $attributeValue->delete();
    }
}
