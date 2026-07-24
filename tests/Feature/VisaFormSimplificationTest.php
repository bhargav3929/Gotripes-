<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\SaudiVisaApplication;
use App\Models\SaudiVisaType;
use App\Models\UAEVApplication;
use App\Models\UAEVisaMaster;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The UAE and Saudi visa forms no longer ask customers to type passport details.
 * These tests pin the new contract: a booking goes through on contact details and
 * document uploads alone, and passport columns are left for OCR to fill rather
 * than being validated or stuffed with placeholder names.
 */
class VisaFormSimplificationTest extends TestCase
{
    use DatabaseTransactions;

    private function fakeCheckout(): void
    {
        Http::fake([
            '*/v1/checkout' => Http::response(
                ['id' => 'chk_simple', 'url' => 'https://pay.test/chk_simple', 'status' => 'created'],
                200
            ),
        ]);
    }

    // ---------------------------------------------------------------- UAE ----

    public function test_uae_form_asks_for_no_passport_details(): void
    {
        $html = $this->get('/uaevisa')->assertOk()->getContent();

        // The applicant cards are built in JS, so assert against the template
        // source: the passport fields must only ever be rendered as hidden inputs.
        foreach (['first_name[]', 'last_name[]', 'passport_number[]', 'gender[]', 'dob[]'] as $field) {
            $this->assertStringNotContainsString(
                'type="text" name="' . $field . '"',
                $html,
                "{$field} must not be a typed-in field"
            );
        }

        $this->assertStringContainsString('type="hidden" name="passport_number[]"', $html);
        $this->assertStringNotContainsString('Passport Number</label>', $html);
        $this->assertStringNotContainsString('Date of Birth</label>', $html);
    }

    public function test_uae_submission_succeeds_without_any_passport_details(): void
    {
        Storage::fake('public');
        $this->fakeCheckout();

        DB::table('tbl_UAEVStatus')->insertOrIgnore(['id' => 1, 'status_name' => 'Pending']);

        UAEVisaMaster::create([
            'company_id' => 1, 'UAEVisaDuration' => '30 Days', 'UAEVPrice' => 100, 'isActive' => true,
        ]);

        // Exactly what the simplified form posts when the browser scan read
        // nothing at all: contact details, quantities and documents.
        $payload = [
            'visaDuration'   => '30 Days',
            'price'          => '0',
            'visa_count'     => 2,
            'applicant_name' => 'Aisha Rahman',
            'email'          => 'simplified_uae@example.com',
            'phone'          => '+971500000001',
            'passport_copy'  => [
                UploadedFile::fake()->create('p0.pdf', 50, 'application/pdf'),
                UploadedFile::fake()->create('p1.pdf', 50, 'application/pdf'),
            ],
            'passport_photo' => [
                UploadedFile::fake()->create('ph0.jpg', 50, 'image/jpeg'),
                UploadedFile::fake()->create('ph1.jpg', 50, 'image/jpeg'),
            ],
        ];

        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('uaev.submit'), $payload)
            ->assertOk()
            ->assertJson(['success' => true]);

        $apps = UAEVApplication::where('UAEV_email', 'simplified_uae@example.com')
            ->orderBy('id')
            ->get();

        $this->assertCount(2, $apps);

        // The lead traveller is named from the one field the customer still types.
        $this->assertEquals('Aisha', $apps[0]->UAEV_first_name);
        $this->assertEquals('Rahman', $apps[0]->UAEV_last_name);

        // The second traveller is left blank for OCR rather than being given a
        // fabricated name that an agent cannot tell apart from a real one.
        $this->assertSame('', (string) $apps[1]->UAEV_first_name);
        $this->assertSame('', (string) $apps[1]->UAEV_last_name);
        $this->assertNull($apps[1]->UAEV_passport_number);
        $this->assertNull($apps[1]->UAEV_dob);
        $this->assertNull($apps[1]->UAEV_gender);
    }

    public function test_uae_submission_keeps_scanned_details_when_the_browser_read_them(): void
    {
        Storage::fake('public');
        $this->fakeCheckout();

        DB::table('tbl_UAEVStatus')->insertOrIgnore(['id' => 1, 'status_name' => 'Pending']);

        UAEVisaMaster::create([
            'company_id' => 1, 'UAEVisaDuration' => '30 Days', 'UAEVPrice' => 100, 'isActive' => true,
        ]);

        $payload = [
            'visaDuration'    => '30 Days',
            'price'           => '0',
            'visa_count'      => 1,
            'applicant_name'  => 'Ignored Contact Name',
            'email'           => 'scanned_uae@example.com',
            'phone'           => '+971500000002',
            // Posted by the hidden fields the browser scan fills in.
            'first_name'      => ['Yusuf'],
            'last_name'       => ['Khan'],
            'passport_number' => ['P4455667'],
            'dob'             => ['1988-03-09'],
            'gender'          => ['Male'],
            'passport_copy'   => [UploadedFile::fake()->create('p0.jpg', 50, 'image/jpeg')],
            'passport_photo'  => [UploadedFile::fake()->create('ph0.jpg', 50, 'image/jpeg')],
        ];

        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('uaev.submit'), $payload)
            ->assertOk()
            ->assertJson(['success' => true]);

        $app = UAEVApplication::where('UAEV_email', 'scanned_uae@example.com')->first();

        $this->assertNotNull($app);
        // The scanned passport name wins over the typed contact name.
        $this->assertEquals('Yusuf', $app->UAEV_first_name);
        $this->assertEquals('Khan', $app->UAEV_last_name);
        $this->assertEquals('P4455667', $app->UAEV_passport_number);
        $this->assertEquals('Male', $app->UAEV_gender);
    }

    public function test_uae_submission_rejects_a_booking_with_no_lead_traveller_name(): void
    {
        Storage::fake('public');
        $this->fakeCheckout();

        DB::table('tbl_UAEVStatus')->insertOrIgnore(['id' => 1, 'status_name' => 'Pending']);

        UAEVisaMaster::create([
            'company_id' => 1, 'UAEVisaDuration' => '30 Days', 'UAEVPrice' => 100, 'isActive' => true,
        ]);

        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('uaev.submit'), [
                'visaDuration'   => '30 Days',
                'price'          => '0',
                'visa_count'     => 1,
                'email'          => 'nameless_uae@example.com',
                'phone'          => '+971500000003',
                'passport_copy'  => [UploadedFile::fake()->create('p0.pdf', 50, 'application/pdf')],
                'passport_photo' => [UploadedFile::fake()->create('ph0.jpg', 50, 'image/jpeg')],
            ])
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertNull(UAEVApplication::where('UAEV_email', 'nameless_uae@example.com')->first());
    }

    public function test_manager_order_pages_render_an_application_awaiting_ocr(): void
    {
        DB::table('tbl_UAEVStatus')->insertOrIgnore(['id' => 1, 'status_name' => 'Pending']);

        $manager = \App\Models\User::factory()->create([
            'access_type' => 'manager',
            'role'        => 'company_admin',
            'company_id'  => 1,
        ]);

        // The row a booking now produces before OCR has run: documents on file,
        // passport columns still empty.
        $application = UAEVApplication::create([
            'company_id'          => 1,
            'UAEV_first_name'     => '',
            'UAEV_last_name'      => '',
            'UAEV_email'          => 'awaiting_ocr@example.com',
            'UAEV_phone'          => '+971500000006',
            'UAEV_passport_copy'  => 'visas/passport_copies/awaiting.pdf',
            'UAEV_visaDuration'   => '30 Days',
            'UAEV_price'          => 100,
            'UAEV_Created_by'     => 'Guest (Multi-Visa)',
            'UAEV_created_date'   => now(),
            'UAEV_isActive'       => 1,
            'UAEV_status'         => 1,
        ]);

        $this->actingAs($manager)->get(route('manager.orders.visa'))->assertOk();
        $this->actingAs($manager)
            ->get(route('manager.orders.visa.show', $application))
            ->assertOk()
            ->assertSee('awaiting_ocr@example.com');
    }

    // -------------------------------------------------------------- Saudi ----

    public function test_saudi_form_asks_for_no_passport_details(): void
    {
        $html = $this->get(route('saudi-visa.index'))->assertOk()->getContent();

        $this->assertStringContainsString('name="full_name"', $html);
        $this->assertStringContainsString('type="hidden" name="passport_number"', $html);

        foreach (['Passport Number *', 'Passport Expiry *', 'Date of Birth *', 'Gender *', 'Nationality *'] as $label) {
            $this->assertStringNotContainsString($label, $html, "'{$label}' must no longer be asked for");
        }
    }

    public function test_saudi_submission_succeeds_without_any_passport_details(): void
    {
        Storage::fake('public');
        $this->fakeCheckout();

        $visaType = SaudiVisaType::create([
            'company_id' => 1,
            'name' => 'Simplified Tourist Visa',
            'price' => 350.00,
            'isActive' => true,
        ]);

        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('saudi-visa.submit'), [
                'full_name'          => 'Omar Al Farsi',
                'email'              => 'simplified_saudi@example.com',
                'phone'              => '+971500000004',
                'saudi_visa_type_id' => $visaType->id,
                'passport_copy'      => UploadedFile::fake()->create('passport.pdf', 100, 'application/pdf'),
                'passport_photo'     => UploadedFile::fake()->create('photo.jpg', 100, 'image/jpeg'),
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $application = SaudiVisaApplication::where('email', 'simplified_saudi@example.com')->first();

        $this->assertNotNull($application);
        $this->assertEquals('Omar Al Farsi', $application->full_name);
        // Split from the typed name so the applicant is identifiable in the portal.
        $this->assertEquals('Omar', $application->first_name);
        $this->assertEquals('Al Farsi', $application->last_name);
        // Passport columns stay empty until OCR reads the uploaded copy.
        $this->assertNull($application->passport_number);
        $this->assertNull($application->dob);
        $this->assertNull($application->gender);
        $this->assertSame('', (string) $application->nationality);
        $this->assertEquals(350.00, (float) $application->price);
    }

    public function test_saudi_submission_requires_the_documents_it_reads_details_from(): void
    {
        Storage::fake('public');
        $this->fakeCheckout();

        $visaType = SaudiVisaType::create([
            'company_id' => 1,
            'name' => 'Docs Required Visa',
            'price' => 300.00,
            'isActive' => true,
        ]);

        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson(route('saudi-visa.submit'), [
                'full_name'          => 'No Documents',
                'email'              => 'nodocs_saudi@example.com',
                'phone'              => '+971500000005',
                'saudi_visa_type_id' => $visaType->id,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['passport_copy', 'passport_photo']);
    }
}
