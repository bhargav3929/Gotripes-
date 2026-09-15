<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Mail\CustomerCareWelcomeMail;
use App\Mail\SupportTicketMail;
use App\Models\Company;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * The restricted customer_care manager login: only the support queue and the
 * help guide, assignable, notified on assignment, created by owners/admins.
 */
class CustomerCareRoleTest extends TestCase
{
    use DatabaseTransactions;

    /** Temporary help files created by the Help test, removed in tearDown. */
    private array $temporaryHelpFiles = [];
    private bool $createdHelpDirectory = false;

    protected function tearDown(): void
    {
        foreach ($this->temporaryHelpFiles as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        if ($this->createdHelpDirectory && is_dir(base_path('docs/help'))) {
            @rmdir(base_path('docs/help')); // only succeeds when empty
        }

        parent::tearDown();
    }

    /**
     * dispatch()->afterResponse() registers an app "terminating" callback. In a
     * real request the process ends afterwards; in a test the same app serves
     * the next request and re-runs every callback registered so far, which
     * would double-count mails and notes. Clear them once they have run.
     */
    private function flushAfterResponseJobs(): void
    {
        $property = new \ReflectionProperty($this->app, 'terminatingCallbacks');
        $property->setValue($this->app, []);
    }

    private function company(): Company
    {
        return Company::where('slug', 'gotrips')->firstOrFail();
    }

    private function makeUser(string $role, array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'company_id' => $this->company()->id,
            'role' => $role,
            'access_type' => 'manager',
            'is_active' => true,
        ], $overrides));
    }

    private function makeTicket(): SupportTicket
    {
        Mail::fake();

        $ticketNumber = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson(route('support.tickets.store'), [
                'message' => 'My eSIM QR code will not scan on my phone.',
                'name' => 'Role Test Traveller',
                'email' => 'role-test@example.com',
                'phone' => '+971500000099',
                'source_url' => 'https://gotrips.ai/esim',
            ])
            ->assertCreated()
            ->json('ticket.ticket_number');

        return SupportTicket::where('ticket_number', $ticketNumber)->firstOrFail();
    }

    public function test_customer_care_login_lands_on_the_support_queue(): void
    {
        $care = $this->makeUser('customer_care');

        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.login.submit'), [
                'email' => $care->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('manager.support.index'));

        $this->assertAuthenticatedAs($care);

        // Visiting the login page while signed in also goes to the queue, not the dashboard.
        $this->get(route('manager.login'))->assertRedirect(route('manager.support.index'));
    }

    public function test_customer_care_is_bounced_from_every_other_manager_route(): void
    {
        $care = $this->makeUser('customer_care');

        $this->actingAs($care)->get(route('manager.dashboard'))
            ->assertRedirect(route('manager.support.index'));
        $this->actingAs($care)->get(route('manager.agents.index'))
            ->assertRedirect(route('manager.support.index'));
        $this->actingAs($care)->get(route('manager.finance.index'))
            ->assertRedirect(route('manager.support.index'));

        // The two areas the role owns stay open.
        $this->actingAs($care)->get(route('manager.support.index'))->assertOk();
        $this->actingAs($care)->get(route('manager.help.index'))->assertOk();

        // Still signed in afterwards: a bounce is a redirect, not a logout.
        $this->assertAuthenticatedAs($care);
    }

    public function test_sidebar_hides_everything_except_support_and_help_for_customer_care(): void
    {
        $care = $this->makeUser('customer_care');

        $response = $this->actingAs($care)->get(route('manager.support.index'))->assertOk();

        $response->assertSee('Support Tickets')
            ->assertSee('Help Guide')
            ->assertSee(route('manager.support.index'), false)
            ->assertDontSee('Hero Ad Slots')
            ->assertDontSee('Earnings')
            ->assertDontSee('Agent Applications')
            ->assertDontSee('Profile &amp; Branding', false)
            ->assertDontSee('Workflow settings')
            ->assertDontSee('Customer care staff')
            ->assertDontSee(route('manager.dashboard') . '"', false);
    }

    public function test_customer_care_staff_appear_in_the_assignee_dropdown(): void
    {
        $ticket = $this->makeTicket();
        $admin = $this->makeUser('company_admin');
        $care = $this->makeUser('customer_care', ['name' => 'Care Person Zed']);
        $inactiveCare = $this->makeUser('customer_care', ['name' => 'Retired Care Agent', 'is_active' => false]);

        $this->actingAs($admin)->get(route('manager.support.show', $ticket))
            ->assertOk()
            ->assertSee('Care Person Zed')
            ->assertDontSee('Retired Care Agent');
    }

    public function test_assigning_a_ticket_emails_the_assignee_and_records_an_internal_note(): void
    {
        $ticket = $this->makeTicket();
        $admin = $this->makeUser('company_admin');
        $care = $this->makeUser('customer_care', ['name' => 'Care Person Zed']);

        Mail::fake();
        $this->actingAs($admin)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.support.update', $ticket), [
                'status' => 'open',
                'priority' => 'high',
                'assigned_to' => $care->id,
            ])
            ->assertRedirect();
        $this->flushAfterResponseJobs();

        $ticket->refresh();
        $this->assertSame($care->id, $ticket->assigned_to);

        Mail::assertSent(SupportTicketMail::class, function (SupportTicketMail $mail) use ($care, $ticket) {
            return $mail->kind === 'staff_assigned'
                && $mail->ticket->is($ticket)
                && $mail->hasTo($care->email);
        });

        $note = $ticket->messages()->where('visibility', 'internal')->first();
        $this->assertNotNull($note);
        $this->assertSame('system', $note->sender_type);
        $this->assertStringContainsString('Care Person Zed', $note->message);
        $this->assertStringContainsString($admin->name, $note->message);

        // The customer's tracking view only shows public messages: the note never leaks.
        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson(route('support.tickets.track'), ['ticket_number' => $ticket->ticket_number])
            ->assertOk()
            ->assertJsonMissing(['visibility' => 'internal'])
            ->assertJsonCount(2, 'ticket.messages');

        // Re-saving with the same assignee sends nothing new. (Mail::fake() is
        // idempotent within a test, so count instead of asserting "not sent".)
        $assignedMails = fn () => Mail::sent(SupportTicketMail::class, fn (SupportTicketMail $mail) => $mail->kind === 'staff_assigned')->count();
        $this->assertSame(1, $assignedMails());
        $this->actingAs($admin)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.support.update', $ticket), [
                'status' => 'open',
                'priority' => 'urgent',
                'assigned_to' => $care->id,
            ])
            ->assertRedirect();
        $this->flushAfterResponseJobs();
        $this->assertSame(1, $assignedMails());
        $this->assertSame(1, $ticket->messages()->where('visibility', 'internal')->count());
    }

    public function test_reply_that_hands_over_the_ticket_also_notifies_the_new_assignee(): void
    {
        $ticket = $this->makeTicket();
        $admin = $this->makeUser('company_admin');
        $care = $this->makeUser('customer_care');

        Mail::fake();
        $this->actingAs($admin)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.support.reply', $ticket), [
                'message' => 'Passing this to our care team, they will follow up shortly.',
                'status' => 'open',
                'assigned_to' => $care->id,
            ])
            ->assertRedirect();
        $this->flushAfterResponseJobs();

        Mail::assertSent(fn (SupportTicketMail $mail) => $mail->kind === 'customer_reply');
        Mail::assertSent(fn (SupportTicketMail $mail) => $mail->kind === 'staff_assigned' && $mail->hasTo($care->email));
        $this->assertSame($care->id, $ticket->fresh()->assigned_to);
    }

    public function test_assigning_a_ticket_to_yourself_sends_no_assignment_email(): void
    {
        $ticket = $this->makeTicket();
        $admin = $this->makeUser('company_admin');

        Mail::fake();
        $this->actingAs($admin)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.support.reply', $ticket), [
                'message' => 'Looking into this now.',
                'status' => 'open',
                'assigned_to' => $admin->id,
            ])
            ->assertRedirect();
        $this->flushAfterResponseJobs();

        Mail::assertSent(fn (SupportTicketMail $mail) => $mail->kind === 'customer_reply');
        Mail::assertNotSent(fn (SupportTicketMail $mail) => $mail->kind === 'staff_assigned');
        $this->assertSame(0, $ticket->messages()->where('visibility', 'internal')->count());
    }

    public function test_owner_creates_customer_care_staff_and_the_welcome_email_goes_out(): void
    {
        $owner = $this->makeUser('company_owner');

        Mail::fake();
        $response = $this->actingAs($owner)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.support.customer-care.store'), [
                'name' => 'New Care Hire',
                'email' => 'New.Care.Hire@example.com',
                'phone' => '+971500000123',
            ])
            ->assertRedirect(route('manager.support.index'))
            ->assertSessionHas('care_credentials');

        $staff = User::where('email', 'new.care.hire@example.com')->firstOrFail();
        $this->assertSame('customer_care', $staff->role);
        $this->assertSame('manager', $staff->access_type);
        $this->assertSame($this->company()->id, (int) $staff->company_id);
        $this->assertTrue($staff->is_active);

        $credentials = session('care_credentials');
        $this->assertSame(12, strlen($credentials['password']));
        $this->assertSame(route('manager.login'), $credentials['url']);

        Mail::assertSent(CustomerCareWelcomeMail::class, function (CustomerCareWelcomeMail $mail) use ($staff, $credentials) {
            return $mail->hasTo($staff->email)
                && $mail->temporaryPassword === $credentials['password']
                && $mail->loginUrl === route('manager.login');
        });

        // The new person can actually log in with the emailed password and lands on the queue.
        $this->post(route('manager.logout'));
        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.login.submit'), [
                'email' => $staff->email,
                'password' => $credentials['password'],
            ])
            ->assertRedirect(route('manager.support.index'));

        // Duplicate email is rejected, not silently overwritten.
        $this->actingAs($owner)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->from(route('manager.support.index'))
            ->post(route('manager.support.customer-care.store'), [
                'name' => 'Again',
                'email' => $staff->email,
            ])
            ->assertRedirect(route('manager.support.index'))
            ->assertSessionHasErrors('email');
    }

    public function test_customer_care_cannot_change_settings_or_create_staff(): void
    {
        $care = $this->makeUser('customer_care');

        $this->actingAs($care)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.support.settings'), [
                'support_hours' => 'Never',
                'support_sla_minutes' => 60,
                'support_auto_response' => 'This should not be saved because the role is not allowed.',
            ])
            ->assertForbidden();

        $this->actingAs($care)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.support.customer-care.store'), [
                'name' => 'Sneaky Hire',
                'email' => 'sneaky@example.com',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'sneaky@example.com']);
        $this->assertNotSame('Never', $this->company()->fresh()->getSetting('support_hours'));
    }

    public function test_inactive_customer_care_cannot_log_in_and_an_open_session_is_dropped(): void
    {
        $care = $this->makeUser('customer_care', ['is_active' => false]);

        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->from(route('manager.login'))
            ->post(route('manager.login.submit'), [
                'email' => $care->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('manager.login'))
            ->assertSessionHasErrors('credentials');
        $this->assertGuest();

        // A session that was valid before deactivation is ended on the next request.
        $this->actingAs($care)->get(route('manager.support.index'))
            ->assertRedirect(route('manager.login'));
        $this->assertGuest();
    }

    public function test_help_index_lists_guides_in_prefix_order_and_show_renders_markdown(): void
    {
        $directory = base_path('docs/help');
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
            $this->createdHelpDirectory = true;
        }

        $first = $directory . '/98-zz-test-guide-a.md';
        $second = $directory . '/99-zz-test-guide-b.md';
        $this->temporaryHelpFiles = [$first, $second];
        file_put_contents($first, "# Zz Test Guide A\n\nSummary line for **guide A** here.\n\n## Steps\n\n| Step | Action |\n|---|---|\n| 1 | Open the page |\n");
        file_put_contents($second, "# Zz Test Guide B\n\nSummary line for guide B.\n");

        $care = $this->makeUser('customer_care');

        $index = $this->actingAs($care)->get(route('manager.help.index'))
            ->assertOk()
            ->assertSee('Zz Test Guide A')
            ->assertSee('Zz Test Guide B')
            ->assertSee('Summary line for guide A here.')
            ->assertSee(route('manager.help.show', 'zz-test-guide-a'), false);

        $this->assertLessThan(
            strpos($index->getContent(), 'Zz Test Guide B'),
            strpos($index->getContent(), 'Zz Test Guide A'),
            'Guides must be listed in numeric-prefix order.'
        );

        $this->actingAs($care)->get(route('manager.help.show', 'zz-test-guide-a'))
            ->assertOk()
            ->assertSee('<h2>Steps</h2>', false)
            ->assertSee('<table>', false)
            ->assertSee('Open the page')
            ->assertSee(route('manager.help.show', 'zz-test-guide-b'), false);

        // Slugs never touch the filesystem directly.
        $this->actingAs($care)->get(route('manager.help.show', '..%2F..%2F.env'))->assertNotFound();
        $this->actingAs($care)->get(route('manager.help.show', 'does-not-exist'))->assertNotFound();
    }
}
