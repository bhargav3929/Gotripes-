@include('header')

@php
    $tenant = current_company();
    $gallery = $package->galleryImages();
    $cover = $package->image ?: ($gallery[0] ?? null);
    $isPurchase = $package->package_type === 'purchase';
    // Per-person prices, falling back to the "from" price for adults.
    $pAdult  = $package->price_adult !== null ? (float) $package->price_adult : (float) $package->price;
    $pChild  = $package->price_child !== null ? (float) $package->price_child : 0;
    $pInfant = $package->price_infant !== null ? (float) $package->price_infant : 0;
    $waDigits = preg_replace('/\D/', '', (string) $package->partner_whatsapp);
@endphp

<main style="background:#000; min-height:100vh; color:#fff; font-family:'Outfit',sans-serif; padding-bottom:60px;">
    <div class="container" style="max-width:1140px; padding-top:32px;">

        <a href="{{ route('tour-packages') }}" style="color:#FFD23F; text-decoration:none; font-size:14px;">
            <i class="bi bi-arrow-left"></i> All destinations
        </a>

        <div class="row g-4 mt-1">
            {{-- LEFT: gallery + description --}}
            <div class="col-lg-7">
                <div class="tpd-main-img">
                    @if($cover)
                        <img id="tpdMainImg" src="{{ str_starts_with($cover,'http') ? $cover : asset($cover) }}" alt="{{ $package->title }}">
                    @else
                        <div class="tpd-placeholder"><i class="bi bi-image"></i></div>
                    @endif
                </div>
                @if(count($gallery) > 1)
                    <div class="tpd-thumbs">
                        @foreach($gallery as $g)
                            @php $src = str_starts_with($g,'http') ? $g : asset($g); @endphp
                            <img src="{{ $src }}" onclick="document.getElementById('tpdMainImg').src='{{ $src }}'" alt="">
                        @endforeach
                    </div>
                @endif

                <div class="tpd-desc">
                    <h2 class="tpd-section-title">About this package</h2>
                    <div class="tpd-desc-body">{!! $package->description !!}</div>
                </div>
            </div>

            {{-- RIGHT: booking box --}}
            <div class="col-lg-5">
                <div class="tpd-box">
                    <span class="tpd-country">{{ $package->country }}</span>
                    <h1 class="tpd-title">{{ $package->title }}</h1>
                    <p class="tpd-duration"><i class="bi bi-clock"></i> {{ $package->duration }}</p>

                    @if($isPurchase)
                        <div class="tpd-from">From <strong>AED {{ number_format($pAdult, 0) }}</strong> / adult</div>

                        {{-- Dynamic price calculator --}}
                        <div class="tpd-calc">
                            <div class="tpd-row">
                                <div><span class="tpd-row-label">Adults</span><span class="tpd-row-price">AED {{ number_format($pAdult,0) }} each</span></div>
                                <div class="tpd-step">
                                    <button type="button" onclick="tpdStep('adults',-1)">−</button>
                                    <input type="text" id="qty_adults" value="1" readonly>
                                    <button type="button" onclick="tpdStep('adults',1)">+</button>
                                </div>
                            </div>
                            <div class="tpd-row">
                                <div><span class="tpd-row-label">Children</span><span class="tpd-row-price">AED {{ number_format($pChild,0) }} each</span></div>
                                <div class="tpd-step">
                                    <button type="button" onclick="tpdStep('children',-1)">−</button>
                                    <input type="text" id="qty_children" value="0" readonly>
                                    <button type="button" onclick="tpdStep('children',1)">+</button>
                                </div>
                            </div>
                            <div class="tpd-row">
                                <div><span class="tpd-row-label">Infants</span><span class="tpd-row-price">AED {{ number_format($pInfant,0) }} each</span></div>
                                <div class="tpd-step">
                                    <button type="button" onclick="tpdStep('infants',-1)">−</button>
                                    <input type="text" id="qty_infants" value="0" readonly>
                                    <button type="button" onclick="tpdStep('infants',1)">+</button>
                                </div>
                            </div>
                            <div class="tpd-total">
                                <span>Total</span>
                                <span class="tpd-total-val">AED <span id="tpdTotal">{{ number_format($pAdult,0) }}</span></span>
                            </div>
                        </div>

                        {{-- Booking form (reuses the working Nomod payment endpoint) --}}
                        <form id="tpdBuyForm" class="tpd-form">
                            @csrf
                            <input class="tpd-input" type="text" id="b_name" placeholder="Full name" required>
                            <input class="tpd-input" type="email" id="b_email" placeholder="Email address" required>
                            <div class="tpd-phone">
                                <select id="b_cc">
                                    <option value="+971">🇦🇪 +971</option>
                                    <option value="+91">+91</option>
                                    <option value="+44">+44</option>
                                    <option value="+1">+1</option>
                                    <option value="+966">+966</option>
                                </select>
                                <input class="tpd-input" type="tel" id="b_phone" placeholder="50 123 4567" data-no-intl required>
                            </div>
                            <button type="submit" class="tpd-btn tpd-btn-pay" id="tpdPayBtn">
                                <i class="bi bi-credit-card"></i> Pay AED <span id="tpdPayAmt">{{ number_format($pAdult,0) }}</span>
                            </button>
                        </form>
                    @else
                        {{-- Enquire packages --}}
                        <div class="tpd-from">From <strong>AED {{ number_format((float) $package->price, 0) }}</strong></div>
                        <p class="tpd-enquire-note">This is a custom package — tell us your dates and group and we'll tailor it for you.</p>

                        @if($waDigits)
                            <a class="tpd-btn tpd-btn-wa" target="_blank"
                               href="https://wa.me/{{ $waDigits }}?text={{ urlencode('Hi, I am interested in the ' . $package->title . ' (' . $package->country . ') package.') }}">
                                <i class="bi bi-whatsapp"></i> Enquire on WhatsApp
                            </a>
                        @endif
                    @endif

                    {{-- On-site enquiry form (both purchase & enquire packages).
                         Submitting notifies the package's configured recipient emails. --}}
                    @if(session('package_enquiry_success'))
                        <div class="tpd-enq-ok">{{ session('package_enquiry_success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="tpd-enq-err">
                            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                        </div>
                    @endif

                    <form class="tpd-form tpd-enq-form" method="POST" action="{{ route('tour-packages.enquire', $package->id) }}">
                        @csrf
                        <div class="tpd-enq-heading">{{ $isPurchase ? 'Have a question? Send an enquiry' : 'Send an enquiry' }}</div>
                        <input class="tpd-input" type="text"  name="name"        value="{{ old('name') }}"        placeholder="Full name" required>
                        <input class="tpd-input" type="email" name="email"       value="{{ old('email') }}"       placeholder="Email address" required>
                        <input class="tpd-input" type="tel"   name="phone"       value="{{ old('phone') }}"       placeholder="Phone (with country code)" data-no-intl>
                        <input class="tpd-input" type="text"  name="travel_date" value="{{ old('travel_date') }}" placeholder="Preferred travel date (e.g. Aug 2026)">
                        <input class="tpd-input" type="number" name="travellers" value="{{ old('travellers') }}"  placeholder="No. of travellers" min="1" max="99">
                        <textarea class="tpd-input" name="message" rows="3" placeholder="Anything we should know?">{{ old('message') }}</textarea>
                        <button type="submit" class="tpd-btn tpd-btn-enq"><i class="bi bi-envelope"></i> Send Enquiry</button>
                    </form>

                    @if(!$isPurchase && $package->partner_email)
                        <p class="tpd-partner"><i class="bi bi-person-badge"></i> Direct: <a href="mailto:{{ $package->partner_email }}">{{ $package->partner_email }}</a></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    .tpd-main-img { aspect-ratio:16/10; background:#111; border-radius:16px; overflow:hidden; border:1px solid rgba(255,215,0,0.12); }
    .tpd-main-img img { width:100%; height:100%; object-fit:cover; display:block; }
    .tpd-placeholder { width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:rgba(255,215,0,0.25); font-size:56px; }
    .tpd-thumbs { display:flex; gap:8px; margin-top:10px; flex-wrap:wrap; }
    .tpd-thumbs img { width:72px; height:54px; object-fit:cover; border-radius:8px; cursor:pointer; border:1px solid rgba(255,215,0,0.2); transition:border-color .2s; }
    .tpd-thumbs img:hover { border-color:#FFD23F; }
    .tpd-desc { margin-top:28px; }
    .tpd-section-title { font-size:22px; font-weight:700; margin-bottom:14px; color:#fff; }
    .tpd-desc-body { color:#cfcfcf; line-height:1.7; font-size:15px; }
    .tpd-desc-body ul, .tpd-desc-body ol { padding-left:20px; }
    .tpd-desc-body a { color:#FFD23F; }

    .tpd-box { background:linear-gradient(180deg,#0e0e10 0%,#070708 100%); border:1px solid rgba(255,215,0,0.15); border-radius:16px; padding:26px; position:sticky; top:90px; }
    .tpd-country { color:#FFD23F; font-size:12px; text-transform:uppercase; letter-spacing:2px; font-weight:600; }
    .tpd-title { font-size:26px; font-weight:800; margin:6px 0 8px; line-height:1.2; }
    .tpd-duration { color:#999; font-size:14px; margin-bottom:14px; }
    .tpd-from { font-size:15px; color:#ddd; margin-bottom:16px; }
    .tpd-from strong { color:#FFD23F; font-size:20px; }

    .tpd-calc { border-top:1px solid rgba(255,255,255,0.08); padding-top:14px; }
    .tpd-row { display:flex; justify-content:space-between; align-items:center; padding:8px 0; }
    .tpd-row-label { display:block; font-size:15px; font-weight:600; }
    .tpd-row-price { display:block; font-size:12px; color:#888; }
    .tpd-step { display:flex; align-items:center; gap:8px; }
    .tpd-step button { width:30px; height:30px; border-radius:8px; border:1px solid rgba(255,215,0,0.3); background:transparent; color:#FFD23F; font-size:18px; cursor:pointer; line-height:1; }
    .tpd-step button:hover { background:rgba(255,215,0,0.1); }
    .tpd-step input { width:36px; text-align:center; background:transparent; border:none; color:#fff; font-size:16px; font-weight:600; }
    .tpd-total { display:flex; justify-content:space-between; align-items:center; border-top:1px dashed rgba(255,215,0,0.2); margin-top:10px; padding-top:12px; }
    .tpd-total-val { color:#FFD23F; font-weight:800; font-size:22px; }

    .tpd-form { margin-top:16px; display:flex; flex-direction:column; gap:10px; }
    .tpd-input { width:100%; height:46px; background:#111; border:1px solid #2a2a2a; border-radius:10px; padding:0 14px; color:#fff; font-size:14px; }
    .tpd-input:focus { outline:none; border-color:#FFD23F; }
    .tpd-phone { display:grid; grid-template-columns:96px 1fr; gap:8px; }
    .tpd-phone select { height:46px; background:#111; border:1px solid #2a2a2a; border-radius:10px; color:#fff; padding:0 8px; }

    .tpd-btn { display:flex; align-items:center; justify-content:center; gap:8px; width:100%; height:50px; border-radius:12px; font-weight:700; font-size:15px; text-decoration:none; border:none; cursor:pointer; margin-top:10px; transition:transform .15s; }
    .tpd-btn:hover { transform:translateY(-2px); }
    .tpd-btn-pay { background:linear-gradient(135deg,#FFD700,#D4AF37); color:#000; }
    .tpd-btn-wa { background:#25D366; color:#fff; }
    .tpd-btn-enq { background:transparent; color:#FFD23F; border:1px solid rgba(255,215,0,0.4); }
    .tpd-enquire-note { color:#aaa; font-size:14px; line-height:1.6; }
    .tpd-partner { margin-top:14px; font-size:13px; color:#999; }
    .tpd-partner a { color:#FFD23F; }
    .tpd-enq-form { margin-top:16px; padding-top:16px; border-top:1px solid rgba(255,215,0,0.12); }
    .tpd-enq-heading { font-size:14px; font-weight:600; color:#fff; margin-bottom:10px; }
    .tpd-enq-form textarea.tpd-input { resize:vertical; min-height:64px; }
    .tpd-enq-ok  { margin-top:14px; padding:11px 14px; border-radius:10px; font-size:13.5px;
                   background:rgba(34,197,94,.14); border:1px solid rgba(34,197,94,.4); color:#7ee2a8; }
    .tpd-enq-err { margin-top:14px; padding:11px 14px; border-radius:10px; font-size:13px;
                   background:rgba(214,54,56,.14); border:1px solid rgba(214,54,56,.4); color:#f3a3a4; }
</style>

<script>
    const TPD = {
        adults: { qty: 1, price: {{ $pAdult }} },
        children: { qty: 0, price: {{ $pChild }} },
        infants: { qty: 0, price: {{ $pInfant }} },
    };
    function tpdRecalc() {
        let total = 0;
        for (const k in TPD) total += TPD[k].qty * TPD[k].price;
        const t = Math.round(total);
        const el = document.getElementById('tpdTotal'); if (el) el.textContent = t.toLocaleString();
        const amt = document.getElementById('tpdPayAmt'); if (amt) amt.textContent = t.toLocaleString();
        return t;
    }
    function tpdStep(kind, delta) {
        const min = kind === 'adults' ? 1 : 0;
        TPD[kind].qty = Math.max(min, TPD[kind].qty + delta);
        document.getElementById('qty_' + kind).value = TPD[kind].qty;
        tpdRecalc();
    }

    const buyForm = document.getElementById('tpdBuyForm');
    if (buyForm) {
        buyForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = document.getElementById('tpdPayBtn');
            const total = tpdRecalc();
            if (total <= 0) { alert('Please select at least one traveller.'); return; }
            const orig = btn.innerHTML;
            btn.disabled = true; btn.innerHTML = 'Processing…';

            // Clean E.164 phone (same approach as the fixed Pay Online form)
            const ccDigits = (document.getElementById('b_cc').value || '').replace(/\D/g, '');
            let pnDigits = (document.getElementById('b_phone').value || '').replace(/\D/g, '');
            if (ccDigits && pnDigits.startsWith(ccDigits)) pnDigits = pnDigits.slice(ccDigits.length);
            pnDigits = pnDigits.replace(/^0+/, '');
            const phone = '+' + ccDigits + pnDigits;

            const csrf = buyForm.querySelector('input[name="_token"]').value;
            const pax = `${TPD.adults.qty} adult(s), ${TPD.children.qty} child(ren), ${TPD.infants.qty} infant(s)`;
            const fd = new FormData();
            fd.set('client_name', document.getElementById('b_name').value);
            fd.set('client_email', document.getElementById('b_email').value);
            fd.set('client_phone', phone);
            fd.set('service', 'Tour Package: {{ addslashes($package->title) }} ({{ addslashes($package->country) }}) — ' + pax);
            fd.set('amount', total);
            fd.set('agent_name', '{{ addslashes($tenant?->name ?? "Website") }}');

            fetch("{{ route('agent.pay') }}", {
                method: 'POST',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: fd
            })
            .then(r => r.ok ? r.json() : Promise.reject('Server error'))
            .then(d => {
                if (d.success && d.checkout_url) { window.location.href = d.checkout_url; }
                else { throw new Error(d.message || 'Payment failed.'); }
            })
            .catch(err => {
                alert((err && err.message) || 'Could not start payment. Please try again.');
                btn.disabled = false; btn.innerHTML = orig;
            });
        });
    }
    tpdRecalc();
</script>

@include('footer')
