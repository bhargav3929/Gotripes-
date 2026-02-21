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
      padding: 8px 14px;
      font-size: 14px;
      margin: 5px;
    }

    .hero-section {
      height: auto;
    }

    .search-box {
      width: 100%;
      padding: 1rem;
    }

    .container {
      padding: 1rem;
    }
  }

  @media (max-width: 480px) {
    .nav-link2 {
      font-size: 12px;
      padding: 6px 10px;
    }
  }
</style>

<!-- Ad TV Boxes - Each box is a dedicated TV with rotating media -->
<div class="container ad-cards-wrapper">
  <div class="ad-grid">
    @if(isset($adSlots) && $adSlots->count() > 0)
      @foreach($adSlots as $slotNumber => $mediaItems)
        <div class="ad-grid-item ad-tv-box" data-tv="{{ $slotNumber }}">
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
        </div>
      @endforeach
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
      <div class="ad-grid-item">
        <div class="ad-tv-slide active" data-type="image" data-duration="5">
          <img src="{{ asset('assets/homepageads/ad_car.png') }}" alt="Car Hire" class="ad-carousel-media">
        </div>
      </div>
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
    margin-bottom: 28px;
    padding-top: 0;
    position: relative;
    z-index: 3;
  }

  .ad-grid {
    display: flex;
    gap: 8px;
    width: 100%;
  }

  .ad-grid-item {
    flex: 1;
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
      margin-top: 0;
    }

    .ad-grid {
      gap: 6px;
    }

    .ad-grid-item {
      height: 100px;
    }
  }

  @media (max-width: 480px) {
    .ad-grid {
      gap: 4px;
    }

    .ad-grid-item {
      height: 80px;
    }
  }
</style>

<!-- Subtle Rotating Single Ad Strip -->
<div class="rotating-ad-strip" style="
  width: 100%;
  max-width: 600px;
  margin: 20px auto 0;
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