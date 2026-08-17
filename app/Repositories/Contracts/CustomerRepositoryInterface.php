<?php

namespace App\Repositories\Contracts;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CustomerRepositoryInterface
{
    public function getAll(array $filters = []): LengthAwarePaginator;

    public function findById(int $id): ?Customer;

    public function create(array $data): Customer;

    public function update(Customer $customer, array $data): Customer;

    public function delete(Customer $customer): bool;

    public function restore(Customer $customer): bool;

    public function changeStatus(Customer $customer, string $status): Customer;

    public function verify(Customer $customer): Customer;

    public function loadRelations(Customer $customer): Customer;
}
