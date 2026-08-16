<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\AdminCreated;
use App\Models\AdminUser;
use App\Repositories\AdminUserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserService
{
    public function __construct(
        protected AdminUserRepository $repository
    ) {}

    public function paginate()
    {
        return $this->repository->paginate();
    }

    public function create(array $data): AdminUser
    {
        return DB::transaction(function () use ($data): AdminUser {
            $data['password_hash'] = Hash::make(
                $data['password']
            );

            unset($data['password']);

            $adminUser = $this->repository->create($data);

            AdminCreated::dispatch($adminUser);

            return $adminUser;
        });
    }

    public function update(
        AdminUser $adminUser,
        array $data
    ): AdminUser {
        if (isset($data['password'])) {
            $data['password_hash'] = Hash::make(
                $data['password']
            );

            unset($data['password']);
        }

        return $this->repository->update(
            $adminUser,
            $data
        );
    }

    public function delete(
        AdminUser $adminUser
    ): bool {
        return $this->repository->delete(
            $adminUser
        );
    }
}
