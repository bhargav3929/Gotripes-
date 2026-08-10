<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Company;
use App\Models\Emirates;
use App\Models\UAEVApplication;
use App\Models\UAEVisaMaster;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The Sharjah security deposit is only refundable to a bank account, so the
 * customer's bank details must actually be required for Sharjah — not just
 * shown. The HTML `required` attribute in uaevisa.blade.php only stops a
 * browser submission; a direct POST to /uaev/submit skipped it entirely
 * before this was enforced server-side too. Dubai (and everywhere else)
 * never charges a deposit, so bank details must stay optional there.
 */
class SharjahBankDetailsRequiredTest extends TestCase
{
    use DatabaseTransactions;

    private function fakeCheckout(): void
    {
        Http::fake([
            '*/v1/checkout' => Http::response(
                ['id' => 'chk_bank', 'url' => 'https://pay.test/chk_bank', 'status' => 'created'],
                200
            ),
        ]);
    }

    private function seedCommon(float $depositAmount = 0): void
    {
        DB::table('tbl_UAEVStatus')->insertOrIgnore(['id' => 1, 'status_name' => 'Pending']);

        UAEVisaMaster::create([
            'company_id' => 1, 'UAEVisaDuration' => '30 Days', 'UAEVPrice' => 100, 'isActive' => true,
        ]);

        $company = Company::first() ?: Company::create(['name' => 'Test Company', 'subdomain' => 'test']);
        $company->settings = array_merge($company->settings ?? [], [
            'visa_sharjah_deposit' => $depositAmount,
            'visa_sharjah_deposit_admin_fee' => 0,
        ]);
        $company->save();
    }

    public function test_sharjah_submission_without_bank_details_is_rejected(): void
    {
        Storage::fake('public');
        $this->fakeCheckout();
        $this->seedCommon(5000);

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson(route('uaev.submit'), [
                'selected_emirate' => 'Sharjah',
                'visaDuration'     => '30 Days',
                'price'            => '0',
                'visa_count'       => 1,
                'applicant_name'   => 'No Bank Details',
                'email'            => 'no_bank_sharjah@example.com',
                'phone'            => '+971500000010',
                'passport_copy'    => [UploadedFile::fake()->create('p0.pdf', 50, 'application/pdf')],
                'passport_photo'   => [UploadedFile::fake()->create('ph0.jpg', 50, 'image/jpeg')],
                // No bank_account_holder / bank_name / bank_account_number.
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['bank_account_holder', 'bank_name', 'bank_account_number']);

        $this->assertNull(UAEVApplication::where('UAEV_email', 'no_bank_sharjah@example.com')->first());
    }

    public function test_sharjah_submission_with_bank_details_succeeds_and_sets_refund_pending(): void
    {
        Storage::fake('public');
        $this->fakeCheckout();
        $this->seedCommon(5000);

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('uaev.submit'), [
                'selected_emirate' => 'Sharjah',
                'visaDuration'     => '30 Days',
                'price'            => '0',
                'visa_count'       => 1,
                'applicant_name'   => 'Has Bank Details',
                'email'            => 'has_bank_sharjah@example.com',
                'phone'            => '+971500000011',
                'bank_account_holder' => 'Has Bank Details',
                'bank_name'           => 'Emirates NBD',
                'bank_account_number' => 'AE1234567890123456789',
                'bank_swift_code'     => 'EBANDAEAAXXX',
                'passport_copy'    => [UploadedFile::fake()->create('p0.pdf', 50, 'application/pdf')],
                'passport_photo'   => [UploadedFile::fake()->create('ph0.jpg', 50, 'image/jpeg')],
            ]);

        $response->assertOk()->assertJson(['success' => true]);

        $app = UAEVApplication::where('UAEV_email', 'has_bank_sharjah@example.com')->first();
        $this->assertNotNull($app);
        $this->assertEquals(5000.00, (float) $app->UAEV_refund_amount);
        $this->assertEquals('pending', $app->UAEV_refund_status);
        $this->assertNull($app->UAEV_refunded_at);
    }

    public function test_dubai_submission_without_bank_details_still_succeeds(): void
    {
        Storage::fake('public');
        $this->fakeCheckout();
        $this->seedCommon(5000); // deposit configured, but Dubai never charges it

        $response = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('uaev.submit'), [
                'selected_emirate' => 'Dubai',
                'visaDuration'     => '30 Days',
                'price'            => '0',
                'visa_count'       => 1,
                'applicant_name'   => 'Dubai No Bank',
                'email'            => 'no_bank_dubai@example.com',
                'phone'            => '+971500000012',
                'passport_copy'    => [UploadedFile::fake()->create('p0.pdf', 50, 'application/pdf')],
                'passport_photo'   => [UploadedFile::fake()->create('ph0.jpg', 50, 'image/jpeg')],
            ]);

        $response->assertOk()->assertJson(['success' => true]);

        $app = UAEVApplication::where('UAEV_email', 'no_bank_dubai@example.com')->first();
        $this->assertNotNull($app);
        $this->assertEquals(0.00, (float) $app->UAEV_deposit_amount);
        $this->assertNull($app->UAEV_refund_status);
        $this->assertNull($app->UAEV_bank_account_holder);
    }

    public function test_manager_can_mark_a_sharjah_refund_as_paid(): void
    {
        DB::table('tbl_UAEVStatus')->insertOrIgnore(['id' => 1, 'status_name' => 'Pending']);

        $manager = User::factory()->create([
            'access_type' => 'manager', 'role' => 'company_admin', 'company_id' => 1,
        ]);

        $app = UAEVApplication::create([
            'company_id' => 1,
            'UAEV_emirate' => 'Sharjah',
            'UAEV_email' => 'refund_me@example.com',
            'UAEV_first_name' => 'Refund',
            'UAEV_last_name' => 'Me',
            'UAEV_phone' => '+971500000013',
            'UAEV_visaDuration' => '30 Days',
            'UAEV_price' => 100,
            'UAEV_deposit_amount' => 5000,
            'UAEV_refund_amount' => 5000,
            'UAEV_refund_status' => 'pending',
            'UAEV_bank_account_holder' => 'Refund Me',
            'UAEV_bank_name' => 'Emirates NBD',
            'UAEV_bank_account_number' => 'AE1234567890123456789',
            'UAEV_Created_by' => 'Guest (Multi-Visa)',
            'UAEV_created_date' => now(),
            'UAEV_isActive' => 1,
            'UAEV_status' => 1,
        ]);

        $this->actingAs($manager)
            ->post(route('manager.orders.visa.refund', $app->id))
            ->assertRedirect(route('manager.orders.visa.show', $app->id));

        $app->refresh();
        $this->assertEquals('refunded', $app->UAEV_refund_status);
        $this->assertNotNull($app->UAEV_refunded_at);
    }

    public function test_marking_refund_paid_is_rejected_when_nothing_is_due(): void
    {
        DB::table('tbl_UAEVStatus')->insertOrIgnore(['id' => 1, 'status_name' => 'Pending']);

        $manager = User::factory()->create([
            'access_type' => 'manager', 'role' => 'company_admin', 'company_id' => 1,
        ]);

        $app = UAEVApplication::create([
            'company_id' => 1,
            'UAEV_emirate' => 'Dubai',
            'UAEV_email' => 'no_refund_due@example.com',
            'UAEV_first_name' => 'No',
            'UAEV_last_name' => 'Refund',
            'UAEV_phone' => '+971500000014',
            'UAEV_visaDuration' => '30 Days',
            'UAEV_price' => 100,
            'UAEV_deposit_amount' => 0,
            'UAEV_refund_amount' => 0,
            'UAEV_Created_by' => 'Guest (Multi-Visa)',
            'UAEV_created_date' => now(),
            'UAEV_isActive' => 1,
            'UAEV_status' => 1,
        ]);

        $this->actingAs($manager)
            ->post(route('manager.orders.visa.refund', $app->id))
            ->assertSessionHas('error');

        $app->refresh();
        $this->assertNull($app->UAEV_refund_status);
    }
}
