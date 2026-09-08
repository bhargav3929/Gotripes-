<?php

// The manager application queue renders and applies this list generically.
// Adding another ordinary filter is one data entry rather than a controller +
// Blade rewrite, leaving the requested headroom for registration changes.
return [
    'service' => [
        'label' => 'Service requested',
        'operator' => 'json_contains',
        'column' => 'services',
        'options_source' => 'agent_services',
    ],
    'country' => [
        'label' => 'Nationality / country',
        'operator' => 'equals',
        'column' => 'country',
        'options_source' => 'countries',
    ],
    'location' => [
        'label' => 'Location',
        'operator' => 'search_columns',
        'columns' => ['country', 'emirate', 'address'],
        'input' => 'text',
        'placeholder' => 'Country, Emirate or address',
    ],
    'license_status' => [
        'label' => 'Trade license',
        'operator' => 'license_status',
        'options' => [
            'valid' => 'Valid',
            'expiring' => 'Expires in 30 days',
            'expired' => 'Expired',
            'missing' => 'Not supplied',
        ],
    ],
    'account_status' => [
        'label' => 'Service access',
        'operator' => 'user_active',
        'options' => [
            'active' => 'Activated',
            'inactive' => 'Not activated / disabled',
        ],
    ],
];
