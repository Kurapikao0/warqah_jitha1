<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Throwable;

class AuthService
{
    public function __construct(
        protected AuthRepositoryInterface $authRepository
    ) {
    }

    /**
     * Register a new customer with avatar upload support
     * 
     * @param array $data
     * @return Customer
     * @throws Throwable
     */
    public function register(array $data): Customer
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['avatar']) && $data['avatar'] instanceof UploadedFile) {
                $path = $data['avatar']->store('avatars', 'public');
                $data['avatar_url'] = Storage::url($path);
                unset($data['avatar']);
            }

            return $this->authRepository->register($data);
        });
    }



    /**
     * Authenticate customer by phone & password
     * 
     * @param array $credentials
     * @return Customer
     * @throws ValidationException
     */
    public function login(array $credentials): Customer
    {
        $customer = $this->authRepository->findByPhone($credentials['phone']);

        if ($customer && !empty($credentials['phone_country_code'])) {
            if ($customer->phone_country_code !== $credentials['phone_country_code']) {
                $customer = null;
            }
        }

        if (!$customer || !Hash::check($credentials['password'], $customer->password_hash)) {
            throw ValidationException::withMessages([
                'phone' => ['بيانات الدخول غير صحيحة، يرجى التأكد من رقم الهاتف وكلمة المرور.'],
            ]);
        }

        return $customer;
    }

    /**
     * Check if email exists
     */
    public function emailExists(string $email): bool
    {
        return $this->authRepository->findByEmail($email) !== null;
    }

    /**
     * Check if phone exists
     */
    public function phoneExists(string $phone): bool
    {
        return $this->authRepository->findByPhone($phone) !== null;
    }
}