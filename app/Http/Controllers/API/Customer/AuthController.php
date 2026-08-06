<?php

namespace App\Http\Controllers\API\Customer;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Events\CustomerRegistered;
use App\Enums\VerificationPurpose;
use App\Services\VerificationCodeService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Customer\VerifyEmailRequest;
use App\Http\Requests\Customer\ResendVerificationRequest;
use App\Notifications\VerifyEmailOtpNotification;
use App\Http\Requests\Customer\ChangeEmailRequest;
use App\Http\Requests\Customer\RequestEmailChangeRequest;
use App\Models\EmailChangeRequest;
use App\Notifications\ChangeEmailOtpNotification;
use App\Exceptions\VerificationCodeException;
use App\Services\NotificationService;

class AuthController extends Controller
{
    public function __construct(
        protected VerificationCodeService $verificationCodeService,
            protected NotificationService $notificationService

    ) {}

    public function register(Request $request)
    {

        $data = $request->validate([

            'full_name'=>'required|string|max:255',

            'email'=>'required|email|unique:customers,email',

            'password'=>'required|string|min:8|confirmed',

        ]);



        $customer = Customer::create([

            'full_name'=>$data['full_name'],

            'email'=>$data['email'],

            'password_hash'=>$data['password'],

        ]);
        event(new CustomerRegistered($customer));

        \Log::info('Event dispatched', [
            'customer_id' => $customer->id,
        ]);

        $token = $customer
            ->createToken('customer-token')
            ->plainTextToken;



        return response()->json([

            'message'=>'Customer registered successfully',

            'customer'=>$customer,

            'token'=>$token

        ],201);


    }





    public function login(Request $request): JsonResponse
{
    $data = $request->validate([

        'email' => 'required|email',

        'password' => 'required|string',

    ]);


    $customer = Customer::where(
        'email',
        $data['email']
    )->first();


    if (
        ! $customer ||
        ! Hash::check(
            $data['password'],
            $customer->password_hash
        )
    ) {

        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);

    }


    if (! $customer->email_verified_at) {

        return response()->json([
            'message' => 'Please verify your email first.'
        ], 403);

    }


    $customer->tokens()->delete();


    $token = $customer
        ->createToken('customer-token')
        ->plainTextToken;


    return response()->json([

        'message' => 'Login successful',

        'customer' => $customer,

        'token' => $token

    ]);
}





    public function logout(Request $request)
    {


        $request
        ->user()
        ->currentAccessToken()
        ->delete();



        return response()->json([

            'message'=>'Logged out successfully'

        ]);


    }

    public function verifyEmail(
    VerifyEmailRequest $request
): JsonResponse {

    $customer = Customer::where(
        'email',
        $request->contact_value
    )->firstOrFail();


    $verified = $this->verificationCodeService->verify(
        $customer,
        VerificationPurpose::SignupEmailVerification,
        $request->contact_value,
        $request->code_or_token
    );


    if (! $verified) {

        return response()->json([
            'message' => 'Invalid or expired verification code.'
        ], 422);

    }


    return response()->json([
        'message' => 'Email verified successfully.'
    ]);
}
public function resendVerification(
    ResendVerificationRequest $request
): JsonResponse {

    $customer = Customer::where(
        'email',
        $request->email
    )->first();


    if (! $customer) {

        return response()->json([
            'message' => 'Customer not found.'
        ], 404);

    }


    if ($customer->email_verified_at) {

        return response()->json([
            'message' => 'Email already verified.'
        ], 422);

    }


        try {

            $verification = $this->verificationCodeService->generate(
                $customer,
                VerificationPurpose::SignupEmailVerification,
                $customer->email
            );
        } catch (VerificationCodeException $e) {
            return response()->json([
                'message'=>$e->getMessage()
            ],429);
        }


    $this->notificationService->send(

        $customer,

        new VerifyEmailOtpNotification(
            $verification->code_or_token
        ),

    );


    return response()->json([

        'message' => 'Verification code sent successfully.'

    ]);

}

/*public function forgotPassword(
    ForgotPasswordRequest $request
): JsonResponse {

    $customer = Customer::where(
        'email',
        $request->email
    )->firstOrFail();


    $verification = $this->verificationCodeService->generate(
        $customer,
        VerificationPurpose::PasswordResetEmailLink,
        $customer->email
    );


    $this->notificationService->send(

        $customer,

        new ResetPasswordNotification(
            $verification->code_or_token
        ),

        'password_reset',

        'إعادة تعيين كلمة المرور - ورقة وجذع'

    );


    return response()->json([
        'message' => 'Password reset instructions sent successfully.'
    ]);
}
public function resetPassword(
    ResetPasswordRequest $request
): JsonResponse {


    $verification = \App\Models\VerificationCode::query()
        ->where(
            'code_or_token',
            $request->code_or_token
        )
        ->where(
            'purpose',
            VerificationPurpose::PasswordResetEmailLink
        )
        ->whereNull('consumed_at')
        ->where(
            'expires_at',
            '>',
            now()
        )
        ->first();


    if (! $verification) {

        return response()->json([
            'message' => 'Invalid or expired reset token.'
        ],422);

    }


    $customer = Customer::find(
        $verification->customer_id
    );


    $customer->update([

        'password_hash' => $request->password,

    ]);


    $verification->update([
        'consumed_at'=>now(),
    ]);


    return response()->json([

        'message'=>'Password reset successfully.'

    ]);
}*/
public function changeEmail(
    ChangeEmailRequest $request
): JsonResponse {

    /** @var Customer $customer */
    $customer = auth('customer')->user();


    $customer->update([

        'email'=>$request->email,

        'email_verified_at'=>null,

    ]);


    return response()->json([

        'message'=>'Email changed successfully.'

    ]);

}
public function requestEmailChange(
    RequestEmailChangeRequest $request
): JsonResponse {

    /** @var Customer $customer */
    $customer = auth('customer')->user();


    $code = (string) random_int(
        100000,
        999999
    );



    EmailChangeRequest::create([

        'customer_id'=>$customer->id,

        'old_email'=>$customer->email,

        'new_email'=>$request->email,

        'verification_code'=>$code,

        'expires_at'=>now()->addMinutes(10),

    ]);



    $this->notificationService->send(

    $customer,

    new ChangeEmailOtpNotification($code),

    $request->email


);



    return response()->json([

        'message'=>'Verification code sent to new email.'

    ]);

}
}