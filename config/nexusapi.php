<?php

/*
|--------------------------------------------------------------------------
| Farenexus nexusAPI (Flight GDS + NDC) configuration
|--------------------------------------------------------------------------
|
| nexusAPI is Farenexus's unified travel API. It aggregates GDS content
| (Travelport/Galileo "1G", Amadeus, Sabre) plus direct NDC airline content
| and exposes search -> price -> book (PNR) -> ticket -> post-booking
| (cancel / refund / void) operations over a single REST interface.
|
| GoTrips connects as a Travelport/Galileo (1G) GoLite IATA reseller. The
| GDS-side identifiers below (PCC, branch, target branch, GPM client id,
| device codes) were issued for "Ayn Al Amir Tourism" and are passed by
| nexusAPI to the underlying GDS session.
|
| IMPORTANT: every value is env-driven. Never commit real credentials.
| The endpoint *paths* under 'endpoints' are placeholders following standard
| GDS/NDC lifecycle naming and MUST be confirmed against the official
| Farenexus nexusAPI documentation before go-live (the partner docs were not
| included in the onboarding email — only credentials were shared).
|
*/

return [

    // Base URL of the nexusAPI gateway. Use the sandbox/UAT host for testing.
    'base_url' => env('NEXUSAPI_BASE_URL', 'https://api.farenexus.com'),

    // Environment toggle: 'test' (UAT/sandbox) or 'production'.
    'environment' => env('NEXUSAPI_ENV', 'test'),

    /*
    |--------------------------------------------------------------------------
    | nexusAPI authentication
    |--------------------------------------------------------------------------
    | nexusAPI typically issues a bearer token from an API key / secret pair
    | (or username/password). Confirm the exact auth scheme with Farenexus.
    */
    'auth' => [
        'api_key'    => env('NEXUSAPI_KEY'),
        'api_secret' => env('NEXUSAPI_SECRET'),
        'username'   => env('NEXUSAPI_USERNAME'),
        'password'   => env('NEXUSAPI_PASSWORD'),
        // Seconds to cache the issued bearer token (set just under real TTL).
        'token_ttl'  => (int) env('NEXUSAPI_TOKEN_TTL', 840),
    ],

    /*
    |--------------------------------------------------------------------------
    | GDS credentials (Travelport / Galileo 1G — GoLite IATA reseller)
    |--------------------------------------------------------------------------
    | Passed through to the GDS session. Defaults intentionally null — set the
    | issued values in .env. (Reference values were shared per-branch in the
    | Farenexus onboarding email; do not hardcode them here.)
    */
    'gds' => [
        'provider'      => env('NEXUSAPI_GDS_PROVIDER', '1G'), // 1G = Galileo/Travelport
        'pcc'           => env('NEXUSAPI_PCC'),                // Main PCC (e.g. UAE 53FA)
        'target_branch' => env('NEXUSAPI_TARGET_BRANCH'),      // UAPI target branch (e.g. P4971960)
        'gds_username'  => env('NEXUSAPI_GDS_USERNAME'),       // e.g. uAPI...-03977649
        'gds_password'  => env('NEXUSAPI_GDS_PASSWORD'),
        'gpm_client_id' => env('NEXUSAPI_GPM_CLIENT_ID'),      // e.g. GQST5SLS
        'accreditation' => env('NEXUSAPI_ACCREDITATION', 'GoLite'),
        'devices' => [
            'tkt' => env('NEXUSAPI_DEVICE_TKT'), // ticketing device (e.g. 8CA603)
            'itn' => env('NEXUSAPI_DEVICE_ITN'), // itinerary device (e.g. 8CA601)
            'mir' => env('NEXUSAPI_DEVICE_MIR'), // MIR device (e.g. 8CA604)
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Branch PCCs (point-of-sale per market)
    |--------------------------------------------------------------------------
    | Multi-market routing: pick the issuing PCC based on the storefront /
    | tenant (UAE main, plus Canada / USA / India branches).
    */
    'branches' => [
        'uae'    => env('NEXUSAPI_PCC_UAE'),    // e.g. 53FA
        'india'  => env('NEXUSAPI_PCC_INDIA'),  // e.g. 3RM1
        'canada' => env('NEXUSAPI_PCC_CANADA'), // e.g. 8O9K
        'usa'    => env('NEXUSAPI_PCC_USA'),     // e.g. 36CT
        'svcb'   => env('NEXUSAPI_PCC_SVCB'),    // e.g. 7S2D
    ],
    'default_branch' => env('NEXUSAPI_DEFAULT_BRANCH', 'uae'),

    /*
    |--------------------------------------------------------------------------
    | Endpoint paths (relative to base_url)
    |--------------------------------------------------------------------------
    | PLACEHOLDERS — confirm against official nexusAPI docs. Kept in config so
    | the service code never hardcodes a path and adjusting to the real spec is
    | a one-line change.
    */
    'endpoints' => [
        'token'          => env('NEXUSAPI_EP_TOKEN', 'v1/auth/token'),

        // Air (flights)
        'air_search'     => env('NEXUSAPI_EP_AIR_SEARCH', 'v1/air/search'),
        'air_price'      => env('NEXUSAPI_EP_AIR_PRICE', 'v1/air/price'),       // revalidate / fare confirm
        'air_rules'      => env('NEXUSAPI_EP_AIR_RULES', 'v1/air/fare-rules'),
        'air_book'       => env('NEXUSAPI_EP_AIR_BOOK', 'v1/air/book'),          // create PNR
        'air_ticket'     => env('NEXUSAPI_EP_AIR_TICKET', 'v1/air/ticket'),      // issue ticket
        'air_retrieve'   => env('NEXUSAPI_EP_AIR_RETRIEVE', 'v1/air/booking'),   // retrieve PNR
        'air_cancel'     => env('NEXUSAPI_EP_AIR_CANCEL', 'v1/air/cancel'),
        'air_void'       => env('NEXUSAPI_EP_AIR_VOID', 'v1/air/void'),
        'air_refund'     => env('NEXUSAPI_EP_AIR_REFUND', 'v1/air/refund'),
        'air_seatmap'    => env('NEXUSAPI_EP_AIR_SEATMAP', 'v1/air/seatmap'),
        'air_ancillary'  => env('NEXUSAPI_EP_AIR_ANCILLARY', 'v1/air/ancillaries'),

        // Hotel / Car (next phase per Farenexus roadmap)
        'hotel_search'   => env('NEXUSAPI_EP_HOTEL_SEARCH', 'v1/hotel/search'),
        'hotel_book'     => env('NEXUSAPI_EP_HOTEL_BOOK', 'v1/hotel/book'),
        'car_search'     => env('NEXUSAPI_EP_CAR_SEARCH', 'v1/car/search'),
        'car_book'       => env('NEXUSAPI_EP_CAR_BOOK', 'v1/car/book'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Commercials
    |--------------------------------------------------------------------------
    */
    // Markup applied to net GDS/NDC fares before display (percent).
    'markup_percent' => (float) env('NEXUSAPI_MARKUP_PERCENT', 0),

    // Default currency for search if not supplied by the storefront.
    'default_currency' => env('NEXUSAPI_DEFAULT_CURRENCY', 'AED'),

    // HTTP request timeout (seconds). Air search can be slow; allow headroom.
    'timeout' => (int) env('NEXUSAPI_TIMEOUT', 45),

    // Log full request/response payloads (verbose). Disable in production.
    'debug' => (bool) env('NEXUSAPI_DEBUG', false),
];
