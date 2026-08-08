<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Models\AdminUser;
use App\Models\Customer;
use App\Repositories\Contracts\AuthRepositoryInterface;


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


    public function findAdminByEmail(string $email): ?AdminUser
    {
        return AdminUser::query()
            ->where('email', $email)
            ->first();
    }
}
