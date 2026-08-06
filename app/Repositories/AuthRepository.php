<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements AuthRepositoryInterface
{
    /**
     * Register a new customer
     * 
     * @param array $data
     * @return Customer
     */
    public function register(array $data): Customer
    {
        // تشفير password إلى password_hash
        if (isset($data['password'])) {
            $data['password_hash'] = Hash::make($data['password']);
            unset($data['password']);
        }

        // إنشاء العميل الجديد
        return Customer::create($data);
    }

    /**
     * Find customer by email
     * 
     * @param string $email
     * @return Customer|null
     */
    public function findByEmail(string $email): ?Customer
    {
        return Customer::where('email', $email)->first();
    }

    /**
     * Find customer by phone
     * 
     * @param string $phone
     * @return Customer|null
     */
    public function findByPhone(string $phone): ?Customer
    {
        return Customer::where('phone', $phone)->first();
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);
        return $customer->fresh();
    }
}