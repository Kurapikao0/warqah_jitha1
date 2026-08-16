<?php

namespace App\Repositories\Contracts;

use App\Models\ProductCustomizationRequest;

interface CustomizationRepositoryInterface
{
    public function getAll();

    public function findById(int $id);

    public function create(array $data);

    public function update(
        ProductCustomizationRequest $request,
        array $data
    );

    public function delete(
        ProductCustomizationRequest $request
    );
}
