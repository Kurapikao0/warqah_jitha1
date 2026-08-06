<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CustomerLoginRequest;
use App\Http\Requests\Auth\CustomerRegisterRequest;
use App\Http\Resources\CustomerResource;
use App\Services\AuthService;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
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

            // 1. تسجيل العميل عبر AuthService
            $customer = $this->authService->register($validated);

            // 2. إنشاء Access Token للعميل
            $token = $customer->createToken('auth_token')->plainTextToken;

            // 3. إرجاع الاستجابة الموحدة
            return response()->json([
                'success' => true,
                'message' => 'Customer registered successfully',
                'user'    => new CustomerResource($customer),
                'token'   => $token,
                'data'    => new CustomerResource($customer),
                'errors'  => null
            ], Response::HTTP_CREATED);

        } catch (Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'data'    => null,
                'errors'  => [$exception->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Login customer via phone and password
     * 
     * @param CustomerLoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(CustomerLoginRequest $request)
    {
        try {
            $validated = $request->validated();

            // 1. التحقق من بيانات الدخول عن طريق AuthService
            $customer = $this->authService->login($validated);

            // 2. إصدار Access Token جديد
            $token = $customer->createToken('auth_token')->plainTextToken;

            // 3. إرجاع الاستجابة بنسق موحد مطابق لاختبارات Postman
            return response()->json([
                'success' => true,
                'message' => 'Customer logged in successfully',
                'user'    => new CustomerResource($customer),
                'token'   => $token,
                'data'    => new CustomerResource($customer),
                'errors'  => null
            ], Response::HTTP_OK);

        } catch (ValidationException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data'    => null,
                'errors'  => $exception->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'data'    => null,
                'errors'  => [$exception->getMessage()]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}