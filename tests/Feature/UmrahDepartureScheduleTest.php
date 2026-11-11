<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\UmrahDeparture;
use App\Models\UmrahPackage;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Every Umrah package had run out of departures — the last one was 5 Aug 2026 —
 * so the booking page showed a calendar with nothing to pick. Upcoming weekly
 * departures are now kept open automatically.
 */
class UmrahDepartureScheduleTest extends TestCase
{
    use DatabaseTransactions;

    private function busPackageWithOnlyPastDepartures(): UmrahPackage
    {
        $package = UmrahPackage::create([
            'company_id' => 1, 'title' => 'Test Bus Umrah', 'category' => 'bus', 'price' => 1500, 'currency' => 'AED',
            'description' => 'Test', 'duration' => '10 Days', 'isActive' => 1, 'sortOrder' => 0,
            'createdBy' => 'test', 'createdDate' => now(),
        ]);
        UmrahDeparture::create([
            'umrah_package_id' => $package->id, 'departure_date' => '2026-08-05',
            'seats_total' => 40, 'seats_available' => 40, 'seats_booked' => 0, 'status' => 'available',
        ]);

        return $package;
    }

    private function upcoming(UmrahPackage $package)
    {
        return $package->departures()->where('departure_date', '>=', now()->toDateString())->orderBy('departure_date')->get();
    }

    public function test_the_page_offers_upcoming_wednesdays_when_all_departures_have_passed(): void
    {
        $package = $this->busPackageWithOnlyPastDepartures();

        $this->get(route('going-saudi.show', $package->id))->assertOk();

        $dates = $this->upcoming($package);
        $this->assertGreaterThanOrEqual(10, $dates->count());
        foreach ($dates as $dep) {
            $this->assertSame('3', $dep->departure_date->format('w'), 'bus departures must be Wednesdays');
            $this->assertTrue($dep->departure_date->gte(now()->addDays(5)->startOfDay()), 'must be 5+ days ahead');
            $this->assertSame(40, (int) $dep->seats_total, 'seat count copied from the last departure');
        }
    }

    public function test_it_does_not_duplicate_dates_or_reopen_a_closed_one(): void
    {
        $package = $this->busPackageWithOnlyPastDepartures();
        $first = now()->addDays(5)->startOfDay();
        while ($first->format('w') !== '3') {
            $first->addDay();
        }
        UmrahDeparture::create([
            'umrah_package_id' => $package->id, 'departure_date' => $first->toDateString(),
            'seats_total' => 40, 'seats_available' => 40, 'seats_booked' => 0, 'status' => 'closed',
        ]);

        $package->ensureUpcomingDepartures();
        $package->ensureUpcomingDepartures();

        $dates = $this->upcoming($package);
        $this->assertSame($dates->count(), $dates->pluck('departure_date')->map->toDateString()->unique()->count());
        $this->assertSame('closed', $dates->first(fn ($d) => $d->departure_date->toDateString() === $first->toDateString())->status);
    }

    public function test_a_generated_date_can_be_booked(): void
    {
        config(['nomod.api_key' => 'test_key']);
        Http::fake(['*/v1/checkout' => Http::response(['id' => 'chk_test', 'url' => 'https://pay.test/chk_test', 'status' => 'created'], 200)]);

        $package = $this->busPackageWithOnlyPastDepartures();
        $package->ensureUpcomingDepartures();
        $date = $this->upcoming($package)->first()->departure_date->toDateString();

        $this->withoutMiddleware(VerifyCsrfToken::class)->postJson(route('going-saudi.checkout', $package->id), [
            'departure_date' => $date, 'adults' => 1, 'children' => 0, 'infants' => 0, 'payment_gateway' => 'Card',
            'customer_name' => 'Amina Rahman', 'customer_email' => 'amina@example.com', 'customer_phone' => '+971501112233',
            'passenger_details' => [['name' => 'Amina Rahman', 'passport_copy' => 'uploads/passports/test.jpg']],
        ])->assertOk()->assertJson(['success' => true]);
    }
}
