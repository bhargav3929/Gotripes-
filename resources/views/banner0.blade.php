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
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Source+Sans+Pro:wght@700&display=swap"
      rel="stylesheet">

    <div
      style="font-size: 2rem; font-weight:bold; color: #ffd235; letter-spacing: 1px; font-family: 'Playfair Display', serif;">
      Welcome To GOTRIPS
    </div>
    <div style="display: flex; align-items: center; justify-content: center; gap: 10px; margin-top:14px;">
      <span style="font-size: 1.2rem; font-weight: 400; color: #fff;  line-height: 1.3;">
        a part of
      </span>
      <span
        style="font-size: 1.50rem; font-weight: 700; color: #BB8525; font-family: 'Source Sans Pro', Arial, sans-serif; line-height: 1.3;">
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
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;
    white-space: nowrap;
    backdrop-filter: blur(5px);
    font-size: 16px;
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

<!-- Navigation -->

<div class="nav-container mt-3">
  <a class="nav-link2 active" onclick="showTab('flights', event)"><i class="fas fa-plane"
      style="transform: rotate(-45deg); display: inline-block;"></i> Flights</a>
  <a class="nav-link2" onclick="showTab('hotels', event)"><i class="fas fa-hotel"></i> Hotels</a>
  <a class="nav-link2" onclick="showTab('cars', event)"><i class="fas fa-taxi"></i> Car Hire</a>
  <a class="nav-link2" onclick="showTab('flights+hotels', event)"><i class="fas fa-plane"></i> Flights + <i
      class="fas fa-hotel"></i> Hotels</a>
</div>

<!-- Tab Sections - Only change: CENTRALIZE the .container usage to ensure all tab content is *inside* .container for consistent alignment -->

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