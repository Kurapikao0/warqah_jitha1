<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\CustomDesignRequest;
use App\Models\CustomDesignRequestImage;
use App\Repositories\Contracts\CustomDesignRequestRepositoryInterface;

class CustomDesignRequestRepository implements CustomDesignRequestRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        $query = CustomDesignRequest::query()
            ->with([
                'customer:id,full_name',
                'images',
            ]);

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);

            $query->where(function ($query) use ($search): void {
                $query
                    ->where('description', 'like', "%{$search}%")
                    ->orWhereHas(
                        'customer',
                        fn ($customerQuery) => $customerQuery
                            ->where(
                                'full_name',
                                'like',
                                "%{$search}%"
                            )
                    );
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query
            ->latest()
            ->paginate((int) ($filters['per_page'] ?? 15));
    }

    public function findById(int $id): CustomDesignRequest
    {
        return CustomDesignRequest::query()
            ->with([
                'customer:id,full_name',
                'images',
            ])
            ->findOrFail($id);
    }

    public function create(array $data): CustomDesignRequest
    {
        return CustomDesignRequest::create($data);
    }

    public function update(
        CustomDesignRequest $request,
        array $data
    ): bool {
        return $request->update($data);
    }

    public function delete(
        CustomDesignRequest $request
    ): bool {
        return $request->delete();
    }

    public function createImage(
        array $data
    ): CustomDesignRequestImage {
        return CustomDesignRequestImage::create($data);
    }

    public function deleteImage(
        CustomDesignRequestImage $image
    ): bool {
        return $image->delete();
    }
}
