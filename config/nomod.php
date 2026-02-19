<?php

return [
    'api_key' => env('NOMOD_API_KEY'),
    'base_url' => env('NOMOD_BASE_URL', 'https://api.nomod.com'),
    'success_url' => env('NOMOD_SUCCESS_URL', 'https://gotrips.ai/payment/nomod/success'),
    'failure_url' => env('NOMOD_FAILURE_URL', 'https://gotrips.ai/payment/nomod/failure'),
    'cancelled_url' => env('NOMOD_CANCELLED_URL', 'https://gotrips.ai/payment/nomod/cancelled'),
];
