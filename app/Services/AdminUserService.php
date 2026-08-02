<?php

namespace App\Services;

use App\Models\AdminUser;
use App\Repositories\AdminUserRepository;
use Illuminate\Support\Facades\Hash;

class AdminUserService
{

    public function __construct(
        protected AdminUserRepository $repository
    ) {
    }


    public function paginate()
    {
        return $this->repository->paginate();
    }


    public function create(array $data)
    {

        $data['password_hash'] =
            Hash::make(
                $data['password']
            );


        unset($data['password']);


        return $this->repository
            ->create($data);
    }


    public function update(
        AdminUser $adminUser,
        array $data
    ) {

        if(isset($data['password'])){

            $data['password_hash'] =
                Hash::make(
                    $data['password']
                );


            unset($data['password']);
        }


        return $this->repository
            ->update(
                $adminUser,
                $data
            );
    }


    public function delete(
        AdminUser $adminUser
    ){

        return $this->repository
            ->delete(
                $adminUser
            );
    }
}