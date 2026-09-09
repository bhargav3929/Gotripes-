<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Services\SupportTicketNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ManagerSupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::with('assignee')
            ->orderByRaw("CASE
                WHEN first_response_at IS NULL AND status NOT IN ('resolved','closed') AND first_response_due_at < ? THEN 0
                WHEN first_response_at IS NULL AND status NOT IN ('resolved','closed') THEN 1
                ELSE 2 END", [now()])
            ->orderBy('created_at');

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($request->boolean('overdue')) {
            $query->whereNull('first_response_at')
                ->whereNotIn('status', ['resolved', 'closed'])
                ->where('first_response_due_at', '<', now());
        }

        $tickets = $query->paginate(25)->withQueryString();
        $stats = $this->stats();
        $company = current_company();

        return view('manager.support.index', compact('tickets', 'stats', 'company'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['messages.user', 'assignee']);
        $agents = current_company()->users()
            ->whereIn('role', ['company_owner', 'company_admin'])
            ->where(function ($query) {
                $query->where('is_active', true)->orWhereNull('is_active');
            })
            ->orderBy('name')->get();

        return view('manager.support.show', compact('ticket', 'agents'));
    }

    public function reply(Request $request, SupportTicket $ticket, SupportTicketNotifier $notifier)
    {
        $validated = $request->validate([
            'message' => 'required|string|min:2|max:4000',
            'status' => ['required', Rule::in(array_keys(SupportTicket::STATUSES))],
            'assigned_to' => 'nullable|integer',
        ]);

        if (!empty($validated['assigned_to'] ?? null)) {
            abort_unless(current_company()->users()->whereKey($validated['assigned_to'])->exists(), 422);
        }

        $message = trim($validated['message']);
        DB::transaction(function () use ($ticket, $validated, $message) {
            $now = now();
            $ticket->messages()->create([
                'company_id' => $ticket->company_id,
                'user_id' => auth()->id(),
                'sender_type' => 'staff',
                'sender_name' => auth()->user()->name,
                'sender_email' => auth()->user()->email,
                'visibility' => 'public',
                'message' => $message,
            ]);

            $ticket->update([
                'status' => $validated['status'],
                'assigned_to' => ($validated['assigned_to'] ?? null) ?: auth()->id(),
                'first_response_at' => $ticket->first_response_at ?: $now,
                'last_staff_response_at' => $now,
                'resolved_at' => in_array($validated['status'], ['resolved', 'closed'], true) ? $now : null,
            ]);
        });

        // Sent after the response for the same reason as the customer side:
        // synchronous SMTP otherwise stalls the manager's reply for seconds.
        $fresh = $ticket->fresh('company');
        dispatch(fn () => $notifier->notifyStaffReply($fresh, $message))->afterResponse();

        return back()->with('success', "Reply sent on {$ticket->ticket_number}.");
    }

    public function update(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(SupportTicket::STATUSES))],
            'priority' => ['required', Rule::in(array_keys(SupportTicket::PRIORITIES))],
            'assigned_to' => 'nullable|integer',
        ]);

        if (!empty($validated['assigned_to'] ?? null)) {
            abort_unless(current_company()->users()->whereKey($validated['assigned_to'])->exists(), 422);
        }

        $ticket->update($validated + [
            'resolved_at' => in_array($validated['status'], ['resolved', 'closed'], true)
                ? ($ticket->resolved_at ?: now())
                : null,
        ]);

        return back()->with('success', "Ticket {$ticket->ticket_number} updated.");
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'support_email' => 'nullable|email:rfc|max:255',
            'support_hours' => 'required|string|max:255',
            'support_sla_minutes' => 'required|integer|min:15|max:10080',
            'support_auto_response' => 'required|string|min:20|max:1000',
            'support_whatsapp_recipient' => 'nullable|string|max:40',
        ]);

        $company = current_company();
        foreach ($validated as $key => $value) {
            $company->setSetting($key, is_string($value) ? trim($value) : $value);
        }

        return back()->with('success', 'Support workflow settings saved.');
    }

    private function stats(): array
    {
        $active = SupportTicket::whereNotIn('status', ['resolved', 'closed']);
        $responded = SupportTicket::whereNotNull('first_response_at')->get(['created_at', 'first_response_at']);

        return [
            'active' => (clone $active)->count(),
            'awaiting' => (clone $active)->whereNull('first_response_at')->count(),
            'overdue' => (clone $active)->whereNull('first_response_at')
                ->where('first_response_due_at', '<', now())->count(),
            'resolved_30d' => SupportTicket::whereIn('status', ['resolved', 'closed'])
                ->where('resolved_at', '>=', now()->subDays(30))->count(),
            'average_first_response_minutes' => $responded->isEmpty()
                ? null
                : (int) round($responded->avg(fn ($ticket) => $ticket->created_at->diffInMinutes($ticket->first_response_at))),
        ];
    }
}
