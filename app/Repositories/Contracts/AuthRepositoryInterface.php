<?php

namespace App\Repositories\Contracts;

use App\Models\Customer;

interface AuthRepositoryInterface
{
    /**
     * Register a new customer
     */
    public function register(array $data): Customer;
    
    /**
     * Find customer by email
     */
    public function findByEmail(string $email): ?Customer;
    
    /**
     * Find customer by phone
     */
    public function findByPhone(string $phone): ?Customer;

    public function update(Customer $customer, array $data): Customer;    
    
}