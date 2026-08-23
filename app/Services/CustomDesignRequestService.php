<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CustomDesignRequest;
use App\Models\CustomDesignRequestImage;
use App\Repositories\Contracts\CustomDesignRequestImageRepositoryInterface;
use App\Repositories\Contracts\CustomDesignRequestRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CustomDesignRequestService
{
    public function __construct(
        protected CustomDesignRequestRepositoryInterface $repository,
        protected CustomDesignRequestImageRepositoryInterface $imageRepository,
    ) {}

    public function getAll(array $filters = [])
    {
        return $this->repository->getAll($filters);
    }

    public function find(int $id): CustomDesignRequest
    {
        return $this->repository->findById($id);
    }

    public function create(
        array $data,
        array $files = []
    ): CustomDesignRequest {
        return DB::transaction(function () use ($data, $files): CustomDesignRequest {
            $data['status'] ??= 'new';

            $request = $this->repository->create($data);

            $this->storeImages(
                $request,
                $files
            );

            return $this->repository->findById($request->id);
        });
    }

    public function update(
        CustomDesignRequest $request,
        array $data,
        array $files = []
    ): CustomDesignRequest {
        return DB::transaction(function () use (
            $request,
            $data,
            $files
        ): CustomDesignRequest {
            $this->repository->update(
                $request,
                $data
            );

            $this->storeImages(
                $request->fresh(),
                $files
            );

            return $this->repository->findById($request->id);
        });
    }

    public function delete(CustomDesignRequest $request): bool
    {
        return DB::transaction(function () use ($request): bool {
            $images = $request->load('images')->images;

            foreach ($images as $image) {
                $this->deleteImageFile($image);

                $this->imageRepository->delete($image);
            }

            return $this->repository->delete($request);
        });
    }

    public function deleteImage(
        CustomDesignRequestImage $image
    ): bool {
        return DB::transaction(function () use ($image): bool {
            $this->deleteImageFile($image);

            return $this->imageRepository->delete($image);
        });
    }

    private function storeImages(
        CustomDesignRequest $request,
        array $files
    ): void {
        $currentMaxSortOrder = (int) (
            $request->images()->max('sort_order') ?? 0
        );

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store(
                'custom-design-requests/' . $request->id,
                'public'
            );

            $currentMaxSortOrder++;

            $this->imageRepository->create([
                'custom_design_request_id' => $request->id,
                'url' => asset(
                    Storage::disk('public')->url($path)
                ),
                'sort_order' => $currentMaxSortOrder,
            ]);
        }
    }

    private function deleteImageFile(
        CustomDesignRequestImage $image
    ): void {
        if (! $image->url) {
            return;
        }

        $urlPath = parse_url(
            $image->url,
            PHP_URL_PATH
        ) ?? '';

        $storagePath = ltrim(
            str_replace('/storage/', '', $urlPath),
            '/'
        );

        if (
            $storagePath !== ''
            && Storage::disk('public')->exists($storagePath)
        ) {
            Storage::disk('public')->delete($storagePath);
        }
    }
}
