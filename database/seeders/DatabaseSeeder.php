<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed UAE Visa statuses
        \Illuminate\Support\Facades\DB::table('tbl_UAEVStatus')->insertOrIgnore([
            ['id' => 1, 'status_name' => 'Pending'],
            ['id' => 2, 'status_name' => 'Approved'],
            ['id' => 3, 'status_name' => 'Rejected'],
        ]);

        \App\Models\Company::updateOrCreate(
            ['slug' => 'gotrips'],
            [
                'name' => 'GoTrips Main',
                'domain' => 'gotrips.ai',
                'subdomain' => null,
                'plan' => 'enterprise',
                'is_active' => true,
                'features' => array_keys(\App\Models\Company::AVAILABLE_FEATURES),
                'subscription_ends_at' => now()->addYears(10),
            ]
        );

        $this->call([
            UAEEmiratesSeeder::class,
            AgentCompaniesSeeder::class,
            UAEVisaPricingSeeder::class,
        ]);
    }
}
