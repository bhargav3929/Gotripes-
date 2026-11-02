<?php

return [
    'api_key' => env('NOMOD_API_KEY'),
    'base_url' => env('NOMOD_BASE_URL', 'https://api.nomod.com'),
    'success_url' => env('NOMOD_SUCCESS_URL'),
    'failure_url' => env('NOMOD_FAILURE_URL'),
    'cancelled_url' => env('NOMOD_CANCELLED_URL'),

    // The business's own email domains and phone numbers. Nomod keeps one customer per email and
    // reuses that customer's saved phone, and Tabby / Tamara check the client against their account,
    // so a payment taken for a client must never carry the agency's own email or phone.
    'own_email_domains' => array_values(array_filter(array_map('trim', explode(',', strtolower((string) env('NOMOD_OWN_EMAIL_DOMAINS', 'aynalamirtourism.com,gotrips.ai')))))),
    'own_phones' => array_values(array_filter(array_map(fn ($p) => preg_replace('/\D/', '', $p), explode(',', (string) env('NOMOD_OWN_PHONES', '+971543651065'))))),
];
