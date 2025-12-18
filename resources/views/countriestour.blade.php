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
    justify-content: center;
    align-items: center;
    flex-direction: column;
    padding: 20px;
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

  /* --- Carousel card styles --- */
  .owl-carousel .item {
    padding: 20px 10px;
    border-radius: 15px;
    color: #fff;
    background: rgba(0, 0, 0, 0.90);      /* Card is now darker, less transparent */
    text-align: center;
    min-height: 440px;
    max-height: 440px;
    height: 440px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.6);
    box-sizing: border-box;
  }
  .owl-carousel .item img {
    border-radius: 10px;
    width: 180px;
    height: 120px;
    object-fit: cover;
    margin-bottom: 15px;
    display: block;
  }
  .flag-info {
    font-size: 15px;
    color: #fff;
    line-height: 1.6;
    max-width: 260px;
    text-align: left;
    text-shadow: 0 2px 8px rgba(0,0,0,0.85), 0 0px 2px #000;
    flex: 1 1 auto;
  }
  .country-name {
    color: rgba(255, 210, 63, 0.7);
    text-transform: uppercase;
    font-weight: bold;
    font-size: 16px;
    display: block;
    margin-bottom: 6px;
    text-shadow: 0 2px 8px rgba(0,0,0,0.85), 0 0px 2px #000;
  }

  /* --- Prevent flash of unstyled carousel --- */
  .owl-carousel {
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
  }
  .owl-carousel.owl-loaded {
    opacity: 1;
    pointer-events: auto;
  }
  #carousel-wrapper { min-height: 440px; }

  @media (max-width: 768px) {
    .overlay { top: 0px; padding: 4px; }
    .overlay-content {
      flex-direction: column;
      margin-top: 150px;
      gap: 0;
    }
    .tagline { font-size: 18px; margin-left: 0; }
    .heading { font-size: 24px; }
    .cta-button { font-size: 18px; min-width: 180px; padding: 6px 16px; }
    .image-overlay { height: 200vh; }
    .flag-info { font-size: 13px; max-width: 100%; text-align: center; }
    .owl-carousel .item {
      min-height: 310px;
      height: 310px;
      max-height: 310px;
      padding: 15px 5px;
    }
    .owl-carousel .item img { width: 140px; height: 100px; }
    #carousel-wrapper { min-height: 310px; }
  }
</style>

<!-- Image + Carousel Overlay -->
<div class="image-overlay">
  <img src="assets/index_files/533419533.jpg" alt="Banner" />
  <div class="overlay">
    <div class="container">
      <div id="carousel-wrapper">
        <div class="owl-carousel owl-theme">

          <div class="item">
            <img src="assets/countries_flag/bahrain.jpeg" alt="Bahrain Flag">
            <div class="flag-info">
              <span class="country-name">Bahrain</span>
              Capital: Manama<br>
              Best time: Nov–Mar<br>
              Currency: BHD (2.65 USD)<br>
              Duration: 4–5 days<br>
              Cost/day: $72–$299<br>
              Airport: BAH<br>
              Airline: Gulf Air (GF)
            </div>
          </div>

          <div class="item">
            <img src="assets/countries_flag/egypt.jpeg" alt="Egypt Flag">
            <div class="flag-info">
              <span class="country-name">Egypt</span>
              Capital: Cairo<br>
              Best time: Oct–Apr<br>
              Currency: EGP (50.67 = 1 USD)<br>
              Duration: 7–10 days<br>
              Cost/day: $271<br>
              Airports: CAI, HRG, SSH<br>
              Airline: Egypt Air (MS)
            </div>
          </div>

          <div class="item">
            <img src="assets/countries_flag/oman.jpeg" alt="Oman Flag">
            <div class="flag-info">
              <span class="country-name">Oman</span>
              Capital: Muscat<br>
              Best time: Oct–Apr<br>
              Currency: OMR (2.60 USD)<br>
              Duration: 5–7 days<br>
              Cost/day: $4,224<br>
              Airport: MCT<br>
              Airline: Oman Air (WY)
            </div>
          </div>

          <div class="item">
            <img src="assets/countries_flag/saudi arabia.jpeg" alt="Saudi Flag">
            <div class="flag-info">
              <span class="country-name">Saudi Arabia</span>
              Capital: Riyadh<br>
              Best time: Oct–Mar<br>
              Currency: SAR (3.75 = 1 USD)<br>
              Duration: 5–7 days<br>
              Cost/day: $100–$200<br>
              Airports: JED, RUH<br>
              Airlines: Saudia, Flynas, Flyadeal
            </div>
          </div>

          <div class="item">
            <img src="assets/countries_flag/uae.png" alt="UAE Flag">
            <div class="flag-info">
              <span class="country-name">UAE</span>
              Capital: Abu Dhabi<br>
              Best time: Oct–Apr<br>
              Currency: AED (3.68 = 1 USD)<br>
              Duration: 5–7 days<br>
              Cost/day: $200–$250<br>
              Airports: DXB, AUH, SHJ<br>
              Airlines: Emirates, Etihad, FlyDubai
            </div>
          </div>

          <div class="item">
            <img src="assets/countries_flag/SA.jpg" alt="South Africa Flag">
            <div class="flag-info">
              <span class="country-name">South Africa</span>
              Capitals: Cape Town, Pretoria, Bloemfontein<br>
              Best time: May–Sep (Safari), Nov–Feb (Coast)<br>
              Currency: ZAR (17.97 = 1 USD)<br>
              Duration: 10–14 days<br>
              Cost/day: $200–$250<br>
              Airports: JNB, CPT<br>
              Airline: SAA
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<!-- Owl Carousel JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script>
  $(function() {
    $(".owl-carousel").owlCarousel({
      items: 3,
      margin: 20,
      loop: true,
      autoplay: true,
      autoplayTimeout: 2500,
      autoplaySpeed: 800,
      smartSpeed: 800,
      dots: false,
      responsive: {
        0: { items: 1 },
        768: { items: 2 },
        1024: { items: 3 }
      }
    });

    document.addEventListener("visibilitychange", function () {
      if (document.visibilityState === "visible") {
        $(".owl-carousel").trigger('play.owl.autoplay', [2000]);
      }
    });
  });
</script>

@include('footer')
