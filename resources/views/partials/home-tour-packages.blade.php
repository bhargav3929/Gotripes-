{{-- Homepage: live tour packages. A bookable package opens the booking wizard
     directly (visa check first); the rest open their detail page. --}}
@if($tourPackages->isNotEmpty())
<section class="htp" aria-labelledby="htpTitle">
  <div class="htp-wrap">
    <div class="htp-head">
      <div>
        <p class="htp-kicker">Book online</p>
        <h2 class="htp-title" id="htpTitle">Tour packages</h2>
      </div>
      <a class="htp-all" href="{{ route('tour-packages') }}">All destinations <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="htp-row">
      @foreach($tourPackages as $pkg)
        @php
          $img = $pkg->image ?: ($pkg->galleryImages()[0] ?? null);
          $imgUrl = $img ? (str_starts_with($img, 'http') ? $img : asset($img)) : null;
          $from = $pkg->cardFromPrice();
          $online = $pkg->isBookableOnline();
        @endphp
        <a class="htp-card" href="{{ $pkg->bookingUrl() }}">
          <span class="htp-img" @if($imgUrl) style="background-image:url('{{ $imgUrl }}')" @endif>
            @if($online)<span class="htp-badge"><i class="bi bi-shield-check"></i> Visa check + book</span>@endif
          </span>
          <span class="htp-body">
            <span class="htp-country">{{ $pkg->country }}</span>
            <span class="htp-name">{{ $pkg->title }}</span>
            @if($pkg->duration)<span class="htp-dur"><i class="bi bi-clock"></i> {{ $pkg->duration }}</span>@endif
            <span class="htp-foot">
              <span class="htp-price">@if($from)<small>From</small>AED {{ number_format($from, 0) }}@endif</span>
              <span class="htp-cta">{{ $online ? 'Book now' : 'View' }} <i class="bi bi-arrow-right"></i></span>
            </span>
          </span>
        </a>
      @endforeach
    </div>
  </div>
</section>

<style>
  .htp { background:#0d0c0a; padding:56px 0 64px; }
  .htp-wrap { max-width:1240px; margin:0 auto; padding:0 16px; }
  .htp-head { display:flex; justify-content:space-between; align-items:flex-end; gap:16px; margin-bottom:24px; }
  .htp-kicker { margin:0 0 6px; color:#E8BC3F; font-size:13px; font-weight:600; letter-spacing:.14em; text-transform:uppercase; }
  .htp-title { margin:0; color:#F4EFE3; font-family:'Outfit',sans-serif; font-size:clamp(28px,3.4vw,40px); font-weight:800; line-height:1.05; letter-spacing:-.02em; }
  .htp-all { flex:none; display:inline-flex; align-items:center; gap:8px; color:#E8BC3F; font-size:15px; font-weight:600; text-decoration:none; transition:gap .15s; }
  .htp-all:hover { color:#F2C94F; gap:12px; }
  .htp-row { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; }
  .htp-card { display:flex; flex-direction:column; min-width:0; border-radius:16px; overflow:hidden; background:#16140f; border:1px solid rgba(255,255,255,.08); text-decoration:none; color:inherit; transition:transform .2s cubic-bezier(.2,.7,.2,1), border-color .2s; }
  .htp-card:hover { transform:translateY(-4px); border-color:rgba(232,188,63,.5); }
  .htp-card:focus-visible { outline:2px solid #E8BC3F; outline-offset:3px; }
  .htp-img { position:relative; display:block; aspect-ratio:4/3; background:#1f1c16 center/cover no-repeat; }
  .htp-badge { position:absolute; left:12px; top:12px; display:inline-flex; align-items:center; gap:6px; padding:6px 10px; border-radius:16px; background:rgba(13,12,10,.82); color:#E8BC3F; font-size:12px; font-weight:600; -webkit-backdrop-filter:blur(8px); backdrop-filter:blur(8px); }
  .htp-body { display:flex; flex-direction:column; gap:6px; flex:1; padding:16px; }
  .htp-country { color:#E8BC3F; font-size:12px; font-weight:600; letter-spacing:.12em; text-transform:uppercase; }
  .htp-name { color:#F4EFE3; font-size:18px; font-weight:700; line-height:1.25; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
  .htp-dur { color:#948E80; font-size:14px; }
  .htp-foot { display:flex; justify-content:space-between; align-items:flex-end; gap:12px; margin-top:auto; padding-top:12px; border-top:1px solid rgba(255,255,255,.08); }
  .htp-price { color:#F4EFE3; font-size:18px; font-weight:700; font-variant-numeric:tabular-nums; }
  .htp-price small { display:block; color:#948E80; font-size:12px; font-weight:400; }
  .htp-cta { display:inline-flex; align-items:center; gap:6px; color:#E8BC3F; font-size:14px; font-weight:600; white-space:nowrap; }
  @media (max-width: 991px) { .htp-row { grid-template-columns:repeat(2,minmax(0,1fr)); } }
  @media (max-width: 575px) {
    .htp { padding:40px 0 48px; }
    .htp-row { display:flex; overflow-x:auto; scroll-snap-type:x mandatory; gap:12px; margin:0 -16px; padding:0 16px 8px; scrollbar-width:none; }
    .htp-row::-webkit-scrollbar { display:none; }
    .htp-card { flex:0 0 78%; scroll-snap-align:start; }
    .htp-all { font-size:14px; }
  }
  @media (prefers-reduced-motion: reduce) { .htp-card, .htp-all { transition:none; } }
</style>
@endif
