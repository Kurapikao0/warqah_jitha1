<?php

namespace App\Repositories;

use App\Models\Address;
use App\Models\Customer;
use App\Repositories\Contracts\AddressRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AddressRepository implements AddressRepositoryInterface
{
    public function getCustomerAddresses(
        Customer $customer
    ): Collection {

        return $customer
            ->addresses()
            ->latest()
            ->get();

    }

    public function findById(
        int $id
    ): ?Address {

        return Address::find($id);

    }

    public function create(
        Customer $customer,
        array $data
    ): Address {
        /** @var Address $address */
        $address = $customer
            ->addresses()
            ->create($data);

        return $address;
    }

    public function update(
        Address $address,
        array $data
    ): Address {

        $address->update($data);

        return $address->fresh();

    }

    public function delete(
        Address $address
    ): bool {

        return $address->delete();

    }

    public function setDefault(
        Address $address
    ): Address {

        return DB::transaction(function () use ($address) {

            Address::where(
                'customer_id',
                $address->customer_id
            )
                ->update([

                    'is_default' => false,

                ]);

            $address->update([

                'is_default' => true,

            ]);

            return $address->fresh();

        });

    }

    public function clearDefaultAddresses(
        Customer $customer
    ): void {

        $customer->addresses()
            ->where(
                'is_default',
                true
            )
            ->update([
                'is_default' => false,
            ]);

    }
}
