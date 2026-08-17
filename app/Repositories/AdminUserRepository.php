<?php

namespace App\Repositories;

use App\Models\AdminUser;

class AdminUserRepository
{
    public function paginate(int $perPage = 15)
    {
        return AdminUser::with('role')
            ->paginate($perPage);
    }

    public function find(AdminUser $adminUser)
    {
        return $adminUser->load('role');
    }

    public function create(array $data): AdminUser
    {
        return AdminUser::create($data);
    }

    public function update(
        AdminUser $adminUser,
        array $data
    ): AdminUser {

        $adminUser->update($data);

        return $adminUser;
    }

    public function delete(
        AdminUser $adminUser
    ): bool {

        return $adminUser->delete();
    }
}
