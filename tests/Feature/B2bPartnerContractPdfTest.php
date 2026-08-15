<?php

namespace Tests\Feature;

use App\Models\B2bPartner;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class B2bPartnerContractPdfTest extends TestCase
{
    use DatabaseTransactions;

    public function test_contract_pdf_is_generated_and_stored_on_registration(): void
    {
        Storage::fake('public');
        Mail::fake();

        $email = 'pdf+' . uniqid() . '@example.com';
        $this->post(route('agency.register.submit'), [
            'company_name' => 'PDF Test Co',
            'contact_name' => 'Sam Smith',
            'email' => $email,
            'phone' => '+1234567890',
            'country' => 'Canada',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'signature_full_name' => 'Sam Smith',
            'signature_agreed' => '1',
        ]);

        $partner = B2bPartner::withoutCompanyScope()->where('email', $email)->firstOrFail();

        $this->assertNotNull($partner->contract_pdf_path);
        Storage::disk('public')->assertExists($partner->contract_pdf_path);
        $this->assertEquals('international', $partner->contract_type);
    }

    public function test_uae_partner_gets_a_national_contract(): void
    {
        Storage::fake('public');
        Mail::fake();

        $email = 'pdf-uae+' . uniqid() . '@example.com';
        $this->post(route('agency.register.submit'), [
            'company_name' => 'UAE PDF Test Co',
            'contact_name' => 'Sam Smith',
            'email' => $email,
            'phone' => '+1234567890',
            'country' => B2bPartner::UAE_COUNTRY_NAME,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'trade_license_number' => 'TL-99999',
            'trade_license_expiry_date' => now()->addYear()->format('Y-m-d'),
            'trade_license_document' => UploadedFile::fake()->create('license.pdf', 100, 'application/pdf'),
            'signature_full_name' => 'Sam Smith',
            'signature_agreed' => '1',
        ]);

        $partner = B2bPartner::withoutCompanyScope()->where('email', $email)->firstOrFail();

        $this->assertEquals('national', $partner->contract_type);
        $this->assertNotNull($partner->contract_pdf_path);
        Storage::disk('public')->assertExists($partner->contract_pdf_path);
    }
}
