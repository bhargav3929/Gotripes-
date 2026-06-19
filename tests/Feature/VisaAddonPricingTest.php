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
                UploadedFile::fake()->image('ph0.jpg'),
                UploadedFile::fake()->image('ph1.jpg'),
                UploadedFile::fake()->image('ph2.jpg'),
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
}
