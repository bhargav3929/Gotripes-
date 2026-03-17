@include('header')

<style>
  html, body { background: #000 !important; }
  .container {
    box-sizing: border-box;
    padding: 0 15px;
  }

  .image-overlay {
    position: relative;
    overflow: hidden;
    height: 100vh;
  }
  .image-overlay img {
    object-fit: cover;
    display: block;
    width: 100%;
    height: 100%;
  }

  .overlay {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    justify-content: flex-start;
    align-items: center;
    flex-direction: column;
    padding: 120px 20px 20px 20px;
  }

  @media (max-width: 991px) {
    .overlay {
      padding: 100px 15px 20px 15px;
    }
  }

  @media (max-width: 575px) {
    .overlay {
      padding: 80px 10px 20px 10px;
    }
  }

  .overlay-content {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin-top: 190px;
    text-align: center;
    height: 100%;
    background: rgba(0,0,0,0.7);
    border-radius: 14px;
    padding: 20px;
  }

  .tagline {
    font-family: 'Satisfy', cursive;
    color: #FFD23F;
    font-size: 28px;
    font-weight: 400;
    text-shadow: 0 2px 8px rgba(0,0,0,0.85), 0 0px 2px #000;
  }

  .heading {
    color: white;
    font-size: 30px;
    font-weight: 600;
    text-shadow: 0 2px 8px rgba(0,0,0,0.85), 0 0px 2px #000;
  }

  .cta-button {
    color: white;
    font-size: 25px;
    font-weight: 600;
    border-radius: 10px;
    background: rgb(200, 48, 0, 0.7);
    padding: 8px 24px;
    min-width: 240px;
    text-align: center;
    text-shadow: 0 2px 8px rgba(0,0,0,0.85), 0 0px 2px #000;
  }

  /* --- Country Card Grid --- */
  .country-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 15px;
    padding: 20px 0;
    max-height: 70vh;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #FFD23F #111;
  }
  .country-grid::-webkit-scrollbar {
    width: 6px;
  }
  .country-grid::-webkit-scrollbar-thumb {
    background-color: #FFD23F;
    border-radius: 10px;
  }

  .country-card {
    background: rgba(15, 15, 15, 0.95);
    border: 1px solid rgba(255, 210, 63, 0.1);
    border-radius: 12px;
    padding: 15px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    z-index: 1;
  }

  .country-card:hover {
    transform: translateY(-5px) scale(1.02);
    border-color: #FFD23F;
    box-shadow: 0 10px 30px rgba(0,0,0,0.8);
    background: rgba(25, 25, 25, 0.98);
    z-index: 10;
  }

  .country-card img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 12px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    pointer-events: none; /* Prevents image from blocking card hover/click */
  }

  .country-card .country-name {
    color: #FFD23F;
    font-weight: 700;
    font-size: 16px;
    margin-bottom: 8px;
    display: block;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  /* --- Dropdown Details --- */
  .country-details {
    cursor: pointer;
    margin-top: 10px;
    width: 100%;
  }

  .country-details summary {
    list-style: none;
    font-size: 11px;
    color: #FFD23F;
    text-transform: uppercase;
    letter-spacing: 1px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.2s;
    padding: 5px 0;
    border-top: 1px solid rgba(255, 210, 63, 0.1);
    outline: none;
  }

  .country-details summary::-webkit-details-marker {
    display: none;
  }

  .country-details summary:hover {
    opacity: 0.8;
  }

  .country-details summary::after {
    content: '\F282'; /* Bootstrap Icon Chevron Down */
    font-family: 'bootstrap-icons';
    font-size: 10px;
    transition: transform 0.3s;
  }

  .country-details[open] summary::after {
    transform: rotate(180deg);
  }

  .details-content {
    margin-top: 10px;
    font-size: 13px;
    color: #eee;
    line-height: 1.6;
    padding-top: 10px;
    border-top: 1px solid rgba(255, 210, 63, 0.1);
    animation: slideDown 0.3s ease-out;
  }

  @keyframes slideDown {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
  }

  #grid-loader {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 200px;
    color: #FFD23F;
    font-size: 18px;
    letter-spacing: 2px;
    text-transform: uppercase;
  }

  @media (max-width: 1024px) {
    .country-grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  @media (max-width: 768px) {
    .overlay-content { margin-top: 100px; }
    .country-grid {
      grid-template-columns: repeat(2, 1fr);
      max-height: 55vh;
    }
    .heading { font-size: 24px !important; text-align: center; }
  }

  @media (max-width: 480px) {
    .country-grid {
      grid-template-columns: 1fr; /* Single column for better readability on small phones */
      gap: 20px;
    }
    .country-card {
        padding: 20px;
    }
    .country-card img {
        height: 180px; /* Larger image for single column */
    }
  }

  /* --- Refined Premium Search Bar --- */
  .search-container {
    margin-bottom: 0; /* Removed margin as it's now in a row */
    display: flex;
    justify-content: flex-end; /* Align right */
    width: 100%;
    animation: fadeInRight 0.8s ease-out;
  }

  .search-wrapper {
    position: relative;
    max-width: 400px;
    width: 100%;
    background: linear-gradient(135deg, rgba(212, 175, 55, 0.2) 0%, rgba(0, 0, 0, 0.8) 50%, rgba(212, 175, 55, 0.2) 100%);
    padding: 1px;
    border-radius: 50px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
  }

  @media (max-width: 991px) {
    .search-container {
      justify-content: center;
      margin-top: 15px;
    }
    .search-wrapper {
      max-width: 100%;
    }
  }

  .search-input {
    width: 100%;
    padding: 12px 25px 12px 55px;
    background: #0a0a0a;
    backdrop-filter: blur(15px);
    border: none;
    border-radius: 50px;
    color: #fff;
    font-size: 15px;
    font-family: 'Outfit', sans-serif;
    outline: none;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    letter-spacing: 0.5px;
  }

  .search-input::placeholder {
    color: rgba(255, 255, 255, 0.4);
    font-weight: 300;
  }

  .search-input:focus {
    background: rgba(0, 0, 0, 0.95);
    box-shadow: 0 0 25px rgba(212, 175, 55, 0.4);
  }

  .search-icon {
    position: absolute;
    left: 22px;
    top: 50%;
    transform: translateY(-50%);
    color: #FFD700;
    font-size: 20px;
    transition: all 0.3s ease;
    z-index: 2;
  }

  .search-input:focus + .search-icon {
    transform: translateY(-50%) scale(1.1);
    text-shadow: 0 0 10px rgba(255, 215, 0, 0.8);
  }

  @keyframes fadeInRight {
    from { opacity: 0; transform: translateX(20px); }
    to { opacity: 1; transform: translateX(0); }
  }

  .no-results {
    color: #888;
    text-align: center;
    padding: 50px;
    grid-column: 1 / -1;
    display: none;
    font-size: 18px;
    letter-spacing: 1px;
  }
</style>

<!-- Image + Grid Overlay -->
<div class="image-overlay">
  <img src="assets/index_files/533419533.jpg" alt="Banner" />
  <div class="overlay">
    <div class="container" style="max-width: 1400px;">
        <!-- Header Row: Title and Search -->
        <div class="row align-items-center mb-4">
            <div class="col-lg-7 text-center text-lg-start">
                <h1 class="heading" style="margin: 0; letter-spacing: 2px; font-size: 36px;">
                    EXPLORE <span style="color: #FFD23F;">195+ COUNTRIES</span>
                </h1>
            </div>
            <div class="col-lg-5">
                <div class="search-container">
                    <div class="search-wrapper">
                        <input type="text" id="countrySearch" class="search-input" placeholder="Search for a country...">
                        <i class="bi bi-search search-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="grid-loader">
            <div class="spinner-border" role="status" style="margin-right: 15px;"></div>
            Loading Destinations...
        </div>

        <div id="country-grid" class="country-grid" style="display: none;">
            <!-- Fetched countries will be injected here -->
        </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const grid = document.getElementById('country-grid');
    const loader = document.getElementById('grid-loader');

    // Local data for original countries to keep their specific info
    const specialInfo = {
        "Bahrain": { best: "Nov–Mar", duration: "4–5 days", cost: "$72–$299", airports: "BAH", airline: "Gulf Air (GF)" },
        "Egypt": { best: "Oct–Apr", duration: "7–10 days", cost: "$271", airports: "CAI, HRG, SSH", airline: "Egypt Air (MS)" },
        "Oman": { best: "Oct–Apr", duration: "5–7 days", cost: "$4,224", airports: "MCT", airline: "Oman Air (WY)" },
        "Saudi Arabia": { best: "Oct–Mar", duration: "5–7 days", cost: "$100–$200", airports: "JED, RUH", airline: "Saudia, Flynas, Flyadeal" },
        "United Arab Emirates": { best: "Oct–Apr", duration: "5–7 days", cost: "$200–$250", airports: "DXB, AUH, SHJ", airline: "Emirates, Etihad, FlyDubai" },
        "South Africa": { best: "May–Sep (Safari), Nov–Feb (Coast)", duration: "10–14 days", cost: "$200–$250", airports: "JNB, CPT", airline: "SAA" }
    };

    fetch('https://restcountries.com/v3.1/all?fields=name,flags,capital,currencies,region,subregion')
      .then(res => res.json())
      .then(countries => {
        countries.sort((a, b) => a.name.common.localeCompare(b.name.common));

        let html = '';
        countries.forEach(country => {
          const commonName = country.name.common;
          const info = specialInfo[commonName] || { best: "Year-Round", duration: "5–7 days", cost: "$150–$300", airports: "International", airline: "Multiple" };
          
          const currencyKey = Object.keys(country.currencies || {})[0];
          const currencyName = currencyKey ? country.currencies[currencyKey].name : 'N/A';
          const currencySymbol = currencyKey ? (country.currencies[currencyKey].symbol || currencyKey) : '';
          const capital = country.capital ? country.capital[0] : 'N/A';

          html += `
            <div class="country-card">
              <img src="${country.flags.svg || country.flags.png}" alt="${commonName} Flag" loading="lazy">
              <span class="country-name">${commonName}</span>
              
              <details class="country-details">
                <summary>Detailed Info</summary>
                <div class="details-content">
                  <strong>Capitals:</strong> ${capital}<br>
                  <strong>Best time:</strong> ${info.best}<br>
                  <strong>Currency:</strong> ${currencyName} (${currencySymbol})<br>
                  <strong>Duration:</strong> ${info.duration}<br>
                  <strong>Cost/day:</strong> ${info.cost}<br>
                  <strong>Airports:</strong> ${info.airports}<br>
                  <strong>Airline:</strong> ${info.airline}
                </div>
              </details>
            </div>
          `;
        });

        grid.innerHTML = html;
        loader.style.display = 'none';
        grid.style.display = 'grid';

        // --- Accordion Logic ---
        grid.querySelectorAll('.country-details').forEach(details => {
            details.addEventListener('toggle', function() {
                if (this.open) {
                    grid.querySelectorAll('.country-details').forEach(otherDetails => {
                        if (otherDetails !== this) {
                            otherDetails.removeAttribute('open');
                        }
                    });
                }
            });
        });

        // --- Search Logic ---
        const searchInput = document.getElementById('countrySearch');
        const noResults = document.createElement('div');
        noResults.className = 'no-results';
        noResults.innerText = 'No countries found matching your search.';
        grid.appendChild(noResults);

        searchInput.addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            const cards = grid.querySelectorAll('.country-card');
            let found = false;

            cards.forEach(card => {
                const name = card.querySelector('.country-name').innerText.toLowerCase();
                if (name.includes(term)) {
                    card.style.display = 'block';
                    found = true;
                } else {
                    card.style.display = 'none';
                    // Close details if card is hidden
                    card.querySelector('.country-details').removeAttribute('open');
                }
            });

            noResults.style.display = found ? 'none' : 'block';
        });

        // Handle Enter keypress
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                // Find first visible card
                const firstVisibleCard = Array.from(grid.querySelectorAll('.country-card')).find(card => card.style.display !== 'none');
                
                if (firstVisibleCard) {
                    const details = firstVisibleCard.querySelector('.country-details');
                    // Toggle open
                    details.setAttribute('open', '');
                    // Scroll into view gently
                    firstVisibleCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
      })
      .catch(err => {
        console.error('Error fetching countries:', err);
        loader.innerHTML = 'Failed to load countries. Please refresh the page.';
      });
  });
</script>

@include('footer')
