<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CustomerLoginRequest;
use App\Http\Requests\Auth\CustomerRegisterRequest;
use App\Http\Resources\CustomerResource;
use App\Services\AuthService;
use Symfony\Component\HttpFoundation\Response;


class CustomerAuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    /**
     * Register new customer
     *
     * @param CustomerRegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(CustomerRegisterRequest $request)
    {
        $validated = $request->validated();

        $customer = $this->authService->register($validated);

        $token = $customer->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Customer registered successfully',
            'user' => new CustomerResource($customer),
            'token' => $token,
            'data' => new CustomerResource($customer),
            'errors' => null,
        ], Response::HTTP_CREATED);
    }

    /**
     * Login customer via phone and password
     *
     * @param CustomerLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(CustomerLoginRequest $request)
    {
        $validated = $request->validated();

        $customer = $this->authService->login($validated);

        $token = $customer->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Customer logged in successfully',
            'user' => new CustomerResource($customer),
            'token' => $token,
            'data' => new CustomerResource($customer),
            'errors' => null,
        ], Response::HTTP_OK);
    }

    /**
     * Logout customer.
     */
    public function logout(\Illuminate\Http\Request $request): \Illuminate\Http\JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }
}
