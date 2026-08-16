<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Customer;
use App\Repositories\Contracts\AddressRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AddressService
{
    public function __construct(
        protected AddressRepositoryInterface $addressRepository
    ) {}

    /**
     * Get customer addresses
     */
    public function getCustomerAddresses(
        Customer $customer
    ): Collection {

        return $this->addressRepository
            ->getCustomerAddresses($customer);

    }

    /**
     * Create address
     */
    public function create(
        Customer $customer,
        array $data
    ): Address {

        return DB::transaction(function () use (
            $customer,
            $data
        ) {

            if (
                ($data['is_default'] ?? false) === true
            ) {

                $this->addressRepository
                    ->clearDefaultAddresses(
                        $customer
                    );

            }

            return $this->addressRepository
                ->create(
                    $customer,
                    $data
                );

        });

    }

    /**
     * Update address
     */
    public function update(
        Address $address,
        array $data
    ): Address {

        return DB::transaction(function () use (
            $address,
            $data
        ) {

            if (
                isset($data['is_default']) &&
                $data['is_default'] === true
            ) {
                $customer = $address->customer;

                if ($customer instanceof Customer) {
                    $this->addressRepository
                        ->clearDefaultAddresses($customer);
                }
            }

            return $this->addressRepository
                ->update(
                    $address,
                    $data
                );

        });

    }

    /**
     * Set default address
     */
    public function setDefault(
        Address $address
    ): Address {

        return DB::transaction(function () use ($address) {

            $customer = $address->customer;

            if ($customer instanceof Customer) {
                $this->addressRepository
                    ->clearDefaultAddresses($customer);
            }

            return $this->addressRepository
                ->update(
                    $address,
                    [
                        'is_default' => true,
                    ]
                );

        });

    }

    /**
     * Delete address
     */
    public function delete(
        Address $address
    ): bool {

        return $this->addressRepository
            ->delete($address);

    }
}
