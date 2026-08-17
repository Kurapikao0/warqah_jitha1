<?php

namespace App\Repositories\Contracts;

use App\Models\ProductAttribute;

interface ProductAttributeRepositoryInterface
{
    public function all();

    public function find(int $id): ProductAttribute;

    public function create(array $data): ProductAttribute;

    public function update(
        ProductAttribute $attribute,
        array $data
    ): bool;

    public function delete(
        ProductAttribute $attribute
    ): bool;
}
