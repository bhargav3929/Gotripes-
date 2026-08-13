<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Company;
use App\Models\Emirates;
use App\Models\UAEVisaPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Aug 13 dashboard redesign: the cross-package "Pricing Matrix" grid is gone,
 * pricing now surfaces inline inside each package's own accordion row, and
 * the create/edit layout is two columns instead of table cells scattered
 * across a form= attribute trick.
 */
class UAEVisaPackageDashboardViewTest extends TestCase
{
    use DatabaseTransactions;

    private function manager(): User
    {
        $company = Company::where('slug', 'gotrips')->firstOrFail();

        return User::where('company_id', $company->id)
            ->whereIn('role', ['company_owner', 'company_admin'])
            ->firstOrFail();
    }

    public function test_pricing_matrix_grid_is_gone_from_the_dashboard(): void
    {
        $response = $this->actingAs($this->manager())->get(route('manager.visa-pricing.index'));

        $response->assertOk();
        $response->assertDontSee('Pricing Matrix');
        $response->assertDontSee('Active Price Grid');
    }

    public function test_package_pricing_surfaces_inline_inside_its_own_accordion_row(): void
    {
        $emirate = Emirates::where('emiratesName', 'Dubai')->firstOrFail();
        $manager = $this->manager();

        $this->actingAs($manager)->withoutMiddleware(VerifyCsrfToken::class)->post(route('manager.visa-packages.store'), [
            'emirates_id'     => $emirate->emiratesID,
            'name'            => 'Test Inline Pricing Package',
            'entry_type'      => 'Single Entry',
            'duration'        => '30 Days',
            'traveller_type'  => 'Adult',
            'price'           => 321,
        ]);

        UAEVisaPackage::where('name', 'Test Inline Pricing Package')->firstOrFail();

        $response = $this->actingAs($manager)->get(route('manager.visa-pricing.index'));

        $response->assertOk();
        $response->assertSee('Test Inline Pricing Package');
        // The package's own price value is rendered inside its own row.
        $response->assertSee('value="321"', false);
        // Every field the redesign requires stays fully visible in the DOM,
        // not split across table cells reachable only by tabbing.
        $response->assertSee('Package Name', false);
        $response->assertSee('Supplier Email', false);
        $response->assertSee('Our Company Email', false);
        $response->assertSee('Add nationality-specific override', false);
    }

    public function test_customer_storefront_pricing_is_unaffected_by_the_admin_redesign(): void
    {
        $emirate = Emirates::where('emiratesName', 'Dubai')->firstOrFail();
        $manager = $this->manager();

        $this->actingAs($manager)->withoutMiddleware(VerifyCsrfToken::class)->post(route('manager.visa-packages.store'), [
            'emirates_id'     => $emirate->emiratesID,
            'name'            => 'Test Storefront Package',
            'entry_type'      => 'Single Entry',
            'duration'        => '30 Days',
            'traveller_type'  => 'Adult',
            'price'           => 777,
        ]);

        $response = $this->get('/uaevisa');

        $response->assertOk();
        $response->assertSee('777', false);
    }
}
