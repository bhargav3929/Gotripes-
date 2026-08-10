<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Company;
use App\Models\Emirates;
use App\Models\UAEVisaPackage;
use App\Models\UAEVisaPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Creating a Dubai/UAE visa package used to leave the Pricing tab empty —
 * managers had to add every entry-type/duration/traveller row by hand before
 * a package could actually sell anything. storePackage() now pre-fills the
 * full matrix, and storePriceRow() rejects exact duplicates of it.
 */
class ManagerVisaPackagePricingTest extends TestCase
{
    use DatabaseTransactions;

    private function manager(): User
    {
        $company = Company::where('slug', 'gotrips')->firstOrFail();

        return User::where('company_id', $company->id)
            ->whereIn('role', ['company_owner', 'company_admin'])
            ->firstOrFail();
    }

    public function test_creating_a_package_prefills_the_full_pricing_matrix(): void
    {
        $emirate = Emirates::where('emiratesName', 'Dubai')->firstOrFail();

        $response = $this->actingAs($this->manager())
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.visa-packages.store'), [
                'emirates_id' => $emirate->emiratesID,
                'name'        => 'Test Auto-Matrix Package',
                'description' => 'created by automated test',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $package = UAEVisaPackage::where('name', 'Test Auto-Matrix Package')->first();
        $this->assertNotNull($package);

        $prices = UAEVisaPrice::where('visa_package_id', $package->id)->get();
        $this->assertCount(12, $prices, 'expected 2 entry types x 2 durations x 3 traveller types');

        $this->assertTrue($prices->every(fn ($p) => (float) $p->price === 0.0));
        $this->assertTrue($prices->every(fn ($p) => !$p->isActive));
        $this->assertTrue($prices->every(fn ($p) => $p->nationality === null));

        $combos = $prices->map(fn ($p) => "{$p->entry_type}|{$p->duration}|{$p->traveller_type}")->unique();
        $this->assertCount(12, $combos, 'every combination must be distinct');
    }

    public function test_adding_a_duplicate_price_row_is_rejected(): void
    {
        $emirate = Emirates::where('emiratesName', 'Dubai')->firstOrFail();
        $manager = $this->manager();

        $this->actingAs($manager)->withoutMiddleware(VerifyCsrfToken::class)->post(route('manager.visa-packages.store'), [
            'emirates_id' => $emirate->emiratesID,
            'name'        => 'Test Duplicate Package',
        ]);

        $package = UAEVisaPackage::where('name', 'Test Duplicate Package')->firstOrFail();
        $this->assertCount(12, UAEVisaPrice::where('visa_package_id', $package->id)->get());

        // Same combination as one of the auto-generated rows.
        $response = $this->actingAs($manager)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.visa-prices.store'), [
                'visa_package_id' => $package->id,
                'entry_type'      => 'Single Entry',
                'duration'        => '30 Days',
                'traveller_type'  => 'Adult',
                'price'           => 500,
            ]);

        $response->assertSessionHasErrors('price');
        $this->assertCount(12, UAEVisaPrice::where('visa_package_id', $package->id)->get(), 'no extra row should have been created');
    }

    public function test_a_nationality_specific_override_is_allowed_alongside_the_generic_row(): void
    {
        $emirate = Emirates::where('emiratesName', 'Dubai')->firstOrFail();
        $manager = $this->manager();

        $this->actingAs($manager)->withoutMiddleware(VerifyCsrfToken::class)->post(route('manager.visa-packages.store'), [
            'emirates_id' => $emirate->emiratesID,
            'name'        => 'Test Nationality Package',
        ]);

        $package = UAEVisaPackage::where('name', 'Test Nationality Package')->firstOrFail();

        $response = $this->actingAs($manager)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.visa-prices.store'), [
                'visa_package_id' => $package->id,
                'entry_type'      => 'Single Entry',
                'duration'        => '30 Days',
                'traveller_type'  => 'Adult',
                'nationality'     => 'India',
                'price'           => 450,
            ]);

        $response->assertSessionHasNoErrors();
        $this->assertCount(13, UAEVisaPrice::where('visa_package_id', $package->id)->get());

        $indiaRow = UAEVisaPrice::where('visa_package_id', $package->id)->where('nationality', 'India')->first();
        $this->assertNotNull($indiaRow);
        $this->assertEquals(450, $indiaRow->price);
        $this->assertTrue((bool) $indiaRow->isActive);
    }
}
