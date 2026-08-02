<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\ProductMedia;
use App\Repositories\Contracts\ProductMediaRepositoryInterface;

class ProductMediaService
{
    public function __construct(
        protected ProductMediaRepositoryInterface $repository
    ) {
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function find(int $id)
    {
        return $this->repository->find($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {

            if (!empty($data['is_primary'])) {

                ProductMedia::where(
                    'product_id',
                    $data['product_id']
                )->update([
                    'is_primary' => false
                ]);

            }

            return $this->repository->create($data);

        });
    }

    public function update(
        ProductMedia $media,
        array $data
    ) {
        return DB::transaction(function () use ($media, $data) {

            if (
                isset($data['is_primary']) &&
                $data['is_primary']
            ) {

                ProductMedia::where(
                    'product_id',
                    $media->product_id
                )->update([
                    'is_primary' => false
                ]);

            }

            return $this->repository->update(
                $media,
                $data
            );

        });
    }

    public function delete(ProductMedia $media)
    {
        return DB::transaction(function () use ($media) {

            return $this->repository->delete($media);

        });
    }
}