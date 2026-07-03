@include('header')

<!-- Tom Select (searchable select dropdown) -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

@php
if (empty($countries)) {
    $countries = [];
    foreach (\App\Support\CountryCodes::all() as $c) {
        $countries[] = [
            'code' => $c['iso'],
            'name' => $c['name'],
            'flag' => $c['flag'],
            'types' => 1
        ];
    }
}
if (empty($nationalities)) {
    $nationalities = [];
    foreach (\App\Support\CountryCodes::all() as $c) {
        $nationalities[] = [
            'code' => $c['iso'],
            'name' => $c['name']
        ];
    }
}
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    .evisa * { box-sizing: border-box; }
    .evisa {
        --c-gold: #FFD700;
        --c-card: #0b0b0b;
        --c-input: #111;
        --c-border: #222;
        --c-muted: #8a8a8a;
        --c-light: #eee;
        background: linear-gradient(180deg, #000 0%, #0a0a0a 100%);
        padding: 24px 24px 48px;
        font-family: 'Outfit', sans-serif;
        color: var(--c-light);
    }
    .evisa-wrap { max-width: 1140px; margin: 0 auto; }
    .evisa-head { margin-bottom: 16px; }

    /* --- Tom Select Custom Styling --- */
    .ts-wrapper.evisa-select {
        padding: 0 !important;
        border: none !important;
        background: transparent !important;
    }
    .ts-wrapper.evisa-select .ts-control {
        background: var(--c-input) !important;
        border: 1px solid var(--c-border) !important;
        border-radius: 9px !important;
        padding: 0 12px !important;
        color: #fff !important;
        font-family: inherit !important;
        font-size: 0.9rem !important;
        height: 41px !important;
        display: flex !important;
        align-items: center !important;
        cursor: pointer !important;
        box-shadow: none !important;
        transition: all 0.2s ease;
        position: relative !important;
    }
    .ts-wrapper.evisa-select.focus .ts-control {
        border-color: var(--c-gold) !important;
        box-shadow: 0 0 0 3px rgba(255,215,0,.12) !important;
    }
    .ts-wrapper.evisa-select .ts-control .item {
        color: #fff !important;
        line-height: 39px !important;
        height: 39px !important;
        display: inline-flex !important;
        align-items: center !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .ts-wrapper.evisa-select .ts-control input {
        color: #fff !important;
        font-family: inherit !important;
        font-size: 0.9rem !important;
        padding: 0 !important;
        line-height: 39px !important;
        height: 39px !important;
        margin: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
    }
    html[data-theme="light"] .ts-wrapper.evisa-select .ts-control .item {
        color: var(--gt-text) !important;
    }
    .ts-wrapper.evisa-select.single .ts-control:after {
        border-color: var(--c-gold) transparent transparent transparent !important;
        border-width: 5px 4px 0 4px !important;
        right: 15px !important;
    }
    .ts-wrapper.evisa-select.single.dropdown-active .ts-control:after {
        border-color: transparent transparent var(--c-gold) transparent !important;
        border-width: 0 4px 5px 4px !important;
    }
    .ts-dropdown {
        background: #0d0d0d !important;
        border: 1px solid #2a2a2a !important;
        border-radius: 10px !important;
        box-shadow: 0 20px 60px rgba(0,0,0,0.7) !important;
        margin-top: 4px !important;
        z-index: 1000 !important;
        padding: 6px 0 !important;
    }
    .ts-dropdown .option {
        padding: 8px 14px !important;
        color: #ddd !important;
        font-family: 'Outfit', sans-serif !important;
        font-size: 14px !important;
        cursor: pointer !important;
    }
    .ts-dropdown .active,
    .ts-dropdown .option:hover {
        background: rgba(255, 215, 0, 0.12) !important;
        color: #fff !important;
    }
    .ts-dropdown .no-results {
        color: #888 !important;
    }

    /* --- Tom Select Light Mode Theme Overrides --- */
    html[data-theme="light"] .ts-wrapper.evisa-select .ts-control {
        background: var(--gt-surface) !important;
        border: 1px solid var(--gt-border-strong) !important;
        color: var(--gt-text) !important;
    }
    html[data-theme="light"] .ts-wrapper.evisa-select.focus .ts-control {
        border-color: var(--gt-gold-2) !important;
        background: #ffffff !important;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.18) !important;
    }
    html[data-theme="light"] .ts-wrapper.evisa-select .ts-control input {
        color: var(--gt-text) !important;
    }
    html[data-theme="light"] .ts-wrapper.evisa-select.single .ts-control:after {
        border-color: var(--gt-gold) transparent transparent transparent !important;
    }
    html[data-theme="light"] .ts-wrapper.evisa-select.single.dropdown-active .ts-control:after {
        border-color: transparent transparent var(--gt-gold) transparent !important;
    }
    html[data-theme="light"] .ts-dropdown {
        background: var(--gt-surface) !important;
        border: 1px solid var(--gt-border-strong) !important;
        box-shadow: var(--gt-shadow-lg) !important;
    }
    html[data-theme="light"] .ts-dropdown .option {
        color: var(--gt-text-body) !important;
    }
    html[data-theme="light"] .ts-dropdown .active,
    html[data-theme="light"] .ts-dropdown .option:hover {
        background: var(--gt-gold-soft) !important;
        color: var(--gt-gold) !important;
    }
    html[data-theme="light"] .ts-dropdown .no-results {
        color: var(--gt-text-muted) !important;
    }
    .evisa-head h1 { font-size: clamp(1.6rem, 4vw, 2.3rem); font-weight: 800; margin: 0; color: #fff; }
    .evisa-head h1 span { color: var(--c-gold); }
    .evisa-head p { color: var(--c-muted); font-weight: 300; font-size: 0.9rem; margin: 6px 0 0; }

    .evisa-alert { display: none; margin-bottom: 14px; padding: 11px 15px; border-radius: 9px; font-size: 0.86rem; border: 1px solid; }
    .evisa-alert.show { display: block; background: rgba(220,38,38,.1); border-color: rgba(220,38,38,.4); color: #fca5a5; }

    .evisa-main { display: grid; grid-template-columns: 1fr 340px; gap: 18px; align-items: start; }
    .evisa-card { background: var(--c-card); border: 1px solid var(--c-border); border-radius: 14px; padding: 16px 18px; margin-bottom: 16px; }
    .evisa-section-title { font-size: 0.72rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--c-gold); margin: 0 0 12px; }

    .evisa-grid { display: grid; gap: 10px 14px; }
    .evisa-grid.g3 { grid-template-columns: repeat(3, 1fr); }
    .evisa-grid.g2 { grid-template-columns: 1fr 1fr; }
    .evisa-field { min-width: 0; }
    .evisa-field label { display: block; font-size: 0.74rem; font-weight: 500; color: var(--c-muted); margin-bottom: 5px; }
    .evisa-field label .req { color: var(--c-gold); }
    .evisa-input, .evisa-select, .evisa-textarea {
        width: 100%; background: var(--c-input); border: 1px solid var(--c-border); border-radius: 9px;
        padding: 10px 12px; color: #fff; font-size: 0.9rem; font-family: inherit;
    }
    .evisa-textarea { min-height: 74px; resize: vertical; }
    .evisa-input:focus, .evisa-select:focus, .evisa-textarea:focus { outline: none; border-color: var(--c-gold); box-shadow: 0 0 0 3px rgba(255,215,0,.12); }
    .evisa-input::placeholder { color: #555; }

    .evisa-file {
        display: flex; align-items: center; gap: 11px; background: var(--c-input); border: 1px dashed #333;
        border-radius: 10px; padding: 11px 13px; cursor: pointer; transition: .15s;
    }
    .evisa-file:hover { border-color: var(--c-gold); background: #141414; }
    .evisa-file input { display: none; }
    .evisa-file i { color: var(--c-gold); font-size: 1.05rem; flex: none; }
    .evisa-file .lab { font-size: 0.82rem; color: var(--c-light); font-weight: 500; display: block; }
    .evisa-file .hint { display: block; font-size: 0.68rem; color: var(--c-muted); }
    .evisa-file.has-file { border-color: var(--c-gold); border-style: solid; }

    /* Visa-type option list */
    .evisa-types { display: grid; gap: 10px; }
    .evisa-type {
        display: flex; align-items: center; gap: 12px; border: 1px solid var(--c-border); border-radius: 11px;
        padding: 12px 14px; cursor: pointer; transition: .15s; background: #0e0e0e;
    }
    .evisa-type:hover { border-color: #3a3a3a; }
    .evisa-type.sel { border-color: var(--c-gold); background: rgba(255,215,0,.05); }
    .evisa-type input { accent-color: var(--c-gold); width: 18px; height: 18px; flex: none; }
    .evisa-type .t-name { font-weight: 600; color: #fff; font-size: 0.92rem; }
    .evisa-type .t-meta { font-size: 0.74rem; color: var(--c-muted); margin-top: 2px; display: block; }
    .evisa-type .t-price { margin-left: auto; font-weight: 800; color: var(--c-gold); font-size: 1.1rem; white-space: nowrap; }

    /* Light Theme overrides */
    html[data-theme="light"] .evisa-type {
        background: var(--gt-surface);
        border-color: var(--gt-border-strong);
    }
    html[data-theme="light"] .evisa-type:hover {
        border-color: var(--gt-gold);
    }
    html[data-theme="light"] .evisa-type.sel {
        border-color: var(--gt-gold);
        background: var(--gt-gold-soft);
    }
    html[data-theme="light"] .evisa-type .t-name {
        color: var(--gt-text) !important;
    }

    .evisa-muted-note { color: var(--c-muted); font-size: 0.82rem; }
    .evisa-scheme-sec { margin-top: 6px; }
    .evisa-scheme-sec > h4 { font-size: 0.82rem; color: #fff; font-weight: 600; margin: 14px 0 9px; padding-top: 12px; border-top: 1px solid #1a1a1a; }
    .evisa-checks { display: flex; flex-wrap: wrap; gap: 8px 16px; }
    .evisa-checks label { display: inline-flex; align-items: center; gap: 6px; font-size: 0.82rem; color: var(--c-light); }

    .evisa-sum-card { position: sticky; top: 16px; }
    .evisa-sum-list { list-style: none; margin: 0 0 14px; padding: 0; display: flex; flex-direction: column; gap: 9px; }
    .evisa-sum-list li { display: flex; align-items: center; gap: 9px; font-size: 0.83rem; color: #d6d6d6; }
    .evisa-sum-list li i { color: var(--c-gold); width: 18px; text-align: center; flex: none; }
    .evisa-fee { display: flex; align-items: baseline; justify-content: space-between; padding: 13px 0; border-top: 1px solid var(--c-border); }
    .evisa-fee .lbl { color: var(--c-muted); font-size: 0.82rem; }
    .evisa-fee .amt { font-size: 1.8rem; font-weight: 800; color: var(--c-gold); }
    .evisa-submit {
        width: 100%; background: var(--c-gold); color: #1a1a1a; border: none; border-radius: 10px;
        padding: 13px; font-weight: 700; font-size: 0.96rem; cursor: pointer; transition: .15s; font-family: inherit;
    }
    .evisa-submit:hover:not(:disabled) { transform: translateY(-1px); }
    .evisa-submit:disabled { opacity: .5; cursor: not-allowed; }
    .evisa-note { margin: 10px 0 0; font-size: 0.7rem; color: var(--c-muted); text-align: center; line-height: 1.4; }
    .evisa-hint-empty { color: var(--c-muted); font-size: 0.85rem; padding: 4px 0; }
    .spin { display:inline-block; width:13px; height:13px; border:2px solid rgba(0,0,0,.3); border-top-color:#1a1a1a; border-radius:50%; animation: evspin .7s linear infinite; vertical-align:-1px; }
    @keyframes evspin { to { transform: rotate(360deg); } }

    @media (max-width: 860px) {
        .evisa-main { grid-template-columns: 1fr; }
        .evisa-grid.g3, .evisa-grid.g2 { grid-template-columns: 1fr; }
        .evisa-sum-card { position: static; }
    }
</style>

<div class="evisa">
    <div class="evisa-wrap">
        <div class="evisa-head">
            <h1>Apply for an <span>e-Visa</span></h1>
            <p>{{ count($countries) }} countries · government-approved e-Visas · complete everything in one screen.</p>
        </div>

        <div id="evisaAlert" class="evisa-alert"></div>

        @if(empty($countries))
            <div class="evisa-card"><p class="evisa-hint-empty">The visa service is being set up. Please check back shortly or contact support.</p></div>
        @else
        <form id="evisaForm" class="evisa-main" enctype="multipart/form-data" novalidate>
            @csrf

            <div>
                {{-- Trip --}}
                <div class="evisa-card">
                    <p class="evisa-section-title">Your trip</p>
                    <div class="evisa-grid g2">
                        <div class="evisa-field">
                            <label>Your nationality <span class="req">*</span></label>
                            <select class="evisa-select" name="nationality" id="evNationality" required>
                                <option value="">Select your nationality</option>
                                @foreach($nationalities as $n)
                                    <option value="{{ $n['code'] }}">{{ $n['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="evisa-field">
                            <label>Destination <span class="req">*</span></label>
                            <select class="evisa-select" name="destination_code" id="evDestination" required>
                                <option value="">Select a country</option>
                                @foreach($countries as $c)
                                    <option value="{{ $c['code'] }}">{{ $c['flag'] }} {{ $c['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="evisa-field">
                            <label>Arrival date <span class="req">*</span></label>
                            <input class="evisa-input" type="date" name="arrival_date" id="evArrival" required>
                        </div>
                        <div class="evisa-field">
                            <label>Departure date <span class="req">*</span></label>
                            <input class="evisa-input" type="date" name="departure_date" id="evDeparture" required>
                        </div>
                    </div>
                </div>

                {{-- Visa type --}}
                <div class="evisa-card" id="evTypesCard" style="display:none;">
                    <p class="evisa-section-title">Choose your visa</p>
                    <div class="evisa-types" id="evTypes"></div>
                </div>

                {{-- Traveller details --}}
                <div class="evisa-card">
                    <p class="evisa-section-title">Traveller details</p>
                    <div class="evisa-grid g2">
                        <div class="evisa-field"><label>First name <span class="req">*</span></label><input class="evisa-input" type="text" name="first_name" required></div>
                        <div class="evisa-field"><label>Last name <span class="req">*</span></label><input class="evisa-input" type="text" name="last_name" required></div>
                        <div class="evisa-field"><label>Email <span class="req">*</span></label><input class="evisa-input" type="email" name="email" placeholder="name@example.com" required></div>
                        <div class="evisa-field"><label>Phone</label><input class="evisa-input" type="tel" name="phone" placeholder="+971 50 000 0000"></div>
                    </div>
                </div>

                {{-- Dynamic, scheme-driven documents & details --}}
                <div class="evisa-card" id="evSchemeCard" style="display:none;">
                    <p class="evisa-section-title">Required documents &amp; details</p>
                    <div id="evScheme"></div>
                </div>
            </div>

            {{-- Summary --}}
            <aside class="evisa-card evisa-sum-card">
                <p class="evisa-section-title">Your visa</p>
                <ul class="evisa-sum-list">
                    <li><i class="bi bi-geo-alt"></i> <span id="sumCountry">Select a destination</span></li>
                    <li><i class="bi bi-card-checklist"></i> <span id="sumType">Choose a visa option</span></li>
                    <li><i class="bi bi-shield-check"></i> Government-approved e-Visa</li>
                    <li><i class="bi bi-envelope-check"></i> Updates by email</li>
                </ul>
                <div class="evisa-fee">
                    <span class="lbl">Total</span>
                    <span class="amt" id="sumPrice">—</span>
                </div>
                <button type="submit" class="evisa-submit" id="evSubmit" disabled>Continue to secure payment</button>
                <p class="evisa-note">Secure payment. You'll get email updates on your application status.</p>
            </aside>
        </form>
        @endif
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
(function () {
    var form = document.getElementById('evisaForm');
    if (!form) return;

    var stripePk = @json(config('fluxir.stripe_publishable_key'));
    var csrf = form.querySelector('input[name="_token"]').value;
    var alertBox = document.getElementById('evisaAlert');
    var btn = document.getElementById('evSubmit');

    var dest = document.getElementById('evDestination');
    var nat  = document.getElementById('evNationality');
    var arr  = document.getElementById('evArrival');
    var dep  = document.getElementById('evDeparture');
    var typesCard = document.getElementById('evTypesCard');
    var typesBox  = document.getElementById('evTypes');
    var schemeCard = document.getElementById('evSchemeCard');
    var schemeBox  = document.getElementById('evScheme');

    var state = { typeId: null, versionId: null, price: null };

    if (typeof TomSelect !== 'undefined') {
        if (nat) {
            new TomSelect(nat, {
                create: false,
                placeholder: 'Select your nationality',
                controlInput: '<input>',
                render: {
                    no_results: function(data, escape) {
                        return '<div class="no-results" style="padding: 8px 14px; color: #888;">No nationality found for "' + escape(data.input) + '"</div>';
                    }
                }
            });
        }
        if (dest) {
            new TomSelect(dest, {
                create: false,
                placeholder: 'Select a country',
                controlInput: '<input>',
                render: {
                    no_results: function(data, escape) {
                        return '<div class="no-results" style="padding: 8px 14px; color: #888;">No destination found for "' + escape(data.input) + '"</div>';
                    }
                }
            });
        }
    }

    function err(msg) { alertBox.textContent = msg; alertBox.classList.add('show'); window.scrollTo({ top: 0, behavior: 'smooth' }); }
    function clearErr() { alertBox.textContent = ''; alertBox.classList.remove('show'); }
    function esc(s) { return String(s == null ? '' : s).replace(/[&<>"]/g, function (c) { return { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;' }[c]; }); }

    function setPrice(p) { state.price = p; document.getElementById('sumPrice').textContent = (p != null) ? ('$' + p) : '—'; }
    function refreshSubmit() { btn.disabled = !(state.typeId && state.price != null); }

    // 1) Destination + nationality chosen -> load visa types
    dest.addEventListener('change', loadTypes);
    nat.addEventListener('change', loadTypes);
    arr.addEventListener('change', loadTypes);
    dep.addEventListener('change', loadTypes);
    function loadTypes() {
        state.typeId = null; state.versionId = null; setPrice(null);
        schemeCard.style.display = 'none'; schemeBox.innerHTML = '';
        document.getElementById('sumType').textContent = 'Choose a visa option';
        refreshSubmit();
        var code = dest.value;
        document.getElementById('sumCountry').textContent = (dest.selectedIndex > 0 ? dest.options[dest.selectedIndex].text : 'Select a destination');
        if (!code) { typesCard.style.display = 'none'; return; }
        typesCard.style.display = '';
        if (!nat.value) { typesBox.innerHTML = '<p class="evisa-hint-empty">Select your nationality above to see available visas and prices.</p>'; return; }
        typesBox.innerHTML = '<p class="evisa-hint-empty">Loading visa options…</p>';
        var q = new URLSearchParams({ country: code, nationality: nat.value, arrival_date: arr.value || '', departure_date: dep.value || '' });
        fetch("{{ route('visa.evisa.types') }}?" + q.toString(), { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (d.needs_nationality) { typesBox.innerHTML = '<p class="evisa-hint-empty">Select your nationality above to see available visas.</p>'; return; }
                if (!d.success || !d.types.length) { typesBox.innerHTML = '<p class="evisa-hint-empty">No online visa options available for this nationality/destination.</p>'; return; }
                typesBox.innerHTML = d.types.map(function (t, i) {
                    var title = t.name;
                    if (t.category) {
                        title = t.category.indexOf('Visa') >= 0 ? t.category : (t.category + ' Visa');
                    } else {
                        var countryName = d.country && d.country.name ? d.country.name : '';
                        if (countryName) {
                            title = title.replace(new RegExp('^' + countryName + '\\s*', 'i'), '');
                        }
                        title = title.replace(/\s*e-?Visa\s*$/i, '');
                        if (title.toLowerCase().indexOf('visa') < 0) {
                            title = title + ' Visa';
                        }
                    }

                    var metaParts = [];
                    if (t.entry) {
                        metaParts.push(t.entry);
                    }
                    if (t.stay) {
                        var stayStr = t.stay;
                        if (stayStr.toLowerCase().indexOf('stay') < 0) {
                            stayStr = 'Stay ' + stayStr;
                        }
                        metaParts.push(stayStr);
                    }
                    if (t.validity) {
                        var validityStr = t.validity;
                        if (validityStr.toLowerCase().indexOf('validity') < 0 && validityStr.toLowerCase().indexOf('valid') < 0) {
                            validityStr = 'Validity ' + validityStr;
                        } else {
                            validityStr = validityStr.replace(/^Valid\s+/i, 'Validity ');
                        }
                        metaParts.push(validityStr);
                    }
                    var meta = metaParts.join(' • ');

                    return '<label class="evisa-type" data-id="' + t.id + '">' +
                        '<input type="radio" name="visa_type_id" value="' + t.id + '">' +
                        '<span>' +
                            '<span class="t-name">' + esc(title) + '</span>' +
                            '<span class="t-meta">' + esc(meta) + '</span>' +
                        '</span>' +
                        '<span class="t-price">$' + t.price + '</span>' +
                    '</label>';
                }).join('');
                typesBox.querySelectorAll('input[name="visa_type_id"]').forEach(function (inp) {
                    inp.addEventListener('change', function () { onTypeChosen(inp); });
                });
            })
            .catch(function () { typesBox.innerHTML = '<p class="evisa-hint-empty">Could not load options. Please retry.</p>'; });
    }

    // 2) Visa type chosen -> load the dynamic scheme + authoritative price
    function onTypeChosen(inp) {
        typesBox.querySelectorAll('.evisa-type').forEach(function (el) { el.classList.remove('sel'); });
        inp.closest('.evisa-type').classList.add('sel');
        state.typeId = inp.value;
        document.getElementById('sumType').textContent = inp.closest('.evisa-type').querySelector('.t-name').textContent;
        if (!nat.value) { err('Please select your nationality first.'); inp.checked = false; state.typeId = null; refreshSubmit(); return; }
        clearErr();
        schemeCard.style.display = '';
        schemeBox.innerHTML = '<p class="evisa-hint-empty">Loading required documents…</p>';
        setPrice(null); refreshSubmit();
        var body = new URLSearchParams({
            destination_code: dest.value, visa_type_id: inp.value, nationality: nat.value,
            arrival_date: arr.value || '', departure_date: dep.value || ''
        });
        fetch("{{ route('visa.evisa.scheme') }}", {
            method: 'POST', body: body,
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/x-www-form-urlencoded' }
        })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
        .then(function (res) {
            if (!res.ok || !res.d.success) { throw new Error(res.d.message || 'Could not load this visa.'); }
            state.versionId = res.d.version_id;
            setPrice(res.d.price);
            schemeBox.innerHTML = renderScheme(res.d.sections || []);
            bindFileInputs();
            refreshSubmit();
        })
        .catch(function (e2) { schemeBox.innerHTML = '<p class="evisa-hint-empty">' + esc(e2.message) + '</p>'; setPrice(null); refreshSubmit(); });
    }

    // Build the dynamic form from the scheme JSON.
    function renderScheme(sections) {
        if (!sections.length) return '<p class="evisa-hint-empty">No extra documents required for this visa.</p>';
        return sections.map(function (sec) {
            var fields = sec.fields.map(renderField).join('');
            return '<div class="evisa-scheme-sec"><h4>' + esc(sec.title) + '</h4><div class="evisa-grid g2">' + fields + '</div></div>';
        }).join('');
    }

    function renderField(f) {
        var req = f.required ? ' required' : '';
        var star = f.required ? ' <span class="req">*</span>' : '';
        var lbl = esc(f.label);
        var nm = f.name_id;
        if (f.is_file) {
            return '<label class="evisa-file" style="grid-column:1/-1;">' +
                '<i class="bi bi-paperclip"></i><span><span class="lab">' + lbl + star + '</span><span class="hint">PDF/JPG/PNG · 8MB</span></span>' +
                '<input type="file" name="files[' + nm + ']" accept=".pdf,.jpg,.jpeg,.png"' + req + '></label>';
        }
        var inner;
        if (f.kind === 'textarea') {
            inner = '<textarea class="evisa-textarea" name="items[' + nm + ']"' + req + '></textarea>';
        } else if (f.kind === 'select') {
            inner = '<select class="evisa-select" name="items[' + nm + ']"' + req + '><option value="">Select</option>' +
                f.options.map(function (o) { return '<option value="' + esc(o.value) + '">' + esc(o.label) + '</option>'; }).join('') + '</select>';
        } else if (f.kind === 'multiselect') {
            inner = '<div class="evisa-checks">' + f.options.map(function (o) {
                return '<label><input type="checkbox" name="items[' + nm + '][]" value="' + esc(o.value) + '"> ' + esc(o.label) + '</label>';
            }).join('') + '</div>';
        } else {
            var type = ({ number:'number', date:'date', tel:'tel' })[f.kind] || 'text';
            inner = '<input class="evisa-input" type="' + type + '" name="items[' + nm + ']"' + req + '>';
        }
        var span = (f.kind === 'textarea' || f.kind === 'multiselect') ? ' style="grid-column:1/-1;"' : '';
        return '<div class="evisa-field"' + span + '><label>' + lbl + star + '</label>' + inner + '</div>';
    }

    function bindFileInputs() {
        schemeBox.querySelectorAll('.evisa-file input[type=file]').forEach(function (inp) {
            inp.addEventListener('change', function () {
                var w = inp.closest('.evisa-file'), lab = w.querySelector('.lab');
                if (inp.files.length) { w.classList.add('has-file'); lab.textContent = inp.files[0].name; }
                else { w.classList.remove('has-file'); }
            });
        });
    }

    // 3) Submit
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearErr();
        if (!state.typeId) { err('Please choose a visa option.'); return; }
        var original = btn.innerHTML;
        btn.disabled = true; btn.innerHTML = '<span class="spin"></span> Submitting…';
        fetch("{{ route('visa.evisa.apply') }}", {
            method: 'POST', body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
        })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
        .then(function (res) {
            var d = res.d;
            if (!res.ok || !d.success) { throw new Error(d.message || 'We could not submit your application.'); }
            if (d.on_credit || d.redirect) { window.location.assign(d.redirect || '/uaevisa'); return; }
            if (!d.checkout_session_id) { throw new Error('Application submitted but payment could not be started. Please contact support.'); }
            // Redirect directly to the Stripe-hosted checkout URL — no publishable key needed.
            window.location.href = 'https://checkout.stripe.com/pay/' + d.checkout_session_id;
        })
        .catch(function (e3) { err(e3.message || 'Something went wrong.'); btn.disabled = false; btn.innerHTML = original; });
    });
})();
</script>

@include('footer')
