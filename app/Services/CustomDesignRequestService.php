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
use App\Repositories\CustomerNotificationRepository;

class CustomDesignRequestService
{
    public function __construct(
        protected CustomDesignRequestRepositoryInterface $repository,
        protected CustomDesignRequestImageRepositoryInterface $imageRepository,
        protected CustomerNotificationRepository $customerNotificationRepository,

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
            $oldStatus = $request->status instanceof \BackedEnum
                ? $request->status->value
                : (string) $request->status;

            $oldQuotedPrice = $request->quoted_price;
            if (
                array_key_exists('quoted_price', $data)
                && $data['quoted_price'] !== null
                && (float) $data['quoted_price'] >= 0
            ) {
                $data['status'] = 'quoted';
            }
            $this->repository->update(
                $request,
                $data
            );

            $this->storeImages(
                $request->fresh(),
                $files
            );

            $updatedRequest = $this->repository->findById(
                $request->id
            );

            $newStatus = $updatedRequest->status instanceof \BackedEnum
                ? $updatedRequest->status->value
                : (string) $updatedRequest->status;

            $newQuotedPrice = $updatedRequest->quoted_price;

            $wasQuoted = $oldStatus === 'quoted';
            $isQuoted = $newStatus === 'quoted';
            $priceChanged = (string) $oldQuotedPrice !== (string) $newQuotedPrice;

            if (
                $isQuoted
                && $newQuotedPrice !== null
                && (!$wasQuoted || $priceChanged)
            ) {
                $this->customerNotificationRepository->create([
                    'customer_id' => $updatedRequest->customer_id,
                    'type' => 'order_update',
                    'title' => 'تم تسعير طلب التصميم الحر',
                    'body' => sprintf(
                        'تم تحديد سعر طلب التصميم الحر رقم #%d بمبلغ %s.',
                        $updatedRequest->id,
                        number_format(
                            (float) $newQuotedPrice,
                            2,
                            '.',
                            ','
                        )
                    ),
                    'is_read' => false,
                ]);
            }

            return $updatedRequest;
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
