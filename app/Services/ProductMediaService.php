<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Models\ProductMedia;
use App\Enums\ProductMediaType;
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

    public function upload(int $productId, array $files): array
    {
        return DB::transaction(function () use ($productId, $files) {
            $createdMedia = [];
            $maxSortOrder = ProductMedia::where('product_id', $productId)->max('sort_order') ?? 0;
            $hasPrimary = ProductMedia::where('product_id', $productId)->where('is_primary', true)->exists();

            foreach ($files as $file) {
                if (!$file instanceof UploadedFile) {
                    continue;
                }

                $mime = $file->getMimeType() ?? '';
                $mediaType = str_starts_with($mime, 'video/')
                    ? ProductMediaType::Video
                    : ProductMediaType::Image;

                $path = $file->store('product-media', 'public');
                $url = asset(Storage::url($path));

                $maxSortOrder++;
                $isPrimary = false;

                if (!$hasPrimary && count($createdMedia) === 0) {
                    $isPrimary = true;
                    $hasPrimary = true;
                }

                $media = $this->repository->create([
                    'product_id' => $productId,
                    'media_type' => $mediaType,
                    'url' => $url,
                    'sort_order' => $maxSortOrder,
                    'is_primary' => $isPrimary,
                ]);

                $createdMedia[] = $media;
            }

            return $createdMedia;
        });
    }

    public function reorder(int $productId, array $orderedIds): void
    {
        DB::transaction(function () use ($productId, $orderedIds) {
            $this->repository->reorder($productId, $orderedIds);
        });
    }

    public function setPrimary(int $productId, int $mediaId): void
    {
        DB::transaction(function () use ($productId, $mediaId) {
            $this->repository->setPrimary($productId, $mediaId);
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
            if ($media->url) {
                // url looks like: /storage/product-media/filename.jpg
                // Storage::disk('public') root = storage/app/public
                // So relative path for the disk is: product-media/filename.jpg
                $urlPath      = parse_url($media->url, PHP_URL_PATH) ?? '';
                $storagePath  = ltrim(str_replace('/storage/', '', $urlPath), '/');

                if ($storagePath && Storage::disk('public')->exists($storagePath)) {
                    Storage::disk('public')->delete($storagePath);
                }
            }

            return $this->repository->delete($media);
        });
    }
}