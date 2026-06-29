@include('header')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .fifa-page {
        background:
            radial-gradient(circle at 20% -10%, rgba(255,210,63,.10), transparent 45%),
            radial-gradient(circle at 100% 0%, rgba(0,90,170,.18), transparent 40%),
            #06060a;
        color:#fff;
        min-height:100vh;
        font-family:'Outfit',sans-serif;
        padding-bottom:120px;
    }
    .fifa-wrap { max-width:1180px; margin:0 auto; padding:0 24px; }

    /* ── Hero ─────────────────────────────── */
    .fifa-hero { padding:90px 0 56px; text-align:center; }
    .fifa-kicker {
        display:inline-flex; align-items:center; gap:10px;
        font-size:12px; font-weight:700; letter-spacing:3px; text-transform:uppercase;
        color:#FFD23F; border:1px solid rgba(255,210,63,.3);
        padding:8px 18px; border-radius:100px; background:rgba(255,210,63,.06);
        margin-bottom:26px;
    }
    .fifa-hero h1 {
        font-size:clamp(40px,7vw,86px); font-weight:800; line-height:.98;
        letter-spacing:-.03em; margin:0 0 18px;
    }
    .fifa-hero h1 span { color:#FFD23F; }
    .fifa-hero p { color:rgba(255,255,255,.6); font-size:18px; font-weight:300; max-width:600px; margin:0 auto; line-height:1.6; }
    .fifa-hero-meta { display:flex; gap:36px; justify-content:center; margin-top:40px; flex-wrap:wrap; }
    .fifa-hero-meta .item { text-align:center; }
    .fifa-hero-meta .num { font-size:30px; font-weight:800; color:#FFD23F; }
    .fifa-hero-meta .lbl { font-size:12px; letter-spacing:1.5px; text-transform:uppercase; color:rgba(255,255,255,.45); margin-top:4px; }

    /* ---- Live scores ---- */
    .fifa-live { margin:6px 0 54px; }
    .fifa-live-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; flex-wrap:wrap; gap:8px; }
    .fifa-live-badge { display:inline-flex; align-items:center; gap:10px; font-size:14px; font-weight:800; letter-spacing:2px; text-transform:uppercase; color:#fff; }
    .fifa-live-badge .dot { width:11px; height:11px; border-radius:50%; background:#ff3b3b; animation:fifaPulse 1.4s infinite; }
    .fifa-live-badge.is-upcoming .dot { background:#FFD23F; animation:none; }
    @keyframes fifaPulse { 0%{box-shadow:0 0 0 0 rgba(255,59,59,.55);} 70%{box-shadow:0 0 0 11px rgba(255,59,59,0);} 100%{box-shadow:0 0 0 0 rgba(255,59,59,0);} }
    .fifa-live-updated { font-size:12px; color:rgba(255,255,255,.4); }
    .fifa-live-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:16px; }
    .fifa-live-card { background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.1); border-radius:16px; padding:18px 20px; transition:border-color .25s ease; }
    .fifa-live-card:hover { border-color:rgba(255,210,63,.45); }
    .fifa-live-row { display:flex; align-items:center; justify-content:space-between; gap:10px; }
    .fifa-live-row + .fifa-live-row { margin-top:10px; }
    .fifa-live-team { display:flex; align-items:center; gap:11px; min-width:0; }
    .fifa-live-team img { width:28px; height:28px; object-fit:contain; border-radius:4px; flex-shrink:0; }
    .fifa-live-team span { color:#fff; font-weight:600; font-size:15px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .fifa-live-score { font-size:22px; font-weight:800; color:#FFD23F; min-width:24px; text-align:right; }
    .fifa-live-meta { text-align:center; margin-top:14px; padding-top:12px; border-top:1px solid rgba(255,255,255,.07); font-size:12px; letter-spacing:1px; text-transform:uppercase; color:rgba(255,255,255,.5); }
    .fifa-live-meta .min { color:#ff5b5b; font-weight:700; }

    /* ── Stage section ────────────────────── */
    .fifa-stage { margin-top:56px; }
    .fifa-stage-title { font-size:14px; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:rgba(255,255,255,.5); display:flex; align-items:center; gap:14px; margin-bottom:22px; }
    .fifa-stage-title::after { content:''; flex:1; height:1px; background:linear-gradient(90deg, rgba(255,210,63,.35), transparent); }

    /* ── Match card ───────────────────────── */
    .fifa-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(360px,1fr)); gap:20px; }
    .fifa-card {
        background:linear-gradient(160deg, rgba(255,255,255,.05), rgba(255,255,255,.015));
        border:1px solid rgba(255,255,255,.08); border-radius:18px; overflow:hidden;
        transition:transform .25s ease, border-color .25s ease;
    }
    .fifa-card:hover { transform:translateY(-4px); border-color:rgba(255,210,63,.35); }
    .fifa-card-head { padding:20px 22px 16px; border-bottom:1px solid rgba(255,255,255,.07); }
    .fifa-code { font-size:11px; font-weight:700; letter-spacing:1px; color:#FFD23F; }
    .fifa-date { display:flex; align-items:center; gap:6px; margin-top:6px; font-size:12px; font-weight:500; color:rgba(255,255,255,.6); line-height:1.3; }
    .fifa-date i { color:#FFD23F; font-size:12px; flex-shrink:0; }
    .fifa-teams { font-size:21px; font-weight:700; letter-spacing:-.01em; margin-top:6px; line-height:1.2; }
    .fifa-teams .vs { color:rgba(255,255,255,.4); font-weight:400; font-size:15px; margin:0 6px; }
    .fifa-side { display:inline-flex; align-items:center; gap:8px; }
    .fifa-flag {
        width:30px; height:21px; border-radius:3px; object-fit:cover; flex-shrink:0;
        box-shadow:0 0 0 1px rgba(255,255,255,.14); background:rgba(255,255,255,.06);
    }
    .fifa-from { font-size:12px; color:rgba(255,255,255,.5); margin-top:8px; }
    .fifa-from b { color:#fff; font-size:15px; font-weight:700; }

    .fifa-tickets-list { padding:8px 12px 14px; }
    .fifa-tk {
        display:flex; align-items:center; gap:12px; padding:12px 10px;
        border-bottom:1px solid rgba(255,255,255,.05);
    }
    .fifa-tk:last-child { border-bottom:none; }
    .fifa-tk-cat { font-size:11px; font-weight:700; color:#06060a; background:#FFD23F; padding:3px 9px; border-radius:5px; flex-shrink:0; }
    .fifa-tk-info { flex:1; min-width:0; }
    .fifa-tk-seat { font-size:13px; color:rgba(255,255,255,.85); }
    .fifa-tk-sub { font-size:11px; color:rgba(255,255,255,.45); }
    .fifa-tk-price { font-size:17px; font-weight:800; color:#fff; }
    .fifa-tk-price small { display:block; font-size:10px; font-weight:500; color:rgba(255,255,255,.4); text-align:right; }
    .fifa-req-btn {
        background:#FFD23F; color:#06060a; border:none; font-weight:700; font-size:12px;
        padding:8px 14px; border-radius:8px; cursor:pointer; flex-shrink:0; transition:opacity .15s;
        white-space:nowrap;
    }
    .fifa-req-btn:hover { opacity:.85; }

    .fifa-note { text-align:center; margin-top:60px; color:rgba(255,255,255,.4); font-size:13px; line-height:1.7; }
    .fifa-note a { color:#FFD23F; text-decoration:none; }

    .fifa-alert {
        max-width:680px; margin:0 auto 8px; background:rgba(46,160,93,.15); border:1px solid rgba(46,160,93,.4);
        color:#7ee2a8; padding:16px 20px; border-radius:12px; text-align:center; font-size:14px;
    }
    .fifa-empty { text-align:center; padding:80px 0; color:rgba(255,255,255,.5); }

    /* =========================================================
       REQUEST TICKETS MODAL — premium redesign (dark base; light
       overrides live in public/css/gt-theme.css). Class prefix: frm-
       ========================================================= */
    #fifaRequestModal .frm-dialog { max-width:560px; }
    #fifaRequestModal .frm-content {
        background:#0e0e16; border:1px solid rgba(255,210,63,.22); border-radius:16px; color:#fff;
        box-shadow:0 24px 64px rgba(0,0,0,.55); overflow:hidden;
    }
    /* Header */
    #fifaRequestModal .frm-header {
        display:flex; align-items:center; justify-content:space-between;
        padding:14px 18px 12px; border-bottom:1px solid rgba(255,255,255,.07);
    }
    #fifaRequestModal .frm-title {
        display:inline-flex; align-items:center; gap:9px; margin:0;
        font-size:16px; font-weight:700; color:#fff; letter-spacing:-.01em;
    }
    #fifaRequestModal .frm-title i { color:#FFD23F; font-size:15px; }
    #fifaRequestModal .frm-close {
        width:30px; height:30px; flex-shrink:0; border-radius:8px; border:1px solid rgba(255,255,255,.12);
        background:rgba(255,255,255,.04); color:rgba(255,255,255,.65); font-size:13px;
        display:inline-flex; align-items:center; justify-content:center; cursor:pointer;
        transition:background .2s ease, color .2s ease;
    }
    #fifaRequestModal .frm-close:hover { background:rgba(255,255,255,.1); color:#fff; }
    /* Body */
    #fifaRequestModal .frm-body { padding:13px 18px 2px; }
    /* Summary card — compact */
    #fifaRequestModal .frm-summary {
        background:linear-gradient(135deg, rgba(255,210,63,.1), rgba(255,210,63,.03));
        border:1px solid rgba(255,210,63,.22); border-radius:10px; padding:10px 12px; margin-bottom:12px;
    }
    #fifaRequestModal .frm-summary:empty { display:none; }
    #fifaRequestModal .frm-sum-match { font-size:14px; font-weight:700; color:#fff; margin:0 0 4px; }
    #fifaRequestModal .frm-sum-row { display:flex; align-items:center; gap:8px; font-size:12px; color:rgba(255,255,255,.72); margin-top:2px; }
    #fifaRequestModal .frm-sum-row i { color:#FFD23F; width:13px; text-align:center; font-size:11px; flex-shrink:0; }
    #fifaRequestModal .frm-sum-row b { color:#FFD23F; font-weight:700; }
    /* Grid: 2 columns on desktop/tablet, 1 on mobile */
    #fifaRequestModal .frm-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px 12px; }
    #fifaRequestModal .frm-col-full { grid-column:1 / -1; }
    #fifaRequestModal .frm-field { display:flex; flex-direction:column; min-width:0; }
    #fifaRequestModal .frm-field label {
        font-size:11.5px; font-weight:600; color:rgba(255,255,255,.6); margin-bottom:4px; letter-spacing:.2px;
    }
    #fifaRequestModal .frm-req { color:#FFD23F; }
    #fifaRequestModal .frm-opt { color:rgba(255,255,255,.35); font-weight:400; }
    /* Inputs (uniform ~44px height) */
    #fifaRequestModal .frm-input {
        height:44px; width:100%; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.12);
        color:#fff; border-radius:10px; padding:0 12px; font-size:14px; font-family:'Outfit',sans-serif;
        transition:border-color .18s ease, box-shadow .18s ease, background .18s ease; outline:none;
    }
    #fifaRequestModal textarea.frm-input { height:auto; padding:9px 12px; resize:vertical; line-height:1.45; }
    #fifaRequestModal .frm-input::placeholder { color:rgba(255,255,255,.32); }
    #fifaRequestModal .frm-input:focus {
        border-color:#FFD23F; background:rgba(255,255,255,.07); box-shadow:0 0 0 3px rgba(255,210,63,.16);
    }
    #fifaRequestModal .frm-select {
        appearance:none; -webkit-appearance:none; -moz-appearance:none; cursor:pointer; padding-right:34px;
        background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='none' stroke='%23FFD23F' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round' d='M1 1.5 6 6.5 11 1.5'/%3E%3C/svg%3E");
        background-repeat:no-repeat; background-position:right 12px center;
    }
    #fifaRequestModal .frm-select option { background:#0e0e16; color:#fff; }
    /* Quantity stepper — compact */
    #fifaRequestModal .frm-stepper {
        display:flex; align-items:center; height:44px; border:1px solid rgba(255,255,255,.12);
        border-radius:10px; overflow:hidden; background:rgba(255,255,255,.05);
        transition:border-color .18s ease, box-shadow .18s ease;
    }
    #fifaRequestModal .frm-stepper:focus-within { border-color:#FFD23F; box-shadow:0 0 0 3px rgba(255,210,63,.16); }
    #fifaRequestModal .frm-step {
        width:40px; height:100%; flex-shrink:0; border:none; background:transparent; color:#FFD23F;
        font-size:18px; font-weight:600; cursor:pointer; transition:background .15s ease;
        display:flex; align-items:center; justify-content:center;
    }
    #fifaRequestModal .frm-step:hover { background:rgba(255,210,63,.12); }
    #fifaRequestModal .frm-step:disabled { color:rgba(255,255,255,.2); cursor:not-allowed; background:transparent; }
    #fifaRequestModal .frm-step-input {
        flex:1; min-width:0; height:100%; border:none; background:transparent; color:#fff; text-align:center;
        font-size:14px; font-weight:600; outline:none; -moz-appearance:textfield;
    }
    #fifaRequestModal .frm-step-input::-webkit-outer-spin-button,
    #fifaRequestModal .frm-step-input::-webkit-inner-spin-button { -webkit-appearance:none; margin:0; }
    /* Inline validation */
    #fifaRequestModal .frm-input-invalid,
    #fifaRequestModal .frm-input-invalid:focus { border-color:#e5484d; box-shadow:0 0 0 3px rgba(229,72,77,.16); }
    #fifaRequestModal .frm-error { display:block; color:#f1a5a5; font-size:11px; margin-top:3px; min-height:0; }
    #fifaRequestModal .frm-error:empty { margin-top:0; }
    /* Submit button (compact, proportional) + footer */
    .fifa-btn-submit {
        display:inline-flex; align-items:center; justify-content:center; gap:7px;
        height:44px; padding:0 20px; background:#FFD23F; color:#06060a; border:none;
        border-radius:10px; font-size:15px; font-weight:600; line-height:1;
        transition:opacity .2s ease, transform .2s ease, box-shadow .2s ease;
    }
    .fifa-btn-submit i { font-size:14px; }
    .fifa-btn-submit:hover { opacity:.92; color:#06060a; transform:translateY(-1px); box-shadow:0 6px 16px rgba(255,210,63,.3); }
    #fifaRequestModal .frm-footer {
        display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;
        padding:12px 18px 16px; border-top:1px solid rgba(255,255,255,.07);
    }
    #fifaRequestModal .frm-actions { display:flex; align-items:center; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
    #fifaRequestModal .frm-pay { background:#16a34a; color:#fff; }
    #fifaRequestModal .frm-pay:hover { background:#15803d; color:#fff; box-shadow:0 6px 16px rgba(22,163,74,.3); }
    #fifaRequestModal .frm-cancel {
        background:transparent; border:none; color:rgba(255,255,255,.6); font-size:14px; font-weight:500;
        cursor:pointer; padding:6px 4px; transition:color .15s ease;
    }
    #fifaRequestModal .frm-cancel:hover { color:#fff; }
    /* Responsive: single column + tighter padding on mobile */
    @media (max-width:600px){
        #fifaRequestModal .frm-grid { grid-template-columns:1fr; }
        #fifaRequestModal .frm-header,
        #fifaRequestModal .frm-body,
        #fifaRequestModal .frm-footer { padding-left:16px; padding-right:16px; }
    }
    @media (max-width:560px){ .fifa-grid{ grid-template-columns:1fr; } }

    /* ── Search / filter matches by team or country ── */
    .fifa-search-wrap { position:relative; max-width:480px; margin:0 auto 34px; }
    .fifa-search-wrap > i { position:absolute; left:18px; top:50%; transform:translateY(-50%); color:#FFD23F; font-size:14px; pointer-events:none; }
    .fifa-search-input {
        width:100%; height:50px; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.12);
        border-radius:100px; color:#fff; padding:0 20px 0 44px; font-size:15px; font-family:'Outfit',sans-serif;
        outline:none; transition:border-color .2s ease, box-shadow .2s ease;
    }
    .fifa-search-input::placeholder { color:rgba(255,255,255,.4); }
    .fifa-search-input:focus { border-color:#FFD23F; box-shadow:0 0 0 3px rgba(255,210,63,.15); }
    .fifa-search-empty { text-align:center; color:rgba(255,255,255,.55); font-size:15px; padding:24px 0 40px; }
</style>

<div class="fifa-page">
    <div class="fifa-wrap">
        {{-- Hero --}}
        <div class="fifa-hero">
            <span class="fifa-kicker"><i class="fas fa-futbol"></i> Official Ticket Availability</span>
            <h1>FIFA <span>World Cup</span><br>2026</h1>
            <p>Secure premium seats for football's greatest stage. Hand-picked categories, concierge-level service — request and our team handles the rest.</p>
            @php
                $allMatches = $matches->flatten();
                $ticketCount = $allMatches->sum(fn($m) => $m->activeTickets->count());
            @endphp
            <div class="fifa-hero-meta">
                <div class="item"><div class="num">{{ $allMatches->count() }}</div><div class="lbl">Matches</div></div>
                <div class="item"><div class="num">{{ $ticketCount }}</div><div class="lbl">Listings</div></div>
                <div class="item"><div class="num">16</div><div class="lbl">Host Cities</div></div>
            </div>
        </div>

        {{-- Live scores (API-Football, polled). Hidden until there is data. --}}
        <div class="fifa-live" id="fifaLive" style="display:none;">
            <div class="fifa-live-head">
                <span class="fifa-live-badge" id="fifaLiveBadge"><span class="dot"></span> <span id="fifaLiveLabel">Live Scores</span></span>
                <span class="fifa-live-updated" id="fifaLiveUpdated"></span>
            </div>
            <div class="fifa-live-grid" id="fifaLiveGrid"></div>
        </div>

        @if(session('fifa_success'))
            <div class="fifa-alert"><i class="fas fa-check-circle"></i> {{ session('fifa_success') }}</div>
        @endif
        @if($errors->any())
            <div class="fifa-alert" style="background:rgba(200,60,60,.15); border-color:rgba(200,60,60,.4); color:#f1a5a5;">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Search / filter matches by team or country (e.g. "Canada") --}}
        @if($matches->isNotEmpty())
        <div class="fifa-search-wrap">
            <i class="fas fa-search" aria-hidden="true"></i>
            <input type="text" id="fifaSearch" class="fifa-search-input" placeholder="Search by team or country — e.g. Canada" autocomplete="off" aria-label="Search matches by team or country">
        </div>
        <div class="fifa-search-empty" id="fifaSearchEmpty" style="display:none;">No matches found for &ldquo;<span id="fifaSearchTerm"></span>&rdquo;. Try another country or team.</div>
        @endif

        {{-- Matches by stage --}}
        @forelse($matches as $stage => $stageMatches)
        <div class="fifa-stage">
            <div class="fifa-stage-title">{{ $stage }}</div>
            <div class="fifa-grid">
                @foreach($stageMatches as $match)
                @php $minPrice = $match->activeTickets->min(fn($t) => $t->customer_price); @endphp
                <div class="fifa-card">
                    <div class="fifa-card-head">
                        <div class="fifa-code">MATCH {{ $match->match_code }}</div>
                        {{-- Match date (and kickoff time when present). Hidden entirely when no date is set. --}}
                        @if($match->match_date)
                        <div class="fifa-date">
                            <i class="bi bi-calendar-event" aria-hidden="true"></i>
                            <span>{{ $match->match_date->isoFormat('ddd, DD MMM YYYY') }}@if($match->match_date->format('H:i') !== '00:00') • {{ $match->match_date->format('g:i A') }}@endif</span>
                        </div>
                        @endif
                        <div class="fifa-teams">
                            <span class="fifa-side">@if($match->flag_a)<img class="fifa-flag" src="{{ $match->flag_a }}" alt="" loading="lazy">@endif{{ $match->team_a }}</span>
                            <span class="vs">vs</span>
                            <span class="fifa-side">@if($match->flag_b)<img class="fifa-flag" src="{{ $match->flag_b }}" alt="" loading="lazy">@endif{{ $match->team_b }}</span>
                        </div>
                        <div class="fifa-from">From <b>${{ number_format($minPrice, 0) }}</b> per ticket</div>
                    </div>
                    <div class="fifa-tickets-list">
                        @foreach($match->activeTickets as $t)
                        <div class="fifa-tk">
                            <span class="fifa-tk-cat">{{ $t->category }}</span>
                            <div class="fifa-tk-info">
                                <div class="fifa-tk-seat">{{ $t->seat_label ?: 'Best available' }}</div>
                                <div class="fifa-tk-sub">{{ $t->quantity }} available</div>
                            </div>
                            <div class="fifa-tk-price">${{ number_format($t->customer_price, 0) }}<small>per ticket</small></div>
                            <button class="fifa-req-btn js-fifa-req"
                                    data-ticket="{{ $t->id }}"
                                    data-match="{{ $match->team_a }} vs {{ $match->team_b }}"
                                    data-cat="{{ $t->category }}"
                                    data-seat="{{ $t->seat_label }}"
                                    data-price="{{ number_format($t->customer_price, 0) }}"
                                    data-max="{{ $t->quantity }}">Request</button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="fifa-empty">
            <i class="fas fa-futbol" style="font-size:40px; color:rgba(255,255,255,.2); display:block; margin-bottom:16px;"></i>
            Ticket availability is being updated. Please check back soon.
        </div>
        @endforelse

        <div class="fifa-note">
            Prices are per ticket in USD and subject to availability at time of confirmation.<br>
            Need something not listed? <a href="#" class="js-fifa-req" data-ticket="" data-match="General enquiry" data-cat="" data-seat="" data-price="" data-max="50">Send us a request</a>.
        </div>
    </div>
</div>

{{-- Request Modal --}}
<div class="modal fade" id="fifaRequestModal" tabindex="-1" aria-labelledby="frmTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered frm-dialog">
    <form class="modal-content frm-content" id="fifaRequestForm" action="{{ route('fifa.request') }}" method="POST" novalidate>
      @csrf
      <input type="hidden" name="ticket_id" id="fr_ticket">

      <div class="modal-header frm-header">
        <h5 class="modal-title frm-title" id="frmTitle"><i class="fas fa-ticket-alt" aria-hidden="true"></i> Request Match Tickets</h5>
        <button type="button" class="frm-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times" aria-hidden="true"></i></button>
      </div>

      <div class="modal-body frm-body">
        {{-- Selected-ticket summary card (populated by JS from the clicked listing) --}}
        <div class="frm-summary" id="fr_summary" role="status" aria-live="polite"></div>

        <div class="frm-grid">
          {{-- Row 1 --}}
          <div class="frm-field">
            <label for="fr_name">Full Name <span class="frm-req" aria-hidden="true">*</span></label>
            <input id="fr_name" name="name" class="frm-input" required autocomplete="name">
            <span class="frm-error" data-for="fr_name" aria-live="polite"></span>
          </div>
          <div class="frm-field">
            <label for="fr_email">Email <span class="frm-req" aria-hidden="true">*</span></label>
            <input id="fr_email" type="email" name="email" class="frm-input" required autocomplete="email">
            <span class="frm-error" data-for="fr_email" aria-live="polite"></span>
          </div>

          {{-- Row 2 --}}
          <div class="frm-field">
            <label for="fr_phone">Phone</label>
            <input id="fr_phone" name="phone" class="frm-input" placeholder="+971 …" autocomplete="tel">
          </div>
          <div class="frm-field">
            <label for="fr_country">Country</label>
            <select id="fr_country" name="country" class="frm-input frm-select">
              <option value="">Select…</option>
              @foreach(config('countries') as $cName => $cInfo)
                <option value="{{ $cName }}">{{ $cName }}</option>
              @endforeach
            </select>
          </div>

          {{-- Row 3 --}}
          <div class="frm-field">
            <label for="fr_qty">Quantity <span class="frm-req" aria-hidden="true">*</span></label>
            <div class="frm-stepper">
              <button type="button" class="frm-step" id="fr_minus" aria-label="Decrease quantity" tabindex="-1">&minus;</button>
              <input id="fr_qty" name="quantity" class="frm-step-input" type="number" value="1" min="1" max="50" required inputmode="numeric" aria-label="Quantity">
              <button type="button" class="frm-step" id="fr_plus" aria-label="Increase quantity" tabindex="-1">+</button>
            </div>
          </div>
          <div class="frm-field">
            <label for="fr_seating">Preferred Seating</label>
            <input id="fr_seating" class="frm-input" placeholder="e.g. seats together, aisle…">
          </div>

          {{-- Row 4 --}}
          <div class="frm-field frm-col-full">
            <label for="fr_message">Special Requests <span class="frm-opt">(Optional)</span></label>
            <textarea id="fr_message" name="message" class="frm-input frm-textarea" rows="2" placeholder="Any seating preferences or additional requests…"></textarea>
          </div>
        </div>
      </div>

      <div class="modal-footer frm-footer">
        <button type="button" class="frm-cancel" data-bs-dismiss="modal">Cancel</button>
        <div class="frm-actions">
          <button type="button" id="fr_pay_btn" class="fifa-btn-submit frm-pay" style="display:none;">
            <i class="fas fa-credit-card"></i> Book &amp; Pay Online
          </button>
          <button type="submit" class="fifa-btn-submit frm-submit">Submit Request <i class="fas fa-arrow-right" aria-hidden="true"></i></button>
        </div>
      </div>
      <p id="fr_pay_note" class="text-secondary" style="display:none; font-size:12px; margin:0 16px 14px; opacity:.75;">
        Pay securely now to confirm your tickets, or submit a request and our team will contact you.
      </p>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // ---- Filter matches by team / country (e.g. "Canada") ----
    (function () {
        var search = document.getElementById('fifaSearch');
        if (!search) return;
        var emptyMsg = document.getElementById('fifaSearchEmpty');
        var termEl   = document.getElementById('fifaSearchTerm');
        search.addEventListener('input', function () {
            var q = this.value.trim().toLowerCase();
            var anyVisible = false;
            document.querySelectorAll('.fifa-stage').forEach(function (stage) {
                var stageVisible = false;
                stage.querySelectorAll('.fifa-card').forEach(function (card) {
                    var teamsEl = card.querySelector('.fifa-teams');
                    var teams = (teamsEl ? teamsEl.textContent : '').toLowerCase();
                    var show = !q || teams.indexOf(q) !== -1;
                    card.style.display = show ? '' : 'none';
                    if (show) stageVisible = true;
                });
                stage.style.display = stageVisible ? '' : 'none';
                if (stageVisible) anyVisible = true;
            });
            if (emptyMsg) emptyMsg.style.display = (q && !anyVisible) ? 'block' : 'none';
            if (termEl) termEl.textContent = this.value.trim();
        });
    })();

    var modalEl   = document.getElementById('fifaRequestModal');
    var modal     = new bootstrap.Modal(modalEl);
    var form      = document.getElementById('fifaRequestForm');
    var qty       = document.getElementById('fr_qty');
    var minus     = document.getElementById('fr_minus');
    var plus      = document.getElementById('fr_plus');
    var summaryEl = document.getElementById('fr_summary');

    function esc(s){ return (s==null?'':String(s)).replace(/[&<>"]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }

    // ---- Quantity stepper (respects min and the per-listing max set on open) ----
    function clampQty(){
        var min = parseInt(qty.min, 10) || 1, max = parseInt(qty.max, 10) || 50;
        var v = parseInt(qty.value, 10); if (isNaN(v)) v = min;
        v = Math.min(max, Math.max(min, v));
        qty.value = v;
        if (minus) minus.disabled = (v <= min);
        if (plus)  plus.disabled  = (v >= max);
    }
    function step(delta){ qty.value = (parseInt(qty.value, 10) || 1) + delta; clampQty(); }
    if (minus) minus.addEventListener('click', function(){ step(-1); });
    if (plus)  plus.addEventListener('click',  function(){ step(1); });
    qty.addEventListener('input', clampQty);

    function clearErrors(){
        form.querySelectorAll('.frm-input-invalid').forEach(function(f){ f.classList.remove('frm-input-invalid'); });
        form.querySelectorAll('.frm-error').forEach(function(s){ s.textContent = ''; });
    }

    // ---- Open the modal from a listing's "Request" button (unchanged contract) ----
    document.querySelectorAll('.js-fifa-req').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var d = this.dataset;
            document.getElementById('fr_ticket').value = d.ticket || '';
            qty.max = d.max || 50;
            clampQty();

            var html;
            if (d.ticket) {
                var rows = '';
                if (d.cat)   rows += '<div class="frm-sum-row"><i class="fas fa-layer-group" aria-hidden="true"></i> ' + esc(d.cat) + '</div>';
                if (d.seat)  rows += '<div class="frm-sum-row"><i class="fas fa-couch" aria-hidden="true"></i> ' + esc(d.seat) + '</div>';
                if (d.price) rows += '<div class="frm-sum-row"><i class="fas fa-tag" aria-hidden="true"></i> <b>$' + esc(d.price) + '</b> per ticket</div>';
                html = '<div class="frm-sum-match">' + esc(d.match) + '</div>' + rows;
            } else {
                html = '<div class="frm-sum-match">General enquiry</div><div class="frm-sum-row">Tell us which match and category you are after.</div>';
            }
            summaryEl.innerHTML = html;
            clearErrors();

            // Online payment is only offered for a specific priced ticket.
            var canPay = !!(d.ticket && d.price);
            var rowPayBtn  = document.getElementById('fr_pay_btn');
            var rowPayNote = document.getElementById('fr_pay_note');
            if (rowPayBtn)  rowPayBtn.style.display  = canPay ? '' : 'none';
            if (rowPayNote) rowPayNote.style.display = canPay ? '' : 'none';

            modal.show();
        });
    });

    // ---- Inline validation (UI only — server-side validation is untouched) ----
    form.querySelectorAll('[required]').forEach(function(field){
        field.addEventListener('input', function(){
            if (field.checkValidity()) {
                field.classList.remove('frm-input-invalid');
                var s = form.querySelector('.frm-error[data-for="' + field.id + '"]'); if (s) s.textContent = '';
            }
        });
    });

    // Fold the optional "Preferred Seating" hint into the existing `message` field.
    function mergeSeating(){
        var seating = document.getElementById('fr_seating');
        var msg = document.getElementById('fr_message');
        var s = seating ? seating.value.trim() : '';
        if (s && msg) { msg.value = 'Preferred seating: ' + s + (msg.value.trim() ? '\n' + msg.value : ''); seating.value = ''; }
    }

    form.addEventListener('submit', function (e) {
        var firstInvalid = null;
        form.querySelectorAll('[required]').forEach(function(field){
            var errEl = form.querySelector('.frm-error[data-for="' + field.id + '"]');
            if (!field.checkValidity()) {
                field.classList.add('frm-input-invalid');
                if (errEl) errEl.textContent = field.validationMessage;
                if (!firstInvalid) firstInvalid = field;
            } else {
                field.classList.remove('frm-input-invalid');
                if (errEl) errEl.textContent = '';
            }
        });
        if (firstInvalid) { e.preventDefault(); firstInvalid.focus(); return; }
        mergeSeating();
    });

    // Book & Pay Online — start a Nomod checkout, then redirect to the payment page.
    var fifaForm = document.querySelector('#fifaRequestModal form');
    var payBtn = document.getElementById('fr_pay_btn');
    if (payBtn && fifaForm) {
        payBtn.addEventListener('click', function () {
            mergeSeating();
            var get = function (n) { var el = fifaForm.querySelector('[name="' + n + '"]'); return el ? el.value.trim() : ''; };
            var ticketId = document.getElementById('fr_ticket').value;
            if (!ticketId) { alert('Please choose a specific ticket to pay online.'); return; }
            if (!get('name') || !get('email')) { alert('Please enter your name and email.'); return; }

            var orig = payBtn.innerHTML;
            payBtn.disabled = true; payBtn.innerHTML = 'Starting payment…';

            var fd = new FormData();
            ['name', 'email', 'phone', 'country', 'quantity', 'message'].forEach(function (n) { fd.set(n, get(n)); });
            fd.set('ticket_id', ticketId);

            fetch("{{ route('fifa.checkout') }}", {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': fifaForm.querySelector('input[name="_token"]').value },
                body: fd
            })
            .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
            .then(function (res) {
                if (res.ok && res.j.success && res.j.checkout_url) { window.location.href = res.j.checkout_url; }
                else { throw new Error(res.j.error || 'Could not start payment.'); }
            })
            .catch(function (err) {
                alert(err.message || 'Could not start payment. Please try again.');
                payBtn.disabled = false; payBtn.innerHTML = orig;
            });
        });
    }

    @if(session('fifa_success'))
        document.querySelector('.fifa-alert')?.scrollIntoView({behavior:'smooth', block:'center'});
    @endif
});
</script>

<script>
(function () {
    var box     = document.getElementById('fifaLive');
    var grid    = document.getElementById('fifaLiveGrid');
    var badge   = document.getElementById('fifaLiveBadge');
    var label   = document.getElementById('fifaLiveLabel');
    var updated = document.getElementById('fifaLiveUpdated');
    if (!box) return;

    // Append ?demo=1 to this page's URL to preview the UI without an API key.
    var demo = /[?&]demo=1/.test(window.location.search);
    var url  = "{{ route('fifa.live-scores') }}" + (demo ? '?demo=1' : '');

    function esc(s){ return (s==null?'':String(s)).replace(/[&<>"]/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]; }); }

    function card(m) {
        var liveStatuses = ['1H','2H','ET','BT','P','LIVE','HT'];
        var isLive = m.elapsed != null && liveStatuses.indexOf(m.status) !== -1;
        var hs = (m.home_goals == null) ? '–' : m.home_goals;
        var as = (m.away_goals == null) ? '–' : m.away_goals;
        var meta;
        if (m.status === 'HT') meta = 'Half Time';
        else if (isLive) meta = '<span class="min">' + esc(m.elapsed) + "&rsquo; LIVE</span>";
        else if (m.status === 'FT') meta = 'Full Time';
        else if (m.date) { var d = new Date(m.date); meta = d.toLocaleDateString([], {month:'short', day:'numeric'}) + ' &middot; ' + d.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}); }
        else meta = '';
        var comp = m.league || m.round;
        if (comp) meta += (meta ? ' &middot; ' : '') + esc(comp);
        var logo = function (u) { return u ? '<img src="' + esc(u) + '" alt="">' : ''; };
        return '<div class="fifa-live-card">'
            + '<div class="fifa-live-row"><div class="fifa-live-team">' + logo(m.home_logo) + '<span>' + esc(m.home) + '</span></div><div class="fifa-live-score">' + esc(hs) + '</div></div>'
            + '<div class="fifa-live-row"><div class="fifa-live-team">' + logo(m.away_logo) + '<span>' + esc(m.away) + '</span></div><div class="fifa-live-score">' + esc(as) + '</div></div>'
            + '<div class="fifa-live-meta">' + meta + '</div></div>';
    }

    function load() {
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (!d.configured || !d.matches || !d.matches.length) { box.style.display = 'none'; return; }
                label.textContent = (d.scope === 'world_cup') ? 'Live Scores' : 'Live Football Scores';
                updated.textContent = 'Auto-updating every 30s';
                grid.innerHTML = d.matches.map(card).join('');
                box.style.display = 'block';
            })
            .catch(function () { box.style.display = 'none'; });
    }

    load();
    setInterval(load, 30000);
})();
</script>

@include('footer')
