<?php

namespace Tests\Feature;

use App\Http\Controllers\UAEVisaController;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\NomodTransaction;
use App\Models\UAEVisaMaster;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * UAE visa add-on pricing: air-ticket = rate × visas, hotel tiered
 * (1–2:25, 3–4:50, 5–6:60, +10 per extra pair). Both scale with the total
 * number of applicants (adults + children + infants).
 */
class VisaAddonPricingTest extends TestCase
{
    use DatabaseTransactions;

    private function hotelFee(int $visas): float
    {
        $m = new \ReflectionMethod(UAEVisaController::class, 'hotelFeeForVisas');
        $m->setAccessible(true);
        return $m->invoke(new UAEVisaController(), $visas, 25.0);
    }

    public function test_hotel_tier_schedule(): void
    {
        $expected = [
            1 => 25, 2 => 25,        // tier 1
            3 => 50, 4 => 50,        // tier 2
            5 => 60, 6 => 60,        // tier 3
            7 => 70, 8 => 70,        // tier 4
            9 => 80, 10 => 80,       // tier 5
            25 => 160,               // tier 13: 60 + (13-3)*10
        ];
        foreach ($expected as $visas => $fee) {
            $this->assertSame((float) $fee, $this->hotelFee($visas), "hotel fee for {$visas} visas");
        }
        $this->assertSame(0.0, $this->hotelFee(0));
    }

    public function test_submit_charges_authoritative_total_including_infants(): void
    {
        Storage::fake('public');
        Http::fake([
            '*/v1/checkout' => Http::response(['id' => 'chk_v', 'url' => 'https://pay.test/chk_v', 'status' => 'created'], 200),
        ]);

        // UAEV_application has a FK to tbl_UAEVStatus(id); ensure status 1 exists.
        \Illuminate\Support\Facades\DB::table('tbl_UAEVStatus')->insertOrIgnore([
            'id' => 1, 'status_name' => 'Pending',
        ]);

        $master = UAEVisaMaster::create([
            'company_id' => 1, 'UAEVisaDuration' => '30 Days', 'UAEVPrice' => 100, 'isActive' => true,
        ]);

        // 2 adults + 1 child + 1 infant = 4 visas. Records are created for
        // adults+children (3) → 3 passport files. Fees scale on all 4.
        $payload = [
            'visaDuration'   => '30 Days',
            'price'          => '999999', // bogus posted total — must be IGNORED
            'visa_count'     => 2,
            'children_count' => 1,
            'infants_count'  => 1,
            'arrival_date'   => '2026-08-01',
            'departure_date' => '2026-08-10',
            'email'          => 'visa@example.com',
            'phone'          => '+971500000000',
            'hotel_booking'  => 1,
            'ticket_booking' => 1,
            'passport_copy'  => [
                UploadedFile::fake()->create('p0.pdf', 50, 'application/pdf'),
                UploadedFile::fake()->create('p1.pdf', 50, 'application/pdf'),
                UploadedFile::fake()->create('p2.pdf', 50, 'application/pdf'),
            ],
            'passport_photo' => [
                UploadedFile::fake()->create('ph0.jpg', 50, 'image/jpeg'),
                UploadedFile::fake()->create('ph1.jpg', 50, 'image/jpeg'),
                UploadedFile::fake()->create('ph2.jpg', 50, 'image/jpeg'),
            ],
        ];

        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('uaev.submit'), $payload)
            ->assertOk()
            ->assertJson(['success' => true]);

        // base 100×4 = 400 ; ticket 25×4 = 100 ; hotel tier(4)=50 → 550
        $txn = NomodTransaction::where('booking_type', 'visa')->latest('id')->first();
        $this->assertNotNull($txn);
        $this->assertEquals(550.00, (float) $txn->amount);
    }

    public function test_submit_charges_matrix_pricing(): void
    {
        Storage::fake('public');
        Http::fake([
            '*/v1/checkout' => Http::response(['id' => 'chk_m', 'url' => 'https://pay.test/chk_m', 'status' => 'created'], 200),
        ]);

        \Illuminate\Support\Facades\DB::table('tbl_UAEVStatus')->insertOrIgnore([
            'id' => 1, 'status_name' => 'Pending',
        ]);

        $emirateId = \Illuminate\Support\Facades\DB::table('tbl_emirates')->insertGetId([
            'emiratesName' => 'Dubai Test',
            'emiratesDescription' => 'Test Description',
            'country' => 'United Arab Emirates',
            'isActive' => 1,
            'createdBy' => 'Test',
            'createdDate' => now(),
        ]);

        $package = \App\Models\UAEVisaPackage::create([
            'emirates_id' => $emirateId,
            'name' => 'Dynamic Tourist Visa',
            'description' => 'Test Package',
            'isActive' => 1,
            'company_id' => 1,
        ]);

        \App\Models\UAEVisaPrice::create([
            'visa_package_id' => $package->id,
            'entry_type' => 'Single Entry',
            'duration' => '30 Days',
            'traveller_type' => 'Adult',
            'price' => 150.00,
            'isActive' => 1,
            'company_id' => 1,
        ]);
        \App\Models\UAEVisaPrice::create([
            'visa_package_id' => $package->id,
            'entry_type' => 'Single Entry',
            'duration' => '30 Days',
            'traveller_type' => 'Child',
            'price' => 120.00,
            'isActive' => 1,
            'company_id' => 1,
        ]);
        \App\Models\UAEVisaPrice::create([
            'visa_package_id' => $package->id,
            'entry_type' => 'Single Entry',
            'duration' => '30 Days',
            'traveller_type' => 'Infant',
            'price' => 50.00,
            'isActive' => 1,
            'company_id' => 1,
        ]);

        $payload = [
            'selected_emirate' => 'Dubai Test',
            'visa_package_id'  => $package->id,
            'entry_type'       => 'Single Entry',
            'visaDuration'     => '30 Days',
            'price'            => '999999',
            'visa_count'       => 2,
            'children_count'   => 1,
            'infants_count'    => 1,
            'arrival_date'     => '2026-08-01',
            'departure_date'   => '2026-08-10',
            'email'            => 'dynamic_visa@example.com',
            'phone'            => '+971500000000',
            'hotel_booking'    => 1,
            'ticket_booking'   => 1,
            'passport_copy'    => [
                UploadedFile::fake()->create('p0.pdf', 50, 'application/pdf'),
                UploadedFile::fake()->create('p1.pdf', 50, 'application/pdf'),
                UploadedFile::fake()->create('p2.pdf', 50, 'application/pdf'),
            ],
            'passport_photo'   => [
                UploadedFile::fake()->create('ph0.jpg', 50, 'image/jpeg'),
                UploadedFile::fake()->create('ph1.jpg', 50, 'image/jpeg'),
                UploadedFile::fake()->create('ph2.jpg', 50, 'image/jpeg'),
            ],
        ];

        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('uaev.submit'), $payload)
            ->assertOk()
            ->assertJson(['success' => true]);

        $txn = NomodTransaction::where('booking_type', 'visa')->latest('id')->first();
        $this->assertNotNull($txn);
        $this->assertEquals(620.00, (float) $txn->amount);

        $app = \App\Models\UAEVApplication::where('UAEV_email', 'dynamic_visa@example.com')->first();
        $this->assertNotNull($app);
        $this->assertEquals('Dubai Test', $app->UAEV_emirate);
        $this->assertEquals('Dynamic Tourist Visa', $app->UAEV_package_name);
        $this->assertEquals('Single Entry', $app->UAEV_visa_type);
        $this->assertEquals('Adult', $app->UAEV_traveller_type);
        $this->assertStringContainsString('hotel', $app->UAEV_addons);
        $this->assertStringContainsString('flight', $app->UAEV_addons);
    }
}
