@include('header')

<main style="background:#000; min-height:100vh; color:#fff; font-family:'Outfit',sans-serif; padding:48px 0 70px;">
    <div class="container" style="max-width:980px;">
        <div style="text-align:center; margin-bottom:30px;">
            <p style="font-family:'Satisfy',cursive; font-size:clamp(18px,2.5vw,26px); color:#FFD23F; margin:0;">Faster bookings</p>
            <h1 style="font-size:clamp(26px,5vw,42px); font-weight:800; letter-spacing:1px; background:linear-gradient(135deg,#FFD700 0%,#D4AF37 50%,#B8960C 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; text-transform:uppercase; margin:6px 0 8px;">Scan Passport</h1>
            <p style="color:#aaa; font-size:15px; max-width:600px; margin:0 auto;">Upload a clear photo of the passport's main page and we'll read the details automatically.</p>
        </div>

        <div class="row g-4">
            {{-- Upload --}}
            <div class="col-lg-5">
                <div class="ps-card">
                    <label for="ppFile" class="ps-drop" id="ppDrop">
                        <div id="ppPlaceholder">
                            <i class="bi bi-cloud-arrow-up" style="font-size:42px; color:#FFD23F;"></i>
                            <p style="margin:10px 0 2px; font-weight:600;">Upload passport photo</p>
                            <small style="color:#888;">JPG, PNG or WebP · max 8 MB</small>
                        </div>
                        <img id="ppPreview" src="" alt="" style="display:none; width:100%; border-radius:10px;">
                    </label>
                    <input type="file" id="ppFile" accept="image/jpeg,image/png,image/jpg,image/webp" hidden>
                    <button id="ppScanBtn" class="ps-btn" disabled>
                        <i class="bi bi-magic"></i> Scan Passport
                    </button>
                    <p id="ppMsg" style="display:none; margin-top:12px; font-size:14px;"></p>
                </div>
            </div>

            {{-- Extracted fields --}}
            <div class="col-lg-7">
                <div class="ps-card">
                    <h3 style="font-size:18px; margin:0 0 14px; color:#FFD23F;">Passport Details</h3>
                    <div class="row g-2" id="ppFields">
                        @php
                            $fields = [
                                'full_name' => 'Full Name', 'surname' => 'Surname', 'given_names' => 'Given Names',
                                'passport_number' => 'Passport Number', 'nationality' => 'Nationality',
                                'date_of_birth' => 'Date of Birth', 'sex' => 'Sex', 'place_of_birth' => 'Place of Birth',
                                'date_of_issue' => 'Date of Issue', 'date_of_expiry' => 'Date of Expiry',
                                'issuing_country' => 'Issuing Country',
                            ];
                        @endphp
                        @foreach($fields as $key => $label)
                            <div class="col-md-6">
                                <label class="ps-label">{{ $label }}</label>
                                <input type="text" class="ps-input" id="f_{{ $key }}" data-key="{{ $key }}" placeholder="—">
                            </div>
                        @endforeach
                    </div>
                    <p style="color:#777; font-size:12px; margin-top:14px;">Always double-check the scanned details against the passport before submitting.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
    .ps-card { background:linear-gradient(180deg,#0e0e10 0%,#070708 100%); border:1px solid rgba(255,215,0,0.15); border-radius:16px; padding:22px; height:100%; }
    .ps-drop { display:flex; align-items:center; justify-content:center; flex-direction:column; min-height:220px; border:2px dashed rgba(255,215,0,0.3); border-radius:12px; cursor:pointer; text-align:center; padding:16px; transition:border-color .2s, background .2s; }
    .ps-drop:hover { border-color:#FFD23F; background:rgba(255,215,0,0.04); }
    .ps-btn { width:100%; margin-top:16px; height:48px; border:none; border-radius:12px; background:linear-gradient(135deg,#FFD700,#D4AF37); color:#000; font-weight:700; font-size:15px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; transition:opacity .2s; }
    .ps-btn:disabled { opacity:.45; cursor:not-allowed; }
    .ps-label { display:block; font-size:12px; color:#999; text-transform:uppercase; letter-spacing:1px; margin:6px 0 3px; }
    .ps-input { width:100%; height:42px; background:#111; border:1px solid #2a2a2a; border-radius:9px; padding:0 12px; color:#fff; font-size:14px; }
    .ps-input:focus { outline:none; border-color:#FFD23F; }
    .ps-input.filled { border-color:rgba(255,215,0,0.5); }
</style>

<script>
(function () {
    const fileInput = document.getElementById('ppFile');
    const drop = document.getElementById('ppDrop');
    const preview = document.getElementById('ppPreview');
    const placeholder = document.getElementById('ppPlaceholder');
    const scanBtn = document.getElementById('ppScanBtn');
    const msg = document.getElementById('ppMsg');
    let file = null;

    fileInput.addEventListener('change', e => {
        file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = ev => {
            preview.src = ev.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
        scanBtn.disabled = false;
        showMsg('', '');
    });

    function showMsg(text, color) {
        if (!text) { msg.style.display = 'none'; return; }
        msg.style.display = 'block';
        msg.style.color = color || '#aaa';
        msg.textContent = text;
    }

    scanBtn.addEventListener('click', () => {
        if (!file) return;
        const orig = scanBtn.innerHTML;
        scanBtn.disabled = true;
        scanBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Reading passport…';
        showMsg('Scanning…', '#FFD23F');

        const fd = new FormData();
        fd.append('passport', file);
        fd.append('_token', '{{ csrf_token() }}');

        fetch("{{ route('passport.extract') }}", {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: fd
        })
        .then(r => r.json().then(d => ({ ok: r.ok, d })))
        .then(({ ok, d }) => {
            scanBtn.disabled = false;
            scanBtn.innerHTML = orig;
            if (ok && d.success && d.fields) {
                let count = 0;
                document.querySelectorAll('#ppFields .ps-input').forEach(inp => {
                    const v = d.fields[inp.dataset.key];
                    if (v) { inp.value = v; inp.classList.add('filled'); count++; }
                });
                showMsg(count ? `Done — ${count} fields filled. Please verify.` : 'No details could be read. Try a clearer photo.', count ? '#4CAF50' : '#e0a100');
            } else {
                showMsg(d.message || 'Could not read the passport. Try a clearer photo.', '#dc3545');
            }
        })
        .catch(() => {
            scanBtn.disabled = false;
            scanBtn.innerHTML = orig;
            showMsg('Network error. Please try again.', '#dc3545');
        });
    });
})();
</script>

@include('footer')
