<?php

/*
|--------------------------------------------------------------------------
| Fluxir — Global Travel Compliance (e-Visa) API
|--------------------------------------------------------------------------
|
| Fluxir is a Compliance-as-a-Service platform for visa applications. GoTrips
| integrates it as the processing backend behind the e-visa form.
|
| Documented flow (https://developer.fluxir.com):
|   1. POST {auth_url}/connect/token            -> bearer token (client_credentials)
|   2. POST {api}/api/app/persons               -> create traveller (personId)
|   3. POST {api}/api/app/trip                  -> create trip (tripId)
|   4. POST {api}/api/app/travel-services       -> create visa application (serviceAppId)
|   5. POST {api}/api/app/attachments/identity-document  -> upload passport/photo
|   6. PATCH {api}/api/app/travel-services/{id} -> fill items + state=ReadyForPayment
|   7. GET  {api}/api/app/trip/{tripId}/checkout?... -> Stripe checkout session
|   8. POST {api}/api/app/trip/{tripId}/finalize-checkout -> confirm after payment
|
| Auth scheme = OAuth2 client_credentials, scope "TravelAideAPI", plus an
| X-Tenant header on EVERY request. Token TTL ~1 year.
|
| All values are env-driven. The client secret is sensitive — keep it in .env
| only (never commit). NOTE: a `cs_test_*` checkout session => the tenant is a
| TEST account; production payments need production Fluxir credentials.
|
*/

return [

    // OAuth token issuer and API gateway (per docs).
    'auth_url' => env('FLUXIR_AUTH_URL', 'https://auth.fluxir.com'),
    'api_url'  => env('FLUXIR_API_URL', 'https://api.fluxir.com'),

    // Multi-tenant identity (sent as X-Tenant on every call).
    'tenant_id'     => env('FLUXIR_TENANT_ID'),

    // OAuth2 client credentials.
    'client_id'     => env('FLUXIR_CLIENT_ID'),
    'client_secret' => env('FLUXIR_CLIENT_SECRET'),
    'scope'         => env('FLUXIR_SCOPE', 'TravelAideAPI'),
    'grant_type'    => env('FLUXIR_GRANT_TYPE', 'client_credentials'),

    // Cache the bearer token just under its real TTL. VERIFIED via live test:
    // the token actually expires in ~3599s (1h), NOT the "1 year" the docs
    // claim. Cache 3300s (55m) so we refresh before expiry; retry-on-401 is
    // the backstop.
    'token_ttl' => (int) env('FLUXIR_TOKEN_TTL', 3300),

    // Checkout redirect targets (Stripe-hosted page returns here).
    'success_url' => env('FLUXIR_SUCCESS_URL', env('APP_URL') . '/visa/fluxir/success'),
    'cancel_url'  => env('FLUXIR_CANCEL_URL', env('APP_URL') . '/visa/fluxir/cancel'),

    // Fluxir checkout returns a Stripe Checkout *session id* (cs_..._). The
    // browser redirects to it via Stripe.js using THIS publishable key, which
    // must belong to Fluxir's Stripe account (ask Fluxir for it). Safe to expose.
    'stripe_publishable_key' => env('FLUXIR_STRIPE_PUBLISHABLE_KEY'),

    // Default service application attributes.
    'service_type'    => env('FLUXIR_SERVICE_TYPE', 'Visa'),
    // VERIFIED live: provider-names for Visa returns ["Fluxir"].
    'provider_name'   => env('FLUXIR_PROVIDER_NAME', 'Fluxir'),
    'default_origin'  => env('FLUXIR_DEFAULT_ORIGIN'),        // ISO country of departure, optional
    'default_destination' => env('FLUXIR_DEFAULT_DESTINATION', 'ARE'), // UAE by default

    // UAE eVisa type (VERIFIED live: visa-type id 2, "United Arab Emirates eVisa",
    // fee 86, 72h). documentVersionId is resolved from this type's active-version
    // unless one is supplied per-request.
    'visa_type_id' => env('FLUXIR_VISA_TYPE_ID', 2),

    // serviceIntentKey is normally discovered via the service-intents endpoint.
    // If Fluxir gives us a fixed key (or the intents lookup is unavailable),
    // set it here to bypass discovery. Null = discover at runtime.
    'service_intent_key' => env('FLUXIR_SERVICE_INTENT_KEY'),

    // HTTP request timeout (seconds).
    'timeout' => (int) env('FLUXIR_TIMEOUT', 45),

    // Verbose request/response logging. Disable in production.
    'debug' => (bool) env('FLUXIR_DEBUG', false),
];
