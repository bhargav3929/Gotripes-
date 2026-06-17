@include('header')

@php
    $tenant = current_company();
    $hasPackages = isset($packages) && $packages->count() > 0;
    $comingSoon  = $comingSoon ?? collect();
    $featuredMap = $hasPackages
        ? $packages->keys()->mapWithKeys(fn($name) => [$name => route('tour-packages.country', \Illuminate\Support\Str::slug($name))])->toArray()
        : [];
    $featuredCountries   = array_keys($featuredMap);
    $comingSoonCountries = $comingSoon->toArray();

    // Build country list locally — no external API needed
    $localCountries = collect(\App\Support\CountryCodes::all())
        ->map(fn($c) => [
            'name'    => $c['name'],
            'flagUrl' => 'https://flagcdn.com/w320/' . strtolower($c['iso']) . '.png',
        ])
        ->values()
        ->toArray();
@endphp

<style>
  @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@200;300;400;500;600;700;800&display=swap');

  html, body { background: #000 !important; }
  .tp * { box-sizing: border-box; font-family: 'Outfit', sans-serif; }

  /* ── HERO overlay ── */
  .tp-hero {
    position: relative;
    height: 100vh;
    min-height: 560px;
    background: url('{{ asset('assets/index_files/533419533.jpg') }}') center/cover no-repeat;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    overflow: hidden;
  }
  .tp-hero-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(180deg, rgba(0,0,0,0.75) 0%, rgba(0,0,0,0.85) 100%);
  }
  .tp-hero-inner {
    position: relative; z-index: 2;
    width: 100%; max-width: 1400px;
    padding: 140px 20px 20px;
  }

  /* ── Title + search row ── */
  .tp-toprow {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 14px; margin-bottom: 22px;
  }
  .tp-hero-title {
    color: #fff; font-size: clamp(24px, 4vw, 40px); font-weight: 800;
    letter-spacing: 2px; text-transform: uppercase; margin: 0; line-height: 1.1;
  }
  .tp-hero-title span { color: #FFD23F; }

  .tp-search-wrap {
    position: relative; max-width: 380px; width: 100%;
    background: linear-gradient(135deg, rgba(212,175,55,.22), rgba(0,0,0,.8), rgba(212,175,55,.22));
    padding: 1px; border-radius: 50px;
  }
  .tp-search-input {
    width: 100%; padding: 11px 22px 11px 50px; background: #0a0a0a;
    border: none; border-radius: 50px; color: #fff; font-size: 14px;
    font-family: 'Outfit', sans-serif; outline: none; letter-spacing: .4px;
    transition: box-shadow .3s;
  }
  .tp-search-input::placeholder { color: rgba(255,255,255,.4); font-weight: 300; }
  .tp-search-input:focus { box-shadow: 0 0 22px rgba(212,175,55,.4); }
  .tp-search-icon {
    position: absolute; left: 18px; top: 50%; transform: translateY(-50%);
    color: #FFD700; font-size: 18px; pointer-events: none;
  }

  /* ── Legend strip ── */
  .tp-legend {
    display: flex; gap: 18px; flex-wrap: wrap; margin-bottom: 14px;
    font-size: 12px; color: #aaa; align-items: center;
  }
  .tp-legend-dot {
    display: inline-block; width: 10px; height: 10px; border-radius: 3px; margin-right: 5px;
  }
  .tp-legend-dot.feat   { background: #FFD700; }
  .tp-legend-dot.soon   { background: rgba(255,255,255,.25); }
  .tp-legend-dot.normal { background: rgba(255,210,63,.15); border: 1px solid rgba(255,210,63,.2); }

  /* ── Country grid ── */
  .tp-grid-loader {
    display: flex; justify-content: center; align-items: center; min-height: 200px;
    color: #FFD23F; font-size: 16px; letter-spacing: 2px; text-transform: uppercase; gap: 12px;
  }
  .tp-country-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 14px;
    max-height: 62vh;
    overflow-y: auto;
    scrollbar-width: thin; scrollbar-color: #FFD23F #111;
    display: none;
  }
  .tp-country-grid::-webkit-scrollbar { width: 6px; }
  .tp-country-grid::-webkit-scrollbar-thumb { background: #FFD23F; border-radius: 10px; }

  .tp-country-card {
    background: rgba(15,15,15,.95);
    border: 1px solid rgba(255,210,63,.1);
    border-radius: 12px; padding: 12px;
    transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
    position: relative; z-index: 1; cursor: default;
  }
  .tp-country-card:hover {
    transform: translateY(-4px) scale(1.02);
    border-color: rgba(255,215,0,.6);
    box-shadow: 0 10px 28px rgba(0,0,0,.85);
    z-index: 10;
  }
  .tp-country-card img {
    width: 100%; height: 110px; object-fit: cover; border-radius: 7px;
    margin-bottom: 10px; border: 1px solid rgba(255,255,255,.05); pointer-events: none;
    display: block;
  }
  .tp-country-name {
    color: #FFD23F; font-weight: 700; font-size: 14px; display: block;
    text-transform: uppercase; letter-spacing: .8px; margin-bottom: 6px;
  }

  /* Featured (has real packages) */
  .tp-country-card.is-featured {
    border-color: rgba(255,215,0,.45);
    background: linear-gradient(160deg, rgba(255,215,0,.07) 0%, rgba(15,15,15,.95) 60%);
  }
  .tp-feat-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 10px; font-weight: 700; letter-spacing: 1px;
    text-transform: uppercase; color: #000; background: #FFD700;
    border-radius: 999px; padding: 3px 9px; margin-bottom: 7px;
  }
  .tp-feat-btn {
    display: block; width: 100%; text-align: center;
    background: linear-gradient(135deg,#FFD700,#D4AF37); color: #000;
    font-size: 11px; font-weight: 700; letter-spacing: .5px;
    border-radius: 8px; padding: 7px 4px; text-decoration: none;
    transition: transform .14s ease, box-shadow .14s ease;
    margin-top: 8px;
  }
  .tp-feat-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(212,175,55,.35); color: #000; text-decoration: none; }

  /* Coming-soon */
  .tp-country-card.is-soon { opacity: .6; }
  .tp-soon-badge {
    display: inline-block; font-size: 10px; font-weight: 600; letter-spacing: .8px;
    text-transform: uppercase; color: #aaa; background: rgba(255,255,255,.09);
    border: 1px solid rgba(255,255,255,.12); border-radius: 999px;
    padding: 2px 8px; margin-bottom: 4px;
  }

  /* Dropdown details (normal countries) */
  .tp-details { cursor: pointer; margin-top: 6px; width: 100%; }
  .tp-details summary {
    list-style: none; font-size: 11px; color: #FFD23F; text-transform: uppercase;
    letter-spacing: 1px; display: flex; align-items: center; justify-content: space-between;
    padding: 4px 0; border-top: 1px solid rgba(255,210,63,.1); outline: none;
    transition: opacity .2s;
  }
  .tp-details summary::-webkit-details-marker { display: none; }
  .tp-details summary:hover { opacity: .75; }
  .tp-details summary::after {
    content: '\F282'; font-family: 'bootstrap-icons'; font-size: 10px; transition: transform .3s;
  }
  .tp-details[open] summary::after { transform: rotate(180deg); }
  .tp-details-content {
    margin-top: 8px; font-size: 12px; color: #eee; line-height: 1.6; padding-top: 8px;
    border-top: 1px solid rgba(255,210,63,.1);
    animation: tpSlide .25s ease-out;
  }
  @keyframes tpSlide { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }

  .tp-no-results {
    color: #888; text-align: center; padding: 40px; grid-column: 1/-1;
    font-size: 16px; letter-spacing: 1px; display: none;
  }


  /* ── Responsive ── */
  @media (max-width: 1200px) { .tp-country-grid { grid-template-columns: repeat(5, 1fr); } }
  @media (max-width: 991px)  { .tp-country-grid { grid-template-columns: repeat(4, 1fr); } }
  @media (max-width: 768px)  {
    .tp-hero-inner { padding-top: 110px; }
    .tp-country-grid { grid-template-columns: repeat(3, 1fr); gap: 10px; max-height: 54vh; }
  }
  @media (max-width: 575px)  {
    .tp-country-grid { grid-template-columns: repeat(3, 1fr); gap: 8px; }
    .tp-country-card img { height: 65px; }
    .tp-country-name { font-size: 10px; }
    .tp-search-wrap { max-width: 100%; }
  }
</style>

<div class="tp">

  {{-- ══════════════ HERO + 180+ COUNTRY GRID ══════════════ --}}
  <section class="tp-hero">
    <div class="tp-hero-overlay"></div>
    <div class="tp-hero-inner">

      <div class="tp-toprow">
        <h1 class="tp-hero-title">Explore <span>195+ Countries</span></h1>
        <div class="tp-search-wrap">
          <input id="tpSearch" type="text" class="tp-search-input" placeholder="Search for a country…">
          <i class="bi bi-search tp-search-icon"></i>
        </div>
      </div>

      <div class="tp-legend">
        @if($hasPackages)
        <span><span class="tp-legend-dot feat"></span> Has packages — tap to view</span>
        @endif
        @if($comingSoon->count())
        <span><span class="tp-legend-dot soon"></span> Coming soon</span>
        @endif
        <span><span class="tp-legend-dot normal"></span> Explore destination</span>
      </div>

      <div id="tpLoader" class="tp-grid-loader">
        <div class="spinner-border spinner-border-sm" role="status"></div>
        Loading destinations…
      </div>
      <div id="tpGrid" class="tp-country-grid"></div>
      <p id="tpNoResults" class="tp-no-results" style="display:none;">No countries found.</p>

    </div>
  </section>


</div><!-- /.tp -->

<script>
(function () {
  const FEAT_MAP  = @json($featuredMap);          // { "Canada": "/tour-packages/canada", ... }
  const FEAT_NAMES = Object.keys(FEAT_MAP);
  const COMING    = @json($comingSoonCountries);  // e.g. ["Jordan","Turkey"]

  const specialInfo = {
    "Bahrain":              { best:"Nov–Mar",    dur:"4–5 days",   cost:"$72–$299",    airports:"BAH",           airline:"Gulf Air (GF)" },
    "Egypt":                { best:"Oct–Apr",    dur:"7–10 days",  cost:"$271",        airports:"CAI,HRG,SSH",   airline:"Egypt Air (MS)" },
    "Oman":                 { best:"Oct–Apr",    dur:"5–7 days",   cost:"$4,224",      airports:"MCT",           airline:"Oman Air (WY)" },
    "Saudi Arabia":         { best:"Oct–Mar",    dur:"5–7 days",   cost:"$100–$200",   airports:"JED,RUH",       airline:"Saudia/Flynas" },
    "United Arab Emirates": { best:"Oct–Apr",    dur:"5–7 days",   cost:"$200–$250",   airports:"DXB,AUH,SHJ",   airline:"Emirates/Etihad" },
    "South Africa":         { best:"May–Sep",    dur:"10–14 days", cost:"$200–$250",   airports:"JNB,CPT",       airline:"SAA" },
  };

  // Local country data — no external API dependency
  const countries = @json($localCountries);

  countries.sort((a, b) => {
    const aF = FEAT_NAMES.includes(a.name);
    const bF = FEAT_NAMES.includes(b.name);
    if (aF && !bF) return -1;
    if (!aF && bF) return 1;
    return a.name.localeCompare(b.name);
  });

  const grid   = document.getElementById('tpGrid');
  const loader = document.getElementById('tpLoader');

  let html = '';
  countries.forEach(c => {
    const name   = c.name;
    const info   = specialInfo[name] || { best:"Year-Round", dur:"5–7 days", cost:"$150–$300", airports:"International", airline:"Multiple" };
    const isFeat = FEAT_NAMES.includes(name);
    const isSoon = COMING.includes(name);

    if (isFeat) {
      const pageUrl = FEAT_MAP[name];
      html += `
        <a class="tp-country-card is-featured" href="${pageUrl}" style="text-decoration:none;">
          <img src="${c.flagUrl}" alt="${name}" loading="lazy">
          <span class="tp-feat-badge"><i class="bi bi-star-fill" style="font-size:9px;"></i> Featured</span>
          <span class="tp-country-name">${name}</span>
          <span class="tp-feat-btn">View Packages →</span>
        </a>`;
    } else if (isSoon) {
      html += `
        <div class="tp-country-card is-soon">
          <img src="${c.flagUrl}" alt="${name}" loading="lazy">
          <span class="tp-soon-badge">Coming Soon</span>
          <span class="tp-country-name">${name}</span>
        </div>`;
    } else {
      html += `
        <div class="tp-country-card">
          <img src="${c.flagUrl}" alt="${name}" loading="lazy">
          <span class="tp-country-name">${name}</span>
          <details class="tp-details">
            <summary>Details</summary>
            <div class="tp-details-content">
              <strong>Best time:</strong> ${info.best}<br>
              <strong>Stay:</strong> ${info.dur}<br>
              <strong>Budget/day:</strong> ${info.cost}<br>
              <strong>Airports:</strong> ${info.airports}<br>
              <strong>Airlines:</strong> ${info.airline}
            </div>
          </details>
        </div>`;
    }
  });

  grid.innerHTML = html;
  loader.style.display = 'none';
  grid.style.display   = 'grid';

  // Accordion: close other open details when one opens
  grid.querySelectorAll('.tp-details').forEach(d => {
    d.addEventListener('toggle', function () {
      if (this.open) {
        grid.querySelectorAll('.tp-details').forEach(o => { if (o !== this) o.removeAttribute('open'); });
      }
    });
  });

  // Search
  document.getElementById('tpSearch').addEventListener('input', function () {
    const term  = this.value.toLowerCase();
    const grid  = document.getElementById('tpGrid');
    const cards = grid.querySelectorAll('.tp-country-card');
    const noRes = document.getElementById('tpNoResults');
    let found   = false;
    cards.forEach(card => {
      const name = card.querySelector('.tp-country-name');
      const show = !term || (name && name.textContent.toLowerCase().includes(term));
      card.style.display = show ? '' : 'none';
      if (show) found = true;
      const d = card.querySelector('.tp-details');
      if (d && !show) d.removeAttribute('open');
    });
    noRes.style.display = found ? 'none' : 'block';
  });
})();
</script>

@include('footer')
