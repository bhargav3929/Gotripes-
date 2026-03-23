<!-- Logo + Text with Fade Transition -->
<div id="fade-overlay" style="
  position: fixed;
  left: 0; top: 0; right: 0; bottom: 0;
  width: 100vw; height: 100vh;
  background: #000;
  z-index: 99999;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  transition: opacity 0.8s;
">
  <img src="/assets/index_files/logo.png" alt="Loading..." style="height: 140px; width: auto; margin-bottom:30px;">
  <div style="text-align: center;">
    <div
      style="font-size: 1.75rem; font-weight: 700; color: #ffd235; letter-spacing: 3px; font-family: 'Outfit', sans-serif; text-transform: uppercase;">
      Welcome To GOTRIPS
    </div>
    <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-top:14px;">
      <span style="font-size: 0.95rem; font-weight: 400; color: rgba(255,255,255,0.6); font-family: 'Outfit', sans-serif; line-height: 1.3; letter-spacing: 1px;">
        a part of
      </span>
      <span
        style="font-size: 1.3rem; font-weight: 700; color: #D4AF37; font-family: 'Outfit', sans-serif; line-height: 1.3; letter-spacing: 1.5px;">
        Ayn Al Amir Tourism
      </span>
    </div>
  </div>
</div>

<style>
  #fade-overlay {
    opacity: 1;
    pointer-events: auto;
    transition: opacity 0.8s;
  }

  #fade-overlay.fade-hide {
    opacity: 0;
    pointer-events: none;
  }
</style>
<script>
  window.addEventListener('load', function () {
    var fade = document.getElementById('fade-overlay');
    if (fade) {
      fade.classList.add('fade-hide');
      setTimeout(function () {
        fade.style.display = 'none';
      }, 900); // matches fade transition time
    }
  });
</script>

<style>
  .nav-link2 {
    display: inline-block;
    margin: 0 8px;
    padding: 12px 30px;
    color: #ffd700;
    background: rgba(10, 10, 10, 0.8);
    border: 1px solid rgba(255, 215, 0, 0.3);
    border-radius: 50px;
    font-family: 'Outfit', sans-serif;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
    white-space: nowrap;
    backdrop-filter: blur(5px);
    font-size: 14px;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
  }

  .nav-link2:hover {
    background: rgba(255, 215, 0, 0.1);
    border-color: #ffd700;
    transform: translateY(-2px);
    color: #fff;
  }

  .nav-link2.active {
    background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
    color: #000;
    border: 1px solid transparent;
    box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
    transform: translateY(-2px);
    font-weight: 700;
  }

  .hero-section {
    position: relative;
    color: white;
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
  }

  .form-control,
  .form-select {
    border-radius: 10px;
  }

  .search-btn:hover {
    background-color: #fff;
  }

  .tab-section {
    display: none;
  }

  .tab-section.active {
    display: block;
  }

  .search-box {
    width: 100%;
    max-width: 600px;
    /* Or whichever max is best for your design */
    margin: 0 auto;
    /* Centers the box if the container is wider */
    height: auto;
    /* Use as needed */
  }

  .nav-container {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 10px;
    padding: 1rem;
  }

  a:hover {
    color: #fff;
  }

  @media (max-width: 768px) {
    .nav-link2 {
      padding: 7px 12px;
      font-size: 11px;
      margin: 2px;
    }

    .nav-container {
      gap: 4px;
      padding: 6px;
    }

    .hero-section {
      height: auto;
    }

    .search-box {
      width: 100%;
      padding: 0.5rem;
    }

    .container {
      padding: 0.5rem;
    }
  }

  @media (max-width: 480px) {
    .nav-link2 {
      font-size: 10px;
      padding: 6px 8px;
      margin: 2px;
    }

    .nav-container {
      gap: 3px;
      padding: 4px;
    }
  }
</style>

<!-- eSIM Promo TV Box Styles -->
<style>
  .esim-promo-tv {
    position: relative;
    cursor: pointer;
  }
  .esim-promo-inner {
    position: absolute;
    inset: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 12px 10px;
    background: linear-gradient(145deg, #0a0a0a 0%, #111 40%, #0d0d0d 100%);
    z-index: 2;
  }
  .esim-promo-inner::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
      radial-gradient(circle at 30% 20%, rgba(255,215,0,0.06) 0%, transparent 60%),
      radial-gradient(circle at 70% 80%, rgba(255,215,0,0.04) 0%, transparent 60%);
    pointer-events: none;
  }
  .esim-promo-icon {
    font-size: 22px;
    color: #FFD700;
    margin-bottom: 6px;
    position: relative;
    z-index: 1;
  }
  .esim-promo-title {
    font-family: 'Outfit', sans-serif;
    font-size: 11px;
    font-weight: 700;
    color: #FFD700;
    text-transform: uppercase;
    letter-spacing: 2px;
    line-height: 1.2;
    margin-bottom: 4px;
    position: relative;
    z-index: 1;
  }
  .esim-promo-subtitle {
    font-family: 'Outfit', sans-serif;
    font-size: 8px;
    font-weight: 400;
    color: rgba(255,255,255,0.5);
    letter-spacing: 0.5px;
    line-height: 1.4;
    max-width: 90%;
    position: relative;
    z-index: 1;
  }
  .esim-promo-cta {
    display: inline-block;
    margin-top: 8px;
    padding: 4px 14px;
    background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
    color: #000;
    font-family: 'Outfit', sans-serif;
    font-size: 7px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    border-radius: 50px;
    text-decoration: none;
    position: relative;
    z-index: 1;
    transition: all 0.3s ease;
  }
  .esim-promo-tv:hover .esim-promo-cta {
    box-shadow: 0 4px 12px rgba(255,215,0,0.3);
    transform: translateY(-1px);
  }
  .esim-promo-pulse {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 6px;
    height: 6px;
    background: #FFD700;
    border-radius: 50%;
    z-index: 3;
    animation: esimPulse 2s ease-in-out infinite;
  }
  @keyframes esimPulse {
    0%, 100% { opacity: 0.4; box-shadow: 0 0 0 0 rgba(255,215,0,0.4); }
    50% { opacity: 1; box-shadow: 0 0 0 4px rgba(255,215,0,0); }
  }
  @media (max-width: 768px) {
    .esim-promo-icon { font-size: 16px; margin-bottom: 3px; }
    .esim-promo-title { font-size: 8px; letter-spacing: 1px; }
    .esim-promo-subtitle { font-size: 6px; display: none; }
    .esim-promo-cta { font-size: 6px; padding: 3px 10px; margin-top: 4px; }
  }
  @media (max-width: 480px) {
    .esim-promo-icon { font-size: 14px; }
    .esim-promo-title { font-size: 7px; }
    .esim-promo-cta { font-size: 5px; padding: 2px 8px; margin-top: 3px; }
  }
</style>

<!-- Ad TV Boxes - Each box is a dedicated TV with rotating media -->
<div class="container ad-cards-wrapper">
  <div class="ad-grid">
    @if(isset($adSlots) && $adSlots->count() > 0)
      @php $slotIndex = 0; @endphp
      @foreach($adSlots as $slotNumber => $mediaItems)
        @php $slotIndex++; @endphp

        {{-- Insert eSIM promo as the 3rd window --}}
        @if($slotIndex === 3)
          <a href="/esim" class="ad-grid-item esim-promo-tv" style="text-decoration:none;color:inherit;display:block;">
            <div class="esim-promo-pulse"></div>
            <div class="esim-promo-inner">
              <div class="esim-promo-icon"><i class="fas fa-sim-card"></i></div>
              <div class="esim-promo-title">Travel eSIM</div>
              <div class="esim-promo-subtitle">Instant data plans for 186+ countries</div>
              <span class="esim-promo-cta">Get Yours</span>
            </div>
          </a>
        @endif

        @php $slotLink = $mediaItems->first()->linkUrl ?? null; @endphp
        @if($slotLink)
          <a href="{{ $slotLink }}" class="ad-grid-item ad-tv-box" data-tv="{{ $slotNumber }}" style="text-decoration:none;color:inherit;display:block;">
        @else
          <div class="ad-grid-item ad-tv-box" data-tv="{{ $slotNumber }}">
        @endif
          @foreach($mediaItems as $idx => $media)
            <div class="ad-tv-slide {{ $idx === 0 ? 'active' : '' }}"
                 data-type="{{ $media->mediaType }}"
                 data-duration="{{ $media->duration ?? 5 }}">
              @if($media->mediaType === 'video')
                <video muted playsinline preload="metadata" class="ad-carousel-media">
                  <source src="{{ asset($media->imgPath) }}" type="video/mp4">
                </video>
              @else
                <img src="{{ asset($media->imgPath) }}" alt="Ad Slot {{ $slotNumber }}" class="ad-carousel-media" loading="lazy">
              @endif
            </div>
          @endforeach
          {{-- Progress bar for this TV --}}
          @if($mediaItems->count() > 1)
            <div class="ad-tv-progress">
              <div class="ad-tv-progress-bar"></div>
            </div>
          @endif
        @if($slotLink)
          </a>
        @else
          </div>
        @endif
      @endforeach

      {{-- If fewer than 3 slots exist, append eSIM promo at the end --}}
      @if($slotIndex < 3)
        <a href="/esim" class="ad-grid-item esim-promo-tv" style="text-decoration:none;color:inherit;display:block;">
          <div class="esim-promo-pulse"></div>
          <div class="esim-promo-inner">
            <div class="esim-promo-icon"><i class="fas fa-sim-card"></i></div>
            <div class="esim-promo-title">Travel eSIM</div>
            <div class="esim-promo-subtitle">Instant data plans for 186+ countries</div>
            <span class="esim-promo-cta">Get Yours</span>
          </div>
        </a>
      @endif
    @else
      {{-- Fallback static images --}}
      <div class="ad-grid-item">
        <div class="ad-tv-slide active" data-type="image" data-duration="5">
          <img src="{{ asset('assets/homepageads/ad_flight.png') }}" alt="Flights" class="ad-carousel-media">
        </div>
      </div>
      <div class="ad-grid-item">
        <div class="ad-tv-slide active" data-type="image" data-duration="5">
          <img src="{{ asset('assets/homepageads/ad_hotel.png') }}" alt="Hotels" class="ad-carousel-media">
        </div>
      </div>
      {{-- eSIM promo in fallback position 3 --}}
      <a href="/esim" class="ad-grid-item esim-promo-tv" style="text-decoration:none;color:inherit;display:block;">
        <div class="esim-promo-pulse"></div>
        <div class="esim-promo-inner">
          <div class="esim-promo-icon"><i class="fas fa-sim-card"></i></div>
          <div class="esim-promo-title">Travel eSIM</div>
          <div class="esim-promo-subtitle">Instant data plans for 186+ countries</div>
          <span class="esim-promo-cta">Get Yours</span>
        </div>
      </a>
      <div class="ad-grid-item">
        <div class="ad-tv-slide active" data-type="image" data-duration="5">
          <img src="{{ asset('assets/homepageads/ad_tour.png') }}" alt="Tours" class="ad-carousel-media">
        </div>
      </div>
      <div class="ad-grid-item">
        <div class="ad-tv-slide active" data-type="image" data-duration="5">
          <img src="{{ asset('assets/homepageads/ad_flight.png') }}" alt="Flights" class="ad-carousel-media">
        </div>
      </div>
    @endif
  </div>
</div>

<!-- Navigation -->

<div class="nav-container" style="margin-top: 4px;">
  <a class="nav-link2 active" onclick="showTab('flights', event)"><i class="fas fa-plane"
      style="transform: rotate(-45deg); display: inline-block;"></i> Flights</a>
  <a class="nav-link2" onclick="showTab('hotels', event)"><i class="fas fa-hotel"></i> Hotels</a>
  <a class="nav-link2" onclick="showTab('cars', event)"><i class="fas fa-taxi"></i> Car Hire</a>
  <a class="nav-link2" onclick="showTab('flights+hotels', event)"><i class="fas fa-plane"></i> Flights + <i
      class="fas fa-hotel"></i> Hotels</a>
</div>

<!-- Tab Sections -->
<div class="container" style="width:100%;">
  <div id="flights" class="tab-section active hero-section">
    @include('banner1')
  </div>
  <div id="hotels" class="tab-section hero-section">
    @include('banner2')
  </div>
  <div id="cars" class="tab-section hero-section">
    @include('banner3')
  </div>
  <div id="flights+hotels" class="tab-section hero-section">
    @include('banner4')
  </div>
</div>

<style>
  .ad-cards-wrapper {
    margin-top: 0;
    margin-bottom: 10px;
    padding: 0 20px;
    position: relative;
    z-index: 3;
    max-width: 1400px;
    margin-left: auto;
    margin-right: auto;
    box-sizing: border-box;
  }

  .ad-grid {
    display: flex;
    justify-content: center;
    gap: 10px;
    width: 100%;
  }

  .ad-grid-item {
    flex: 1 1 0;
    min-width: 0;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #C9A227;
    background: #000;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    position: relative;
    height: 160px;
  }

  .ad-grid-item:hover {
    border: 2px solid #C9A227;
    box-shadow: 0 8px 25px rgba(201, 162, 39, 0.35),
      0 0 0 1px rgba(201, 162, 39, 0.5);
  }

  /* TV Slide system */
  .ad-tv-slide {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 0.8s ease-in-out;
    z-index: 1;
  }

  .ad-tv-slide.active {
    opacity: 1;
    z-index: 2;
  }

  .ad-carousel-media {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  /* Progress bar at bottom of each TV */
  .ad-tv-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: rgba(255, 255, 255, 0.2);
    z-index: 10;
  }

  .ad-tv-progress-bar {
    height: 100%;
    background: linear-gradient(90deg, #FFD700, #D4AF37);
    width: 0%;
    transition: width linear;
  }

  @media (max-width: 768px) {
    .ad-cards-wrapper {
      margin-top: 0 !important;
      margin-bottom: 6px !important;
      padding: 6px 6px 0 !important;
      background: #000;
      box-sizing: border-box;
    }

    .ad-grid {
      gap: 5px !important;
      flex-wrap: nowrap !important;
      justify-content: center !important;
    }

    .ad-grid-item {
      flex: 1 1 0 !important;
      min-width: 0 !important;
      height: 75px !important;
      min-height: 75px !important;
      border-radius: 8px !important;
      border-width: 1px !important;
      box-shadow: 0 2px 6px rgba(0,0,0,0.3) !important;
    }

    .ad-grid-item:hover {
      border: 1px solid #C9A227 !important;
      box-shadow: none !important;
      transform: none !important;
    }
  }

  @media (max-width: 480px) {
    .ad-cards-wrapper {
      padding: 5px 4px 0 !important;
    }

    .ad-grid {
      gap: 4px !important;
    }

    .ad-grid-item {
      height: 65px !important;
      min-height: 65px !important;
      border-radius: 6px !important;
    }
  }

  @media (max-width: 360px) {
    .ad-grid-item {
      height: 55px !important;
      min-height: 55px !important;
      border-radius: 5px !important;
    }
  }
</style>

<!-- Subtle Rotating Single Ad Strip -->
<div class="rotating-ad-strip" style="
  width: 100%;
  max-width: 600px;
  margin: 8px auto 0;
  text-align: center;
  min-height: 40px;
  position: relative;
">
  <div class="ad-item active" style="animation: adFadeInOut 4s ease-in-out infinite;">
    <a href="#"
      style="color: #FFD700; font-family: 'Outfit', sans-serif; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
      <i class="fas fa-tag" style="font-size: 12px;"></i>
      <span><strong>Special Offer:</strong> Book flights & get 15% off hotels • <u>Learn More</u></span>
    </a>
  </div>
</div>

<style>
  @keyframes adFadeInOut {

    0%,
    100% {
      opacity: 0.4;
    }

    50% {
      opacity: 1;
    }
  }

  .rotating-ad-strip a:hover {
    color: #fff !important;
  }

  @media (max-width: 768px) {
    .rotating-ad-strip {
      display: none;
    }
  }
</style>

<script>
  function showTab(tabId, event) {
    const tabs = ['flights', 'hotels', 'cars', 'flights+hotels'];
    tabs.forEach(id => {
      document.getElementById(id).classList.remove('active');
    });
    document.getElementById(tabId).classList.add('active');
    // Update nav active class
    document.querySelectorAll('.nav-link2').forEach(link => link.classList.remove('active'));
    event.target.classList.add('active');
  }
</script>
<script>
  window.addEventListener('load', function () {
    var loader = document.getElementById('loader-overlay');
    if (loader) {
      loader.classList.add('loader-hide');
      setTimeout(function () {
        loader.style.display = 'none';
      }, 600);
    }
  });
</script>

{{-- Airport TV Slideshow Engine --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
  var tvBoxes = document.querySelectorAll('.ad-tv-box');

  tvBoxes.forEach(function(tvBox) {
    var slides = tvBox.querySelectorAll('.ad-tv-slide');
    if (slides.length <= 1) return; // No rotation needed for single item

    var progressBar = tvBox.querySelector('.ad-tv-progress-bar');
    var currentIndex = 0;
    var timer = null;

    function showSlide(index) {
      slides.forEach(function(s) { s.classList.remove('active'); });
      // Pause all videos in this TV
      slides.forEach(function(s) {
        var vid = s.querySelector('video');
        if (vid) { vid.pause(); vid.currentTime = 0; }
      });

      slides[index].classList.add('active');
      var slide = slides[index];
      var type = slide.getAttribute('data-type');
      var duration = parseInt(slide.getAttribute('data-duration')) || 5;

      if (type === 'video') {
        var video = slide.querySelector('video');
        if (video) {
          video.currentTime = 0;
          video.muted = true;
          video.play().catch(function() {});

          // Progress bar follows video duration
          if (progressBar) {
            progressBar.style.transition = 'none';
            progressBar.style.width = '0%';
          }

          video.addEventListener('timeupdate', function onTimeUpdate() {
            if (video.duration && progressBar) {
              var pct = (video.currentTime / video.duration) * 100;
              progressBar.style.transition = 'none';
              progressBar.style.width = pct + '%';
            }
          });

          video.addEventListener('ended', function onEnded() {
            video.removeEventListener('ended', onEnded);
            video.removeEventListener('timeupdate', arguments.callee);
            nextSlide();
          });

          // Fallback: if video is too long or fails, skip after 30s
          timer = setTimeout(function() { nextSlide(); }, 30000);
        } else {
          scheduleNext(duration);
        }
      } else {
        // Image: show for `duration` seconds with progress bar
        if (progressBar) {
          progressBar.style.transition = 'none';
          progressBar.style.width = '0%';
          // Force reflow
          void progressBar.offsetWidth;
          progressBar.style.transition = 'width ' + duration + 's linear';
          progressBar.style.width = '100%';
        }
        scheduleNext(duration);
      }
    }

    function scheduleNext(seconds) {
      clearTimeout(timer);
      timer = setTimeout(function() { nextSlide(); }, seconds * 1000);
    }

    function nextSlide() {
      clearTimeout(timer);
      currentIndex = (currentIndex + 1) % slides.length;
      showSlide(currentIndex);
    }

    // Start the first slide
    showSlide(0);
  });

});
</script>

