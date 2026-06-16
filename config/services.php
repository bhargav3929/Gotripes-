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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    
    // API-Football (api-sports.io) — free tier covers live scores + FIFA World Cup.
    // Get a free key at https://dashboard.api-football.com and set FOOTBALL_API_KEY.
    'football' => [
        'key'       => env('FOOTBALL_API_KEY'),
        'base'      => env('FOOTBALL_API_BASE', 'https://v3.football.api-sports.io'),
        'wc_league' => env('FOOTBALL_WC_LEAGUE_ID', 1),   // World Cup league id
        'season'    => env('FOOTBALL_WC_SEASON', 2026),
    ],

    // CCAvenue (deprecated — replaced by Nomod Hosted Checkout)
    // 'ccavenue' => [
    //     'merchant_id' => env('CCAVENUE_MERCHANT_ID'),
    //     'working_key' => env('CCAVENUE_WORKING_KEY'),
    //     'access_code' => env('CCAVENUE_ACCESS_CODE'),
    //     'url' => env('CCAVENUE_URL'),
    //     'redirect_url' => env('CCAVENUE_REDIRECT'),
    //     'cancel_url' => env('CCAVENUE_CANCEL'),
    // ],










];
