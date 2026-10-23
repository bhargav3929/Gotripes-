@include('header')

@php
    $margin = 1 + (float) $package->margin_percent / 100;
    $sellOf = fn ($net) => round((float) $net * $margin, 2);
    $cover = $package->image ?: ($package->galleryImages()[0] ?? null);
    $coverUrl = $cover ? (str_starts_with($cover, 'http') ? $cover : asset($cover)) : null;
    $img = fn ($p) => $p ? (str_starts_with($p, 'http') ? $p : asset($p)) : null;

    // Everything the page needs, with selling prices only (never net).
    $wizard = [
        'urls' => [
            'quote' => route('tour-packages.quote', $package->id),
            'visa' => route('tour-packages.visa', $package->id),
            'checkout' => route('tour-packages.checkout', $package->id),
        ],
        'destination' => $package->country,
        'durations' => $durations->values(),
        'tiers' => $tiers,
        'insuranceRequired' => (bool) $package->insurance_required,
        'childMaxAge' => (int) $package->child_max_age,
        'infantMaxAge' => (int) $package->infant_max_age,
        'mealPlans' => \App\Models\TourPackageHotel::MEAL_PLANS,
        'hotels' => $package->hotels->mapWithKeys(fn ($h) => [$h->hotel_stars => [
            'name' => $h->name, 'location' => $h->location,
            'check_in' => $h->check_in_time, 'check_out' => $h->check_out_time,
            'late_check_in' => $h->late_check_in_policy, 'late_check_out' => $h->late_check_out_policy,
            'meal_plans' => $h->mealPlanCodes(),
        ]]),
        'addons' => $package->addons->groupBy('category')->map(fn ($g) => $g->map(fn ($a) => [
            'code' => $a->code, 'label' => $a->label, 'description' => $a->description,
            'price' => $sellOf($a->net_price), 'basis' => \App\Models\TourPackageAddon::BASES[$a->charge_basis] ?? '',
        ])->values()),
        'days' => $package->itineraryDays->map(fn ($d) => [
            'duration' => $d->duration_days, 'day' => $d->day_number, 'title' => $d->title,
            'description' => $d->description, 'image' => $img($d->image_path),
        ])->values(),
    ];
@endphp

<main class="tpw">
    <div class="container tpw-wrap">
        <a href="{{ route('tour-packages.show', $package->id) }}" class="tpw-back"><i class="bi bi-arrow-left"></i> {{ $package->title }}</a>

        <header class="tpw-hero" @if($coverUrl) style="--hero:url('{{ $coverUrl }}')" @endif>
            <div>
                <div class="tpw-kicker">{{ $package->country }}</div>
                <h1 class="tpw-title">{{ $package->title }}</h1>
                @if($fromPrice)<div class="tpw-from">From <strong>AED {{ number_format($fromPrice) }}</strong> per adult</div>@endif
            </div>
        </header>

        <ol class="tpw-steps" aria-label="Booking steps">
            @foreach(['Dates & visa', 'Travellers', 'Stay', 'Options', 'Itinerary', 'Checkout'] as $i => $label)
                <li data-step-dot="{{ $i + 1 }}"><span>{{ $i + 1 }}</span>{{ $label }}</li>
            @endforeach
        </ol>

        <div class="row g-4">
            <div class="col-lg-8">
                <form id="tpwForm" novalidate>
                    @csrf

                    {{-- 1. Dates & eligibility --}}
                    <section class="tpw-step" data-step="1">
                        <h2>When are you travelling, and on which passport?</h2>
                        <p class="tpw-lede">We check your entry requirements for {{ $package->country }} straight away.</p>
                        <div class="tpw-grid2">
                            <label class="tpw-field">Start date
                                <input type="date" name="start_date" min="{{ now()->addDay()->toDateString() }}" max="{{ now()->addYears(2)->toDateString() }}" required>
                            </label>
                            <label class="tpw-field">Passport nationality
                                <select name="nationality" required>
                                    <option value="">Choose…</option>
                                    @foreach($countries as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach
                                </select>
                            </label>
                        </div>
                        <div class="tpw-field-static">Destination <strong>{{ $package->country }}</strong></div>
                        <div id="tpwVisa" class="tpw-visa" hidden></div>
                    </section>

                    {{-- 2. Travellers --}}
                    <section class="tpw-step" data-step="2" hidden>
                        <h2>Who is travelling?</h2>
                        <div class="tpw-cards4" role="radiogroup">
                            @foreach(['solo' => ['Solo', '1 adult', 'bi-person'], 'couple' => ['Couple', '2 adults', 'bi-people'], 'family' => ['Family', 'adults and children', 'bi-house-heart'], 'group' => ['Group', '5 people or more', 'bi-people-fill']] as $val => [$t, $s, $icon])
                                <label class="tpw-card"><input type="radio" name="traveler_type" value="{{ $val }}" @checked($val === 'couple')>
                                    <i class="bi {{ $icon }}"></i><strong>{{ $t }}</strong><small>{{ $s }}</small></label>
                            @endforeach
                        </div>
                        <div id="tpwCounts" class="tpw-counts">
                            <div class="tpw-counter"><span>Adults <small>12+</small></span>
                                <div><button type="button" data-adj="adults" data-by="-1" aria-label="Fewer adults">−</button><input name="adults" value="2" inputmode="numeric" readonly><button type="button" data-adj="adults" data-by="1" aria-label="More adults">+</button></div>
                            </div>
                            <div id="tpwKids">
                                <div class="tpw-kids-head"><span>Children <small>age on the travel date, for bedding and child rates</small></span>
                                    <button type="button" class="tpw-link" id="tpwAddChild"><i class="bi bi-plus-circle"></i> Add a child</button></div>
                                <div id="tpwChildList" class="tpw-child-list"></div>
                            </div>
                        </div>
                    </section>

                    {{-- 3. Duration & hotel --}}
                    <section class="tpw-step" data-step="3" hidden>
                        <h2>How long, and where do you stay?</h2>
                        <div class="tpw-tabs" id="tpwDurations" role="radiogroup"></div>
                        <div class="tpw-cards3" id="tpwTiers" role="radiogroup"></div>
                        <div id="tpwHotelPolicy" class="tpw-policy" hidden></div>
                    </section>

                    {{-- 4. Customisation --}}
                    <section class="tpw-step" data-step="4" hidden>
                        <h2>Make it yours</h2>
                        <div id="tpwGuide"></div>
                        <div id="tpwTransport"></div>
                        <div id="tpwMeals"></div>
                        <div id="tpwInsurance"></div>
                    </section>

                    {{-- 5. Itinerary --}}
                    <section class="tpw-step" data-step="5" hidden>
                        <h2>Your trip, day by day</h2>
                        <div id="tpwTimeline" class="tpw-timeline"></div>
                        <div id="tpwTransfer"></div>
                    </section>

                    {{-- 6. Checkout --}}
                    <section class="tpw-step" data-step="6" hidden>
                        <h2>Check and pay</h2>
                        <div id="tpwReceipt" class="tpw-receipt"></div>
                        <div class="tpw-grid2">
                            <label class="tpw-field">Full name (as on passport)<input name="customer_name" autocomplete="name" required></label>
                            <label class="tpw-field">Email<input type="email" name="customer_email" autocomplete="email" required></label>
                            <label class="tpw-field">Phone with country code<input type="tel" name="customer_phone" placeholder="+971 50 123 4567" autocomplete="tel" data-no-intl required></label>
                            <label class="tpw-field">Special requests <small>(optional)</small><input name="special_requests" maxlength="1000"></label>
                        </div>
                        <label class="tpw-check"><input type="checkbox" name="accept_terms" value="1" required>
                            I have checked the dates, travellers, hotel policies and visa requirements above.</label>
                        <div id="tpwPayError" class="tpw-error" role="alert" hidden></div>
                        <button type="button" id="tpwPay" class="tpw-btn tpw-btn-pay"><i class="bi bi-lock"></i> Proceed to secure payment</button>
                    </section>

                    <div id="tpwStepError" class="tpw-error" role="alert" hidden></div>
                    <div class="tpw-nav">
                        <button type="button" class="tpw-btn tpw-btn-ghost" id="tpwPrev" hidden>Back</button>
                        <button type="button" class="tpw-btn tpw-btn-next" id="tpwNext">Continue</button>
                    </div>
                </form>
            </div>

            <aside class="col-lg-4">
                <div class="tpw-summary" aria-live="polite">
                    <div class="tpw-summary-title">Your trip</div>
                    <div id="tpwSummary" class="tpw-summary-body"><p class="tpw-muted">Choose your dates to see the price.</p></div>
                    <div class="tpw-summary-total"><span>Total</span><strong id="tpwTotal">—</strong></div>
                    <p class="tpw-muted tpw-small">Prices in AED, final when you pay. Card fees included.</p>
                </div>
            </aside>
        </div>
    </div>
</main>

<style>
    .tpw { background:#000; color:#fff; font-family:'Outfit',sans-serif; min-height:100vh; padding-bottom:64px; }
    .tpw-wrap { max-width:1140px; padding-top:32px; }
    .tpw-back { color:#FFD23F; text-decoration:none; font-size:14px; }
    .tpw-hero { margin:16px 0 24px; border-radius:16px; min-height:180px; display:flex; align-items:flex-end; padding:24px;
        background:linear-gradient(180deg,rgba(0,0,0,.1),rgba(0,0,0,.85)), var(--hero, #0e0e10) center/cover; border:1px solid rgba(255,215,0,.15); }
    .tpw-kicker { color:#FFD23F; font-size:12px; letter-spacing:2px; text-transform:uppercase; font-weight:600; }
    .tpw-title { font-size:clamp(28px,4vw,44px); font-weight:800; line-height:1.1; letter-spacing:-.02em; margin:6px 0; }
    .tpw-from { color:#ddd; } .tpw-from strong { color:#FFD23F; }
    .tpw-steps { list-style:none; display:flex; gap:8px; padding:0; margin:0 0 24px; overflow-x:auto; }
    .tpw-steps li { flex:1 0 auto; display:flex; align-items:center; gap:8px; font-size:13px; color:#777; padding:8px 12px; border-radius:99px; border:1px solid #222; white-space:nowrap; }
    .tpw-steps li span { width:22px; height:22px; border-radius:50%; display:grid; place-items:center; background:#1a1a1a; font-weight:700; font-size:12px; }
    .tpw-steps li.is-current { color:#000; background:#FFD23F; border-color:#FFD23F; } .tpw-steps li.is-current span { background:#000; color:#FFD23F; }
    .tpw-steps li.is-done { color:#FFD23F; border-color:rgba(255,215,0,.35); }
    .tpw-step h2 { font-size:24px; font-weight:800; letter-spacing:-.01em; margin-bottom:6px; }
    .tpw-lede, .tpw-muted { color:#9a9a9a; } .tpw-lede { font-size:15px; } .tpw-small { font-size:12px; margin:8px 0 0; }
    .tpw-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin:16px 0; }
    .tpw-field { display:flex; flex-direction:column; gap:6px; font-size:13px; color:#bbb; font-weight:600; }
    .tpw-field input, .tpw-field select { height:48px; background:#111; border:1px solid #2a2a2a; border-radius:10px; padding:0 14px; color:#fff; font-size:15px; font-weight:400; }
    .tpw-field input:focus, .tpw-field select:focus { outline:2px solid #FFD23F; outline-offset:1px; border-color:transparent; }
    .tpw-field-static { color:#9a9a9a; font-size:14px; } .tpw-field-static strong { color:#fff; }
    .tpw-visa { margin-top:16px; padding:14px 16px; border-radius:12px; border:1px solid; font-size:14px; line-height:1.6; }
    .tpw-visa.ok { background:rgba(34,197,94,.1); border-color:rgba(34,197,94,.45); }
    .tpw-visa.warn { background:rgba(245,158,11,.1); border-color:rgba(245,158,11,.5); }
    .tpw-visa.unknown { background:#111; border-color:#2a2a2a; }
    .tpw-visa strong { display:block; font-size:15px; }
    .tpw-cards4, .tpw-cards3 { display:grid; gap:12px; margin:16px 0; }
    .tpw-cards4 { grid-template-columns:repeat(4,1fr); } .tpw-cards3 { grid-template-columns:repeat(3,1fr); }
    .tpw-card { position:relative; display:flex; flex-direction:column; gap:4px; padding:16px; border:1px solid #2a2a2a; border-radius:12px; background:#0b0b0c; cursor:pointer; transition:border-color .15s, transform .15s; }
    .tpw-card:hover { transform:translateY(-2px); border-color:rgba(255,215,0,.4); }
    .tpw-card input { position:absolute; opacity:0; }
    .tpw-card:has(input:checked) { border-color:#FFD23F; background:rgba(255,215,0,.06); }
    .tpw-card:has(input:focus-visible) { outline:2px solid #FFD23F; outline-offset:2px; }
    .tpw-card i { font-size:22px; color:#FFD23F; } .tpw-card small { color:#9a9a9a; }
    .tpw-card .tpw-price { color:#FFD23F; font-weight:700; margin-top:6px; }
    .tpw-counts { display:flex; flex-direction:column; gap:16px; margin-top:8px; }
    .tpw-counter { display:flex; justify-content:space-between; align-items:center; padding:12px 0; border-bottom:1px solid #1d1d1d; }
    .tpw-counter small, .tpw-kids-head small { color:#888; font-weight:400; margin-left:6px; }
    .tpw-counter div { display:flex; align-items:center; gap:8px; }
    .tpw-counter button { width:36px; height:36px; border-radius:10px; border:1px solid rgba(255,215,0,.35); background:transparent; color:#FFD23F; font-size:18px; }
    .tpw-counter input { width:44px; text-align:center; background:transparent; border:0; color:#fff; font-size:17px; font-weight:700; }
    .tpw-kids-head { display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; }
    .tpw-link { background:none; border:0; color:#FFD23F; font-weight:600; }
    .tpw-child-list { display:flex; flex-wrap:wrap; gap:8px; margin-top:10px; }
    .tpw-child { display:flex; align-items:center; gap:6px; background:#111; border:1px solid #2a2a2a; border-radius:10px; padding:6px 10px; white-space:nowrap; font-size:14px; }
    .tpw-child select { background:#111; color:#fff; border:0; width:auto; height:auto; padding:2px 4px; font-size:14px; }
    .tpw-child button { background:none; border:0; color:#888; }
    .tpw-tabs { display:flex; gap:8px; flex-wrap:wrap; margin-top:12px; }
    .tpw-tab { padding:10px 18px; border-radius:99px; border:1px solid #2a2a2a; cursor:pointer; position:relative; }
    .tpw-tab input { position:absolute; opacity:0; }
    .tpw-tab:has(input:checked) { background:#FFD23F; color:#000; border-color:#FFD23F; font-weight:700; }
    .tpw-tab:has(input:focus-visible) { outline:2px solid #FFD23F; outline-offset:2px; }
    .tpw-stars { color:#FFD23F; letter-spacing:2px; }
    .tpw-policy { margin-top:8px; padding:16px; border:1px solid #222; border-radius:12px; background:#0b0b0c; font-size:14px; line-height:1.6; }
    .tpw-policy dt { color:#9a9a9a; font-weight:500; } .tpw-policy dd { margin:0 0 8px; }
    .tpw-opt { margin:20px 0; } .tpw-opt-title { font-weight:700; font-size:16px; margin-bottom:8px; }
    /* The site template makes every input full-width and block; undo that for choices. */
    .tpw input[type=radio], .tpw input[type=checkbox] { width:auto; height:auto; min-width:0; display:inline-block; margin:0; padding:0; flex:none; box-shadow:none; }
    .tpw-radio { display:flex; justify-content:space-between; gap:12px; align-items:center; padding:12px 14px; border:1px solid #2a2a2a; border-radius:10px; margin-bottom:8px; cursor:pointer; font-size:15px; font-weight:500; color:#eee; line-height:1.35; }
    .tpw-radio > span:first-child { display:flex; align-items:center; gap:10px; }
    .tpw-radio input { width:18px !important; height:18px !important; }
    .tpw-radio:has(input:checked) { border-color:#FFD23F; background:rgba(255,215,0,.05); }
    .tpw-radio input { accent-color:#FFD23F; }
    .tpw-radio .tpw-price { color:#FFD23F; font-size:13px; white-space:nowrap; }
    .tpw-timeline { border-left:2px solid rgba(255,215,0,.3); margin:16px 0 24px 10px; padding-left:22px; }
    .tpw-day { position:relative; margin-bottom:22px; }
    .tpw-day::before { content:''; position:absolute; left:-31px; top:6px; width:16px; height:16px; border-radius:50%; background:#FFD23F; border:3px solid #000; }
    .tpw-day-label { color:#FFD23F; font-size:12px; letter-spacing:1.5px; text-transform:uppercase; font-weight:700; }
    .tpw-day h3 { font-size:19px; font-weight:700; margin:4px 0; }
    .tpw-day img { width:100%; max-height:260px; object-fit:cover; border-radius:12px; margin:8px 0; }
    .tpw-day p { color:#bdbdbd; margin:0; line-height:1.6; font-size:15px; }
    .tpw-toggle { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:16px; border:1px solid #2a2a2a; border-radius:12px; }
    .tpw-toggle input { width:22px !important; height:22px !important; accent-color:#FFD23F; }
    .tpw-receipt { border:1px solid #222; border-radius:12px; padding:16px; margin:16px 0; background:#0b0b0c; }
    .tpw-receipt-row { display:flex; justify-content:space-between; gap:12px; padding:8px 0; border-bottom:1px solid #1a1a1a; font-size:14px; }
    .tpw-receipt-row small { display:block; color:#888; }
    .tpw-receipt-row span:last-child { font-variant-numeric:tabular-nums; white-space:nowrap; }
    .tpw-receipt-total { display:flex; justify-content:space-between; padding-top:12px; font-size:18px; font-weight:800; color:#FFD23F; }
    .tpw-check { display:flex; gap:10px; align-items:flex-start; font-size:14px; color:#ccc; margin:8px 0 16px; }
    .tpw-check input { margin-top:3px !important; width:18px !important; height:18px !important; accent-color:#FFD23F; }
    .tpw-error { margin:12px 0; padding:12px 14px; border-radius:10px; background:rgba(214,54,56,.12); border:1px solid rgba(214,54,56,.45); color:#f3a3a4; font-size:14px; }
    .tpw-nav { display:flex; justify-content:space-between; gap:12px; margin-top:24px; }
    .tpw-btn { height:50px; padding:0 28px; border-radius:12px; font-weight:700; border:0; transition:transform .15s, opacity .15s; }
    .tpw-btn:hover { transform:translateY(-1px); } .tpw-btn:disabled { opacity:.5; transform:none; }
    .tpw-btn-next, .tpw-btn-pay { background:linear-gradient(135deg,#FFD700,#D4AF37); color:#000; margin-left:auto; }
    .tpw-btn-pay { width:100%; }
    .tpw-btn-ghost { background:transparent; color:#FFD23F; border:1px solid rgba(255,215,0,.4); }
    .tpw-summary { position:sticky; top:96px; border:1px solid rgba(255,215,0,.15); border-radius:16px; padding:20px; background:linear-gradient(180deg,#0e0e10,#070708); }
    .tpw-summary-title { font-weight:800; font-size:18px; margin-bottom:10px; }
    .tpw-summary-body { font-size:14px; color:#ccc; }
    .tpw-summary-body div { display:flex; justify-content:space-between; gap:10px; padding:4px 0; }
    .tpw-summary-total { display:flex; justify-content:space-between; align-items:baseline; border-top:1px dashed rgba(255,215,0,.25); margin-top:12px; padding-top:12px; }
    .tpw-summary-total strong { color:#FFD23F; font-size:24px; font-variant-numeric:tabular-nums; }
    @media (max-width: 991px) { .tpw-summary { position:static; } }
    @media (max-width: 767px) {
        .tpw-grid2, .tpw-cards3 { grid-template-columns:1fr; }
        .tpw-cards4 { grid-template-columns:1fr 1fr; }
        .tpw-steps li { font-size:0; padding:6px; } .tpw-steps li span { font-size:12px; }
        .tpw-steps li.is-current { font-size:13px; padding:6px 12px; }
    }
</style>

<script>
(function () {
    const W = @json($wizard);
    const form = document.getElementById('tpwForm');
    const $ = (id) => document.getElementById(id);
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const money = (n) => { const v = Number(n), whole = Number.isInteger(v);
        return 'AED ' + v.toLocaleString('en-US', {minimumFractionDigits: whole ? 0 : 2, maximumFractionDigits: 2}); };
    const csrf = form.querySelector('input[name=_token]').value;
    let step = 1, lastQuote = null, quoteTimer = null, quoteSeq = 0;

    // ---------- state ----------
    const state = { days: W.durations[0], stars: null, childAges: [] };

    function payload() {
        const fd = new FormData(form);
        const data = Object.fromEntries(fd.entries());
        data.days = state.days; data.hotel_stars = state.stars;
        data.child_ages = state.childAges;
        data.airport_transfer = !!form.querySelector('[name=airport_transfer]:checked');
        data.insurance = W.insuranceRequired || !!form.querySelector('[name=insurance]:checked');
        return data;
    }

    // ---------- step 1: visa ----------
    form.nationality.addEventListener('change', async () => {
        const box = $('tpwVisa');
        if (!form.nationality.value) { box.hidden = true; return; }
        const r = await fetch(W.urls.visa + '?nationality=' + encodeURIComponent(form.nationality.value), {headers: {Accept: 'application/json'}});
        renderVisa(await r.json());
    });
    function renderVisa(v) {
        const box = $('tpwVisa');
        box.className = 'tpw-visa ' + (v.tone || 'unknown');
        let html = '<strong>' + (v.tone === 'ok' ? '<i class="bi bi-check-circle"></i> ' : v.tone === 'warn' ? '<i class="bi bi-exclamation-triangle"></i> ' : '') + esc(v.label) + '</strong>';
        const facts = [];
        if (v.max_stay_days) facts.push('Stay up to ' + v.max_stay_days + ' days');
        if (v.processing_days) facts.push('Processing about ' + v.processing_days + ' days');
        if (v.fee) facts.push('Fee about ' + money(v.fee));
        if (facts.length) html += facts.map(esc).join(' · ');
        if (v.documents) html += '<div>Documents: ' + esc(v.documents) + '</div>';
        if (v.notes) html += '<div>' + esc(v.notes) + '</div>';
        box.innerHTML = html; box.hidden = false;
    }

    // ---------- step 2: travellers ----------
    function setAdults(n) { form.adults.value = Math.max(1, Math.min(50, n)); requote(); }
    form.querySelectorAll('[data-adj]').forEach(b => b.addEventListener('click', () => setAdults(+form.adults.value + +b.dataset.by)));
    function renderChildren() {
        $('tpwChildList').innerHTML = state.childAges.map((age, i) =>
            '<span class="tpw-child">Child ' + (i + 1) + ' <select data-child="' + i + '" aria-label="Age of child ' + (i + 1) + '">' +
            Array.from({length: 18}, (_, a) => '<option value="' + a + '"' + (a === age ? ' selected' : '') + '>' + (a === 0 ? 'under 1' : a + (a === 1 ? ' year' : ' years')) + '</option>').join('') +
            '</select><button type="button" data-remove="' + i + '" aria-label="Remove child ' + (i + 1) + '"><i class="bi bi-x-lg"></i></button></span>').join('');
    }
    $('tpwAddChild').addEventListener('click', () => { state.childAges.push(5); renderChildren(); requote(); });
    $('tpwChildList').addEventListener('change', e => { if (e.target.dataset.child !== undefined) { state.childAges[+e.target.dataset.child] = +e.target.value; requote(); } });
    $('tpwChildList').addEventListener('click', e => { const b = e.target.closest('[data-remove]'); if (b) { state.childAges.splice(+b.dataset.remove, 1); renderChildren(); requote(); } });
    form.querySelectorAll('[name=traveler_type]').forEach(r => r.addEventListener('change', applyType));
    function applyType() {
        const t = form.traveler_type.value;
        $('tpwKids').hidden = (t === 'solo' || t === 'couple');
        if (t === 'solo') { form.adults.value = 1; state.childAges = []; }
        if (t === 'couple') { form.adults.value = 2; state.childAges = []; }
        if (t === 'family' && !state.childAges.length) state.childAges = [5];
        if (t === 'group' && +form.adults.value < 5) form.adults.value = 5;
        form.querySelectorAll('[data-adj]').forEach(b => b.disabled = (t === 'solo' || t === 'couple'));
        renderChildren(); requote();
    }

    // ---------- step 3: duration & hotel ----------
    function renderDurations() {
        $('tpwDurations').innerHTML = W.durations.map(d =>
            '<label class="tpw-tab"><input type="radio" name="days_choice" value="' + d + '"' + (d === state.days ? ' checked' : '') + '>' + d + ' days · ' + (d - 1) + ' nights</label>').join('');
        renderTiers();
    }
    $('tpwDurations').addEventListener('change', e => { state.days = +e.target.value; renderTiers(); renderTimeline(); requote(); });
    function renderTiers() {
        const tiers = W.tiers[state.days] || [];
        if (!tiers.includes(state.stars)) state.stars = tiers[0] ?? null;
        $('tpwTiers').innerHTML = tiers.map(s => {
            const h = W.hotels[s];
            return '<label class="tpw-card"><input type="radio" name="tier_choice" value="' + s + '"' + (s === state.stars ? ' checked' : '') + '>' +
                '<span class="tpw-stars" aria-label="' + s + ' stars">' + '★'.repeat(s) + '</span><strong>' + (h ? esc(h.name) : s + '-star hotel') + '</strong>' +
                (h && h.location ? '<small>' + esc(h.location) + '</small>' : '') + '</label>';
        }).join('');
        renderPolicy(); renderOptions();
    }
    $('tpwTiers').addEventListener('change', e => { state.stars = +e.target.value; renderPolicy(); renderOptions(); requote(); });
    function renderPolicy() {
        const h = W.hotels[state.stars], box = $('tpwHotelPolicy');
        if (!h) { box.hidden = true; return; }
        box.innerHTML = '<dl>' +
            '<dt>Check-in / check-out</dt><dd>From ' + esc(h.check_in) + ' · by ' + esc(h.check_out) + '</dd>' +
            (h.late_check_in ? '<dt>Late check-in</dt><dd>' + esc(h.late_check_in) + '</dd>' : '') +
            (h.late_check_out ? '<dt>Late check-out</dt><dd>' + esc(h.late_check_out) + '</dd>' : '') +
            '<dt>Meals this hotel serves</dt><dd>' + h.meal_plans.map(c => esc(W.mealPlans[c])).join(' · ') + '</dd></dl>';
        box.hidden = false;
    }

    // ---------- step 4: options ----------
    const priceTag = (a) => a.price > 0 ? '+' + money(a.price) + ' ' + esc(a.basis) : 'Included';
    function radioGroup(name, title, options, current) {
        if (!options || !options.length) return '';
        const sel = current && options.some(o => o.code === current) ? current : (options.find(o => !o.price) || options[0]).code;
        return '<div class="tpw-opt"><div class="tpw-opt-title">' + title + '</div>' + options.map(o =>
            '<label class="tpw-radio"><span><input type="radio" name="' + name + '" value="' + esc(o.code) + '"' + (o.code === sel ? ' checked' : '') + '>' + esc(o.label) +
            (o.description ? ' <small class="tpw-muted">' + esc(o.description) + '</small>' : '') + '</span><span class="tpw-price">' + priceTag(o) + '</span></label>').join('') + '</div>';
    }
    function renderOptions() {
        const keep = (n) => form.querySelector('[name=' + n + ']:checked')?.value;
        $('tpwGuide').innerHTML = radioGroup('guide_language', 'Tour guide language', W.addons.guide_language, keep('guide_language'));
        $('tpwTransport').innerHTML = radioGroup('transport', 'Daily transport', W.addons.transport, keep('transport'));
        const h = W.hotels[state.stars];
        const served = h ? h.meal_plans : ['breakfast'];
        const supplements = Object.fromEntries((W.addons.meal_plan || []).map(a => [a.code, a]));
        const meals = served.map(code => supplements[code] || {code, label: W.mealPlans[code], price: 0, basis: ''})
            .map(m => ({...m, label: W.mealPlans[m.code] || m.label}));
        $('tpwMeals').innerHTML = radioGroup('meal_plan', 'Meals <small class="tpw-muted">(only plans this hotel actually serves)</small>', meals, keep('meal_plan'));
        const ins = (W.addons.insurance || [])[0];
        $('tpwInsurance').innerHTML = !ins ? '' : '<div class="tpw-opt"><div class="tpw-opt-title">Travel insurance</div>' +
            '<label class="tpw-radio"><span><input type="checkbox" name="insurance" value="1"' + (W.insuranceRequired ? ' checked disabled' : (keep('insurance') ? ' checked' : '')) + '>' + esc(ins.label) +
            (W.insuranceRequired ? ' <small class="tpw-muted">required for this destination</small>' : '') + '</span><span class="tpw-price">' + priceTag(ins) + '</span></label></div>';
    }
    form.addEventListener('change', e => { if (['guide_language', 'transport', 'meal_plan', 'insurance', 'airport_transfer', 'start_date', 'nationality'].includes(e.target.name)) requote(); });

    // ---------- step 5: itinerary ----------
    function renderTimeline() {
        const own = W.days.filter(d => d.duration === state.days);
        const days = (own.length ? own : W.days.filter(d => d.duration === null && d.day <= state.days)).sort((a, b) => a.day - b.day);
        $('tpwTimeline').innerHTML = days.length ? days.map(d =>
            '<div class="tpw-day"><div class="tpw-day-label">Day ' + d.day + '</div><h3>' + esc(d.title) + '</h3>' +
            (d.image ? '<img src="' + esc(d.image) + '" alt="' + esc(d.title) + '" loading="lazy">' : '') +
            (d.description ? '<p>' + esc(d.description) + '</p>' : '') + '</div>').join('')
            : '<p class="tpw-muted">The detailed day-by-day plan is sent with your confirmation.</p>';
        const t = (W.addons.airport_transfer || [])[0];
        $('tpwTransfer').innerHTML = !t ? '' : '<label class="tpw-toggle"><span><strong>Do you need a pickup from the airport to your hotel?</strong><br><small class="tpw-muted">' +
            esc(t.label) + ' · ' + priceTag(t) + '</small></span><input type="checkbox" role="switch" name="airport_transfer" value="1"></label>';
    }

    // ---------- live quote ----------
    function requote() { clearTimeout(quoteTimer); quoteTimer = setTimeout(fetchQuote, 250); }
    async function fetchQuote() {
        const seq = ++quoteSeq;
        const data = payload();
        if (!data.start_date || !state.stars) return;
        const r = await fetch(W.urls.quote, {method: 'POST', headers: {'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf}, body: JSON.stringify(data)});
        if (seq !== quoteSeq) return; // a newer request is on its way
        const res = await r.json();
        lastQuote = res.ok ? res.quote : null;
        renderSummary(res);
    }
    function renderSummary(res) {
        if (!res.ok) {
            $('tpwSummary').innerHTML = '<p class="tpw-muted">' + esc(Object.values(res.errors || {})[0] || 'Complete the steps to see your price.') + '</p>';
            $('tpwTotal').textContent = '—'; return;
        }
        const q = res.quote;
        $('tpwSummary').innerHTML =
            '<div><span>Dates</span><span>' + esc(q.start_date) + ' → ' + esc(q.end_date) + '</span></div>' +
            '<div><span>Stay</span><span>' + q.days + ' days, ' + q.hotel_stars + '★</span></div>' +
            '<div><span>Travellers</span><span>' + [q.adults && q.adults + ' adult' + (q.adults > 1 ? 's' : ''), q.children && q.children + ' child' + (q.children > 1 ? 'ren' : ''), q.infants && q.infants + ' infant' + (q.infants > 1 ? 's' : '')].filter(Boolean).join(', ') + '</span></div>' +
            '<div><span>Meals</span><span>' + esc(q.selections.meal_plan_label) + '</span></div>' +
            (q.season ? '<div><span>Season</span><span>' + esc(q.season) + '</span></div>' : '');
        $('tpwTotal').textContent = money(q.total);
        if (q.visa) renderVisa(q.visa);
        $('tpwReceipt').innerHTML = q.lines.map(l => '<div class="tpw-receipt-row"><span>' + esc(l.label) + '<small>' + esc(l.detail) + '</small></span><span>' + money(l.amount) + '</span></div>').join('') +
            '<div class="tpw-receipt-total"><span>Total to pay</span><span>' + money(q.total) + '</span></div>' +
            (q.hotel ? '<p class="tpw-muted tpw-small">' + esc(q.hotel.name) + ': check-in from ' + esc(q.hotel.check_in) + ', check-out by ' + esc(q.hotel.check_out) + '.' +
                (q.hotel.late_check_in ? ' Late check-in: ' + esc(q.hotel.late_check_in) : '') + (q.hotel.late_check_out ? ' Late check-out: ' + esc(q.hotel.late_check_out) : '') + '</p>' : '') +
            (q.visa ? '<p class="tpw-muted tpw-small">Visa: ' + esc(q.visa.label) + (q.visa.documents ? ' (' + esc(q.visa.documents) + ')' : '') + '. Visa fees are not included.</p>' : '');
    }

    // ---------- navigation ----------
    function stepError(msg) { const e = $('tpwStepError'); e.textContent = msg || ''; e.hidden = !msg; }
    function check(n) {
        if (n === 1) {
            if (!form.start_date.value) return 'Choose your travel start date.';
            if (!form.nationality.value) return 'Choose your passport nationality.';
        }
        if (n === 2 && form.traveler_type.value === 'family' && !state.childAges.length) return 'Add each child\'s age, or choose another traveller type.';
        if (n === 3 && !state.stars) return 'Choose a hotel.';
        return '';
    }
    function go(n) {
        step = Math.max(1, Math.min(6, n));
        document.querySelectorAll('.tpw-step').forEach(s => s.hidden = +s.dataset.step !== step);
        document.querySelectorAll('[data-step-dot]').forEach(d => {
            d.classList.toggle('is-current', +d.dataset.stepDot === step);
            d.classList.toggle('is-done', +d.dataset.stepDot < step);
        });
        $('tpwPrev').hidden = step === 1; $('tpwNext').hidden = step === 6;
        stepError('');
        window.scrollTo({top: document.querySelector('.tpw-steps').offsetTop - 90, behavior: 'smooth'});
    }
    $('tpwNext').addEventListener('click', async () => {
        const msg = check(step);
        if (msg) return stepError(msg);
        if (step >= 2) {
            await fetchQuote();
            if (!lastQuote && step >= 3) return stepError($('tpwSummary').textContent);
        }
        go(step + 1);
    });
    $('tpwPrev').addEventListener('click', () => go(step - 1));

    // ---------- pay ----------
    $('tpwPay').addEventListener('click', async () => {
        const err = $('tpwPayError'); err.hidden = true;
        if (!form.reportValidity()) return;
        const btn = $('tpwPay'), label = btn.innerHTML;
        btn.disabled = true; btn.innerHTML = 'Opening secure payment…';
        try {
            const data = payload(); data.accept_terms = form.accept_terms.checked ? 1 : 0;
            const r = await fetch(W.urls.checkout, {method: 'POST', headers: {'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf}, body: JSON.stringify(data)});
            const res = await r.json();
            if (res.ok && res.checkout_url) { window.location.href = res.checkout_url; return; }
            throw new Error(res.message || Object.values(res.errors || {}).flat()[0] || 'Payment could not be started.');
        } catch (e) {
            err.textContent = e.message; err.hidden = false; btn.disabled = false; btn.innerHTML = label;
        }
    });

    renderDurations(); renderTimeline(); applyType(); go(1);
})();
</script>

@include('footer')
