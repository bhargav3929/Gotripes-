<?php

namespace Tests\Feature;

use App\Mail\B2bPartnerApplicationDecisionMail;
use App\Models\B2bPartner;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * A pending B2B partner must not be able to log in until a manager approves
 * them; a rejected one never can. Approval/rejection is tenant-scoped —
 * a manager from another company can't touch it.
 */
class B2bPartnerApprovalGateTest extends TestCase
{
    use DatabaseTransactions;

    private function manager(): User
    {
        $company = Company::where('slug', 'gotrips')->firstOrFail();

        return User::where('company_id', $company->id)
            ->whereIn('role', ['company_owner', 'company_admin'])
            ->firstOrFail();
    }

    private function pendingPartner(array $overrides = []): B2bPartner
    {
        $company = Company::where('slug', 'gotrips')->firstOrFail();

        return B2bPartner::withoutCompanyScope()->create(array_merge([
            'company_id' => $company->id,
            'company_name' => 'Gate Test Co',
            'contact_name' => 'Alex Gate',
            'email' => 'gate+' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'phone' => '+1234567890',
            'country' => 'France',
            'contract_type' => 'international',
            'signature_full_name' => 'Alex Gate',
            'signature_agreed' => true,
            'signature_ip' => '127.0.0.1',
            'signed_at' => now(),
            'status' => 'pending',
        ], $overrides));
    }

    public function test_pending_partner_cannot_log_in(): void
    {
        $partner = $this->pendingPartner();

        $response = $this->post(route('agency.login.submit'), [
            'email' => $partner->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertFalse(Auth::guard('b2b_partner')->check());
    }

    public function test_approved_partner_can_log_in(): void
    {
        $partner = $this->pendingPartner(['status' => 'approved', 'reviewed_at' => now()]);

        $response = $this->post(route('agency.login.submit'), [
            'email' => $partner->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('agency.dashboard'));
        $this->assertTrue(Auth::guard('b2b_partner')->check());
    }

    public function test_rejected_partner_cannot_log_in(): void
    {
        $partner = $this->pendingPartner(['status' => 'rejected', 'rejection_reason' => 'Incomplete documents']);

        $response = $this->post(route('agency.login.submit'), [
            'email' => $partner->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('not approved', $response->getSession()->get('errors')->first('email'));
    }

    public function test_manager_approving_flips_status_and_sends_decision_email(): void
    {
        Mail::fake();
        $partner = $this->pendingPartner();
        $manager = $this->manager();

        $response = $this->actingAs($manager)->post(route('manager.b2b-partners.approve', $partner->id));

        $response->assertRedirect(route('manager.b2b-partners.index'));
        $this->assertEquals('approved', $partner->fresh()->status);
        Mail::assertSent(B2bPartnerApplicationDecisionMail::class, function ($mail) {
            return $mail->decision === 'approved';
        });
    }

    public function test_manager_rejecting_requires_a_reason_and_sends_decision_email(): void
    {
        Mail::fake();
        $partner = $this->pendingPartner();
        $manager = $this->manager();

        $missingReason = $this->actingAs($manager)->post(route('manager.b2b-partners.reject', $partner->id), []);
        $missingReason->assertSessionHasErrors('reason');

        $response = $this->actingAs($manager)->post(route('manager.b2b-partners.reject', $partner->id), [
            'reason' => 'Trade license document unreadable.',
        ]);

        $response->assertRedirect(route('manager.b2b-partners.index'));
        $partner->refresh();
        $this->assertEquals('rejected', $partner->status);
        $this->assertEquals('Trade license document unreadable.', $partner->rejection_reason);
        Mail::assertSent(B2bPartnerApplicationDecisionMail::class, function ($mail) {
            return $mail->decision === 'rejected';
        });
    }

    public function test_a_manager_cannot_view_or_approve_another_tenants_partner(): void
    {
        // In this environment IdentifyTenant always resolves the request
        // host (localhost, in tests) back to the 'gotrips' company, so
        // acting as a manager from a genuinely different tenant would be
        // bounced by ManagerAuthMiddleware's own tenant-mismatch check
        // before ever reaching this controller (a different security layer,
        // already covered elsewhere). To isolate the boundary this test
        // actually targets — BelongsToCompany's scoping on B2bPartner
        // itself — use the real gotrips manager, and make the PARTNER
        // belong to a different tenant instead.
        $otherCompany = Company::create([
            'name' => 'Other Tenant', 'slug' => 'other-tenant-' . uniqid(), 'features' => ['visas'],
        ]);
        $partner = $this->pendingPartner(['company_id' => $otherCompany->id]);
        $manager = $this->manager();

        $response = $this->actingAs($manager)->get(route('manager.b2b-partners.show', $partner->id));
        $response->assertStatus(404);

        $approve = $this->actingAs($manager)->post(route('manager.b2b-partners.approve', $partner->id));
        $approve->assertStatus(404);

        $this->assertEquals('pending', $partner->fresh()->status);
    }

    public function test_a_partner_already_logged_in_is_force_logged_out_once_disabled_mid_session(): void
    {
        $partner = $this->pendingPartner(['status' => 'approved', 'reviewed_at' => now()]);

        $this->actingAs($partner, 'b2b_partner');
        $this->assertTrue(Auth::guard('b2b_partner')->check());

        $partner->update(['is_active' => false]);

        $response = $this->get(route('agency.dashboard'));

        $response->assertRedirect(route('agency.login'));
        $this->assertFalse(Auth::guard('b2b_partner')->check());
    }
}
