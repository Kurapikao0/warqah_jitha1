<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CustomerRegisterRequest;
use App\Http\Resources\CustomerResource;
use App\Services\AuthService;
use Illuminate\Http\Response;
use Throwable;

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
        try {
            $validated = $request->validated();

            // ✅ AuthService يتعامل مع التشفير والـ Repository
            $customer = $this->authService->register($validated);

            return response()->json([
                'success' => true,
                'message' => 'Customer registered successfully',
                'data' => new CustomerResource($customer),
                'errors' => null
            ], Response::HTTP_CREATED);

        } catch (Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'data' => null,
                'errors' => [$exception->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}