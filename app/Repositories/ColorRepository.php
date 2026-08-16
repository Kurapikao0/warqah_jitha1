<?php

namespace App\Repositories;

use App\Models\Color;
use App\Repositories\Contracts\ColorRepositoryInterface;

class ColorRepository implements ColorRepositoryInterface
{
    public function all()
    {
        return Color::latest()->get();
    }

    public function find(int $id): Color
    {
        return Color::findOrFail($id);
    }

    public function create(array $data): Color
    {
        return Color::create($data);
    }

    public function update(
        Color $color,
        array $data
    ): bool {
        return $color->update($data);
    }

    public function delete(
        Color $color
    ): bool {
        return $color->delete();
    }
}
