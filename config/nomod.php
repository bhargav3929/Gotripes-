<?php

return [
    'api_key' => env('NOMOD_API_KEY'),
    'base_url' => env('NOMOD_BASE_URL', 'https://api.nomod.com'),
    'success_url' => env('NOMOD_SUCCESS_URL'),
    'failure_url' => env('NOMOD_FAILURE_URL'),
    'cancelled_url' => env('NOMOD_CANCELLED_URL'),
];
