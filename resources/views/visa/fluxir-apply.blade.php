@include('header')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    .evisa * { box-sizing: border-box; }
    .evisa {
        --c-gold: #FFD700;
        --c-gold-grad: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
        --c-card: #0b0b0b;
        --c-input: #111;
        --c-border: #222;
        --c-muted: #8a8a8a;
        --c-light: #eee;
        background: linear-gradient(180deg, #000 0%, #0a0a0a 100%);
        padding: 24px 24px 18px;
        font-family: 'Outfit', sans-serif;
        color: var(--c-light);
    }
    .evisa-wrap { max-width: 1140px; margin: 0 auto; }

    /* compact header */
    .evisa-head { display: flex; align-items: baseline; gap: 12px; flex-wrap: wrap; margin-bottom: 8px; }
    .evisa-head h1 {
        font-size: 1.4rem; font-weight: 800; letter-spacing: -0.01em; margin: 0;
        background: var(--c-gold-grad); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;
    }
    .evisa-head p { color: var(--c-muted); font-weight: 300; font-size: 0.88rem; margin: 0; }

    .evisa-alert { display: none; margin-bottom: 12px; padding: 10px 14px; border-radius: 9px; font-size: 0.85rem; border: 1px solid; }
    .evisa-alert.err { display: block; background: rgba(220,38,38,.1); border-color: rgba(220,38,38,.4); color: #fca5a5; }

    /* two columns: form | summary — fits one window, no scroll */
    .evisa-main { display: grid; grid-template-columns: 1fr 330px; gap: 18px; align-items: start; }
    .evisa-card { background: var(--c-card); border: 1px solid var(--c-border); border-radius: 14px; padding: 13px 16px; }

    .evisa-section-title { font-size: 0.7rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: var(--c-gold); margin: 0 0 8px; }
    .evisa-section-title.mt { margin-top: 12px; }

    .evisa-grid { display: grid; gap: 6px 12px; }
    .evisa-grid.g3 { grid-template-columns: repeat(3, 1fr); }
    .evisa-grid.g2 { grid-template-columns: 1fr 1fr; }

    .evisa-field label { display: block; font-size: 0.72rem; font-weight: 500; color: var(--c-muted); margin-bottom: 4px; }
    .evisa-field label .req { color: var(--c-gold); }
    .evisa-input, .evisa-select {
        width: 100%; background: var(--c-input); border: 1px solid var(--c-border);
        border-radius: 8px; padding: 7px 10px; color: var(--c-light); font-family: inherit;
        font-size: 0.85rem; transition: border-color .15s ease, box-shadow .15s ease;
    }
    .evisa-input:focus, .evisa-select:focus { outline: none; border-color: var(--c-gold); box-shadow: 0 0 0 3px rgba(255,215,0,.12); }
    .evisa-input::placeholder { color: #555; }

    .evisa-file {
        display: flex; align-items: center; gap: 9px;
        border: 1.5px dashed var(--c-border); border-radius: 8px; padding: 9px 11px;
        cursor: pointer; background: var(--c-input); transition: border-color .15s ease, background .15s ease;
    }
    .evisa-file:hover { border-color: var(--c-gold); background: #141414; }
    .evisa-file input { display: none; }
    .evisa-file i { color: var(--c-gold); font-size: 1.05rem; flex: none; }
    .evisa-file .lab { font-size: 0.8rem; color: var(--c-light); font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .evisa-file .hint { display: block; font-size: 0.66rem; color: var(--c-muted); font-weight: 400; }
    .evisa-file.has-file { border-color: var(--c-gold); border-style: solid; }

    /* summary card */
    .evisa-sum-card { display: flex; flex-direction: column; }
    .evisa-sum-list { list-style: none; margin: 0 0 12px; padding: 0; display: flex; flex-direction: column; gap: 8px; }
    .evisa-sum-list li { display: flex; align-items: center; gap: 9px; font-size: 0.82rem; color: #d6d6d6; }
    .evisa-sum-list li i { color: var(--c-gold); font-size: 0.95rem; width: 18px; text-align: center; flex: none; }
    .evisa-fee { display: flex; align-items: baseline; justify-content: space-between; padding: 12px 0; margin-top: auto; border-top: 1px solid var(--c-border); }
    .evisa-fee .lbl { color: var(--c-muted); font-size: 0.8rem; }
    .evisa-fee .amt { font-size: 1.7rem; font-weight: 800; color: var(--c-gold); }

    .evisa-submit {
        width: 100%; padding: 13px; border: none; border-radius: 10px;
        background: var(--c-gold-grad); color: #000; font-family: inherit; font-size: 0.95rem; font-weight: 700;
        letter-spacing: 0.02em; cursor: pointer; transition: transform .12s ease, opacity .15s ease;
    }
    .evisa-submit:hover { transform: translateY(-1px); }
    .evisa-submit:disabled { opacity: .6; cursor: not-allowed; transform: none; }
    .evisa-note { margin: 10px 0 0; font-size: 0.7rem; color: var(--c-muted); text-align: center; line-height: 1.4; }

    .spin { display: inline-block; width: 14px; height: 14px; border: 2px solid rgba(0,0,0,.3); border-top-color: #000; border-radius: 50%; animation: evspin .6s linear infinite; vertical-align: -2px; }
    @keyframes evspin { to { transform: rotate(360deg); } }

    @media (max-width: 880px) {
        .evisa-main { grid-template-columns: 1fr; }
        .evisa-grid.g3 { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 520px) {
        .evisa-grid.g3, .evisa-grid.g2 { grid-template-columns: 1fr; }
    }
</style>

<div class="evisa">
    <div class="evisa-wrap">
        <div class="evisa-head">
            <h1>United Arab Emirates&nbsp;eVisa</h1>
            <p>30-day tourist visa · single entry — complete everything below in one screen.</p>
        </div>

        <div id="evisaAlert" class="evisa-alert err"></div>

        <form id="evisaForm" class="evisa-main" enctype="multipart/form-data" novalidate>
            @csrf

            <!-- LEFT: all inputs -->
            <div class="evisa-card">
                <p class="evisa-section-title">Traveller details</p>
                <div class="evisa-grid g3">
                    <div class="evisa-field"><label>First name <span class="req">*</span></label><input class="evisa-input" type="text" name="first_name" required></div>
                    <div class="evisa-field"><label>Last name <span class="req">*</span></label><input class="evisa-input" type="text" name="last_name" required></div>
                    <div class="evisa-field"><label>Gender</label><select class="evisa-select" name="gender"><option value="">Select</option><option value="male">Male</option><option value="female">Female</option></select></div>
                    <div class="evisa-field"><label>Email <span class="req">*</span></label><input class="evisa-input" type="email" name="email" placeholder="name@example.com" required></div>
                    <div class="evisa-field"><label>Phone</label><input class="evisa-input" type="tel" name="phone" placeholder="+971 50 000 0000"></div>
                    <div class="evisa-field"><label>Nationality</label><input class="evisa-input" type="text" name="nationality" maxlength="3" placeholder="e.g. IND"></div>
                </div>

                <p class="evisa-section-title mt">Trip &amp; documents</p>
                <div class="evisa-grid g3">
                    <div class="evisa-field"><label>Arrival date <span class="req">*</span></label><input class="evisa-input" type="date" name="arrival_date" required></div>
                    <div class="evisa-field"><label>Departure date <span class="req">*</span></label><input class="evisa-input" type="date" name="departure_date" required></div>
                    <div class="evisa-field"><label>Departing from</label><input class="evisa-input" type="text" name="origination_code" maxlength="3" placeholder="e.g. IND"></div>
                </div>
                <input type="hidden" name="destination_code" value="ARE">

                <div class="evisa-grid g2" style="margin-top:10px;">
                    <label class="evisa-file">
                        <i class="bi bi-file-earmark-text"></i>
                        <span><span class="lab">Passport bio page *</span><span class="hint">PDF/JPG/PNG · 8MB</span></span>
                        <input type="file" name="passport_file" accept=".pdf,.jpg,.jpeg,.png" required>
                    </label>
                    <label class="evisa-file">
                        <i class="bi bi-image"></i>
                        <span><span class="lab">Personal photo *</span><span class="hint">White bg · JPG/PNG · 8MB</span></span>
                        <input type="file" name="personal_photo" accept="image/jpeg,image/png" required>
                    </label>
                </div>
            </div>

            <!-- RIGHT: summary + pay -->
            <aside class="evisa-card evisa-sum-card">
                <p class="evisa-section-title">Your visa</p>
                <ul class="evisa-sum-list">
                    <li><i class="bi bi-calendar-check"></i> 30 days · single entry</li>
                    <li><i class="bi bi-clock-history"></i> ~72h processing</li>
                    <li><i class="bi bi-shield-check"></i> Government-approved e-Visa</li>
                    <li><i class="bi bi-envelope-check"></i> Updates by email</li>
                </ul>
                <div class="evisa-fee">
                    <span class="lbl">Total</span>
                    <span class="amt">${{ $fee ?? 96 }}</span>
                </div>
                <button type="submit" class="evisa-submit" id="evisaSubmit">Continue to secure payment</button>
                <p class="evisa-note">Secure payment. You'll get email updates on your application status.</p>
            </aside>
        </form>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
(function () {
    const form = document.getElementById('evisaForm');
    const btn  = document.getElementById('evisaSubmit');
    const alertBox = document.getElementById('evisaAlert');
    const stripePk = @json(config('fluxir.stripe_publishable_key'));

    document.querySelectorAll('.evisa-file input').forEach(function (inp) {
        inp.addEventListener('change', function () {
            const wrap = inp.closest('.evisa-file');
            const lab = wrap.querySelector('.lab');
            if (inp.files.length) { wrap.classList.add('has-file'); lab.textContent = inp.files[0].name; }
            else { wrap.classList.remove('has-file'); }
        });
    });

    function showError(msg) {
        alertBox.textContent = msg;
        alertBox.classList.add('err');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        alertBox.textContent = '';
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spin"></span> Submitting…';

        fetch("{{ route('visa.fluxir.apply') }}", {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            }
        })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
        .then(function (res) {
            const d = res.d;
            if (!res.ok || !d.success) { throw new Error(d.message || 'We could not submit your application. Please try again.'); }
            if (!d.checkout_session_id) { throw new Error('Application submitted, but payment could not be started. Please contact support.'); }
            if (!stripePk) { throw new Error('Payment is not configured yet. Please contact support.'); }
            const stripe = Stripe(stripePk);
            return stripe.redirectToCheckout({ sessionId: d.checkout_session_id }).then(function (result) {
                if (result && result.error) { throw new Error(result.error.message); }
            });
        })
        .catch(function (err) {
            showError(err.message || 'Something went wrong.');
            btn.disabled = false;
            btn.innerHTML = original;
        });
    });
})();
</script>

@include('footer')
