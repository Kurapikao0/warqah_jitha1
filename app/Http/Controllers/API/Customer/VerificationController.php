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

        /** @var Customer $customer */
        $customer = $request->user();

        $verification = $this->verificationCodeService->generate(
            $customer,
            VerificationPurpose::from(
                $request->validated('purpose')
            ),
            $request->validated('contact_value')
        );

        return response()->json([
            'message' => 'Verification generated successfully.',
            'data' => new VerificationCodeResource($verification),
        ], 201);
    }


    /**
     * Verify code/token.
     */
    public function verify(
        VerifyVerificationCodeRequest $request
    ): JsonResponse {

        /** @var Customer $customer */
        $customer = $request->user();

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