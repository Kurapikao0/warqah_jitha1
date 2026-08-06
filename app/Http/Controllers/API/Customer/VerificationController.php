<?php

namespace App\Http\Controllers\API\Customer;

use App\Enums\VerificationPurpose;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\GenerateVerificationCodeRequest;
use App\Http\Requests\Customer\VerifyVerificationCodeRequest;
use App\Http\Resources\VerificationCodeResource;
use App\Models\Customer;
use App\Services\VerificationCodeService;
use Illuminate\Http\JsonResponse;

class VerificationController extends Controller
{
    public function __construct(
        protected VerificationCodeService $verificationCodeService
    ) {
    }

    /**
     * Generate verification code/token.
     */
    public function generate(
        GenerateVerificationCodeRequest $request
    ): JsonResponse {
        $user = $request->user();

        // 1. استخدام العميل المصدق (مسجل الدخول) إن وجد
        $customer = ($user instanceof Customer) ? $user : null;

        // 2. إذا لم يكن مسجلاً للدخول، يتم البحث عنه عبر contact_value الممررة في الطلب
        if (! $customer && $request->has('contact_value')) {
            $customer = Customer::where('email', $request->contact_value)
                ->orWhere('phone', $request->contact_value)
                ->first();
        }

        if (! $customer) {
            return response()->json([
                'message' => 'Customer record not found.',
            ], 404);
        }

        $verification = $this->verificationCodeService->generateCode($customer);

        return response()->json([
            'message' => 'Verification generated successfully.',
            'data'    => new VerificationCodeResource($verification),
        ], 201);
    }

    /**
     * Verify code/token.
     */
    public function verify(
        VerifyVerificationCodeRequest $request
    ): JsonResponse {
        $user = $request->user();

        // 1. استخدام العميل المصدق (مسجل الدخول) إن وجد
        $customer = ($user instanceof Customer) ? $user : null;

        // 2. البحث عن العميل ببيانات التواصل بدلاً من Customer::first() لضمان الأمان
        if (! $customer) {
            $customer = Customer::where('email', $request->validated('contact_value'))
                ->orWhere('phone', $request->validated('contact_value'))
                ->first();
        }

        if (! $customer) {
            return response()->json([
                'message' => 'Customer record not found.',
            ], 404);
        }

        $verified = $this->verificationCodeService->verify(
            $customer,
            VerificationPurpose::from(
                $request->validated('purpose')
            ),
            $request->validated('contact_value'),
            $request->validated('code_or_token')
        );

        if (! $verified) {
            return response()->json([
                'message' => 'Invalid or expired verification code.',
            ], 422);
        }

        return response()->json([
            'message' => 'Verification completed successfully.',
        ]);
    }
}