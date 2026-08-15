<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerRepository implements CustomerRepositoryInterface
{
    /**
     * Get paginated customers with optional filters
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return Customer::query()
            ->with([
                'addresses',
                'orders',
                'reviews',
                'favorites',
                'notifications',
                'cart'
            ])
            ->when(
                isset($filters['search']),
                function ($query) use ($filters) {
                    $search = $filters['search'];
                    $query->where(function ($q) use ($search) {
                        $q->where('full_name', 'LIKE', "%{$search}%")
                          ->orWhere('email', 'LIKE', "%{$search}%")
                          ->orWhere('phone', 'LIKE', "%{$search}%");
                    });
                }
            )
            ->when(
                isset($filters['category']),
                function ($query) use ($filters) {
                    $query->where('category', $filters['category']);
                }
            )
            ->when(
                array_key_exists('verified', $filters) && $filters['verified'] !== '' && $filters['verified'] !== null,
                function ($query) use ($filters) {
                    $verified = filter_var($filters['verified'], FILTER_VALIDATE_BOOLEAN);

                    if ($verified) {
                        $query->whereNotNull('email_verified_at');
                    } else {
                        $query->whereNull('email_verified_at');
                    }
                }
            )
            ->orderBy(
                $filters['sort_by'] ?? 'created_at',
                $filters['sort_direction'] ?? 'desc'
            )
            ->paginate(
                $filters['per_page'] ?? 15
            );
    }

    /**
     * Find customer by ID with relations
     */
    public function findById(int $id): ?Customer
    {
        return Customer::with([
            'addresses',
            'orders',
            'reviews',
            'favorites',
            'notifications',
            'cart'
        ])
        ->find($id);
    }

    /**
     * Create new customer
     */
    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    /**
     * Update customer data (Supports profile, avatar_url, etc.)
     */
    public function update(
        Customer $customer,
        array $data
    ): Customer {
        $customer->update($data);

        return $customer->fresh();
    }

    /**
     * Soft delete customer
     */
    public function delete(
        Customer $customer
    ): bool {
        return $customer->delete();
    }

    /**
     * Restore soft deleted customer
     */
    public function restore(
        Customer $customer
    ): bool {
        return $customer->restore();
    }

    /**
     * Change customer status
     */
    public function changeStatus(
        Customer $customer,
        string $status
    ): Customer {
        $customer->update([
            'status' => $status
        ]);

        return $customer->fresh();
    }

    /**
     * Verify customer email timestamp
     */
    public function verify(
        Customer $customer
    ): Customer {
        $customer->update([
            'email_verified_at' => now()
        ]);

        return $customer->fresh();
    }

    /**
     * Eager load relations for an existing customer model
     */
    public function loadRelations(
        Customer $customer
    ): Customer {
        return $customer->load([
            'addresses',
            'orders',
            'reviews',
            'favorites',
            'notifications',
            'cart'
        ]);
    }
}
