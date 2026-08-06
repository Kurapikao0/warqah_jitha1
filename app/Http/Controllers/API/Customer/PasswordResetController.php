<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ForgotPasswordRequest;
use App\Events\PasswordResetRequested;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Customer\ResetPasswordRequest;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Hash;
use App\Enums\VerificationPurpose; 
use App\Notifications\PasswordChangedNotification;
use App\Services\NotificationService;
use App\Enums\EmailNotificationType;

class PasswordResetController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {
    }
    public function forgotPassword(
        ForgotPasswordRequest $request
    ): JsonResponse {

        $customer = Customer::where(
            'email',
            $request->validated('email')
        )->first();

        if (! $customer->email_verified_at) {
            return response()->json([
                'message' => 'Please verify your email first.'
            ],403);
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

    //dd('PasswordResetController reached');


    $customer = Customer::where(
    'email',
    $request->validated('email')
)->firstOrFail();

$verification = VerificationCode::query()
    ->where('customer_id', $customer->id)
    ->where('purpose', VerificationPurpose::PasswordResetEmailLink->value)
    ->where('code_or_token', $request->validated('code_or_token'))
    ->whereNull('consumed_at')
    ->where('expires_at', '>', now())
    ->first();



    if (! $verification) {

        return response()->json([
            'message'=>'Invalid or expired reset token.'
        ],422);

    }


    $customer = $verification->customer;


    $customer->update([
        'password_hash' => $request->validated('password')
    ]);
    $this->notificationService->send(
        $customer,
        new PasswordChangedNotification(),
    );

    VerificationCode::where('customer_id',$customer->id)
    ->where('purpose','password_reset_email_link')
    ->whereNull('consumed_at')
    ->update([
        'consumed_at'=>now()
    ]);


    return response()->json([
        'message'=>'Password reset successfully.'
    ]);
}
}