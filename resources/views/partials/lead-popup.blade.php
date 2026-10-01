{{--
╔═══════════════════════════════════════════════════════════════════════════════╗
║ PREMIUM LEAD CAPTURE POPUP - "Get Free Quote"
║ Appears 5 seconds after visitor enters the website (first time only)
║ Circular black-and-gold medallion, same family as the Visa Route selector and
║ the registration pop-ups (Amer, 1 Oct 2026: "Can we change this also to
║ circular - welcome page"). It used to be a two-column rounded rectangle with a
║ marketing panel on the left; a circle cannot hold two columns, so the panel is
║ folded into a single "15+ years" chip under the logo and the form is one
║ column. Every field, id, name and the submit path are unchanged.
║
║ Circle in any window at least 900px wide and 536px tall: the disc is never
║ drawn below 640px (the size that still holds the form) and a shorter window
║ scales it to fit (partials/gold-medallion-fit.blade.php). Narrower than that
║ it becomes a rounded card with the same ring.
╚═══════════════════════════════════════════════════════════════════════════════╝
--}}
@include('partials.gold-medallion-tokens')
@include('partials.gold-medallion-fit')
@php
    $leadModalLogo = (isset($company) && $company && $company->logo)
        ? asset('storage/' . $company->logo)
        : asset('assets/index_files/logo.png');

    // Country picker data comes from our own list (App\Support\CountryCodes), not an
    // outside API. The picker used to fetch restcountries.com in the browser; that
    // service now answers with a redirect lacking CORS headers, so the fetch
    // failed for every visitor and the picker offered only its 6-country fallback
    // (no Tanzania, no Kenya, no Nigeria...). Main markets first, then A-Z.
    $leadPriority = ['AE', 'IN', 'SA', 'GB', 'US'];
    $leadAll = collect(\App\Support\CountryCodes::all())->map(fn ($c) => [
        'name' => $c['name'], 'code' => $c['dial'], 'flag' => strtolower($c['iso']),
    ]);
    $leadCountries = $leadAll
        ->filter(fn ($c) => in_array(strtoupper($c['flag']), $leadPriority, true))
        ->sortBy(fn ($c) => array_search(strtoupper($c['flag']), $leadPriority, true))
        ->concat($leadAll->reject(fn ($c) => in_array(strtoupper($c['flag']), $leadPriority, true)))
        ->values();
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    .gotrips-lead-popup-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 99999;
        background: rgba(0, 0, 0, 0.92);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        animation: gotripsPopupFadeIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        padding: 16px;
        /* clip, not just hidden: the scaled disc keeps its full layout size, which
           makes a hidden container scrollable, and a browser scrolls those to
           reveal a focused field, shifting the circle up. clip cannot scroll. */
        overflow: hidden;
        overflow: clip;
        align-items: center;
        justify-content: center;
    }

    .gotrips-lead-popup-overlay.active {
        display: flex;
    }

    /* Medallion shell: black disc, brushed-gold ring (shared tokens). */
    .gotrips-lead-modal {
        --disc: max(640px, min(780px, 96vw, 92vh));
        --ring-w: calc(var(--disc) * var(--gm-ring-ratio, .045));
        scale: var(--gm-fit, 1);
        position: relative;
        width: var(--disc);
        height: var(--disc);
        margin: 0;
        padding: 0;
        border-radius: 50%;
        border: var(--ring-w) solid transparent;
        background: var(--gm-disc-fill) padding-box, var(--gm-gold-metal) border-box;
        box-shadow: 0 30px 90px rgba(0, 0, 0, .85), 0 0 60px rgba(212, 175, 55, .18), inset 0 0 0 1px rgba(60, 42, 8, .9);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-family: 'Outfit', sans-serif;
        overflow: visible;
        animation: gotripsModalSlideIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    /* Inner hairline just inside the band. */
    .gotrips-lead-modal::before {
        content: '';
        position: absolute;
        inset: calc(var(--disc) * .012);
        border: 2px solid rgba(212, 175, 55, .6);
        border-radius: 50%;
        pointer-events: none;
    }

    .gotrips-lead-brand-medallion {
        flex: 0 0 auto;
        width: calc(var(--disc) * .12);
        height: calc(var(--disc) * .12);
        border-radius: 50%;
        overflow: hidden;
        background: #050505;
        box-shadow: 0 10px 26px rgba(0, 0, 0, .6);
        margin-bottom: calc(var(--disc) * .012);
    }

    .gotrips-lead-brand-medallion img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Close sits on the ring at 45 degrees, like a clasp (same maths as the
       registration pop-ups: the ring's centre line at 45 degrees is about
       0.117 of the disc from the corner). */
    .gotrips-lead-close-btn {
        position: absolute;
        z-index: 3;
        top: calc(var(--disc) * .117 - 20px);
        right: calc(var(--disc) * .117 - 20px);
        width: 40px;
        height: 40px;
        padding: 0;
        background: #050505;
        border: 2px solid var(--gm-gold);
        color: var(--gm-gold);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 26px;
        line-height: 1;
        box-shadow: 0 4px 14px rgba(0, 0, 0, .6);
        transition: background .2s ease, color .2s ease;
    }

    .gotrips-lead-close-btn:hover {
        background: var(--gm-gold);
        color: #000;
    }

    .gotrips-lead-content-wrapper {
        flex: 0 0 auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 400px;
    }

    .gotrips-lead-brand-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(212, 175, 55, 0.05) 100%);
        border: 1px solid rgba(212, 175, 55, 0.3);
        border-radius: 50px;
        margin-bottom: 10px;
    }

    .gotrips-lead-brand-badge svg {
        width: 12px;
        height: 12px;
        color: var(--gm-gold-lite);
    }

    .gotrips-lead-brand-badge span {
        font-size: 9px;
        font-weight: 700;
        color: var(--gm-gold-lite);
        text-transform: uppercase;
        letter-spacing: 1.8px;
    }

    .gotrips-lead-form-header {
        text-align: center;
        margin-bottom: 14px;
    }

    .gotrips-lead-form-title {
        font-size: calc(var(--disc) * .03);
        font-weight: 800;
        letter-spacing: .045em;
        color: var(--gm-gold);
        text-transform: uppercase;
        white-space: nowrap;
        margin: 0 0 4px;
    }

    .gotrips-lead-form-title b {
        color: #fff;
        font-weight: 800;
    }

    .gotrips-lead-form-subtitle {
        font-size: 12px;
        color: #9a9a9a;
        margin: 0;
    }

    .gotrips-lead-form {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: stretch;
    }

    .gotrips-lead-form-group {
        margin-bottom: 11px;
    }

    .gotrips-lead-form-label {
        display: block;
        margin-bottom: 5px;
        color: var(--gm-gold-lite);
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .gotrips-lead-form-input,
    .gotrips-lead-form-select {
        width: 100%;
        height: 44px;
        background: #080808;
        border: 1px solid #222;
        border-radius: 12px;
        padding: 0 16px;
        color: #fff;
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
        transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
    }

    .gotrips-lead-form-input:focus,
    .gotrips-lead-form-select:focus {
        border-color: var(--gm-gold);
        outline: none;
        background: #000;
        box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.1);
    }

    .gotrips-lead-phone-group {
        display: grid;
        grid-template-columns: 112px 1fr;
        gap: 10px;
    }

    .gotrips-lead-country-flag {
        width: 20px;
        border-radius: 2px;
    }

    /* Primary button: gold pill centred on its own row, like the Next button
       on the registration pop-ups. A circle narrows toward the bottom, which a
       centred control respects. */
    .gotrips-lead-submit-btn {
        align-self: center;
        flex: none;
        width: calc(var(--disc) * .34);
        min-width: 230px;
        height: 46px;
        margin-top: 6px;
        background: var(--gm-gold-metal);
        color: #000;
        border: none;
        border-radius: 50px;
        font-family: 'Outfit', sans-serif;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.6px;
        white-space: nowrap;
        cursor: pointer;
        box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .gotrips-lead-submit-btn:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 14px 34px rgba(255, 215, 0, 0.28);
    }

    .gotrips-lead-submit-btn:disabled {
        opacity: .65;
        cursor: default;
    }

    /* The bottom of a disc is its narrowest part, so this line wraps to two
       short lines instead of running as wide as the fields above it. */
    .gotrips-lead-disclaimer {
        max-width: 250px;
        margin: 10px auto 0;
        text-wrap: balance;
        font-size: 10.5px;
        color: #8a8a8a;
        text-align: center;
        line-height: 1.4;
    }

    .gotrips-lead-disclaimer a {
        color: var(--gm-gold-lite);
    }

    /* Success state, centred in the disc. */
    .gotrips-lead-success {
        display: none;
        text-align: center;
    }

    .gotrips-lead-success.active {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 340px;
        animation: gotripsPopupFadeIn 0.5s ease;
    }

    .gotrips-lead-success h3 {
        font-size: 22px;
        font-weight: 800;
        color: var(--gm-gold);
        margin: 0 0 8px;
        text-transform: uppercase;
        letter-spacing: .045em;
    }

    .gotrips-lead-success p {
        font-size: 13px;
        line-height: 1.55;
        color: #b5b5b5;
        margin: 0;
    }

    /* Card mode: phones and tablets (narrower than 900px) and windows shorter
       than 536px, where a circle cannot hold a form. Same ring, grown to its
       content; the page scrolls if it must, the form never does. */
    @media (max-width: 899px), (max-height: 535px) {
        .gotrips-lead-popup-overlay {
            padding: 12px;
            overflow-y: auto;
            align-items: flex-start;
        }

        .gotrips-lead-modal {
            --disc: 520px;
            scale: none;
            width: min(94vw, 520px);
            height: auto;
            margin: 12px auto;
            border-radius: 32px;
            border-width: 10px;
            padding: 22px 0 24px;
            justify-content: flex-start;
        }

        .gotrips-lead-modal::before {
            inset: 8px;
            border-radius: 22px;
        }

        .gotrips-lead-brand-medallion {
            width: 64px;
            height: 64px;
            margin-bottom: 8px;
        }

        .gotrips-lead-close-btn {
            top: 12px;
            right: 12px;
            width: 34px;
            height: 34px;
            font-size: 22px;
        }

        .gotrips-lead-content-wrapper {
            width: 100%;
            padding: 0 22px;
        }

        .gotrips-lead-form-title {
            font-size: 18px;
            white-space: normal;
            text-align: center;
        }

        .gotrips-lead-submit-btn {
            width: min(100%, 280px);
            min-width: 0;
        }

        .gotrips-lead-success.active {
            width: 100%;
            padding: 24px 22px;
        }
    }

    @media (max-width: 420px) {
        .gotrips-lead-phone-group {
            grid-template-columns: 100px 1fr;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .gotrips-lead-popup-overlay,
        .gotrips-lead-modal,
        .gotrips-lead-success.active {
            animation: none;
        }
    }

    @keyframes gotripsPopupFadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes gotripsModalSlideIn {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Premium Country Selector Styles */
    .gotrips-country-selector {
        position: relative;
        width: 100%;
    }

    .gotrips-country-selected {
        display: flex;
        align-items: center;
        gap: 6px;
        height: 50px;
        background: #080808;
        border: 1px solid #222;
        border-radius: 12px;
        padding: 0 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 100px;
        white-space: nowrap;
    }

    .gotrips-country-selected:hover {
        border-color: #FFD700;
    }

    .gotrips-country-selected .gotrips-lead-country-flag {
        width: 24px;
        height: 16px;
        border-radius: 2px;
        object-fit: cover;
    }

    .gotrips-country-selected span {
        color: #fff;
        font-size: 13px;
        font-weight: 600;
    }

    .gotrips-country-selected .dropdown-arrow {
        margin-left: auto;
        color: #666;
        transition: transform 0.3s ease;
    }

    .gotrips-country-selector.open .dropdown-arrow {
        transform: rotate(180deg);
    }

    .gotrips-country-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 5px);
        left: 0;
        width: 280px;
        max-height: 300px;
        background: #0a0a0a;
        border: 1px solid rgba(255, 215, 0, 0.3);
        border-radius: 12px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.8);
        z-index: 1000;
        overflow: hidden;
    }

    .gotrips-country-selector.open .gotrips-country-dropdown {
        display: block;
        animation: dropdownFadeIn 0.2s ease;
    }

    @keyframes dropdownFadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .gotrips-country-search {
        width: 100%;
        height: 45px;
        background: #111;
        border: none;
        border-bottom: 1px solid #222;
        padding: 0 15px;
        color: #fff;
        font-family: 'Outfit', sans-serif;
        font-size: 13px;
    }

    .gotrips-country-search:focus {
        outline: none;
        background: #0a0a0a;
    }

    .gotrips-country-search::placeholder {
        color: #555;
    }

    .gotrips-country-list {
        max-height: 250px;
        overflow-y: auto;
    }

    .gotrips-country-list::-webkit-scrollbar {
        width: 6px;
    }

    .gotrips-country-list::-webkit-scrollbar-track {
        background: #111;
    }

    .gotrips-country-list::-webkit-scrollbar-thumb {
        background: #333;
        border-radius: 3px;
    }

    .gotrips-country-list::-webkit-scrollbar-thumb:hover {
        background: #FFD700;
    }

    .gotrips-country-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 15px;
        cursor: pointer;
        transition: all 0.2s ease;
        border-bottom: 1px solid #1a1a1a;
    }

    .gotrips-country-item:hover {
        background: rgba(255, 215, 0, 0.1);
    }

    .gotrips-country-item:last-child {
        border-bottom: none;
    }

    .gotrips-country-item img {
        width: 24px;
        height: 16px;
        border-radius: 2px;
        object-fit: cover;
    }

    .gotrips-country-item .country-name {
        flex: 1;
        color: #ccc;
        font-size: 13px;
    }

    .gotrips-country-item .country-code {
        color: #FFD700;
        font-size: 12px;
        font-weight: 600;
    }

    @media (max-width: 900px) {
        .gotrips-country-dropdown {
            width: 250px;
        }

        .gotrips-country-selected {
            height: 45px;
        }
    }

    /* The country list opens downward from the Mobile field; inside a disc it
       must stay shorter than the room below it, and the list scrolls. */
    .gotrips-lead-modal .gotrips-country-selected {
        height: 44px;
        min-width: 0;
    }

    .gotrips-lead-modal .gotrips-country-dropdown {
        width: 260px;
        max-height: 205px;
    }

    .gotrips-lead-modal .gotrips-country-list {
        max-height: 156px;
    }
</style>

<div id="gotripsLeadPopupOverlay" class="gotrips-lead-popup-overlay">
    <div class="gotrips-lead-modal" data-gm-fit="640">
        <div class="gotrips-lead-brand-medallion" aria-hidden="true">
            <img src="{{ $leadModalLogo }}" alt="">
        </div>

        <button type="button" class="gotrips-lead-close-btn" id="gotripsLeadCloseBtn" aria-label="Close">&times;</button>

        <!-- Success Message -->
        <div id="gotripsLeadSuccess" class="gotrips-lead-success">
            <div
                style="width:70px; height:70px; background:rgba(255,215,0,0.1); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; border:2px solid #FFD700;">
                <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="#FFD700" viewBox="0 0 16 16">
                    <path
                        d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                </svg>
            </div>
            <h3>Thank You!</h3>
            <p>Your request has been received. Our expert will contact you shortly.</p>
            <button class="gotrips-lead-submit-btn" style="margin-top:26px;"
                onclick="closeLeadPopup()">Close</button>
        </div>

        <div class="gotrips-lead-content-wrapper" id="gotripsLeadContentWrapper">
            <div class="gotrips-lead-brand-badge">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                </svg>
                <span>15+ Years of Excellence</span>
            </div>

            <div class="gotrips-lead-form-header">
                <h3 class="gotrips-lead-form-title">Get <b>Free</b> Quote</h3>
                <p class="gotrips-lead-form-subtitle">Unlock exclusive premium travel deals</p>
            </div>

            <form class="gotrips-lead-form" id="gotripsLeadForm">
                @csrf
                <div class="gotrips-lead-form-group">
                    <label class="gotrips-lead-form-label">Name</label>
                    <input type="text" name="lead_name" class="gotrips-lead-form-input" placeholder="Full Name"
                        required>
                </div>

                <div class="gotrips-lead-form-group">
                    <label class="gotrips-lead-form-label">Mobile</label>
                    <div class="gotrips-lead-phone-group">
                        <!-- Premium Country Selector -->
                        <div class="gotrips-country-selector" id="countrySelector">
                            <div class="gotrips-country-selected" id="countrySelected"
                                onclick="toggleCountryDropdown()">
                                <img src="https://flagcdn.com/w40/ae.png" alt="UAE"
                                    class="gotrips-lead-country-flag" id="selectedFlag">
                                <span id="selectedCode">+971</span>
                                <svg class="dropdown-arrow" width="10" height="6" viewBox="0 0 10 6" fill="none">
                                    <path d="M1 1L5 5L9 1" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" />
                                </svg>
                            </div>
                            <div class="gotrips-country-dropdown" id="countryDropdown">
                                <input type="text" class="gotrips-country-search" id="countrySearch"
                                    placeholder="Search country..." oninput="filterCountries()">
                                <div class="gotrips-country-list" id="countryList">
                                    <!-- Countries will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="lead_country_code" id="leadCountryCode" value="+971">
                        <input type="tel" name="lead_phone" class="gotrips-lead-form-input"
                            placeholder="50 000 0000" data-no-intl required>
                    </div>
                </div>

                <div class="gotrips-lead-form-group">
                    <label class="gotrips-lead-form-label">Service</label>
                    <select name="lead_interest" class="gotrips-lead-form-select" required>
                        <option value="" disabled selected>Select Service</option>
                        <option value="uae_activities">UAE Activities</option>
                        <option value="holiday_packages">Holiday Packages</option>
                        <option value="umrah">Umrah Services</option>
                        <option value="visa_services">Visa Services</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <button type="submit" class="gotrips-lead-submit-btn">GET QUOTE</button>

                <p class="gotrips-lead-disclaimer">
                    By submitting, you agree to our <a href="#">Terms</a>. Your data is secure.
                </p>
            </form>
        </div>
    </div>
</div>

<script>
    // Complete Countries Data with Flags and Dial Codes
    // Full country list, rendered by the server (see the @php block above).
    let countries = @json($leadCountries);

    let isDropdownOpen = false;

    // Initialize the country list
    function initCountryList() {
        if (!document.getElementById('countryList')) return;
        renderCountries(countries);
    }

    function renderCountries(list) {
        const countryList = document.getElementById('countryList');
        countryList.innerHTML = '';
        list.forEach(country => {
            const item = document.createElement('div');
            item.className = 'gotrips-country-item';
            item.innerHTML = `
                <img src="https://flagcdn.com/w40/${country.flag}.png" alt="${country.name}" loading="lazy">
                <span class="country-name">${country.name}</span>
                <span class="country-code">${country.code}</span>
            `;
            item.onclick = () => selectCountry(country);
            countryList.appendChild(item);
        });
    }

    // Toggle Dropdown
    window.toggleCountryDropdown = function () {
        const selector = document.getElementById('countrySelector');
        const search = document.getElementById('countrySearch');

        isDropdownOpen = !isDropdownOpen;

        if (isDropdownOpen) {
            selector.classList.add('open');
            setTimeout(() => search.focus(), 100);
        } else {
            selector.classList.remove('open');
            search.value = '';
            filterCountries();
        }
    }

    // Select Country
    function selectCountry(country) {
        document.getElementById('selectedFlag').src = `https://flagcdn.com/w40/${country.flag}.png`;
        document.getElementById('selectedCode').textContent = country.code;
        document.getElementById('leadCountryCode').value = country.code;
        toggleCountryDropdown();
    }

    // Filter Countries
    window.filterCountries = function () {
        const search = document.getElementById('countrySearch').value.toLowerCase();
        const items = document.querySelectorAll('.gotrips-country-item');

        items.forEach(item => {
            const name = item.querySelector('.country-name').textContent.toLowerCase();
            const code = item.querySelector('.country-code').textContent.toLowerCase();

            if (name.includes(search) || code.includes(search)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        const selector = document.getElementById('countrySelector');
        if (selector && !selector.contains(e.target) && isDropdownOpen) {
            toggleCountryDropdown();
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        const popup = document.getElementById('gotripsLeadPopupOverlay');
        const closeBtn = document.getElementById('gotripsLeadCloseBtn');
        const form = document.getElementById('gotripsLeadForm');

        // Initialize country selector
        initCountryList();

        // Show after 5 seconds (only once per session)
        const popupShown = sessionStorage.getItem('gotripsLeadPopupShown');
        if (!popupShown) {
            setTimeout(() => {
                popup.classList.add('active');
            }, 5000); // 5 Seconds
        }

        // Close functions
        window.closeLeadPopup = function () {
            popup.classList.remove('active');
            sessionStorage.setItem('gotripsLeadPopupShown', 'true');
        }

        closeBtn.addEventListener('click', closeLeadPopup);

        // Form Submit
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.innerText;
            btn.innerText = 'SENDING...';
            btn.disabled = true;

            const formData = new FormData(form);

            fetch("{{ route('contact.submit') }}", {
                method: "POST", body: formData,
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
                .then(res => {
                    document.getElementById('gotripsLeadContentWrapper').style.display = 'none';
                    document.getElementById('gotripsLeadSuccess').classList.add('active');
                    sessionStorage.setItem('gotripsLeadPopupShown', 'true');
                })
                .catch(err => {
                    alert('Something went wrong. Please try again.');
                    btn.innerText = originalText;
                    btn.disabled = false;
                });
        });
    });
</script>