<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;


class ProductRepository implements ProductRepositoryInterface
{


    public function all()
    {
        return Product::query()
            ->latest()
            ->paginate(20);
    }



    public function findById(int $id)
    {
        return Product::with([
            'category',
            'media',
            'colors',
            'attributes'
        ])
        ->findOrFail($id);
    }



    public function create(array $data): Product
    {
        return Product::create($data);
    }



    public function update(Product $product,array $data): bool
    {
        return $product->update($data);
    }



    public function delete(Product $product): bool
    {
        return $product->delete();
    }



    public function withRelations()
    {
        return Product::with([
            'category',
            'media',
            'colors'
        ]);
    }


}