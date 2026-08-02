<?php

namespace App\Repositories;

use App\Models\DesignPattern;
use App\Repositories\Contracts\DesignPatternRepositoryInterface;

class DesignPatternRepository implements DesignPatternRepositoryInterface
{
    public function all()
    {
        return DesignPattern::latest()->get();
    }

    public function find(int $id): DesignPattern
    {
        return DesignPattern::findOrFail($id);
    }

    public function create(array $data): DesignPattern
    {
        return DesignPattern::create($data);
    }

    public function update(
        DesignPattern $designPattern,
        array $data
    ): bool {
        return $designPattern->update($data);
    }

    public function delete(
        DesignPattern $designPattern
    ): bool {
        return $designPattern->delete();
    }
}