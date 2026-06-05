@include('header')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    .evisa * { box-sizing: border-box; }
    .evisa {
        --c-gold: #FFD700;
        --c-gold-grad: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
        --c-bg: #050505;
        --c-card: #0b0b0b;
        --c-input: #111;
        --c-border: #222;
        --c-muted: #888;
        --c-light: #eee;
        background: linear-gradient(180deg, #000 0%, #0a0a0a 100%);
        min-height: 100vh;
        padding: 160px 20px 80px;
        font-family: 'Outfit', sans-serif;
        color: var(--c-light);
    }
    .evisa-wrap { max-width: 880px; margin: 0 auto; }
    .evisa-head { margin-bottom: 40px; }
    .evisa-head h1 {
        font-size: clamp(2.2rem, 5vw, 3.4rem); font-weight: 800; line-height: 1.05;
        letter-spacing: -0.02em; margin: 0 0 12px;
        background: var(--c-gold-grad); -webkit-background-clip: text; background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .evisa-head p { color: var(--c-muted); font-weight: 300; font-size: 1.05rem; max-width: 560px; margin: 0; }

    .evisa-card {
        background: var(--c-card); border: 1px solid var(--c-border);
        border-radius: 16px; padding: 36px;
    }
    .evisa-section-title {
        font-size: 0.78rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase;
        color: var(--c-gold); margin: 0 0 20px; padding-bottom: 12px; border-bottom: 1px solid var(--c-border);
    }
    .evisa-section + .evisa-section { margin-top: 40px; }

    .evisa-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 640px) { .evisa-grid { grid-template-columns: 1fr; } }
    .evisa-field.full { grid-column: 1 / -1; }
    .evisa-field label {
        display: block; font-size: 0.82rem; font-weight: 500; color: var(--c-muted); margin-bottom: 8px;
    }
    .evisa-field label .req { color: var(--c-gold); }
    .evisa-input, .evisa-select {
        width: 100%; background: var(--c-input); border: 1px solid var(--c-border);
        border-radius: 10px; padding: 13px 14px; color: var(--c-light); font-family: inherit;
        font-size: 0.95rem; transition: border-color .15s ease, box-shadow .15s ease;
    }
    .evisa-input:focus, .evisa-select:focus {
        outline: none; border-color: var(--c-gold); box-shadow: 0 0 0 3px rgba(255,215,0,.12);
    }
    .evisa-input::placeholder { color: #555; }

    .evisa-file {
        border: 1.5px dashed var(--c-border); border-radius: 10px; padding: 18px 14px;
        text-align: center; cursor: pointer; transition: border-color .15s ease, background .15s ease;
        background: var(--c-input);
    }
    .evisa-file:hover { border-color: var(--c-gold); background: #141414; }
    .evisa-file input { display: none; }
    .evisa-file .ico { font-size: 1.4rem; }
    .evisa-file .lab { display: block; font-size: 0.85rem; color: var(--c-light); margin-top: 6px; font-weight: 500; }
    .evisa-file .hint { display: block; font-size: 0.72rem; color: var(--c-muted); margin-top: 2px; }
    .evisa-file.has-file { border-color: var(--c-gold); border-style: solid; }

    .evisa-summary {
        display: flex; align-items: baseline; justify-content: space-between;
        margin-top: 36px; padding: 20px 24px; background: #0e0e0e; border: 1px solid var(--c-border); border-radius: 12px;
    }
    .evisa-summary .lbl { color: var(--c-muted); font-size: 0.9rem; }
    .evisa-summary .amt { font-size: 1.6rem; font-weight: 800; color: var(--c-gold); }

    .evisa-submit {
        width: 100%; margin-top: 24px; padding: 16px; border: none; border-radius: 12px;
        background: var(--c-gold-grad); color: #000; font-family: inherit; font-size: 1.02rem; font-weight: 700;
        letter-spacing: 0.02em; cursor: pointer; transition: transform .12s ease, opacity .15s ease;
    }
    .evisa-submit:hover { transform: translateY(-1px); }
    .evisa-submit:disabled { opacity: .6; cursor: not-allowed; transform: none; }

    .evisa-note { margin-top: 16px; font-size: 0.8rem; color: var(--c-muted); text-align: center; }
    .evisa-alert {
        display: none; margin-bottom: 24px; padding: 14px 16px; border-radius: 10px;
        font-size: 0.9rem; border: 1px solid;
    }
    .evisa-alert.err { display: block; background: rgba(220,38,38,.1); border-color: rgba(220,38,38,.4); color: #fca5a5; }
    .spin { display: inline-block; width: 15px; height: 15px; border: 2px solid rgba(0,0,0,.3); border-top-color: #000; border-radius: 50%; animation: evspin .6s linear infinite; vertical-align: -2px; }
    @keyframes evspin { to { transform: rotate(360deg); } }
</style>

<div class="evisa">
    <div class="evisa-wrap">
        <div class="evisa-head">
            <h1>United Arab Emirates&nbsp;eVisa</h1>
            <p>Apply for your 30-day UAE tourist visa. Complete the form, upload your passport and photo, and pay securely to submit your application.</p>
        </div>

        <div id="evisaAlert" class="evisa-alert err"></div>

        <form id="evisaForm" class="evisa-card" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="evisa-section">
                <p class="evisa-section-title">Traveller details</p>
                <div class="evisa-grid">
                    <div class="evisa-field">
                        <label>First name <span class="req">*</span></label>
                        <input class="evisa-input" type="text" name="first_name" required>
                    </div>
                    <div class="evisa-field">
                        <label>Last name <span class="req">*</span></label>
                        <input class="evisa-input" type="text" name="last_name" required>
                    </div>
                    <div class="evisa-field">
                        <label>Email <span class="req">*</span></label>
                        <input class="evisa-input" type="email" name="email" placeholder="name@example.com" required>
                    </div>
                    <div class="evisa-field">
                        <label>Phone</label>
                        <input class="evisa-input" type="tel" name="phone" placeholder="+971 50 000 0000">
                    </div>
                    <div class="evisa-field">
                        <label>Nationality</label>
                        <input class="evisa-input" type="text" name="nationality" maxlength="3" placeholder="e.g. IND" value="">
                    </div>
                    <div class="evisa-field">
                        <label>Gender</label>
                        <select class="evisa-select" name="gender">
                            <option value="">Select</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="evisa-section">
                <p class="evisa-section-title">Trip dates</p>
                <div class="evisa-grid">
                    <div class="evisa-field">
                        <label>Arrival date <span class="req">*</span></label>
                        <input class="evisa-input" type="date" name="arrival_date" required>
                    </div>
                    <div class="evisa-field">
                        <label>Departure date <span class="req">*</span></label>
                        <input class="evisa-input" type="date" name="departure_date" required>
                    </div>
                    <input type="hidden" name="destination_code" value="ARE">
                    <div class="evisa-field">
                        <label>Departing from (country)</label>
                        <input class="evisa-input" type="text" name="origination_code" maxlength="3" placeholder="e.g. IND">
                    </div>
                </div>
            </div>

            <div class="evisa-section">
                <p class="evisa-section-title">Required documents</p>
                <div class="evisa-grid">
                    <div class="evisa-field">
                        <label>Passport bio page <span class="req">*</span></label>
                        <label class="evisa-file" id="dropPassport">
                            <span class="ico">📄</span>
                            <span class="lab">Upload passport</span>
                            <span class="hint">PDF, JPG or PNG · max 8MB</span>
                            <input type="file" name="passport_file" accept=".pdf,.jpg,.jpeg,.png" required>
                        </label>
                    </div>
                    <div class="evisa-field">
                        <label>Personal photo <span class="req">*</span></label>
                        <label class="evisa-file" id="dropPhoto">
                            <span class="ico">🖼️</span>
                            <span class="lab">Upload photo</span>
                            <span class="hint">White background · JPG or PNG · max 8MB</span>
                            <input type="file" name="personal_photo" accept="image/jpeg,image/png" required>
                        </label>
                    </div>
                </div>
            </div>

            <div class="evisa-summary">
                <span class="lbl">UAE eVisa — 30 days, single entry</span>
                <span class="amt">${{ $fee ?? 96 }}</span>
            </div>

            <button type="submit" class="evisa-submit" id="evisaSubmit">Continue to secure payment</button>
            <p class="evisa-note">Payment is processed securely. You'll receive email updates on your application status.</p>
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

    // File picker labels
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
        alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        alertBox.textContent = '';
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spin"></span> Submitting application…';

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
            if (!res.ok || !d.success) {
                throw new Error(d.message || 'We could not submit your application. Please try again.');
            }
            if (!d.checkout_session_id) {
                throw new Error('Application submitted, but payment could not be started. Please contact support.');
            }
            if (!stripePk) {
                throw new Error('Payment is not configured yet (missing Stripe key). Please contact support.');
            }
            // Redirect to Fluxir-hosted Stripe Checkout using the returned session id.
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
