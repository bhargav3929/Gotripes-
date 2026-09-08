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
        // turn a successfully-created ticket into a customer-facing 500.
        $notifier->notifyCreated($ticket);

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
            'email' => 'required|email:rfc|max:255',
        ]);

        $ticket = $this->findForCustomer($validated['ticket_number'], $validated['email']);

        return response()->json(['ticket' => $this->publicTicket($ticket)]);
    }

    public function reply(Request $request, SupportTicketNotifier $notifier): JsonResponse
    {
        $validated = $request->validate([
            'ticket_number' => 'required|string|max:32',
            'email' => 'required|email:rfc|max:255',
            'message' => 'required|string|min:2|max:4000',
        ]);

        $ticket = $this->findForCustomer($validated['ticket_number'], $validated['email']);
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

        $notifier->notifyCustomerFollowup($ticket->fresh('company'), $message);

        return response()->json(['ticket' => $this->publicTicket($ticket->fresh('messages'))]);
    }

    private function findForCustomer(string $number, string $email): SupportTicket
    {
        return SupportTicket::with(['messages' => fn ($query) => $query->where('visibility', 'public')])
            ->where('ticket_number', strtoupper(trim($number)))
            ->whereRaw('LOWER(customer_email) = ?', [strtolower(trim($email))])
            ->firstOrFail();
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
