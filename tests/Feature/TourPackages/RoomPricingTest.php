<?php

namespace Tests\Feature\TourPackages;

use App\Services\TourPackages\TourPackageQuote;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Room and group pricing (see georgiaRooms(): 6 days = 5 nights, margin 10%).
 * Hotel = rooms the party needs × nights; land = per adult by group size,
 * children at 50%, infants free; young children share the parents' bed.
 */
class RoomPricingTest extends TestCase
{
    use DatabaseTransactions, TourPackageFixture;

    private function quote(array $over): array
    {
        $p = $this->georgiaRooms($this->tenant());
        $res = TourPackageQuote::make($p, $this->input(array_merge(['days' => 6, 'hotel_stars' => 3], $over)));
        $this->assertTrue($res['ok'], json_encode($res['errors']));
        return $res['quote'];
    }

    public function test_couple_gets_a_double_room(): void
    {
        $q = $this->quote(['traveler_type' => 'couple', 'adults' => 2]);
        $this->assertSame('1 Double room', $q['rooms']);
        // hotel 300 x 5 = 1500, land 2 x 1100 = 2200, +10%
        $this->assertSame(4070.0, $q['total']);
    }

    public function test_solo_gets_a_single_room_and_the_one_person_tour_price(): void
    {
        $q = $this->quote(['traveler_type' => 'solo', 'adults' => 1]);
        $this->assertSame('1 Single room', $q['rooms']);
        $this->assertSame(round((250 * 5 + 1800) * 1.1, 2), $q['total']);
    }

    public function test_three_adults_share_a_triple_and_pay_the_smaller_group_price(): void
    {
        $q = $this->quote(['traveler_type' => 'group', 'adults' => 5]);
        $this->assertSame('1 Double room + 1 Triple room', $q['rooms']);
        $this->assertSame(round(((300 + 390) * 5 + 5 * 750) * 1.1, 2), $q['total']);
    }

    public function test_four_adults_get_two_doubles(): void
    {
        $p = $this->georgiaRooms($this->tenant());
        $q = TourPackageQuote::make($p, $this->input(['days' => 6, 'hotel_stars' => 3, 'traveler_type' => 'group', 'adults' => 4]), false)['quote'];
        $this->assertSame('2 Double rooms', $q['rooms']);
        $this->assertSame(round((600 * 5 + 4 * 900) * 1.1, 2), $q['total']);
    }

    public function test_a_hotel_without_triples_or_singles_uses_another_double(): void
    {
        $p = $this->georgiaRooms($this->tenant());
        $q = TourPackageQuote::make($p, $this->input(['days' => 6, 'hotel_stars' => 4, 'adults' => 3]), false)['quote'];
        $this->assertSame('2 Double rooms', $q['rooms']);
    }

    public function test_older_child_needs_a_bed_and_pays_half_the_tour(): void
    {
        $q = $this->quote(['traveler_type' => 'family', 'adults' => 2, 'child_ages' => [8]]);
        $this->assertSame('1 Triple room', $q['rooms']);
        // hotel 390 x 5, land 2 x 900 + 450 (group of 3)
        $this->assertSame(round((390 * 5 + 2 * 900 + 450) * 1.1, 2), $q['total']);
    }

    public function test_young_child_shares_the_parents_bed(): void
    {
        $q = $this->quote(['traveler_type' => 'family', 'adults' => 2, 'child_ages' => [4]]);
        $this->assertSame('1 Double room', $q['rooms']);
        $this->assertSame(round((300 * 5 + 2 * 900 + 450) * 1.1, 2), $q['total']);
    }

    public function test_infant_is_free(): void
    {
        $q = $this->quote(['traveler_type' => 'family', 'adults' => 2, 'child_ages' => [1]]);
        $this->assertSame(4070.0, $q['total']);
    }

    public function test_lines_add_up_and_never_show_net(): void
    {
        $q = $this->quote(['traveler_type' => 'family', 'adults' => 2, 'child_ages' => [8]]);
        $this->assertEqualsWithDelta($q['total'], array_sum(array_column($q['lines'], 'amount')), 0.001);
        foreach ($q['lines'] as $l) {
            $this->assertArrayNotHasKey('net', $l);
        }
    }

    public function test_only_listed_trip_lengths_and_hotels_can_be_booked(): void
    {
        $p = $this->georgiaRooms($this->tenant());
        $this->assertArrayHasKey('days', TourPackageQuote::make($p, $this->input(['days' => 5, 'hotel_stars' => 3]))['errors']);
        $this->assertArrayHasKey('hotel_stars', TourPackageQuote::make($p, $this->input(['days' => 6, 'hotel_stars' => 5]))['errors']);
    }

    public function test_from_price_is_per_adult_two_sharing(): void
    {
        $p = $this->georgiaRooms($this->tenant());
        $this->assertSame(round((300 * 5 / 2 + 1100) * 1.1, 2), $p->wizardFromPrice());
        $this->assertTrue($p->isBookableOnline());
    }

    public function test_wizard_page_offers_every_priced_hotel_for_every_trip_length(): void
    {
        $company = $this->tenant();
        $p = $this->georgiaRooms($company);
        $html = $this->get('http://' . $company->subdomain . '.gotrips.test/tour-packages/' . $p->id . '/book')->assertOk()->getContent();
        $this->assertStringContainsString('"packs":{"3":{"days":[6],"from":2035},"4":{"days":[6],"from":2585}}', $html);
        $this->assertStringNotContainsString('double_net', $html);
    }
}
