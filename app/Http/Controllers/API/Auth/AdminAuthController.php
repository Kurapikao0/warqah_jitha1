<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Http\Resources\AdminUserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

final class AdminAuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {
    }

    /**
     * Admin Login.
     */
public function login(AdminLoginRequest $request): JsonResponse
{
    $admin = $this->authService->adminLogin(
        $request->validated()
    );

    $token = $admin
        ->createToken('admin-access-token')
        ->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Admin logged in successfully.',
        'data' => [
            'admin' => new AdminUserResource($admin),
            'token' => $token,
        ],
    ]);
}

    /**
     * Admin Logout.
     */
public function logout(Request $request): JsonResponse
{
    $request->user()->currentAccessToken()->delete();

    return response()->json([
        'success' => true,
        'message' => 'Logged out successfully.',
    ]);
}
}
