{{--
  Circular freelancer registration pop-up — same "black disc, brushed gold
  ring" language as the Visa Route selector (partials/emirate_selector_modal)
  and the homepage "Create Partner Account" pop-up (banner.blade.php), which
  is where this shell's CSS/JS pattern is copied from. Amer asked for this
  shape specifically on /freelancer/register, 27 Sep 2026 (docs/client-
  feedback-2026-09-27.md) — "borders are beautiful but the shape isnt
  appealing... want something similar" to the circle.

  Two steps (Personal Info, Bank Details) match his mock-up exactly, plus a
  third Agreement step that only appears when a contract is published —
  same "contract first, access after" rule as /agent/register.

  Expects $contract (ContractDocument|null) and $countries (array-like) from
  FreelancerSignupController::landing().
--}}
@include('partials.gold-medallion-tokens')
@include('partials.gold-medallion-fit')
@php
    $flModalLogo = (isset($company) && $company && $company->logo)
        ? asset('storage/' . $company->logo)
        : asset('assets/index_files/logo.png');
@endphp
<style>
.fl-registration-modal{display:none;position:fixed;inset:0;z-index:10000;background:rgba(0,0,0,.9);backdrop-filter:blur(10px);padding:16px;overflow:hidden;overflow:clip;align-items:center;justify-content:center;animation:flModalFadeIn .3s ease-in-out}
.fl-registration-modal.is-open{display:flex}
body>.iti--container{z-index:10001}
.fl-registration-modal .fl-modal-content{--disc:max(680px,min(920px,96vw,92vh));scale:var(--gm-fit,1);--ring-w:calc(var(--disc) * var(--gm-ring-ratio,.045));position:relative;width:var(--disc);height:var(--disc);border-radius:50%;border:var(--ring-w) solid transparent;background:var(--gm-disc-fill) padding-box,var(--gm-gold-metal) border-box;box-shadow:0 30px 90px rgba(0,0,0,.85),0 0 60px rgba(212,175,55,.18),inset 0 0 0 1px rgba(60,42,8,.9);display:flex;flex-direction:column;align-items:center;justify-content:center;overflow:visible;animation:flModalSlideIn .4s cubic-bezier(.16,1,.3,1);font-family:'Outfit','Plus Jakarta Sans',sans-serif;color:#fff}
.fl-registration-modal .fl-modal-content::before{content:'';position:absolute;inset:calc(var(--disc) * .012);border:2px solid rgba(212,175,55,.6);border-radius:50%;pointer-events:none}
.fl-registration-modal .fl-brand-medallion{flex:0 0 auto;width:calc(var(--disc) * .12);height:calc(var(--disc) * .12);border-radius:50%;overflow:hidden;background:#050505;box-shadow:0 10px 26px rgba(0,0,0,.6);margin-bottom:calc(var(--disc) * .012)}
.fl-registration-modal .fl-brand-medallion img{display:block;width:100%;height:100%;object-fit:cover}
.fl-registration-modal .fl-modal-header{flex:0 0 auto;margin-bottom:calc(var(--disc) * .012);text-align:center}
.fl-registration-modal .fl-modal-header h2{margin:0;font-size:calc(var(--disc) * .026);font-weight:800;letter-spacing:.04em;color:var(--gm-gold);text-transform:uppercase;white-space:nowrap}
.fl-registration-modal .fl-modal-close{position:absolute;z-index:3;top:calc(var(--disc) * .117 - 20px);right:calc(var(--disc) * .117 - 20px);width:40px;height:40px;background:#050505;border:2px solid var(--gm-gold);color:var(--gm-gold);font-size:26px;font-weight:300;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.6);transition:all .2s ease}
.fl-registration-modal .fl-modal-close:hover{background:var(--gm-gold);color:#000;transform:rotate(90deg)}
.fl-registration-modal .fl-modal-body{flex:0 0 auto;width:460px;height:470px;padding:4px 0 6px;box-sizing:border-box;display:flex;flex-direction:column;overflow:hidden}
.fl-registration-modal .fl-modal-body form{display:flex;flex-direction:column;flex:1 1 auto;min-height:0}
.fl-registration-modal .fl-step-indicator,.fl-registration-modal .fl-form-actions{flex:0 0 auto}
.fl-registration-modal .fl-step-indicator{display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:14px}
.fl-registration-modal .fl-step-dot{width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#8a8a8a;border:1px solid #333;background:#111;flex:none}
.fl-registration-modal .fl-step-dot.is-active{background:var(--gm-gold-metal);border-color:transparent;color:#000}
.fl-registration-modal .fl-step-dot.is-done{border-color:var(--gm-gold);color:var(--gm-gold)}
.fl-registration-modal .fl-step-line{width:24px;height:1px;background:#333}
.fl-registration-modal .fl-columns{display:flex;flex-direction:column;justify-content:safe center;flex:1 1 auto;min-height:0;overflow-y:auto;padding-right:6px;scrollbar-width:thin;scrollbar-color:var(--gm-gold) rgba(255,255,255,.06)}
.fl-registration-modal .fl-columns::-webkit-scrollbar{width:8px}
.fl-registration-modal .fl-columns::-webkit-scrollbar-thumb{background-color:var(--gm-gold);border-radius:8px}
.fl-registration-modal .fl-step{display:none}
.fl-registration-modal .fl-step.is-active{display:block}
.fl-registration-modal .fl-step>*+*{margin-top:12px}
.fl-registration-modal .fl-step-title{font-size:13px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--gm-gold);text-align:center;margin-bottom:2px}
.fl-registration-modal .fl-step-subtitle{font-size:11px;color:#999;text-align:center;margin:0 0 4px;line-height:1.4}
.fl-registration-modal .fl-field-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.fl-registration-modal .fl-form-group{display:flex;flex-direction:column}
.fl-registration-modal .fl-form-group label{font-size:11px;color:#aaa;margin-bottom:6px;letter-spacing:.02em}
.fl-registration-modal .fl-form-group input,.fl-registration-modal .fl-form-group select{background:#111;border:1px solid #333;border-radius:8px;color:#fff;padding:10px 12px;font-size:13px;font-family:inherit;width:100%}
.fl-registration-modal .fl-form-group input:focus,.fl-registration-modal .fl-form-group select:focus{outline:none;border-color:var(--gm-gold)}
.fl-registration-modal .fl-error-msg{display:block;min-height:0;margin-top:4px;font-size:11px;color:#ff6b6b}
.fl-registration-modal .fl-error-msg:empty{display:none}
.fl-registration-modal .fl-agreement-open{display:inline-block;color:var(--gm-gold);font-size:12px;text-decoration:underline;margin-bottom:4px}
.fl-registration-modal .fl-agreement-text{max-height:110px;overflow-y:auto;font-size:11px;color:#ccc;background:#0c0c0c;border:1px solid #2a2a2a;border-radius:8px;padding:10px;white-space:pre-wrap}
.fl-registration-modal .fl-agreement-accept{display:flex;align-items:flex-start;gap:8px;font-size:11px;color:#ccc;margin-top:8px}
.fl-registration-modal .fl-agreement-accept input{margin-top:2px}
.fl-registration-modal .fl-agreement-version{display:block;font-size:10px;color:#888;font-weight:400;text-transform:none;letter-spacing:0;margin-top:2px}
.fl-registration-modal .fl-form-actions{display:flex;flex-direction:column;align-items:center;gap:10px;margin-top:14px}
.fl-registration-modal .fl-actions-main{display:flex;justify-content:center;align-items:center;gap:12px}
.fl-registration-modal .fl-submit-btn,.fl-registration-modal .fl-next-btn{flex:none;width:calc(var(--disc) * .30);min-width:200px;height:46px;border:none;border-radius:99px;background:var(--gm-gold-metal);color:#000;font-weight:700;font-size:12px;letter-spacing:1.2px;text-transform:uppercase;cursor:pointer;white-space:nowrap;padding:0 14px;transition:transform .15s ease}
.fl-registration-modal .fl-submit-btn:hover,.fl-registration-modal .fl-next-btn:hover{transform:translateY(-1px)}
.fl-registration-modal .fl-submit-btn:disabled{opacity:.6;cursor:default;transform:none}
.fl-registration-modal .fl-back-btn{flex:none;width:110px;height:46px;border-radius:99px;background:transparent;border:1px solid #444;color:#ccc;font-size:11px;letter-spacing:1px;text-transform:uppercase;cursor:pointer}
.fl-registration-modal .fl-cancel-btn{background:none;border:1px solid #2e2e2e;color:#8a8a8a;border-radius:99px;min-width:112px;height:32px;padding:0 20px;font-size:10px;letter-spacing:1.6px;text-transform:uppercase;cursor:pointer;transition:all .2s ease}
.fl-registration-modal .fl-cancel-btn:hover{color:#fff;border-color:#555}
@keyframes flModalFadeIn{from{opacity:0}to{opacity:1}}
@keyframes flModalSlideIn{from{opacity:0;transform:scale(.92)}to{opacity:1;transform:scale(1)}}
/* Circle holds down to a 536px-tall window (the disc is scaled to fit by
   partials/gold-medallion-fit); below that, or on phones and tablets, a
   circle cannot hold the form, so it becomes a rounded card with the same ring. */
@media (max-width:899px),(max-height:535px){
  .fl-registration-modal{padding:12px;overflow-y:auto;align-items:flex-start}
  .fl-registration-modal .fl-modal-content{--disc:560px;scale:none;width:min(96vw,560px);height:auto;margin:12px auto;border-radius:32px;border-width:10px;padding:20px 0;justify-content:flex-start;overflow:visible}
  .fl-registration-modal .fl-modal-content::before{inset:8px;border-radius:22px}
  .fl-registration-modal .fl-brand-medallion{width:72px;height:72px;margin-bottom:8px}
  .fl-registration-modal .fl-modal-header h2{font-size:15px;white-space:normal;padding:0 56px}
  .fl-registration-modal .fl-modal-close{top:12px;right:12px;width:34px;height:34px;font-size:22px}
  .fl-registration-modal .fl-modal-body{width:100%;height:auto;padding:6px 20px 8px;overflow:visible}
  .fl-registration-modal .fl-columns{overflow:visible;padding-right:0}
  .fl-registration-modal .fl-submit-btn,.fl-registration-modal .fl-next-btn{width:min(100%,280px);min-width:0}
}
@media (max-width:420px){
  .fl-registration-modal .fl-field-row{grid-template-columns:1fr}
  .fl-registration-modal .fl-actions-main{width:100%}
  .fl-registration-modal .fl-back-btn{width:96px}
  .fl-registration-modal .fl-submit-btn,.fl-registration-modal .fl-next-btn{flex:1 1 auto;width:auto}
}
@media (prefers-reduced-motion:reduce){.fl-registration-modal,.fl-registration-modal .fl-modal-content{animation:none}}
</style>

<div id="flRegistrationModal" class="fl-registration-modal">
  <div class="fl-modal-content" data-gm-fit="680">
    <div class="fl-brand-medallion"><img src="{{ $flModalLogo }}" alt=""></div>
    <div class="fl-modal-header">
      <h2>Become a Freelancer</h2>
    </div>
    <span class="fl-modal-close" id="flCloseModal" role="button" aria-label="Close">&times;</span>

    <div class="fl-modal-body">
      <form id="flRegistrationForm">
        @csrf

        <div class="fl-step-indicator">
          <div class="fl-step-dot is-active" data-step-dot="1"><span>1</span></div>
          <div class="fl-step-line"></div>
          <div class="fl-step-dot" data-step-dot="2"><span>2</span></div>
          @if($contract)
            <div class="fl-step-line"></div>
            <div class="fl-step-dot" data-step-dot="3"><span>3</span></div>
          @endif
        </div>

        <div class="fl-columns">

          <!-- Step 1: Personal Info -->
          <div class="fl-step is-active" data-step="1">
            <div class="fl-step-title">Personal Info</div>
            <p class="fl-step-subtitle">Set up your travel-selling storefront in under 2 minutes.</p>

            <div class="fl-field-row">
              <div class="fl-form-group">
                <label for="flName">Full Name</label>
                <input type="text" id="flName" name="name" placeholder="Jane Smith" required>
                <span class="fl-error-msg" id="flName-error"></span>
              </div>
              <div class="fl-form-group">
                <label for="flPhone">Phone</label>
                <input type="tel" id="flPhone" name="phone" placeholder="50 123 4567" data-no-intl required>
                <span class="fl-error-msg" id="flPhone-error"></span>
              </div>
            </div>

            <div class="fl-form-group">
              <label for="flCountry">Country</label>
              <select id="flCountry" name="country" required>
                <option value="">Select country...</option>
                @foreach($countries as $c)
                  <option value="{{ $c }}">{{ $c }}</option>
                @endforeach
              </select>
              <span class="fl-error-msg" id="flCountry-error"></span>
            </div>

            <div class="fl-form-group">
              <label for="flEmail">Email</label>
              <input type="email" id="flEmail" name="email" placeholder="jane@example.com" required>
              <span class="fl-error-msg" id="flEmail-error"></span>
            </div>
          </div>

          <!-- Step 2: Bank Details (for payouts) -->
          <div class="fl-step" data-step="2">
            <div class="fl-step-title">Bank Details (for payouts)</div>

            <div class="fl-field-row">
              <div class="fl-form-group">
                <label for="flBankName">Bank Name</label>
                <input type="text" id="flBankName" name="bank_name" placeholder="e.g. Emirates NBD" required>
                <span class="fl-error-msg" id="flBankName-error"></span>
              </div>
              <div class="fl-form-group">
                <label for="flAccountHolder">Account Holder</label>
                <input type="text" id="flAccountHolder" name="account_holder_name" placeholder="As on bank record" required>
                <span class="fl-error-msg" id="flAccountHolder-error"></span>
              </div>
            </div>

            <div class="fl-form-group">
              <label for="flAccountNumber">Account Number</label>
              <input type="text" id="flAccountNumber" name="account_number" placeholder="Account / IBAN number" required>
              <span class="fl-error-msg" id="flAccountNumber-error"></span>
            </div>

            <div class="fl-field-row">
              <div class="fl-form-group">
                <label for="flIban">IBAN (optional)</label>
                <input type="text" id="flIban" name="iban" placeholder="AE07 0331 ...">
                <span class="fl-error-msg" id="flIban-error"></span>
              </div>
              <div class="fl-form-group">
                <label for="flSwift">SWIFT/BIC (optional)</label>
                <input type="text" id="flSwift" name="swift_code" placeholder="EBILAEAD">
                <span class="fl-error-msg" id="flSwift-error"></span>
              </div>
            </div>

            <div class="fl-field-row">
              <div class="fl-form-group">
                <label for="flPassword">Password</label>
                <input type="password" id="flPassword" name="password" placeholder="••••••••" minlength="8" required>
                <span class="fl-error-msg" id="flPassword-error"></span>
              </div>
              <div class="fl-form-group">
                <label for="flPasswordConfirm">Confirm Password</label>
                <input type="password" id="flPasswordConfirm" name="password_confirmation" placeholder="Repeat password" minlength="8" required>
                <span class="fl-error-msg" id="flPasswordConfirm-error"></span>
              </div>
            </div>
          </div>

          @if($contract)
            <!-- Step 3: Agreement — contract first, access after -->
            <div class="fl-step" data-step="3">
              <div class="fl-step-title">
                Agreement
                <span class="fl-agreement-version">{{ $contract->title }} · v{{ $contract->version }}</span>
              </div>

              @if($contract->isUpload())
                <a class="fl-agreement-open" href="{{ Storage::url($contract->pdf_path) }}" target="_blank" rel="noopener">
                  Read the agreement ({{ $contract->file_name }})
                </a>
              @else
                <div class="fl-agreement-text" tabindex="0">{{ $contract->body }}</div>
              @endif

              <div class="fl-form-group">
                <label for="flSignatureName">Type your full name to sign</label>
                <input type="text" id="flSignatureName" name="signature_full_name" placeholder="Your full legal name" maxlength="255" required>
                <span class="fl-error-msg" id="flSignatureName-error"></span>
              </div>

              <div class="fl-agreement-accept">
                <input type="checkbox" id="flSignatureAgreed" name="signature_agreed" value="1" required>
                <label for="flSignatureAgreed">
                  I have read the agreement and accept it. My name, the date and my IP address are recorded with this signature.
                </label>
              </div>
              <span class="fl-error-msg" id="flSignatureAgreed-error"></span>
            </div>
          @endif

        </div>

        <div class="fl-form-actions">
          <div class="fl-actions-main">
            <button type="button" class="fl-back-btn" id="flBackBtn" style="display:none;">Back</button>
            <button type="button" class="fl-next-btn" id="flNextBtn">Next</button>
            <button type="submit" class="fl-submit-btn" id="flSubmitBtn" style="display:none;">Create my storefront</button>
          </div>
          <button type="button" class="fl-cancel-btn" id="flCancelBtn">Cancel</button>
        </div>

      </form>
    </div>
  </div>
</div>

<script>
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('flRegistrationModal');
    const closeBtn = document.getElementById('flCloseModal');
    const cancelBtn = document.getElementById('flCancelBtn');
    const form = document.getElementById('flRegistrationForm');
    if (!modal || !closeBtn || !cancelBtn || !form) return;

    // Built explicitly (data-no-intl opts the field out of the global
    // auto-init) with dropdownContainer: document.body so the country-list
    // dropdown escapes this modal's scrollable body — same reason
    // banner.blade.php's partner phone field does the same thing.
    (function initFlPhone() {
      const phoneInput = document.getElementById('flPhone');
      if (!phoneInput || phoneInput.__iti || typeof window.intlTelInput !== 'function') return;
      const iti = window.intlTelInput(phoneInput, {
        initialCountry: 'ae',
        preferredCountries: ['ae', 'sa', 'in', 'pk', 'gb', 'us'],
        separateDialCode: true,
        dropdownContainer: document.body,
        utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.3/build/js/utils.js',
      });
      phoneInput.__iti = iti;
      if (typeof window.GTDetectCountry === 'function') {
        window.GTDetectCountry().then(function (code) {
          if (!code) return;
          const current = iti.getSelectedCountryData();
          if (!current || current.iso2 === 'ae') iti.setCountry(code);
        });
      }
      // Choosing a Country also sets the dial code, so a Tanzanian applicant
      // is never left on +971 if the IP guess misses.
      if (typeof window.GTLinkCountrySelect === 'function') {
        window.GTLinkCountrySelect(document.getElementById('flCountry'), iti);
      }
    })();

    function openModal() {
      modal.classList.add('is-open');
      document.body.style.overflow = 'hidden';
      goToStep(1);
    }
    function closeModal() {
      modal.classList.remove('is-open');
      document.body.style.overflow = 'auto';
      clearErrors();
      form.reset();
      goToStep(1);
    }
    window.openFreelancerRegistrationModal = openModal;

    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    window.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal(); });

    // Any element with this attribute opens the pop-up instead of following
    // its href (the href stays as a working fallback — see freelancer.landing
    // and header.blade.php's "Register Now" link).
    document.querySelectorAll('[data-open-freelancer-register]').forEach(function (el) {
      el.addEventListener('click', function (e) { e.preventDefault(); openModal(); });
    });

    const steps = Array.from(form.querySelectorAll('.fl-step'));
    const stepDots = Array.from(modal.querySelectorAll('.fl-step-dot'));
    const backBtn = document.getElementById('flBackBtn');
    const nextBtn = document.getElementById('flNextBtn');
    const submitBtn = document.getElementById('flSubmitBtn');
    let currentStep = 1;
    const totalSteps = steps.length;

    function stepIsValid(stepEl) {
      const fields = stepEl.querySelectorAll('input, select, textarea');
      let valid = true;
      fields.forEach(function (field) {
        if (field.offsetParent === null) return;
        if (!field.checkValidity()) { valid = false; field.reportValidity(); }
      });
      return valid;
    }

    function goToStep(step) {
      currentStep = step;
      steps.forEach(function (el) { el.classList.toggle('is-active', Number(el.dataset.step) === step); });
      stepDots.forEach(function (dot) {
        const dotStep = Number(dot.dataset.stepDot);
        dot.classList.toggle('is-active', dotStep === step);
        dot.classList.toggle('is-done', dotStep < step);
      });
      backBtn.style.display = step > 1 ? '' : 'none';
      nextBtn.style.display = step < totalSteps ? '' : 'none';
      submitBtn.style.display = step === totalSteps ? '' : 'none';
      const columns = modal.querySelector('.fl-columns');
      if (columns) columns.scrollTop = 0;
    }

    nextBtn.addEventListener('click', function () {
      const stepEl = steps.find(function (el) { return Number(el.dataset.step) === currentStep; });
      if (stepEl && !stepIsValid(stepEl)) return;
      if (currentStep < totalSteps) goToStep(currentStep + 1);
    });
    backBtn.addEventListener('click', function () { if (currentStep > 1) goToStep(currentStep - 1); });

    function clearErrors() {
      modal.querySelectorAll('.fl-error-msg').forEach(function (el) { el.textContent = ''; });
    }

    function displayErrors(errors) {
      clearErrors();
      const fieldMapping = {
        name: 'flName', phone: 'flPhone', country: 'flCountry', email: 'flEmail',
        bank_name: 'flBankName', account_holder_name: 'flAccountHolder',
        account_number: 'flAccountNumber', iban: 'flIban', swift_code: 'flSwift',
        password: 'flPassword', password_confirmation: 'flPasswordConfirm',
        signature_full_name: 'flSignatureName', signature_agreed: 'flSignatureAgreed',
      };
      let firstErrorStep = null;
      Object.keys(errors).forEach(function (field) {
        const mapped = fieldMapping[field] || field;
        const errorEl = document.getElementById(mapped + '-error');
        if (errorEl && errors[field][0]) {
          errorEl.textContent = errors[field][0];
          const stepEl = errorEl.closest('.fl-step');
          const stepNum = stepEl ? Number(stepEl.dataset.step) : null;
          if (stepNum && (firstErrorStep === null || stepNum < firstErrorStep)) firstErrorStep = stepNum;
        }
      });
      if (firstErrorStep !== null) goToStep(firstErrorStep);
    }

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      const formData = new FormData(form);
      submitBtn.disabled = true;
      const originalText = submitBtn.textContent;
      submitBtn.textContent = 'Creating...';
      clearErrors();

      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || form.querySelector('input[name="_token"]')?.value;

      fetch('{{ route('freelancer.register.submit') }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
      })
        .then(function (response) { return response.json().then(function (data) { return { status: response.status, data: data }; }); })
        .then(function (result) {
          if (result.data && result.data.success) {
            window.location.href = '{{ route('freelancer.register.submitted') }}';
          } else if (result.data && result.data.errors) {
            displayErrors(result.data.errors);
          } else {
            alert('Registration failed: ' + (result.data && result.data.message ? result.data.message : 'Unknown error'));
          }
        })
        .catch(function () { alert('Network error occurred. Please try again.'); })
        .finally(function () { submitBtn.disabled = false; submitBtn.textContent = originalText; });
    });

    // Auto-open when arriving via /freelancer/register (old links, the
    // sitewide header CTA) or a shared ?open=register link.
    const params = new URLSearchParams(window.location.search);
    if (params.get('open') === 'register') openModal();
  });
})();
</script>
