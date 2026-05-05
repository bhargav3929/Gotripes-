<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds two real tenant companies + their owner users so you can visually
 * verify multi-tenant isolation, branding, feature gates, and email routing.
 *
 * Idempotent — re-running the seeder updates the rows in place rather than
 * creating duplicates.
 *
 * Usage:
 *   php artisan db:seed --class=TestTenantsSeeder
 */
class TestTenantsSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = [
            [
                'name'             => 'Fortune Travels',
                'subdomain'        => 'fortune',
                'email'            => 'owner@fortune.test',
                'phone'            => '+971 50 111 1111',
                'address'          => 'Sheikh Zayed Road, Dubai, UAE',
                'primary_color'    => '#16a34a',  // green
                'secondary_color'  => '#15803d',
                'plan'             => 'pro',
                'commission_value' => 12,
                'features'         => ['activities', 'visas', 'esim', 'tours'],
                'admin_name'       => 'Fortune Owner',
                'admin_password'   => 'Test@1234',
            ],
            [
                'name'             => 'Sahara Adventures',
                'subdomain'        => 'sahara',
                'email'            => 'owner@sahara.test',
                'phone'            => '+971 50 222 2222',
                'address'          => 'Al Quoz, Dubai, UAE',
                'primary_color'    => '#d97706',  // amber
                'secondary_color'  => '#b45309',
                'plan'             => 'basic',
                'commission_value' => 8,
                'features'         => ['activities', 'tours'],   // intentionally NO esim/visas
                'admin_name'       => 'Sahara Owner',
                'admin_password'   => 'Test@1234',
            ],
        ];

        foreach ($tenants as $t) {
            $company = Company::updateOrCreate(
                ['subdomain' => $t['subdomain']],
                [
                    'name'             => $t['name'],
                    'slug'             => $t['subdomain'],
                    'email'            => $t['email'],
                    'phone'            => $t['phone'],
                    'address'          => $t['address'],
                    'primary_color'    => $t['primary_color'],
                    'secondary_color' => $t['secondary_color'],
                    'plan'             => $t['plan'],
                    'commission_type'  => 'percentage',
                    'commission_value' => $t['commission_value'],
                    'features'         => $t['features'],
                    'currency'         => 'AED',
                    'timezone'         => 'Asia/Dubai',
                    'is_active'        => true,
                    // Long subscription so we never get blocked by expiry middleware
                    'subscription_ends_at' => now()->addYears(5),
                ]
            );

            User::updateOrCreate(
                ['email' => $t['email']],
                [
                    'name'           => $t['admin_name'],
                    'password'       => Hash::make($t['admin_password']),
                    'company_id'     => $company->id,
                    'role'           => 'company_owner',
                    'is_super_admin' => 0,
                    'access_type'    => 'manager',
                ]
            );

            $this->command->info(sprintf(
                "  %s  (id=%d, subdomain=%s, features=%s)  →  user=%s / Test@1234",
                str_pad($t['name'], 22),
                $company->id,
                $company->subdomain,
                implode(',', $t['features']),
                $t['email']
            ));
        }

        $this->command->info('');
        $this->command->info('Test tenants ready. Visit:');
        $this->command->info('  http://fortune.gotrips.local:8000/manager/login');
        $this->command->info('  http://sahara.gotrips.local:8000/manager/login');
    }
}
