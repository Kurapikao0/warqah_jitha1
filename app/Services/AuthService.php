<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\CustomerRegistered;
use App\Models\AdminUser;
use App\Models\Customer;
use App\Repositories\Contracts\AuthRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

final class AuthService
{
    public function __construct(
        protected AuthRepositoryInterface $authRepository,
    ) {}

    /**
     * Register a new customer.
     *
     * @throws Throwable
     */
    public function register(array $data): Customer
    {
        return DB::transaction(function () use ($data): Customer {
            /*
             * Convert the plain password received from the request
             * into the database password_hash field.
             *
             * Customer model also has the "hashed" cast for password_hash,
             * therefore we intentionally pass the plain value here and
             * let Eloquent perform the hashing.
             */
            if (isset($data['password'])) {
                $data['password_hash'] = $data['password'];

                unset(
                    $data['password'],
                    $data['password_confirmation']
                );
            }

            /*
             * Store customer avatar when provided.
             */
            if (
                isset($data['avatar']) &&
                $data['avatar'] instanceof UploadedFile
            ) {
                $path = $data['avatar']->store(
                    'avatars',
                    'public'
                );

                $data['avatar_url'] = Storage::url($path);

                unset($data['avatar']);
            }

            /*
             * Persist customer through the repository.
             */
            $customer = $this->authRepository->register($data);

            /*
             * Dispatch customer registration event.
             */
            event(new CustomerRegistered($customer));

            return $customer;
        });
    }

    /**
     * Authenticate customer using phone and password.
     *
     * @throws ValidationException
     */
    public function login(array $credentials): Customer
    {
        $customer = $this->authRepository->findByPhone(
            $credentials['phone']
        );

        /*
         * Verify phone country code when supplied.
         */
        if (
            $customer &&
            ! empty($credentials['phone_country_code']) &&
            $customer->phone_country_code !==
            $credentials['phone_country_code']
        ) {
            $customer = null;
        }

        /*
         * Verify customer credentials.
         */
        if (
            ! $customer ||
            ! Hash::check(
                $credentials['password'],
                $customer->password_hash
            )
        ) {
            throw ValidationException::withMessages([
                'phone' => [
                    'The provided credentials are incorrect.',
                ],
            ]);
        }

        return $customer;
    }

    /**
     * Determine whether a customer email already exists.
     */
    public function emailExists(string $email): bool
    {
        return $this->authRepository->findByEmail($email) !== null;
    }

    /**
     * Determine whether a customer phone already exists.
     */
    public function phoneExists(string $phone): bool
    {
        return $this->authRepository->findByPhone($phone) !== null;
    }

    /**
     * Authenticate an admin using email and password.
     *
     * @throws ValidationException
     */
    public function adminLogin(array $credentials): AdminUser
    {
        $admin = $this->authRepository->findAdminByEmail(
            $credentials['email']
        );

        if (
            ! $admin ||
            ! Hash::check(
                $credentials['password'],
                $admin->password_hash
            )
        ) {
            throw ValidationException::withMessages([
                'email' => [
                    'The provided credentials are incorrect.',
                ],
            ]);
        }

        $admin->update([
            'last_login_at' => now(),
        ]);

        return $admin->fresh();
    }
}
