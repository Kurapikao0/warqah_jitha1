<?php

namespace App\Repositories\Contracts;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;

interface AddressRepositoryInterface
{
    public function getCustomerAddresses(
        Customer $customer
    ): Collection;

    public function findById(
        int $id
    ): ?Address;

    public function create(
        Customer $customer,
        array $data
    ): Address;

    public function update(
        Address $address,
        array $data
    ): Address;

    public function delete(
        Address $address
    ): bool;

    public function setDefault(
        Address $address
    ): Address;

    public function clearDefaultAddresses(
        Customer $customer
    ): void;
}
