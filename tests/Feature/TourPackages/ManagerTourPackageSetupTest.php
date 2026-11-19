<?php

namespace Tests\Feature\TourPackages;

use App\Models\Company;
use App\Models\DestinationVisaRule;
use App\Models\TourPackageAddon;
use App\Models\TourPackageBooking;
use App\Models\TourPackageDay;
use App\Models\TourPackageHotel;
use App\Models\TourPackageRate;
use App\Models\TravelPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * The manager screens that load supplier packages into the wizard.
 */
class ManagerTourPackageSetupTest extends TestCase
{
    use DatabaseTransactions, TourPackageFixture;

    /** @var array<int, User> one owner per tenant: switching users mid-session is logged out by AuthenticateSession */
    private array $owners = [];

    private function owner(Company $company): User
    {
        if (isset($this->owners[$company->id])) {
            return $this->owners[$company->id];
        }
        $this->flushSession(); // a different person = a different browser session
        $u = $this->owners[$company->id] = User::create(['name' => 'Owner', 'email' => 'own-' . Str::random(8) . '@t.test', 'password' => bcrypt('x')]);
        $u->forceFill(['role' => 'company_owner', 'company_id' => $company->id])->save();
        return $u;
    }

    private function url(Company $c, string $path): string
    {
        return 'http://' . $c->subdomain . '.gotrips.test' . $path;
    }

    public function test_creating_a_wizard_package_leads_to_setup(): void
    {
        $company = $this->tenant();
        $res = $this->actingAs($this->owner($company))->post($this->url($company, '/manager/packages'), [
            'title' => 'Vietnam North to South', 'country' => 'Vietnam', 'package_type' => 'wizard',
            'price' => 0, 'description' => 'Hanoi, Ha Long, Hoi An, Saigon', 'duration' => '5–6 days',
            'margin_percent' => 18, 'insurance_required' => 1, 'child_max_age' => 11, 'infant_max_age' => 1,
            'image' => UploadedFile::fake()->image('cover.jpg'),
        ]);

        $p = TravelPackage::withoutCompanyScope()->where('title', 'Vietnam North to South')->first();
        $res->assertRedirect(route('manager.packages.booking-setup', $p->id));
        $this->assertSame($company->id, $p->company_id);
        $this->assertSame(18.0, $p->margin_percent);
        $this->assertTrue($p->insurance_required);
        File::delete(public_path($p->image));
    }

    public function test_a_wizard_package_needs_no_from_price_but_others_do(): void
    {
        $company = $this->tenant();
        $base = fn (array $more) => array_merge(['country' => 'Georgia', 'description' => 'x', 'duration' => '6 days',
            'image' => UploadedFile::fake()->image('c.jpg')], $more);
        $this->actingAs($this->owner($company))->post($this->url($company, '/manager/packages'), $base(['title' => 'No Price Wizard', 'package_type' => 'wizard']))
            ->assertSessionHasNoErrors();
        $p = TravelPackage::withoutCompanyScope()->where('title', 'No Price Wizard')->first();
        $this->assertSame(0.0, (float) $p->price);
        File::delete(public_path($p->image));

        $this->actingAs($this->owner($company))->post($this->url($company, '/manager/packages'), $base(['title' => 'No Price Enquiry', 'package_type' => 'enquire']))
            ->assertSessionHasErrors('price');
    }

    public function test_setup_page_renders(): void
    {
        $company = $this->tenant();
        $p = $this->georgiaRooms($company);
        $this->actingAs($this->owner($company))->get($this->url($company, "/manager/packages/{$p->id}/booking-setup"))
            ->assertOk()->assertSee('Hotels and room prices')->assertSee('Tours, transfers and guide')
            ->assertSee('Price check')->assertSee('1 Double room')->assertSee('Tbilisi Inn')->assertSee('Open the booking page');
    }

    public function test_old_rate_packages_are_told_how_to_switch(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        $this->actingAs($this->owner($company))->get($this->url($company, "/manager/packages/{$p->id}/booking-setup"))
            ->assertOk()->assertSee('still priced from its old per-person rates');
    }

    public function test_hotels_and_room_prices_are_saved(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        $this->actingAs($this->owner($company))->put($this->url($company, "/manager/packages/{$p->id}/booking-setup/rooms"), ['hotels' => [
            3 => ['name' => 'Old Town Inn', 'location' => 'Old Tbilisi', 'single_net' => 250, 'double_net' => 300, 'triple_net' => '', 'extra_bed_net' => 60],
            4 => ['name' => ''], // empty name: not offered
            5 => ['name' => 'Stamba', 'double_net' => 900],
        ]])->assertSessionHasNoErrors();

        $h3 = TourPackageHotel::where('package_id', $p->id)->where('hotel_stars', 3)->first();
        $this->assertSame('Old Town Inn', $h3->name);
        $this->assertSame(300.0, $h3->double_net);
        $this->assertNull($h3->triple_net);
        $this->assertSame('14:00', $h3->check_in_time, 'The details saved earlier are kept.');
        $this->assertNull(TourPackageHotel::where('package_id', $p->id)->where('hotel_stars', 4)->first());
        $this->assertGreaterThan(0, TourPackageRate::where('package_id', $p->id)->count(), 'Old rates stay until tour prices are entered too.');
    }

    public function test_a_hotel_needs_a_double_room_price(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        $this->actingAs($this->owner($company))->put($this->url($company, "/manager/packages/{$p->id}/booking-setup/rooms"), ['hotels' => [
            3 => ['name' => 'Old Town Inn', 'single_net' => 250],
        ]])->assertSessionHasErrors('hotels');
    }

    public function test_tour_prices_are_saved_by_group_size_and_retire_the_old_rates(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        $owner = $this->owner($company);
        $this->actingAs($owner)->put($this->url($company, "/manager/packages/{$p->id}/booking-setup/rooms"), ['hotels' => [
            3 => ['name' => 'Old Town Inn', 'double_net' => 300],
        ]])->assertSessionHasNoErrors();
        $this->actingAs($owner)->put($this->url($company, "/manager/packages/{$p->id}/booking-setup/land"), [
            'land' => [
                ['days' => 6, 'g1' => 1800, 'g2' => 1100, 'g3' => '', 'g5' => 750],
                ['days' => '', 'g1' => ''], // empty row is ignored
            ],
            'child_land_percent' => 40, 'child_sharing_max_age' => 4,
        ])->assertSessionHasNoErrors();

        $p = TravelPackage::withoutCompanyScope()->with('landRates')->find($p->id);
        $this->assertSame([1, 2, 5], $p->landRates->pluck('min_group')->all());
        $this->assertSame(40.0, $p->child_land_percent);
        $this->assertSame(4, $p->child_sharing_max_age);
        $this->assertTrue($p->usesRoomPricing());
        $this->assertSame(0, TourPackageRate::where('package_id', $p->id)->count(), 'The old per-person rates are gone.');
    }

    public function test_tour_prices_refuse_duplicates_and_empty_rows(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        $owner = $this->owner($company);
        $this->actingAs($owner)->put($this->url($company, "/manager/packages/{$p->id}/booking-setup/land"), [
            'land' => [['days' => 6, 'g2' => 1000], ['days' => 6, 'g2' => 1100]], 'child_land_percent' => 50, 'child_sharing_max_age' => 5,
        ])->assertSessionHasErrors('land');
        $this->actingAs($owner)->put($this->url($company, "/manager/packages/{$p->id}/booking-setup/land"), [
            'land' => [['days' => 6]], 'child_land_percent' => 50, 'child_sharing_max_age' => 5,
        ])->assertSessionHasErrors('land');
        // Hotel only: a trip length with a 0 tour price is fine.
        $this->actingAs($owner)->put($this->url($company, "/manager/packages/{$p->id}/booking-setup/land"), [
            'land' => [['days' => 4, 'g1' => 0]], 'child_land_percent' => 50, 'child_sharing_max_age' => 5,
        ])->assertSessionHasNoErrors();
        $this->assertSame(0.0, \App\Models\TourPackageLandRate::where('package_id', $p->id)->where('days', 4)->value('adult_net'));
    }

    public function test_hotels_meals_and_policies_are_saved(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        $this->actingAs($this->owner($company))->put($this->url($company, "/manager/packages/{$p->id}/booking-setup/hotels"), ['hotels' => [
            3 => ['check_in_time' => '15:00', 'check_out_time' => '11:00',
                  'late_check_in_policy' => 'Free until midnight', 'late_check_out_policy' => '30 AED per hour', 'meal_plans' => ['breakfast']],
            5 => ['meal_plans' => ['breakfast', 'half_board', 'full_board']],
        ]])->assertSessionHasNoErrors();

        $h3 = TourPackageHotel::where('package_id', $p->id)->where('hotel_stars', 3)->first();
        $this->assertSame('Tbilisi Inn', $h3->name, 'The name is set with the room prices, not here.');
        $this->assertSame('15:00', $h3->check_in_time);
        $this->assertSame('30 AED per hour', $h3->late_check_out_policy);
        $this->assertSame(['breakfast', 'half_board', 'full_board'], TourPackageHotel::where('package_id', $p->id)->where('hotel_stars', 5)->first()->meal_plans);

        $this->actingAs($this->owner($company))->put($this->url($company, "/manager/packages/{$p->id}/booking-setup/hotels"), ['hotels' => [
            3 => ['check_in_time' => '3pm'],
        ]])->assertSessionHasErrors('hotels.3.check_in_time');
    }

    public function test_options_are_saved_with_meal_plan_codes(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        $this->actingAs($this->owner($company))->put($this->url($company, "/manager/packages/{$p->id}/booking-setup/addons"), ['addons' => [
            ['category' => 'guide_language', 'label' => 'English', 'net_price' => 0, 'charge_basis' => 'per_booking'],
            ['category' => 'guide_language', 'label' => 'Russian speaking', 'net_price' => 50, 'charge_basis' => 'per_day'],
            ['category' => 'meal_plan', 'code' => 'half_board', 'label' => 'Half board', 'net_price' => 20, 'charge_basis' => 'per_person_per_day'],
            ['category' => 'insurance', 'label' => 'Insurance', 'net_price' => 25, 'charge_basis' => 'per_person'],
        ]])->assertSessionHasNoErrors();

        $codes = TourPackageAddon::where('package_id', $p->id)->pluck('code', 'label');
        $this->assertSame('russian_speaking', $codes['Russian speaking']);
        $this->assertSame('half_board', $codes['Half board']);
        $this->assertSame(4, TourPackageAddon::where('package_id', $p->id)->count());
    }

    public function test_a_long_option_name_gets_a_code_that_fits_the_column(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        $this->actingAs($this->owner($company))->put($this->url($company, "/manager/packages/{$p->id}/booking-setup/addons"), ['addons' => [
            ['category' => 'insurance', 'label' => 'Travel insurance (medical and cancellation)', 'net_price' => 45, 'charge_basis' => 'per_person'],
        ]])->assertSessionHasNoErrors();

        $code = TourPackageAddon::where('package_id', $p->id)->value('code');
        $this->assertSame('travel_insurance_medical_and_cancellatio', $code);
        $this->assertLessThanOrEqual(40, strlen($code));
    }

    public function test_itinerary_days_with_pictures(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        $this->actingAs($this->owner($company))->post($this->url($company, "/manager/packages/{$p->id}/booking-setup/days"), ['days' => [
            ['day_number' => 1, 'title' => 'Arrive in Tbilisi', 'description' => 'Old town walk', 'image' => UploadedFile::fake()->image('d1.jpg')],
            ['day_number' => 2, 'title' => 'Kazbegi', 'duration_days' => 5],
            ['day_number' => 3, 'title' => ''], // ignored
        ]])->assertSessionHasNoErrors();

        $days = TourPackageDay::where('package_id', $p->id)->orderBy('day_number')->get();
        $this->assertCount(2, $days);
        $this->assertFileExists(public_path($days[0]->image_path));
        $this->assertStringStartsWith('assets/packages/' . $company->id . '/itinerary/', $days[0]->image_path);
        $this->assertSame(5, $days[1]->duration_days);

        // Saving without the first day removes it and its picture.
        $path = $days[0]->image_path;
        $this->actingAs($this->owner($company))->post($this->url($company, "/manager/packages/{$p->id}/booking-setup/days"), ['days' => [
            ['id' => $days[1]->id, 'day_number' => 2, 'title' => 'Kazbegi'],
        ]]);
        $this->assertFileDoesNotExist(public_path($path));
        $this->assertSame(1, TourPackageDay::where('package_id', $p->id)->count());
    }

    public function test_another_tenant_cannot_open_or_change_the_setup(): void
    {
        $a = $this->tenant();
        $b = $this->tenant();
        $p = $this->georgia($a);
        $bOwner = $this->owner($b);

        $this->actingAs($bOwner)->get($this->url($b, "/manager/packages/{$p->id}/booking-setup"))->assertNotFound();
        $this->actingAs($bOwner)->put($this->url($b, "/manager/packages/{$p->id}/booking-setup/rates"), ['rates' => [
            ['days' => 1, 'hotel_stars' => 3, 'adult_net' => 1]]])->assertNotFound();
        $this->assertSame(9, TourPackageRate::where('package_id', $p->id)->count());
    }

    public function test_bookings_list_and_detail_show_net_and_margin_to_the_manager(): void
    {
        $company = $this->tenant();
        $p = $this->georgia($company);
        app()->instance('current_company', $company);
        $q = \App\Services\TourPackages\TourPackageQuote::make($p, $this->input())['quote'];
        $b = TourPackageBooking::create([
            'company_id' => $company->id, 'package_id' => $p->id, 'order_id' => 'ORDTPK999999',
            'customer_name' => 'Priya Nair', 'customer_email' => 'p@example.com', 'customer_phone' => '+971501234567',
            'nationality' => 'India', 'start_date' => $q['start_date'], 'end_date' => $q['end_date'], 'days' => 5,
            'hotel_stars' => 4, 'traveler_type' => 'couple', 'adults' => 2, 'selections' => $q['selections'],
            'price_breakdown' => ['lines' => $q['net_lines'], 'hotel' => $q['hotel'], 'season' => null, 'margin_percent' => 10],
            'net_total' => $q['net_total'], 'total' => $q['total'], 'payment_status' => 'paid',
        ]);
        app()->forgetInstance('current_company');
        $owner = $this->owner($company);

        $this->actingAs($owner)->get($this->url($company, '/manager/tour-bookings'))->assertOk()
            ->assertSee('ORDTPK999999')->assertSee('Priya Nair')->assertSee('3,696.00');
        $this->actingAs($owner)->get($this->url($company, "/manager/tour-bookings/{$b->id}"))->assertOk()
            ->assertSee('3,360.00')->assertSee('336.00');

        $this->actingAs($owner)->post($this->url($company, "/manager/tour-bookings/{$b->id}/status"), ['status' => 'confirmed']);
        $this->assertSame('confirmed', $b->fresh()->status);

        $other = $this->tenant();
        $this->actingAs($this->owner($other))->get($this->url($other, "/manager/tour-bookings/{$b->id}"))->assertNotFound();
    }

    public function test_visa_rules_crud(): void
    {
        $company = $this->tenant();
        $owner = $this->owner($company);
        $this->actingAs($owner)->post($this->url($company, '/manager/visa-rules'), [
            'destination' => 'Vietnam', 'nationality' => 'India', 'requirement' => 'evisa', 'processing_days' => 3, 'fee' => 95,
            'documents' => 'Passport photo, passport scan',
        ])->assertSessionHasNoErrors();
        $this->actingAs($owner)->post($this->url($company, '/manager/visa-rules'), [
            'destination' => 'Vietnam', 'nationality' => 'India', 'requirement' => 'visa_required',
        ])->assertSessionHasNoErrors();

        $rules = DestinationVisaRule::withoutCompanyScope()->where('company_id', $company->id)->where('destination', 'Vietnam')->get();
        $this->assertCount(1, $rules, 'Same destination + passport updates the rule.');
        $this->assertSame('visa_required', $rules[0]->requirement);

        $this->actingAs($owner)->get($this->url($company, '/manager/visa-rules'))->assertOk()->assertSee('Vietnam');
        $this->actingAs($owner)->post($this->url($company, '/manager/visa-rules'), ['destination' => 'Narnia', 'nationality' => 'India', 'requirement' => 'evisa'])
            ->assertSessionHasErrors('destination');
        $this->actingAs($owner)->delete($this->url($company, '/manager/visa-rules/' . $rules[0]->id));
        $this->assertSame(0, DestinationVisaRule::withoutCompanyScope()->where('company_id', $company->id)->count());
    }

    public function test_sidebar_links(): void
    {
        $company = $this->tenant();
        $this->actingAs($this->owner($company))->get($this->url($company, '/manager/packages'))->assertOk()
            ->assertSee('Tour Bookings')->assertSee('Visa Rules');
    }
}
