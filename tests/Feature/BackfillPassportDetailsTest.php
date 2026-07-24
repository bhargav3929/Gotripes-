<?php

namespace Tests\Feature;

use App\Jobs\BackfillPassportDetails;
use App\Models\UAEVApplication;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The forms stopped collecting passport details, so the server has to read them
 * off the uploaded copy. This covers that safety net: it fills what is blank,
 * never overwrites what the browser scan already read, and stays quiet when the
 * passport cannot be read at all.
 */
class BackfillPassportDetailsTest extends TestCase
{
    use DatabaseTransactions;

    private const COLUMNS = [
        'first_name'      => 'UAEV_first_name',
        'last_name'       => 'UAEV_last_name',
        'passport_number' => 'UAEV_passport_number',
        'dob'             => 'UAEV_dob',
        'gender'          => 'UAEV_gender',
    ];

    /**
     * Store a real (1×1) JPEG on the fake disk and return its path.
     *
     * Written as raw bytes rather than via UploadedFile::fake()->image(), which
     * needs the GD extension the OCR path itself does not require.
     */
    private function storedJpeg(string $name = 'passport.jpg'): string
    {
        $bytes = base64_decode(
            '/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRof'
            . 'Hh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/wAALCAABAAEBAREA/8QAFAAB'
            . 'AAAAAAAAAAAAAAAAAAAACf/EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEAAD8AKp//2Q=='
        );

        $path = 'visas/passport_copies/' . $name;
        Storage::disk('public')->put($path, $bytes);

        return $path;
    }

    private function fakeGroq(array $fields): void
    {
        Http::fake([
            '*/chat/completions' => Http::response([
                'choices' => [[
                    'message' => ['content' => json_encode($fields)],
                ]],
            ], 200),
        ]);
    }

    private function application(array $attributes = []): UAEVApplication
    {
        DB::table('tbl_UAEVStatus')->insertOrIgnore(['id' => 1, 'status_name' => 'Pending']);

        return UAEVApplication::create(array_merge([
            'company_id'        => 1,
            'UAEV_first_name'   => '',
            'UAEV_last_name'    => '',
            'UAEV_email'        => 'backfill@example.com',
            'UAEV_visaDuration' => '30 Days',
            'UAEV_price'        => 100,
            'UAEV_Created_by'   => 'Guest (Multi-Visa)',
            'UAEV_created_date' => now(),
            'UAEV_isActive'     => 1,
            'UAEV_status'       => 1,
        ], $attributes));
    }

    public function test_it_fills_blank_passport_columns_from_the_uploaded_copy(): void
    {
        Storage::fake('public');
        $this->fakeGroq([
            'full_name'       => 'MARIA SANTOS',
            'given_names'     => 'MARIA',
            'surname'         => 'SANTOS',
            'passport_number' => 'X1122334',
            'date_of_birth'   => '1992-07-14',
            'sex'             => 'F',
        ]);

        $path = $this->storedJpeg();
        $application = $this->application();

        (new BackfillPassportDetails(UAEVApplication::class, $application->id, $path, self::COLUMNS))
            ->handle(app(\App\Services\PassportOcrService::class));

        $application->refresh();

        $this->assertEquals('MARIA', $application->UAEV_first_name);
        $this->assertEquals('SANTOS', $application->UAEV_last_name);
        $this->assertEquals('X1122334', $application->UAEV_passport_number);
        $this->assertEquals('1992-07-14', $application->UAEV_dob);
        $this->assertEquals('Female', $application->UAEV_gender);
    }

    public function test_it_never_overwrites_details_the_browser_scan_already_read(): void
    {
        Storage::fake('public');
        $this->fakeGroq([
            'given_names'     => 'WRONG',
            'surname'         => 'VALUE',
            'passport_number' => 'SHOULD-NOT-WIN',
            'date_of_birth'   => '1900-01-01',
            'sex'             => 'M',
        ]);

        $path = $this->storedJpeg();
        $application = $this->application([
            'UAEV_first_name'      => 'Yusuf',
            'UAEV_last_name'       => 'Khan',
            'UAEV_passport_number' => 'P4455667',
            'UAEV_dob'             => '1988-03-09',
            'UAEV_gender'          => 'Male',
        ]);

        (new BackfillPassportDetails(UAEVApplication::class, $application->id, $path, self::COLUMNS))
            ->handle(app(\App\Services\PassportOcrService::class));

        $application->refresh();

        $this->assertEquals('Yusuf', $application->UAEV_first_name);
        $this->assertEquals('P4455667', $application->UAEV_passport_number);
        $this->assertEquals('1988-03-09', $application->UAEV_dob);

        // Nothing was missing, so the OCR service is never even called.
        Http::assertNothingSent();
    }

    public function test_a_pdf_copy_leaves_the_record_untouched_rather_than_failing(): void
    {
        Storage::fake('public');
        $this->fakeGroq(['given_names' => 'NEVER', 'surname' => 'REACHED']);

        $path = UploadedFile::fake()->create('passport.pdf', 20, 'application/pdf')
            ->store('visas/passport_copies', 'public');
        $application = $this->application();

        (new BackfillPassportDetails(UAEVApplication::class, $application->id, $path, self::COLUMNS))
            ->handle(app(\App\Services\PassportOcrService::class));

        $application->refresh();

        $this->assertSame('', (string) $application->UAEV_first_name);
        Http::assertNothingSent();
    }

    public function test_a_deleted_record_does_not_throw(): void
    {
        Storage::fake('public');
        $this->fakeGroq(['given_names' => 'ANY']);

        $path = $this->storedJpeg();

        (new BackfillPassportDetails(UAEVApplication::class, 99999999, $path, self::COLUMNS))
            ->handle(app(\App\Services\PassportOcrService::class));

        $this->assertTrue(true, 'job completed without throwing');
    }

    public function test_uae_submission_queues_the_backfill_for_every_applicant(): void
    {
        Storage::fake('public');
        Bus::fake();
        Http::fake([
            '*/v1/checkout' => Http::response(['id' => 'chk', 'url' => 'https://pay.test/chk', 'status' => 'created'], 200),
        ]);

        DB::table('tbl_UAEVStatus')->insertOrIgnore(['id' => 1, 'status_name' => 'Pending']);
        \App\Models\UAEVisaMaster::create([
            'company_id' => 1, 'UAEVisaDuration' => '30 Days', 'UAEVPrice' => 100, 'isActive' => true,
        ]);

        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post(route('uaev.submit'), [
                'visaDuration'   => '30 Days',
                'price'          => '0',
                'visa_count'     => 2,
                'applicant_name' => 'Aisha Rahman',
                'email'          => 'queued_uae@example.com',
                'phone'          => '+971500000007',
                'passport_copy'  => [
                    UploadedFile::fake()->create('p0.jpg', 20, 'image/jpeg'),
                    UploadedFile::fake()->create('p1.jpg', 20, 'image/jpeg'),
                ],
                'passport_photo' => [
                    UploadedFile::fake()->create('ph0.jpg', 20, 'image/jpeg'),
                    UploadedFile::fake()->create('ph1.jpg', 20, 'image/jpeg'),
                ],
            ])
            ->assertOk();

        Bus::assertDispatchedAfterResponseTimes(BackfillPassportDetails::class, 2);
    }
}
