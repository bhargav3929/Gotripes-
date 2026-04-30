<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

/**
 * Seeds the three agent subdomains required by the partner program:
 *
 *   bhargav.gotrips.ai  →  activities only
 *   telha.gotrips.ai    →  activities + Hajj & Umrah
 *   amer.gotrips.ai     →  full access (all services)
 *
 * Idempotent: re-running this seeder updates feature lists in place
 * without creating duplicates.
 */
class AgentCompaniesSeeder extends Seeder
{
    public function run(): void
    {
        $agents = [
            [
                'name'      => 'Bhargav',
                'subdomain' => 'bhargav',
                'features'  => ['activities'],
            ],
            [
                'name'      => 'Telha',
                'subdomain' => 'telha',
                'features'  => ['activities', 'hajj_umrah'],
            ],
            [
                'name'      => 'Amer',
                'subdomain' => 'amer',
                'features'  => array_keys(Company::AVAILABLE_FEATURES),
            ],
        ];

        foreach ($agents as $data) {
            Company::updateOrCreate(
                ['subdomain' => $data['subdomain']],
                [
                    'name'       => $data['name'] . ' (GoTrips Partner)',
                    'slug'       => $data['subdomain'],
                    'type'       => 'agency',
                    'features'   => $data['features'],
                    'is_active'  => true,
                    'plan'       => 'pro',
                    'currency'   => 'AED',
                    'timezone'   => 'Asia/Dubai',
                    'primary_color'   => '#FFD700',
                    'secondary_color' => '#D4AF37',
                ]
            );
        }
    }
}
