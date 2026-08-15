<?php

namespace Tests\Feature;

use App\Models\B2bPartner;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * A UAE partner disabled by the expiry command can submit a renewed license
 * without full login (they can't pass canLogin()), but the account stays
 * disabled until a manager explicitly confirms it — auto-reactivation was
 * deliberately rejected as a design option.
 */
class B2bPartnerLicenseRenewalTest extends TestCase
{
    use DatabaseTransactions;

    private function manager(): User
    {
        $company = Company::where('slug', 'gotrips')->firstOrFail();

        return User::where('company_id', $company->id)
            ->whereIn('role', ['company_owner', 'company_admin'])
            ->firstOrFail();
    }

    private function disabledForExpiryPartner(): B2bPartner
    {
        $company = Company::where('slug', 'gotrips')->firstOrFail();

        return B2bPartner::withoutCompanyScope()->create([
            'company_id' => $company->id,
            'company_name' => 'Renewal Test Co',
            'contact_name' => 'Ravi Renew',
            'email' => 'renew+' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'phone' => '+1234567890',
            'country' => B2bPartner::UAE_COUNTRY_NAME,
            'trade_license_number' => 'TL-OLD-1',
            'trade_license_expiry_date' => now()->subDay(),
            'contract_type' => 'national',
            'signature_full_name' => 'Ravi Renew',
            'signature_agreed' => true,
            'signature_ip' => '127.0.0.1',
            'signed_at' => now(),
            'status' => 'approved',
            'reviewed_at' => now(),
            'is_active' => false,
            'disabled_at' => now(),
        ]);
    }

    public function test_submitting_a_renewal_does_not_reactivate_the_account(): void
    {
        Storage::fake('public');
        Mail::fake();
        $partner = $this->disabledForExpiryPartner();

        $response = $this->post(route('agency.renew-license.submit'), [
            'email' => $partner->email,
            'password' => 'password123',
            'trade_license_number' => 'TL-NEW-1',
            'trade_license_expiry_date' => now()->addYear()->format('Y-m-d'),
            'trade_license_document' => UploadedFile::fake()->create('license.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('agency.renew-license.submitted'));

        $partner->refresh();
        $this->assertEquals('TL-NEW-1', $partner->trade_license_number);
        $this->assertTrue($partner->pending_license_review);
        $this->assertFalse($partner->is_active, 'submitting a renewal must not auto-reactivate the account');

        // Still cannot log in until a manager confirms it.
        $login = $this->post(route('agency.login.submit'), [
            'email' => $partner->email,
            'password' => 'password123',
        ]);
        $login->assertSessionHasErrors('email');
    }

    public function test_wrong_password_is_rejected(): void
    {
        $partner = $this->disabledForExpiryPartner();

        $response = $this->post(route('agency.renew-license.submit'), [
            'email' => $partner->email,
            'password' => 'wrong-password',
            'trade_license_number' => 'TL-NEW-1',
            'trade_license_expiry_date' => now()->addYear()->format('Y-m-d'),
            'trade_license_document' => UploadedFile::fake()->create('license.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertFalse($partner->fresh()->pending_license_review);
    }

    public function test_a_pending_or_approved_active_partner_cannot_use_the_renewal_form(): void
    {
        $company = Company::where('slug', 'gotrips')->firstOrFail();
        $active = B2bPartner::withoutCompanyScope()->create([
            'company_id' => $company->id,
            'company_name' => 'Active Co', 'contact_name' => 'A', 'email' => 'active+' . uniqid() . '@example.com',
            'password' => Hash::make('password123'), 'phone' => '1', 'country' => 'Germany',
            'contract_type' => 'international', 'signature_full_name' => 'A', 'signature_agreed' => true,
            'signature_ip' => '127.0.0.1', 'signed_at' => now(), 'status' => 'approved', 'is_active' => true,
        ]);

        $response = $this->post(route('agency.renew-license.submit'), [
            'email' => $active->email,
            'password' => 'password123',
            'trade_license_number' => 'X',
            'trade_license_expiry_date' => now()->addYear()->format('Y-m-d'),
            'trade_license_document' => UploadedFile::fake()->create('license.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_manager_confirming_the_renewal_reactivates_the_account(): void
    {
        $partner = $this->disabledForExpiryPartner();
        $partner->submitLicenseRenewal('TL-NEW-2', now()->addYear()->format('Y-m-d'), 'partners/trade_licenses/fake.pdf');
        $manager = $this->manager();

        $response = $this->actingAs($manager)->post(route('manager.b2b-partners.confirm-renewal', $partner->id));

        $response->assertRedirect(route('manager.b2b-partners.index'));
        $partner->refresh();
        $this->assertTrue($partner->is_active);
        $this->assertNull($partner->disabled_at);
        $this->assertFalse($partner->pending_license_review);

        $login = $this->post(route('agency.login.submit'), [
            'email' => $partner->email,
            'password' => 'password123',
        ]);
        $login->assertRedirect(route('agency.dashboard'));
    }

    public function test_manager_can_set_commission_on_an_approved_partner(): void
    {
        $company = Company::where('slug', 'gotrips')->firstOrFail();
        $partner = B2bPartner::withoutCompanyScope()->create([
            'company_id' => $company->id,
            'company_name' => 'Commission Co', 'contact_name' => 'C', 'email' => 'comm+' . uniqid() . '@example.com',
            'password' => Hash::make('password123'), 'phone' => '1', 'country' => 'Germany',
            'contract_type' => 'international', 'signature_full_name' => 'C', 'signature_agreed' => true,
            'signature_ip' => '127.0.0.1', 'signed_at' => now(), 'status' => 'approved', 'is_active' => true,
        ]);
        $manager = $this->manager();

        $response = $this->actingAs($manager)->put(route('manager.b2b-partners.commission.update', $partner->id), [
            'commission_percent' => 12.5,
        ]);

        $response->assertRedirect(route('manager.b2b-partners.show', $partner->id));
        $this->assertEquals(12.5, (float) $partner->fresh()->commission_percent);
    }
}
