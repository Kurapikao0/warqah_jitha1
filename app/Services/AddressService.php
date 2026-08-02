<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AddressService
{
    public function getCustomerAddresses(Customer $customer): Collection
    {
        return $customer->addresses()
            ->latest()
            ->get();
    }


    public function create(Customer $customer, array $data): Address
    {
        return DB::transaction(function () use ($customer, $data) {

            if (($data['is_default'] ?? false) === true) {
                $customer->addresses()
                    ->where('is_default', true)
                    ->update([
                        'is_default' => false
                    ]);           
                }

            return $customer->addresses()->create($data);
        });
    }


    public function update(Address $address, array $data): Address
    {
        return DB::transaction(function () use ($address, $data) {

            if (
                isset($data['is_default']) &&
                $data['is_default'] === true
            ) {
                $this->clearDefault($address->customer);
            }

            $address->update($data);

            return $address->refresh();
        });
    }


    public function delete(Address $address): bool
    {
        return $address->delete();
    }


    protected function clearDefault(Customer $customer): void
    {
        $customer->addresses()
            ->where('is_default', true)
            ->update([
                'is_default' => false
            ]);
    }

    public function setDefault(Address $address): Address
{
    return DB::transaction(function () use ($address) {

        $this->clearDefault($address->customer);

        $address->refresh();

        $address->update([
            'is_default' => true
        ]);

        return $address->refresh();
            });
        }
}