<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\CustomDesignRequestImage;
use App\Repositories\Contracts\CustomDesignRequestImageRepositoryInterface;

class CustomDesignRequestImageRepository implements CustomDesignRequestImageRepositoryInterface
{
    public function create(array $data): CustomDesignRequestImage
    {
        return CustomDesignRequestImage::create($data);
    }

    public function delete(
        CustomDesignRequestImage $image
    ): bool {
        return $image->delete();
    }
}
