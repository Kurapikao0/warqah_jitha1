<?php

namespace App\Http\Controllers\API\Customer;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Events\CustomerRegistered;
use App\Enums\VerificationPurpose;
use App\Services\VerificationCodeService;
class AuthController extends Controller
{
    public function __construct(
        protected VerificationCodeService $verificationCodeService
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





    public function login(Request $request)
    {


        $data=$request->validate([


            'email'=>'required|email',

            'password'=>'required|string'


        ]);




        $customer = Customer::where(
            'email',
            $data['email']
        )->first();




        if(
            !$customer ||
            !Hash::check(
                $data['password'],
                $customer->password_hash
            )
        )
        {


            return response()->json([

                'message'=>'Invalid credentials'

            ],401);


        }





        $token=$customer
        ->createToken('customer-token')
        ->plainTextToken;



        return response()->json([

            'message'=>'Login successful',

            'customer'=>$customer,

            'token'=>$token

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


}