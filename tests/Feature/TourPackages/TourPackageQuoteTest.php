<?php

namespace Tests\Feature\TourPackages;

use App\Services\TourPackages\TourPackageQuote;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * The quote is what the customer is charged, so every rule is pinned here.
 * Georgia fixture: 5 days 4-star adult net 1680, child 840, single supplement 360, margin 10%.
 */
class TourPackageQuoteTest extends TestCase
{
    use DatabaseTransactions, TourPackageFixture;

    private function quote(array $over = [], array $pkg = []): array
    {
        $company = $this->tenant();
        app()->instance('current_company', $company);
        return TourPackageQuote::make($this->georgia($company, $pkg), $this->input($over));
    }

    public function test_couple_pays_two_adult_rates_plus_margin(): void
    {
        $r = $this->quote();

        $this->assertTrue($r['ok'], json_encode($r['errors']));
        $q = $r['quote'];
        $this->assertSame(3360.0, $q['net_total']);              // 2 × 1680
        $this->assertSame(3696.0, $q['total']);                  // +10%
        $this->assertSame(4, $q['nights']);
        $this->assertSame(now()->addMonth()->addDays(4)->toDateString(), $q['end_date']);
        $this->assertArrayNotHasKey('net', $q['lines'][0], 'Net prices are never shown to the customer.');
        $this->assertSame('Rooms Tbilisi', $q['hotel']['name']);
    }

    public function test_lines_always_add_up_to_the_total(): void
    {
        $q = $this->quote(['traveler_type' => 'family', 'adults' => 3, 'child_ages' => [1, 7, 15],
            'meal_plan' => 'half_board', 'guide_language' => 'ar', 'transport' => 'luxury',
            'airport_transfer' => true, 'insurance' => true], ['margin_percent' => 12.5])['quote'];

        $this->assertEqualsWithDelta($q['total'], array_sum(array_column($q['lines'], 'amount')), 0.001);
    }

    public function test_children_are_priced_by_age(): void
    {
        $q = $this->quote(['traveler_type' => 'family', 'adults' => 2, 'child_ages' => [1, 7, 15]])['quote'];

        // 15-year-old pays the adult rate; 7 is a child; 1 is an infant.
        $this->assertSame(3, $q['adults']);
        $this->assertSame(1, $q['children']);
        $this->assertSame(1, $q['infants']);
        // 3 adults is odd: one single supplement.
        $keys = array_column($q['lines'], 'key');
        $this->assertContains('single_supplement', $keys);
        $this->assertSame((float) (3 * 1680 + 840 + 50 + 360), $q['net_total']);
    }

    public function test_solo_traveller_pays_the_single_supplement(): void
    {
        $q = $this->quote(['traveler_type' => 'solo', 'adults' => 1])['quote'];
        $this->assertSame((float) (1680 + 360), $q['net_total']);
    }

    public function test_addons_are_charged_on_their_basis(): void
    {
        $base = 2 * 1680;
        $q = $this->quote(['guide_language' => 'ar', 'transport' => 'luxury', 'meal_plan' => 'half_board',
            'airport_transfer' => true, 'insurance' => true])['quote'];

        $expected = $base
            + 40 * 5          // Arabic guide per day
            + 15 * 2 * 5      // luxury bus per person per day
            + 25 * 2 * 5      // half board per person per day
            + 120             // transfer per booking
            + 30 * 2;         // insurance per person
        $this->assertSame((float) $expected, $q['net_total']);
    }

    public function test_infants_ride_free_on_per_person_addons(): void
    {
        $q = $this->quote(['traveler_type' => 'family', 'adults' => 2, 'child_ages' => [0], 'insurance' => true])['quote'];
        $ins = collect($q['lines'])->firstWhere('key', 'insurance');
        $this->assertSame(round(30 * 2 * 1.1, 2), $ins['amount']);
    }

    public function test_meal_plan_must_be_one_the_hotel_serves(): void
    {
        // The 3-star hotel has no lunch or dinner menu.
        $r = $this->quote(['hotel_stars' => 3, 'meal_plan' => 'half_board']);
        $this->assertFalse($r['ok']);
        $this->assertArrayHasKey('meal_plan', $r['errors']);

        $this->assertTrue($this->quote(['hotel_stars' => 5, 'meal_plan' => 'full_board'])['ok']);
    }

    public function test_peak_season_surcharge_applies_to_the_package_rate(): void
    {
        $this->travelTo('2027-11-01');
        $q = $this->quote(['start_date' => '2027-12-30'])['quote'];
        $season = collect($q['lines'])->firstWhere('key', 'season');

        $this->assertSame('New Year', $q['season']);
        $this->assertSame(round(3360 * 0.20 * 1.1, 2), $season['amount']);
    }

    public function test_compulsory_insurance_is_always_added(): void
    {
        $q = $this->quote(['insurance' => false], ['insurance_required' => true])['quote'];
        $this->assertTrue($q['selections']['insurance']);
        $this->assertContains('insurance', array_column($q['lines'], 'key'));
    }

    public function test_visa_requirement_by_nationality_with_fallback(): void
    {
        $india = $this->quote(['nationality' => 'India'])['quote']['visa'];
        $this->assertSame('evisa', $india['requirement']);
        $this->assertSame('warn', $india['tone']);
        $this->assertSame(5, $india['processing_days']);

        $uk = $this->quote(['nationality' => 'United Kingdom'])['quote']['visa'];
        $this->assertSame('visa_free', $uk['requirement']);
        $this->assertSame('ok', $uk['tone']);
    }

    public function test_invalid_choices_are_refused_with_reasons(): void
    {
        $cases = [
            [['days' => 7], 'days'],
            [['start_date' => now()->toDateString()], 'start_date'],
            [['start_date' => 'nonsense'], 'start_date'],
            [['traveler_type' => 'solo', 'adults' => 2], 'traveler_type'],
            [['traveler_type' => 'couple', 'adults' => 3], 'traveler_type'],
            [['traveler_type' => 'family', 'child_ages' => []], 'traveler_type'],
            [['traveler_type' => 'group', 'adults' => 3], 'traveler_type'],
            [['traveler_type' => 'group', 'adults' => 51], 'adults'],
            [['traveler_type' => 'family', 'child_ages' => [19]], 'child_ages'],
            [['guide_language' => 'klingon'], 'guide_language'],
            [['transport' => 'helicopter'], 'transport'],
        ];
        foreach ($cases as [$over, $field]) {
            $r = $this->quote($over);
            $this->assertFalse($r['ok'], json_encode($over));
            $this->assertArrayHasKey($field, $r['errors'], json_encode($over) . ' => ' . json_encode($r['errors']));
        }
    }

    public function test_margin_of_zero_charges_net(): void
    {
        $q = $this->quote([], ['margin_percent' => 0])['quote'];
        $this->assertSame($q['net_total'], $q['total']);
    }
}
