<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function login(Request $request)
    {

        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);


        $admin = AdminUser::where(
            'email',
            $request->email
        )->first();


        if(
            !$admin ||
            !Hash::check(
                $request->password,
                $admin->password_hash
            )
        ){

            return response()->json([
                'message'=>'Invalid credentials'
            ],401);

        }


        $token = $admin->createToken(
            'admin-token'
        )->plainTextToken;


        return response()->json([

            'admin'=>$admin,

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
            'message'=>'Logged out'
        ]);

    }

}