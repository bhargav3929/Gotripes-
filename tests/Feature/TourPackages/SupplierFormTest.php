<?php

namespace Tests\Feature\TourPackages;

use App\Models\Company;
use App\Models\SupplierInvite;
use App\Models\SupplierSubmission;
use App\Models\TourPackageAddon;
use App\Models\TourPackageHotel;
use App\Models\TourPackageLandRate;
use App\Models\TravelPackage;
use App\Models\User;
use App\Services\TourPackages\TourPackageQuote;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Supplier rate form: code gate, sending rates, and staff applying them to a package.
 */
class SupplierFormTest extends TestCase
{
    use DatabaseTransactions, TourPackageFixture;

    private array $cleanup = [];

    protected function tearDown(): void
    {
        foreach ($this->cleanup as $path) {
            File::deleteDirectory($path);
            File::delete($path);
        }
        parent::tearDown();
    }

    private function url(Company $c, string $path): string
    {
        return 'http://' . $c->subdomain . '.gotrips.test' . $path;
    }

    private function owner(Company $company): User
    {
        $u = User::create(['name' => 'Owner', 'email' => 'own-' . Str::random(8) . '@t.test', 'password' => bcrypt('x')]);
        $u->forceFill(['role' => 'company_owner', 'company_id' => $company->id])->save();
        return $u;
    }

    private function invite(Company $company, array $attrs = []): SupplierInvite
    {
        $inv = SupplierInvite::withoutCompanyScope()->create($attrs + [
            'company_id' => $company->id, 'code' => SupplierInvite::newCode(), 'supplier_name' => 'Caucasus DMC', 'country' => 'Georgia',
        ]);
        $this->cleanup[] = storage_path('app/supplier-uploads/' . $company->id);
        return $inv;
    }

    private function form(array $over = []): array
    {
        return array_replace_recursive([
            'company' => ['name' => 'Caucasus DMC', 'contact' => 'Nino', 'email' => 'nino@dmc.test', 'phone' => '+995 555 000'],
            'package' => ['name' => 'Georgia Classic', 'country' => 'Georgia', 'places' => 'Tbilisi, Kazbegi', 'description' => 'Mountains and wine.', 'included' => "Airport pickup\nCar"],
            'currency' => 'USD',
            'hotels' => [
                3 => ['name' => 'Tbilisi Inn', 'location' => 'Old town', 'single' => 40, 'double' => 50, 'triple' => 70, 'meals' => ['breakfast'], 'check_in' => '14:00', 'check_out' => '12:00'],
                4 => ['name' => '', 'double' => ''],
            ],
            'tours' => [['days' => 4, 'g1' => 200], ['days' => '', 'g1' => '']],
            'child_percent' => 50, 'child_free_age' => 5,
            'extras' => [['category' => 'guide_language', 'name' => 'Arabic-speaking guide', 'price' => 30, 'basis' => 'per_day'], ['name' => '']],
            'days' => [['day' => 1, 'title' => 'Arrive in Tbilisi', 'description' => 'Pickup'], ['title' => '']],
            'seasons' => [['name' => 'New Year', 'from' => '2026-12-28', 'to' => '2027-01-05', 'percent' => 20]],
            'photos' => [UploadedFile::fake()->image('room.jpg', 800, 600)],
        ], $over);
    }

    public function test_only_a_live_code_opens_the_form(): void
    {
        $company = $this->tenant();
        $inv = $this->invite($company);
        $off = $this->invite($company, ['revoked_at' => now()]);

        $this->get($this->url($company, '/suppliers/form'))->assertRedirect(route('suppliers.start'));
        $this->post($this->url($company, '/suppliers'), ['code' => 'ZZZZ-ZZZZ'])->assertSessionHasErrors('code');
        $this->post($this->url($company, '/suppliers'), ['code' => $off->display_code])->assertSessionHasErrors('code');

        // Another tenant's code does not work here.
        $other = $this->invite($this->tenant());
        $this->post($this->url($company, '/suppliers'), ['code' => $other->display_code])->assertSessionHasErrors('code');

        $this->post($this->url($company, '/suppliers'), ['code' => strtolower(substr($inv->code, 0, 4) . ' ' . substr($inv->code, 4))])
            ->assertRedirect(route('suppliers.form'));
        $this->get($this->url($company, '/suppliers/form'))->assertOk()->assertSee('Your rates')->assertSee('Caucasus DMC');
    }

    public function test_supplier_sends_rates_and_can_update_them_later(): void
    {
        Mail::fake();
        $company = $this->tenant();
        $inv = $this->invite($company);
        $this->post($this->url($company, '/suppliers'), ['code' => $inv->code]);

        $this->post($this->url($company, '/suppliers/form'), $this->form())->assertRedirect(route('suppliers.thanks'));

        $s = SupplierSubmission::withoutCompanyScope()->where('supplier_invite_id', $inv->id)->sole();
        $this->assertSame($company->id, $s->company_id);
        $this->assertEquals(['name' => 'Tbilisi Inn', 'location' => 'Old town', 'single' => 40.0, 'double' => 50.0, 'triple' => 70.0, 'extra_bed' => null,
            'check_in' => '14:00', 'check_out' => '12:00', 'meals' => ['breakfast'], 'late_policy' => null], $s->hotels()[3]);
        $this->assertCount(1, $s->get('tours'));
        $this->assertCount(1, $s->get('extras'));
        $this->assertCount(1, $s->photos);
        $this->assertFileExists(storage_path('app/' . $s->photos[0]['path']));

        // Coming back: the form opens with what they sent, and the photo can be kept.
        $this->get($this->url($company, '/suppliers/form'))->assertOk()->assertSee('Update your rates')->assertSee('Tbilisi Inn');
        $this->get($this->url($company, '/suppliers/photo/0'))->assertOk();

        $this->post($this->url($company, '/suppliers/form'), $this->form(['hotels' => [3 => ['double' => 55]], 'keep_photos' => [0], 'photos' => []]))
            ->assertRedirect(route('suppliers.thanks'));
        $latest = SupplierSubmission::withoutCompanyScope()->where('supplier_invite_id', $inv->id)->latest('id')->first();
        $this->assertEquals(55, $latest->hotels()[3]['double']);
        $this->assertSame($s->photos[0]['path'], $latest->photos[0]['path']);
    }

    public function test_a_named_hotel_needs_a_double_price_and_something_must_be_priced(): void
    {
        $company = $this->tenant();
        $inv = $this->invite($company);
        $this->post($this->url($company, '/suppliers'), ['code' => $inv->code]);

        $this->post($this->url($company, '/suppliers/form'), $this->form(['hotels' => [3 => ['double' => '']]]))
            ->assertSessionHasErrors('hotels.3.double');
        $this->post($this->url($company, '/suppliers/form'), $this->form(['hotels' => [3 => ['name' => '']], 'tours' => [['days' => '', 'g1' => '']]]))
            ->assertSessionHasErrors('hotels');
        $this->post($this->url($company, '/suppliers/form'), $this->form(['tours' => [['days' => 4, 'g1' => 200], ['days' => 4, 'g1' => 300]]]))
            ->assertSessionHasErrors('tours.1.days');
        // A price with no trip length points at that row's Days box
        $this->post($this->url($company, '/suppliers/form'), $this->form(['tours' => [['days' => 4, 'g1' => 200], ['days' => '', 'g1' => 300]]]))
            ->assertSessionHasErrors('tours.1.days');
        $this->assertSame(0, SupplierSubmission::withoutCompanyScope()->where('supplier_invite_id', $inv->id)->count());
    }

    public function test_staff_create_a_code_and_apply_rates_to_a_new_package(): void
    {
        Mail::fake();
        $company = $this->tenant();
        $owner = $this->owner($company);

        $this->actingAs($owner)->post($this->url($company, '/manager/supplier-forms/codes'), ['supplier_name' => 'Caucasus DMC', 'country' => 'Georgia'])
            ->assertSessionHasNoErrors();
        $inv = SupplierInvite::withoutCompanyScope()->where('company_id', $company->id)->sole();
        $this->assertMatchesRegularExpression('/^[2-9A-HJKMNP-Z]{8}$/', $inv->code);
        $this->cleanup[] = storage_path('app/supplier-uploads/' . $company->id);
        $this->get($this->url($company, '/manager/supplier-forms'))->assertOk()->assertSee($inv->display_code);

        $this->post($this->url($company, '/suppliers'), ['code' => $inv->code]);
        $this->post($this->url($company, '/suppliers/form'), $this->form());
        $s = SupplierSubmission::withoutCompanyScope()->where('supplier_invite_id', $inv->id)->sole();

        $this->get($this->url($company, '/manager/supplier-forms/' . $s->id))->assertOk()->assertSee('Tbilisi Inn')->assertSee('Arabic-speaking guide');
        $this->get($this->url($company, '/manager/supplier-forms/' . $s->id . '/photos/0'))->assertOk();
        $this->get($this->url($company, '/manager/supplier-forms/' . $s->id . '/photos.zip'))->assertOk();

        $res = $this->post($this->url($company, '/manager/supplier-forms/' . $s->id . '/apply'), [
            'target' => 'new', 'rate' => 3.6725, 'sections' => ['hotels', 'tours', 'extras', 'days', 'seasons'],
        ]);
        $p = TravelPackage::withoutCompanyScope()->where('company_id', $company->id)->where('title', 'Georgia Classic')->sole();
        $this->cleanup[] = public_path($p->image);
        $res->assertRedirect(route('manager.packages.booking-setup', $p->id));

        $this->assertSame('wizard', $p->package_type);
        $this->assertSame('4 days', $p->duration);
        $this->assertFileExists(public_path($p->image));
        $hotel = TourPackageHotel::where('package_id', $p->id)->sole();
        $this->assertSame(183.63, $hotel->double_net); // 50 USD
        $this->assertSame(146.9, $hotel->single_net);
        $this->assertSame(734.5, TourPackageLandRate::where('package_id', $p->id)->sole()->adult_net); // 200 USD
        $guides = TourPackageAddon::where('package_id', $p->id)->where('category', 'guide_language')->orderBy('sort_order')->get();
        $this->assertSame([0.0, 110.18], $guides->pluck('net_price')->all()); // the guide in the tour price, then the paid upgrade
        $this->assertSame(1, $p->itineraryDays()->count());
        $this->assertSame(1, $p->seasons()->count());
        $this->assertSame('applied', $s->fresh()->status);
        $this->assertSame($p->id, $inv->fresh()->package_id);

        // Customers can now be priced from it.
        $p = $p->fresh();
        $this->assertTrue($p->hasPricing());
        $q = TourPackageQuote::make($p, ['start_date' => now()->addDays(40)->toDateString(), 'days' => 4, 'hotel_stars' => 3, 'adults' => 2]);
        $this->assertNotNull($q['quote'] ?? null);
        $this->assertEquals(0, collect($q['quote']['lines'])->where('key', 'guide_language')->sum('amount')); // upgrade not charged unless picked
    }

    public function test_cover_photo_child_ages_preview_and_package_text(): void
    {
        Mail::fake();
        $company = $this->tenant();
        $owner = $this->owner($company);
        $inv = $this->invite($company);
        $this->post($this->url($company, '/suppliers'), ['code' => $inv->code]);

        // The supplier picks the second new photo as the cover, and gives three child bands
        $this->post($this->url($company, '/suppliers/form'), $this->form([
            'photos' => [UploadedFile::fake()->image('deck.jpg', 800, 600), UploadedFile::fake()->image('bay.jpg', 800, 600)],
            'cover' => 'new:1', 'child_free_age' => 5, 'child_adult_age' => 8,
            'package' => ['excluded' => "Flights\n- Tips"],
        ]))->assertRedirect();
        $s = SupplierSubmission::withoutCompanyScope()->where('supplier_invite_id', $inv->id)->sole();
        $this->assertSame(['bay.jpg', 'deck.jpg'], array_column($s->photos, 'name'));
        $this->assertSame(8, $s->get('child_adult_age'));

        $this->get($this->url($company, '/suppliers/thanks'))->assertOk()->assertSee('Preview as a traveller');
        $this->get($this->url($company, '/suppliers/preview'))->assertOk()
            ->assertSee('Georgia Classic')->assertSee('Airport pickup')->assertSee('Tips')->assertSee('From age 8, the adult price.');

        // Keeping both photos later and choosing the old second one moves it first again
        $again = $this->form(['keep_photos' => [0, 1], 'cover' => 'old:1']);
        unset($again['photos']);
        $this->post($this->url($company, '/suppliers/form'), $again)->assertSessionHasNoErrors();
        $latest = SupplierSubmission::withoutCompanyScope()->where('supplier_invite_id', $inv->id)->latest('id')->first();
        $this->assertSame(['deck.jpg', 'bay.jpg'], array_column($latest->photos, 'name'));

        // Ages that overlap are refused
        $this->post($this->url($company, '/suppliers/form'), $this->form(['child_free_age' => 6, 'child_adult_age' => 7]))
            ->assertSessionHasErrors('child_adult_age');

        $this->actingAs($owner)->post($this->url($company, '/manager/supplier-forms/' . $s->id . '/apply'), [
            'target' => 'new', 'rate' => 3.6725, 'sections' => ['tours'],
        ]);
        $p = TravelPackage::withoutCompanyScope()->where('company_id', $company->id)->where('title', 'Georgia Classic')->sole();
        $this->cleanup[] = public_path($p->image);
        foreach ($p->images as $img) {
            $this->cleanup[] = public_path($img->image_path);
        }
        $this->assertSame(['Airport pickup', 'Car'], $p->includedList());
        $this->assertSame(['Flights', 'Tips'], $p->excludedList());
        $this->assertStringNotContainsString('Included:', $p->description);
        $this->assertSame('<p>Mountains and wine.</p><p>Places: Tbilisi, Kazbegi</p>', $p->description);
        $this->assertSame(7, $p->child_max_age);
        $this->assertSame(5, $p->infant_max_age);
        $this->assertSame(5, $p->child_sharing_max_age);
        $this->assertSame(1, $p->images()->count());

        // Package text and photos can be applied again to the package later
        $p->update(['included' => 'Old list', 'description' => '<p>Ours</p>']);
        $this->post($this->url($company, '/manager/supplier-forms/' . $latest->id . '/apply'), [
            'target' => $p->id, 'rate' => 3.6725, 'sections' => ['details', 'photos'],
        ])->assertSessionHasNoErrors();
        $p = $p->fresh();
        $this->cleanup[] = public_path($p->image);
        foreach ($p->images as $img) {
            $this->cleanup[] = public_path($img->image_path);
        }
        $this->assertSame(['Airport pickup', 'Car'], $p->includedList());
        $this->assertFileExists(public_path($p->image));
        $this->get($this->url($company, '/tour-packages/' . $p->id))->assertOk()->assertSee('What the price covers')->assertSee('Airport pickup');
    }

    public function test_applying_to_an_existing_package_keeps_our_insurance_and_other_tenants_are_kept_out(): void
    {
        Mail::fake();
        $company = $this->tenant();
        $package = $this->georgia($company);
        TourPackageAddon::create(['package_id' => $package->id, 'category' => 'insurance', 'code' => 'own_cover', 'label' => 'Our cover', 'net_price' => 20, 'charge_basis' => 'per_person']);
        $inv = $this->invite($company, ['package_id' => $package->id]);
        $this->post($this->url($company, '/suppliers'), ['code' => $inv->code]);
        $this->post($this->url($company, '/suppliers/form'), $this->form(['currency' => 'AED']));
        $s = SupplierSubmission::withoutCompanyScope()->where('supplier_invite_id', $inv->id)->sole();

        $other = $this->tenant();
        $this->actingAs($this->owner($other))->get($this->url($other, '/manager/supplier-forms/' . $s->id))->assertNotFound();

        $this->flushSession();
        $this->actingAs($this->owner($company))->post($this->url($company, '/manager/supplier-forms/' . $s->id . '/apply'), [
            'target' => $package->id, 'rate' => 1, 'sections' => ['hotels', 'tours', 'extras'],
        ])->assertSessionHasNoErrors()->assertRedirect(route('manager.packages.booking-setup', $package->id));

        $this->assertSame(50.0, TourPackageHotel::where('package_id', $package->id)->sole()->double_net);
        $cats = TourPackageAddon::where('package_id', $package->id)->pluck('category')->unique()->values()->all();
        $this->assertEqualsCanonicalizing(['insurance', 'guide_language'], $cats);
        $this->assertTrue(TourPackageAddon::where('package_id', $package->id)->where('code', 'own_cover')->exists());
        $this->assertSame(0, $package->rates()->count()); // old per-person rates retired
        $this->assertTrue($package->fresh()->usesRoomPricing());
    }
}
