<?php

use App\Models\AdminUser;
use App\Models\Customer;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'customer'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'customers'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */
    'guards' => [

        'web' => [
            'driver' => 'session',
            'provider' => 'customers',
        ],

        'customer' => [
            'driver' => 'session', // Sanctum يستعين بـ session driver
            'provider' => 'customers',
        ],

        'admin' => [
            'driver' => 'session', // Sanctum يستعين بـ session driver
            'provider' => 'admins',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */
    'providers' => [

        'customers' => [
            'driver' => 'eloquent',
            'model' => Customer::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => AdminUser::class,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */
    'passwords' => [

        'customers' => [
            'provider' => 'customers',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
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

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */
    'password_timeout' => 10800,

];