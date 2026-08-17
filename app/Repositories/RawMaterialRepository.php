<?php

namespace App\Repositories;

use App\Models\RawMaterial;
use Illuminate\Database\Eloquent\Collection;

class RawMaterialRepository
{
    public function all(): Collection
    {
        return RawMaterial::latest()->get();
    }

    public function find(int $id): RawMaterial
    {
        return RawMaterial::findOrFail($id);
    }

    public function create(array $data): RawMaterial
    {
        return RawMaterial::create($data);
    }

    public function update(RawMaterial $rawMaterial, array $data): RawMaterial
    {
        $rawMaterial->update($data);

        return $rawMaterial->refresh();
    }

    public function delete(RawMaterial $rawMaterial): bool
    {
        return $rawMaterial->delete();
    }
}
