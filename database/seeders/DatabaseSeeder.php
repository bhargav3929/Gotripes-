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
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

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
