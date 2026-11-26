<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\B2bTenantAgreement;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Manager -> Tenant Agreements: platform staff generate the B2B Tenant Portal
 * Agreement PDF for a partner company, with rent and percentage left blank.
 */
class B2bTenantAgreementTest extends TestCase
{
    use DatabaseTransactions;

    private function platform(): Company
    {
        return Company::where('slug', 'gotrips')->firstOrFail();
    }

    private function manager(Company $company, string $role = 'company_owner'): User
    {
        return User::factory()->create([
            'company_id' => $company->id,
            'role' => $role,
            'access_type' => 'manager',
            'is_active' => true,
        ]);
    }

    private function partner(array $overrides = []): array
    {
        return array_merge([
            'legal_name' => 'Desert Horizon Tours LLC',
            'trade_licence' => '',
            'address' => 'Office 12, Al Wahda Tower, Abu Dhabi',
            'country' => 'United Arab Emirates',
            'contact_name' => 'Sara Khan',
            'contact_title' => 'Managing Director',
            'email' => 'sara@deserthorizon.example',
            'phone' => '+971 50 123 4567',
            'portal_domain' => 'deserthorizon.gotrips.ai',
            'start_date' => '2026-12-01',
            'term_months' => 12,
            'modules' => ['tours', 'visas'],
        ], $overrides);
    }

    public function test_page_loads_for_platform_manager(): void
    {
        $this->actingAs($this->manager($this->platform()))
            ->get(route('manager.tenant-agreements.index'))
            ->assertOk()
            ->assertSee('Tenant Agreements')
            ->assertSee('Generate PDF')
            ->assertDontSee('name="monthly_rent"', false);
    }

    public function test_generating_stores_the_agreement_and_downloads_a_pdf(): void
    {
        $this->actingAs($this->manager($this->platform()));

        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.tenant-agreements.store'), $this->partner())
            ->assertRedirect(route('manager.tenant-agreements.index'))
            ->assertSessionHasNoErrors();

        $agreement = B2bTenantAgreement::where('partner_name', 'Desert Horizon Tours LLC')->latest('id')->firstOrFail();
        $this->assertMatchesRegularExpression('/^GT-B2B-\d{4}-\d{4}$/', $agreement->reference);
        $this->assertSame($this->platform()->id, $agreement->company_id);

        $this->get(route('manager.tenant-agreements.index'))
            ->assertOk()
            ->assertSee($agreement->reference)
            ->assertSee('https://wa.me/971501234567?text=', false);

        $response = $this->get(route('manager.tenant-agreements.download', $agreement));
        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_rent_and_percentage_are_printed_blank(): void
    {
        $this->actingAs($this->manager($this->platform()));

        // Even if someone posts values for them, nothing is stored or printed.
        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.tenant-agreements.store'), $this->partner([
                'monthly_rent' => '4321',
                'sales_percentage' => '17',
            ]));

        $agreement = B2bTenantAgreement::where('partner_name', 'Desert Horizon Tours LLC')->latest('id')->firstOrFail();
        $this->assertArrayNotHasKey('monthly_rent', $agreement->partner);

        $html = view('contracts.b2b-tenant-agreement', [
            'agreement' => $agreement,
            'platform' => $this->platform(),
            'modules' => Company::AVAILABLE_FEATURES,
            'terms' => \App\Http\Controllers\Manager\ManagerTenantAgreementsController::TERMS,
        ])->render();

        $this->assertStringContainsString('AED ________________</span> per month', $html);   // clause 3.1
        $this->assertStringContainsString('AED __________________</span> per month', $html); // Schedule 1
        $this->assertSame(2, substr_count($html, '__________ %'));
        $this->assertStringNotContainsString('4321', $html);
        $this->assertStringContainsString('Desert Horizon Tours LLC', $html);
        $this->assertStringContainsString('SCHEDULE 1', $html);
    }

    public function test_validation_rejects_missing_fields_and_bad_phone(): void
    {
        $this->actingAs($this->manager($this->platform()));

        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.tenant-agreements.store'), $this->partner([
                'legal_name' => '',
                'phone' => 'call me',
                'term_months' => 5,
                'modules' => ['nonsense'],
            ]))
            ->assertSessionHasErrors(['legal_name', 'phone', 'term_months', 'modules.0']);
    }

    public function test_tenant_managers_cannot_open_it(): void
    {
        $tenant = Company::create([
            'name' => 'Agreement Test Tenant',
            'slug' => 'agreement-test-' . uniqid(),
            'is_active' => true,
            'plan' => 'basic',
            'subscription_ends_at' => now()->addYear(),
            'features' => array_keys(Company::AVAILABLE_FEATURES),
        ]);

        $this->actingAs($this->manager($tenant))
            ->get('http://' . $tenant->subdomain . '.gotrips.ai/manager/tenant-agreements')
            ->assertNotFound();
    }

    public function test_customer_care_cannot_open_it(): void
    {
        $this->actingAs($this->manager($this->platform(), 'customer_care'))
            ->get(route('manager.tenant-agreements.index'))
            ->assertRedirect(route('manager.support.index'));
    }
}
