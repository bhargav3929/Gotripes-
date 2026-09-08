<?php

namespace Tests\Feature;

use App\Models\AgentApplication;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AgentApplicationFilterTest extends TestCase
{
    use DatabaseTransactions;

    private function manager(): User
    {
        $company = Company::where('slug', 'gotrips')->firstOrFail();

        return User::factory()->create([
            'company_id' => $company->id,
            'role' => 'company_admin',
            'access_type' => 'manager',
            'is_active' => true,
        ]);
    }

    private function application(array $overrides): AgentApplication
    {
        return AgentApplication::create(array_merge([
            'company_id' => Company::where('slug', 'gotrips')->value('id'),
            'name' => 'Filter Applicant',
            'email' => 'filter-' . uniqid() . '@example.com',
            'password' => Hash::make('Password123!'),
            'phone' => '+971500000099',
            'country' => 'United Arab Emirates',
            'emirate' => 'Dubai',
            'services' => ['esim'],
            'trade_license_expiry_date' => now()->addYear()->toDateString(),
            'status' => 'pending',
        ], $overrides));
    }

    public function test_configured_service_location_and_expired_license_filters_can_be_combined(): void
    {
        $matching = $this->application([
            'name' => 'Matching Applicant',
            'country' => 'India',
            'address' => 'Dubai Marina',
            'services' => ['esim', 'tours'],
            'trade_license_expiry_date' => now()->subDay()->toDateString(),
        ]);
        $this->application([
            'name' => 'Different Applicant',
            'country' => 'India',
            'address' => 'Abu Dhabi',
            'services' => ['activities'],
            'trade_license_expiry_date' => now()->addYear()->toDateString(),
        ]);

        $this->actingAs($this->manager())
            ->get(route('manager.agent-applications.index', [
                'status' => 'all',
                'service' => 'esim',
                'country' => 'India',
                'location' => 'Dubai',
                'license_status' => 'expired',
            ]))
            ->assertOk()
            ->assertSee($matching->name)
            ->assertDontSee('Different Applicant');
    }
}
