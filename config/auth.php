<?php

// use App\Models\User;
use App\Models\AdminUser;
use App\Models\Customer;
return [

    'defaults' => [
        'guard' => 'sanctum',
        'passwords' => 'users',
    ],
    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application. You may change these values
    | as required, but they're a perfect start for most applications.
    |
    */

    // 'defaults' => [
    //     'guard' => env('AUTH_GUARD', 'web'),
    //     'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    // ],
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'customer'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'customers'),
    ],    



    // 'guards' => [

    //     'web' => [
    //         'driver' => 'session',
    //         'provider' => 'users',

        'guards' => [

            'web' => [
                'driver' => 'session',
                'provider' => 'customers',
            ],

            'customer' => [
                'driver' => 'sanctum',
                'provider' => 'customers',
            ],

            'admin' => [
                'driver' => 'sanctum',
                'provider' => 'admins',
            ],

        ],


    //     'sanctum' => [
    //         'driver' => 'sanctum',
    //         'provider' => 'actors',
    //     ],

    // ],


    // 'providers' => [


        /*
        |--------------------------------------------------------------------------
        | Default Laravel User
        |--------------------------------------------------------------------------
        */
        // 'users' => [
        //     'driver' => 'eloquent',
        //     'model' => User::class,
        // ],


        /*
        |--------------------------------------------------------------------------
        | Real application actors
        |--------------------------------------------------------------------------
        */
        // 'actors' => [
        //     'driver' => 'eloquent',
        //     'model' => App\Models\AdminUser::class,
        // ],

    // ],


    'passwords' => [

        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],


        'admins' => [
            'provider' => 'admins',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],

    ],


    'password_timeout' => 10800,

];