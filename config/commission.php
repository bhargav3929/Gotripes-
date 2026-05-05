<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Commission Hold Period
    |--------------------------------------------------------------------------
    |
    | Number of days a freshly recorded commission stays in `pending` before
    | the scheduled `commissions:release` command flips it to `available`.
    |
    | The hold protects against chargebacks / refunds — within the hold window
    | a refund event reverses the commission cleanly.
    |
    | Set to 0 for no hold (commission is immediately withdrawable). 14 is a
    | reasonable production default that matches typical card-network dispute
    | windows.
    |
    */
    'hold_days' => env('COMMISSION_HOLD_DAYS', 0),

    /*
    |--------------------------------------------------------------------------
    | Services that generate commission
    |--------------------------------------------------------------------------
    |
    | Map a Nomod booking type → the source_type stored on the commission row.
    | A type missing from this map will NOT generate commission (e.g. umrah
    | which is direct-billed by the platform, not the tenant).
    |
    */
    'eligible_services' => [
        'activity'      => 'activity_booking',
        'esim'          => 'esim_order',
        'visa'          => 'visa_application',
        'agent_booking' => 'agent_booking',  // flights / hotels via agent
    ],

];
