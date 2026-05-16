<?php

return [
    /*
    |--------------------------------------------------------------------------
    | External APIs Configuration
    |--------------------------------------------------------------------------
    */

    'external_apis' => [
        // Quran API
        'quran' => [
            'base_url' => 'https://equran.id/api/v2',
            'timeout' => 10,
            'verify_ssl' => env('VERIFY_SSL', false), // Set to true di production
        ],

        // Prayer Times API
        'prayer' => [
            'base_url' => 'https://api.aladhan.com/v1',
            'timeout' => 10,
            'verify_ssl' => env('VERIFY_SSL', false),
        ],
    ],
];
