<?php

namespace App\Services;

use App\Events\EmailChanged;
use App\Events\PasswordChanged;
use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CustomerService
{
    public function __construct(
        protected CustomerRepositoryInterface $customerRepository
    ) {}

    /**
     * Create Customer
     */
    public function createCustomer(array $data): Customer
    {
        return DB::transaction(function () use ($data) {

            // ✅ تحويل password إلى password_hash
            if (isset($data['password'])) {
                $data['password_hash'] = Hash::make($data['password']);
                unset($data['password']);
            }

            return $this->customerRepository->create($data);
        });
    }

    /**
     * Update Customer
     */
    public function updateCustomer(
        Customer $customer,
        array $data
    ): Customer {
        return DB::transaction(function () use ($customer, $data) {

            // ✅ شفر password إلى password_hash هنا في Service
            if (isset($data['password'])) {
                $data['password_hash'] = Hash::make($data['password']);
                unset($data['password']);
            }

            return $this->customerRepository->update($customer, $data);
        });
    }

    /**
     * Delete Customer
     */
    public function deleteCustomer(Customer $customer): bool
    {
        return DB::transaction(function () use ($customer) {
            return $this->customerRepository->delete($customer);
        });
    }

    /**
     * Restore Customer
     */
    public function restoreCustomer(Customer $customer): bool
    {
        return DB::transaction(function () use ($customer) {
            return $this->customerRepository->restore($customer);
        });
    }

    /**
     * Change Customer Status
     */
    public function changeStatus(
        Customer $customer,
        string $status
    ): Customer {
        return DB::transaction(function () use ($customer, $status) {
            return $this->customerRepository->changeStatus($customer, $status);
        });
    }

    /**
     * Verify Customer
     */
    public function verifyCustomer(Customer $customer): Customer
    {
        return DB::transaction(function () use ($customer) {
            return $this->customerRepository->verify($customer);
        });
    }

    /**
     * Update Customer Profile
     */
    public function updateProfile(
        Customer $customer,
        array $data
    ): Customer {
        return DB::transaction(function () use ($customer, $data) {
            $oldEmail = $customer->email;

            if (isset($data['password'])) {
                $data['password_hash'] = Hash::make($data['password']);
                unset($data['password']);
            }

            $emailChanged = isset($data['email'])
                && $data['email'] !== $oldEmail;

            if ($emailChanged) {
                $data['email_verified_at'] = null;
            }

            $updatedCustomer = $this->customerRepository->update(
                $customer,
                $data
            );

            if ($emailChanged) {
                EmailChanged::dispatch(
                    customer: $updatedCustomer,
                    oldEmail: $oldEmail,
                    newEmail: $updatedCustomer->email,
                );
            }

            return $updatedCustomer;
        });
    }

    /**
     * Update Avatar (Deletes old image & stores new one)
     *
     * @throws Throwable
     */
    public function updateAvatar(
        Customer $customer,
        UploadedFile $file
    ): Customer {
        return DB::transaction(function () use ($customer, $file) {

            // 1. حذف الصورة القديمة من الـ storage إذا كانت موجودة
            if ($customer->avatar_url) {
                $oldPath = str_replace('/storage/', '', parse_url($customer->avatar_url, PHP_URL_PATH));

                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            // 2. رفع الصورة الجديدة إلى مجلد public/avatars
            $newPath = $file->store('avatars', 'public');
            $newAvatarUrl = Storage::url($newPath);

            // 3. تحديث بيانات العميل عبر الـ Repository
            return $this->customerRepository->update($customer, [
                'avatar_url' => $newAvatarUrl,
            ]);
        });
    }

    /**
     * Change Password
     */
    public function changePassword(
        Customer $customer,
        string $password
    ): Customer {
        return DB::transaction(function () use ($customer, $password) {
            $customer->update([
                'password_hash' => Hash::make($password),
            ]);

            $customer = $customer->fresh();

            PasswordChanged::dispatch($customer);

            return $customer;
        });
    }

    /**
     * Get Customer Details
     */
    public function getCustomer(int $id): ?Customer
    {
        return $this->customerRepository->findById($id);
    }

    /**
     * Get Customers
     */
    public function getCustomers(array $filters = [])
    {
        return $this->customerRepository->getAll($filters);
    }
}
