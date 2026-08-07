<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\Customer;

class AddressPolicy
{


    /**
     * View address
     */
    public function view(
        Customer $customer,
        Address $address
    ): bool
    {

        return $address->customer_id === $customer->id;

    }





    /**
     * Update address
     */
    public function update(
        Customer $customer,
        Address $address
    ): bool
    {

        return $address->customer_id === $customer->id;

    }





    /**
     * Delete address
     */
    public function delete(
        Customer $customer,
        Address $address
    ): bool
    {

        return $address->customer_id === $customer->id;

    }





    /**
     * Set default address
     */
    public function setDefault(
        Customer $customer,
        Address $address
    ): bool
    {

        return $address->customer_id === $customer->id;

    }


}