@include('header')

@php
    $margin = 1 + (float) $package->margin_percent / 100;
    $sellOf = fn ($net) => round((float) $net * $margin, 2);
    $cover = $package->image ?: ($package->galleryImages()[0] ?? null);
    $coverUrl = $cover ? (str_starts_with($cover, 'http') ? $cover : asset($cover)) : null;
    $img = fn ($p) => $p ? (str_starts_with($p, 'http') ? $p : asset($p)) : null;
    $stepLabels = ['Dates & visa', 'Travellers', 'Stay', 'Options', 'Itinerary', 'Checkout'];

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
    <div class="tpw-wrap">
        <a href="{{ route('tour-packages.show', $package->id) }}" class="tpw-back"><i class="bi bi-arrow-left"></i><span>{{ $package->title }}</span></a>

        <header class="tpw-hero" @if($coverUrl) style="--hero:url('{{ $coverUrl }}')" @endif>
            <div class="tpw-hero-text">
                <div class="tpw-kicker">{{ $package->country }} · Book online</div>
                <h1 class="tpw-title">{{ $package->title }}</h1>
                @if($fromPrice)<div class="tpw-from">From <strong>AED {{ number_format($fromPrice) }}</strong> per adult</div>@endif
            </div>
        </header>

        <div class="tpw-progress">
            <div class="tpw-progress-label" aria-live="polite"><span id="tpwStepCount">Step 1 of 6</span><strong id="tpwStepName">{{ $stepLabels[0] }}</strong></div>
            <ol class="tpw-steps" aria-label="Booking steps">
                @foreach($stepLabels as $i => $label)
                    <li data-step-dot="{{ $i + 1 }}"><span>{{ $i + 1 }}</span>{{ $label }}</li>
                @endforeach
            </ol>
        </div>

        <div class="tpw-layout">
            <form id="tpwForm" class="tpw-panel" novalidate>
                @csrf

                {{-- 1. Dates & eligibility --}}
                <section class="tpw-step" data-step="1">
                    <h2>When are you travelling, and on which passport?</h2>
                    <p class="tpw-lede">We check your entry requirements for {{ $package->country }} straight away.</p>
                    <div class="tpw-grid2">
                        <label class="tpw-field"><span>Start date</span>
                            <input type="date" name="start_date" min="{{ now()->addDay()->toDateString() }}" max="{{ now()->addYears(2)->toDateString() }}" required>
                        </label>
                        <label class="tpw-field"><span>Passport nationality</span>
                            <select name="nationality" required>
                                <option value="">Choose your passport</option>
                                @foreach($countries as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach
                            </select>
                        </label>
                    </div>
                    <div class="tpw-dest"><i class="bi bi-geo-alt"></i> Destination <strong>{{ $package->country }}</strong></div>
                    <div id="tpwVisa" class="tpw-visa" hidden></div>
                </section>

                {{-- 2. Travellers --}}
                <section class="tpw-step" data-step="2" hidden>
                    <h2>Who is travelling?</h2>
                    <p class="tpw-lede">Children's ages decide bedding and child rates.</p>
                    <div class="tpw-cards4" role="radiogroup" aria-label="Traveller type">
                        @foreach(['solo' => ['Solo', '1 adult', 'bi-person'], 'couple' => ['Couple', '2 adults', 'bi-people'], 'family' => ['Family', 'Adults and children', 'bi-house-heart'], 'group' => ['Group', '5 people or more', 'bi-people-fill']] as $val => [$t, $s, $icon])
                            <label class="tpw-card"><input type="radio" name="traveler_type" value="{{ $val }}" @checked($val === 'couple')>
                                <i class="bi {{ $icon }}" aria-hidden="true"></i><strong>{{ $t }}</strong><small>{{ $s }}</small><span class="tpw-tick" aria-hidden="true"><i class="bi bi-check-lg"></i></span></label>
                        @endforeach
                    </div>
                    <div id="tpwCounts" class="tpw-counts">
                        <div class="tpw-counter"><span class="tpw-counter-label">Adults <small>12 and over</small></span>
                            <div class="tpw-stepper"><button type="button" data-adj="adults" data-by="-1" aria-label="Fewer adults"><i class="bi bi-dash-lg"></i></button><input name="adults" value="2" inputmode="numeric" readonly aria-label="Adults"><button type="button" data-adj="adults" data-by="1" aria-label="More adults"><i class="bi bi-plus-lg"></i></button></div>
                        </div>
                        <div id="tpwKids" class="tpw-kids">
                            <div class="tpw-kids-head"><span class="tpw-counter-label">Children <small>age on the travel date</small></span>
                                <button type="button" class="tpw-link" id="tpwAddChild"><i class="bi bi-plus-lg"></i> Add a child</button></div>
                            <div id="tpwChildList" class="tpw-child-list"></div>
                        </div>
                    </div>
                </section>

                {{-- 3. Duration & hotel --}}
                <section class="tpw-step" data-step="3" hidden>
                    <h2>How long, and where do you stay?</h2>
                    <div class="tpw-sub">Trip length</div>
                    <div class="tpw-tabs" id="tpwDurations" role="radiogroup" aria-label="Trip length"></div>
                    <div class="tpw-sub">Hotel</div>
                    <div class="tpw-cards3" id="tpwTiers" role="radiogroup" aria-label="Hotel"></div>
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
                    <div class="tpw-sub">Lead traveller</div>
                    <div class="tpw-grid2">
                        <label class="tpw-field"><span>Full name, as on passport</span><input name="customer_name" autocomplete="name" required></label>
                        <label class="tpw-field"><span>Email</span><input type="email" name="customer_email" autocomplete="email" inputmode="email" required></label>
                        <label class="tpw-field"><span>Phone with country code</span><input type="tel" name="customer_phone" placeholder="+971 50 123 4567" autocomplete="tel" data-no-intl required></label>
                        <label class="tpw-field"><span>Special requests <small>optional</small></span><input name="special_requests" maxlength="1000"></label>
                    </div>
                    <label class="tpw-check"><input type="checkbox" name="accept_terms" value="1" required><span class="tpw-box" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
                        <span>I have checked the dates, travellers, hotel policies and visa requirements above.</span></label>
                    <div id="tpwPayError" class="tpw-error" role="alert" hidden></div>
                    <p class="tpw-secure"><i class="bi bi-shield-lock"></i> You pay by card on Nomod's secure page. We never see your card details.</p>
                </section>

                <div id="tpwStepError" class="tpw-error" role="alert" hidden></div>

                {{-- On phones this is a bar pinned to the bottom of the screen, with the live total. --}}
                <div class="tpw-nav">
                    <button type="button" class="tpw-bar-total" id="tpwBarTotal" aria-controls="tpwSummaryPanel" aria-expanded="false">
                        <small>Total <i class="bi bi-chevron-up"></i></small><strong id="tpwBarAmount">—</strong>
                    </button>
                    <button type="button" class="tpw-btn tpw-btn-ghost" id="tpwPrev" hidden aria-label="Back"><i class="bi bi-arrow-left"></i><span>Back</span></button>
                    <button type="button" class="tpw-btn tpw-btn-next" id="tpwNext"><span>Continue</span><i class="bi bi-arrow-right"></i></button>
                    <button type="button" class="tpw-btn tpw-btn-next tpw-btn-pay" id="tpwPay" hidden><i class="bi bi-lock-fill"></i><span>Pay securely</span></button>
                </div>
            </form>

            <aside class="tpw-aside">
                <div class="tpw-backdrop" id="tpwBackdrop" hidden></div>
                <div class="tpw-summary" id="tpwSummaryPanel" aria-live="polite">
                    <div class="tpw-sheet-handle" aria-hidden="true"></div>
                    <div class="tpw-summary-head">
                        <div class="tpw-summary-title">Your trip</div>
                        <button type="button" class="tpw-sheet-close" id="tpwSheetClose" aria-label="Close"><i class="bi bi-x-lg"></i></button>
                    </div>
                    <div id="tpwSummary" class="tpw-summary-body"><p class="tpw-muted">Choose your dates to see the price.</p></div>
                    <div class="tpw-summary-total"><span>Total</span><strong id="tpwTotal">—</strong></div>
                    <p class="tpw-small">Prices in AED, final when you pay. Card fees included.</p>
                </div>
            </aside>
        </div>
    </div>
</main>

<style>
    /* ---------- tokens ---------- */
    .tpw {
        --bg:#0c0b09; --surface:#14130f; --surface-2:#1b1915; --line:rgba(255,255,255,.08); --line-2:rgba(255,255,255,.14);
        --gold:#E8BC3F; --gold-soft:rgba(232,188,63,.12); --gold-line:rgba(232,188,63,.55); --on-gold:#1a1305;
        --ink:#F4EFE3; --ink-2:#C9C3B5; --mute:#948E80;
        --ok:#4ADE80; --ok-bg:rgba(74,222,128,.08); --warn:#F5B544; --warn-bg:rgba(245,181,68,.08); --err:#F58A8B; --err-bg:rgba(245,90,92,.1);
        --r-sm:12px; --r-lg:20px; --ease:cubic-bezier(.2,.7,.2,1);
        --bar-h:76px;
        background:var(--bg); color:var(--ink); min-height:100vh; padding-bottom:96px;
        font-size:16px; line-height:1.5; font-weight:400; -webkit-font-smoothing:antialiased;
    }
    /* The site template sets 24px text, block-level full-width inputs and its own button and heading
       styles on every page. Everything in the wizard sets its own size so none of that leaks in. */
    .tpw *, .tpw *::before, .tpw *::after { box-sizing:border-box; }
    .tpw :where(h1,h2,h3,p,ol,dl,dd) { margin:0; padding:0; }
    .tpw :where(button, input, select) { font:inherit; letter-spacing:normal; text-transform:none; }
    .tpw :where(button) { cursor:pointer; -webkit-tap-highlight-color:transparent; }
    .tpw input[type=radio], .tpw input[type=checkbox] { position:absolute; opacity:0; width:1px !important; height:1px !important; margin:0; pointer-events:none; }
    .tpw .bi { line-height:1; }

    .tpw-wrap { max-width:1180px; margin:0 auto; padding:24px 16px 0; }
    .tpw-back { display:inline-flex; align-items:center; gap:8px; max-width:100%; color:var(--ink-2); text-decoration:none; font-size:14px; font-weight:500; transition:color .15s; }
    .tpw-back span { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .tpw-back:hover { color:var(--gold); }

    /* ---------- hero ---------- */
    .tpw-hero { position:relative; margin:16px 0 24px; border-radius:var(--r-lg); min-height:168px; display:flex; align-items:flex-end; padding:24px; overflow:hidden;
        background:linear-gradient(180deg,rgba(12,11,9,.05) 0%,rgba(12,11,9,.7) 42%,rgba(12,11,9,.94) 100%), var(--hero, var(--surface)) center/cover; border:1px solid var(--line); }
    .tpw-hero-text { text-shadow:0 1px 12px rgba(0,0,0,.5); }
    .tpw-kicker { color:var(--gold); font-size:12px; letter-spacing:.14em; text-transform:uppercase; font-weight:600; }
    .tpw-title { font-size:clamp(28px,4.4vw,48px); font-weight:800; line-height:1.05; letter-spacing:-.025em; margin:8px 0 6px; color:#fff; }
    .tpw-from { color:var(--ink-2); font-size:15px; font-weight:300; } .tpw-from strong { color:var(--gold); font-weight:700; }

    /* ---------- progress ---------- */
    .tpw-progress { margin-bottom:24px; }
    .tpw-progress-label { display:none; }
    .tpw-steps { list-style:none; display:grid; grid-template-columns:repeat(6,1fr); gap:8px; }
    .tpw-steps li { position:relative; display:flex; align-items:center; gap:10px; padding:12px 0 0; font-size:13px; font-weight:500; color:var(--mute); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
        border-top:3px solid var(--line-2); transition:color .25s, border-color .35s var(--ease); }
    .tpw-steps li span { flex:none; width:24px; height:24px; border-radius:50%; display:grid; place-items:center; font-size:12px; font-weight:700; background:var(--surface-2); color:var(--mute); transition:background .25s, color .25s; }
    .tpw-steps li.is-done { color:var(--ink-2); border-color:var(--gold-line); }
    .tpw-steps li.is-done span { background:var(--gold-soft); color:var(--gold); }
    .tpw-steps li.is-current { color:var(--ink); font-weight:600; border-color:var(--gold); }
    .tpw-steps li.is-current span { background:var(--gold); color:var(--on-gold); }

    /* ---------- layout ---------- */
    .tpw-layout { display:grid; grid-template-columns:minmax(0,1fr) 360px; gap:32px; align-items:start; }
    .tpw-panel { background:var(--surface); border:1px solid var(--line); border-radius:var(--r-lg); padding:32px; min-width:0; }

    /* ---------- steps ---------- */
    .tpw .tpw-step { padding:0; margin:0; } /* the template pads every <section> by 50px */
    .tpw-step h2 { font-size:clamp(22px,2.4vw,28px); font-weight:700; line-height:1.15; letter-spacing:-.02em; color:#fff; }
    .tpw-lede { color:var(--mute); font-size:15px; font-weight:300; line-height:1.55; margin-top:8px; }
    .tpw-sub { font-size:12px; letter-spacing:.12em; text-transform:uppercase; font-weight:600; color:var(--mute); margin:24px 0 12px; }
    .tpw-step.is-enter { animation:tpwIn .32s var(--ease) both; }
    .tpw-step.is-enter.is-back { animation-name:tpwInBack; }
    @keyframes tpwIn { from { opacity:0; transform:translateX(16px); } to { opacity:1; transform:none; } }
    @keyframes tpwInBack { from { opacity:0; transform:translateX(-16px); } to { opacity:1; transform:none; } }

    /* ---------- fields ---------- */
    .tpw-grid2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:24px; }
    .tpw-field { display:flex; flex-direction:column; gap:8px; margin:0; min-width:0; }
    .tpw-field > span { font-size:13px; font-weight:500; color:var(--ink-2); line-height:1.3; }
    .tpw-field small { color:var(--mute); font-weight:300; }
    .tpw-field input, .tpw-field select { display:block; width:100%; height:52px; margin:0; padding:0 16px; background:var(--bg); border:1px solid var(--line-2); border-radius:var(--r-sm);
        color:var(--ink); font-size:16px; line-height:normal; box-shadow:none; transition:border-color .15s, box-shadow .15s; -webkit-appearance:none; appearance:none; }
    .tpw-field input::placeholder { color:#5f5a50; }
    .tpw-field select { padding-right:44px; background:var(--bg) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1.5l5 5 5-5' fill='none' stroke='%23C9C3B5' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E") no-repeat right 16px center; }
    .tpw-field input::-webkit-date-and-time-value { text-align:left; }
    .tpw-field input::-webkit-calendar-picker-indicator { filter:invert(.8); opacity:.8; cursor:pointer; }
    .tpw-field input:focus, .tpw-field select:focus { outline:none; border-color:var(--gold); box-shadow:0 0 0 3px var(--gold-soft); }
    .tpw-dest { display:flex; align-items:center; gap:8px; margin-top:16px; color:var(--mute); font-size:14px; } .tpw-dest strong { color:var(--ink); font-weight:600; }

    .tpw-visa { margin-top:16px; padding:16px; border-radius:var(--r-sm); border:1px solid; font-size:14px; line-height:1.55; color:var(--ink-2); animation:tpwIn .3s var(--ease) both; }
    .tpw-visa strong { display:flex; align-items:center; gap:8px; font-size:15px; font-weight:600; color:var(--ink); margin-bottom:4px; }
    .tpw-visa div { margin-top:4px; }
    .tpw-visa.ok { background:var(--ok-bg); border-color:rgba(74,222,128,.35); } .tpw-visa.ok strong i { color:var(--ok); }
    .tpw-visa.warn { background:var(--warn-bg); border-color:rgba(245,181,68,.4); } .tpw-visa.warn strong i { color:var(--warn); }
    .tpw-visa.unknown { background:var(--surface-2); border-color:var(--line-2); }

    /* ---------- choice cards ---------- */
    .tpw-cards4, .tpw-cards3 { display:grid; gap:12px; }
    .tpw-cards4 { grid-template-columns:repeat(4,minmax(0,1fr)); margin-top:24px; } .tpw-cards3 { grid-template-columns:repeat(3,minmax(0,1fr)); }
    .tpw-card { position:relative; display:flex; flex-direction:column; align-items:flex-start; gap:4px; margin:0; padding:16px; min-width:0; border:1px solid var(--line-2); border-radius:var(--r-sm);
        background:var(--bg); cursor:pointer; transition:border-color .15s, background .15s, transform .15s var(--ease); -webkit-tap-highlight-color:transparent; }
    .tpw-card:hover { border-color:rgba(232,188,63,.35); }
    .tpw-card:active { transform:scale(.98); }
    .tpw-card > i { font-size:22px; color:var(--gold); margin-bottom:8px; }
    .tpw-card strong { font-size:16px; font-weight:600; color:var(--ink); line-height:1.25; }
    .tpw-card small { font-size:13px; color:var(--mute); line-height:1.35; }
    .tpw-card:has(input:checked) { border-color:var(--gold); background:var(--gold-soft); box-shadow:inset 0 0 0 1px var(--gold); }
    .tpw-card:has(input:focus-visible), .tpw-tab:has(input:focus-visible), .tpw-radio:has(input:focus-visible), .tpw-check:has(input:focus-visible), .tpw-toggle:has(input:focus-visible) { outline:2px solid var(--gold); outline-offset:2px; }
    .tpw-tick { position:absolute; top:12px; right:12px; width:22px; height:22px; border-radius:50%; display:grid; place-items:center; background:var(--gold); color:var(--on-gold); font-size:13px;
        opacity:0; transform:scale(.6); transition:opacity .18s, transform .18s var(--ease); }
    .tpw-card:has(input:checked) .tpw-tick { opacity:1; transform:none; }
    .tpw-stars { color:var(--gold); font-size:14px; letter-spacing:2px; margin-bottom:4px; }

    /* ---------- travellers ---------- */
    .tpw-counts { margin-top:24px; border-top:1px solid var(--line); }
    .tpw-counter, .tpw-kids { padding:16px 0; border-bottom:1px solid var(--line); }
    .tpw-counter { display:flex; justify-content:space-between; align-items:center; gap:16px; }
    .tpw-counter-label { font-size:16px; font-weight:500; color:var(--ink); line-height:1.3; }
    .tpw-counter-label small { display:block; font-size:13px; font-weight:300; color:var(--mute); margin-top:2px; }
    .tpw-stepper { display:flex; align-items:center; gap:4px; flex:none; }
    .tpw-stepper button { width:44px; height:44px; display:grid; place-items:center; padding:0; border-radius:50%; border:1px solid var(--line-2); background:transparent; color:var(--ink); font-size:16px; transition:border-color .15s, background .15s, transform .1s; }
    .tpw-stepper button:hover:not(:disabled) { border-color:var(--gold); color:var(--gold); }
    .tpw-stepper button:active:not(:disabled) { transform:scale(.92); background:var(--gold-soft); }
    .tpw-stepper button:disabled { opacity:.3; cursor:not-allowed; }
    .tpw-stepper input { width:44px; height:44px; padding:0; text-align:center; background:transparent; border:0; color:var(--ink); font-size:18px; font-weight:700; font-variant-numeric:tabular-nums; }
    .tpw-kids-head { display:flex; justify-content:space-between; align-items:center; gap:12px; }
    .tpw-link { display:inline-flex; align-items:center; gap:6px; flex:none; height:40px; padding:0 14px; border-radius:var(--r-sm); background:var(--gold-soft); border:1px solid transparent; color:var(--gold); font-size:14px; font-weight:600; transition:border-color .15s; }
    .tpw-link:hover { border-color:var(--gold-line); }
    .tpw-child-list { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:8px; margin-top:12px; }
    .tpw-child-list:empty { display:none; }
    .tpw-child { display:flex; align-items:center; gap:8px; height:48px; padding:0 6px 0 14px; background:var(--bg); border:1px solid var(--line-2); border-radius:var(--r-sm); font-size:14px; color:var(--ink-2); white-space:nowrap; animation:tpwIn .25s var(--ease) both; }
    .tpw-child select { flex:1; min-width:0; height:36px; padding:0 8px; background:transparent; color:var(--ink); border:0; font-size:15px; font-weight:600; -webkit-appearance:none; appearance:none; cursor:pointer; }
    .tpw-child select:focus { outline:2px solid var(--gold); border-radius:8px; }
    .tpw-child button { width:36px; height:36px; display:grid; place-items:center; padding:0; border:0; border-radius:50%; background:none; color:var(--mute); font-size:13px; }
    .tpw-child button:hover { color:var(--err); background:var(--err-bg); }

    /* ---------- duration & hotel ---------- */
    .tpw-tabs { display:grid; grid-auto-flow:column; grid-auto-columns:1fr; gap:4px; padding:4px; background:var(--bg); border:1px solid var(--line-2); border-radius:var(--r-sm); }
    .tpw-tab { position:relative; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:2px; margin:0; min-height:56px; padding:8px; border-radius:8px; cursor:pointer; text-align:center;
        color:var(--ink-2); transition:background .2s var(--ease), color .2s; -webkit-tap-highlight-color:transparent; }
    .tpw-tab strong { font-size:16px; font-weight:600; line-height:1.2; } .tpw-tab small { font-size:12px; color:var(--mute); line-height:1.2; transition:color .2s; }
    .tpw-tab:hover { color:var(--ink); }
    .tpw-tab:has(input:checked) { background:var(--gold); color:var(--on-gold); } .tpw-tab:has(input:checked) small { color:rgba(26,19,5,.7); }
    .tpw-cards3 .tpw-card { padding:16px 16px 18px; }
    .tpw-policy { margin-top:16px; padding:20px; border:1px solid var(--line); border-radius:var(--r-sm); background:var(--bg); animation:tpwIn .3s var(--ease) both; }
    .tpw-policy dl { display:grid; grid-template-columns:minmax(120px,auto) 1fr; gap:12px 20px; font-size:14px; line-height:1.55; }
    .tpw-policy dt { color:var(--mute); font-weight:400; } .tpw-policy dd { color:var(--ink); }

    /* ---------- options ---------- */
    .tpw-opt { margin-top:24px; }
    .tpw-opt-title { font-size:12px; letter-spacing:.12em; text-transform:uppercase; font-weight:600; color:var(--mute); margin-bottom:12px; }
    .tpw-opt-title small { text-transform:none; letter-spacing:0; font-weight:300; }
    .tpw-radio { position:relative; display:grid; grid-template-columns:22px minmax(0,1fr) auto; align-items:center; gap:14px; margin:0 0 8px; padding:14px 16px; min-height:56px;
        border:1px solid var(--line-2); border-radius:var(--r-sm); background:var(--bg); cursor:pointer; transition:border-color .15s, background .15s; -webkit-tap-highlight-color:transparent; }
    .tpw-radio:hover { border-color:rgba(232,188,63,.35); }
    .tpw-radio:has(input:checked) { border-color:var(--gold); background:var(--gold-soft); }
    .tpw-ind { width:22px; height:22px; border-radius:50%; border:2px solid var(--line-2); display:grid; place-items:center; transition:border-color .15s; }
    .tpw-ind::after { content:''; width:10px; height:10px; border-radius:50%; background:var(--gold); transform:scale(0); transition:transform .18s var(--ease); }
    .tpw-radio:has(input:checked) .tpw-ind { border-color:var(--gold); } .tpw-radio:has(input:checked) .tpw-ind::after { transform:scale(1); }
    .tpw-ind.is-box { border-radius:6px; } .tpw-ind.is-box::after { content:'\2713'; width:auto; height:auto; background:none; color:var(--on-gold); font-size:13px; font-weight:800; }
    .tpw-radio:has(input:checked) .tpw-ind.is-box { background:var(--gold); }
    .tpw-radio:has(input:disabled) { cursor:default; }
    .tpw-opt-text { font-size:15px; font-weight:500; color:var(--ink); line-height:1.35; min-width:0; }
    .tpw-opt-text small { display:block; font-size:13px; font-weight:300; color:var(--mute); margin-top:2px; }
    .tpw-price { font-size:14px; font-weight:600; color:var(--gold); text-align:right; line-height:1.3; font-variant-numeric:tabular-nums; }
    .tpw-price small { display:block; font-size:11px; font-weight:400; color:var(--mute); }
    .tpw-price.is-free { color:var(--mute); font-weight:500; }

    /* ---------- itinerary ---------- */
    .tpw-timeline { position:relative; margin:24px 0; padding-left:28px; }
    .tpw-timeline::before { content:''; position:absolute; left:7px; top:8px; bottom:8px; width:2px; background:linear-gradient(var(--gold-line),var(--line)); }
    .tpw-day { position:relative; padding-bottom:28px; }
    .tpw-day:last-child { padding-bottom:0; }
    .tpw-day::before { content:''; position:absolute; left:-28px; top:4px; width:16px; height:16px; border-radius:50%; background:var(--bg); border:3px solid var(--gold); }
    .tpw-day-label { color:var(--gold); font-size:12px; letter-spacing:.12em; text-transform:uppercase; font-weight:600; }
    .tpw-day h3 { font-size:18px; font-weight:600; line-height:1.3; color:var(--ink); margin:4px 0 0; }
    .tpw-day img { display:block; width:100%; aspect-ratio:16/9; object-fit:cover; border-radius:var(--r-sm); margin:12px 0; background:var(--surface-2); }
    .tpw-day p { color:var(--ink-2); font-size:15px; font-weight:300; line-height:1.6; margin-top:8px; }
    .tpw-day img + p { margin-top:0; }
    .tpw-toggle { position:relative; display:flex; justify-content:space-between; align-items:center; gap:16px; margin:0; padding:20px; border:1px solid var(--line-2); border-radius:var(--r-sm); background:var(--bg); cursor:pointer; transition:border-color .15s, background .15s; }
    .tpw-toggle:has(input:checked) { border-color:var(--gold); background:var(--gold-soft); }
    .tpw-toggle strong { display:block; font-size:16px; font-weight:600; color:var(--ink); line-height:1.3; }
    .tpw-toggle small { display:block; font-size:13px; color:var(--mute); margin-top:4px; line-height:1.4; }
    .tpw-switch { flex:none; position:relative; width:48px; height:28px; border-radius:14px; background:var(--line-2); transition:background .2s; }
    .tpw-switch::after { content:''; position:absolute; top:3px; left:3px; width:22px; height:22px; border-radius:50%; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,.4); transition:transform .22s var(--ease); }
    .tpw-toggle:has(input:checked) .tpw-switch { background:var(--gold); } .tpw-toggle:has(input:checked) .tpw-switch::after { transform:translateX(20px); }

    /* ---------- checkout ---------- */
    .tpw-receipt { margin-top:24px; padding:8px 20px 20px; border:1px solid var(--line); border-radius:var(--r-sm); background:var(--bg); }
    .tpw-receipt-row { display:flex; justify-content:space-between; align-items:flex-start; gap:16px; padding:12px 0; border-bottom:1px solid var(--line); font-size:15px; color:var(--ink); line-height:1.4; }
    .tpw-receipt-row > span:first-child { min-width:0; }
    .tpw-receipt-row small { display:block; font-size:13px; color:var(--mute); font-weight:300; margin-top:2px; }
    .tpw-receipt-row > span:last-child { font-variant-numeric:tabular-nums; white-space:nowrap; font-weight:500; }
    .tpw-receipt-total { display:flex; justify-content:space-between; align-items:baseline; gap:16px; padding-top:16px; font-size:20px; font-weight:700; color:var(--gold); font-variant-numeric:tabular-nums; }
    .tpw-receipt .tpw-small { margin-top:12px; }
    .tpw-check { position:relative; display:flex; gap:12px; align-items:flex-start; margin:24px 0 0; font-size:14px; color:var(--ink-2); line-height:1.5; cursor:pointer; }
    .tpw-box { flex:none; width:22px; height:22px; margin-top:1px; border-radius:6px; border:2px solid var(--line-2); display:grid; place-items:center; color:transparent; font-size:14px; transition:background .15s, border-color .15s, color .15s; }
    .tpw-check:has(input:checked) .tpw-box { background:var(--gold); border-color:var(--gold); color:var(--on-gold); }
    .tpw-secure { display:flex; gap:8px; align-items:flex-start; margin-top:16px; font-size:13px; color:var(--mute); line-height:1.5; }
    .tpw-secure i { color:var(--gold); margin-top:2px; }

    .tpw-error { display:flex; gap:10px; align-items:flex-start; margin-top:20px; padding:14px 16px; border-radius:var(--r-sm); background:var(--err-bg); border:1px solid rgba(245,90,92,.4); color:var(--err); font-size:14px; line-height:1.45; animation:tpwShake .32s ease both; }
    .tpw-error::before { content:'!'; flex:none; width:20px; height:20px; border-radius:50%; display:grid; place-items:center; background:var(--err); color:var(--bg); font-size:13px; font-weight:800; }
    .tpw-error[hidden] { display:none; }
    @keyframes tpwShake { 0%,100% { transform:none; } 25% { transform:translateX(-4px); } 75% { transform:translateX(4px); } }

    /* ---------- buttons ---------- */
    .tpw-nav { display:flex; align-items:center; gap:12px; margin-top:32px; padding-top:24px; border-top:1px solid var(--line); }
    .tpw-btn { display:inline-flex; align-items:center; justify-content:center; gap:10px; height:52px; margin:0; padding:0 24px; border-radius:var(--r-sm); border:0;
        font-size:16px; font-weight:600; line-height:1; white-space:nowrap; transition:transform .15s var(--ease), opacity .15s, background .15s, box-shadow .15s; }
    .tpw-btn[hidden] { display:none; }
    .tpw-btn:active:not(:disabled) { transform:scale(.97); }
    .tpw-btn:disabled { opacity:.55; cursor:progress; }
    .tpw-btn:focus-visible { outline:2px solid var(--gold); outline-offset:3px; }
    .tpw-btn-next { margin-left:auto; min-width:200px; background:var(--gold); color:var(--on-gold); box-shadow:0 8px 24px -10px rgba(232,188,63,.7); }
    .tpw-btn-next:hover:not(:disabled) { background:#F2C94F; transform:translateY(-1px); }
    .tpw-btn-next .bi { transition:transform .15s var(--ease); } .tpw-btn-next:hover .bi-arrow-right { transform:translateX(3px); }
    .tpw-btn-ghost { background:transparent; color:var(--ink); border:1px solid var(--line-2); }
    .tpw-btn-ghost:hover { border-color:var(--ink-2); }
    .tpw-btn.is-busy { position:relative; color:transparent; }
    .tpw-btn.is-busy::after { content:''; position:absolute; width:20px; height:20px; border-radius:50%; border:2px solid var(--on-gold); border-right-color:transparent; animation:tpwSpin .7s linear infinite; }
    @keyframes tpwSpin { to { transform:rotate(360deg); } }
    .tpw-bar-total { display:none; }

    /* ---------- summary ---------- */
    .tpw-aside { min-width:0; align-self:stretch; } /* full column height, so the summary can stay in view */
    .tpw-summary { position:sticky; top:180px; border:1px solid var(--line); border-radius:var(--r-lg); padding:24px; background:var(--surface); }
    .tpw-summary-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; }
    .tpw-summary-title { font-size:18px; font-weight:700; color:#fff; letter-spacing:-.01em; }
    .tpw-sheet-handle, .tpw-sheet-close, .tpw-backdrop { display:none; }
    .tpw-summary-body { font-size:14px; color:var(--ink-2); line-height:1.4; }
    .tpw-summary-body > div { display:flex; justify-content:space-between; gap:16px; padding:8px 0; border-bottom:1px solid var(--line); }
    .tpw-summary-body > div span:first-child { color:var(--mute); flex:none; }
    .tpw-summary-body > div span:last-child { text-align:right; color:var(--ink); }
    .tpw-summary-total { display:flex; justify-content:space-between; align-items:baseline; gap:12px; margin-top:16px; }
    .tpw-summary-total span { font-size:15px; font-weight:500; color:var(--ink-2); }
    .tpw-summary-total strong { color:var(--gold); font-size:28px; font-weight:700; letter-spacing:-.02em; font-variant-numeric:tabular-nums; }
    .tpw-muted { color:var(--mute); font-size:14px; line-height:1.5; }
    .tpw-small { font-size:12px; color:var(--mute); line-height:1.5; margin-top:8px; }
    .is-loading { color:transparent !important; border-radius:8px; background:linear-gradient(90deg,var(--surface-2) 0%,#2a2620 50%,var(--surface-2) 100%) 0 0/200% 100%; animation:tpwShimmer 1.1s linear infinite; }
    @keyframes tpwShimmer { to { background-position:-200% 0; } }

    /* ---------- tablet and phone ---------- */
    @media (max-width: 991px) {
        .tpw { padding-bottom:calc(var(--bar-h) + 32px + env(safe-area-inset-bottom)); }
        .tpw-layout { grid-template-columns:minmax(0,1fr); gap:0; }
        .tpw-panel { padding:24px; }
        /* Bottom bar: live total on the left, Back and Continue / Pay on the right. */
        .tpw-nav { position:fixed; left:0; right:0; bottom:0; z-index:1040; margin:0; gap:8px; padding:12px 16px calc(12px + env(safe-area-inset-bottom));
            background:rgba(16,15,12,.92); -webkit-backdrop-filter:blur(16px); backdrop-filter:blur(16px); border-top:1px solid var(--line-2); }
        .tpw-bar-total { display:flex; flex-direction:column; align-items:flex-start; justify-content:center; gap:2px; min-width:0; height:52px; margin-right:auto; padding:0 4px; background:none; border:0; text-align:left; }
        .tpw-bar-total small { display:flex; align-items:center; gap:4px; font-size:12px; color:var(--mute); font-weight:500; }
        .tpw-bar-total small .bi { font-size:10px; transition:transform .2s; }
        .tpw-bar-total[aria-expanded=true] small .bi { transform:rotate(180deg); }
        .tpw-bar-total strong { font-size:19px; font-weight:700; color:var(--gold); letter-spacing:-.01em; font-variant-numeric:tabular-nums; white-space:nowrap; min-width:72px; }
        .tpw-btn-next { min-width:0; margin-left:0; padding:0 22px; }
        .tpw-btn-ghost { width:52px; padding:0; } .tpw-btn-ghost span { display:none; }
        /* The summary becomes a sheet that slides up from the bar. */
        .tpw-backdrop { display:block; position:fixed; inset:0; z-index:1041; background:rgba(0,0,0,.55); opacity:0; transition:opacity .25s; }
        .tpw-backdrop.is-open { opacity:1; }
        .tpw-backdrop[hidden] { display:none; }
        .tpw-summary { position:fixed; left:0; right:0; bottom:0; top:auto; z-index:1042; max-height:80vh; overflow-y:auto; border-radius:var(--r-lg) var(--r-lg) 0 0; border-bottom:0;
            padding:12px 20px calc(24px + env(safe-area-inset-bottom)); background:var(--surface); transform:translateY(105%); transition:transform .32s var(--ease); visibility:hidden; }
        .tpw-summary.is-open { transform:none; visibility:visible; }
        .tpw-sheet-handle { display:block; width:40px; height:4px; border-radius:2px; background:var(--line-2); margin:0 auto 12px; }
        .tpw-sheet-close { display:grid; place-items:center; width:40px; height:40px; padding:0; border:0; border-radius:50%; background:var(--surface-2); color:var(--ink); }
        /* The floating WhatsApp and Help buttons would sit on the bar. */
        .whats, #gtSupportLauncher { display:none !important; }
    }
    @media (max-width: 767px) {
        .tpw-wrap { padding:16px 16px 0; }
        .tpw-hero { min-height:148px; padding:20px; margin:12px 0 20px; }
        .tpw-progress-label { display:flex; justify-content:space-between; align-items:baseline; gap:12px; margin-bottom:10px; font-size:13px; }
        .tpw-progress-label span { color:var(--mute); font-weight:500; }
        .tpw-progress-label strong { color:var(--ink); font-weight:600; }
        .tpw-steps { gap:4px; }
        .tpw-steps li { padding:0; font-size:0; border-top-width:4px; border-radius:2px; }
        .tpw-steps li span { display:none; }
        .tpw-panel { padding:0; background:none; border:0; border-radius:0; }
        .tpw-grid2 { grid-template-columns:minmax(0,1fr); }
        .tpw-cards4 { grid-template-columns:repeat(2,minmax(0,1fr)); }
        .tpw-cards3 { grid-template-columns:minmax(0,1fr); }
        .tpw-cards3 .tpw-card { padding:14px 48px 14px 16px; }
        .tpw-policy dl { grid-template-columns:minmax(0,1fr); gap:2px; } .tpw-policy dd { margin-bottom:10px; } .tpw-policy dd:last-child { margin-bottom:0; }
        .tpw-receipt { padding:4px 16px 16px; }
        .tpw-radio { padding:14px; gap:12px; }
    }
    @media (max-width: 359px) {
        .tpw-nav { gap:6px; padding-left:12px; padding-right:12px; }
        .tpw-btn-next { padding:0 14px; } .tpw-btn-next .bi-arrow-right { display:none; }
        .tpw-btn-ghost { width:44px; }
        .tpw-bar-total strong { font-size:15px; min-width:0; }
    }
    @media (prefers-reduced-motion: reduce) {
        .tpw *, .tpw *::before, .tpw *::after { animation-duration:.01ms !important; transition-duration:.01ms !important; }
    }
</style>

<script>
(function () {
    const W = @json($wizard);
    const LABELS = @json($stepLabels);
    const form = document.getElementById('tpwForm');
    const $ = (id) => document.getElementById(id);
    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    const money = (n) => { const v = Number(n), whole = Number.isInteger(v);
        return 'AED ' + v.toLocaleString('en-US', {minimumFractionDigits: whole ? 0 : 2, maximumFractionDigits: 2}); };
    const csrf = form.querySelector('input[name=_token]').value;
    const mobile = window.matchMedia('(max-width: 991px)');
    let step = 1, lastQuote = null, quoteTimer = null, quoteSeq = 0, shownTotal = null;

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
        const icon = v.tone === 'ok' ? 'bi-check-circle-fill' : v.tone === 'warn' ? 'bi-exclamation-triangle-fill' : 'bi-info-circle';
        let html = '<strong><i class="bi ' + icon + '"></i>' + esc(v.label) + '</strong>';
        const facts = [];
        if (v.max_stay_days) facts.push('Stay up to ' + v.max_stay_days + ' days');
        if (v.processing_days) facts.push('Processing about ' + v.processing_days + ' days');
        if (v.fee) facts.push('Fee about ' + money(v.fee));
        if (facts.length) html += '<div>' + facts.map(esc).join(' · ') + '</div>';
        if (v.documents) html += '<div>Documents: ' + esc(v.documents) + '</div>';
        if (v.notes) html += '<div>' + esc(v.notes) + '</div>';
        box.innerHTML = html; box.hidden = false;
    }

    // ---------- step 2: travellers ----------
    function setAdults(n) { form.adults.value = Math.max(1, Math.min(50, n)); syncSteppers(); requote(); }
    function syncSteppers() {
        const t = form.traveler_type.value, fixed = (t === 'solo' || t === 'couple'), n = +form.adults.value;
        form.querySelector('[data-adj][data-by="-1"]').disabled = fixed || n <= 1;
        form.querySelector('[data-adj][data-by="1"]').disabled = fixed || n >= 50;
    }
    form.querySelectorAll('[data-adj]').forEach(b => b.addEventListener('click', () => setAdults(+form.adults.value + +b.dataset.by)));
    const ageLabel = (a) => a === 0 ? 'Under 1' : a + (a === 1 ? ' year' : ' years');
    function renderChildren() {
        $('tpwChildList').innerHTML = state.childAges.map((age, i) =>
            '<span class="tpw-child">Child ' + (i + 1) + ' <select data-child="' + i + '" aria-label="Age of child ' + (i + 1) + '">' +
            Array.from({length: 18}, (_, a) => '<option value="' + a + '"' + (a === age ? ' selected' : '') + '>' + ageLabel(a) + '</option>').join('') +
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
        syncSteppers(); renderChildren(); requote();
    }

    // ---------- step 3: duration & hotel ----------
    function renderDurations() {
        $('tpwDurations').innerHTML = W.durations.map(d =>
            '<label class="tpw-tab"><input type="radio" name="days_choice" value="' + d + '"' + (d === state.days ? ' checked' : '') + '><strong>' + d + ' days</strong><small>' + (d - 1) + ' nights</small></label>').join('');
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
                (h && h.location ? '<small>' + esc(h.location) + '</small>' : '') + '<span class="tpw-tick" aria-hidden="true"><i class="bi bi-check-lg"></i></span></label>';
        }).join('');
        renderPolicy(); renderOptions();
    }
    $('tpwTiers').addEventListener('change', e => { state.stars = +e.target.value; renderPolicy(); renderOptions(); requote(); });
    function renderPolicy() {
        const h = W.hotels[state.stars], box = $('tpwHotelPolicy');
        if (!h) { box.hidden = true; return; }
        box.innerHTML = '<dl>' +
            '<dt>Check-in / out</dt><dd>From ' + esc(h.check_in) + ' · by ' + esc(h.check_out) + '</dd>' +
            (h.late_check_in ? '<dt>Late check-in</dt><dd>' + esc(h.late_check_in) + '</dd>' : '') +
            (h.late_check_out ? '<dt>Late check-out</dt><dd>' + esc(h.late_check_out) + '</dd>' : '') +
            '<dt>Meals served</dt><dd>' + h.meal_plans.map(c => esc(W.mealPlans[c])).join(' · ') + '</dd></dl>';
        box.hidden = false;
        box.style.animation = 'none'; void box.offsetWidth; box.style.animation = '';
    }

    // ---------- step 4: options ----------
    const priceTag = (a) => a.price > 0
        ? '<span class="tpw-price">+' + money(a.price) + (a.basis ? '<small>' + esc(a.basis) + '</small>' : '') + '</span>'
        : '<span class="tpw-price is-free">Included</span>';
    function optRow(type, name, o, checked, extra = '') {
        return '<label class="tpw-radio"><input type="' + type + '" name="' + name + '" value="' + esc(o.code) + '"' + (checked ? ' checked' : '') + extra + '>' +
            '<span class="tpw-ind' + (type === 'checkbox' ? ' is-box' : '') + '" aria-hidden="true"></span>' +
            '<span class="tpw-opt-text">' + esc(o.label) + (o.description ? '<small>' + esc(o.description) + '</small>' : '') + '</span>' + priceTag(o) + '</label>';
    }
    function radioGroup(name, title, options, current) {
        if (!options || !options.length) return '';
        const sel = current && options.some(o => o.code === current) ? current : (options.find(o => !o.price) || options[0]).code;
        return '<div class="tpw-opt" role="radiogroup"><div class="tpw-opt-title">' + title + '</div>' + options.map(o => optRow('radio', name, o, o.code === sel)).join('') + '</div>';
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
        $('tpwMeals').innerHTML = radioGroup('meal_plan', 'Meals <small>· only what this hotel serves</small>', meals, keep('meal_plan'));
        const ins = (W.addons.insurance || [])[0];
        $('tpwInsurance').innerHTML = !ins ? '' : '<div class="tpw-opt"><div class="tpw-opt-title">Travel insurance</div>' +
            optRow('checkbox', 'insurance', {...ins, code: '1', description: W.insuranceRequired ? 'Required for this destination' : ins.description},
                W.insuranceRequired || !!keep('insurance'), W.insuranceRequired ? ' disabled' : '') + '</div>';
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
        const was = !!form.querySelector('[name=airport_transfer]:checked');
        $('tpwTransfer').innerHTML = !t ? '' : '<label class="tpw-toggle"><input type="checkbox" role="switch" name="airport_transfer" value="1"' + (was ? ' checked' : '') + '>' +
            '<span><strong>Airport pickup to your hotel</strong><small>' + esc(t.label) + ' · ' + (t.price > 0 ? '+' + money(t.price) + ' ' + esc(t.basis) : 'Included') + '</small></span>' +
            '<span class="tpw-switch" aria-hidden="true"></span></label>';
    }

    // ---------- live quote ----------
    function setLoading(on) { [$('tpwTotal'), $('tpwBarAmount')].forEach(el => el.classList.toggle('is-loading', on && shownTotal === null)); }
    function requote() { clearTimeout(quoteTimer); quoteTimer = setTimeout(fetchQuote, 250); }
    async function fetchQuote() {
        const seq = ++quoteSeq;
        const data = payload();
        if (!data.start_date || !state.stars) return;
        setLoading(true);
        try {
            const r = await fetch(W.urls.quote, {method: 'POST', headers: {'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf}, body: JSON.stringify(data)});
            if (seq !== quoteSeq) return; // a newer request is on its way
            const res = await r.json();
            lastQuote = res.ok ? res.quote : null;
            renderSummary(res);
        } catch (e) {
            if (seq === quoteSeq) { lastQuote = null; renderSummary({ok: false, errors: {net: 'We could not reach the server. Check your connection and try again.'}}); }
        } finally {
            if (seq === quoteSeq) setLoading(false);
        }
    }
    // The total counts to its new value instead of jumping.
    function showTotal(v) {
        const els = [$('tpwTotal'), $('tpwBarAmount')];
        if (v === null) { shownTotal = null; els.forEach(el => el.textContent = '—'); return; }
        const from = shownTotal ?? v, t0 = performance.now(), dur = from === v ? 0 : 450;
        shownTotal = v;
        const tick = (t) => {
            const p = dur ? Math.min(1, (t - t0) / dur) : 1, e = 1 - Math.pow(1 - p, 3);
            const cur = p < 1 ? Math.round(from + (v - from) * e) : v;
            els.forEach(el => el.textContent = money(cur));
            if (p < 1 && shownTotal === v) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    }
    function renderSummary(res) {
        if (!res.ok) {
            $('tpwSummary').innerHTML = '<p class="tpw-muted">' + esc(Object.values(res.errors || {})[0] || 'Complete the steps to see your price.') + '</p>';
            showTotal(null); return;
        }
        const q = res.quote;
        const who = [q.adults && q.adults + ' adult' + (q.adults > 1 ? 's' : ''), q.children && q.children + ' child' + (q.children > 1 ? 'ren' : ''), q.infants && q.infants + ' infant' + (q.infants > 1 ? 's' : '')].filter(Boolean).join(', ');
        const d = (s) => new Date(s + 'T00:00:00').toLocaleDateString('en-GB', {day: 'numeric', month: 'short'});
        $('tpwSummary').innerHTML =
            '<div><span>Dates</span><span>' + esc(d(q.start_date)) + ' – ' + esc(d(q.end_date)) + '</span></div>' +
            '<div><span>Stay</span><span>' + q.days + ' days · ' + q.hotel_stars + '★' + (q.hotel ? ' ' + esc(q.hotel.name) : '') + '</span></div>' +
            '<div><span>Travellers</span><span>' + esc(who) + '</span></div>' +
            '<div><span>Meals</span><span>' + esc(q.selections.meal_plan_label) + '</span></div>' +
            (q.season ? '<div><span>Season</span><span>' + esc(q.season) + '</span></div>' : '');
        showTotal(q.total);
        if (q.visa) renderVisa(q.visa);
        $('tpwReceipt').innerHTML = q.lines.map(l => '<div class="tpw-receipt-row"><span>' + esc(l.label) + '<small>' + esc(l.detail) + '</small></span><span>' + money(l.amount) + '</span></div>').join('') +
            '<div class="tpw-receipt-total"><span>Total to pay</span><span>' + money(q.total) + '</span></div>' +
            (q.hotel ? '<p class="tpw-small">' + esc(q.hotel.name) + ': check-in from ' + esc(q.hotel.check_in) + ', check-out by ' + esc(q.hotel.check_out) + '.' +
                (q.hotel.late_check_in ? ' Late check-in: ' + esc(q.hotel.late_check_in) : '') + (q.hotel.late_check_out ? ' Late check-out: ' + esc(q.hotel.late_check_out) : '') + '</p>' : '') +
            (q.visa ? '<p class="tpw-small">Visa: ' + esc(q.visa.label) + (q.visa.documents ? ' (' + esc(q.visa.documents) + ')' : '') + '. Visa fees are not included.</p>' : '');
    }

    // ---------- summary sheet (phones and tablets) ----------
    function sheet(open) {
        const panel = $('tpwSummaryPanel'), bd = $('tpwBackdrop');
        $('tpwBarTotal').setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) { bd.hidden = false; requestAnimationFrame(() => { bd.classList.add('is-open'); panel.classList.add('is-open'); }); }
        else { bd.classList.remove('is-open'); panel.classList.remove('is-open'); setTimeout(() => { if (!panel.classList.contains('is-open')) bd.hidden = true; }, 260); }
    }
    $('tpwBarTotal').addEventListener('click', () => sheet(!$('tpwSummaryPanel').classList.contains('is-open')));
    $('tpwBackdrop').addEventListener('click', () => sheet(false));
    $('tpwSheetClose').addEventListener('click', () => sheet(false));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') sheet(false); });
    mobile.addEventListener('change', () => sheet(false));

    // ---------- navigation ----------
    function stepError(msg) { const e = $('tpwStepError'); e.textContent = msg || ''; e.hidden = !msg; }
    // A new message is brought into view; on a phone it would otherwise sit below the fold.
    function showStepError(msg) {
        stepError(msg);
        const e = $('tpwStepError'), r = e.getBoundingClientRect(), bar = mobile.matches ? 96 : 0;
        if (r.top < 120 || r.bottom > window.innerHeight - bar) e.scrollIntoView({behavior: 'smooth', block: 'center'});
    }
    // Once a message is showing, keep it in step with the form: it goes when the field is fixed,
    // or moves on to the next thing still missing.
    ['input', 'change'].forEach(t => form.addEventListener(t, () => { if (!$('tpwStepError').hidden) stepError(check(step)); }));
    function check(n) {
        if (n === 1) {
            if (!form.start_date.value) return 'Choose your travel start date.';
            if (!form.nationality.value) return 'Choose your passport nationality.';
        }
        if (n === 2 && form.traveler_type.value === 'family' && !state.childAges.length) return 'Add each child\'s age, or choose another traveller type.';
        if (n === 3 && !state.stars) return 'Choose a hotel.';
        return '';
    }
    function go(n, first) {
        const back = n < step;
        step = Math.max(1, Math.min(6, n));
        document.querySelectorAll('.tpw-step').forEach(s => {
            const on = +s.dataset.step === step;
            s.hidden = !on;
            s.classList.remove('is-enter', 'is-back');
            if (on && !first) { void s.offsetWidth; s.classList.add('is-enter'); s.classList.toggle('is-back', back); }
        });
        document.querySelectorAll('[data-step-dot]').forEach(d => {
            d.classList.toggle('is-current', +d.dataset.stepDot === step);
            d.classList.toggle('is-done', +d.dataset.stepDot < step);
        });
        $('tpwStepCount').textContent = 'Step ' + step + ' of 6';
        $('tpwStepName').textContent = LABELS[step - 1];
        $('tpwPrev').hidden = step === 1;
        $('tpwNext').hidden = step === 6;
        $('tpwPay').hidden = step !== 6;
        stepError(''); sheet(false);
        if (first) return;
        // Land with the progress bar just below the sticky header, whatever its height on this screen.
        const header = document.querySelector('.gt-header');
        const top = document.querySelector('.tpw-progress').getBoundingClientRect().top + window.scrollY;
        window.scrollTo({top: Math.max(0, top - (header ? header.offsetHeight : 90) - 16), behavior: 'smooth'});
    }
    // Locked while the price loads, so a double tap on a slow connection cannot skip a step.
    $('tpwNext').addEventListener('click', async () => {
        const btn = $('tpwNext');
        if (btn.disabled) return;
        const msg = check(step);
        if (msg) return showStepError(msg);
        btn.disabled = true;
        const slow = setTimeout(() => btn.classList.add('is-busy'), 250);
        try {
            if (step >= 2) {
                await fetchQuote();
                if (!lastQuote && step >= 3) return showStepError($('tpwSummary').textContent);
            }
            go(step + 1);
        } finally {
            clearTimeout(slow); btn.classList.remove('is-busy'); btn.disabled = false;
        }
    });
    $('tpwPrev').addEventListener('click', () => go(step - 1));

    // ---------- pay ----------
    $('tpwPay').addEventListener('click', async () => {
        const err = $('tpwPayError'); err.hidden = true;
        if (!form.accept_terms.checked) {
            err.textContent = 'Tick the box to confirm you have checked the details.'; err.hidden = false;
            err.scrollIntoView({behavior: 'smooth', block: 'center'}); return;
        }
        if (!form.reportValidity()) return;
        const btn = $('tpwPay');
        btn.disabled = true; btn.classList.add('is-busy');
        try {
            const data = payload(); data.accept_terms = 1;
            const r = await fetch(W.urls.checkout, {method: 'POST', headers: {'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf}, body: JSON.stringify(data)});
            const res = await r.json();
            if (res.ok && res.checkout_url) { window.location.href = res.checkout_url; return; }
            throw new Error(res.message || Object.values(res.errors || {}).flat()[0] || 'Payment could not be started.');
        } catch (e) {
            err.textContent = e.message; err.hidden = false; btn.disabled = false; btn.classList.remove('is-busy');
            err.scrollIntoView({behavior: 'smooth', block: 'center'});
        }
    });

    renderDurations(); renderTimeline(); applyType(); go(1, true);
})();
</script>

@include('footer')
