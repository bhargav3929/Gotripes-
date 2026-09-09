<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Mail\SupportTicketMail;
use App\Models\Company;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SupportTicketFlowTest extends TestCase
{
    use DatabaseTransactions;

    private function company(): Company
    {
        return Company::where('slug', 'gotrips')->firstOrFail();
    }

    private function createTicket(string $email = 'traveller@example.com'): array
    {
        Mail::fake();

        return $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson(route('support.tickets.store'), [
                'message' => 'I paid for an eSIM but cannot find the installation instructions.',
                'name' => 'Test Traveller',
                'email' => $email,
                'phone' => '+971500000010',
                'source_url' => 'https://gotrips.ai/esim',
            ])
            ->assertCreated()
            ->assertJsonStructure(['ticket' => ['ticket_number', 'status', 'messages']])
            ->json('ticket');
    }

    public function test_customer_receives_a_trackable_ticket_and_both_email_routes_are_attempted(): void
    {
        $ticketData = $this->createTicket();

        $ticket = SupportTicket::where('ticket_number', $ticketData['ticket_number'])->firstOrFail();
        $this->assertSame('new', $ticket->status);
        $this->assertSame('not_configured', $ticket->whatsapp_delivery_status);
        $this->assertCount(2, $ticket->messages); // customer problem + auto acknowledgement

        Mail::assertSent(fn (SupportTicketMail $mail) => $mail->kind === 'customer_created');
        Mail::assertSent(fn (SupportTicketMail $mail) => $mail->kind === 'staff_created');

        // Tracking is by ticket number alone — the number is the credential.
        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson(route('support.tickets.track'), [
                'ticket_number' => strtolower($ticket->ticket_number),
            ])
            ->assertOk()
            ->assertJsonPath('ticket.ticket_number', $ticket->ticket_number)
            ->assertJsonCount(2, 'ticket.messages');
    }

    public function test_tracking_needs_only_the_ticket_number_and_rejects_unknown_ones(): void
    {
        $ticketData = $this->createTicket();

        // No email: a customer who mistyped the address they signed up with must
        // still be able to reach their own conversation.
        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson(route('support.tickets.track'), [
                'ticket_number' => $ticketData['ticket_number'],
            ])
            ->assertOk()
            ->assertJsonPath('ticket.ticket_number', $ticketData['ticket_number']);

        // An unknown number gets an actionable message, not a raw model error.
        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson(route('support.tickets.track'), [
                'ticket_number' => 'GT-260101-NOPE00',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('ticket_number');
    }

    public function test_ticket_uses_the_referring_page_when_source_url_is_not_posted(): void
    {
        Mail::fake();

        $ticketNumber = $this->withoutMiddleware(VerifyCsrfToken::class)
            ->withHeader('Referer', 'https://gotrips.ai/esim')
            ->postJson(route('support.tickets.store'), [
                'message' => 'The installation screen is not clear on my phone.',
                'name' => 'Test Traveller',
                'email' => 'source-test@example.com',
                'phone' => '+971500000011',
            ])
            ->assertCreated()
            ->json('ticket.ticket_number');

        $this->assertSame(
            'https://gotrips.ai/esim',
            SupportTicket::where('ticket_number', $ticketNumber)->value('source_url')
        );
    }

    public function test_manager_reply_records_first_response_and_customer_can_follow_up(): void
    {
        $ticketData = $this->createTicket();
        $ticket = SupportTicket::where('ticket_number', $ticketData['ticket_number'])->firstOrFail();
        $manager = User::factory()->create([
            'company_id' => $this->company()->id,
            'role' => 'company_admin',
            'access_type' => 'manager',
            'is_active' => true,
        ]);

        Mail::fake();
        $this->actingAs($manager)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post(route('manager.support.reply', $ticket), [
                'message' => 'Please open your order email and use the QR code under Installation.',
                'status' => 'waiting_customer',
                'assigned_to' => $manager->id,
            ])
            ->assertRedirect();

        $ticket->refresh();
        $this->assertNotNull($ticket->first_response_at);
        $this->assertSame($manager->id, $ticket->assigned_to);
        $this->assertSame('waiting_customer', $ticket->status);
        Mail::assertSent(fn (SupportTicketMail $mail) => $mail->kind === 'customer_reply'
            && $mail->ticket->is($ticket));

        $this->withoutMiddleware(VerifyCsrfToken::class)
            ->postJson(route('support.tickets.reply'), [
                'ticket_number' => $ticket->ticket_number,
                'message' => 'I found it, but my phone says the code has already been used.',
            ])
            ->assertOk()
            ->assertJsonPath('ticket.status', 'open');

        $this->assertSame(4, $ticket->fresh()->messages()->count());
    }

    public function test_manager_queue_and_dashboard_expose_support_performance(): void
    {
        $this->createTicket();
        $manager = User::factory()->create([
            'company_id' => $this->company()->id,
            'role' => 'company_admin',
            'access_type' => 'manager',
            'is_active' => true,
        ]);

        $this->actingAs($manager)->get(route('manager.support.index'))
            ->assertOk()
            ->assertSee('Awaiting first reply')
            ->assertSee('Awaiting API credentials');

        $this->actingAs($manager)->get(route('manager.dashboard'))
            ->assertOk()
            ->assertSee('Customer support operations')
            ->assertSee('Awaiting first reply');
    }
}
