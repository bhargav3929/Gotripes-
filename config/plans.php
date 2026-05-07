<?php

use App\Models\Company;

/*
|--------------------------------------------------------------------------
| Tenant Subscription Plans
|--------------------------------------------------------------------------
|
| Each plan defines what a tenant on that plan gets:
|   - features:        which services they can sell
|   - commission_pct:  the % of every booking we (the platform) take
|   - price/period:    what they pay us per billing cycle
|
| When a super-admin changes a tenant's plan via /superadmin/companies,
| these defaults are applied to the company record.
|
| Note: A super-admin can still override individual features per tenant
| (e.g. give a Basic tenant access to eSIM as a special arrangement) by
| editing the company directly. This config sets the default starting
| point on plan change.
|
*/

return [

    'trial' => [
        'name'           => 'Trial',
        'price'          => 0,
        'period'         => 14,
        'period_unit'    => 'days',
        'features'       => ['activities', 'visas'],
        'commission_pct' => 5,
    ],

    'basic' => [
        'name'           => 'Basic',
        'price'          => 49,
        'period'         => 1,
        'period_unit'    => 'month',
        'features'       => ['activities', 'visas', 'tours'],
        'commission_pct' => 8,
    ],

    'pro' => [
        'name'           => 'Pro',
        'price'          => 199,
        'period'         => 1,
        'period_unit'    => 'month',
        'features'       => ['activities', 'visas', 'tours', 'esim', 'hajj_umrah'],
        'commission_pct' => 12,
    ],

    'enterprise' => [
        'name'           => 'Enterprise',
        'price'          => 699,
        'period'         => 1,
        'period_unit'    => 'month',
        // Every available feature
        'features'       => array_keys(Company::AVAILABLE_FEATURES),
        'commission_pct' => 15,
    ],

];
