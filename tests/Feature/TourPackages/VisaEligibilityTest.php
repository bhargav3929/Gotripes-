<?php

namespace Tests\Feature\TourPackages;

use App\Models\DestinationVisaRule;
use App\Models\TravelPackage;
use App\Services\TourPackages\VisaEligibility;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/** Step 1 of the tour wizard asks the Fluxir eVisa API whether a passport can go. */
class VisaEligibilityTest extends TestCase
{
    use DatabaseTransactions, TourPackageFixture;

    private function host($company, string $path): string
    {
        return 'http://' . $company->subdomain . '.gotrips.test' . $path;
    }

    /** Georgia package, Fluxir switched on, and its API answering with $items for any passport. */
    private function withFluxir(array $items): array
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        config(['fluxir.client_id' => 'id', 'fluxir.client_secret' => 'secret', 'fluxir.tenant_id' => 't',
            'fluxir.api_url' => 'https://fluxir.test', 'fluxir.auth_url' => 'https://auth.fluxir.test']);
        Cache::put('fluxir_access_token', 'token', 600);
        Cache::put('fluxir.online_catalog.v1', ['GEO' => ['code' => 'GEO', 'name' => 'Georgia', 'types' => [
            ['id' => 209, 'name' => 'Georgia eVisa', 'net_fee' => 30, 'processing' => 120, 'stay' => 30],
        ]]], 600);
        Http::fake(['fluxir.test/*' => Http::response(['items' => $items])]);
        return [$company, $p];
    }

    public function test_visa_free_passport_is_eligible(): void
    {
        [$company, $p] = $this->withFluxir([[
            'serviceType' => 'Visa', 'serviceIntentKey' => 'Visa-Fluxir-GEO-208', 'fee' => 0, 'isSelectable' => false,
            'extraProperties' => ['visaFree' => 'true', 'stayDuration' => '1', 'stayDurationUnit' => 'Years'],
        ]]);

        $this->getJson($this->host($company, "/tour-packages/{$p->id}/visa?nationality=United Kingdom"))
            ->assertOk()->assertJson(['source' => 'evisa', 'requirement' => 'visa_free', 'tone' => 'ok', 'max_stay_days' => 365]);

        Http::assertSent(fn ($r) => str_contains($r->url(), 'service-intents')
            && $r['tripContext']['items']['system.tripOrigination'] === 'GBR'
            && $r['tripContext']['items']['system.tripDestination'] === 'GEO');
    }

    public function test_evisa_passport_gets_the_evisa_and_its_selling_price(): void
    {
        [$company, $p] = $this->withFluxir([[
            'serviceType' => 'Visa', 'serviceIntentKey' => 'Visa-Fluxir-GEO-209', 'fee' => 0, 'isSelectable' => true,
            'extraProperties' => ['visaFree' => 'false', 'stayDuration' => '30', 'stayDurationUnit' => 'Days',
                'requirements' => "A passport valid for 6 months.\nHotel booking."],
        ]]);

        $res = $this->getJson($this->host($company, "/tour-packages/{$p->id}/visa?nationality=Egypt"))->assertOk();
        $res->assertJson(['requirement' => 'evisa', 'tone' => 'ok', 'max_stay_days' => 30, 'processing_days' => 5,
            'documents' => 'A passport valid for 6 months.', 'evisa' => ['name' => 'Georgia eVisa', 'currency' => 'USD']]);
        $this->assertGreaterThanOrEqual(30, $res->json('evisa.price'));
    }

    public function test_a_staff_rule_for_that_passport_wins_over_fluxir(): void
    {
        [$company, $p] = $this->withFluxir([[
            'serviceType' => 'Visa', 'serviceIntentKey' => 'Visa-Fluxir-GEO-208', 'isSelectable' => false,
            'extraProperties' => ['visaFree' => 'true'],
        ]]);

        // The fixture has an India rule: e-Visa, 5 days.
        $this->getJson($this->host($company, "/tour-packages/{$p->id}/visa?nationality=India"))
            ->assertOk()->assertJson(['requirement' => 'evisa', 'processing_days' => 5]);
        Http::assertNothingSent();
    }

    public function test_no_fluxir_answer_falls_back_to_the_rule_for_everyone(): void
    {
        [$company, $p] = $this->withFluxir([]);

        $this->getJson($this->host($company, "/tour-packages/{$p->id}/visa?nationality=Japan"))
            ->assertOk()->assertJson(['requirement' => 'visa_free', 'max_stay_days' => 365])
            ->assertJsonMissing(['source' => 'evisa']);
    }

    public function test_fluxir_down_still_answers(): void
    {
        [$company, $p] = $this->withFluxir([]);
        Http::fake(['fluxir.test/*' => Http::response('oops', 500)]);
        DestinationVisaRule::withoutCompanyScope()->where('company_id', $company->id)->where('nationality', '*')->delete();

        $this->getJson($this->host($company, "/tour-packages/{$p->id}/visa?nationality=Japan"))
            ->assertOk()->assertJson(['known' => false, 'tone' => 'unknown']);
    }

    public function test_country_names_map_to_fluxir_codes(): void
    {
        $this->assertSame('GEO', VisaEligibility::alpha3('Georgia'));
        $this->assertSame('ARE', VisaEligibility::alpha3('United Arab Emirates'));
        $this->assertSame('IND', VisaEligibility::alpha3('India'));
    }

    public function test_homepage_lists_bookable_packages_straight_into_the_wizard(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);

        $this->get($this->host($company, '/'))->assertOk()
            ->assertSee('Tour packages')
            ->assertSee(route('tour-packages.book', $p->id, false));
    }

    public function test_manager_can_switch_an_enquiry_package_to_the_wizard(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company, ['package_type' => 'enquire']);
        $owner = \App\Models\User::factory()->create(['company_id' => $company->id, 'role' => 'company_owner']);

        $this->actingAs($owner)->post($this->host($company, "/manager/packages/{$p->id}/use-wizard"))
            ->assertRedirect();
        $this->assertSame('wizard', TravelPackage::withoutCompanyScope()->find($p->id)->package_type);
    }
}
