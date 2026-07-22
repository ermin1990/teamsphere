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

    // Native mobile clients don't send an Origin header and aren't subject
    // to CORS at all - this only matters for a browser-based (web/PWA)
    // client calling the API from JS, so it's locked to real app origins
    // rather than left wide open.
    'allowed_origins' => array_values(array_filter([
        config('app.url'),
        env('FRONTEND_URL'),
        'https://mojturnir.online',
    ])),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
