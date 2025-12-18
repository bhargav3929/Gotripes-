<div class="flight-banner-section">
  <style>
    /* Your original styles with enhancements */
    .flight-banner-section .flight-search-box {
      background: #222;
      padding: 25px;
      border-radius: 16px;
      color: white;
      font-family: 'Segoe UI', sans-serif;
    }
    
    .flight-banner-section .flight-btn-search {
      background-color: rgb(255,210,63,.7);
      border: none;
      color: white;
      padding: 10px 24px;
      font-size: 16px;
      border-radius: 0.7rem;
      font-weight: 600;
      width: 100%;
      cursor: pointer;
    }
    
    .flight-banner-section .flight-btn-search:hover:enabled {
      background-color: rgb(255,210,63,1);
    }
    
    .flight-banner-section .flight-form-group-autocomplete {
      position: relative;
    }
    
    .flight-banner-section .flight-autocomplete-dropdown {
      position: absolute;
      top: 100%;
      left: 0;
      right: 0;
      z-index: 2111;
      background: #191c23;
      border: 1px solid #222;
      box-shadow: 0 2px 12px 1px #0006;
      max-height: 230px;
      overflow-y: auto;
      color: #fff;
      border-radius: 0 0 12px 12px;
    }
    
    .flight-banner-section .flight-autocomplete-option {
      padding: 8px 18px;
      cursor: pointer;
    }
    
    .flight-banner-section .flight-autocomplete-option.active, 
    .flight-banner-section .flight-autocomplete-option:hover {
      background: #232323;
      color: #FFD235;
    }
    
    .flight-banner-section .flight-autocomplete-dropdown {
      min-width: 330px; 
      width: max(330px, 100%);
      left: 0;
      right: auto;
    }

    /* PERFECT RADIO BUTTON ALIGNMENT FIX */
    .flight-banner-section .radio-container {
      display: flex;
      gap: 1.5rem;
      align-items: center;
      flex-wrap: wrap;
      margin-bottom: 1rem;
    }

    .flight-banner-section .form-check {
      display: flex;
      align-items: center;
      margin-bottom: 0;
      padding-left: 0;
      gap: 0.5rem;
    }

    .flight-banner-section .form-check-input {
      margin: 0 !important;
      vertical-align: middle !important;
      position: static !important;
      top: auto !important;
      width: 1em;
      height: 1em;
      flex-shrink: 0;
    }

    .flight-banner-section .form-check-label {
      color: white;
      font-weight: 500;
      font-size: 15px;
      margin-bottom: 0 !important;
      line-height: 1.4;
      cursor: pointer;
      display: flex;
      align-items: center;
    }

    /* ENHANCED MOBILE RESPONSIVE MODAL/OVERLAY */
    .flight-banner-section #flight-dialog-backdrop {
      position: fixed;
      inset: 0;
      z-index: 3050;
      background: rgba(0,0,0,0.95) !important;
      display: none;
      justify-content: center;
      align-items: flex-start;
      padding: 0;
      overflow-y: auto;
    }
    
    .flight-banner-section #flight-dialog-modal {
      background: #181b22;
      color: #fff;
      border-radius: 12px;
      width: 95%;
      max-width: 1040px;
      min-width: 320px;
      max-height: none;
      box-shadow: 0 20px 60px rgba(0,0,0,0.8), 0 8px 30px rgba(255,210,63,0.1);
      position: relative;
      padding: 0;
      animation: modalSlideIn 0.3s ease-out;
      display: flex;
      flex-direction: column;
      margin: 20px auto;
      min-height: auto;
    }

    @keyframes modalSlideIn {
      from {
        transform: translateY(-30px) scale(0.95);
        opacity: 0;
      }
      to {
        transform: translateY(0) scale(1);
        opacity: 1;
      }
    }
    
    .flight-banner-section .flight-close-dialog {
      position: absolute;
      top: 15px;
      right: 20px;
      background: rgba(255,210,63,0.1);
      border: 2px solid #FFD235;
      color: #FFD235;
      font-size: 24px;
      font-weight: 700;
      line-height: 1;
      cursor: pointer;
      z-index: 12;
      padding: 8px 12px;
      border-radius: 50%;
      width: 45px;
      height: 45px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
    }
    
    .flight-banner-section .flight-close-dialog:hover {
      background: rgba(255,210,63,0.2);
      color: #fff;
      transform: rotate(90deg);
    }
    
    .flight-banner-section .flight-dialog-list-area {
      padding: 60px 25px 25px 25px;
      width: 100%;
      overflow-y: visible;
    }
    
    .flight-banner-section .flight-overlay-header {
      color: #FFD235;
      font-weight: 800;
      font-size: 1.8rem;
      margin-bottom: 25px;
      text-align: center;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    
    /* COMPLETELY REDESIGNED FLIGHT CARDS FOR MOBILE */
    .flight-banner-section .flight-result-card {
      width: 100%;
      max-width: 100%;
      margin: 0 auto 20px auto;
      display: flex;
      flex-direction: column;
      border-radius: 15px;
      background: linear-gradient(135deg, #1e2126, #252830);
      box-shadow: 0 8px 25px rgba(0,0,0,0.4);
      overflow: hidden;
      border: 2px solid #333;
      transition: all 0.3s ease;
    }

    .flight-banner-section .flight-result-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 35px rgba(0,0,0,0.5);
      border-color: rgba(255,210,63,0.4);
    }
    
    /* MOBILE OPTIMIZED CARD HEADER */
    .flight-banner-section .flight-card-header {
      background: linear-gradient(135deg, #2a2d35, #1f2228);
      padding: 15px 20px;
      border-bottom: 1px solid rgba(255,210,63,0.2);
      position: relative;
    }

    .flight-banner-section .flight-card-header .flight-non-refundable-tag {
      position: absolute;
      top: 0;
      left: 0;
      background: linear-gradient(135deg, #ff4757, #ff3742);
      color: white;
      font-size: 10px;
      font-weight: 700;
      padding: 4px 12px;
      border-radius: 0 0 8px 0;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .flight-banner-section .flight-airline-info {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 10px;
    }

    .flight-banner-section .flight-airline-details {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .flight-banner-section .flight-airline-code {
      background: rgba(255,210,63,0.1);
      color: #FFD235;
      font-size: 16px;
      font-weight: 900;
      padding: 6px 10px;
      border-radius: 6px;
      min-width: 50px;
      text-align: center;
    }

    .flight-banner-section .flight-airline-text {
      display: flex;
      flex-direction: column;
    }

    .flight-banner-section .flight-airline-name {
      font-size: 14px;
      color: #e0e0e0;
      font-weight: 600;
      margin: 0;
    }

    .flight-banner-section .flight-flight-num {
      font-size: 12px;
      color: #aaa;
      font-weight: 500;
      margin: 0;
    }

    .flight-banner-section .flight-card-price-mobile {
      text-align: right;
    }

    .flight-banner-section .flight-card-price {
      color: #FFD235;
      font-size: 1.5rem;
      font-weight: 900;
      margin: 0;
    }

    .flight-banner-section .flight-price-label {
      font-size: 11px;
      color: #888;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    /* MOBILE OPTIMIZED FLIGHT TIMELINE */
    .flight-banner-section .flight-card-body {
      padding: 20px;
    }

    .flight-banner-section .flight-timeline-mobile {
      display: flex;
      flex-direction: column;
      gap: 15px;
      margin-bottom: 20px;
    }

    .flight-banner-section .flight-route-info {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: rgba(255,210,63,0.05);
      padding: 15px;
      border-radius: 10px;
      border: 1px solid rgba(255,210,63,0.1);
    }

    .flight-banner-section .flight-location {
      display: flex;
      flex-direction: column;
      align-items: center;
      flex: 1;
    }

    .flight-banner-section .flight-time {
      font-size: 18px;
      font-weight: 700;
      color: #FFD235;
      margin-bottom: 2px;
    }

    .flight-banner-section .flight-city {
      font-size: 13px;
      color: #ccc;
      font-weight: 600;
      text-align: center;
      max-width: 80px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .flight-banner-section .flight-route-arrow {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin: 0 10px;
    }

    .flight-banner-section .flight-plane-icon {
      font-size: 20px;
      color: #FFD235;
      margin-bottom: 4px;
    }

    .flight-banner-section .flight-duration-mobile {
      font-size: 11px;
      color: #888;
      background: rgba(255,255,255,0.05);
      padding: 2px 6px;
      border-radius: 4px;
      white-space: nowrap;
    }

    .flight-banner-section .flight-card-footer {
      padding: 15px 20px;
      background: rgba(0,0,0,0.2);
      border-top: 1px solid rgba(255,255,255,0.1);
    }

    .flight-banner-section .flight-book-btn {
      display: block;
      background: linear-gradient(135deg, #FFD235, #FFC41B) !important;
      color: #1a1d24 !important;
      font-weight: 800;
      font-size: 16px;
      border: none;
      border-radius: 25px;
      padding: 12px 20px;
      text-decoration: none;
      text-align: center;
      box-shadow: 0 4px 15px rgba(255,210,63,0.3);
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      width: 100%;
    }
    
    .flight-banner-section .flight-book-btn:hover {
      background: linear-gradient(135deg, #FFC41B, #FFB800) !important;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(255,210,63,0.4);
    }

    .flight-banner-section .flight-book-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }
    
    .flight-banner-section #flight-dialog-modal::-webkit-scrollbar {
      width: 8px;
      background: transparent;
    }
    
    .flight-banner-section #flight-dialog-modal::-webkit-scrollbar-thumb {
      background: rgba(255,210,63,0.3);
      border-radius: 4px;
    }

    /* Updated dropdown styles for opacity and colors */
    .flight-autocomplete-dropdown {
      background: #191c23 !important;
      color: #ffffff !important;
      border: 1px solid #222 !important;
      box-shadow: 0 2px 12px 1px #0006 !important;
      border-radius: 0 0 12px 12px !important;
      max-height: 230px !important;
      overflow-y: auto !important;
      min-width: 330px !important;
      opacity: 1 !important;
      filter: none !important;
      position: absolute !important;
      z-index: 100000 !important;
    }
    
    .flight-autocomplete-option {
      padding: 8px 18px;
      cursor: pointer;
      color: #ffffff;
      background-color: transparent;
    }
    
    .flight-autocomplete-option.active,
    .flight-autocomplete-option:hover {
      background-color: #232323;
      color: #FFD235;
    }

    /* RESPONSIVE BREAKPOINTS */
    @media (max-width: 1200px) {
      .flight-banner-section #flight-dialog-modal {
        width: 92%;
      }
    }
    
    @media (max-width: 768px) {
      .flight-banner-section #flight-dialog-backdrop {
        align-items: flex-start;
        padding: 10px;
      }
      
      .flight-banner-section #flight-dialog-modal {
        width: 100%;
        margin: 10px auto;
        border-radius: 15px;
      }
      
      .flight-banner-section .flight-overlay-header {
        font-size: 1.5rem;
        margin-bottom: 20px;
      }
      
      .flight-banner-section .flight-dialog-list-area {
        padding: 55px 20px 20px 20px;
      }
      
      .flight-banner-section .flight-close-dialog {
        font-size: 20px;
        width: 40px;
        height: 40px;
        top: 12px;
        right: 15px;
      }
    }
    
    @media (max-width: 576px) {
      .flight-banner-section .radio-container {
        gap: 1rem;
        justify-content: flex-start;
      }
      
      .flight-banner-section .form-check-label {
        font-size: 14px;
      }
      
      .flight-banner-section #flight-dialog-backdrop {
        padding: 5px;
      }
      
      .flight-banner-section #flight-dialog-modal {
        width: calc(100% - 10px);
        margin: 5px auto;
        border-radius: 12px;
      }
      
      .flight-banner-section .flight-dialog-list-area {
        padding: 50px 15px 15px 15px;
      }
      
      .flight-banner-section .flight-overlay-header {
        font-size: 1.3rem;
        margin-bottom: 15px;
      }

      .flight-banner-section .flight-card-header {
        padding: 12px 15px;
      }

      .flight-banner-section .flight-card-body {
        padding: 15px;
      }

      .flight-banner-section .flight-card-footer {
        padding: 12px 15px;
      }

      .flight-banner-section .flight-route-info {
        padding: 12px;
      }

      .flight-banner-section .flight-time {
        font-size: 16px;
      }

      .flight-banner-section .flight-city {
        font-size: 12px;
        max-width: 70px;
      }
    }
    
    @media (max-width: 400px) {
      .flight-banner-section .radio-container {
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-start;
      }
      
      .flight-banner-section .flight-search-box {
        padding: 20px 15px;
      }

      .flight-banner-section #flight-dialog-backdrop {
        padding: 0;
      }
      
      .flight-banner-section #flight-dialog-modal {
        width: 100%;
        margin: 0;
        border-radius: 0;
        min-height: 100vh;
      }

      .flight-banner-section .flight-airline-info {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
      }

      .flight-banner-section .flight-card-price-mobile {
        text-align: left;
      }
    }
  </style>

  <div class="row">
    <div class="col-12">
      <div class="flight-search-box">
        <!-- FIXED RADIO BUTTON STRUCTURE WITH PERFECT ALIGNMENT -->
        <div class="radio-container">
          <div class="form-check">
            <input class="form-check-input" type="radio" name="flight-tripType" id="flight-return" value="false" checked>
            <label class="form-check-label" for="flight-return">Return</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="flight-tripType" id="flight-oneWay" value="true">
            <label class="form-check-label" for="flight-oneWay">One way</label>
          </div>
        </div>

        <div class="row g-2 mb-1 align-items-end">
          <div class="col-12 col-md-3 flight-form-group-autocomplete">
            <label>From</label>
            <input type="text" class="form-control" id="flight-origin-input" autocomplete="off" placeholder="Type city or code (e.g. Delhi or DEL)">
          </div>
          <div class="col-12 col-md-3 flight-form-group-autocomplete">
            <label>To</label>
            <input type="text" class="form-control" id="flight-destination-input" autocomplete="off" placeholder="Type city or code (e.g. Paris or CDG)">
          </div>
          <div class="col-6 col-md-2">
            <label>Depart</label>
            <input type="month" class="form-control" id="flight-departure_at" value="{{ date('Y-m') }}">
          </div>
          <div class="col-6 col-md-2" id="flight-return-date-box">
            <label>Return</label>
            <input type="month" class="form-control" id="flight-return_at" value="{{ date('Y-m') }}">
          </div>
          <div class="col-12 col-md-2 d-flex align-items-end">
            <button class="flight-btn-search w-100" id="flight-searchBtn" type="button" onclick="flightPerformSearch()">Search</button>
          </div>
        </div>

        <div class="row" style="margin:0">
          <div class="col-12 col-md-3 form-check">
            <input class="form-check-input" type="checkbox" id="flight-directFlights">
            <label class="form-check-label" for="flight-directFlights">Direct flights only</label>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- FULLY RESPONSIVE MODAL -->
  <div id="flight-dialog-backdrop">
    <div id="flight-dialog-modal" tabindex="0">
      <button class="flight-close-dialog" onclick="flightCloseDialog()" aria-label="Close">×</button>
      <div class="flight-dialog-list-area" id="flight-dialog-content"></div>
    </div>
  </div>

  <script>
    // Enhanced autocomplete functionality with better positioning
    function flightSetupAutocomplete({inputId}) {
      const input = document.getElementById(inputId);

      // Create dropdown div dynamically and append to body
      let dropdown = document.createElement('div');
      dropdown.className = 'flight-autocomplete-dropdown';
      dropdown.style.display = 'none';
      dropdown.style.position = 'absolute';
      dropdown.style.zIndex = '100000';
      document.body.appendChild(dropdown);

      let options = [];
      let selectedIndex = -1;

      function updateDropdownPosition() {
        const rect = input.getBoundingClientRect();
        const viewportHeight = window.innerHeight;
        const dropdownHeight = 250;
        const spaceBelow = viewportHeight - rect.bottom;
        const spaceAbove = rect.top;
        
        if (spaceBelow >= dropdownHeight || spaceBelow >= spaceAbove) {
          dropdown.style.top = (rect.bottom + window.scrollY + 2) + 'px';
          dropdown.style.borderRadius = '0 0 12px 12px';
        } else {
          dropdown.style.top = (rect.top + window.scrollY - Math.min(dropdownHeight, spaceAbove)) + 'px';
          dropdown.style.borderRadius = '12px 12px 0 0';
        }
        
        dropdown.style.left = (rect.left + window.scrollX) + 'px';
        dropdown.style.width = Math.max(300, rect.width) + 'px';
      }

      input.addEventListener('input', function() {
        const term = input.value.trim();
        input.dataset.iata = '';
        input.dataset.name = '';
        
        if (term.length < 2) {
          dropdown.style.display = 'none';
          dropdown.innerHTML = '';
          return;
        }
        
        fetch(`https://autocomplete.travelpayouts.com/places2?locale=en&types[]=city&term=${encodeURIComponent(term)}`)
          .then(res => res.json())
          .then(data => {
            options = data;
            dropdown.innerHTML = '';
            selectedIndex = -1;
            
            if (options.length === 0) {
              dropdown.style.display = 'none';
              return;
            }
            
            options.forEach(place => {
              const display = `${place.name} (${place.code}), ${place.country_name}`;
              const div = document.createElement('div');
              div.className = 'flight-autocomplete-option';
              div.textContent = display;
              div.onmousedown = (e) => {
                e.preventDefault();
                input.value = display;
                input.dataset.iata = place.code;
                input.dataset.name = display;
                dropdown.style.display = 'none';
                dropdown.innerHTML = '';
              };
              dropdown.appendChild(div);
            });
            
            updateDropdownPosition();
            dropdown.style.display = 'block';
          })
          .catch(() => {
            dropdown.style.display = 'none';
            dropdown.innerHTML = '';
          });
      });

      input.addEventListener('keydown', function(e) {
        const items = dropdown.querySelectorAll('.flight-autocomplete-option');
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
          e.preventDefault();
          selectedIndex = (selectedIndex + 1) % items.length;
          updateActive();
        } else if (e.key === 'ArrowUp') {
          e.preventDefault();
          selectedIndex = (selectedIndex - 1 + items.length) % items.length;
          updateActive();
        } else if (e.key === 'Enter') {
          if (selectedIndex > -1) {
            e.preventDefault();
            items[selectedIndex].click();
          }
        }
      });

      function updateActive() {
        const items = dropdown.querySelectorAll('.flight-autocomplete-option');
        items.forEach(item => item.classList.remove('active'));
        if (selectedIndex > -1 && items[selectedIndex]) {
          items[selectedIndex].classList.add('active');
          items[selectedIndex].scrollIntoView({block: 'nearest'});
        }
      }

      document.addEventListener('mousedown', e => {
        if (!dropdown.contains(e.target) && e.target !== input) {
          dropdown.style.display = 'none';
        }
      });

      input.addEventListener('blur', () => {
        setTimeout(() => {
          dropdown.style.display = 'none';
          if (!/\([A-Z]{3}\)/.test(input.value)) {
            input.dataset.iata = '';
            input.dataset.name = '';
          }
        }, 150);
      });

      const updatePosition = () => {
        if (dropdown.style.display === 'block') {
          updateDropdownPosition();
        }
      };
      
      window.addEventListener('scroll', updatePosition, { passive: true });
      window.addEventListener('resize', updatePosition, { passive: true });
    }

    // Initialize both autocomplete inputs
    flightSetupAutocomplete({inputId: 'flight-origin-input'});
    flightSetupAutocomplete({inputId: 'flight-destination-input'});

    // Return date visibility toggle
    function flightUpdateReturnBox() {
      const tripType = document.querySelector('input[name="flight-tripType"]:checked').value;
      const box = document.getElementById('flight-return-date-box');
      const input = box.querySelector('input');
      
      if (tripType === "true") {
        box.style.opacity = '0.5';
        box.style.pointerEvents = 'none';
        input.disabled = true;
        box.setAttribute('aria-hidden', 'true');
      } else {
        box.style.opacity = '1';
        box.style.pointerEvents = 'auto';
        input.disabled = false;
        box.setAttribute('aria-hidden', 'false');
      }
    }

    document.querySelectorAll('input[name="flight-tripType"]').forEach(radio => {
      radio.addEventListener('change', flightUpdateReturnBox);
    });
    flightUpdateReturnBox();

    // Search button loading state
    function flightSetSearchButtonLoading(loading) {
      const btn = document.getElementById('flight-searchBtn');
      btn.disabled = !!loading;
      
      if (loading) {
        btn.innerHTML = `
          <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          Searching...
        `;
      } else {
        btn.innerHTML = 'Search';
      }
    }

    function flightPerformSearch() {
      flightSetSearchButtonLoading(true);
      const one_way = document.querySelector('input[name="flight-tripType"]:checked').value;
      let origin = document.getElementById('flight-origin-input').dataset.iata || '',
          originName = document.getElementById('flight-origin-input').dataset.name || '',
          destination = document.getElementById('flight-destination-input').dataset.iata || '',
          destinationName = document.getElementById('flight-destination-input').dataset.name || '';
      
      if (!origin || !destination) {
        flightSetSearchButtonLoading(false);
        alert('Please select both origin and destination from the dropdown list.');
        return;
      }
      
      const departure_at = document.getElementById('flight-departure_at').value,
            direct = document.getElementById('flight-directFlights').checked ? 'true' : 'false',
            token = "cf2c7d9387d4f197f23df39a0c5c38c5";
      
      let params = {
        origin,
        destination,
        departure_at,
        unique: 'false',
        sorting: 'price',
        direct,
        cy: 'usd',
        limit: 30,
        page: 1,
        one_way,
        token
      };
      
      if (one_way === "false") {
        const return_at = document.getElementById('flight-return_at').value;
        if (return_at) params.return_at = return_at;
      }
      
      const query = new URLSearchParams(params).toString();
      const proxyURL = `/proxy-prices?${query}`;
      
      fetch(proxyURL)
        .then(response => response.json())
        .then(data => {
          flightSetSearchButtonLoading(false);
          flightShowDialogWithResults(data.data || data, null, originName, destinationName);
        })
        .catch(() => {
          flightSetSearchButtonLoading(false);
          flightShowDialogWithResults([], 'Error loading flight results. Please try again.', originName, destinationName);
        });
    }

    function flightShowDialogWithResults(results, errorText, originFull, destinationFull) {
      let html = '';
      
      if (errorText) {
        html = `
          <div class="flight-overlay-header">Flight Search Results</div>
          <div class="text-center text-white p-4">
            <div class="mb-3" style="font-size: 2rem;">⚠️</div>
            <h5>${errorText}</h5>
          </div>
        `;
      } else if (!results || results.length === 0) {
        html = `
          <div class="flight-overlay-header">Flight Search Results</div>
          <div class="text-center text-white p-4">
            <div class="mb-3" style="font-size: 2rem;">✈️</div>
            <h5>No flights found for your search criteria.</h5>
            <p class="text-muted">Try adjusting your dates or destinations.</p>
          </div>
        `;
      } else {
        html = `<div class="flight-overlay-header">Flight Search Results</div>`;
        
        results.slice(0, 10).forEach(flight => {
          const depTime = flight.departure_time || (flight.departure_at ? (flight.departure_at.split('T')[1] || '').substr(0, 5) : '');
          const arrTime = flight.arrival_time || (flight.arrival_at ? (flight.arrival_at.split('T')[1] || '').substr(0, 5) : '');
          
          html += `
            <div class="flight-result-card">
              <!-- MOBILE OPTIMIZED CARD HEADER -->
              <div class="flight-card-header">
                <div class="flight-non-refundable-tag">NON REFUNDABLE</div>
                <div class="flight-airline-info">
                  <div class="flight-airline-details">
                    <div class="flight-airline-code">${flight.airline_code ? flight.airline_code : (flight.airline || '').toUpperCase()}</div>
                    <div class="flight-airline-text">
                      <div class="flight-airline-name">${flight.airline_name ? `<div class="flight-airline-name">${flight.airline_name}</div>` : ''}</div>
                      <div class="flight-flight-num">Flight ${flight.flight_number || "N/A"}</div>
                    </div>
                  </div>
                  <div class="flight-card-price-mobile">
                    <div class="flight-price-label">Total Price</div>
                    <div class="flight-card-price">${flight.value ? ('$' + flight.value) : flight.price ? ('$' + flight.price) : 'N/A'}</div>
                  </div>
                </div>
              </div>

              <!-- MOBILE OPTIMIZED CARD BODY -->
              <div class="flight-card-body">
                <div class="flight-route-info">
                  <div class="flight-location">
                    <div class="flight-time">${depTime || '--:--'}</div>
                    <div class="flight-city" title="${originFull || flight.origin}">${originFull ? originFull.split(',')[0] : flight.origin}</div>
                  </div>
                  
                  <div class="flight-route-arrow">
                    <div class="flight-plane-icon">✈</div>
                    <div class="flight-duration-mobile">${flight.duration || "N/A"}</div>
                  </div>
                  
                  <div class="flight-location">
                    <div class="flight-time">${arrTime || '--:--'}</div>
                    <div class="flight-city" title="${destinationFull || flight.destination}">${destinationFull ? destinationFull.split(',')[0] : flight.destination}</div>
                  </div>
                </div>
              </div>

              <!-- MOBILE OPTIMIZED CARD FOOTER -->
              <div class="flight-card-footer">
                <a href="https://www.aviasales.ge/${flight.link ? flight.link : ''}" 
                   target="_blank" 
                   class="flight-book-btn"
                   ${!flight.link ? 'style="opacity:0.6;pointer-events:none;"' : ''}>
                  ${flight.link ? 'Book This Flight' : 'Not Available'}
                </a>
              </div>
            </div>
          `;
        });
      }
      
      document.getElementById('flight-dialog-content').innerHTML = html;
      document.getElementById('flight-dialog-backdrop').style.display = 'flex';
      setTimeout(() => {
        document.getElementById('flight-dialog-modal').focus();
      }, 100);
    }

    function flightCloseDialog() {
      document.getElementById('flight-dialog-content').innerHTML = "";
      document.getElementById('flight-dialog-backdrop').style.display = 'none';
    }

    // Enhanced keyboard navigation
    document.addEventListener('keydown', function(e) {
      if (document.getElementById('flight-dialog-backdrop').style.display === 'flex') {
        if (e.key === "Escape" || e.key === "Esc") {
          flightCloseDialog();
        }
      }
    });

    // Close modal when clicking backdrop
    document.getElementById('flight-dialog-backdrop').addEventListener('click', function(e) {
      if (e.target === this) {
        flightCloseDialog();
      }
    });
  </script>
</div>
