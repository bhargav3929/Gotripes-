<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Mail\CustomerCareWelcomeMail;
use App\Models\SupportTicket;
use App\Models\User;
use App\Services\SupportTicketNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ManagerSupportTicketController extends Controller
{
    /** Roles that can be handed a ticket. */
    private const ASSIGNABLE_ROLES = ['company_owner', 'company_admin', 'customer_care'];

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
        $careStaff = $this->isAdmin()
            ? $company->users()->where('role', 'customer_care')->orderBy('name')->get()
            : collect();

        return view('manager.support.index', compact('tickets', 'stats', 'company', 'careStaff'));
    }

    public function show(SupportTicket $ticket)
    {
        $ticket->load(['messages.user', 'assignee']);
        $agents = current_company()->users()
            ->whereIn('role', self::ASSIGNABLE_ROLES)
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

        $previousAssigneeId = $ticket->assigned_to;
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
        $newAssignee = $this->newAssignee($previousAssigneeId, $fresh);
        $actor = auth()->user();
        dispatch(function () use ($notifier, $fresh, $message, $newAssignee, $actor) {
            $notifier->notifyStaffReply($fresh, $message);
            if ($newAssignee) {
                $notifier->notifyAssigned($fresh, $newAssignee, $actor);
            }
        })->afterResponse();

        return back()->with('success', "Reply sent on {$ticket->ticket_number}.");
    }

    public function update(Request $request, SupportTicket $ticket, SupportTicketNotifier $notifier)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(SupportTicket::STATUSES))],
            'priority' => ['required', Rule::in(array_keys(SupportTicket::PRIORITIES))],
            'assigned_to' => 'nullable|integer',
        ]);

        if (!empty($validated['assigned_to'] ?? null)) {
            abort_unless(current_company()->users()->whereKey($validated['assigned_to'])->exists(), 422);
        }

        $previousAssigneeId = $ticket->assigned_to;
        $ticket->update($validated + [
            'resolved_at' => in_array($validated['status'], ['resolved', 'closed'], true)
                ? ($ticket->resolved_at ?: now())
                : null,
        ]);

        $fresh = $ticket->fresh('company');
        if ($newAssignee = $this->newAssignee($previousAssigneeId, $fresh)) {
            $actor = auth()->user();
            dispatch(fn () => $notifier->notifyAssigned($fresh, $newAssignee, $actor))->afterResponse();
        }

        return back()->with('success', "Ticket {$ticket->ticket_number} updated.");
    }

    public function updateSettings(Request $request)
    {
        abort_unless($this->isAdmin(), 403);

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

    /**
     * Owner/admin creates a customer-care login. The temporary password is
     * emailed to the new staff member and flashed exactly once to the screen.
     */
    public function storeCustomerCare(Request $request): RedirectResponse
    {
        abort_unless($this->isAdmin(), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc|max:255|unique:users,email',
            'phone' => 'nullable|string|max:30',
        ]);

        $company = current_company();
        $password = Str::password(12);

        $staff = User::create([
            'name' => trim($validated['name']),
            'email' => strtolower(trim($validated['email'])),
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($password),
            'role' => 'customer_care',
            'access_type' => 'manager',
            'company_id' => $company->id,
            'is_active' => true,
        ]);

        $loginUrl = route('manager.login');
        $emailSent = true;
        try {
            Mail::to($staff->email, $staff->name)
                ->send(new CustomerCareWelcomeMail($staff, $password, $loginUrl, $company->name ?? config('app.name', 'GoTrips')));
        } catch (\Throwable $e) {
            $emailSent = false;
            Log::error('Customer care welcome email failed', [
                'user' => $staff->email,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('manager.support.index')
            ->with('success', $emailSent
                ? "Customer care login created for {$staff->name}. The login details were emailed to {$staff->email}."
                : "Customer care login created for {$staff->name}, but the welcome email could not be sent. Share the details below.")
            ->with('care_credentials', [
                'name' => $staff->name,
                'email' => $staff->email,
                'password' => $password,
                'url' => $loginUrl,
            ]);
    }

    /**
     * The user a ticket was just handed to, or null when the assignee did not
     * change, was cleared, or is the person making the change (no point
     * notifying yourself).
     */
    private function newAssignee(?int $previousAssigneeId, SupportTicket $ticket): ?User
    {
        $currentId = $ticket->assigned_to ? (int) $ticket->assigned_to : null;
        if (!$currentId || $currentId === (int) $previousAssigneeId || $currentId === (int) auth()->id()) {
            return null;
        }

        return User::find($currentId);
    }

    private function isAdmin(): bool
    {
        $user = auth()->user();

        return $user && ($user->is_super_admin || $user->isSuperAdmin() || $user->isCompanyAdmin());
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
