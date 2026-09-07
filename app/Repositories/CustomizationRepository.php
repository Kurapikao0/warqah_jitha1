<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ProductCustomizationRequest;
use App\Repositories\Contracts\CustomizationRepositoryInterface;

class CustomizationRepository implements CustomizationRepositoryInterface
{
    public function getAll()
    {
        return ProductCustomizationRequest::with([
            'customer',
            'baseProduct',
            'color',
            'designPattern',
            'attributeValues.attribute',
        ])
            ->latest()
            ->paginate(20);
    }

    public function findById(int $id)
    {
        return ProductCustomizationRequest::with([
            'customer',
            'baseProduct',
            'color',
            'designPattern',
            'attributeValues.attribute',
        ])
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return ProductCustomizationRequest::create($data);
    }

    public function update(
        ProductCustomizationRequest $request,
        array $data
    ) {
        return $request->update($data);
    }

    public function delete(
        ProductCustomizationRequest $request
    ) {
        return $request->delete();
    }
}
