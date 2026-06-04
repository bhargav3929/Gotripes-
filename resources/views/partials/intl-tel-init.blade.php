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
