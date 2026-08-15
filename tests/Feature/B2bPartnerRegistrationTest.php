<?php

namespace Tests\Feature;

use App\Mail\B2bPartnerWelcomeMail;
use App\Models\B2bPartner;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Public B2B partner self-registration: UAE applicants must supply trade
 * license fields and get a "national" contract; everyone else gets
 * "international" and never touches the license fields. Registration never
 * logs the applicant in — they start pending.
 */
class B2bPartnerRegistrationTest extends TestCase
{
    use DatabaseTransactions;

    private function basePayload(array $overrides = []): array
    {
        return array_merge([
            'company_name' => 'Test Travel Co',
            'contact_name' => 'Jane Doe',
            'email' => 'jane+' . uniqid() . '@example.com',
            'phone' => '+1234567890',
            'country' => 'United Kingdom',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'signature_full_name' => 'Jane Doe',
            'signature_agreed' => '1',
        ], $overrides);
    }

    public function test_non_uae_registration_succeeds_without_license_fields_and_does_not_log_in(): void
    {
        Storage::fake('public');
        Mail::fake();

        $response = $this->post(route('agency.register.submit'), $this->basePayload());

        $response->assertRedirect(route('agency.register.submitted'));

        $partner = B2bPartner::withoutCompanyScope()->where('email', 'like', 'jane+%')->firstOrFail();
        $this->assertEquals('international', $partner->contract_type);
        $this->assertNull($partner->trade_license_number);
        $this->assertNull($partner->trade_license_expiry_date);
        $this->assertEquals('pending', $partner->status);

        $this->assertFalse(\Illuminate\Support\Facades\Auth::guard('b2b_partner')->check());
    }

    public function test_uae_registration_without_license_fields_fails_validation(): void
    {
        $response = $this->post(route('agency.register.submit'), $this->basePayload([
            'country' => B2bPartner::UAE_COUNTRY_NAME,
        ]));

        $response->assertSessionHasErrors(['trade_license_number', 'trade_license_expiry_date', 'trade_license_document']);
    }

    public function test_uae_registration_with_license_fields_succeeds_and_stores_national_contract(): void
    {
        Storage::fake('public');
        Mail::fake();

        $email = 'uae+' . uniqid() . '@example.com';
        $response = $this->post(route('agency.register.submit'), $this->basePayload([
            'country' => B2bPartner::UAE_COUNTRY_NAME,
            'email' => $email,
            'trade_license_number' => 'TL-12345',
            'trade_license_expiry_date' => now()->addYear()->format('Y-m-d'),
            'trade_license_document' => UploadedFile::fake()->create('license.pdf', 100, 'application/pdf'),
        ]));

        $response->assertRedirect(route('agency.register.submitted'));

        $partner = B2bPartner::withoutCompanyScope()->where('email', $email)->firstOrFail();
        $this->assertEquals('national', $partner->contract_type);
        $this->assertEquals('TL-12345', $partner->trade_license_number);
        $this->assertNotNull($partner->trade_license_document_path);
        Storage::disk('public')->assertExists($partner->trade_license_document_path);
    }

    public function test_missing_signature_agreement_fails_validation(): void
    {
        $response = $this->post(route('agency.register.submit'), $this->basePayload([
            'signature_agreed' => null,
        ]));

        $response->assertSessionHasErrors('signature_agreed');
    }

    public function test_welcome_email_is_sent_exactly_once(): void
    {
        Storage::fake('public');
        Mail::fake();

        $this->post(route('agency.register.submit'), $this->basePayload());

        Mail::assertSent(B2bPartnerWelcomeMail::class, 1);
    }
}
