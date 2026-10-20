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

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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
    | Google Gemini (Smart AI Matching)
    |--------------------------------------------------------------------------
    |
    | Used by App\Services\GeminiEmbeddingService to turn event descriptions
    | and volunteer profiles into vectors stored in PostgreSQL (pgvector).
    | The dimension MUST match the vector(768) column in the events table.
    |
    */

    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        'embed_model' => env('GEMINI_EMBED_MODEL', 'gemini-embedding-001'),
        'embed_dim' => (int) env('GEMINI_EMBED_DIM', 768),
        'base_url' => 'https://generativelanguage.googleapis.com/v1beta',
        'timeout' => 20,
    ],

];