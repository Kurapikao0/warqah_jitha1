<?php

namespace App\Repositories\Contracts;

use App\Models\Color;

interface ColorRepositoryInterface
{
    public function all();

    public function find(int $id): Color;

    public function create(array $data): Color;

    public function update(
        Color $color,
        array $data
    ): bool;

    public function delete(
        Color $color
    ): bool;
}