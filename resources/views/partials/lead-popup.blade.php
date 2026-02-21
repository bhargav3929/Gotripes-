{{--
╔═══════════════════════════════════════════════════════════════════════════════╗
║ PREMIUM LEAD CAPTURE POPUP - MOBILE OPTIMIZED
║ Appears 5 seconds after visitor enters the website (first time only)
║ Matches GoTrips premium black & gold design system
╚═══════════════════════════════════════════════════════════════════════════════╝
--}}

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    .gotrips-lead-popup-overlay {
        display: none;
        position: fixed;
        z-index: 99999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(0, 0, 0, 0.95) 0%, rgba(10, 10, 10, 0.98) 100%);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        animation: gotripsPopupFadeIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        overflow-y: auto;
        padding: 20px;
        align-items: center;
        /* Center vertically on desktop */
        justify-content: center;
    }

    .gotrips-lead-popup-overlay.active {
        display: flex;
    }

    .gotrips-lead-modal {
        background: linear-gradient(165deg, #0a0a0a 0%, #050505 100%);
        margin: auto;
        border: 1px solid rgba(212, 175, 55, 0.2);
        border-radius: 28px;
        width: 100%;
        max-width: 900px;
        box-shadow: 0 50px 100px rgba(0, 0, 0, 0.9), 0 0 80px rgba(212, 175, 55, 0.1);
        animation: gotripsModalSlideIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        color: #ffffff;
        position: relative;
        font-family: 'Outfit', sans-serif;
        overflow: hidden;
    }

    .gotrips-lead-modal::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 70%;
        height: 2px;
        background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.3) 15%, rgba(255, 215, 0, 0.9) 50%, rgba(212, 175, 55, 0.3) 85%, transparent);
    }

    .gotrips-lead-content-wrapper {
        display: grid;
        grid-template-columns: 1fr 1.1fr;
        min-height: 480px;
    }

    /* Left Side */
    .gotrips-lead-visual-section {
        background: linear-gradient(145deg, #0e0e0e 0%, #080808 100%);
        padding: 50px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
        border-right: 1px solid rgba(212, 175, 55, 0.1);
    }

    .gotrips-lead-brand-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(212, 175, 55, 0.05) 100%);
        border: 1px solid rgba(212, 175, 55, 0.3);
        border-radius: 50px;
        margin-bottom: 28px;
        width: fit-content;
    }

    .gotrips-lead-brand-badge svg {
        width: 18px;
        height: 18px;
        color: #FFD700;
    }

    .gotrips-lead-brand-badge span {
        font-size: 11px;
        font-weight: 700;
        color: #FFD700;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .gotrips-lead-headline {
        font-size: 32px;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 18px;
    }

    .gotrips-lead-headline .gold-text {
        background: linear-gradient(135deg, #FFD700 0%, #D4AF37 50%, #B8960C 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .gotrips-lead-subheadline {
        font-size: 15px;
        color: #888;
        line-height: 1.6;
        margin-bottom: 32px;
    }

    .gotrips-lead-benefits {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .gotrips-lead-benefits li {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 18px;
        font-size: 14px;
        color: #ccc;
        font-weight: 500;
    }

    .gotrips-lead-benefit-icon {
        width: 38px;
        height: 38px;
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.15) 0%, rgba(212, 175, 55, 0.05) 100%);
        border: 1px solid rgba(212, 175, 55, 0.25);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .gotrips-lead-benefit-icon svg {
        width: 18px;
        height: 18px;
        color: #FFD700;
    }

    /* Right Side */
    .gotrips-lead-form-section {
        padding: 45px 50px;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .gotrips-lead-close-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 44px;
        height: 44px;
        border: 1px solid rgba(255, 215, 0, 0.4);
        background: rgba(255, 215, 0, 0.05);
        color: #FFD700;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 24px;
        transition: all 0.3s ease;
        z-index: 10;
    }

    .gotrips-lead-close-btn:hover {
        color: #FFF0A0;
        border-color: rgba(255, 215, 0, 0.7);
        background: rgba(255, 215, 0, 0.15);
    }

    .gotrips-lead-form-title {
        font-size: 24px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 8px 0;
    }

    .gotrips-lead-form-subtitle {
        font-size: 14px;
        color: #666;
        margin: 0 0 25px 0;
    }

    .gotrips-lead-form-group {
        margin-bottom: 15px;
    }

    .gotrips-lead-form-label {
        display: block;
        margin-bottom: 6px;
        color: #FFD700;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .gotrips-lead-form-input,
    .gotrips-lead-form-select {
        width: 100%;
        height: 50px;
        background: #080808;
        border: 1px solid #222;
        border-radius: 12px;
        padding: 0 20px;
        color: #fff;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .gotrips-lead-form-input:focus,
    .gotrips-lead-form-select:focus {
        border-color: #FFD700;
        outline: none;
        background: #000;
        box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.1);
    }

    .gotrips-lead-phone-group {
        display: grid;
        grid-template-columns: 120px 1fr;
        gap: 10px;
    }

    .gotrips-lead-country-code {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        height: 50px;
        background: #080808;
        border: 1px solid #222;
        border-radius: 12px;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
    }

    .gotrips-lead-country-flag {
        width: 20px;
        border-radius: 2px;
    }

    .gotrips-lead-submit-btn {
        width: 100%;
        height: 54px;
        background: linear-gradient(135deg, #FFD700 0%, #B8960C 100%);
        color: #000;
        border: none;
        border-radius: 50px;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        cursor: pointer;
        margin-top: 10px;
        box-shadow: 0 10px 30px rgba(255, 215, 0, 0.2);
    }

    .gotrips-lead-disclaimer {
        margin-top: 15px;
        font-size: 11px;
        color: #444;
        text-align: center;
        line-height: 1.4;
    }

    /* Success State */
    .gotrips-lead-success {
        text-align: center;
        padding: 60px 40px;
        display: none;
    }

    .gotrips-lead-success.active {
        display: block;
        animation: gotripsPopupFadeIn 0.5s ease;
    }

    /* MOBILE OPTIMIZATION */
    @media (max-width: 900px) {
        .gotrips-lead-popup-overlay {
            padding: 10px;
            /* Tighter padding on mobile */
            align-items: flex-start;
            /* Allow scrolling from top */
        }

        .gotrips-lead-modal {
            margin-top: 20px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }

        .gotrips-lead-content-wrapper {
            grid-template-columns: 1fr;
            min-height: auto;
        }

        /* Compact Top Section for Mobile */
        .gotrips-lead-visual-section {
            padding: 25px 20px;
            /* Reduced padding */
            background: linear-gradient(180deg, #111 0%, #050505 100%);
            border-right: none;
            border-bottom: 1px solid rgba(255, 215, 0, 0.15);
            text-align: center;
            align-items: center;
        }

        .gotrips-lead-brand-badge {
            margin-bottom: 15px;
            padding: 6px 12px;
        }

        .gotrips-lead-brand-badge span {
            font-size: 9px;
        }

        .gotrips-lead-headline {
            font-size: 22px;
            margin-bottom: 8px;
        }

        .gotrips-lead-subheadline {
            font-size: 13px;
            margin-bottom: 0;
            display: none;
            /* Hide subheadline on mobile to save space */
        }

        .gotrips-lead-benefits {
            display: none;
            /* Hide benefits list to prioritize form */
        }

        /* Form Area Mobile */
        .gotrips-lead-form-section {
            padding: 25px 20px;
        }

        .gotrips-lead-close-btn {
            top: 10px;
            right: 10px;
            width: 35px;
            height: 35px;
            font-size: 20px;
            background: rgba(255, 215, 0, 0.08);
        }

        .gotrips-lead-form-header {
            text-align: center;
            margin-bottom: 20px;
            padding-right: 0;
        }

        .gotrips-lead-form-title {
            font-size: 20px;
        }

        .gotrips-lead-form-subtitle {
            font-size: 13px;
        }

        .gotrips-lead-form-input,
        .gotrips-lead-form-select {
            height: 45px;
            font-size: 13px;
        }

        .gotrips-lead-country-code {
            height: 45px;
        }

        .gotrips-lead-phone-group {
            grid-template-columns: 80px 1fr;
        }

        .gotrips-lead-submit-btn {
            height: 50px;
            font-size: 13px;
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
</style>

<div id="gotripsLeadPopupOverlay" class="gotrips-lead-popup-overlay">
    <div class="gotrips-lead-modal">

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
            <button class="gotrips-lead-submit-btn" style="margin-top:30px; width:200px;"
                onclick="closeLeadPopup()">Close</button>
        </div>

        <div class="gotrips-lead-content-wrapper" id="gotripsLeadContentWrapper">
            {{-- Left Visual Section --}}
            <div class="gotrips-lead-visual-section">
                <div class="gotrips-lead-brand-badge">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                    </svg>
                    <span>15+ Years of Excellence</span>
                </div>

                <h2 class="gotrips-lead-headline">
                    <span class="gold-text">Our Expertise.</span><br>
                    <span class="white-text">Your Journey.</span>
                </h2>

                <p class="gotrips-lead-subheadline">
                    Powered by industry legends with 15 years of expertise, serving travelers worldwide.
                </p>

                <ul class="gotrips-lead-benefits">
                    <li>
                        <div class="gotrips-lead-benefit-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                                <path
                                    d="M10.067.87a2.89 2.89 0 0 0-4.134 0l-.622.638-.89-.011a2.89 2.89 0 0 0-2.924 2.924l.01.89-.636.622a2.89 2.89 0 0 0 0 4.134l.637.622-.011.89a2.89 2.89 0 0 0 2.924 2.924l.89-.01.622.636a2.89 2.89 0 0 0 4.134 0l.622-.637.89.011a2.89 2.89 0 0 0 2.924-2.924l-.01-.89.636-.622a2.89 2.89 0 0 0 0-4.134l-.637-.622.011-.89a2.89 2.89 0 0 0-2.924-2.924l-.89.01-.622-.636zm.287 5.984-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7 8.793l2.646-2.647a.5.5 0 0 1 .708.708z" />
                            </svg>
                        </div>
                        <span>Industry Legends at Your Service</span>
                    </li>
                    <li>
                        <div class="gotrips-lead-benefit-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                                <path
                                    d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664z" />
                            </svg>
                        </div>
                        <span>Best Price Guarantee</span>
                    </li>
                </ul>
            </div>

            {{-- Right Form Section --}}
            <div class="gotrips-lead-form-section">
                <button type="button" class="gotrips-lead-close-btn" id="gotripsLeadCloseBtn">×</button>

                <div class="gotrips-lead-form-header">
                    <h3 class="gotrips-lead-form-title">Get Free Quote</h3>
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
                                placeholder="50 000 0000" required>
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
</div>

<script>
    // Complete Countries Data with Flags and Dial Codes
    // Default fallback list (top common countries)
    let countries = [
        { name: "United Arab Emirates", code: "+971", flag: "ae" },
        { name: "India", code: "+91", flag: "in" },
        { name: "Saudi Arabia", code: "+966", flag: "sa" },
        { name: "United States", code: "+1", flag: "us" },
        { name: "United Kingdom", code: "+44", flag: "gb" },
        { name: "Pakistan", code: "+92", flag: "pk" },
        // ... list will be populated/overwritten by API
    ];

    let isDropdownOpen = false;

    // Fetch and Initialize Country List
    function initCountryList() {
        const countryList = document.getElementById('countryList');
        if (!countryList) return;

        // Render initial (fallback) list first
        renderCountries(countries);

        // Fetch full list
        fetch('https://restcountries.com/v3.1/all?fields=name,flags,idd,cca2')
            .then(res => res.json())
            .then(data => {
                // Process Data
                countries = data.map(c => {
                    const code = (c.idd.root || '') + (c.idd.suffixes ? c.idd.suffixes[0] : '');
                    return {
                        name: c.name.common,
                        code: code,
                        flag: c.cca2.toLowerCase()
                    };
                }).filter(c => c.code && c.code !== '+').sort((a, b) => a.name.localeCompare(b.name));

                // Prioritize UAE, India, Saudi, UK, US
                const priorities = ['ae', 'in', 'sa', 'gb', 'us'];
                const top = [];
                const others = [];

                countries.forEach(c => {
                    if (priorities.includes(c.flag)) top.push(c);
                    else others.push(c);
                });

                // Sort top based on priorities order
                top.sort((a, b) => priorities.indexOf(a.flag) - priorities.indexOf(b.flag));

                countries = [...top, ...others];
                renderCountries(countries);
            })
            .catch(err => console.log('Country API failed, using fallback', err));
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