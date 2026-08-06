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

        'customer' => [
            'driver' => 'sanctum',
            'provider' => 'customers',
        ],

        'admin' => [
            'driver' => 'sanctum',
            'provider' => 'admins',
        ],

        'web' => [
            'driver' => 'session',
            'provider' => 'customers',
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
    | Password Resetting
    |--------------------------------------------------------------------------
    */

    'passwords' => [

        'customers' => [
            'provider' => 'customers',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'admins' => [
            'provider' => 'admins',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];