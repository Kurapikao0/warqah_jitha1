<?php

use App\Models\User;

return [

    'defaults' => [
        'guard' => 'sanctum',
        'passwords' => 'users',
    ],


    'guards' => [

        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],


        'sanctum' => [
            'driver' => 'sanctum',
            'provider' => 'actors',
        ],

    ],


    'providers' => [


        /*
        |--------------------------------------------------------------------------
        | Default Laravel User
        |--------------------------------------------------------------------------
        */
        'users' => [
            'driver' => 'eloquent',
            'model' => User::class,
        ],


        /*
        |--------------------------------------------------------------------------
        | Real application actors
        |--------------------------------------------------------------------------
        */
        'actors' => [
            'driver' => 'eloquent',
            'model' => App\Models\AdminUser::class,
        ],

    ],


    'passwords' => [

        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

    ],


    'password_timeout' => 10800,

];