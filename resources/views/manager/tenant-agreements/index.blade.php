@extends('layouts.manager')

@section('title', 'Tenant Agreements')
@section('page-title', 'Tenant Agreements')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Tenant Agreements</h1>
</div>

<style>
    .gt-card {
        border: 1px solid rgba(255, 215, 0, 0.35);
        border-radius: 10px;
        box-shadow: 0 0 0 1px rgba(255, 215, 0, 0.06), 0 0 40px rgba(255, 215, 0, 0.08), 0 16px 40px rgba(0, 0, 0, 0.45);
        overflow: hidden;
    }
    .ta-intro { color: var(--wp-text-muted); font-size: 13px; max-width: 680px; margin: -8px 0 24px; line-height: 1.6; }
    .ta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 16px; }
    .ta-grid .span-2 { grid-column: 1 / -1; }
    .ta-section { font-size: 11px; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--wp-primary); margin: 8px 0 16px; }
    .ta-section + .ta-grid ~ .ta-section { margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--wp-border-light); }
    .ta-blank-note {
        display: flex; gap: 12px; align-items: flex-start;
        border: 1px dashed rgba(255, 215, 0, .4); border-radius: 8px; padding: 12px 16px;
        font-size: 13px; color: var(--wp-text-muted); margin: 8px 0 24px;
    }
    .ta-blank-note i { color: var(--wp-primary); margin-top: 3px; }
    .ta-modules { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 8px; }
    .ta-modules label {
        display: flex; gap: 8px; align-items: center; font-size: 13px; cursor: pointer;
        border: 1px solid var(--wp-border-light); border-radius: 8px; padding: 8px 12px;
        transition: border-color .18s ease, background-color .18s ease;
    }
    .ta-modules label:hover { border-color: rgba(255, 215, 0, .45); }
    .ta-modules input:checked + span { color: var(--wp-primary); }
    .ta-ready { display: flex; gap: 16px; align-items: flex-start; flex-wrap: wrap; }
    .ta-ready .dot { width: 10px; height: 10px; border-radius: 50%; margin-top: 6px; background: #35b37e; box-shadow: 0 0 0 4px rgba(53, 179, 126, .15); flex: 0 0 auto; }
    .ta-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px; }
    .wp-btn-whatsapp { background: #1f8f4e; border-color: #1f8f4e; color: #fff; }
    .wp-btn-whatsapp:hover { background: #18733e; border-color: #18733e; color: #fff; }
    .ta-row { padding: 12px 0; border-bottom: 1px solid var(--wp-border-light); }
    .ta-row:last-child { border-bottom: none; }
    .ta-row .ref { font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-size: 12px; color: var(--wp-primary); }
    .ta-row .meta { font-size: 12px; color: var(--wp-text-muted); margin-top: 4px; }
    .ta-row .ta-actions { margin-top: 8px; }
    .ta-empty { text-align: center; padding: 24px 8px; color: var(--wp-text-muted); font-size: 13px; }
    .ta-empty i { font-size: 24px; color: rgba(255, 215, 0, .45); display: block; margin-bottom: 8px; }
    @media (max-width: 575px) {
        .ta-grid { grid-template-columns: 1fr; }
        .ta-actions .wp-btn { flex: 1 1 auto; justify-content: center; }
    }
</style>

<p class="ta-intro">
    Agreement for a partner company that gets its own GoTrips portal. Fill in their details and generate the PDF,
    then send it on WhatsApp. Monthly rent and the percentage on sales are printed as blank lines for you to
    write in by hand.
</p>

{{-- session('success') is already printed by layouts.manager --}}
@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
@endif

@if($generated)
    <div class="wp-card gt-card">
        <div class="wp-card-body">
            <div class="ta-ready">
                <span class="dot"></span>
                <div style="flex:1; min-width:0;">
                    <strong>{{ $generated->partner_name }}</strong>
                    <span class="wp-badge wp-badge-amber" style="margin-left:6px;">{{ $generated->reference }}</span>
                    <p class="wp-form-help mb-0">
                        Your download should start. Open WhatsApp, attach the PDF to the chat, then send.
                    </p>
                    <div class="ta-actions">
                        <a href="{{ route('manager.tenant-agreements.download', $generated) }}" class="wp-btn wp-btn-primary" id="taAutoDownload">
                            <i class="fas fa-file-pdf"></i> Download PDF
                        </a>
                        <a href="{{ $generated->whatsappUrl() }}" target="_blank" rel="noopener" class="wp-btn wp-btn-whatsapp">
                            <i class="fab fa-whatsapp"></i> Send on WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row">
    <div class="col-lg-7">
        <div class="wp-card gt-card">
            <div class="wp-card-header"><i class="fas fa-file-contract text-secondary-wp"></i> New agreement</div>
            <div class="wp-card-body">
                <form action="{{ route('manager.tenant-agreements.store') }}" method="POST" novalidate>
                    @csrf

                    <div class="ta-section">Partner company</div>
                    <div class="ta-grid">
                        <div class="wp-form-group span-2">
                            <label class="wp-form-label" for="legal_name">Legal company name <span class="required">*</span></label>
                            <input type="text" id="legal_name" name="legal_name" class="wp-input" required maxlength="200"
                                   value="{{ old('legal_name') }}" placeholder="As shown on the trade licence">
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label" for="trade_licence">Trade licence no.</label>
                            <input type="text" id="trade_licence" name="trade_licence" class="wp-input" maxlength="100"
                                   value="{{ old('trade_licence') }}">
                            <p class="wp-form-help">Leave empty to print a blank line.</p>
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label" for="country">Country <span class="required">*</span></label>
                            <input type="text" id="country" name="country" class="wp-input" required maxlength="100"
                                   value="{{ old('country', 'United Arab Emirates') }}">
                        </div>
                        <div class="wp-form-group span-2">
                            <label class="wp-form-label" for="address">Registered address <span class="required">*</span></label>
                            <input type="text" id="address" name="address" class="wp-input" required maxlength="500"
                                   value="{{ old('address') }}" placeholder="Office, building, street, city">
                        </div>
                    </div>

                    <div class="ta-section">Contact</div>
                    <div class="ta-grid">
                        <div class="wp-form-group">
                            <label class="wp-form-label" for="contact_name">Contact person <span class="required">*</span></label>
                            <input type="text" id="contact_name" name="contact_name" class="wp-input" required maxlength="150"
                                   value="{{ old('contact_name') }}">
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label" for="contact_title">Job title</label>
                            <input type="text" id="contact_title" name="contact_title" class="wp-input" maxlength="100"
                                   value="{{ old('contact_title') }}" placeholder="e.g. Managing Director">
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label" for="email">Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" class="wp-input" required maxlength="190"
                                   value="{{ old('email') }}">
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label" for="phone">Phone / WhatsApp <span class="required">*</span></label>
                            <input type="tel" id="phone" name="phone" class="wp-input" required maxlength="30"
                                   value="{{ old('phone') }}" placeholder="50 123 4567">
                            <p class="wp-form-help">Pick the country first, so WhatsApp can open the chat.</p>
                        </div>
                    </div>

                    <div class="ta-section">Portal and term</div>
                    <div class="ta-grid">
                        <div class="wp-form-group span-2">
                            <label class="wp-form-label" for="portal_domain">Portal domain or sub-domain</label>
                            <input type="text" id="portal_domain" name="portal_domain" class="wp-input" maxlength="190"
                                   value="{{ old('portal_domain') }}" placeholder="e.g. partnername.gotrips.ai or www.partner.com">
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label" for="start_date">Start date <span class="required">*</span></label>
                            <input type="date" id="start_date" name="start_date" class="wp-input" required
                                   value="{{ old('start_date', now()->toDateString()) }}">
                        </div>
                        <div class="wp-form-group">
                            <label class="wp-form-label" for="term_months">Term <span class="required">*</span></label>
                            <select id="term_months" name="term_months" class="wp-select" required>
                                @foreach($terms as $months => $label)
                                    <option value="{{ $months }}" @selected((int) old('term_months', 12) === $months)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="wp-form-group span-2">
                            <label class="wp-form-label">Modules to enable</label>
                            <div class="ta-modules">
                                @foreach($modules as $key => $label)
                                    <label>
                                        <input type="checkbox" name="modules[]" value="{{ $key }}"
                                               @checked(in_array($key, old('modules', ['tours']), true))>
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="ta-blank-note">
                        <i class="fas fa-pen-nib"></i>
                        <div>Monthly rent (AED) and percentage on sales are left blank in the PDF, in clause 3 and in Schedule 1. Write them in by hand before it is signed.</div>
                    </div>

                    <button type="submit" class="wp-btn wp-btn-primary">
                        <i class="fas fa-file-pdf"></i> Generate PDF
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="wp-card gt-card">
            <div class="wp-card-header"><i class="fas fa-layer-group text-secondary-wp"></i> Generated</div>
            <div class="wp-card-body">
                @forelse($agreements as $agreement)
                    <div class="ta-row">
                        <span class="ref">{{ $agreement->reference }}</span>
                        <div><strong>{{ $agreement->partner_name }}</strong></div>
                        <div class="meta">
                            {{ $agreement->field('contact_name') }} · {{ $agreement->field('country') }}
                            · {{ $agreement->created_at->format('d M Y') }}
                            · {{ optional($agreement->author)->name ?? 'Unknown' }}
                        </div>
                        <div class="ta-actions">
                            <a href="{{ route('manager.tenant-agreements.download', $agreement) }}" class="wp-btn wp-btn-secondary wp-btn-sm">
                                <i class="fas fa-download"></i> PDF
                            </a>
                            <a href="{{ $agreement->whatsappUrl() }}" target="_blank" rel="noopener" class="wp-btn wp-btn-secondary wp-btn-sm">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="ta-empty">
                        <i class="fas fa-handshake"></i>
                        No agreements yet. The first one you generate is listed here, so you can download it or send it again later.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@if($generated)
<script>
    // Start the download straight after generating, so staff do not have to
    // click twice. The button stays for when the browser blocks it.
    (function () {
        const link = document.getElementById('taAutoDownload');
        if (link) window.setTimeout(() => { window.location.href = link.href; }, 300);
    })();
</script>
@endif
@endsection
