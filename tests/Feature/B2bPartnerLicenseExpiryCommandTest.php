<?php

namespace Tests\Feature;

use App\Mail\B2bPartnerLicenseExpiringMail;
use App\Mail\BookingNotificationMail;
use App\Models\B2bPartner;
use App\Models\Company;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class B2bPartnerLicenseExpiryCommandTest extends TestCase
{
    use DatabaseTransactions;

    private function approvedUaePartner(array $overrides = []): B2bPartner
    {
        $company = Company::where('slug', 'gotrips')->firstOrFail();

        return B2bPartner::withoutCompanyScope()->create(array_merge([
            'company_id' => $company->id,
            'company_name' => 'Expiry Test Co',
            'contact_name' => 'Nora Expiry',
            'email' => 'expiry+' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'phone' => '+1234567890',
            'country' => B2bPartner::UAE_COUNTRY_NAME,
            'trade_license_number' => 'TL-EXP-1',
            'contract_type' => 'national',
            'signature_full_name' => 'Nora Expiry',
            'signature_agreed' => true,
            'signature_ip' => '127.0.0.1',
            'signed_at' => now(),
            'status' => 'approved',
            'reviewed_at' => now(),
            'is_active' => true,
        ], $overrides));
    }

    public function test_warns_once_and_is_idempotent(): void
    {
        Mail::fake();
        $partner = $this->approvedUaePartner(['trade_license_expiry_date' => now()->addDays(10)]);

        $this->artisan('partners:check-license-expiry')->assertExitCode(0);

        $partner->refresh();
        $this->assertNotNull($partner->expiry_warning_sent_at);
        $this->assertTrue($partner->is_active);
        Mail::assertSent(B2bPartnerLicenseExpiringMail::class, 1);
        Mail::assertSent(BookingNotificationMail::class, 1);

        // Running again must not send a second round of emails.
        $this->artisan('partners:check-license-expiry')->assertExitCode(0);
        Mail::assertSent(B2bPartnerLicenseExpiringMail::class, 1);
    }

    public function test_disables_an_expired_partner_and_blocks_login(): void
    {
        Mail::fake();
        $partner = $this->approvedUaePartner(['trade_license_expiry_date' => now()->subDay()]);

        $this->artisan('partners:check-license-expiry')->assertExitCode(0);

        $partner->refresh();
        $this->assertFalse($partner->is_active);
        $this->assertNotNull($partner->disabled_at);

        $response = $this->post(route('agency.login.submit'), [
            'email' => $partner->email,
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors('email');
    }

    public function test_dry_run_mutates_nothing(): void
    {
        Mail::fake();
        $expiringSoon = $this->approvedUaePartner(['trade_license_expiry_date' => now()->addDays(5)]);
        $expired = $this->approvedUaePartner(['trade_license_expiry_date' => now()->subDay()]);

        $this->artisan('partners:check-license-expiry --dry-run')->assertExitCode(0);

        $this->assertNull($expiringSoon->fresh()->expiry_warning_sent_at);
        $this->assertTrue($expired->fresh()->is_active);
        Mail::assertNothingSent();
    }

    public function test_non_uae_partner_is_never_touched(): void
    {
        Mail::fake();
        $partner = $this->approvedUaePartner([
            'country' => 'Germany',
            'contract_type' => 'international',
            'trade_license_number' => null,
            'trade_license_expiry_date' => null,
        ]);

        $this->artisan('partners:check-license-expiry')->assertExitCode(0);

        $partner->refresh();
        $this->assertTrue($partner->is_active);
        $this->assertNull($partner->expiry_warning_sent_at);
        Mail::assertNothingSent();
    }
}
