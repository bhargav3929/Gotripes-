@php
    $modalLogo = (isset($company) && $company && $company->logo)
        ? asset('storage/' . $company->logo)
        : asset('assets/index_files/logo.png');
    $modalName = (isset($company) && $company && $company->name) ? $company->name : 'Go Trips';
    $modalEmirates = $activeEmirates->map(fn ($emirate) => [
        'id' => $emirate->emiratesName,
        'name' => $emirate->emiratesName,
        'image' => $emirate->emiratesImage ? asset($emirate->emiratesImage) : null,
    ])->values();

    // The disc is fixed, so the three-circle geometry only holds if the circles
    // shrink as more emirates start issuing visas. Two routes = the layout the
    // client signed off on; more than two step down so nothing touches the ring.
    // Re-fitted Sep 2026 when the outer ring grew from 2.2% to 8.5% of the disc
    // (client: match the logo's ring thickness) — the usable inner area shrank,
    // so every circle steps down a notch and the row gap tightens.
    $modalCount = $modalEmirates->count();
    $ringScale = $modalCount <= 2 ? '.235' : ($modalCount === 3 ? '.19' : '.16');
    $gapScale  = $modalCount <= 2 ? '.08'  : ($modalCount === 3 ? '.045' : '.03');
@endphp

@include('partials.gold-medallion-tokens')
<style>
/* Emirates route selector — medallion layout per client reference (Sep 2026):
   one large gold-ringed disc, three equal circles (brand on top, routes below),
   caption inside the disc. All geometry derives from --disc so the whole thing
   scales as one unit and never spills outside the circle. */
.emirate-overlay{position:fixed;inset:0;z-index:11000;display:flex;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,.9);backdrop-filter:blur(10px);opacity:0;visibility:hidden;transition:opacity .25s,visibility .25s}
.emirate-overlay.active{opacity:1;visibility:visible}
/* Ring thickness: the company logo's gold ring measures ~10% of its diameter
   (27px of 256px). 8.5% of the disc reads as the same weight on screen once the
   inner hairline is added (client feedback, 15 Sep 2026). --ring-w is the one
   knob; every ring in the medallion derives from it. The ring is painted as a
   gradient on the border-box (black disc on the padding-box) so it carries the
   brushed, light-top / dark-side bevel of the logo instead of a flat band. */
.emirate-modal{--disc:min(720px,94vw,88vh);--ring-w:calc(var(--disc) * var(--gm-ring-ratio,.085));--ring:calc(var(--disc) * var(--ring-scale,.235));--gold:var(--gm-gold,#d4af37);--gold-lite:var(--gm-gold-lite,#f0cf6b);--gold-deep:var(--gm-gold-deep,#8f6a16);--gold-metal:var(--gm-gold-metal);position:relative;width:var(--disc);height:var(--disc);border-radius:50%;border:var(--ring-w) solid transparent;background:var(--gm-disc-fill) padding-box,var(--gold-metal) border-box;box-shadow:0 30px 90px rgba(0,0,0,.85),0 0 60px rgba(212,175,55,.18),inset 0 0 0 1px rgba(60,42,8,.9);display:grid;grid-template-rows:auto auto auto;align-content:center;justify-items:center;row-gap:calc(var(--disc) * .03);font-family:'Outfit',sans-serif;transform:scale(.92);transition:transform .35s cubic-bezier(.34,1.56,.64,1)}
.emirate-overlay.active .emirate-modal{transform:scale(1)}
/* Thin concentric hairline just inside the band — the reference shows a double
   gold outline, not one band. Positioned from the padding edge (inset:0 is the
   inner edge of the border), so it tracks --ring-w automatically. */
.emirate-modal::before{content:'';position:absolute;inset:calc(var(--disc) * .012);border:2px solid rgba(212,175,55,.6);border-radius:50%;pointer-events:none}
/* Outer rim highlight so the band reads as raised metal, like the logo's edge. */
.emirate-modal::after{content:'';position:absolute;inset:calc(var(--ring-w) * -1);border-radius:50%;pointer-events:none;box-shadow:inset 0 0 0 2px rgba(255,240,180,.35),inset 0 0 0 4px rgba(90,64,10,.35)}
.emirate-brand-medallion,.emirate-card{width:var(--ring);height:var(--ring);border-radius:50%;background:#050505;box-shadow:0 10px 26px rgba(0,0,0,.6)}
/* Route circles use the same ring ratio as the disc (≈ logo weight). */
.emirate-card{border:calc(var(--ring) * .085) solid transparent;background:#050505 padding-box,var(--gold-metal) border-box;box-shadow:0 10px 26px rgba(0,0,0,.6)}
.emirate-brand-medallion{position:relative;overflow:hidden}
.emirate-logo{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.emirate-cards-row{position:relative;display:flex;flex-wrap:wrap;justify-content:center;align-items:center;gap:calc(var(--disc) * var(--gap-scale,.09))}
/* Divider between the two route circles, as drawn in the reference. */
.emirate-cards-row[data-count="2"]::before{content:'';position:absolute;top:8%;bottom:8%;left:50%;width:1px;transform:translateX(-.5px);background:linear-gradient(transparent,rgba(212,175,55,.85),transparent)}
.emirate-card{position:relative;overflow:hidden;padding:0;cursor:pointer;color:#fff;transition:transform .2s,box-shadow .2s;animation:emirateHalo 3.4s ease-in-out infinite}
.emirate-card:nth-of-type(2){animation-delay:1.7s}
/* drop-shadow, not box-shadow: the card clips its own overflow, so a spread
   shadow would be cut off at the rim. */
@keyframes emirateHalo{0%,100%{filter:drop-shadow(0 0 0 rgba(212,175,55,0))}50%{filter:drop-shadow(0 0 13px rgba(212,175,55,.85))}}
/* Slow drift on the photo itself, so the motion lives inside the circle and
   never fights the hover transform on the card. */
.emirate-flag-img{animation:emirateDrift 11s ease-in-out infinite}
.emirate-card:nth-of-type(2) .emirate-flag-img{animation-delay:-5.5s}
@keyframes emirateDrift{0%,100%{transform:scale(1)}50%{transform:scale(1.09)}}
.emirate-card:hover,.emirate-card:focus-visible{transform:translateY(-4px);box-shadow:0 16px 34px rgba(0,0,0,.6),0 0 0 3px var(--gold-lite)}
.emirate-card.selected{box-shadow:0 0 0 3px #0a0a0a,0 0 0 7px var(--gold-lite)}
.emirate-flag-img,.emirate-card-icon{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.emirate-card-icon{display:grid;place-items:center;background:linear-gradient(135deg,#222,#050505);color:var(--gold);font-size:calc(var(--disc) * .05);font-weight:800;letter-spacing:.04em}
.emirate-card::after{content:'';position:absolute;inset:34% 0 0;background:linear-gradient(transparent 0,rgba(0,0,0,.88) 62%,rgba(0,0,0,.7) 100%)}
/* Sits at ~20% from the bottom, not on the rim: a circle's chord is far
   narrower near its base, which is where long names like "Abu Dhabi" spill. */
.emirate-card-name{position:absolute;z-index:2;left:8%;right:8%;bottom:20%;font-size:calc(var(--disc) * .03);line-height:1.12;font-weight:800;letter-spacing:.04em;text-transform:uppercase;text-align:center;text-shadow:0 2px 6px #000}
.emirate-modal-title{margin:calc(var(--disc) * -.008) 0 0;font-size:calc(var(--disc) * .033);font-weight:800;letter-spacing:.045em;text-transform:uppercase;color:var(--gold);text-align:center;white-space:nowrap}
.emirate-modal-title b{color:#fff;font-weight:800}
@media(max-width:620px){.emirate-modal{--disc:min(94vw,78vh);row-gap:calc(var(--disc) * .03)}.emirate-modal-title{font-size:calc(var(--disc) * .030);letter-spacing:.02em}}
@media(prefers-reduced-motion:reduce){.emirate-overlay,.emirate-modal,.emirate-card{transition:none}.emirate-card,.emirate-flag-img{animation:none}}
</style>

<div id="emirateSelectorOverlay" class="emirate-overlay" role="dialog" aria-modal="true" aria-labelledby="emirateModalTitle">
    <div class="emirate-modal" style="--ring-scale:{{ $ringScale }};--gap-scale:{{ $gapScale }}">
        <div class="emirate-brand-medallion" aria-hidden="true">
            <img src="{{ $modalLogo }}" alt="" class="emirate-logo">
        </div>

        <div class="emirate-cards-row" id="emirateGrid" data-count="{{ $modalEmirates->count() }}" aria-label="Available Emirates visa routes"></div>

        <h2 class="emirate-modal-title" id="emirateModalTitle">Which <b>Emirates</b> visa route ?</h2>
    </div>
</div>

<script>
(function(){
    const available=@json($modalEmirates);
    const overlay=document.getElementById('emirateSelectorOverlay');
    const grid=document.getElementById('emirateGrid');
    const hiddenInput=document.getElementById('selectedEmirate');
    if(!overlay||!grid)return;

    const escapeHtml=value=>String(value??'').replace(/[&<>'"]/g,char=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[char]));
    grid.innerHTML=available.map(emirate=>{
        const name=escapeHtml(emirate.name),id=escapeHtml(emirate.id),image=emirate.image?escapeHtml(emirate.image):'';
        // Bootstrap Icons' webfont 404s on production, so the no-image fallback
        // is the emirate's initial rather than a glyph that renders as a box.
        const visual=image
            ?`<img class="emirate-flag-img" src="${image}" alt="" loading="lazy">`
            :`<span class="emirate-card-icon" aria-hidden="true">${name.slice(0,1)}</span>`;
        return `<button type="button" class="emirate-card" data-emirate="${id}" aria-label="Select ${name}">${visual}<span class="emirate-card-name">${name}</span></button>`;
    }).join('');

    function closeSelector(){overlay.classList.remove('active');document.body.style.overflow=''}
    function selectEmirate(name){
        if(hiddenInput)hiddenInput.value=name;
        document.dispatchEvent(new CustomEvent('emirateChanged',{detail:name}));
        setTimeout(closeSelector,180);
    }
    grid.querySelectorAll('.emirate-card').forEach(card=>card.addEventListener('click',function(){
        grid.querySelectorAll('.emirate-card').forEach(item=>{item.classList.remove('selected');item.removeAttribute('aria-pressed')});
        this.classList.add('selected');this.setAttribute('aria-pressed','true');selectEmirate(this.dataset.emirate);
    }));
    // Picking a route is mandatory — the rest of the form is priced off it, so
    // there is deliberately no close button, no backdrop dismiss and no Escape.
    // Tab still moves between the routes and Enter selects, so it is not a trap.
    window.showEmirateSelector=function(){
        // Nothing to choose from (no emirate has an active package) would leave
        // the customer staring at an empty disc she cannot dismiss. Stay shut.
        if(!grid.querySelector('.emirate-card'))return;
        overlay.classList.add('active');
        document.body.style.overflow='hidden';
        grid.querySelectorAll('.emirate-card').forEach(item=>{item.classList.remove('selected');item.removeAttribute('aria-pressed')});
        setTimeout(()=>grid.querySelector('.emirate-card')?.focus(),50);
    };
})();
</script>
