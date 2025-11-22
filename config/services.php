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

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'google_maps' => [
        'key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    'commercegate' => [
        'merchant_id' => env('COMMERCEGATE_MERCHANT_ID', '104675-TEST'),
        'website_id' => env('COMMERCEGATE_WEBSITE_ID', '31052-TEST'),
        'auth_login' => env('COMMERCEGATE_AUTH_LOGIN', '104675-TEST'),
        'auth_password' => env('COMMERCEGATE_AUTH_PASSWORD', 'XX0e909e2119c04428fxx940'),
        'test_mode' => env('COMMERCEGATE_TEST_MODE', true),
        // URL base da API conforme documentação Swagger do CommerceGate
        // Teste e produção usam a mesma URL, diferença está nas credenciais
        'api_url_test' => env('COMMERCEGATE_API_URL_TEST', 'https://gw.cgpaytech.com'),
        'api_url_production' => env('COMMERCEGATE_API_URL_PRODUCTION', 'https://gw.cgpaytech.com'),
        // NOTA: Não é necessário configurar hosted_payment_url pois o endpoint
        // /v1/api/payment_form/configure retorna forwardUrl automaticamente
    ],

    // Subscription system mode: 'commercegate', 'stripe', or 'mock'
    'subscriptions' => [
        'mode' => env('SUBSCRIPTIONS_MODE', 'commercegate'),
    ],

    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'version' => env('RECAPTCHA_VERSION', 'v2'), // v2 ou v3
        'min_score' => env('RECAPTCHA_MIN_SCORE', 0.5), // Apenas para v3
    ],

];
