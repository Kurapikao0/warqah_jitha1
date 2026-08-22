<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function __construct(
        protected ProductRepositoryInterface $repository
    ) {}

    public function getAll(?string $search = null, int $perPage = 20)
    {
        return $this->repository->all($search, $perPage);
    }

    public function getById(int $id): Product
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data): Product {
            $attributeValues = $data['attribute_values'] ?? [];

            unset($data['attribute_values']);

            $product = $this->repository->create($data);

            $this->syncAttributes($product, $attributeValues);

            return $product->fresh([
                'category',
                'media',
                'attributes',
            ]);
        });
    }

    public function update(
        Product $product,
        array $data
    ): Product {
        return DB::transaction(function () use ($product, $data): Product {
            $hasAttributeValues = array_key_exists(
                'attribute_values',
                $data
            );

            $attributeValues = $data['attribute_values'] ?? [];

            unset($data['attribute_values']);

            $this->repository->update(
                $product,
                $data
            );

            if ($hasAttributeValues) {
                $this->syncAttributes(
                    $product,
                    $attributeValues
                );
            }

            return $this->repository->findById($product->id);
        });
    }

    public function delete(Product $product): bool
    {
        return DB::transaction(function () use ($product): bool {
            return $this->repository->delete($product);
        });
    }

    /**
     * Synchronize the attributes assigned to a product.
     *
     * The product_attribute_values table has a unique constraint
     * on (product_id, attribute_id), so sync() is the correct
     * operation for replacing the current assignments.
     */
    private function syncAttributes(
        Product $product,
        array $attributeValues
    ): void {
        $mapped = collect($attributeValues)
            ->keyBy('attribute_id')
            ->map(
                fn (array $attribute): array => [
                    'value' => $attribute['value'],
                ]
            )
            ->all();

        $product->attributes()->sync($mapped);
    }
}
