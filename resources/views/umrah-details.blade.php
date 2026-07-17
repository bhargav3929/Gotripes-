@include('header')

{{-- ═══════════════════════════════════════════════════════════════════
     umrah-details.blade.php — Production-Polish Build
     Improvements: sticky sidebar, multi-step wizard, color-coded cal,
     expanded booking summary, improved installments widget, trust strip,
     filled content sections, review modal, mobile-first responsive.
══════════════════════════════════════════════════════════════════════ --}}

<style>
/* ─── Font ──────────────────────────────────────────────────────────── */
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

*, *::before, *::after { box-sizing: border-box; }
body, main { font-family: 'Outfit', sans-serif; }

/* ─── Page Background ───────────────────────────────────────────────── */
.ud-page { background: #050505; min-height: 100vh; color: #e2e2e2; padding-bottom: 80px; }

/* ─── Hero ──────────────────────────────────────────────────────────── */
.ud-hero {
    position: relative;
    height: 60vh; min-height: 420px;
    background: linear-gradient(170deg, rgba(0,0,0,0.25) 0%, rgba(5,5,5,0.98) 100%),
                url('{{ asset($package->image) }}') center / cover no-repeat;
    display: flex; align-items: flex-end; padding-bottom: 44px;
}
.ud-hero .badge-cat {
    background: #FFD700; color: #000; font-size: 10.5px; font-weight: 800;
    padding: 5px 14px; border-radius: 50px; text-transform: uppercase;
    letter-spacing: 1.2px; display: inline-block; margin-bottom: 14px;
}
.ud-hero h1 {
    font-size: clamp(28px, 4.5vw, 50px); font-weight: 800; color: #fff;
    margin: 0 0 16px; line-height: 1.1; text-shadow: 0 4px 20px rgba(0,0,0,0.9);
}
.ud-hero .info-pill {
    background: rgba(255,255,255,0.08); padding: 7px 14px; border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.06); font-size: 13.5px; color: #ddd;
    display: inline-flex; align-items: center; gap: 7px;
}
.ud-hero-price-card {
    background: rgba(0,0,0,0.75); border: 1px solid rgba(255,215,0,0.3);
    padding: 22px 24px; border-radius: 16px; backdrop-filter: blur(12px);
    min-width: 240px;
}
.ud-hero-price-card .from-label { font-size: 13.5px; color: #aaa; font-weight: 600; letter-spacing: 0.5px; }
.ud-hero-price-card .ud-price { font-size: 40px; color: #FFD700; font-weight: 800; }

/* ─── Layout: Main grid ─────────────────────────────────────────────── */
.ud-content-row { display: grid; grid-template-columns: 1fr 400px; gap: 32px; align-items: start; }
@media(max-width:1100px) { .ud-content-row { grid-template-columns: 1fr; } }

/* ─── Panels ────────────────────────────────────────────────────────── */
.ud-panel {
    background: #0c0c0c; border: 1px solid #1c1c1c;
    border-radius: 16px; padding: 26px;
    box-shadow: 0 8px 28px rgba(0,0,0,0.45);
}
.ud-panel + .ud-panel { margin-top: 20px; }
.ud-panel-title {
    font-size: 16px; font-weight: 700; color: #fff;
    margin: 0 0 18px; padding-bottom: 12px; border-bottom: 1px solid #1e1e1e;
}
.ud-panel-title i { color: #FFD700; margin-right: 8px; }

/* ─── Gallery ───────────────────────────────────────────────────────── */
.ud-gallery { border-radius: 14px; overflow: hidden; height: 380px; }
.ud-gallery img { width: 100%; height: 100%; object-fit: cover; }

/* ─── Tabs ──────────────────────────────────────────────────────────── */
.ud-tabs { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 22px; padding-bottom: 16px; border-bottom: 1px solid #1e1e1e; }
.ud-tab {
    background: #111; border: 1px solid #222; color: #bbb;
    font-size: 13px; font-weight: 600; padding: 7px 16px; border-radius: 8px;
    cursor: pointer; transition: all 0.18s;
}
.ud-tab.active { background: #FFD700; border-color: #FFD700; color: #000; }
.ud-tab:hover:not(.active) { border-color: #444; color: #fff; }
.ud-tab-panel { display: none; }
.ud-tab-panel.active { display: block; }

/* ─── Features grid ─────────────────────────────────────────────────── */
.ud-feat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media(max-width:500px) { .ud-feat-grid { grid-template-columns: 1fr; } }
.ud-feat-item {
    display: flex; gap: 12px; align-items: flex-start;
    background: #141414; border: 1px solid #1c1c1c; padding: 14px; border-radius: 12px;
}
.ud-feat-icon {
    width: 42px; height: 42px; flex: none;
    background: rgba(255,215,0,0.1); border: 1.5px solid rgba(255,215,0,0.25);
    border-radius: 9px; display: flex; align-items: center; justify-content: center;
    font-size: 18px; color: #FFD700;
}
.ud-feat-label { font-size: 13.5px; font-weight: 700; color: #fff; margin: 0 0 3px; }
.ud-feat-desc  { font-size: 12px; color: #888; margin: 0; line-height: 1.4; }

/* ─── Itinerary timeline ────────────────────────────────────────────── */
.ud-timeline-item {
    display: flex; gap: 14px; margin-bottom: 20px;
}
.ud-timeline-dot {
    display: flex; flex-direction: column; align-items: center;
}
.ud-timeline-dot .dot {
    width: 32px; height: 32px; border-radius: 50%; background: #FFD700;
    color: #000; font-size: 11px; font-weight: 800; flex: none;
    display: flex; align-items: center; justify-content: center;
}
.ud-timeline-dot .line {
    width: 2px; flex: 1; background: linear-gradient(#FFD700, transparent);
    margin-top: 4px; min-height: 20px;
}
.ud-timeline-content { padding-top: 4px; }
.ud-timeline-content h6 { font-size: 13.5px; font-weight: 700; color: #fff; margin-bottom: 4px; }
.ud-timeline-content p  { font-size: 13px; color: #999; line-height: 1.55; margin: 0; }

/* ─── Inclusion / Exclusion list ────────────────────────────────────── */
.inc-list li { font-size: 13.5px; color: #ccc; margin-bottom: 8px; display: flex; align-items: flex-start; gap: 8px; list-style: none; }
.inc-list li i.inc { color: #22c55e; }
.inc-list li i.exc { color: #ef4444; }

/* ─── FAQ Accordion ─────────────────────────────────────────────────── */
.ud-faq-item { margin-bottom: 10px; border: 1px solid #1e1e1e; border-radius: 10px; overflow: hidden; }
.ud-faq-btn {
    width: 100%; text-align: left; background: #111; border: none;
    padding: 14px 18px; color: #ddd; font-size: 14px; font-weight: 600;
    cursor: pointer; display: flex; justify-content: space-between; align-items: center;
    transition: background 0.2s;
}
.ud-faq-btn.open { background: #161616; color: #FFD700; }
.ud-faq-btn i { transition: transform 0.25s; font-size: 14px; }
.ud-faq-btn.open i { transform: rotate(180deg); }
.ud-faq-body { display: none; padding: 14px 18px; font-size: 13.5px; color: #999; line-height: 1.65; background: #0e0e0e; }

/* ─── Why Choose section ────────────────────────────────────────────── */
.ud-why-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; }
@media(max-width:700px) { .ud-why-grid { grid-template-columns: 1fr 1fr; } }
.ud-why-item { text-align: center; padding: 20px 14px; background: #111; border: 1px solid #1c1c1c; border-radius: 12px; }
.ud-why-item i { font-size: 26px; color: #FFD700; display: block; margin-bottom: 10px; }
.ud-why-item h5 { font-size: 13px; font-weight: 700; color: #fff; margin-bottom: 6px; }
.ud-why-item p  { font-size: 12px; color: #888; margin: 0; line-height: 1.5; }

/* ─── Testimonials ──────────────────────────────────────────────────── */
.ud-testi-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
@media(max-width:600px) { .ud-testi-grid { grid-template-columns: 1fr; } }
.ud-testi-card { background: #111; border: 1px solid #1c1c1c; border-radius: 12px; padding: 18px; }
.ud-testi-stars { color: #FFD700; font-size: 13px; margin-bottom: 8px; }
.ud-testi-text  { font-size: 13px; color: #ccc; line-height: 1.6; margin-bottom: 12px; font-style: italic; }
.ud-testi-author { font-size: 12px; color: #888; font-weight: 600; }
.ud-testi-author span { color: #FFD700; }

/* ─── Related packages ──────────────────────────────────────────────── */
.ud-related-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; }
@media(max-width:900px) { .ud-related-grid { grid-template-columns: 1fr 1fr; } }
@media(max-width:560px) { .ud-related-grid { grid-template-columns: 1fr; } }
.ud-rel-card {
    background: #0c0c0c; border: 1px solid #1c1c1c; border-radius: 14px;
    overflow: hidden; transition: all 0.3s ease; display: flex; flex-direction: column;
}
.ud-rel-card:hover { border-color: rgba(255,215,0,0.45); transform: translateY(-4px); }
.ud-rel-thumb { height: 160px; background-size: cover; background-position: center; }
.ud-rel-body { padding: 16px; flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
.ud-rel-cat   { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #FFD700; margin-bottom: 6px; display: block; }
.ud-rel-title { font-size: 15px; font-weight: 700; color: #fff; margin-bottom: 14px; line-height: 1.35; }
.ud-rel-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px solid #191919; }
.ud-rel-price { font-size: 16px; font-weight: 800; color: #FFD700; }
.ud-rel-from  { font-size: 10px; color: #666; display: block; }

/* ─── ─── BOOKING SIDEBAR ─── ─── */
.ud-booking-col { position: sticky; top: 78px; max-height: calc(100vh - 90px); overflow-y: auto; scrollbar-width: thin; scrollbar-color: #2a2a2a transparent; }
.ud-booking-col::-webkit-scrollbar { width: 4px; }
.ud-booking-col::-webkit-scrollbar-thumb { background: #2a2a2a; border-radius: 4px; }

.ud-booking-card {
    background: #0c0c0c; border: 1px solid #1e1e1e;
    border-radius: 18px; overflow: hidden;
    box-shadow: 0 16px 48px rgba(0,0,0,0.55);
}
.ud-bk-header {
    background: linear-gradient(135deg, #1a1500 0%, #0c0c0c 100%);
    border-bottom: 1px solid #1e1e1e; padding: 20px 22px;
    display: flex; justify-content: space-between; align-items: center;
}
.ud-bk-title { font-size: 26px; font-weight: 800; color: #fff; margin: 0; letter-spacing: 0.5px; }
.ud-bk-price { font-size: 30px; font-weight: 800; color: #FFD700; }
.ud-bk-price small { font-size: 15px; color: #888; font-weight: 400; display: block; text-align: right; }

/* Quick Feat Strip */
.ud-feat-strip {
    display: grid; grid-template-columns: repeat(4,1fr);
    border-bottom: 1px solid #171717; text-align: center;
}
.ud-feat-strip-item { padding: 11px 4px; border-right: 1px solid #171717; }
.ud-feat-strip-item:last-child { border-right: none; }
.ud-feat-strip-item i { font-size: 20px; color: #FFD700; display: block; margin-bottom: 3px; }
.ud-feat-strip-item span { font-size: 13px; color: #999; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; display: block; }

/* Step Progress */
.ud-steps { display: flex; align-items: center; padding: 14px 22px 0; gap: 0; }
.ud-step {
    flex: 1; text-align: center; position: relative;
}
.ud-step::after {
    content: ''; position: absolute; top: 11px; left: 50%; right: -50%;
    height: 2px; background: #232323; z-index: 0;
}
.ud-step:last-child::after { display: none; }
.ud-step-dot {
    width: 24px; height: 24px; border-radius: 50%;
    background: #222; border: 2px solid #333;
    font-size: 10px; font-weight: 800; color: #666;
    display: inline-flex; align-items: center; justify-content: center;
    position: relative; z-index: 1; transition: all 0.3s;
}
.ud-step.done .ud-step-dot { background: #22c55e; border-color: #22c55e; color: #fff; }
.ud-step.active .ud-step-dot { background: #FFD700; border-color: #FFD700; color: #000; box-shadow: 0 0 10px rgba(255,215,0,0.5); }
.ud-step-label { font-size: 14px; color: #666; margin-top: 5px; font-weight: 600; }
.ud-step.active .ud-step-label { color: #FFD700; }
.ud-step.done .ud-step-label  { color: #22c55e; }

/* Form body */
.ud-bk-body { padding: 18px 22px 22px; }

.ud-field-label { font-size: 17px; color: #888; font-weight: 600; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.6px; display: block; }

/* Calendar */
.ud-cal-hint { font-size: 13.5px; color: #888; padding: 10px 14px; background: rgba(255,215,0,0.04); border: 1px solid rgba(255,215,0,0.1); border-radius: 8px; margin-bottom: 12px; line-height: 1.55; }
.ud-calendar { background: #111; border: 1px solid #1e1e1e; border-radius: 12px; padding: 14px; }
.ud-cal-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
.ud-cal-nav-btn { background: none; border: 1px solid #2a2a2a; color: #FFD700; width: 28px; height: 28px; border-radius: 6px; cursor: pointer; font-size: 13px; transition: background 0.2s; }
.ud-cal-nav-btn:hover { background: rgba(255,215,0,0.12); }
.ud-cal-month { font-size: 13.5px; font-weight: 700; color: #fff; }
.ud-cal-dow { display: grid; grid-template-columns: repeat(7,1fr); text-align: center; font-size: 10px; color: #555; font-weight: 700; margin-bottom: 6px; }
.ud-cal-dow span.wed { color: #FFD700; }
.ud-cal-days  { display: grid; grid-template-columns: repeat(7,1fr); gap: 4px; }
.ud-day {
    aspect-ratio: 1; display: flex; align-items: center; justify-content: center;
    border-radius: 7px; font-size: 12.5px; font-weight: 600; transition: all 0.18s;
    cursor: default; position: relative;
}
.ud-day.other  { color: #303030; }
.ud-day.grey   { color: #3a3a3a; }
.ud-day.avail  {
    background: rgba(34,197,94,0.12); border: 1px solid rgba(34,197,94,0.35);
    color: #4ade80; cursor: pointer;
}
.ud-day.avail:hover { background: rgba(34,197,94,0.3); color: #fff; }
.ud-day.selected {
    background: #FFD700 !important; color: #000 !important; font-weight: 800;
    box-shadow: 0 0 14px rgba(255,215,0,0.55);
}
.ud-day.avail .seats-dot {
    position: absolute; bottom: 3px; left: 50%; transform: translateX(-50%);
    width: 4px; height: 4px; border-radius: 50%; background: #4ade80;
}
.ud-day.selected .seats-dot { background: #000; }

/* Passenger counter */
.ud-pax-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; }
.ud-pax-box {
    background: #111; border: 1px solid #1e1e1e; border-radius: 10px;
    padding: 10px 6px; text-align: center;
}
.ud-pax-box .pax-label { font-size: 10.5px; color: #888; font-weight: 600; display: block; margin-bottom: 8px; }
.ud-pax-controls { display: flex; align-items: center; justify-content: center; gap: 8px; }
.ud-pax-btn {
    background: #1e1e1e; border: 1px solid #2a2a2a; color: #FFD700;
    width: 24px; height: 24px; border-radius: 50%; font-size: 14px; font-weight: 700;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: all 0.18s; line-height: 1;
}
.ud-pax-btn:hover { background: #FFD700; color: #000; }
.ud-pax-val { font-size: 15px; font-weight: 800; color: #fff; min-width: 20px; }

/* Step sections */
.ud-step-section { display: none; }
.ud-step-section.active { display: block; animation: fadeSlide 0.28s ease; }
@keyframes fadeSlide { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

/* Inputs */
.ud-input {
    background: #111 !important; border: 1px solid #222 !important;
    color: #fff !important; padding: 11px 14px !important;
    border-radius: 9px !important; font-size: 14.5px; width: 100%;
    font-family: 'Outfit', sans-serif; outline: none; transition: border-color 0.2s;
}
.ud-input:focus { border-color: rgba(255,215,0,0.5) !important; box-shadow: 0 0 0 3px rgba(255,215,0,0.07) !important; }
.ud-input::placeholder { color: #555; }

/* Gateway */
.ud-gw-row { display: grid; grid-template-columns: repeat(3,1fr); gap: 8px; }
.ud-gw-label input { display: none; }
.ud-gw-btn {
    background: #111; border: 1px solid #222; color: #aaa;
    padding: 11px 8px; border-radius: 9px; font-size: 13.5px; font-weight: 700;
    text-align: center; cursor: pointer; transition: all 0.2s;
}
.ud-gw-label input:checked + .ud-gw-btn {
    background: #FFD700; border-color: #FFD700; color: #000;
    box-shadow: 0 4px 12px rgba(255,215,0,0.2);
}

/* Installments widget */
.ud-install-box {
    background: rgba(255,215,0,0.04); border: 1px solid rgba(255,215,0,0.18);
    border-radius: 11px; padding: 14px 16px; margin-top: 10px;
}
.ud-install-total { font-size: 22px; font-weight: 800; color: #FFD700; }
.ud-install-arrow { text-align: center; color: #444; font-size: 20px; margin: 8px 0; }
.ud-install-plan  { display: flex; gap: 10px; }
.ud-install-cell  {
    flex: 1; background: #141414; border: 1px solid #222;
    border-radius: 9px; padding: 10px 12px; text-align: center;
}
.ud-install-cell .ic-label { font-size: 10px; color: #888; font-weight: 600; margin-bottom: 4px; display: block; }
.ud-install-cell .ic-val   { font-size: 17px; font-weight: 800; color: #fff; }
.ud-install-badge { display: inline-block; background: #22c55e; color: #000; font-size: 9.5px; font-weight: 800; padding: 3px 9px; border-radius: 50px; margin-bottom: 8px; }

/* Navigation buttons */
.ud-nav-row { display: flex; gap: 8px; margin-top: 16px; }
.ud-btn-back {
    background: #1a1a1a; border: 1px solid #2a2a2a; color: #bbb;
    padding: 11px 16px; border-radius: 9px; font-size: 14px; font-weight: 700;
    cursor: pointer; transition: background 0.2s; flex-shrink: 0;
}
.ud-btn-back:hover { background: #242424; }
.ud-btn-next {
    flex: 1; background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
    border: none; color: #000; padding: 12px; border-radius: 9px;
    font-size: 15px; font-weight: 800; cursor: pointer; transition: opacity 0.2s;
}
.ud-btn-next:hover { opacity: 0.9; }
.ud-btn-next:disabled { opacity: 0.45; cursor: not-allowed; }
.ud-btn-submit {
    width: 100%; background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
    border: none; color: #000; padding: 14px; border-radius: 10px;
    font-size: 15px; font-weight: 800; cursor: pointer; transition: opacity 0.2s;
    margin-top: 14px;
}
.ud-btn-submit:hover { opacity: 0.9; }
.ud-btn-submit:disabled { opacity: 0.45; cursor: not-allowed; }

/* Booking summary rows */
.ud-sum-row { display: flex; justify-content: space-between; font-size: 13.5px; color: #888; margin-bottom: 10px; }
.ud-sum-row .val { color: #fff; font-weight: 600; text-align: right; max-width: 55%; }
.ud-sum-divider { border: none; border-top: 1px solid #1e1e1e; margin: 12px 0; }
.ud-sum-total { display: flex; justify-content: space-between; align-items: center; }
.ud-sum-total .tlabel { font-size: 16px; font-weight: 700; color: #fff; }
.ud-sum-total .tval   { font-size: 25px; font-weight: 800; color: #FFD700; }

/* Trust strip (below card) */
.ud-trust-list { list-style: none; padding: 0; margin: 0; }
.ud-trust-list li {
    display: flex; align-items: center; gap: 10px;
    font-size: 13.5px; color: #aaa; padding: 9px 0;
    border-bottom: 1px solid #141414;
}
.ud-trust-list li:last-child { border-bottom: none; }
.ud-trust-list li i { color: #FFD700; font-size: 15px; flex: none; }

/* Alert */
.ud-alert {
    padding: 10px 14px; border-radius: 9px; font-size: 13px;
    background: rgba(220,38,38,0.12); border: 1px solid rgba(220,38,38,0.35);
    color: #fca5a5; margin-bottom: 14px; display: none;
}

/* Review panel */
.ud-review-row { display: flex; justify-content: space-between; font-size: 13px; color: #888; padding: 9px 0; border-bottom: 1px solid #181818; }
.ud-review-row:last-child { border-bottom: none; }
.ud-review-row .rv  { color: #fff; font-weight: 600; }

/* Passenger fields */
.ud-pax-field-card { background: #111; border: 1px solid #1e1e1e; border-radius: 10px; padding: 14px 16px; margin-bottom: 10px; }
.ud-pax-field-card h6 { font-size: 12.5px; color: #FFD700; font-weight: 700; margin: 0 0 12px; }
.ud-pax-fields-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.ud-pax-fields-grid .full { grid-column: 1/-1; }

/* Mobile floating CTA */
.ud-mobile-cta {
    display: none; position: fixed; bottom: 0; left: 0; right: 0; z-index: 900;
    background: #0c0c0c; border-top: 1px solid #1e1e1e;
    padding: 12px 16px; gap: 12px; align-items: center;
    box-shadow: 0 -8px 24px rgba(0,0,0,0.6);
}
@media(max-width:1100px) {
    .ud-mobile-cta { display: flex; }
    .ud-booking-col { display: none; }
    .ud-page { padding-bottom: 90px; }
}
.ud-mobile-price { flex: 1; }
.ud-mobile-price .mprice { font-size: 18px; font-weight: 800; color: #FFD700; }
.ud-mobile-price .mlabel { font-size: 10px; color: #888; }
.ud-mobile-book-btn {
    background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
    color: #000; font-weight: 800; font-size: 14px;
    padding: 12px 28px; border-radius: 10px; border: none; cursor: pointer;
}

/* Mobile modal booking */
.ud-mobile-modal {
    display: none; position: fixed; inset: 0; z-index: 950;
    background: rgba(0,0,0,0.85); backdrop-filter: blur(4px);
    align-items: flex-end; justify-content: center;
}
.ud-mobile-modal.open { display: flex; }
.ud-mobile-modal-inner {
    background: #0c0c0c; border: 1px solid #222;
    border-radius: 20px 20px 0 0; width: 100%; max-width: 600px;
    max-height: 92vh; overflow-y: auto; padding: 24px 20px 30px;
}
.ud-modal-handle { width: 40px; height: 4px; background: #333; border-radius: 4px; margin: 0 auto 18px; }

/* Back to Packages Button Style */
.ud-back-btn {
    color: #000 !important;
    text-decoration: none;
    font-weight: 800;
    font-size: 16px;
    background: #FFD700;
    padding: 12px 28px;
    border-radius: 50px;
    border: 2px solid #FFD700;
    box-shadow: 0 6px 20px rgba(255, 215, 0, 0.35);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 28px;
    transition: all 0.25s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.ud-back-btn:hover {
    background: #000 !important;
    color: #FFD700 !important;
    border-color: #FFD700 !important;
    box-shadow: 0 8px 24px rgba(255, 215, 0, 0.5);
    transform: translateY(-2px);
}

/* Light theme override */
@media(prefers-color-scheme: light) {
    /* Respect system default but keep dark explicitly since GoTrips is dark-first */
}
</style>

<main class="ud-page">

    {{-- ───────────────── HERO ───────────────── --}}
    <section class="ud-hero">
        <div class="container" style="position:relative;z-index:2;">
            <a href="{{ route('umrah-visas.index') }}" class="ud-back-btn">
                <i class="bi bi-arrow-left"></i> Back to Hajj & Umrah
            </a>
            <div class="row align-items-end g-4">
                <div class="col-lg-8">
                    <span class="badge-cat">🚌 Umrah Bus · {{ $package->category }}</span>
                    <h1>{{ $package->title }}</h1>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <span class="info-pill"><i class="bi bi-clock text-warning"></i>{{ $package->duration }}</span>
                        <span class="info-pill"><i class="bi bi-building text-warning"></i>{{ $package->hotels ? Str::limit($package->hotels, 40) : 'Hotels Included' }}</span>
                        <span class="info-pill"><i class="bi bi-bus-front text-warning"></i>{{ $package->transport ? Str::limit($package->transport, 40) : 'Coach Travel' }}</span>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="ud-hero-price-card">
                        <span class="from-label">Starting from</span>
                        <div class="ud-price">
                            @if($package->discount_price && $package->discount_price < $package->original_price)
                                <span style="text-decoration: line-through; font-size: 20px; color: #888; margin-right: 8px;">AED {{ number_format($package->original_price, 0) }}</span>
                                AED {{ number_format($package->starting_price, 0) }}
                            @else
                                AED {{ number_format($package->starting_price, 0) }}
                            @endif
                        </div>
                        <button onclick="scrollToBooking()" class="ud-btn-next w-100 mt-3" style="border-radius:9px;padding:11px;">
                            Book Now <i class="bi bi-arrow-down-short"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ───────────────── MAIN GRID ───────────────── --}}
    <div class="container mt-4">
        <div class="ud-content-row">

            {{-- ── LEFT COLUMN ── --}}
            <div>
                {{-- Gallery --}}
                <div class="ud-panel" style="padding:10px; margin-bottom:20px;">
                    <div id="ud-gallery" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner ud-gallery">
                            <div class="carousel-item active" style="height:100%;">
                                <img src="{{ asset($package->image) }}" alt="{{ $package->title }}" class="w-100 h-100" style="object-fit:cover;">
                            </div>
                            @if(!empty($package->gallery_images))
                                @foreach($package->gallery_images as $gi => $img)
                                <div class="carousel-item" style="height:100%;">
                                    <img src="{{ asset($img) }}" alt="Gallery {{ $gi+1 }}" class="w-100 h-100" style="object-fit:cover;">
                                </div>
                                @endforeach
                            @endif
                        </div>
                        @if(!empty($package->gallery_images) && count($package->gallery_images))
                            <button class="carousel-control-prev" type="button" data-bs-target="#ud-gallery" data-bs-slide="prev"></button>
                            <button class="carousel-control-next" type="button" data-bs-target="#ud-gallery" data-bs-slide="next"></button>
                        @endif
                    </div>
                </div>

                {{-- Tabs Panel --}}
                <div class="ud-panel">
                    <div class="ud-tabs">
                        <button class="ud-tab active" onclick="switchTab(this,'tab-overview')">Overview</button>
                        <button class="ud-tab" onclick="switchTab(this,'tab-hotels')">Hotels</button>
                        @if($package->category === 'air')
                        <button class="ud-tab" onclick="switchTab(this,'tab-air')">Flight Details</button>
                        @endif
                        <button class="ud-tab" onclick="switchTab(this,'tab-itinerary')">Itinerary</button>
                        <button class="ud-tab" onclick="switchTab(this,'tab-inclusions')">Inclusions</button>
                        <button class="ud-tab" onclick="switchTab(this,'tab-faq')">FAQs</button>
                    </div>

                    {{-- Overview --}}
                    <div class="ud-tab-panel active" id="tab-overview">
                        <h4 class="ud-panel-title" style="border:none;padding:0;margin-bottom:14px;font-size:18px;"><i class="bi bi-info-circle"></i>Package Overview</h4>
                        <p style="color:#aaa;font-size:14px;line-height:1.7;margin-bottom:22px;">{{ $package->description ?: 'Experience a spiritually enriching Umrah journey from UAE with full package support, accommodation, transport, and guided Ziyarat tours across Medina and Makkah.' }}</p>
                        <div class="ud-feat-grid">
                            <div class="ud-feat-item"><div class="ud-feat-icon"><i class="bi bi-shield-check"></i></div><div><p class="ud-feat-label">Visa Included</p><p class="ud-feat-desc">Fully managed Umrah visa documentation & submission.</p></div></div>
                            <div class="ud-feat-item"><div class="ud-feat-icon"><i class="bi bi-bus-front"></i></div><div><p class="ud-feat-label">AC Coach</p><p class="ud-feat-desc">Round-trip luxury air-conditioned coach from UAE.</p></div></div>
                            <div class="ud-feat-item"><div class="ud-feat-icon"><i class="bi bi-building"></i></div><div><p class="ud-feat-label">Hotel Stay</p><p class="ud-feat-desc">Comfortable stays in Medina & Makkah close to Harams.</p></div></div>
                            <div class="ud-feat-item"><div class="ud-feat-icon"><i class="bi bi-compass"></i></div><div><p class="ud-feat-label">Ziyarat Tours</p><p class="ud-feat-desc">Historical guided visits across Medina & Makkah.</p></div></div>
                        </div>

                        <!-- Service Order Workflow -->
                        <div style="margin-top: 35px; border-top: 1px solid #1e1e1e; padding-top: 25px;">
                            <h5 style="color: #fff; font-size: 15px; font-weight: 700; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 0.8px;">
                                <i class="bi bi-arrow-right-circle text-warning me-2"></i>Logical Service Booking Order
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div style="background: rgba(255,215,0,0.02); border: 1.5px solid rgba(255,215,0,0.15); border-radius: 12px; padding: 18px; position: relative; height: 100%;">
                                        <div style="position: absolute; top: -12px; left: 16px; background: #FFD700; color: #000; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 4px;">STEP 1</div>
                                        <h6 style="color: #FFD700; font-size: 14.5px; font-weight: 700; margin-top: 4px; margin-bottom: 8px;">
                                            <i class="bi bi-file-earmark-person-fill me-2"></i>1. Visa Approval
                                        </h6>
                                        <p style="font-size: 12px; color: #aaa; margin: 0; line-height: 1.5;">We verify passports and process your Saudi tourist or Umrah visa with absolute priority.</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div style="background: rgba(255,255,255,0.01); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 18px; position: relative; height: 100%;">
                                        <div style="position: absolute; top: -12px; left: 16px; background: #222; color: #aaa; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 4px;">STEP 2</div>
                                        <h6 style="color: #fff; font-size: 14.5px; font-weight: 700; margin-top: 4px; margin-bottom: 8px;">
                                            <i class="bi bi-bus-front-fill me-2 text-warning"></i>2. Transport Booking
                                        </h6>
                                        <p style="font-size: 12px; color: #aaa; margin: 0; line-height: 1.5;">We reserve your seat in our luxury air-conditioned transport coach (or flight bookings).</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div style="background: rgba(255,255,255,0.01); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 18px; position: relative; height: 100%;">
                                        <div style="position: absolute; top: -12px; left: 16px; background: #222; color: #aaa; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 4px;">STEP 3</div>
                                        <h6 style="color: #fff; font-size: 14.5px; font-weight: 700; margin-top: 4px; margin-bottom: 8px;">
                                            <i class="bi bi-building-fill me-2 text-warning"></i>3. Hotel Confirmation
                                        </h6>
                                        <p style="font-size: 12px; color: #aaa; margin: 0; line-height: 1.5;">We finalize your comfortable rooms in Medina & Makkah close to both Harams.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Hotels --}}
                    <div class="ud-tab-panel" id="tab-hotels">
                        <h4 class="ud-panel-title" style="border:none;padding:0;margin-bottom:14px;font-size:18px;"><i class="bi bi-building"></i>Hotels & Accommodation</h4>
                        
                        @if($package->mapped_hotels && $package->mapped_hotels->count() > 0)
                            <div class="row g-3 mb-3">
                                @foreach($package->mapped_hotels as $hotel)
                                <div class="col-md-6">
                                    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 15px; height: 100%;">
                                        <h5 style="color: #FFD700; margin-bottom: 5px; font-size: 16px;">
                                            <i class="bi bi-building me-2"></i>{{ $hotel->name }}
                                        </h5>
                                        <div style="font-size: 13px; color: #aaa; margin-bottom: 8px;">
                                            <i class="bi bi-geo-alt-fill me-1 text-danger"></i> {{ $hotel->city }}
                                        </div>
                                        <div style="color: #ccc; font-size: 13px;">
                                            <div class="mb-1"><strong>Star Rating:</strong> {{ $hotel->star_rating }} Stars</div>
                                            <div class="mb-1"><strong>Distance:</strong> {{ $hotel->distance_to_haram }}</div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endif

                        <p style="color:#ccc;font-size:14px;line-height:1.75;white-space:pre-line;">{{ $package->hotels ?: 'Hotel details will be confirmed at booking time.' }}</p>
                    </div>

                    @if($package->category === 'air')
                    {{-- Air Details --}}
                    <div class="ud-tab-panel" id="tab-air">
                        <h4 class="ud-panel-title" style="border:none;padding:0;margin-bottom:14px;font-size:18px;"><i class="bi bi-airplane"></i>Flight Details</h4>
                        
                        <div class="row g-3" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 15px; margin: 0;">
                            @if($package->airline)
                            <div class="col-sm-6">
                                <span style="color: #888; font-size: 12px; display: block;">Airline</span>
                                <strong style="color: #fff;">{{ $package->airline }}</strong>
                            </div>
                            @endif
                            @if($package->flight_number)
                            <div class="col-sm-6">
                                <span style="color: #888; font-size: 12px; display: block;">Flight Number</span>
                                <strong style="color: #fff;">{{ $package->flight_number }}</strong>
                            </div>
                            @endif
                            @if($package->departure_airport)
                            <div class="col-sm-6">
                                <span style="color: #888; font-size: 12px; display: block;">Departure</span>
                                <strong style="color: #fff;">{{ $package->departure_airport }}</strong>
                            </div>
                            @endif
                            @if($package->arrival_airport)
                            <div class="col-sm-6">
                                <span style="color: #888; font-size: 12px; display: block;">Arrival</span>
                                <strong style="color: #fff;">{{ $package->arrival_airport }}</strong>
                            </div>
                            @endif
                            @if($package->cabin_class)
                            <div class="col-sm-6">
                                <span style="color: #888; font-size: 12px; display: block;">Cabin Class</span>
                                <strong style="color: #fff;">{{ $package->cabin_class }}</strong>
                            </div>
                            @endif
                            @if($package->baggage)
                            <div class="col-sm-6">
                                <span style="color: #888; font-size: 12px; display: block;">Baggage</span>
                                <strong style="color: #fff;">{{ $package->baggage }}</strong>
                            </div>
                            @endif
                            @if($package->transit_details)
                            <div class="col-12 mt-3 pt-3" style="border-top: 1px solid rgba(255,255,255,0.08);">
                                <span style="color: #888; font-size: 12px; display: block;">Transit Details</span>
                                <strong style="color: #fff;">{{ $package->transit_details }}</strong>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    {{-- Itinerary --}}
                    <div class="ud-tab-panel" id="tab-itinerary">
                        <h4 class="ud-panel-title" style="border:none;padding:0;margin-bottom:20px;font-size:18px;"><i class="bi bi-map"></i>Day-by-Day Itinerary</h4>
                        @if($package->itinerary && count($package->itinerary))
                        @foreach($package->itinerary as $di => $day)
                        <div class="ud-timeline-item">
                            <div class="ud-timeline-dot">
                                <div class="dot">{{ $di+1 }}</div>
                                @if(!$loop->last)<div class="line"></div>@endif
                            </div>
                            <div class="ud-timeline-content">
                                <h6>Day {{ $di+1 }}</h6>
                                <p>{{ $day }}</p>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <p style="color:#888;">Detailed itinerary provided upon booking confirmation.</p>
                        @endif
                    </div>

                    {{-- Inclusions --}}
                    <div class="ud-tab-panel" id="tab-inclusions">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h5 style="color:#22c55e;font-size:15px;font-weight:700;margin-bottom:14px;"><i class="bi bi-check2-square me-2"></i>Inclusions</h5>
                                <ul class="inc-list ps-0">
                                    @if($package->inclusions && count($package->inclusions))
                                        @foreach($package->inclusions as $inc)
                                        <li><i class="bi bi-check-circle-fill inc"></i>{{ $inc }}</li>
                                        @endforeach
                                    @else
                                        <li style="color:#666;">Will be detailed on booking.</li>
                                    @endif
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 style="color:#ef4444;font-size:15px;font-weight:700;margin-bottom:14px;"><i class="bi bi-x-square me-2"></i>Exclusions</h5>
                                <ul class="inc-list ps-0">
                                    @if($package->exclusions && count($package->exclusions))
                                        @foreach($package->exclusions as $exc)
                                        <li><i class="bi bi-x-circle-fill exc"></i>{{ $exc }}</li>
                                        @endforeach
                                    @else
                                        <li style="color:#666;">Will be detailed on booking.</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- FAQs --}}
                    <div class="ud-tab-panel" id="tab-faq">
                        <h4 class="ud-panel-title" style="border:none;padding:0;margin-bottom:16px;font-size:18px;"><i class="bi bi-question-circle"></i>Frequently Asked Questions</h4>
                        <div class="ud-faq-item">
                            <button class="ud-faq-btn" onclick="toggleFaq(this)">What documents are required? <i class="bi bi-chevron-down"></i></button>
                            <div class="ud-faq-body">A clear passport copy (6+ months validity) and UAE residence visa copy are required. These are collected securely during checkout.</div>
                        </div>
                        <div class="ud-faq-item">
                            <button class="ud-faq-btn" onclick="toggleFaq(this)">Is the Umrah Visa fee included? <i class="bi bi-chevron-down"></i></button>
                            <div class="ud-faq-body">Yes — visa processing is fully included in the quoted package price. No hidden charges at checkout.</div>
                        </div>
                        <div class="ud-faq-item">
                            <button class="ud-faq-btn" onclick="toggleFaq(this)">When do coaches depart? <i class="bi bi-chevron-down"></i></button>
                            <div class="ud-faq-body">All bus departures operate exclusively on Wednesdays. This ensures a consistent group schedule and streamlined processing at border crossings.</div>
                        </div>
                        <div class="ud-faq-item">
                            <button class="ud-faq-btn" onclick="toggleFaq(this)">What is the cancellation policy? <i class="bi bi-chevron-down"></i></button>
                            <div class="ud-faq-body">Cancellations made 10+ days before departure receive a 90% refund. Cancellations within 5 days of departure are non-refundable as hotel and bus slots are finalized.</div>
                        </div>
                        <div class="ud-faq-item">
                            <button class="ud-faq-btn" onclick="toggleFaq(this)">Can I pay in installments? <i class="bi bi-chevron-down"></i></button>
                            <div class="ud-faq-body">Yes! Select Tabby or Tamara as your payment method during checkout to split your booking into 4 equal monthly interest-free installments.</div>
                        </div>
                    </div>
                </div>

                {{-- ── Why Choose This Package ── --}}
                <div class="ud-panel mt-4">
                    <h4 class="ud-panel-title"><i class="bi bi-stars"></i>Why Choose This Package</h4>
                    <div class="ud-why-grid">
                        <div class="ud-why-item">
                            <i class="bi bi-patch-check-fill"></i>
                            <h5>DTCM Licensed</h5>
                            <p>Fully licensed UAE travel agency with government authorization.</p>
                        </div>
                        <div class="ud-why-item">
                            <i class="bi bi-people-fill"></i>
                            <h5>Group Safety</h5>
                            <p>Group travel with experienced guides at every milestone.</p>
                        </div>
                        <div class="ud-why-item">
                            <i class="bi bi-wallet2"></i>
                            <h5>Best Value</h5>
                            <p>Transparent all-inclusive pricing with zero hidden fees.</p>
                        </div>
                        <div class="ud-why-item">
                            <i class="bi bi-clock-history"></i>
                            <h5>10+ Years Exp.</h5>
                            <p>Thousands of pilgrims served from UAE since 2014.</p>
                        </div>
                        <div class="ud-why-item">
                            <i class="bi bi-headset"></i>
                            <h5>24/7 Support</h5>
                            <p>WhatsApp & phone support throughout your journey.</p>
                        </div>
                        <div class="ud-why-item">
                            <i class="bi bi-geo-alt-fill"></i>
                            <h5>Haram Proximity</h5>
                            <p>Hotels chosen for minimal walking distance to Masjid.</p>
                        </div>
                    </div>
                </div>

                {{-- ── Customer Testimonials ── --}}
                <div class="ud-panel mt-4">
                    <h4 class="ud-panel-title"><i class="bi bi-chat-quote"></i>What Pilgrims Say</h4>
                    <div class="ud-testi-grid">
                        <div class="ud-testi-card">
                            <div class="ud-testi-stars">★★★★★</div>
                            <p class="ud-testi-text">"Alhamdulillah, everything was perfectly organised. The coach was comfortable and the hotel was very close to Masjid al-Haram. Highly recommend GoTrips!"</p>
                            <div class="ud-testi-author">Mohammed A. · <span>Abu Dhabi</span></div>
                        </div>
                        <div class="ud-testi-card">
                            <div class="ud-testi-stars">★★★★★</div>
                            <p class="ud-testi-text">"The Ziyarat tours were very informative. Our guide was knowledgeable and patient. I'm planning my second trip with GoTrips this coming Ramadan."</p>
                            <div class="ud-testi-author">Fatima H. · <span>Dubai</span></div>
                        </div>
                        <div class="ud-testi-card">
                            <div class="ud-testi-stars">★★★★☆</div>
                            <p class="ud-testi-text">"Very smooth visa process — the team handled everything. Bus was on time and hotel rooms were clean. Will book again for family Umrah."</p>
                            <div class="ud-testi-author">Ahmed S. · <span>Sharjah</span></div>
                        </div>
                        <div class="ud-testi-card">
                            <div class="ud-testi-stars">★★★★★</div>
                            <p class="ud-testi-text">"Used the Tabby installment option — made it easy to manage the cost. The whole booking took under 10 minutes. JazakAllah Khair, GoTrips!"</p>
                            <div class="ud-testi-author">Nadia R. · <span>Ajman</span></div>
                        </div>
                    </div>
                </div>

                {{-- ── Related Packages ── --}}
                @if(isset($relatedPackages) && count($relatedPackages))
                <div class="ud-panel mt-4">
                    <h4 class="ud-panel-title"><i class="bi bi-kaaba"></i>Other Hajj & Umrah Packages</h4>
                    <div class="ud-related-grid">
                        @foreach($relatedPackages as $rp)
                        <div class="ud-rel-card">
                            <div class="ud-rel-thumb" style="background-image:url('{{ asset($rp->image) }}');"></div>
                            <div class="ud-rel-body">
                                <div>
                                    <span class="ud-rel-cat">{{ $rp->category }}</span>
                                    <h6 class="ud-rel-title">{{ $rp->title }}</h6>
                                </div>
                                <div class="ud-rel-footer">
                                    <div>
                                        <span class="ud-rel-from">From</span>
                                        <span class="ud-rel-price">AED {{ number_format($rp->starting_price, 0) }}</span>
                                    </div>
                                    <a href="{{ route('umrah-visas.show', $rp->id) }}" class="ud-btn-next" style="font-size:12px;padding:8px 16px;border-radius:7px;display:inline-block;flex:none !important;width:auto !important;text-decoration:none;">View</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- ── RIGHT COLUMN: STICKY BOOKING SIDEBAR ── --}}
            <div class="ud-booking-col" id="bookingCardCol">

                <div class="ud-booking-card">
                    {{-- Header --}}
                    <div class="ud-bk-header">
                        <h3 class="ud-bk-title">Book Your Pilgrimage</h3>
                        <div>
                            <div class="ud-bk-price">
                                @if($package->discount_price && $package->discount_price < $package->original_price)
                                    <span style="text-decoration: line-through; font-size: 13px; color: #888; margin-right: 4px;">AED {{ number_format($package->original_price, 0) }}</span>
                                    AED {{ number_format($package->starting_price, 0) }}
                                @else
                                    AED {{ number_format($package->starting_price, 0) }}
                                @endif
                                <small>per person</small>
                            </div>
                        </div>
                    </div>

                    {{-- Feature strip --}}
                    <div class="ud-feat-strip">
                        <div class="ud-feat-strip-item"><i class="bi bi-shield-check"></i><span>Visa Incl.</span></div>
                        <div class="ud-feat-strip-item"><i class="bi bi-building"></i><span>Hotel Incl.</span></div>
                        <div class="ud-feat-strip-item"><i class="bi bi-bus-front"></i><span>AC Coach</span></div>
                        <div class="ud-feat-strip-item"><i class="bi bi-lightning-charge"></i><span>Instant</span></div>
                    </div>

                    {{-- Step progress --}}
                    <div class="ud-steps" id="step-indicator">
                        <div class="ud-step active" id="si-1"><div class="ud-step-dot">1</div><div class="ud-step-label">Date</div></div>
                        <div class="ud-step" id="si-2"><div class="ud-step-dot">2</div><div class="ud-step-label">Pax</div></div>
                        <div class="ud-step" id="si-3"><div class="ud-step-dot">3</div><div class="ud-step-label">Details</div></div>
                        <div class="ud-step" id="si-4"><div class="ud-step-dot">4</div><div class="ud-step-label">Contact</div></div>
                        <div class="ud-step" id="si-5"><div class="ud-step-dot">5</div><div class="ud-step-label">Payment</div></div>
                        <div class="ud-step" id="si-6"><div class="ud-step-dot">6</div><div class="ud-step-label">Review</div></div>
                    </div>

                    {{-- Form body --}}
                    <div class="ud-bk-body">
                        <div id="ud-alert" class="ud-alert"></div>
                        <form id="umrah-booking-form" onsubmit="submitBooking(event)">
                            @csrf
                            <input type="hidden" id="departure_date" name="departure_date">

                            {{-- ─── STEP 1: Date ─── --}}
                            <div class="ud-step-section active" id="step-1">
                                <span class="ud-field-label">Step 1 — Select Departure Date</span>
                                <div class="ud-cal-hint">
                                    <i class="bi bi-info-circle text-warning me-1"></i>
                                    Bus departures operate every <strong style="color:#FFD700;">Wednesday</strong>. Book at least 5 days before departure.
                                </div>
                                <div class="ud-calendar">
                                    <div class="ud-cal-nav">
                                        <button type="button" class="ud-cal-nav-btn" onclick="navigateMonth(-1)"><i class="bi bi-chevron-left"></i></button>
                                        <span class="ud-cal-month" id="cal-month-label"></span>
                                        <button type="button" class="ud-cal-nav-btn" onclick="navigateMonth(1)"><i class="bi bi-chevron-right"></i></button>
                                    </div>
                                    <div class="ud-cal-dow">
                                        <span>Su</span><span>Mo</span><span>Tu</span>
                                        <span class="wed">We</span>
                                        <span>Th</span><span>Fr</span><span>Sa</span>
                                    </div>
                                    <div class="ud-cal-days" id="cal-days"></div>
                                </div>
                                <div class="ud-nav-row">
                                    <button type="button" class="ud-btn-next w-100" onclick="goStep(2)" id="btn-step1-next" disabled>
                                        Choose Passengers <i class="bi bi-chevron-right ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- ─── STEP 2: Passenger Count ─── --}}
                            <div class="ud-step-section" id="step-2">
                                <span class="ud-field-label">Step 2 — Select Passengers</span>
                                <div class="ud-pax-row mb-3">
                                    <div class="ud-pax-box">
                                        <span class="pax-label">Adults</span>
                                        <div class="ud-pax-controls">
                                            <button type="button" class="ud-pax-btn" onclick="updateCounter('adults',-1)">−</button>
                                            <span class="ud-pax-val" id="adults-count">1</span>
                                            <button type="button" class="ud-pax-btn" onclick="updateCounter('adults',1)">+</button>
                                        </div>
                                    </div>
                                    <div class="ud-pax-box">
                                        <span class="pax-label">Children</span>
                                        <div class="ud-pax-controls">
                                            <button type="button" class="ud-pax-btn" onclick="updateCounter('children',-1)">−</button>
                                            <span class="ud-pax-val" id="children-count">0</span>
                                            <button type="button" class="ud-pax-btn" onclick="updateCounter('children',1)">+</button>
                                        </div>
                                    </div>
                                    <div class="ud-pax-box">
                                        <span class="pax-label">Infants</span>
                                        <div class="ud-pax-controls">
                                            <button type="button" class="ud-pax-btn" onclick="updateCounter('infants',-1)">−</button>
                                            <span class="ud-pax-val" id="infants-count">0</span>
                                            <button type="button" class="ud-pax-btn" onclick="updateCounter('infants',1)">+</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="ud-nav-row">
                                    <button type="button" class="ud-btn-back" onclick="goStep(1)"><i class="bi bi-chevron-left"></i></button>
                                    <button type="button" class="ud-btn-next" onclick="goStep(3)">Enter Passenger Details <i class="bi bi-chevron-right ms-1"></i></button>
                                </div>
                            </div>

                            {{-- ─── STEP 3: Passenger Details ─── --}}
                            <div class="ud-step-section" id="step-3">
                                <span class="ud-field-label">Step 3 — Passenger Details</span>
                                <div id="pax-fields-wrap"></div>
                                <div class="ud-nav-row">
                                    <button type="button" class="ud-btn-back" onclick="goStep(2)"><i class="bi bi-chevron-left"></i></button>
                                    <button type="button" class="ud-btn-next" onclick="goStep(4)">Contact Information <i class="bi bi-chevron-right ms-1"></i></button>
                                </div>
                            </div>

                            {{-- ─── STEP 4: Contact ─── --}}
                            <div class="ud-step-section" id="step-4">
                                <span class="ud-field-label">Step 4 — Contact Information</span>
                                <div class="mb-2">
                                    <input type="text" name="customer_name" class="ud-input" placeholder="Full Name" required>
                                </div>
                                <div class="mb-2">
                                    <input type="email" name="customer_email" class="ud-input" placeholder="Email Address" required>
                                </div>
                                <div class="mb-2">
                                    <input type="tel" name="customer_phone" class="ud-input" placeholder="Phone / WhatsApp" required>
                                </div>
                                <div class="ud-nav-row">
                                    <button type="button" class="ud-btn-back" onclick="goStep(3)"><i class="bi bi-chevron-left"></i></button>
                                    <button type="button" class="ud-btn-next" onclick="goStep(5)">Payment Method <i class="bi bi-chevron-right ms-1"></i></button>
                                </div>
                            </div>

                            {{-- ─── STEP 5: Payment ─── --}}
                            <div class="ud-step-section" id="step-5">
                                <span class="ud-field-label">Step 5 — Payment Method</span>
                                <div class="ud-gw-row mb-3">
                                    <label class="ud-gw-label">
                                        <input type="radio" name="payment_gateway" value="Card" checked onchange="updatePaymentUI()">
                                        <div class="ud-gw-btn"><i class="bi bi-credit-card d-block mb-1" style="font-size:16px;"></i>Card</div>
                                    </label>
                                    <label class="ud-gw-label">
                                        <input type="radio" name="payment_gateway" value="Tabby" onchange="updatePaymentUI()">
                                        <div class="ud-gw-btn"><i class="bi bi-calendar3 d-block mb-1" style="font-size:16px;"></i>Tabby</div>
                                    </label>
                                    <label class="ud-gw-label">
                                        <input type="radio" name="payment_gateway" value="Tamara" onchange="updatePaymentUI()">
                                        <div class="ud-gw-btn"><i class="bi bi-calendar3 d-block mb-1" style="font-size:16px;"></i>Tamara</div>
                                    </label>
                                </div>

                                {{-- Installments widget (hidden unless Tabby/Tamara) --}}
                                <div id="install-widget" style="display:none;">
                                    <div class="ud-install-box">
                                        <span class="ud-install-badge">0% Interest-Free</span>
                                        <div class="ud-install-total" id="install-total">AED 0</div>
                                        <div class="ud-install-arrow">↓</div>
                                        <div class="ud-install-plan">
                                            <div class="ud-install-cell">
                                                <span class="ic-label">4 payments</span>
                                                <span class="ic-val" id="install-monthly">AED 0</span>
                                                <span style="font-size:10px;color:#888;display:block;margin-top:2px;">/month</span>
                                            </div>
                                            <div class="ud-install-cell">
                                                <span class="ic-label">Today pay</span>
                                                <span class="ic-val" id="install-down">AED 0</span>
                                                <span style="font-size:10px;color:#888;display:block;margin-top:2px;">deposit</span>
                                            </div>
                                        </div>
                                        <p style="font-size:10.5px;color:#888;margin:10px 0 0;text-align:center;" id="install-gateway-label">Powered by Tabby</p>
                                    </div>
                                </div>

                                <div class="ud-nav-row">
                                    <button type="button" class="ud-btn-back" onclick="goStep(4)"><i class="bi bi-chevron-left"></i></button>
                                    <button type="button" class="ud-btn-next" onclick="goStep(6)">Review Booking <i class="bi bi-chevron-right ms-1"></i></button>
                                </div>
                            </div>

                            {{-- ─── STEP 6: Review ─── --}}
                            <div class="ud-step-section" id="step-6">
                                <span class="ud-field-label">Step 6 — Review Your Booking</span>

                                {{-- Review panel --}}
                                <div style="background:#111;border:1px solid #1e1e1e;border-radius:12px;padding:16px;margin-bottom:14px;">
                                    <div class="ud-review-row"><span>Package</span><span class="rv">{{ $package->title }}</span></div>
                                    <div class="ud-review-row"><span>Category</span><span class="rv" style="text-transform:capitalize;">{{ $package->category }}</span></div>
                                    <div class="ud-review-row"><span>Departure</span><span class="rv" id="rv-date">—</span></div>
                                    <div class="ud-review-row"><span>Hotel</span><span class="rv">{{ $package->hotels ? Str::limit($package->hotels, 40) : '—' }}</span></div>
                                    @if($package->category === 'air')
                                    <div class="ud-review-row"><span>Airline</span><span class="rv">{{ $package->airline ?: '—' }}</span></div>
                                    @else
                                    <div class="ud-review-row"><span>Transport</span><span class="rv">{{ $package->transport ? Str::limit($package->transport, 40) : '—' }}</span></div>
                                    @endif
                                    <div class="ud-review-row"><span>Visa</span><span class="rv" style="color:#22c55e;">✓ Included</span></div>
                                    <div class="ud-review-row"><span>Adults</span><span class="rv" id="rv-adults">1</span></div>
                                    <div class="ud-review-row"><span>Children</span><span class="rv" id="rv-children">0</span></div>
                                    <div class="ud-review-row"><span>Infants</span><span class="rv" id="rv-infants">0</span></div>
                                    <div class="ud-review-row"><span>Name</span><span class="rv" id="rv-name">—</span></div>
                                    <div class="ud-review-row"><span>Email</span><span class="rv" id="rv-email">—</span></div>
                                    <div class="ud-review-row"><span>Phone</span><span class="rv" id="rv-phone">—</span></div>
                                    <div class="ud-review-row"><span>Payment</span><span class="rv" id="rv-gateway">Card</span></div>
                                    <hr class="ud-sum-divider">
                                    <div class="ud-sum-total">
                                        <span class="tlabel">Total Amount</span>
                                        <span class="tval" id="rv-total">AED 0</span>
                                    </div>
                                </div>

                                <div class="ud-nav-row">
                                    <button type="button" class="ud-btn-back" onclick="goStep(5)"><i class="bi bi-chevron-left"></i></button>
                                    <button type="submit" class="ud-btn-next" id="book-btn">
                                        <i class="bi bi-cart-plus me-1"></i> Add to Cart
                                    </button>
                                </div>
                            </div>

                        </form>

                        {{-- Persistent compact summary (always visible) --}}
                        <div style="background:#111;border:1px solid #1a1a1a;border-radius:11px;padding:14px;margin-top:16px;" id="live-summary-box">
                            <p style="font-size:10.5px;color:#666;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;margin-bottom:10px;">Booking Summary</p>
                            <div class="ud-sum-row"><span>Package</span><span class="val">{{ $package->title }}</span></div>
                            <div class="ud-sum-row"><span>Category</span><span class="val" style="text-transform:capitalize;">{{ $package->category }}</span></div>
                            <div class="ud-sum-row"><span>Departure</span><span class="val" id="sum-date">Not selected</span></div>
                            <div class="ud-sum-row"><span>Hotel</span><span class="val">{{ $package->hotels ? Str::limit($package->hotels, 30) : '—' }}</span></div>
                            @if($package->category === 'air')
                            <div class="ud-sum-row"><span>Airline</span><span class="val">{{ $package->airline ?: '—' }}</span></div>
                            @else
                            <div class="ud-sum-row"><span>Transport</span><span class="val">{{ $package->transport ? Str::limit($package->transport, 30) : '—' }}</span></div>
                            @endif
                            <div class="ud-sum-row"><span>Visa</span><span class="val" style="color:#22c55e;">Included ✓</span></div>
                            <div class="ud-sum-row"><span>Adults</span><span class="val" id="sum-adults">1</span></div>
                            <div class="ud-sum-row"><span>Children</span><span class="val" id="sum-children">0</span></div>
                            <div class="ud-sum-row"><span>Infants</span><span class="val" id="sum-infants">0</span></div>
                            <hr class="ud-sum-divider">
                            <div class="ud-sum-total">
                                <span class="tlabel">Total</span>
                                <span class="tval" id="sum-total">AED {{ number_format($package->adult_price ?? $package->effectivePrice(), 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Trust Section --}}
                <div class="ud-panel mt-3">
                    <ul class="ud-trust-list">
                        <li><i class="bi bi-patch-check-fill"></i> Licensed Travel Agency · DTCM Registered</li>
                        <li><i class="bi bi-shield-lock-fill"></i> Secure Payment · SSL Encrypted Checkout</li>
                        <li><i class="bi bi-file-earmark-check-fill"></i> Visa Assistance Included in All Packages</li>
                        <li><i class="bi bi-lightning-charge-fill"></i> Instant Booking Confirmation by Email</li>
                        <li><i class="bi bi-headset"></i> 24×7 Customer Support on WhatsApp</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</main>

{{-- ─── Mobile Floating CTA ─── --}}
<div class="ud-mobile-cta">
    <div class="ud-mobile-price">
        <div class="mprice">
            @if($package->discount_price && $package->discount_price < $package->original_price)
                <span style="text-decoration: line-through; font-size: 12px; color: #888; margin-right: 4px;">AED {{ number_format($package->original_price, 0) }}</span>
                AED {{ number_format($package->starting_price, 0) }}
            @else
                AED {{ number_format($package->starting_price, 0) }}
            @endif
        </div>
        <div class="mlabel">per person · <span id="mob-date-label">No date selected</span></div>
    </div>
    <button class="ud-mobile-book-btn" onclick="openMobileModal()">
        Book Now
    </button>
</div>

{{-- ─── Mobile Booking Modal ─── --}}
<div class="ud-mobile-modal" id="mob-modal">
    <div class="ud-mobile-modal-inner">
        <div class="ud-modal-handle"></div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
            <h4 style="margin:0;font-size:18px;font-weight:800;color:#fff;">Book This Package</h4>
            <button onclick="closeMobileModal()" style="background:none;border:none;color:#888;font-size:22px;cursor:pointer;">✕</button>
        </div>
        <p style="font-size:13px;color:#888;margin-bottom:0;">Use the full desktop view to complete your booking, or scroll to the booking form above.</p>
        <a href="#bookingCardCol" onclick="closeMobileModal()" class="ud-btn-next d-block text-center mt-3" style="text-decoration:none;padding:13px;border-radius:9px;">
            Go to Booking Form
        </a>
    </div>
</div>

<script>
/* ═══════════════════════════════════════════════════════════
   DATA from PHP
═══════════════════════════════════════════════════════════ */
const activeDepartures = @json($departures->map(fn($d) => [
    'date'  => $d->departure_date->toDateString(),
    'seats' => $d->seats_available - $d->seats_booked
]));

const ADULT_PRICE = {{ $package->adult_price ?? $package->effectivePrice() }};
const CHILD_PRICE = {{ $package->child_price ?? ($package->effectivePrice() * 0.5) }};
const INFANT_PRICE = {{ $package->infant_price ?? 0 }};

let selectedDate = '';
let currentYear, currentMonth;
let pax = { adults: 1, children: 0, infants: 0 };
let currentStep = 1;

/* ═══════════════════════════════════════════════════════════
   TABS
═══════════════════════════════════════════════════════════ */
function switchTab(btn, panelId) {
    document.querySelectorAll('.ud-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.ud-tab-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(panelId).classList.add('active');
}

/* ═══════════════════════════════════════════════════════════
   FAQ
═══════════════════════════════════════════════════════════ */
function toggleFaq(btn) {
    const body = btn.nextElementSibling;
    const isOpen = btn.classList.contains('open');
    document.querySelectorAll('.ud-faq-btn').forEach(b => { b.classList.remove('open'); b.nextElementSibling.style.display = 'none'; });
    if (!isOpen) { btn.classList.add('open'); body.style.display = 'block'; }
}

/* ═══════════════════════════════════════════════════════════
   CALENDAR
═══════════════════════════════════════════════════════════ */
const MONTHS = ["January","February","March","April","May","June","July","August","September","October","November","December"];

function renderCalendar() {
    document.getElementById('cal-month-label').textContent = `${MONTHS[currentMonth]} ${currentYear}`;
    const grid = document.getElementById('cal-days');
    grid.innerHTML = '';

    const firstDay = new Date(currentYear, currentMonth, 1).getDay();
    const totalDays = new Date(currentYear, currentMonth + 1, 0).getDate();

    for (let i = 0; i < firstDay; i++) {
        const e = document.createElement('div'); e.className = 'ud-day other'; grid.appendChild(e);
    }

    for (let d = 1; d <= totalDays; d++) {
        const cell = document.createElement('div');
        const mm  = String(currentMonth + 1).padStart(2,'0');
        const dd  = String(d).padStart(2,'0');
        const iso = `${currentYear}-${mm}-${dd}`;
        const dep = activeDepartures.find(x => x.date === iso);

        if (dep && dep.seats > 0) {
            cell.className = 'ud-day avail' + (selectedDate === iso ? ' selected' : '');
            cell.title     = dep.seats + ' seats available';
            cell.innerHTML = d + '<span class="seats-dot"></span>';
            cell.onclick   = () => selectDate(iso, cell);
        } else {
            cell.className = 'ud-day grey';
            cell.textContent = d;
        }

        grid.appendChild(cell);
    }
}

function navigateMonth(dir) {
    currentMonth += dir;
    if (currentMonth < 0)  { currentMonth = 11; currentYear--; }
    if (currentMonth > 11) { currentMonth = 0;  currentYear++; }
    renderCalendar();
}

function selectDate(iso, cell) {
    selectedDate = iso;
    document.getElementById('departure_date').value = iso;
    document.querySelectorAll('.ud-day.avail').forEach(c => c.classList.remove('selected'));
    cell.classList.add('selected');

    // Enable step-1 next button
    document.getElementById('btn-step1-next').disabled = false;
    document.getElementById('btn-step1-next').textContent = '✓ Date Selected — Choose Passengers →';

    updateSummary();
    updateMobileDateLabel();
}

/* ═══════════════════════════════════════════════════════════
   PASSENGER COUNTER
═══════════════════════════════════════════════════════════ */
function updateCounter(type, delta) {
    pax[type] += delta;
    if (pax.adults   < 1)  pax.adults   = 1;
    if (pax.children < 0)  pax.children = 0;
    if (pax.infants  < 0)  pax.infants  = 0;
    if (pax[type]    > 20) pax[type]    = 20;

    document.getElementById(type + '-count').textContent = pax[type];
    updateSummary();
    if (currentStep === 3) generatePassengerFields();
}

/* ═══════════════════════════════════════════════════════════
   LIVE SUMMARY
═══════════════════════════════════════════════════════════ */
function updateSummary() {
    const total  = (pax.adults * ADULT_PRICE) + (pax.children * CHILD_PRICE) + (pax.infants * INFANT_PRICE);

    // Summary box
    document.getElementById('sum-adults').textContent   = pax.adults;
    document.getElementById('sum-children').textContent = pax.children;
    document.getElementById('sum-infants').textContent  = pax.infants;
    document.getElementById('sum-total').textContent    = 'AED ' + total.toLocaleString();

    if (selectedDate) {
        const opts = { day:'numeric', month:'short', year:'numeric' };
        document.getElementById('sum-date').textContent = new Date(selectedDate).toLocaleDateString('en-US', opts);
    }

    // Installment widget
    const monthly = (total / 4).toFixed(0);
    document.getElementById('install-total').textContent   = 'AED ' + total.toLocaleString();
    document.getElementById('install-monthly').textContent = 'AED ' + parseInt(monthly).toLocaleString();
    document.getElementById('install-down').textContent    = 'AED ' + parseInt(monthly).toLocaleString();
}

/* ═══════════════════════════════════════════════════════════
   PAYMENT UI
═══════════════════════════════════════════════════════════ */
function updatePaymentUI() {
    const gw = document.querySelector('input[name="payment_gateway"]:checked').value;
    const w  = document.getElementById('install-widget');
    if (gw === 'Card') {
        w.style.display = 'none';
    } else {
        w.style.display = 'block';
        document.getElementById('install-gateway-label').textContent = 'Powered by ' + gw;
    }
    updateSummary();
}

/* ═══════════════════════════════════════════════════════════
   MULTI-STEP WIZARD
═══════════════════════════════════════════════════════════ */
function goStep(n) {
    const alert = document.getElementById('ud-alert');
    alert.style.display = 'none';

    // Validate before advancing
    if (n > 1 && !selectedDate) {
        alert.textContent = '📅 Please select a departure date first.';
        alert.style.display = 'block'; goStep(1); return;
    }
    if (n > 2 && pax.adults < 1) {
        alert.textContent = '👥 At least 1 adult is required.';
        alert.style.display = 'block'; return;
    }
    if (n > 3) {
        // Validate passenger fields
        const paxInputs = document.querySelectorAll('#pax-fields-wrap input[required]');
        for (let inp of paxInputs) {
            if (!inp.value.trim()) {
                alert.textContent = '✏️ Please fill in all passenger details.';
                alert.style.display = 'block'; goStep(3); return;
            }
        }
    }
    if (n > 4) {
        const name  = document.querySelector('input[name="customer_name"]').value;
        const email = document.querySelector('input[name="customer_email"]').value;
        const phone = document.querySelector('input[name="customer_phone"]').value;
        if (!name || !email || !phone) {
            alert.textContent = '📋 Please complete your contact information.';
            alert.style.display = 'block'; goStep(4); return;
        }
    }

    // If entering step 3, build passenger fields
    if (n === 3) generatePassengerFields();

    // If entering step 6 (review), populate review values
    if (n === 6) populateReview();

    // Hide all, show target
    document.querySelectorAll('.ud-step-section').forEach(s => s.classList.remove('active'));
    document.getElementById('step-' + n).classList.add('active');

    // Update step indicator
    for (let i = 1; i <= 6; i++) {
        const si = document.getElementById('si-' + i);
        si.classList.remove('active','done');
        if (i < n)      si.classList.add('done');
        else if (i === n) si.classList.add('active');
    }

    currentStep = n;
}

/* ═══════════════════════════════════════════════════════════
   PASSENGER FIELDS
═══════════════════════════════════════════════════════════ */
function generatePassengerFields() {
    const wrap = document.getElementById('pax-fields-wrap');
    wrap.innerHTML = '';
    let idx = 0;

    const buildType = (count, label) => {
        for (let i = 0; i < count; i++) {
            const card = document.createElement('div');
            card.className = 'ud-pax-field-card';
            card.innerHTML = `
                <h6>${label} ${i+1}</h6>
                <div class="ud-pax-fields-grid">
                    <div class="full">
                        <input type="text" name="passenger_details[${idx}][name]" class="ud-input" placeholder="Full name (as in passport)" required>
                    </div>
                    <div>
                        <input type="text" name="passenger_details[${idx}][passport_no]" class="ud-input" placeholder="Passport No." required>
                    </div>
                    <div>
                        <input type="date" name="passenger_details[${idx}][dob]" class="ud-input" required>
                    </div>
                </div>
            `;
            wrap.appendChild(card);
            idx++;
        }
    };

    buildType(pax.adults,   'Adult');
    buildType(pax.children, 'Child');
    buildType(pax.infants,  'Infant');
}

/* ═══════════════════════════════════════════════════════════
   REVIEW POPULATE
═══════════════════════════════════════════════════════════ */
function populateReview() {
    const fmt = { day:'numeric', month:'short', year:'numeric' };
    const total = (pax.adults * ADULT_PRICE) + (pax.children * CHILD_PRICE) + (pax.infants * INFANT_PRICE);
    const gw = document.querySelector('input[name="payment_gateway"]:checked').value;

    document.getElementById('rv-date').textContent     = selectedDate ? new Date(selectedDate).toLocaleDateString('en-US', fmt) : '—';
    document.getElementById('rv-adults').textContent   = pax.adults;
    document.getElementById('rv-children').textContent = pax.children;
    document.getElementById('rv-infants').textContent  = pax.infants;
    document.getElementById('rv-name').textContent     = document.querySelector('input[name="customer_name"]').value  || '—';
    document.getElementById('rv-email').textContent    = document.querySelector('input[name="customer_email"]').value || '—';
    document.getElementById('rv-phone').textContent    = document.querySelector('input[name="customer_phone"]').value || '—';
    document.getElementById('rv-gateway').textContent  = gw;
    document.getElementById('rv-total').textContent    = 'AED ' + total.toLocaleString();
}

/* ═══════════════════════════════════════════════════════════
   SUBMIT
═══════════════════════════════════════════════════════════ */
function submitBooking(e) {
    e.preventDefault();
    const btn   = document.getElementById('book-btn');
    const alert = document.getElementById('ud-alert');

    btn.disabled = true;
    btn.textContent = 'Adding to Cart…';
    alert.style.display = 'none';

    const passengers = [];
    document.querySelectorAll('#pax-fields-wrap .ud-pax-field-card').forEach((card, i) => {
        const inputs = card.querySelectorAll('input');
        passengers.push({ name: inputs[0].value, passport_no: inputs[1].value, dob: inputs[2].value });
    });

    const payload = {
        departure_date: selectedDate,
        adults: pax.adults, children: pax.children, infants: pax.infants,
        customer_name:  document.querySelector('input[name="customer_name"]').value,
        customer_email: document.querySelector('input[name="customer_email"]').value,
        customer_phone: document.querySelector('input[name="customer_phone"]').value,
        payment_gateway: document.querySelector('input[name="payment_gateway"]:checked').value,
        passenger_details: passengers
    };

    fetch('{{ route('umrah-visas.checkout', $package->id) }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
        body: JSON.stringify(payload)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.checkout_url) {
            btn.textContent = 'Redirecting…';
            window.location.href = data.checkout_url;
        } else {
            alert.textContent = data.error || 'Something went wrong. Please try again.';
            alert.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-cart-plus me-1"></i> Add to Cart';
        }
    })
    .catch(() => {
        alert.textContent = 'Network error. Please check your connection.';
        alert.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-cart-plus me-1"></i> Add to Cart';
    });
}

/* ═══════════════════════════════════════════════════════════
   SCROLL HELPERS
═══════════════════════════════════════════════════════════ */
function scrollToBooking() {
    document.getElementById('bookingCardCol').scrollIntoView({ behavior: 'smooth' });
}

/* ═══════════════════════════════════════════════════════════
   MOBILE MODAL
═══════════════════════════════════════════════════════════ */
function openMobileModal()  { document.getElementById('mob-modal').classList.add('open'); }
function closeMobileModal() { document.getElementById('mob-modal').classList.remove('open'); }

function updateMobileDateLabel() {
    if (selectedDate) {
        const fmt = { day:'numeric', month:'short' };
        document.getElementById('mob-date-label').textContent = new Date(selectedDate).toLocaleDateString('en-US', fmt);
    }
}

/* ═══════════════════════════════════════════════════════════
   INIT
═══════════════════════════════════════════════════════════ */
window.addEventListener('DOMContentLoaded', () => {
    if (activeDepartures.length > 0) {
        const d = new Date(activeDepartures[0].date);
        currentYear  = d.getFullYear();
        currentMonth = d.getMonth();
    } else {
        const t = new Date();
        currentYear  = t.getFullYear();
        currentMonth = t.getMonth();
    }
    renderCalendar();
    updateSummary();
});
</script>

@include('footer')
