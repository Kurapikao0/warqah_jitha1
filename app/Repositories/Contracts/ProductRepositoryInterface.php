<?php

namespace App\Repositories\Contracts;

use App\Models\Product;

interface ProductRepositoryInterface
{

    public function all();

    public function findById(int $id);

    public function create(array $data): Product;

    public function update(Product $product,array $data): bool;

    public function delete(Product $product): bool;

    public function withRelations();

}