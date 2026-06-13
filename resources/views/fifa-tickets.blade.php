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

    /* Modal restyle for dark theme */
    #fifaRequestModal .modal-content { background:#0e0e16; border:1px solid rgba(255,210,63,.25); border-radius:18px; color:#fff; }
    #fifaRequestModal .modal-header, #fifaRequestModal .modal-footer { border-color:rgba(255,255,255,.08); }
    #fifaRequestModal label { font-size:12px; font-weight:600; color:rgba(255,255,255,.65); margin-bottom:5px; letter-spacing:.3px; }
    #fifaRequestModal .form-control, #fifaRequestModal .form-select {
        background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.12); color:#fff; border-radius:9px; padding:10px 12px;
    }
    #fifaRequestModal .form-control:focus, #fifaRequestModal .form-select:focus { border-color:#FFD23F; box-shadow:none; background:rgba(255,255,255,.07); }
    #fifaRequestModal .form-control::placeholder { color:rgba(255,255,255,.3); }
    #fifaRequestModal .form-select option { background:#0e0e16; }
    .fifa-summary { background:rgba(255,210,63,.08); border:1px solid rgba(255,210,63,.2); border-radius:10px; padding:12px 14px; font-size:13px; }
    .fifa-summary b { color:#FFD23F; }
    .fifa-btn-submit { background:#FFD23F; color:#06060a; border:none; font-weight:700; padding:11px 26px; border-radius:10px; }
    .fifa-btn-submit:hover { opacity:.88; color:#06060a; }
    @media (max-width:560px){ .fifa-grid{ grid-template-columns:1fr; } }
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

        @if(session('fifa_success'))
            <div class="fifa-alert"><i class="fas fa-check-circle"></i> {{ session('fifa_success') }}</div>
        @endif
        @if($errors->any())
            <div class="fifa-alert" style="background:rgba(200,60,60,.15); border-color:rgba(200,60,60,.4); color:#f1a5a5;">
                {{ $errors->first() }}
            </div>
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
<div class="modal fade" id="fifaRequestModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" action="{{ route('fifa.request') }}" method="POST">
      @csrf
      <input type="hidden" name="ticket_id" id="fr_ticket">
      <div class="modal-header">
        <h5 class="modal-title" style="font-weight:700;">Request Tickets</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="fifa-summary mb-3" id="fr_summary"></div>
        <div class="row g-3">
          <div class="col-md-6"><label>Full Name *</label><input name="name" class="form-control" required></div>
          <div class="col-md-6"><label>Email *</label><input type="email" name="email" class="form-control" required></div>
          <div class="col-md-6"><label>Phone</label><input name="phone" class="form-control" placeholder="+971 …"></div>
          <div class="col-md-6"><label>Country</label>
            <select name="country" class="form-select">
              <option value="">Select…</option>
              @foreach(config('countries') as $cName => $cInfo)
                <option value="{{ $cName }}">{{ $cName }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4"><label>Quantity *</label><input type="number" name="quantity" id="fr_qty" class="form-control" value="1" min="1" max="50" required></div>
          <div class="col-md-8"><label>Message</label><input name="message" class="form-control" placeholder="Any preferences…"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link text-secondary" data-bs-dismiss="modal" style="text-decoration:none;">Cancel</button>
        <button type="submit" class="fifa-btn-submit"><i class="fas fa-paper-plane"></i> Submit Request</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('fifaRequestModal');
    var modal = new bootstrap.Modal(modalEl);

    document.querySelectorAll('.js-fifa-req').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var d = this.dataset;
            document.getElementById('fr_ticket').value = d.ticket || '';
            var qty = document.getElementById('fr_qty');
            qty.max = d.max || 50;
            if (qty.value > qty.max) qty.value = qty.max;

            var summary;
            if (d.ticket) {
                var seat = d.seat ? ' · ' + d.seat : '';
                summary = '<b>' + d.match + '</b><br>' + d.cat + seat + ' — <b>$' + d.price + '</b> per ticket';
            } else {
                summary = '<b>General enquiry</b><br>Tell us which match and category you are after.';
            }
            document.getElementById('fr_summary').innerHTML = summary;
            modal.show();
        });
    });

    @if(session('fifa_success'))
        // scroll to confirmation
        document.querySelector('.fifa-alert')?.scrollIntoView({behavior:'smooth', block:'center'});
    @endif
});
</script>

@include('footer')
