<?php

namespace Tests\Feature;

use App\Mail\AgentLicenseExpiringMail;
use App\Models\AgentApplication;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * End-to-end coverage for the rebuilt agent registration flow from the
 * 2026-08-25 meeting requirements: single-screen form (company/contact,
 * UAE-vs-outside location, services), each field stored individually
 * (not a blob), automated pending-application creation, and the trade
 * license expiry/renewal lifecycle mirrored from B2bPartner.
 */
class AgentRegistrationTest extends TestCase
{
    use DatabaseTransactions;

    private function manager(): User
    {
        $company = Company::where('slug', 'gotrips')->firstOrFail();

        return User::where('company_id', $company->id)
            ->whereIn('role', ['company_owner', 'company_admin'])
            ->firstOrFail();
    }

    private function baseRegistrationPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Jane Applicant',
            'company_name' => 'Jane Travels LLC',
            'email' => 'jane+' . uniqid() . '@example.com',
            'phone' => '+971501234567',
            'address' => '123 Sheikh Zayed Road, Dubai',
            'registering_from_uae' => '1',
            'emirate' => 'Dubai',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'services' => ['activities', 'esim'],
            'trade_license_number' => 'TL-12345',
            'trade_license_expiry_date' => now()->addYear()->format('Y-m-d'),
            'trade_license_document' => UploadedFile::fake()->create('license.pdf', 100, 'application/pdf'),
        ], $overrides);
    }

    // ---------------------------------------------------------------
    // UI
    // ---------------------------------------------------------------

    public function test_registration_page_renders_all_three_sections(): void
    {
        $response = $this->get(route('agent.register'));

        $response->assertOk();
        $response->assertSee('Contact Name', false);
        $response->assertSee('Business Name / Company', false);
        $response->assertSee('Registering from UAE?', false);
        $response->assertSee('Dubai', false);
        $response->assertSee('Which products or services', false);
        $response->assertSee('Click to Register', false);
    }

    // ---------------------------------------------------------------
    // Validation
    // ---------------------------------------------------------------

    public function test_registration_requires_emirate_when_uae_selected(): void
    {
        Storage::fake('public');

        $response = $this->post(route('agent.register.submit'), $this->baseRegistrationPayload(['emirate' => '']));

        $response->assertSessionHasErrors('emirate');
    }

    public function test_registration_requires_country_when_outside_uae_selected(): void
    {
        Storage::fake('public');

        $response = $this->post(route('agent.register.submit'), $this->baseRegistrationPayload([
            'registering_from_uae' => '0',
            'emirate' => '',
            'country' => '',
        ]));

        $response->assertSessionHasErrors('country');
    }

    public function test_registration_requires_company_name_and_address(): void
    {
        Storage::fake('public');

        $response = $this->post(route('agent.register.submit'), $this->baseRegistrationPayload([
            'company_name' => '',
            'address' => '',
        ]));

        $response->assertSessionHasErrors(['company_name', 'address']);
    }

    public function test_registration_requires_at_least_one_service(): void
    {
        Storage::fake('public');

        $response = $this->post(route('agent.register.submit'), $this->baseRegistrationPayload(['services' => []]));

        $response->assertSessionHasErrors('services');
    }

    public function test_registration_rejects_a_trade_license_expiring_today_or_earlier(): void
    {
        Storage::fake('public');

        $response = $this->post(route('agent.register.submit'), $this->baseRegistrationPayload([
            'trade_license_expiry_date' => now()->format('Y-m-d'),
        ]));

        $response->assertSessionHasErrors('trade_license_expiry_date');
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        Storage::fake('public');
        Mail::fake();

        $email = 'dupe+' . uniqid() . '@example.com';
        $this->post(route('agent.register.submit'), $this->baseRegistrationPayload(['email' => $email]))
            ->assertRedirect(route('agent.register.submitted'));

        $response = $this->post(route('agent.register.submit'), $this->baseRegistrationPayload(['email' => $email]));

        $response->assertSessionHasErrors('email');
    }

    // ---------------------------------------------------------------
    // Registration + individual-field storage
    // ---------------------------------------------------------------

    public function test_valid_uae_registration_stores_every_field_individually(): void
    {
        Storage::fake('public');
        Mail::fake();

        $response = $this->post(route('agent.register.submit'), $this->baseRegistrationPayload());

        $response->assertRedirect(route('agent.register.submitted'));

        $application = AgentApplication::where('email', '!=', null)->latest('id')->first();
        $this->assertNotNull($application);
        $this->assertSame('Jane Applicant', $application->name);
        $this->assertSame('Jane Travels LLC', $application->company_name);
        $this->assertSame('123 Sheikh Zayed Road, Dubai', $application->address);
        $this->assertSame('United Arab Emirates', $application->country);
        $this->assertSame('Dubai', $application->emirate);
        $this->assertSame(['activities', 'esim'], $application->services);
        $this->assertSame('TL-12345', $application->trade_license_number);
        $this->assertSame('pending', $application->status);
        $this->assertNotNull($application->trade_license_document_path);
        Storage::disk('public')->assertExists($application->trade_license_document_path);
    }

    public function test_valid_outside_uae_registration_stores_country_and_leaves_emirate_null(): void
    {
        Storage::fake('public');
        Mail::fake();

        $this->post(route('agent.register.submit'), $this->baseRegistrationPayload([
            'registering_from_uae' => '0',
            'emirate' => '',
            'country' => 'India',
        ]))->assertRedirect(route('agent.register.submitted'));

        $application = AgentApplication::latest('id')->first();
        $this->assertSame('India', $application->country);
        $this->assertNull($application->emirate);
    }

    // ---------------------------------------------------------------
    // Automated account creation on approval
    // ---------------------------------------------------------------

    public function test_approving_an_application_creates_a_working_agent_account_with_every_field_copied(): void
    {
        Storage::fake('public');
        Mail::fake();

        $this->post(route('agent.register.submit'), $this->baseRegistrationPayload())
            ->assertRedirect(route('agent.register.submitted'));

        $application = AgentApplication::latest('id')->first();
        $manager = $this->manager();

        // No login exists yet — the account is only provisioned on approval.
        $preApproveLogin = $this->post(route('agent.login.submit'), [
            'email' => $application->email,
            'password' => 'password123',
        ]);
        $preApproveLogin->assertSessionHasErrors('credentials');

        $this->actingAs($manager)->post(route('manager.agent-applications.approve', $application->id))
            ->assertRedirect(route('manager.agent-applications.index'));

        $agent = User::where('email', $application->email)->first();
        $this->assertNotNull($agent);
        $this->assertSame('company_agent', $agent->role);
        $this->assertTrue($agent->is_active);
        $this->assertSame('Jane Travels LLC', $agent->company_name);
        $this->assertSame('United Arab Emirates', $agent->country);
        $this->assertSame('Dubai', $agent->emirate);
        $this->assertSame('123 Sheikh Zayed Road, Dubai', $agent->address);
        $this->assertSame('TL-12345', $agent->trade_license_number);
        $this->assertEqualsCanonicalizing(['activities', 'esim'], $agent->agent_services);

        $postApproveLogin = $this->post(route('agent.login.submit'), [
            'email' => $agent->email,
            'password' => 'password123',
        ]);
        $postApproveLogin->assertRedirect(route('agent.dashboard'));
    }

    // ---------------------------------------------------------------
    // Trade license expiry — server-side enforcement
    // ---------------------------------------------------------------

    private function approvedAgent(array $overrides = []): User
    {
        $company = Company::where('slug', 'gotrips')->firstOrFail();

        return User::create(array_merge([
            'name' => 'Expiry Test Agent',
            'company_name' => 'Expiry Co',
            'email' => 'expiry+' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'phone' => '+971500000000',
            'role' => 'company_agent',
            'company_id' => $company->id,
            'country' => 'United Arab Emirates',
            'emirate' => 'Dubai',
            'agent_services' => ['activities'],
            'trade_license_number' => 'TL-99999',
            'is_active' => true,
        ], $overrides));
    }

    public function test_agent_with_future_expiry_can_log_in(): void
    {
        $agent = $this->approvedAgent(['trade_license_expiry_date' => now()->addMonths(6)]);

        $response = $this->post(route('agent.login.submit'), [
            'email' => $agent->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('agent.dashboard'));
    }

    public function test_agent_with_expiry_today_is_treated_as_expired(): void
    {
        $agent = $this->approvedAgent(['trade_license_expiry_date' => now()->startOfDay()]);

        $response = $this->post(route('agent.login.submit'), [
            'email' => $agent->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('credentials');
        $this->assertStringContainsString('trade license', $response->getSession()->get('errors')->first('credentials'));
        $this->assertFalse($agent->fresh()->is_active);
    }

    public function test_agent_with_already_expired_license_cannot_log_in_and_is_auto_disabled(): void
    {
        $agent = $this->approvedAgent(['trade_license_expiry_date' => now()->subDays(5)]);

        $response = $this->post(route('agent.login.submit'), [
            'email' => $agent->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('credentials');
        $agent->refresh();
        $this->assertFalse($agent->is_active);
        $this->assertNotNull($agent->disabled_at);
    }

    public function test_an_expired_agent_already_logged_in_is_force_logged_out_mid_session(): void
    {
        $agent = $this->approvedAgent(['trade_license_expiry_date' => now()->addDays(10)]);
        $this->actingAs($agent);
        $this->assertTrue(Auth::check());

        // License lapses without the agent logging out.
        $agent->update(['trade_license_expiry_date' => now()->subDay()]);

        $response = $this->get(route('agent.dashboard'));

        $response->assertRedirect(route('agent.login'));
        $this->assertFalse(Auth::check());
        $this->assertFalse($agent->fresh()->is_active);
    }

    public function test_manager_created_agent_without_a_trade_license_is_unaffected(): void
    {
        // Regression: path-B agents (created directly by a manager, never
        // captured a license) must keep working exactly as before.
        $agent = $this->approvedAgent(['trade_license_expiry_date' => null]);

        $response = $this->post(route('agent.login.submit'), [
            'email' => $agent->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('agent.dashboard'));
    }

    // ---------------------------------------------------------------
    // Scheduled command
    // ---------------------------------------------------------------

    public function test_check_license_expiry_command_warns_soon_and_disables_expired(): void
    {
        Mail::fake();

        $soon = $this->approvedAgent(['trade_license_expiry_date' => now()->addDays(10)]);
        $expired = $this->approvedAgent(['trade_license_expiry_date' => now()->subDays(2)]);
        $safe = $this->approvedAgent(['trade_license_expiry_date' => now()->addMonths(6)]);

        Artisan::call('agents:check-license-expiry');

        $this->assertNotNull($soon->fresh()->expiry_warning_sent_at);
        $this->assertTrue($soon->fresh()->is_active);

        $this->assertFalse($expired->fresh()->is_active);
        $this->assertNotNull($expired->fresh()->disabled_at);

        $this->assertTrue($safe->fresh()->is_active);
        $this->assertNull($safe->fresh()->expiry_warning_sent_at);

        Mail::assertSent(AgentLicenseExpiringMail::class, fn ($mail) => $mail->agent->is($soon));
    }

    public function test_check_license_expiry_command_is_idempotent(): void
    {
        Mail::fake();
        $expired = $this->approvedAgent(['trade_license_expiry_date' => now()->subDays(2)]);

        Artisan::call('agents:check-license-expiry');
        Artisan::call('agents:check-license-expiry');

        Mail::assertSentCount(1);
        $this->assertFalse($expired->fresh()->is_active);
    }

    // ---------------------------------------------------------------
    // Self-service renewal + manager confirmation
    // ---------------------------------------------------------------

    public function test_disabled_agent_can_submit_renewal_which_requires_manager_confirmation(): void
    {
        Storage::fake('public');
        Mail::fake();

        $agent = $this->approvedAgent([
            'trade_license_expiry_date' => now()->subDays(1),
            'is_active' => false,
            'disabled_at' => now(),
        ]);

        $response = $this->post(route('agent.renew-license.submit'), [
            'email' => $agent->email,
            'password' => 'password123',
            'trade_license_number' => 'TL-RENEWED',
            'trade_license_expiry_date' => now()->addYear()->format('Y-m-d'),
            'trade_license_document' => UploadedFile::fake()->create('renewed.pdf', 100, 'application/pdf'),
        ]);

        $response->assertRedirect(route('agent.renew-license.submitted'));

        $agent->refresh();
        $this->assertTrue($agent->pending_license_review);
        $this->assertFalse($agent->is_active, 'Renewal alone must not restore access.');
        $this->assertSame('TL-RENEWED', $agent->trade_license_number);

        // Still cannot log in until a manager confirms.
        $this->post(route('agent.login.submit'), [
            'email' => $agent->email,
            'password' => 'password123',
        ])->assertSessionHasErrors('credentials');

        $manager = $this->manager();
        $this->actingAs($manager)->post(route('manager.agents.confirm-renewal', $agent->id))
            ->assertRedirect(route('manager.agents.index'));

        $agent->refresh();
        $this->assertTrue($agent->is_active);
        $this->assertFalse($agent->pending_license_review);
        $this->assertNull($agent->disabled_at);

        $this->post(route('agent.login.submit'), [
            'email' => $agent->email,
            'password' => 'password123',
        ])->assertRedirect(route('agent.dashboard'));
    }

    public function test_renewal_form_rejects_wrong_password(): void
    {
        Storage::fake('public');

        $agent = $this->approvedAgent([
            'trade_license_expiry_date' => now()->subDays(1),
            'is_active' => false,
            'disabled_at' => now(),
        ]);

        $response = $this->post(route('agent.renew-license.submit'), [
            'email' => $agent->email,
            'password' => 'wrong-password',
            'trade_license_number' => 'TL-RENEWED',
            'trade_license_expiry_date' => now()->addYear()->format('Y-m-d'),
            'trade_license_document' => UploadedFile::fake()->create('renewed.pdf', 100, 'application/pdf'),
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertFalse($agent->fresh()->pending_license_review);
    }

    // ---------------------------------------------------------------
    // Security / tenant isolation
    // ---------------------------------------------------------------

    public function test_a_manager_cannot_confirm_renewal_for_another_tenants_agent(): void
    {
        $otherCompany = Company::create([
            'name' => 'Other Tenant', 'slug' => 'other-tenant-' . uniqid(), 'features' => ['visas'],
        ]);

        $agent = $this->approvedAgent([
            'company_id' => $otherCompany->id,
            'trade_license_expiry_date' => now()->subDays(1),
            'is_active' => false,
            'disabled_at' => now(),
            'pending_license_review' => true,
        ]);

        $manager = $this->manager();

        $response = $this->actingAs($manager)->post(route('manager.agents.confirm-renewal', $agent->id));

        $response->assertStatus(404);
        $this->assertFalse($agent->fresh()->is_active);
    }
}
