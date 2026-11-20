@extends('layouts.manager')

@section('title', 'Supplier forms')
@section('page-title', 'Supplier forms')

@push('styles')
<style>
    .sf-code { font-family: ui-monospace, Menlo, Consolas, monospace; font-size:15px; font-weight:700; letter-spacing:.06em; color:var(--wp-primary); white-space:nowrap; }
    .sf-msg { width:100%; min-height:96px; font-size:13px; }
    .sf-new { border:1px solid var(--wp-primary); border-radius:8px; padding:16px; margin-bottom:16px; }
    .sf-row-new td { background: rgba(255, 215, 0, .06); }
    .sf-steps { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:16px; font-size:13px; }
    .sf-steps strong { display:block; margin-bottom:4px; }
</style>
@endpush

@section('content')
@php
    $formUrl = route('suppliers.start');
    $message = fn ($inv) => "Hello {$inv->supplier_name},\n\nPlease send us your hotel and tour rates here:\n{$formUrl}\n\nYour access code: {$inv->display_code}\n\nUse the same code any time your prices change.";
    $newId = session('new_invite');
@endphp

<div class="wp-page-header">
    <div>
        <h1 class="wp-page-title">Supplier forms</h1>
        <p class="text-muted-wp" style="margin:4px 0 0;">Suppliers fill in their hotels, room prices, tour prices, extras and photos. You check it and fill a package in one click.</p>
    </div>
    <a href="{{ $formUrl }}" target="_blank" rel="noopener" class="wp-btn wp-btn-secondary"><i class="fas fa-external-link-alt"></i> See the supplier form</a>
</div>

<div class="wp-card">
    <div class="wp-card-header"><i class="fas fa-route text-secondary-wp"></i> How it works</div>
    <div class="wp-card-body sf-steps">
        <div><strong>1. Create a code</strong>One per supplier. Only people with a code can open the form.</div>
        <div><strong>2. Send it</strong>Copy the message, or send it on WhatsApp.</div>
        <div><strong>3. They fill it in</strong>You get an email and it shows under "Received" below.</div>
        <div><strong>4. Apply it</strong>Open it, check it, and fill a new or existing package with it.</div>
    </div>
</div>

<div class="wp-card">
    <div class="wp-card-header"><i class="fas fa-key text-secondary-wp"></i> Create an access code</div>
    <div class="wp-card-body">
        <form method="POST" action="{{ route('manager.supplier-forms.codes.store') }}" class="bs-grid" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:12px; align-items:end;">
            @csrf
            <div class="wp-form-group" style="margin:0;">
                <label class="wp-form-label" for="supplier_name">Supplier name</label>
                <input class="wp-input" id="supplier_name" name="supplier_name" value="{{ old('supplier_name') }}" required placeholder="e.g. Caucasus Travel DMC">
            </div>
            <div class="wp-form-group" style="margin:0;">
                <label class="wp-form-label" for="country">Country <small class="text-muted-wp">optional</small></label>
                <input class="wp-input" id="country" name="country" value="{{ old('country') }}" placeholder="e.g. Georgia">
            </div>
            <div class="wp-form-group" style="margin:0;">
                <label class="wp-form-label" for="package_id">For package <small class="text-muted-wp">optional</small></label>
                <select class="wp-input" id="package_id" name="package_id">
                    <option value="">A new package</option>
                    @foreach($packages as $p)
                        <option value="{{ $p->id }}" @selected(old('package_id') == $p->id)>{{ $p->title }} · {{ $p->country }}</option>
                    @endforeach
                </select>
            </div>
            <div class="wp-form-group" style="margin:0;">
                <label class="wp-form-label" for="expires_on">Code works until <small class="text-muted-wp">optional</small></label>
                <input type="date" class="wp-input" id="expires_on" name="expires_on" value="{{ old('expires_on') }}" min="{{ now()->addDay()->toDateString() }}">
            </div>
            <div><button class="wp-btn wp-btn-primary" style="width:100%;"><i class="fas fa-plus"></i> Create code</button></div>
        </form>
    </div>
</div>

<div class="wp-card">
    <div class="wp-card-header"><i class="fas fa-inbox text-secondary-wp"></i> Received <span class="text-muted-wp" style="font-weight:400;">· {{ $submissions->where('status', 'new')->count() }} to check</span></div>
    <div class="wp-card-body" style="padding:0;">
        @if($submissions->isEmpty())
            <p class="text-muted-wp" style="padding:24px; margin:0;">Nothing yet. When a supplier sends the form, it appears here and you get an email.</p>
        @else
            <div class="table-responsive">
                <table class="wp-table">
                    <thead><tr><th>Received</th><th>Supplier</th><th>Package</th><th>Hotels</th><th>Prices in</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach($submissions as $s)
                            <tr>
                                <td style="white-space:nowrap;">{{ $s->created_at->format('j M Y, H:i') }}</td>
                                <td>{{ $s->invite?->supplier_name ?? '–' }}<br><small class="text-muted-wp">{{ $s->get('company.contact') }}</small></td>
                                <td>{{ $s->get('package.name') }}<br><small class="text-muted-wp">{{ $s->get('package.country') }}</small></td>
                                <td>{{ collect($s->hotels())->keys()->sortDesc()->map(fn ($k) => $k . '★')->join(' ') ?: '–' }}</td>
                                <td>{{ $s->get('currency') }}</td>
                                <td>
                                    @if($s->status === 'applied')
                                        <span class="wp-badge wp-badge-green">Applied</span>
                                        @if($s->appliedPackage)<br><small class="text-muted-wp">to {{ $s->appliedPackage->title }}</small>@endif
                                    @else
                                        <span class="wp-badge wp-badge-amber">New</span>
                                    @endif
                                </td>
                                <td><a href="{{ route('manager.supplier-forms.show', $s->id) }}" class="wp-btn wp-btn-secondary wp-btn-sm">Open</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="wp-card">
    <div class="wp-card-header"><i class="fas fa-key text-secondary-wp"></i> Access codes</div>
    <div class="wp-card-body" style="padding:0;">
        @if($invites->isEmpty())
            <p class="text-muted-wp" style="padding:24px; margin:0;">No codes yet. Create one above for your first supplier.</p>
        @else
            <div class="table-responsive">
                <table class="wp-table">
                    <thead><tr><th>Supplier</th><th>Code</th><th>For</th><th>Sent forms</th><th>Status</th><th>Message to send</th><th></th></tr></thead>
                    <tbody>
                        @foreach($invites as $inv)
                            <tr class="{{ $newId === $inv->id ? 'sf-row-new' : '' }}">
                                <td>{{ $inv->supplier_name }}<br><small class="text-muted-wp">{{ $inv->country }}</small></td>
                                <td><span class="sf-code">{{ $inv->display_code }}</span></td>
                                <td>{{ $inv->package?->title ?? 'New package' }}</td>
                                <td>{{ $inv->submissions_count }}@if($inv->last_used_at)<br><small class="text-muted-wp">last {{ $inv->last_used_at->format('j M') }}</small>@endif</td>
                                <td>
                                    <span class="wp-badge {{ $inv->isUsable() ? 'wp-badge-green' : 'wp-badge-red' }}">{{ $inv->statusLabel() }}</span>
                                    @if($inv->expires_on && $inv->isUsable())<br><small class="text-muted-wp">until {{ $inv->expires_on->format('j M Y') }}</small>@endif
                                </td>
                                <td style="min-width:260px;">
                                    @if($inv->isUsable())
                                        <textarea class="wp-input sf-msg" readonly id="msg{{ $inv->id }}">{{ $message($inv) }}</textarea>
                                        <div style="display:flex; gap:8px; margin-top:8px;">
                                            <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" data-copy="msg{{ $inv->id }}"><i class="fas fa-copy"></i> Copy</button>
                                            <a class="wp-btn wp-btn-secondary wp-btn-sm" target="_blank" rel="noopener" href="https://wa.me/?text={{ rawurlencode($message($inv)) }}"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                                        </div>
                                    @else
                                        <small class="text-muted-wp">Turn the code on to send it again.</small>
                                    @endif
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('manager.supplier-forms.codes.toggle', $inv->id) }}">
                                        @csrf
                                        <button class="wp-btn wp-btn-secondary wp-btn-sm">{{ $inv->revoked_at ? 'Turn on' : 'Turn off' }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-copy]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var el = document.getElementById(btn.dataset.copy);
        var done = function () { btn.innerHTML = '<i class="fas fa-check"></i> Copied'; setTimeout(function () { btn.innerHTML = '<i class="fas fa-copy"></i> Copy'; }, 1800); };
        if (navigator.clipboard) { navigator.clipboard.writeText(el.value).then(done); } else { el.select(); document.execCommand('copy'); done(); }
    });
});
</script>
@endpush
