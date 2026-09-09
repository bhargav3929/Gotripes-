<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Services\SupportTicketNotifier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupportTicketController extends Controller
{
    public function store(Request $request, SupportTicketNotifier $notifier): JsonResponse
    {
        abort_unless(current_company(), 404);

        $validated = $request->validate([
            'message' => 'required|string|min:10|max:4000',
            'name' => 'required|string|max:120',
            'email' => 'required|email:rfc|max:255',
            'phone' => 'required|string|min:7|max:40',
            'source_url' => 'nullable|string|max:2048',
        ]);

        $company = current_company();
        $slaMinutes = max(15, min(10080, (int) $company->getSetting(
            'support_sla_minutes',
            config('support.default_sla_minutes', 240)
        )));
        $message = trim($validated['message']);
        $sourceUrl = $validated['source_url'] ?? $request->headers->get('referer');
        $autoResponse = $company->getSetting('support_auto_response')
            ?: 'Thanks — your request has reached our support team. We will review it during our support hours and reply as soon as possible.';

        $ticket = DB::transaction(function () use ($validated, $company, $slaMinutes, $message, $sourceUrl, $autoResponse) {
            $ticket = SupportTicket::create([
                'company_id' => $company->id,
                'customer_name' => trim($validated['name']),
                'customer_email' => strtolower(trim($validated['email'])),
                'customer_phone' => trim($validated['phone']),
                'subject' => (string) str($message)->squish()->limit(120),
                'status' => 'new',
                'priority' => 'normal',
                'source_url' => $sourceUrl,
                'first_response_due_at' => now()->addMinutes($slaMinutes),
                'last_customer_message_at' => now(),
            ]);

            $ticket->messages()->create([
                'company_id' => $company->id,
                'sender_type' => 'customer',
                'sender_name' => $ticket->customer_name,
                'sender_email' => $ticket->customer_email,
                'visibility' => 'public',
                'message' => $message,
            ]);

            $ticket->messages()->create([
                'company_id' => $company->id,
                'sender_type' => 'system',
                'sender_name' => $company->name . ' Support',
                'visibility' => 'public',
                'message' => $autoResponse,
            ]);

            return $ticket;
        });

        // Ticket persistence is authoritative: notification trouble must never
        // turn a successfully-created ticket into a customer-facing 500. SMTP is
        // synchronous here (QUEUE_CONNECTION=sync), and two mails plus a WhatsApp
        // call added ~9s to the request, so send once the customer already has
        // the response. afterResponse needs no queue worker, which matters on
        // shared hosting. The notifier logs and swallows its own failures.
        dispatch(fn () => $notifier->notifyCreated($ticket))->afterResponse();

        return response()->json([
            'ticket' => $this->publicTicket($ticket->fresh('messages')),
            'support_hours' => $company->getSetting('support_hours', config('support.default_hours')),
            'response_target_minutes' => $slaMinutes,
        ], 201);
    }

    public function track(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ticket_number' => 'required|string|max:32',
        ]);

        $ticket = $this->findByNumber($validated['ticket_number']);

        return response()->json(['ticket' => $this->publicTicket($ticket)]);
    }

    public function reply(Request $request, SupportTicketNotifier $notifier): JsonResponse
    {
        $validated = $request->validate([
            'ticket_number' => 'required|string|max:32',
            'message' => 'required|string|min:2|max:4000',
        ]);

        $ticket = $this->findByNumber($validated['ticket_number']);
        if ($ticket->status === 'closed') {
            throw ValidationException::withMessages([
                'ticket_number' => 'This ticket is closed. Please create a new ticket if you still need help.',
            ]);
        }

        $message = trim($validated['message']);
        DB::transaction(function () use ($ticket, $message) {
            $ticket->messages()->create([
                'company_id' => $ticket->company_id,
                'sender_type' => 'customer',
                'sender_name' => $ticket->customer_name,
                'sender_email' => $ticket->customer_email,
                'visibility' => 'public',
                'message' => $message,
            ]);

            $ticket->update([
                'status' => 'open',
                'last_customer_message_at' => now(),
                'resolved_at' => null,
            ]);
        });

        $fresh = $ticket->fresh('company');
        dispatch(fn () => $notifier->notifyCustomerFollowup($fresh, $message))->afterResponse();

        return response()->json(['ticket' => $this->publicTicket($ticket->fresh('messages'))]);
    }

    /**
     * Resolve a ticket from the reference the customer was given.
     *
     * The ticket number is the bearer credential: it is 6 random characters on
     * top of the date, and the customer endpoints are rate limited, so guessing
     * one is impractical. Scoping still applies, so a number cannot be read
     * across tenants.
     */
    private function findByNumber(string $number): SupportTicket
    {
        $ticket = SupportTicket::with(['messages' => fn ($query) => $query->where('visibility', 'public')])
            ->where('ticket_number', strtoupper(trim($number)))
            ->first();

        if (!$ticket) {
            throw ValidationException::withMessages([
                'ticket_number' => 'We could not find that ticket number. Please check it and try again.',
            ]);
        }

        return $ticket;
    }

    private function publicTicket(SupportTicket $ticket): array
    {
        $ticket->loadMissing(['messages' => fn ($query) => $query->where('visibility', 'public')]);

        return [
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'status' => $ticket->status,
            'status_label' => SupportTicket::STATUSES[$ticket->status] ?? ucfirst($ticket->status),
            'created_at' => $ticket->created_at?->toIso8601String(),
            'updated_at' => $ticket->updated_at?->toIso8601String(),
            'messages' => $ticket->messages->map(fn ($message) => [
                'sender_type' => $message->sender_type,
                'sender_name' => $message->sender_name,
                'message' => $message->message,
                'created_at' => $message->created_at?->toIso8601String(),
            ])->values(),
        ];
    }
}
