<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Aug 13 dashboard redesign, item 3: a "Global e-Visa" tab, separate from
 * UAE Visa package pricing, for managing per-agent e-Visa commissions built
 * on top of the existing Manager -> Agents (company_agent) accounts.
 */
class ManagerEvisaSettingsTest extends TestCase
{
    use DatabaseTransactions;

    private function manager(): User
    {
        $company = Company::where('slug', 'gotrips')->firstOrFail();

        return User::where('company_id', $company->id)
            ->whereIn('role', ['company_owner', 'company_admin'])
            ->firstOrFail();
    }

    private function agent(int $companyId, array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role'                      => 'company_agent',
            'company_id'                => $companyId,
            'agent_services'            => ['esim'],
            'evisa_commission_percent'  => null,
            'is_active'                 => true,
        ], $overrides));
    }

    public function test_index_is_scoped_to_the_managers_own_company(): void
    {
        $company = Company::where('slug', 'gotrips')->firstOrFail();
        $otherCompany = Company::create([
            'name' => 'Other Co', 'slug' => 'other-co-' . uniqid(), 'features' => ['visas'],
        ]);

        $ownAgent = $this->agent($company->id, ['name' => 'Own Agent Visible']);
        $otherAgent = $this->agent($otherCompany->id, ['name' => 'Other Tenant Agent']);

        $response = $this->actingAs($this->manager())->get(route('manager.evisa-settings.index'));

        $response->assertOk();
        $response->assertSee('Own Agent Visible');
        $response->assertDontSee('Other Tenant Agent');
    }

    public function test_agent_commission_can_be_enabled_and_saved(): void
    {
        $company = Company::where('slug', 'gotrips')->firstOrFail();
        $agent = $this->agent($company->id, ['agent_services' => []]);
        $manager = $this->manager();

        $response = $this->actingAs($manager)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->put(route('manager.evisa-settings.agent-commissions.update'), [
                'agents' => [
                    $agent->id => ['enabled' => '1', 'commission_percent' => 12.5],
                ],
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $agent->refresh();
        $this->assertTrue($agent->hasService('evisa'));
        $this->assertEquals(12.5, (float) $agent->evisa_commission_percent);
    }

    public function test_agent_commission_can_be_disabled_again(): void
    {
        $company = Company::where('slug', 'gotrips')->firstOrFail();
        $agent = $this->agent($company->id, ['agent_services' => ['esim', 'evisa'], 'evisa_commission_percent' => 20]);
        $manager = $this->manager();

        $this->actingAs($manager)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->put(route('manager.evisa-settings.agent-commissions.update'), [
                'agents' => [
                    $agent->id => ['enabled' => '0', 'commission_percent' => 20],
                ],
            ]);

        $agent->refresh();
        $this->assertFalse($agent->hasService('evisa'));
        // The other previously-granted service must survive untouched.
        $this->assertContains('esim', $agent->agent_services);
    }

    public function test_a_cross_tenant_agent_id_in_the_payload_is_ignored(): void
    {
        $company = Company::where('slug', 'gotrips')->firstOrFail();
        $otherCompany = Company::create([
            'name' => 'Other Co 2', 'slug' => 'other-co-2-' . uniqid(), 'features' => ['visas'],
        ]);
        $foreignAgent = $this->agent($otherCompany->id, ['agent_services' => []]);
        $manager = $this->manager();

        $this->actingAs($manager)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->put(route('manager.evisa-settings.agent-commissions.update'), [
                'agents' => [
                    $foreignAgent->id => ['enabled' => '1', 'commission_percent' => 99],
                ],
            ]);

        $foreignAgent->refresh();
        $this->assertFalse($foreignAgent->hasService('evisa'), 'a manager must not be able to grant evisa to another tenant\'s agent');
        $this->assertNull($foreignAgent->evisa_commission_percent);
    }

    public function test_evisa_service_is_only_grantable_when_the_tenant_has_the_visas_feature(): void
    {
        // In this environment IdentifyTenant always resolves the request host
        // (localhost, in tests) back to the 'gotrips' company, so the gating
        // is exercised by toggling that company's own 'visas' feature rather
        // than by standing up a second tenant on a distinct host/domain.
        $company = Company::where('slug', 'gotrips')->firstOrFail();
        $manager = $this->manager();
        $originalFeatures = $company->features;

        $company->features = array_values(array_unique([...$originalFeatures, 'visas']));
        $company->save();
        $this->actingAs($manager)->get(route('manager.agents.create'))
            ->assertOk()->assertSee('e-Visa Commission');

        $company->features = array_values(array_diff($originalFeatures, ['visas']));
        $company->save();
        $this->actingAs($manager)->get(route('manager.agents.create'))
            ->assertOk()->assertDontSee('e-Visa Commission');

        $company->features = $originalFeatures;
        $company->save();
    }
}
