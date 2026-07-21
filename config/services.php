<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    /*
    | One-off token protecting the /internal/seed-tuzlanska-liga route (see
    | routes/web.php + Api/V1/... no - SeedController). Never hardcode a
    | fallback here - the repo is public, so the only place this secret can
    | live is the VPS .env, set via the SEED_TOKEN GitHub Actions secret.
    */
    'seed_token' => env('SEED_TOKEN'),

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase (Google sign-in)
    |--------------------------------------------------------------------------
    |
    | These are all public, client-side config values used by the Firebase
    | JS SDK for "Sign in with Google" - there's no service account/private
    | key involved. The backend only needs project_id, to check the `aud`/
    | `iss` claims when verifying a Firebase ID token against Google's
    | public certs (see App\Services\FirebaseAuthService).
    |
    */
    'firebase' => [
        'api_key' => env('FIREBASE_API_KEY'),
        'auth_domain' => env('FIREBASE_AUTH_DOMAIN'),
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'storage_bucket' => env('FIREBASE_STORAGE_BUCKET'),
        'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID'),
        'app_id' => env('FIREBASE_APP_ID'),
        'measurement_id' => env('FIREBASE_MEASUREMENT_ID'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Gemini (AI popuni ligu asistent)
    |--------------------------------------------------------------------------
    |
    | Free-tier Gemini Flash models used by App\Services\GeminiLeagueAssistantService
    | to turn a plain-language description into suggested competition-create
    | form values. `model` must be one of `allowed_models` - the service
    | refuses to call anything outside this free-tier set.
    |
    | Verified against the live API (2026-07-17): gemini-2.5-flash and
    | gemini-2.5-flash-lite are retired for this key ("no longer available
    | to new users") - NOT transient, don't re-add them without re-testing.
    | gemini-3.1-flash-lite and gemini-3.5-flash both work (3.5-flash can
    | occasionally 503 "high demand" - that's transient Google-side load).
    |
    */
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-3.1-flash-lite'),
        'allowed_models' => [
            'gemini-3.1-flash-lite',
            'gemini-3.5-flash',
        ],
    ],

];
