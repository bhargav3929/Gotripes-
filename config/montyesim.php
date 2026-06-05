<?php

return [
    'base_url' => env('MONTYESIM_BASE_URL', 'https://resellerapi.montyesim.com/api/v0'),
    'username' => env('MONTYESIM_USERNAME'),
    'password' => env('MONTYESIM_PASSWORD'),
    'markup_percent' => env('ESIM_MARKUP_PERCENT', 20),

    // MontyeSIM prices come in USD; customers are charged in AED. Fixed UAE peg.
    'usd_to_aed' => env('ESIM_USD_TO_AED', 3.6725),
];
