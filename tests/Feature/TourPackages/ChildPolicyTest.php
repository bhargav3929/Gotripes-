<?php

namespace Tests\Feature\TourPackages;

use App\Models\TourPackageBooking;
use App\Models\TourPackageHotel;
use App\Models\TourPackageLandRate;
use App\Services\TourPackages\TourPackageQuote;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Children in a hotel with no triple or extra-bed price (a cruise cabin): the
 * supplier's tiers are free up to 5, 50% from 6 to 7, the adult price from 8.
 * Cabin USD 249 single / 338 double, in AED; everything is in the cabin price.
 */
class ChildPolicyTest extends TestCase
{
    use DatabaseTransactions, TourPackageFixture;

    private const SINGLE = 914.45;
    private const DOUBLE = 1241.31;

    private function hotelLine(array $over): array
    {
        $p = $this->georgiaRooms($this->tenant());
        TourPackageHotel::where('package_id', $p->id)->where('hotel_stars', 3)
            ->update(['single_net' => self::SINGLE, 'double_net' => self::DOUBLE, 'triple_net' => null, 'extra_bed_net' => null]);
        TourPackageLandRate::where('package_id', $p->id)->delete();
        TourPackageLandRate::create(['package_id' => $p->id, 'days' => 3, 'min_group' => 1, 'adult_net' => 0]);
        $p->forceFill(['child_land_percent' => 50, 'child_sharing_max_age' => 5, 'infant_max_age' => 5, 'child_max_age' => 7])->save();

        $res = TourPackageQuote::make($p->fresh(), $this->input(array_merge(['days' => 3, 'hotel_stars' => 3, 'adults' => 1], $over)));
        $this->assertTrue($res['ok'], json_encode($res['errors']));
        $q = $res['quote'];
        $line = collect($q['net_lines'])->firstWhere('key', 'hotel');
        return ['rooms' => $q['rooms'], 'net' => $line['net'], 'q' => $q];
    }

    public function test_an_adult_alone_pays_the_single_cabin(): void
    {
        $r = $this->hotelLine([]);
        $this->assertSame('1 Single room', $r['rooms']);
        $this->assertEqualsWithDelta(2 * self::SINGLE, $r['net'], 0.01);
    }

    public function test_an_adult_with_a_six_year_old_pays_half_a_double_plus_half_of_that_for_the_child(): void
    {
        $r = $this->hotelLine(['child_ages' => [6]]);
        $this->assertSame('1 Double room, 1 child sharing', $r['rooms']);
        // 169 + 84.50 USD a night, not the whole 338 double
        $this->assertEqualsWithDelta(2 * (self::DOUBLE / 2 * 1.5), $r['net'], 0.05);
        $this->assertLessThan(2 * self::DOUBLE, $r['net']);
    }

    public function test_two_adults_and_a_six_year_old_share_one_cabin(): void
    {
        $r = $this->hotelLine(['adults' => 2, 'child_ages' => [6]]);
        $this->assertSame('1 Double room, 1 child sharing', $r['rooms']);
        $this->assertEqualsWithDelta(2 * (self::DOUBLE + self::DOUBLE / 4), $r['net'], 0.05);
    }

    public function test_children_up_to_the_free_age_cost_nothing(): void
    {
        $r = $this->hotelLine(['adults' => 2, 'child_ages' => [4]]);
        $this->assertSame('1 Double room', $r['rooms']);
        $this->assertEqualsWithDelta(2 * self::DOUBLE, $r['net'], 0.01);
    }

    public function test_from_the_adult_age_a_child_is_priced_as_an_adult(): void
    {
        $r = $this->hotelLine(['adults' => 1, 'child_ages' => [8]]);
        $this->assertSame('1 Double room', $r['rooms']);
        $this->assertSame(2, $r['q']['adults']);
    }

    public function test_the_booking_still_counts_an_adult_priced_child_as_a_child(): void
    {
        $b = new TourPackageBooking(['adults' => 2, 'children' => 0, 'infants' => 0, 'child_ages' => [8]]);
        $this->assertSame('1 adult, 1 child', $b->travellerSummary());
        $b = new TourPackageBooking(['adults' => 2, 'children' => 1, 'infants' => 1, 'child_ages' => [6, 1]]);
        $this->assertSame('2 adults, 1 child, 1 infant', $b->travellerSummary());
    }
}
