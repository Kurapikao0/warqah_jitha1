<?php

namespace App\Repositories\Contracts;

use App\Models\DesignPattern;

interface DesignPatternRepositoryInterface
{
    public function all();

    public function find(int $id): DesignPattern;

    public function create(array $data): DesignPattern;

    public function update(
        DesignPattern $designPattern,
        array $data
    ): bool;

    public function delete(
        DesignPattern $designPattern
    ): bool;
}
