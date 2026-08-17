<?php

declare(strict_types=1);

namespace App\Http\Controllers\API\Customer;

use App\Events\PasswordResetRequested;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ForgotPasswordRequest;
use App\Http\Requests\Customer\ResetPasswordRequest;
use App\Models\Customer;
use App\Services\PasswordResetService;
use Illuminate\Http\JsonResponse;

final class PasswordResetController extends Controller
{
    public function __construct(
        private readonly PasswordResetService $passwordResetService,
    ) {}

    public function forgotPassword(
        ForgotPasswordRequest $request
    ): JsonResponse {
        $customer = Customer::query()
            ->where('email', $request->validated('email'))
            ->first();

        if ($customer === null) {
            return response()->json([
                'message' => 'Invalid email address.',
            ], 422);
        }

        if ($customer->email_verified_at === null) {
            return response()->json([
                'message' => 'Please verify your email first.',
            ], 403);
        }

        event(
            new PasswordResetRequested($customer)
        );

        return response()->json([
            'message' => 'Password reset link sent successfully.',
        ]);
    }

    public function resetPassword(
        ResetPasswordRequest $request
    ): JsonResponse {
        $customer = Customer::query()
            ->where('email', $request->validated('email'))
            ->first();

        if ($customer === null) {
            return response()->json([
                'message' => 'Invalid reset request.',
            ], 422);
        }

        $this->passwordResetService->reset(
            customer: $customer,
            codeOrToken: $request->validated('code_or_token'),
            password: $request->validated('password'),
        );

        return response()->json([
            'message' => 'Password reset successfully.',
        ]);
    }
}
