<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Company;
use App\Models\User;
use App\Models\ReferralAgent;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

echo "Creating test data...\n\n";

// Create Test Companies
$companies = [
    [
        'name' => 'TravelMax UAE',
        'slug' => 'travelmax-uae',
        'subdomain' => 'travelmax',
        'email' => 'admin@travelmax.ae',
        'phone' => '+971501234567',
        'primary_color' => '#FF6B35',
        'secondary_color' => '#004E89',
        'currency' => 'AED',
        'timezone' => 'Asia/Dubai',
        'plan' => 'pro',
        'markup_percentage' => 25,
        'is_active' => true,
        'subscription_ends_at' => now()->addMonths(6),
    ],
    [
        'name' => 'Global eSIM Solutions',
        'slug' => 'global-esim',
        'subdomain' => 'globalesim',
        'email' => 'contact@globalesim.com',
        'phone' => '+14155551234',
        'primary_color' => '#2ECC71',
        'secondary_color' => '#3498DB',
        'currency' => 'USD',
        'timezone' => 'America/New_York',
        'plan' => 'enterprise',
        'markup_percentage' => 30,
        'is_active' => true,
        'subscription_ends_at' => now()->addYear(),
    ],
    [
        'name' => 'SimplyConnect',
        'slug' => 'simplyconnect',
        'subdomain' => 'simplyconnect',
        'email' => 'hello@simplyconnect.io',
        'phone' => '+442071234567',
        'primary_color' => '#9B59B6',
        'secondary_color' => '#E74C3C',
        'currency' => 'GBP',
        'timezone' => 'Europe/London',
        'plan' => 'basic',
        'markup_percentage' => 20,
        'is_active' => true,
        'subscription_ends_at' => now()->addMonths(3),
    ],
    [
        'name' => 'TestCo Trial',
        'slug' => 'testco-trial',
        'subdomain' => 'testco',
        'email' => 'test@testco.com',
        'primary_color' => '#F39C12',
        'secondary_color' => '#1ABC9C',
        'currency' => 'AED',
        'timezone' => 'Asia/Dubai',
        'plan' => 'trial',
        'markup_percentage' => 15,
        'is_active' => true,
        'trial_ends_at' => now()->addDays(7),
    ],
];

foreach ($companies as $companyData) {
    $company = Company::updateOrCreate(
        ['slug' => $companyData['slug']],
        $companyData
    );
    echo "Created company: {$company->name}\n";

    // Create admin user for each company
    $adminUser = User::updateOrCreate(
        ['email' => $companyData['email']],
        [
            'name' => explode('@', $companyData['email'])[0],
            'password' => Hash::make('password123'),
            'company_id' => $company->id,
            'role' => 'company_owner',
        ]
    );
    echo "  - Admin: {$adminUser->email} (password: password123)\n";

    // Create 2 referral agents per company
    for ($i = 1; $i <= 2; $i++) {
        $agent = ReferralAgent::updateOrCreate(
            ['email' => "agent{$i}@{$company->slug}.test"],
            [
                'name' => "Agent {$i} - {$company->name}",
                'phone' => '+971500000' . rand(100, 999),
                'referral_code' => strtoupper(Str::random(8)),
                'commission_rate' => rand(5, 15),
                'company_id' => $company->id,
                'is_active' => true,
            ]
        );
        echo "  - Agent: {$agent->name} (Code: {$agent->referral_code})\n";
    }
    echo "\n";
}

echo "\n✅ Test data created successfully!\n";
echo "\nYou can login to any company admin with:\n";
echo "Email: [company email]\n";
echo "Password: password123\n";
