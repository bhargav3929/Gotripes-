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
@endphp

<style>
.emirate-overlay{position:fixed;inset:0;z-index:11000;display:flex;align-items:center;justify-content:center;padding:18px;background:rgba(0,0,0,.88);backdrop-filter:blur(10px);opacity:0;visibility:hidden;transition:opacity .25s,visibility .25s}.emirate-overlay.active{opacity:1;visibility:visible}.emirate-modal{position:relative;width:min(720px,94vw);aspect-ratio:1;background:radial-gradient(circle at 50% 42%,#151515 0,#070707 62%,#000 100%);border:clamp(8px,1.8vw,18px) solid #d4af37;border-radius:50%;box-shadow:0 30px 90px rgba(0,0,0,.85),0 0 0 3px #7b5910 inset,0 0 45px rgba(212,175,55,.12) inset;display:grid;grid-template-rows:1fr 1fr;align-items:center;justify-items:center;padding:8% 9% 9%;transform:scale(.92);transition:transform .35s cubic-bezier(.34,1.56,.64,1);overflow:hidden;font-family:'Outfit',sans-serif}.emirate-overlay.active .emirate-modal{transform:scale(1)}.emirate-close-btn{position:absolute;top:6.5%;right:9%;z-index:4;width:34px;height:34px;border:1px solid rgba(255,215,0,.45);border-radius:50%;background:#111;color:#d4af37;font-size:22px;line-height:1;display:grid;place-items:center;cursor:pointer;transition:.2s}.emirate-close-btn:hover{background:#d4af37;color:#111;transform:rotate(90deg)}.emirate-brand-medallion,.emirate-card{width:clamp(150px,25vw,220px);aspect-ratio:1;border:clamp(5px,.8vw,9px) solid #d4af37;border-radius:50%;background:#050505;box-shadow:0 8px 24px rgba(0,0,0,.55),0 0 0 2px rgba(255,235,150,.25) inset}.emirate-brand-medallion{grid-row:1;align-self:end;display:grid;place-items:center;padding:11px}.emirate-logo{display:block;width:82%;height:82%;object-fit:contain}.emirate-cards-grid{grid-row:2;align-self:start;width:100%;display:flex;justify-content:center;gap:clamp(24px,7vw,74px);margin-top:3.5%}.emirate-card{position:relative;overflow:hidden;padding:0;cursor:pointer;color:#fff;transition:transform .2s,box-shadow .2s}.emirate-card:hover,.emirate-card:focus-visible{transform:translateY(-5px);box-shadow:0 14px 34px rgba(212,175,55,.25),0 0 0 3px #fff2ac}.emirate-card.selected{box-shadow:0 0 0 4px #fff,0 0 0 8px #d4af37}.emirate-flag-img,.emirate-card-icon{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}.emirate-card-icon{display:grid;place-items:center;background:linear-gradient(135deg,#222,#050505);color:#d4af37;font-size:30px}.emirate-card::after{content:'';position:absolute;inset:45% 0 0;background:linear-gradient(transparent,rgba(0,0,0,.92))}.emirate-card-name{position:absolute;z-index:2;left:9%;right:9%;bottom:10%;font-size:clamp(16px,2.2vw,25px);font-weight:900;letter-spacing:1.5px;text-transform:uppercase;text-shadow:0 2px 5px #000}.emirate-card-hover-hint{display:none}.emirate-modal-title{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}
@media(max-height:780px){.emirate-modal{width:min(620px,88vh)}}
@media(max-width:620px){.emirate-modal{width:min(94vw,520px);padding:10% 7%}.emirate-brand-medallion,.emirate-card{width:clamp(108px,31vw,158px)}.emirate-cards-grid{gap:15px}.emirate-close-btn{top:7%;right:10%;width:30px;height:30px}.emirate-card-name{font-size:clamp(13px,4vw,18px);letter-spacing:.8px}}
@media(max-width:390px){.emirate-modal{padding:12% 5%}.emirate-brand-medallion,.emirate-card{width:105px}.emirate-cards-grid{gap:10px}}
@media(prefers-reduced-motion:reduce){.emirate-overlay,.emirate-modal,.emirate-card{transition:none}}
</style>

<div id="emirateSelectorOverlay" class="emirate-overlay" role="dialog" aria-modal="true" aria-labelledby="emirateModalTitle">
    <div class="emirate-modal">
        <h2 class="emirate-modal-title" id="emirateModalTitle">Choose your Emirates visa route</h2>
        <button type="button" class="emirate-close-btn" id="emirateCloseBtn" aria-label="Close modal">&times;</button>

        <div class="emirate-brand-medallion" aria-label="{{ $modalName }}">
            <img src="{{ $modalLogo }}" alt="{{ $modalName }}" class="emirate-logo">
        </div>

        <div class="emirate-cards-grid" id="emirateGrid" aria-label="Available Emirates visa routes"></div>
    </div>
</div>

<script>
(function(){
    const available=@json($modalEmirates);
    const overlay=document.getElementById('emirateSelectorOverlay');
    const grid=document.getElementById('emirateGrid');
    const closeButton=document.getElementById('emirateCloseBtn');
    const hiddenInput=document.getElementById('selectedEmirate');
    if(!overlay||!grid)return;

    const escapeHtml=value=>String(value??'').replace(/[&<>'"]/g,char=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[char]));
    grid.innerHTML=available.map(emirate=>{
        const name=escapeHtml(emirate.name),id=escapeHtml(emirate.id),image=emirate.image?escapeHtml(emirate.image):'';
        const visual=image
            ?`<img class="emirate-flag-img" src="${image}" alt="" loading="lazy">`
            :'<span class="emirate-card-icon"><i class="bi bi-flag-fill" aria-hidden="true"></i></span>';
        return `<button type="button" class="emirate-card" data-emirate="${id}" aria-label="Select ${name}">${visual}<span class="emirate-card-name">${name}</span></button>`;
    }).join('');

    function closeSelector(){overlay.classList.remove('active')}
    function selectEmirate(name){
        if(hiddenInput)hiddenInput.value=name;
        document.dispatchEvent(new CustomEvent('emirateChanged',{detail:name}));
        setTimeout(closeSelector,180);
    }
    grid.querySelectorAll('.emirate-card').forEach(card=>card.addEventListener('click',function(){
        grid.querySelectorAll('.emirate-card').forEach(item=>{item.classList.remove('selected');item.removeAttribute('aria-pressed')});
        this.classList.add('selected');this.setAttribute('aria-pressed','true');selectEmirate(this.dataset.emirate);
    }));
    closeButton?.addEventListener('click',closeSelector);
    overlay.addEventListener('click',event=>{if(event.target===overlay)closeSelector()});
    document.addEventListener('keydown',event=>{if(event.key==='Escape'&&overlay.classList.contains('active'))closeSelector()});
    window.showEmirateSelector=function(){
        overlay.classList.add('active');
        grid.querySelectorAll('.emirate-card').forEach(item=>{item.classList.remove('selected');item.removeAttribute('aria-pressed')});
        setTimeout(()=>grid.querySelector('.emirate-card')?.focus(),50);
    };
})();
</script>
