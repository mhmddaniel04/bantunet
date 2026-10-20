<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | In BantuNet only NPO organisers log in. Volunteers do not need accounts.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'organizations'),
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'organizations',
        ],
    ],

    'providers' => [
        'organizations' => [
            'driver' => 'eloquent',
            'model' => App\Models\Organization::class,
        ],
    ],

    'passwords' => [
        'organizations' => [
            'provider' => 'organizations',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];