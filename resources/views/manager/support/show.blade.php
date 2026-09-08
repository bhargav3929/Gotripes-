@extends('layouts.manager')

@section('title', $ticket->ticket_number)
@section('page-title', 'Support Ticket '.$ticket->ticket_number)

@section('content')
<div class="ticket-toolbar">
    <a href="{{ route('manager.support.index') }}" class="wp-btn wp-btn-secondary"><i class="fas fa-arrow-left"></i> Queue</a>
    @if($ticket->isOverdue())<span class="ticket-alert"><i class="fas fa-clock"></i> First response overdue</span>@endif
</div>

<div class="ticket-layout">
    <section class="wp-card">
        <div class="wp-card-header"><i class="fas fa-comments"></i> Conversation</div>
        <div class="wp-card-body ticket-thread">
            @foreach($ticket->messages as $message)
                <article class="ticket-message ticket-message-{{ $message->sender_type }}">
                    <header>
                        <strong>{{ $message->sender_name ?: ucfirst($message->sender_type) }}</strong>
                        <span>{{ $message->created_at?->format('d M Y, H:i') }}</span>
                    </header>
                    <div>{{ $message->message }}</div>
                </article>
            @endforeach
        </div>
        @if($ticket->status !== 'closed')
            <div class="wp-card-footer">
                <form action="{{ route('manager.support.reply', $ticket) }}" method="POST">
                    @csrf
                    <label class="wp-form-label" for="replyMessage">Reply to {{ $ticket->customer_name }}</label>
                    <textarea id="replyMessage" class="wp-textarea" name="message" rows="6" required placeholder="Write a clear update and next step…">{{ old('message') }}</textarea>
                    <div class="reply-actions">
                        <select class="wp-select" name="status" required>
                            @foreach(\App\Models\SupportTicket::STATUSES as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', 'waiting_customer') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <select class="wp-select" name="assigned_to">
                            <option value="">Assign to me</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" @selected(old('assigned_to', $ticket->assigned_to) == $agent->id)>{{ $agent->name }}</option>
                            @endforeach
                        </select>
                        <button class="wp-btn wp-btn-primary" type="submit"><i class="fas fa-paper-plane"></i> Send reply</button>
                    </div>
                </form>
            </div>
        @endif
    </section>

    <aside>
        <div class="wp-card">
            <div class="wp-card-header"><i class="fas fa-user"></i> Customer</div>
            <div class="wp-card-body ticket-meta">
                <dl>
                    <dt>Name</dt><dd>{{ $ticket->customer_name }}</dd>
                    <dt>Email</dt><dd><a href="mailto:{{ $ticket->customer_email }}">{{ $ticket->customer_email }}</a></dd>
                    <dt>Phone</dt><dd><a href="tel:{{ $ticket->customer_phone }}">{{ $ticket->customer_phone }}</a></dd>
                    <dt>Source page</dt><dd class="source-url">{{ $ticket->source_url ?: 'Not recorded' }}</dd>
                    <dt>Created</dt><dd>{{ $ticket->created_at?->format('d M Y, H:i') }}</dd>
                    <dt>First response</dt><dd>{{ $ticket->first_response_at ? $ticket->firstResponseMinutes().' minutes' : 'Waiting' }}</dd>
                    <dt>Email routing</dt><dd>{{ ucfirst($ticket->email_delivery_status) }}</dd>
                    <dt>WhatsApp routing</dt><dd>{{ $ticket->whatsapp_delivery_status === 'not_configured' ? 'Awaiting API setup' : ucfirst($ticket->whatsapp_delivery_status) }}</dd>
                </dl>
            </div>
        </div>

        <div class="wp-card">
            <div class="wp-card-header"><i class="fas fa-sliders-h"></i> Ticket controls</div>
            <div class="wp-card-body">
                <form action="{{ route('manager.support.update', $ticket) }}" method="POST">
                    @csrf
                    <div class="wp-form-group"><label class="wp-form-label">Status</label><select class="wp-select" name="status">
                        @foreach(\App\Models\SupportTicket::STATUSES as $value => $label)<option value="{{ $value }}" @selected($ticket->status === $value)>{{ $label }}</option>@endforeach
                    </select></div>
                    <div class="wp-form-group"><label class="wp-form-label">Priority</label><select class="wp-select" name="priority">
                        @foreach(\App\Models\SupportTicket::PRIORITIES as $value => $label)<option value="{{ $value }}" @selected($ticket->priority === $value)>{{ $label }}</option>@endforeach
                    </select></div>
                    <div class="wp-form-group"><label class="wp-form-label">Assignee</label><select class="wp-select" name="assigned_to"><option value="">Unassigned</option>
                        @foreach($agents as $agent)<option value="{{ $agent->id }}" @selected($ticket->assigned_to == $agent->id)>{{ $agent->name }}</option>@endforeach
                    </select></div>
                    <button type="submit" class="wp-btn wp-btn-primary">Update ticket</button>
                </form>
            </div>
        </div>
    </aside>
</div>

<style>
.ticket-toolbar{display:flex;gap:12px;align-items:center;margin-bottom:16px}.ticket-alert{color:#fca5a5;background:rgba(239,68,68,.14);border:1px solid rgba(239,68,68,.35);padding:6px 10px;border-radius:6px}.ticket-layout{display:grid;grid-template-columns:minmax(0,1fr) 330px;gap:18px}.ticket-thread{display:flex;flex-direction:column;gap:14px;max-height:62vh;overflow:auto}.ticket-message{max-width:82%;border-radius:12px;padding:12px 14px;white-space:pre-wrap}.ticket-message header{display:flex;justify-content:space-between;gap:18px;font-size:11px;margin-bottom:6px}.ticket-message header span{opacity:.65;white-space:nowrap}.ticket-message-customer{align-self:flex-start;background:#333;border:1px solid rgba(255,255,255,.1)}.ticket-message-staff{align-self:flex-end;background:rgba(255,215,0,.12);border:1px solid rgba(255,215,0,.3)}.ticket-message-system{align-self:center;max-width:92%;background:rgba(114,174,230,.1);border:1px solid rgba(114,174,230,.25);color:#cbd5e1}.reply-actions{display:grid;grid-template-columns:190px 1fr auto;gap:10px;margin-top:10px}.ticket-meta dl{margin:0}.ticket-meta dt{color:#888;font-size:11px;text-transform:uppercase;margin-top:12px}.ticket-meta dt:first-child{margin-top:0}.ticket-meta dd{color:#eee;margin:2px 0 0}.ticket-meta a{color:#FFD700}.source-url{overflow-wrap:anywhere}@media(max-width:900px){.ticket-layout{grid-template-columns:1fr}.reply-actions{grid-template-columns:1fr}.ticket-message{max-width:95%}}
</style>
@endsection
