<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Profile\ChangePasswordRequest;
use App\Http\Requests\User\Profile\UpdateProfileRequest;
use App\Http\Requests\User\Profile\UpdateAvatarRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProfileController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {
    }

    /**
     * Get authenticated customer profile
     */
    public function show(Request $request)
    {
        $customer = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Profile fetched successfully',
            'data'    => new CustomerResource($customer),
            'errors'  => null,
        ], Response::HTTP_OK);
    }

    /**
     * Update customer profile
     */
    public function update(UpdateProfileRequest $request)
    {
        $customer = $request->user();

            $customer = $this->customerService->updateProfile(
                $customer,
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data'    => new CustomerResource($customer),
                'errors'  => null,
            ], Response::HTTP_OK);
    }

    /**
     * Change password
     */
    public function updatePassword(ChangePasswordRequest $request)
    {
        $customer = $request->user();

            $this->customerService->changePassword(
                $customer,
                $request->password
            );

            return response()->json([
                'success' => true,
                'message' => 'Password changed successfully',
                'data'    => null,
                'errors'  => null,
            ], Response::HTTP_OK);

    }

    /**
     * Logout customer
     */
    public function logout(Request $request)
    {
        $customer = $request->user();

        if ($customer) {
            $customer->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
            'data'    => null,
            'errors'  => null,
        ], Response::HTTP_OK);
    }

    /**
     * Update customer avatar
     */
    public function updateAvatar(UpdateAvatarRequest $request)
    {
            $customer = $request->user();

            // تم التغيير من authService إلى customerService
            $updatedCustomer = $this->customerService->updateAvatar(
                $customer,
                $request->file('avatar')
            );

            return response()->json([
                'success' => true,
                'message' => 'Avatar updated successfully',
                'user'    => new CustomerResource($updatedCustomer),
                'data'    => new CustomerResource($updatedCustomer),
                'errors'  => null,
            ], Response::HTTP_OK);
    }
}
