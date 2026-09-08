<?php

return [
    'default_hours' => env('SUPPORT_HOURS', 'Monday–Saturday, 9:00 AM–6:00 PM UAE time'),
    'default_sla_minutes' => (int) env('SUPPORT_SLA_MINUTES', 240),
    'default_recipient' => env('SUPPORT_EMAIL'),

    // WhatsApp Business Cloud API. The ticket system remains fully functional
    // while these are blank; the manager queue reports "Awaiting API setup".
    'whatsapp' => [
        'enabled' => filter_var(env('SUPPORT_WHATSAPP_ENABLED', false), FILTER_VALIDATE_BOOL),
        'endpoint' => env('SUPPORT_WHATSAPP_ENDPOINT'),
        'token' => env('SUPPORT_WHATSAPP_TOKEN'),
        'recipient' => env('SUPPORT_WHATSAPP_RECIPIENT'),
    ],
];
