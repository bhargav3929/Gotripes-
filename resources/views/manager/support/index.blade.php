@extends('layouts.manager')

@section('title', 'Support Tickets')
@section('page-title', 'Support Tickets')

@section('content')
<div class="support-kpis">
    <a class="support-kpi" href="{{ route('manager.support.index') }}">
        <span>Active</span><strong>{{ $stats['active'] }}</strong>
    </a>
    <a class="support-kpi" href="{{ route('manager.support.index', ['status' => 'new']) }}">
        <span>Awaiting first reply</span><strong>{{ $stats['awaiting'] }}</strong>
    </a>
    <a class="support-kpi support-kpi-danger" href="{{ route('manager.support.index', ['overdue' => 1]) }}">
        <span>Overdue</span><strong>{{ $stats['overdue'] }}</strong>
    </a>
    <div class="support-kpi">
        <span>Avg. first response</span>
        <strong>{{ $stats['average_first_response_minutes'] === null ? '—' : ($stats['average_first_response_minutes'].' min') }}</strong>
    </div>
    <div class="support-kpi">
        <span>Resolved · 30 days</span><strong>{{ $stats['resolved_30d'] }}</strong>
    </div>
</div>

<div class="wp-card">
    <div class="wp-card-header support-header">
        <span><i class="fas fa-headset"></i> Customer queue</span>
        <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" data-bs-toggle="collapse" data-bs-target="#supportSettings">
            <i class="fas fa-sliders-h"></i> Workflow settings
        </button>
    </div>
    <div class="collapse" id="supportSettings">
        <div class="wp-card-body support-settings">
            <form action="{{ route('manager.support.settings') }}" method="POST">
                @csrf
                <div class="support-settings-grid">
                    <label>Support email
                        <input class="wp-input" type="email" name="support_email" value="{{ old('support_email', $company->getSetting('support_email', $company->email)) }}" placeholder="support@example.com">
                    </label>
                    <label>First-response target (minutes)
                        <input class="wp-input" type="number" min="15" max="10080" name="support_sla_minutes" value="{{ old('support_sla_minutes', $company->getSetting('support_sla_minutes', config('support.default_sla_minutes'))) }}" required>
                    </label>
                    <label class="support-span-2">Support hours
                        <input class="wp-input" type="text" name="support_hours" value="{{ old('support_hours', $company->getSetting('support_hours', config('support.default_hours'))) }}" required>
                    </label>
                    <label class="support-span-2">Automatic acknowledgement
                        <textarea class="wp-textarea" name="support_auto_response" required>{{ old('support_auto_response', $company->getSetting('support_auto_response', 'Thanks — your request has reached our support team. We will review it during our support hours and reply as soon as possible.')) }}</textarea>
                    </label>
                    <label class="support-span-2">WhatsApp staff destination
                        <input class="wp-input" type="text" name="support_whatsapp_recipient" value="{{ old('support_whatsapp_recipient', $company->getSetting('support_whatsapp_recipient')) }}" placeholder="9715XXXXXXXX">
                        <small>Business Cloud API status: <strong>{{ config('support.whatsapp.enabled') && config('support.whatsapp.endpoint') && config('support.whatsapp.token') ? 'Configured' : 'Awaiting API credentials' }}</strong>.</small>
                    </label>
                </div>
                <button class="wp-btn wp-btn-primary" type="submit"><i class="fas fa-save"></i> Save support settings</button>
            </form>
        </div>
    </div>
    <div class="wp-card-body">
        <form class="support-filters" method="GET" action="{{ route('manager.support.index') }}">
            <input class="wp-input" type="search" name="q" value="{{ request('q') }}" placeholder="Ticket, customer, email, phone or issue…">
            <select class="wp-select" name="status">
                <option value="">All statuses</option>
                @foreach(\App\Models\SupportTicket::STATUSES as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <label class="support-check"><input type="checkbox" name="overdue" value="1" @checked(request('overdue'))> Overdue only</label>
            <button class="wp-btn wp-btn-primary" type="submit"><i class="fas fa-search"></i> Filter</button>
            @if(request()->hasAny(['q', 'status', 'overdue']))
                <a class="wp-btn wp-btn-secondary" href="{{ route('manager.support.index') }}">Clear</a>
            @endif
        </form>
    </div>

    <div style="overflow-x:auto;">
        <table class="wp-table support-table">
            <thead><tr>
                <th>Ticket</th><th>Customer</th><th>Issue</th><th>Status</th>
                <th>SLA</th><th>Assignee</th><th>Updated</th><th></th>
            </tr></thead>
            <tbody>
            @forelse($tickets as $ticket)
                <tr class="{{ $ticket->isOverdue() ? 'support-row-overdue' : '' }}">
                    <td><strong>{{ $ticket->ticket_number }}</strong><br><small>{{ ucfirst($ticket->priority) }} priority</small></td>
                    <td>{{ $ticket->customer_name }}<br><small>{{ $ticket->customer_email }}</small></td>
                    <td class="support-subject">{{ $ticket->subject }}</td>
                    <td><span class="support-status status-{{ $ticket->status }}">{{ \App\Models\SupportTicket::STATUSES[$ticket->status] ?? ucfirst($ticket->status) }}</span></td>
                    <td>
                        @if($ticket->first_response_at)
                            {{ $ticket->firstResponseMinutes() }} min
                        @elseif($ticket->isOverdue())
                            <strong class="support-overdue">Overdue {{ $ticket->first_response_due_at->diffForHumans() }}</strong>
                        @else
                            Due {{ $ticket->first_response_due_at?->diffForHumans() ?? '—' }}
                        @endif
                    </td>
                    <td>{{ $ticket->assignee?->name ?? 'Unassigned' }}</td>
                    <td>{{ $ticket->updated_at?->diffForHumans() }}</td>
                    <td><a class="wp-btn wp-btn-secondary wp-btn-sm" href="{{ route('manager.support.show', $ticket) }}">Open</a></td>
                </tr>
            @empty
                <tr class="empty-row"><td colspan="8">No support tickets match this view.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($tickets->hasPages())
        <div class="wp-card-footer">{{ $tickets->links() }}</div>
    @endif
</div>

<style>
.support-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:18px}.support-kpi{background:#222;border:1px solid var(--wp-border);border-radius:8px;padding:14px 16px;color:#ddd;text-decoration:none}.support-kpi:hover{color:#fff;border-color:var(--wp-primary)}.support-kpi span{display:block;color:#aaa;font-size:11px;text-transform:uppercase;letter-spacing:.4px}.support-kpi strong{display:block;color:#fff;font-size:22px;margin-top:4px}.support-kpi-danger strong,.support-overdue{color:#f87171}.support-header{justify-content:space-between}.support-settings{background:#181818;border-bottom:1px solid var(--wp-border-light)}.support-settings-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px 18px;margin-bottom:14px}.support-settings label{color:#eee;font-weight:600}.support-settings label input,.support-settings label textarea{display:block;margin-top:5px}.support-settings small{display:block;color:#999;font-weight:400;margin-top:5px}.support-span-2{grid-column:1/-1}.support-filters{display:grid;grid-template-columns:minmax(240px,1fr) 190px auto auto auto;gap:10px;align-items:center}.support-check{display:flex;align-items:center;gap:7px;color:#ccc;white-space:nowrap}.support-subject{max-width:300px}.support-table small{color:#999}.support-status{display:inline-flex;border-radius:99px;padding:3px 9px;font-size:11px;font-weight:700;white-space:nowrap}.status-new{background:rgba(239,68,68,.16);color:#fca5a5}.status-open{background:rgba(59,130,246,.16);color:#93c5fd}.status-waiting_customer{background:rgba(245,158,11,.16);color:#fcd34d}.status-resolved,.status-closed{background:rgba(34,197,94,.16);color:#86efac}.support-row-overdue{box-shadow:inset 3px 0 #ef4444}@media(max-width:800px){.support-settings-grid{grid-template-columns:1fr}.support-span-2{grid-column:auto}.support-filters{grid-template-columns:1fr}.support-table{min-width:980px}}
</style>
@endsection
