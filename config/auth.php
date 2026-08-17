<?php

use App\Models\AdminUser;
use App\Models\Customer;

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'customer'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'customers'),
    ],

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

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
