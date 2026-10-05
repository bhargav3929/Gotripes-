{{--
    Global phone-with-country-dropdown.
    Auto-upgrades every <input type="tel"> on the page to intl-tel-input
    (country dropdown with flags + dial codes), and rewrites the value to
    the full international number on form submit.

    Opt out per input with `data-no-intl` or wrap inside `.phone-input-group`.
--}}
@once
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.3/build/css/intlTelInput.css">
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.3/build/js/intlTelInput.min.js"></script>
<style>
    .iti { width: 100%; display: block; }
    .iti--allow-dropdown input.iti__tel-input,
    .iti input[type=tel] { padding-left: 96px !important; }
    .iti__country-list { max-height: 260px; z-index: 9999; }
    .iti--separate-dial-code .iti__selected-dial-code { font-weight: 600; }
</style>
<script>
// Shared IP-based country guess, used by this file's auto-init and by the
// handful of pages that build their own intl-tel-input instance instead of
// relying on the auto-init (uaevisa, saudi-visa, the partner pop-up in
// banner.blade.php). Memoised to one network call per page no matter how
// many phone fields exist. Resolves to null (never rejects) on any failure
// or after 2.5s, so callers always have a safe "leave it on the ae default"
// path — this must never be the thing that breaks a phone field.
window.GTDetectCountry = (function () {
    var promise = null;
    // Two independent lookups raced against each other: ipapi.co is a free
    // service that rate-limits and is blocked on some networks, so on its own
    // it left a share of visitors on the +971 default. Cloudflare's trace
    // endpoint has no key or quota. First usable answer wins.
    function viaIpapi() {
        return fetch('https://ipapi.co/json/', { cache: 'no-store' })
            .then(function (res) { return res.ok ? res.json() : null; })
            .then(function (d) { return d && d.country_code ? String(d.country_code).toLowerCase() : null; });
    }
    function viaCloudflare() {
        return fetch('https://www.cloudflare.com/cdn-cgi/trace', { cache: 'no-store' })
            .then(function (res) { return res.ok ? res.text() : ''; })
            .then(function (t) {
                var m = /^loc=([A-Z]{2})$/m.exec(t || '');
                return m && m[1] !== 'XX' && m[1] !== 'T1' ? m[1].toLowerCase() : null;
            });
    }
    return function () {
        if (promise) return promise;
        promise = new Promise(function (resolve) {
            var settled = false, pending = 2;
            var done = function (code) { if (!settled) { settled = true; resolve(code); } };
            var miss = function () { if (--pending === 0) done(null); };
            var timer = setTimeout(function () { done(null); }, 3500);
            [viaIpapi, viaCloudflare].forEach(function (lookup) {
                lookup().then(function (code) { if (code) { clearTimeout(timer); done(code); } else { miss(); } },
                              function () { miss(); });
            });
        });
        return promise;
    };
})();

// Make a form's Country <select> drive its phone dial code. If someone says
// they are in Tanzania, the phone box should read +255, not +971. Stops
// following once the visitor picks a dial code by hand.
window.GTLinkCountrySelect = function (select, iti) {
    if (!select || !iti || !window.intlTelInputGlobals) return;
    var manual = false, programmatic = false;
    var input = iti.telInput || (iti.a || null);
    var alias = { 'uk': 'gb', 'usa': 'us', 'russia': 'ru', 'south korea': 'kr', 'north korea': 'kp', 'iran': 'ir',
                  'syria': 'sy', 'vietnam': 'vn', 'tanzania': 'tz', 'czech republic': 'cz', 'myanmar': 'mm',
                  'turkey': 'tr', 'ivory coast': 'ci', 'cape verde': 'cv', 'laos': 'la', 'palestine': 'ps' };
    var norm = function (t) { return String(t || '').toLowerCase().replace(/\(.*?\)/g, '').replace(/[^a-z ]/g, '').replace(/\s+/g, ' ').trim(); };
    function isoFor(name) {
        var n = norm(name);
        if (!n) return null;
        if (alias[n]) return alias[n];
        var data = window.intlTelInputGlobals.getCountryData();
        var hit = data.filter(function (c) { return norm(c.name) === n; })[0]
               || data.filter(function (c) { return norm(c.name).indexOf(n) === 0 || n.indexOf(norm(c.name)) === 0; })[0];
        return hit ? hit.iso2 : null;
    }
    if (input) input.addEventListener('countrychange', function () { if (!programmatic) manual = true; });
    select.addEventListener('change', function () {
        if (manual) return;
        var iso = isoFor(select.options[select.selectedIndex] && select.options[select.selectedIndex].text);
        if (!iso) return;
        programmatic = true;
        try { iti.setCountry(iso); } finally { setTimeout(function () { programmatic = false; }, 0); }
    });
};

(function () {
    function start() {
        if (!window.intlTelInput) return;
        var initialized = new WeakSet();

        function initOne(input) {
            if (!input || initialized.has(input)) return;
            if (input.type !== 'tel') return;
            if (input.closest('.phone-input-group')) return;
            if (input.closest('.iti')) { initialized.add(input); return; }
            if (input.dataset.noIntl !== undefined) return;

            var iti;
            try {
                iti = window.intlTelInput(input, {
                    initialCountry: 'ae',
                    preferredCountries: ['ae', 'sa', 'in', 'pk', 'gb', 'us'],
                    separateDialCode: true,
                    utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.3/build/js/utils.js',
                });
            } catch (e) { return; }

            initialized.add(input);
            input.__iti = iti;

            // The dial code starts on the UAE default above so the field never
            // renders empty; swap it to the visitor's actual country once IP
            // lookup resolves, unless they've already changed it themselves.
            window.GTDetectCountry().then(function (code) {
                if (!code) return;
                var current = iti.getSelectedCountryData();
                if (!current || current.iso2 === 'ae') iti.setCountry(code);
            });

            var form = input.form;
            if (form && !form.__itiHooked) {
                form.__itiHooked = true;
                form.addEventListener('submit', function () {
                    form.querySelectorAll('input[type="tel"]').forEach(function (el) {
                        if (!el.__iti) return;
                        var full = el.__iti.getNumber();
                        if (full) {
                            el.value = full;
                        } else if (el.value) {
                            var dial = el.__iti.getSelectedCountryData().dialCode;
                            if (dial) el.value = '+' + dial + el.value.replace(/^\+?/, '').replace(/\s+/g, '');
                        }
                    });
                }, true);
            }
        }

        function initAll(root) {
            (root || document).querySelectorAll('input[type="tel"]').forEach(initOne);
        }

        initAll(document);

        var mo = new MutationObserver(function (muts) {
            muts.forEach(function (m) {
                m.addedNodes.forEach(function (n) {
                    if (n.nodeType !== 1) return;
                    if (n.matches && n.matches('input[type="tel"]')) initOne(n);
                    if (n.querySelectorAll) n.querySelectorAll('input[type="tel"]').forEach(initOne);
                });
            });
        });
        mo.observe(document.documentElement, { childList: true, subtree: true });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
</script>
@endonce
