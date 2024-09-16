<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost',
        'https://localhost',
        'http://localhost:3000',
        'https://localhost:3000',
        'https://vbalance.lc',
        'http://vbalance.lc',
        'https://dev-front.vbalance.net',
        'https://preprod.vbalance.net',
        'https://vbalance.net',
        'https://app.vbalance.net',
        'https://admin.vbalance.net',
        'https://dev-admin.vbalance.net',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
