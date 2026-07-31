<?php

namespace App\Repositories\Contracts;

use App\Models\ProductAttributeValue;

interface ProductAttributeValueRepositoryInterface
{
    public function all();

    public function find(int $id): ProductAttributeValue;

    public function create(array $data): ProductAttributeValue;

    public function update(
        ProductAttributeValue $attributeValue,
        array $data
    ): bool;

    public function delete(
        ProductAttributeValue $attributeValue
    ): bool;
}