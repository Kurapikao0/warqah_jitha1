<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository implements ProductRepositoryInterface
{
    public function all(?string $search = null, int $perPage = 20): LengthAwarePaginator
    {
        return Product::query()
            ->with([
                'category',
                'media',
                'attributes',
            ])
            ->when(
                filled($search),
                function ($query) use ($search): void {
                    $query->where(function ($query) use ($search): void {
                        $query
                            ->where('name', 'ilike', '%'.$search.'%')
                            ->orWhere('sku', 'ilike', '%'.$search.'%');
                    });
                }
            )
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): Product
    {
        return Product::with([
            'category',
            'media',
            'colors',
            'attributes',
        ])->findOrFail($id);
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function update(Product $product, array $data): bool
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
            'colors',
        ]);
    }
}
