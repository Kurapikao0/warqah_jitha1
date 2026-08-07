<?php

namespace App\Services;

use App\Enums\VerificationPurpose;
use App\Models\Customer;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Exceptions\VerificationCodeException;

class VerificationCodeService
{

    public function generate(
        Customer $customer,
        VerificationPurpose $purpose,
        string $contactValue
    ): VerificationCode {

        return DB::transaction(function () use (
            $customer,
            $purpose,
            $contactValue
        ) {

            $recentCode = VerificationCode::query()
                ->where('customer_id', $customer->id)
                ->where('purpose', $purpose->value)
                ->where('created_at', '>', now()->subMinute())
                ->exists();


            if ($recentCode) {

                throw new VerificationCodeException(
                    'Please wait before requesting another code.'
                );
            }


            $this->invalidatePreviousCodes(
                $customer,
                $purpose
            );


            return VerificationCode::create([

                'customer_id' => $customer->id,

                'purpose' => $purpose->value,

                'code_or_token' => $this->generateValue($purpose),

                'contact_value' => $contactValue,

                'expires_at' => now()->addMinutes(
                    $this->expirationMinutes($purpose)
                ),

            ]);
        });
    }



    public function verify(
        Customer $customer,
        VerificationPurpose $purpose,
        string $contactValue,
        string $code
    ): bool {


        return DB::transaction(function () use (
            $customer,
            $purpose,
            $contactValue,
            $code
        ) {


            $verification = VerificationCode::query()

                ->where('customer_id',$customer->id)

                ->where('purpose',$purpose->value)

                ->where('contact_value',$contactValue)

                ->whereNull('consumed_at')

                ->where('expires_at','>',now())

                ->latest('id')

                ->first();


            if(! $verification){
                return false;
            }


            if(! hash_equals(
                $verification->code_or_token,
                $code
            )){
                return false;
            }


            $verification->update([

                'consumed_at'=>now()

            ]);



            match($purpose){

                VerificationPurpose::SignupEmailVerification =>

                $customer->update([
                    'email_verified_at'=>now()
                ]),


                VerificationPurpose::SignupPhoneVerification =>

                $customer->update([
                    'phone_verified_at'=>now()
                ]),


                default=>null
            };


            return true;

        });

    }




    protected function invalidatePreviousCodes(
        Customer $customer,
        VerificationPurpose $purpose
    ):void{


        VerificationCode::query()

            ->where('customer_id',$customer->id)

            ->where('purpose',$purpose->value)

            ->whereNull('consumed_at')

            ->update([

                'consumed_at'=>now()

            ]);

    }




    protected function generateValue(
        VerificationPurpose $purpose
    ):string{


        return match($purpose){


            VerificationPurpose::SignupEmailVerification,
            VerificationPurpose::SignupPhoneVerification,
            VerificationPurpose::PasswordResetPhoneOtp,
            VerificationPurpose::ChangeEmailVerification

            => (string) random_int(100000,999999),


            VerificationPurpose::PasswordResetEmailLink

            => Str::random(64)

        };


    }




    protected function expirationMinutes(
        VerificationPurpose $purpose
    ):int{


        return match($purpose){

            VerificationPurpose::SignupEmailVerification,
            VerificationPurpose::SignupPhoneVerification,
            VerificationPurpose::PasswordResetPhoneOtp,
            VerificationPurpose::ChangeEmailVerification

            =>10,


            VerificationPurpose::PasswordResetEmailLink

            =>30

        };

    }

}