@include('header')
<meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://fonts.googleapis.com/css?family=Rajdhani&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

<style>
  .golden-notice {
    color: #ffc107 !important;
    font-weight: bold !important;
    font-size: 18px;
    letter-spacing: 0.5px;
    text-align: center;
    margin-bottom: 25px;
    font-family: inherit;
}

.remarks-textarea {
    padding-left: 45px;
    min-height: 120px;
    resize: vertical;
}

  .gold-card {
    border: 3px solid #FFD23F;
    border-radius: 12px;
    background: #000;
    color: #FFD23F;
    padding: 28px 24px 26px 24px;
    min-height: 260px;
    margin-bottom: 10px;
    box-shadow: 0 2px 18px 0 rgba(0,0,0,0.35);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .gold-card ul {
    padding-left: 12px;
    list-style: disc;
    margin-bottom: 24px;
  }
  .gold-card li {
    color: #FFD23F;
    font-size: 18px;
    margin-bottom: 6px;
    font-weight: 500;
    text-align: left;
  }
  .gold-card-btn {
    display: block;
    width: 100%;
    border: none;
    border-radius: 6px;
    background: #FFD23F;
    color: #111;
    padding: 16px 0 10px 0;
    font-size: 21px;
    font-weight: bold;
    letter-spacing: 0.5px;
    margin: 0 auto;
    text-align: center;
    transition: background 0.2s, color 0.2s;
    box-shadow:0 2px 12px #0007;
    position: relative;
  }
  .gold-card-btn .subtitle {
    display: block;
    font-size: 15px;
    line-height: 1.2;
    font-weight: 600;
    letter-spacing: 1.2px;
    text-transform: uppercase;
  }
  .gold-card-btn:hover {
    background: #fffbbb;
    color: #000;
  }
  .gold-card-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }
  .btn-loader {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid #111;
    border-radius: 50%;
    border-top-color: transparent;
    animation: spin 1s linear infinite;
    margin-right: 8px;
  }

  /* Button Snackbar Styles */
  .button-snackbar {
    position: absolute;
    top: -50px;
    left: 50%;
    transform: translateX(-50%);
    background: #50cb4a;
    color: #111;
    padding: 8px 16px;
    border-radius: 5px;
    font-size: 14px;
    font-weight: 600;
    z-index: 1000;
    opacity: 0;
    transition: opacity 0.3s;
    box-shadow: 0 3px 15px rgba(0,0,0,0.13);
    border-bottom: 3px solid #9be58d;
    white-space: nowrap;
  }
  .button-snackbar.failed {
    background: #fa5353;
    color: #fff;
    border-bottom: 3px solid #c44f4f;
  }
  .button-snackbar.show {
    opacity: 1;
  }

  body, html { overflow-x: hidden; background: #000 !important; }
  .slide-inner .overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1; }
  .slide-inner .container { position: relative; z-index: 2; }
  .center { text-align: center; }
  h2 { line-height: 1.1; }
  .container { width: 1200px; padding: 0 15px; margin: 0 auto; }
  .hero-slider { width: 100%; height: 600px; position: relative; display: flex; z-index: 0; }
  @media (max-width: 991px) { .hero-slider { height: 600px; } }
  @media (max-width: 767px) { .hero-slider { height: 500px; } }
  .swiper-container { width: 100vw; height: 100%; position: absolute; left: 0; top: 0; }
  .swiper-slide { overflow: hidden; color: #fff; }
  .slide-inner { width: 100%; height: 100%; position: relative; display: flex; justify-content: center; align-items: center; text-align: left; overflow: hidden; }
  .slide-inner .slide-img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0; }
  .slide-inner .container { position: relative; z-index: 2; }
  .hero-style .slide-title, .hero-style .slide-text, .hero-style .slide-btns { max-width: 690px; }
  .hero-style .slide-title h2 { font-size: 100px; font-weight: 600; color: #fff; margin: 0 0 40px; text-transform: capitalize; transition: all .4s ease; }
  @media (max-width: 1199px) { .hero-style .slide-title h2 { font-size: 75px; } }
  @media (max-width: 991px) { .hero-style .slide-title h2 { font-size: 50px; margin-bottom: 35px; } }
  @media (max-width: 767px) { .hero-style .slide-title h2 { font-size: 35px; margin-bottom: 30px; } }
  .hero-style .slide-text p { opacity: 0.8; font-size: 32px; font-weight: 500; color: #fff; margin: 0 0 40px; }
  @media (max-width: 767px) { .hero-style .slide-text p { font-size: 16px; margin-bottom: 30px; } }
  .swiper-container-horizontal { overflow-x: hidden; }
  .form-group { position: relative; }
  .form-icon-inside { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); font-size: 18px; pointer-events: none; z-index: 2; }
  .form-control.with-icon { padding-left: 45px !important; position: relative; z-index: 1; }
  .bton:hover { background-color: #debb55 !important; color: #fff !important; border-color: #FFD700 !important; transition: all 0.3s ease-in-out; }
  select.form-control.with-icon { appearance: none; -webkit-appearance: none; -moz-appearance: none; background-position: right 1rem center; background-repeat: no-repeat; }
  .product-info p, .product-info ul li { font-size: 24px; line-height: 26px; color: #afafaf; }
  .aboutus h2 { font-size: 30px; margin-bottom: 10px; }
  .aboutus h5 { font-size: 30px; margin-bottom: 10px; line-height: 45px; font-family: mont-heavy; color: #debb55; }
  .product-info ul{ margin:0px; padding:0px 0px 0px 15px;}
  .product-info ul li { font-size: 24px; line-height: 26px; color: #afafaf; margin-bottom:15px; list-style: disc; }
  .golden-heading { background-color: rgb(255, 210, 63); color: white !important; padding: 10px 20px; border-radius: 0; margin-top: 40px; margin-bottom: 20px !important; display: block; width: 100%; }

  .form-container { background: #181818; border-radius: 12px; padding: 40px 30px 28px 30px; box-shadow:0 10px 50px 0 rgba(0,0,0,.13); margin-bottom: 32px; }
  @media (max-width: 991px) { .form-container { padding: 25px 5px; } }
  @media (max-width: 576px) { .form-container { padding: 12px 3px; } }

  .submit-btn {
    background-color: #FFD23F;
    color: #111;
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    font-size: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: background 0.25s;
  }
  .submit-btn:disabled { opacity: 0.85; cursor: not-allowed; }
  .classic-loader-spin {
    border: 2px solid #fffbe5;
    border-top: 2px solid #FFD700;
    border-radius: 50%;
    width: 18px; height: 18px;
    display: inline-block;
    margin-right: 10px;
    vertical-align: middle;
    animation: spin 1s linear infinite;
  }
  .centered-content {
    max-width: 1500px;
    margin: 0 auto;
    background: none;
    padding: 0 12px 34px 12px;
    display: block;
  }
  @keyframes spin { 0% { transform: rotate(0deg);} 100% {transform: rotate(360deg);} }
  .classic-snackbar {
    display: none;
    position: fixed;
    left: 50%;
    bottom: 35px;
    transform: translateX(-50%);
    min-width: 240px;
    z-index: 2000;
    color: #111;
    border-radius: 5px;
    font-size: 18px;
    font-weight: 600;
    padding: 16px 35px;
    text-align: center;
    opacity: 0;
    transition: opacity 0.4s;
    box-shadow: 0 3px 15px rgba(0,0,0,0.13);
    background: #ffef8e;
    border-bottom: 4px solid #FFD700;
  }
  .classic-snackbar.success { background: #50cb4a; color: #111; border-bottom: 4px solid #9be58d;}
  .classic-snackbar.failed { background: #fa5353; color: #fff; border-bottom: 4px solid #c44f4f;}
  .classic-snackbar.show { display: block; opacity: 1; }

  /* Mobile responsiveness fixes */
  @media (max-width: 768px) {
    .container { width: 100%; padding: 0 10px; }
    .hero-style .slide-title h2 { margin-right: 0 !important; }
    .hero-style .slide-text p { margin-right: 0 !important; }
    .gold-card { padding: 20px 16px; min-height: 220px; }
    .gold-card li { font-size: 16px; }
    .gold-card-btn { font-size: 18px; padding: 14px 0 8px 0; }
    .gold-card-btn .subtitle { font-size: 13px; }
    .button-snackbar { font-size: 12px; padding: 6px 12px; }
  }

  @media (max-width: 576px) {
    .hero-slider { height: 400px; }
    .gold-card { padding: 16px 12px; min-height: 200px; }
    .gold-card li { font-size: 14px; margin-bottom: 4px; }
    .gold-card-btn { font-size: 16px; padding: 12px 0 6px 0; }
  }



.input-icon-group {
    position: relative;
    width: 100%;
}

/* GOLD FLAG ICON */
.icon-flag {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 20px;
    pointer-events: none;
    background: linear-gradient(45deg, #d4af37, #f7d774, #b8860b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: bold;
}

/* Input with extra padding */
.input-with-icon {
    padding-left: 45px !important;
    height: 45px;
    border-radius: 8px;
    border: 1px solid #ccc;
}

/* Focus effect */
.input-with-icon:focus {
    border-color: #d4af37;
    box-shadow: 0 0 5px rgba(212,175,55,0.5);
}


.icon-flag svg {
    width: 18px;
    height: 18px;
    stroke: #000; /* black border */
    fill: #fff;   /* white inside */
}


</style>

<section class="hero-slider hero-style">
  <div class="swiper-container">
    <div class="swiper-wrapper">
      @if($detail && $detail->activityImage)
        @foreach(explode('#cseparator', $detail->activityImage) as $img)
          @if(trim($img) !== '')
            <div class="swiper-slide">
              <div class="slide-inner">
                <div class="overlay"></div>
                <img class="slide-img" src="{{ asset(trim($img)) }}" alt="{{ $activity->activityName }}">
                <div class="container center">
                  <div data-swiper-parallax="300" class="slide-title">
                    <h2 style="margin-right:-550px;">{{ $activity->activityName }}</h2>
                  </div>
                  <div data-swiper-parallax="400" class="slide-text">
                    <p style="margin-right:-550px;">{{ $activity->activityLocation }}</p>
                  </div>
                </div>
              </div>
            </div>
          @endif
        @endforeach
      @endif
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script>
var interleaveOffset = 0;
var swiperOptions = {
  loop: true,
  speed: 1000,
  parallax: true,
  autoplay: {
    delay: 3500,
    disableOnInteraction: false,
  },
  watchSlidesProgress: true,
  pagination: { el: '.swiper-pagination', clickable: true },
  navigation: {
    nextEl: '.swiper-button-next',
    prevEl: '.swiper-button-prev',
  },
  on: {
    progress: function () {
      var swiper = this;
      for (var i = 0; i < swiper.slides.length; i++) {
        var slideProgress = swiper.slides[i].progress;
        var innerOffset = swiper.width * interleaveOffset;
        var innerTranslate = slideProgress * innerOffset;
        swiper.slides[i].querySelector(".slide-inner").style.transform =
          "translate3d(" + innerTranslate + "px, 0, 0)";
      }
    },
    touchStart: function () {
      var swiper = this;
      for (var i = 0; i < swiper.slides.length; i++) {
        swiper.slides[i].style.transition = "";
      }
    },
    setTransition: function (speed) {
      var swiper = this;
      for (var i = 0; i < swiper.slides.length; i++) {
        swiper.slides[i].style.transition = speed + "ms";
        swiper.slides[i].querySelector(".slide-inner").style.transition = speed + "ms";
      }
    }
  }
};
var swiper = new Swiper(".swiper-container", swiperOptions);
</script>

<section class="pt-40px pb-0 aboutus">
  <div class="container">
    <div class="row">
      <div class="d-flex align-items-center justify-content-center min-vh-100" style="background: transparent;">
       <form method="POST" id="activityBookingForm" autocomplete="off" class="w-100" style="max-width: 900px;">
  @csrf
  <input type="hidden" name="activityId" id="activityId" value="{{ $activity->activityID }}">

  <div class="text-center">
    <span class="fs-20 text-dark-gray fw-600 mb-25px d-inline-block">Book Your Ticket Now</span>
  </div>
  
  <div class="row">
    <!-- Row 1: Name and Date -->
    <!-- Name -->
    <div class="col-md-6">
      <div class="position-relative form-group mb-20px">
        <i class="bi bi-emoji-smile form-icon-inside text-dark"></i>
        <input type="text" name="name" class="form-control with-icon" placeholder="Your name*" autocomplete="off" required>
      </div>
    </div>

    <!-- Date -->
    <div class="col-md-6">
  <div class="position-relative form-group mb-20px">
    <i class="bi bi-calendar form-icon-inside text-dark"></i>
    <input type="text" 
           name="date" 
           class="form-control with-icon" 
           placeholder="Select Date of Booking" 
           onfocus="(this.type='date')" 
           onblur="if(this.value==''){this.type='text'}" 
           required>
  </div>
</div>


    <!-- Row 2: Address and Nationality (NEW FIELDS) -->
    <!-- Address -->
    <div class="col-md-6">
      <div class="position-relative form-group mb-20px">
        <i class="bi bi-geo-alt form-icon-inside text-dark"></i>
        <input type="text" name="address" class="form-control with-icon" placeholder="Your address*" autocomplete="off" required>
      </div>
    </div>

    <!-- Nationality -->
 <!-- Nationality -->
<div class="col-md-6">
    <div class="input-icon-group mb-20px">
        <span class="icon-flag">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 15c1.5-1 3-.5 5-.5s3.5-1.5 5-1.5 3 .5 5 .5V5c-2 0-3.5-1.5-5-1.5S11 5 9 5 6 3.5 4 3.5v11.5z"/>
                <line x1="4" y1="3" x2="4" y2="21"></line>
            </svg>
        </span>

        <input 
            type="text" 
            name="nationality" 
            class="form-control input-with-icon" 
            placeholder="Your nationality*" 
            autocomplete="off" 
            required
        >
    </div>
</div>




    <!-- Row 3: Email and Phone -->
    <!-- Email -->
    <div class="col-md-6">
      <div class="position-relative form-group mb-20px">
        <i class="bi bi-envelope form-icon-inside text-dark"></i>
        <input type="email" name="email" class="form-control with-icon" placeholder="Your email address*" required>
      </div>
    </div>

    <!-- Phone -->
    <div class="col-md-6">
  <div class="position-relative form-group mb-20px">
    <i class="bi bi-telephone form-icon-inside text-dark"></i>
    <input type="number" name="phone" class="form-control with-icon" placeholder="Your phone number*" min="1000000000" max="9999999999" required>
  </div>
</div>


    <!-- Row 4: Adults and Children -->
    <!-- Adults -->
    <!-- Adults -->
<div class="col-md-6">
  <div class="position-relative form-group mb-20px">
    <i class="bi bi-people form-icon-inside text-dark"></i>
    <input type="number" class="form-control with-icon" name="adults" placeholder="Number of Adults (Age 18+)" min="0" max="20" required>
  </div>
</div>

<!-- Children -->
<div class="col-md-6">
  <div class="position-relative form-group mb-20px">
    <i class="bi bi-person form-icon-inside text-dark"></i>
    <input type="number" class="form-control with-icon" name="childrens" placeholder="Number of Children (Age 6-17)" min="0" max="20" required>
  </div>
</div>


    
    <!-- Payment Option -->
    <div class="col-md-6">
  <div class="position-relative form-group mb-20px">
    <i class="bi bi-cash-coin form-icon-inside text-dark"></i>
    <select class="form-control with-icon" name="payment_option" required style="font-weight: bold;">
      <option value="book_and_pay_yourself" selected style="font-weight: bold;">Book & Pay Yourself</option>
    </select>
  </div>
</div>


    <!-- Transfer Option -->
<div class="col-md-6">
  <div class="position-relative form-group mb-20px">
    <i class="bi bi-truck form-icon-inside text-dark"></i>
    <select class="form-control with-icon" name="transfer" required style="font-weight: bold;">
      <option value="" style="font-weight: bold;">-- Select Transport type --</option>
      <option value="abu_dhabi" style="font-weight: bold;">Transport within Abu Dhabi</option>
      <option value="dubai" style="font-weight: bold;">Transport within Dubai</option>
      <option value="abu_dhabi_to_dubai" style="font-weight: bold;">Transport from Abu Dhabi to Dubai (OR Dubai to Abu Dhabi)</option>
      <option value="any_emirates" style="font-weight: bold;">Transport from any Emirates to any Emirates</option>
      <option value="without_transfer" style="font-weight: bold;">Without Transport</option>
    </select>
  </div>
</div>



    <!-- Row 6: Remarks (Full Width) -->
    <!-- Row 6: Remarks (Full Width with increased height) -->
<div class="col-12">
  <div class="position-relative form-group mb-20px">
    <i class="bi bi-pencil-square form-icon-inside text-dark"></i>
    <textarea name="remarks" class="form-control with-icon" rows="4" placeholder="Any special request or remarks, our guest attendant coordinator will personally ensure it is taken care." style="padding-left: 45px; min-height: 70px; height: 70px; resize: vertical;"></textarea>
  </div>
</div>

<!-- Important Notice -->
<div class="col-12">
  <div class="mb-25px text-center">
    <p class="fs-18 fw-bold mb-0" style="color: #ffc107; font-family: inherit; letter-spacing: 0.5px;">
      PLEASE READ ALL THE BELOW INFORMATION BEFORE YOU BOOK
    </p>
  </div>
</div>


    <!-- Hidden currency -->
    <input type="hidden" name="currency" id="formCurrency" value="AED">

    <!-- Action Cards -->
    <div class="row justify-content-center" id="actionCardsWrapper" style="margin-top:32px;">
      <!-- Card 1: Book Now -->
      <div class="col-md-6 mb-4 action-card-col" id="card-book-now" style="display:none;">
        <div class="gold-card">
          <ul>
            <li>Book now</li>
            <li>Our Agent will contact you</li>
            <li>Help you complete payment</li>
            <li id="bookNowTransportInfo">Transport info will appear here</li>
          </ul>
          <button type="button" class="gold-card-btn" id="bookNowBtn" style="position: relative;">
            <div class="button-snackbar" id="bookNowSnackbar"></div>
            <span id="bookNowButtonText">Book With...</span><br>
            <span class="subtitle" id="bookNowSubtitle">AGENT ASSISTED</span>
          </button>
        </div>
      </div>

      <!-- Card 2: Book and Pay Now -->
      <div class="col-md-6 mb-4 action-card-col" id="card-pay-now" style="display:none;">
        <div class="gold-card">
          <ul>
            <li>Book now</li>
            <li>Pay instantly</li>
            <li>Receive confirmation</li>
            <li id="payNowTransportInfo">Transport info will appear here</li>
            <li>Maximum 4 Guests</li>
          </ul>
          <button type="button" class="gold-card-btn" id="payNowBtn" style="position: relative;">
            <div class="button-snackbar" id="payNowSnackbar"></div>
            <span id="payNowButtonText">Book And Pay Now With...</span><br>
            <span class="subtitle" id="payNowSubtitle">INSTANT BOOKING</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</form>

        <!-- Payment Details Overlay -->
        <div id="chargePreviewModal" style="display:none;position:fixed;left:0;top:0;width:100vw;height:100vh;z-index:3000;justify-content:center;align-items:center;background:rgba(0,0,0,0.7);">
          <div style="background:#181818;color:#fff;padding:40px 36px 32px 36px;border-radius:14px;min-width:350px;max-width:96vw;box-shadow:0 10px 50px #0008;">
            <h3 style="margin-bottom:28px;font-size:1.6rem;letter-spacing:1.1px;text-align:center;">Payment Details</h3>
            <table style="width:100%;border-collapse:collapse;font-size:17px;margin-bottom:20px;">
              <tbody>
                <tr>
                  <td style="padding:9px 6px 7px;color:#ffc107;font-weight:600;">Adult Price</td>
                  <td style="text-align:right;padding:9px 6px 7px;">
                    <span id="modalAdultPrice"></span> <span class="modal-currency"></span>
                  </td>
                </tr>
                <tr>
                  <td style="padding:9px 6px 7px;font-weight:600;">Child Price</td>
                  <td style="text-align:right;padding:9px 6px 7px;">
                    <span id="modalChildPrice"></span> <span class="modal-currency"></span>
                  </td>
                </tr>
                <tr id="transportChargeRow" style="display:none;">
                  <td style="padding:9px 6px 7px;font-weight:600;color:#ffc107;">Transport Charges</td>
                  <td style="text-align:right;padding:9px 6px 7px;">
                    <span id="modalTransportCharges"></span> <span class="modal-currency"></span>
                  </td>
                </tr>
                <tr>
                  <td style="padding:9px 6px 7px;font-weight:600;">Transaction Charges</td>
                  <td style="text-align:right;padding:9px 6px 7px;">
                    <span id="modalTxnCharges"></span> <span class="modal-currency"></span>
                  </td>
                </tr>
                <tr>
                  <td style="padding:12px 6px 4px;font-weight:bold;font-size:19px;color:#fff;border-top:2px solid #ffc107;">Total Amount</td>
                  <td style="text-align:right;padding:12px 6px 4px;font-weight:bold;font-size:19px;color:#ffc107;border-top:2px solid #ffc107;">
                    <span id="modalTotalAmount"></span> <span class="modal-currency"></span>
                  </td>
                </tr>
              </tbody>
            </table>
            <div style="text-align:right;margin-top:5px;">
              <button type="button" id="closeModalBtn" style="background:#666;color:#fff;padding:10px 22px;border:none;border-radius:5px;margin-right:10px;">Cancel</button>
              <button type="button" id="ccavenuePayBtn" class="submit-btn" style="padding:10px 26px 10px 26px;letter-spacing:1px;">PAY NOW</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Snackbar for notifications -->
<div class="classic-snackbar" id="snackbar"></div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Set up CSRF token for all AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
        window.csrfToken = csrfToken.getAttribute('content');
    }

    // Initialize variables
    const activityIdInput = document.querySelector('input[name="activityId"]');
    if (!activityIdInput) {
        console.error('activityId input not found!');
        return;
    }
    const activityId = activityIdInput.value;
    console.log("Activity ID:", activityId);
    
    let dubaiPrice = 0;
    let abuDhabiPrice = 0;
    let fromAbuDhabiToDubaiPrice = 0;  // New variable
    let emiratesPrice = 0;             // New variable

    // Get DOM elements
    const paymentSelect = document.querySelector('select[name="payment_option"]');
    const transferSelect = document.querySelector('select[name="transfer"]');
    const bookNowCard = document.getElementById('card-book-now');
    const payNowCard = document.getElementById('card-pay-now');
    const bookNowBtn = document.getElementById('bookNowBtn');
    const payNowBtn = document.getElementById('payNowBtn');
    const form = document.getElementById('activityBookingForm');

    // Content elements
    const bookNowTransportInfo = document.getElementById('bookNowTransportInfo');
    const payNowTransportInfo = document.getElementById('payNowTransportInfo');
    const bookNowButtonText = document.getElementById('bookNowButtonText');
    const payNowButtonText = document.getElementById('payNowButtonText');
    const bookNowSubtitle = document.getElementById('bookNowSubtitle');
    const payNowSubtitle = document.getElementById('payNowSubtitle');

    // Prevent form from submitting traditionally
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submit prevented - should use button clicks instead');
        });
    }

    // Fetch prices from API
    function fetchPrices() {
    console.log("Fetching prices for activity ID:", activityId);
    fetch(`/activity/prices/${activityId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': window.csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log("Prices response status:", response.status);
        return response.json();
    })
    .then(data => {
        console.log("Prices data:", data);
        if (data.error) {
            console.error("Error fetching prices:", data.error);
            return;
        }
        dubaiPrice = parseFloat(data.dubaiPrice) || 0;
        abuDhabiPrice = parseFloat(data.abuDhabiPrice) || 0;
        fromAbuDhabiToDubaiPrice = parseFloat(data.fromAbuDhabiToDubai) || 0;  // New price
        emiratesPrice = parseFloat(data.emirates) || 0;                       // New price
        console.log("Fetched prices - Dubai:", dubaiPrice, "Abu Dhabi:", abuDhabiPrice, "Abu Dhabi to Dubai:", fromAbuDhabiToDubaiPrice, "Emirates:", emiratesPrice);
        updateCardDisplay();
    })
    .catch(error => {
        console.error("Fetch prices error:", error);
        dubaiPrice = 0;
        abuDhabiPrice = 0;
        fromAbuDhabiToDubaiPrice = 0;  // Reset new variables
        emiratesPrice = 0;             // Reset new variables
        updateCardDisplay();
    });
}

    // Update card content based on selections
    function updateCardDisplay() {
        const paymentOption = paymentSelect ? paymentSelect.value : '';
        const transferOption = transferSelect ? transferSelect.value : '';

        console.log("Updating display - Payment:", paymentOption, "Transfer:", transferOption);

        // Hide all cards first
        if (bookNowCard) bookNowCard.style.display = 'none';
        if (payNowCard) payNowCard.style.display = 'none';

        // Show appropriate card based on payment selection
        if (paymentOption === 'book_with_us') {
            if (bookNowCard) bookNowCard.style.display = 'block';
            updateBookNowCard(transferOption);
        } else if (paymentOption === 'book_and_pay_yourself') {
            if (payNowCard) payNowCard.style.display = 'block';
            updatePayNowCard(transferOption);
        }
    }

    // Update Book Now card content
    // Update Book Now card content
function updateBookNowCard(transferOption) {
    let transportInfo = '';
    let buttonText = 'Book With...';
    let subtitle = 'AGENT ASSISTED';

    switch(transferOption) {
        case 'abu_dhabi':
            transportInfo = `Transport from Abu Dhabi (${abuDhabiPrice.toFixed(2)} AED)`;
            buttonText = 'Book With ABU DHABI Transport';
            subtitle = 'WITHIN ABU DHABI';
            break;
        case 'dubai':
            transportInfo = `Transport from Dubai (${dubaiPrice.toFixed(2)} AED)`;
            buttonText = 'Book With DUBAI Transport';
            subtitle = 'WITHIN DUBAI';
            break;
        case 'abu_dhabi_to_dubai':
            transportInfo = `Transport Abu Dhabi to Dubai or Dubai to Abu Dhabi (${fromAbuDhabiToDubaiPrice.toFixed(2)} AED)`;
            buttonText = 'Book With ABU DHABI-DUBAI Transport';
            subtitle = 'ABU DHABI TO DUBAI or DUBAI TO ABU DHABI';
            break;
        case 'any_emirates':
            transportInfo = `Transport from any Emirates (${emiratesPrice.toFixed(2)} AED)`;
            buttonText = 'Book With Transport from any Emirates';
            subtitle = 'To Any EMIRATES';
            break;
        case 'without_transfer':
            transportInfo = 'No transport included';
            buttonText = 'Book Without Transport';
            subtitle = 'DIRECT BOOKING';
            break;
        default:
            transportInfo = 'Select transport option';
            buttonText = 'Book With...';
            break;
    }

    if (bookNowTransportInfo) bookNowTransportInfo.textContent = transportInfo;
    if (bookNowButtonText) bookNowButtonText.textContent = buttonText;
    if (bookNowSubtitle) bookNowSubtitle.textContent = subtitle;
}


    // Update Pay Now card content
    // Update Pay Now card content
function updatePayNowCard(transferOption) {
    let transportInfo = '';
    let buttonText = 'Book And Pay Now With...';
    let subtitle = 'INSTANT BOOKING';

    switch(transferOption) {
        case 'abu_dhabi':
            transportInfo = `Transport from Abu Dhabi (${abuDhabiPrice.toFixed(2)} AED)`;
            buttonText = 'Book And Pay Now With ABU DHABI Transport';
            subtitle = 'WITHIN ABU DHABI';
            break;
        case 'dubai':
            transportInfo = `Transport from Dubai (${dubaiPrice.toFixed(2)} AED)`;
            buttonText = 'Book And Pay Now With DUBAI Transport';
            subtitle = 'WITHIN DUBAI';
            break;
        case 'abu_dhabi_to_dubai':
            transportInfo = `Transport Abu Dhabi to Dubai or Dubai to Abu Dhabi (${fromAbuDhabiToDubaiPrice.toFixed(2)} AED)`;
            buttonText = 'Book And Pay Now With ABU DHABI-DUBAI Transport';
            subtitle = 'ABU DHABI TO DUBAI or DUBAI TO ABU DHABI';
            break;
        case 'any_emirates':
            transportInfo = `Transport from any Emirates (${emiratesPrice.toFixed(2)} AED)`;
            buttonText = 'Book With Transport from any EMIRATES';
            subtitle = 'To any EMIRATES';
            break;
        case 'without_transfer':
            transportInfo = 'No transport included';
            buttonText = 'Book And Pay Now Without Transport';
            subtitle = 'DIRECT PAYMENT';
            break;
        default:
            transportInfo = 'Select transport option';
            buttonText = 'Book And Pay Now With...';
            break;
    }

    if (payNowTransportInfo) payNowTransportInfo.textContent = transportInfo;
    if (payNowButtonText) payNowButtonText.textContent = buttonText;
    if (payNowSubtitle) payNowSubtitle.textContent = subtitle;
}


    // Validate form
    function validateForm() {
        const name = document.querySelector('input[name="name"]').value.trim();
        const email = document.querySelector('input[name="email"]').value.trim();
        const date = document.querySelector('input[name="date"]').value;
        const phone = document.querySelector('input[name="phone"]').value.trim();
        const address = document.querySelector('input[name="address"]').value.trim();
        const nationality = document.querySelector('input[name="nationality"]').value.trim();
        const adults = document.querySelector('input[name="adults"]').value;
const children = document.querySelector('input[name="childrens"]').value;

        const paymentOption = document.querySelector('select[name="payment_option"]').value;
        const transferOption = document.querySelector('select[name="transfer"]').value;

        if (!name || !email || !date || !phone || !address || !nationality || !adults || !children || !paymentOption || !transferOption) {
            return false;
        }

        return true;
    }

    // Show button snackbar
    function showButtonSnackbar(buttonId, message, type = 'success') {
        console.log('Showing snackbar for:', buttonId, 'Message:', message, 'Type:', type);
        
        const snackbar = document.getElementById(buttonId + 'Snackbar');
        if (snackbar) {
            snackbar.textContent = message;
            snackbar.className = `button-snackbar ${type} show`;
            
            // Force visibility
            snackbar.style.display = 'block';
            snackbar.style.opacity = '1';
            
            setTimeout(() => {
                snackbar.classList.remove('show');
                setTimeout(() => {
                    snackbar.style.display = 'none';
                }, 300);
            }, 4000); // Show for 4 seconds
            
            console.log('Snackbar should be visible now');
        } else {
            console.error('Snackbar element not found:', buttonId + 'Snackbar');
        }
    }

    // Show button loader
    function showButtonLoader(button) {
        if (button) {
            button.disabled = true;
            const originalContent = button.innerHTML;
            button.setAttribute('data-original-content', originalContent);
            button.innerHTML = `<span class="btn-loader"></span>Processing...`;
        }
    }

    // Hide button loader
    function hideButtonLoader(button) {
        if (button) {
            button.disabled = false;
            const originalContent = button.getAttribute('data-original-content');
            if (originalContent) {
                button.innerHTML = originalContent;
            }
        }
    }

    // Reset form and UI after successful booking
    function resetFormAndUI() {
        console.log("Resetting form and UI");
        
        // Reset the form
        if (form) {
            form.reset();
        }
        
        // Hide action cards
        if (bookNowCard) bookNowCard.style.display = 'none';
        if (payNowCard) payNowCard.style.display = 'none';
        
        // Reset select elements to default
        if (paymentSelect) paymentSelect.value = '';
        if (transferSelect) transferSelect.value = '';
        
        // Reset card content to default
        if (bookNowTransportInfo) bookNowTransportInfo.textContent = 'Select transport option';
        if (payNowTransportInfo) payNowTransportInfo.textContent = 'Select transport option';
        if (bookNowButtonText) bookNowButtonText.textContent = 'Book With...';
        if (payNowButtonText) payNowButtonText.textContent = 'Book And Pay Now With...';
        if (bookNowSubtitle) bookNowSubtitle.textContent = 'AGENT ASSISTED';
        if (payNowSubtitle) payNowSubtitle.textContent = 'INSTANT BOOKING';
        
        // Clear any stored data
        window.currentBookingId = null;
        window.paymentDetails = null;
        
        console.log("Form and UI reset completed");
    }

    // FIXED Submit booking (Book Now)
    function submitBooking() {
        console.log("submitBooking called");
        
        if (!validateForm()) {
            console.log("Form validation failed");
            showButtonSnackbar('bookNow', 'Please fill in all required fields.', 'failed');
            return;
        }

        console.log("Form validation passed, submitting...");
        showButtonLoader(bookNowBtn);

        const formData = new FormData(form);
        formData.append('action_type', 'book_only');

        fetch('{{ route("activity.book") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest' // CRITICAL FIX
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            
            // Handle redirect responses (non-AJAX fallback)
            if (response.redirected || response.status === 302) {
                hideButtonLoader(bookNowBtn);
                showButtonSnackbar('bookNow', 'Booking submitted successfully! Our agent will contact you shortly within 48 hrs.', 'success');
                setTimeout(() => resetFormAndUI(), 2000);
                return;
            }
            
            // Handle JSON responses
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                return { success: true, message: 'Booking submitted successfully! Our agent will contact you shortly within 48 hrs.' };
            }
        })
        .then(data => {
            console.log("Book response data:", data);
            hideButtonLoader(bookNowBtn);
            
            if (data && data.success) {
                showButtonSnackbar('bookNow', data.message || 'Booking submitted successfully!', 'success');
                console.log("Success snackbar should be visible now");
                
                // Reset form after successful submission
                setTimeout(() => resetFormAndUI(), 2000);
            } else {
                showButtonSnackbar('bookNow', data?.message || 'Booking failed. Please try again.', 'failed');
            }
        })
        .catch(error => {
            console.error('Book error:', error);
            hideButtonLoader(bookNowBtn);
            showButtonSnackbar('bookNow', 'An error occurred. Please try again.', 'failed');
        });
    }

    // FIXED Submit booking and payment (Pay Now)
    function submitBookingAndPayment() {
        console.log("submitBookingAndPayment called");
        
        if (!validateForm()) {
            console.log("Form validation failed");
            showButtonSnackbar('payNow', 'Please fill in all required fields.', 'failed');
            return;
        }

        console.log("Form validation passed, submitting for payment...");
        showButtonLoader(payNowBtn);

        const formData = new FormData(form);
        formData.append('action_type', 'book_and_pay');

        fetch('{{ route("activity.book") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest' // CRITICAL FIX
            }
        })
        .then(response => {
            console.log("Pay response status:", response.status);
            
            // Handle redirect responses
            if (response.redirected || response.status === 302) {
                hideButtonLoader(payNowBtn);
                showButtonSnackbar('payNow', 'Booking created! Showing payment details...', 'success');
                setTimeout(() => showChargeOverlay(), 1000);
                return;
            }
            
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                return { success: true, message: 'Booking created! Showing payment details...' };
            }
        })
        .then(data => {
            console.log("Pay response data:", data);
            hideButtonLoader(payNowBtn);
            
            if (data && data.success) {
                // Store booking ID for payment
                if (data.booking_id) {
                    window.currentBookingId = data.booking_id;
                }
                
                showButtonSnackbar('payNow', 'Booking created! Showing payment details...', 'success');
                
                // Show payment overlay after a short delay
                setTimeout(() => showChargeOverlay(), 1000);
            } else {
                showButtonSnackbar('payNow', data?.message || 'Booking failed. Please try again.', 'failed');
            }
        })
        .catch(error => {
            console.error('Pay error:', error);
            hideButtonLoader(payNowBtn);
            showButtonSnackbar('payNow', 'An error occurred. Please try again.', 'failed');
        });
    }

    // Get transport charges
    // Get transport charges
function getTransportCharges() {
    const transferOption = document.querySelector('select[name="transfer"]').value;
    
    switch(transferOption) {
        case 'abu_dhabi':
            return abuDhabiPrice;
        case 'dubai':
            return dubaiPrice;
        case 'abu_dhabi_to_dubai':
            return fromAbuDhabiToDubaiPrice;
        case 'any_emirates':
            return emiratesPrice;
        case 'without_transfer':
            return 0;
        default:
            return 0;
    }
}


    // Get current currency
    function getCurrentCurrency() {
        const el = document.getElementById('formCurrency');
        if (el) return el.value.trim();
        return localStorage.getItem('selectedCurrency') || 'AED';
    }

    // Show charge overlay with pricing
    function showChargeOverlay() {
        console.log("showChargeOverlay called");
        
        fetch(`/activity/pricing/${activityId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(res => {
            console.log("Pricing response status:", res.status);
            return res.json();
        })
        .then(function(data) {
            console.log("Pricing data:", data);
            
            const adultPrice = parseFloat(data.activityPrice) || 0;
            const childPrice = parseFloat(data.activityChildPrice) || adultPrice;
            const txnCharges = parseFloat(data.activityTransactionCharges) || 0;
            const adults = parseInt(document.querySelector('input[name="adults"]').value) || 0;
const children = parseInt(document.querySelector('input[name="childrens"]').value) || 0;

            const transportCharges = getTransportCharges();
            
            const adultsTotal = adults * adultPrice;
            const childrenTotal = children * childPrice;
            const subTotal = adultsTotal + childrenTotal + txnCharges + transportCharges;
            const total = subTotal;
            const currency = getCurrentCurrency();

            // Update modal content
            document.getElementById('modalAdultPrice').textContent = `${adultPrice} x ${adults} = ${adultsTotal.toFixed(2)}`;
            document.getElementById('modalChildPrice').textContent = `${childPrice} x ${children} = ${childrenTotal.toFixed(2)}`;
            document.getElementById('modalTxnCharges').textContent = txnCharges.toFixed(2);
            document.getElementById('modalTotalAmount').textContent = total.toFixed(2);
            
            // Show/hide transport charges row
            const transportRow = document.getElementById('transportChargeRow');
            if (transportCharges > 0) {
                document.getElementById('modalTransportCharges').textContent = transportCharges.toFixed(2);
                transportRow.style.display = 'table-row';
            } else {
                transportRow.style.display = 'none';
            }

            // Set currency
            document.querySelectorAll('#chargePreviewModal .modal-currency').forEach(function(elem) {
                elem.textContent = currency;
            });
            
            // Store payment details
            window.paymentDetails = {
                orderId: null,
                amount: total.toFixed(2),
                currency: currency,
                name: document.querySelector('input[name=name]').value.trim(),
                email: document.querySelector('input[name=email]').value.trim()
            };

            document.getElementById('chargePreviewModal').style.display = 'flex';
        })
        .catch(error => {
            console.error('Error fetching pricing data:', error);
            showButtonSnackbar('payNow', 'Error loading pricing information. Please try again.', 'failed');
        });
    }

    // CCAvenue payment redirection
    function proceedToPayment(bookingId, amount) {
        const ccavenueUrl = @json(config('services.ccavenue.url'));
        console.log('Proceeding to payment for booking:', bookingId, 'Amount:', amount);
        
        const payButton = document.getElementById('ccavenuePayBtn');
        if (!payButton) {
            console.error('Payment button not found');
            return;
        }
        
        const originalText = payButton.innerHTML;
        payButton.disabled = true;
        payButton.innerHTML = '<span class="classic-loader-spin"></span>Processing...';
        
        fetch('{{ route("activity.payment.initiate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                booking_id: bookingId,
                amount: amount
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            
            if (response.status === 302) {
                throw new Error('Authentication required - please log in and try again');
            }
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    console.error('Non-JSON response:', text.substring(0, 200));
                    throw new Error('Server error - please try again');
                });
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Payment response received:', data);
            
            if (data.success && data.encryptedData && data.accessCode) {
                // Create CCAvenue form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = ccavenueUrl;
                form.style.display = 'none';
                
                const encRequest = document.createElement('input');
                encRequest.type = 'hidden';
                encRequest.name = 'encRequest';
                encRequest.value = data.encryptedData;
                form.appendChild(encRequest);
                
                const accessCode = document.createElement('input');
                accessCode.type = 'hidden';
                accessCode.name = 'access_code';
                accessCode.value = data.accessCode;
                form.appendChild(accessCode);
                
                document.body.appendChild(form);
                form.submit();
            } else {
                throw new Error(data.error || 'Payment initiation failed');
            }
        })
        .catch(error => {
            console.error('Payment error:', error);
            payButton.disabled = false;
            payButton.innerHTML = originalText;
            alert(error.message || 'Payment failed. Please try again.');
        });
    }

    // Event listeners
    if (paymentSelect) {
        paymentSelect.addEventListener('change', updateCardDisplay);
    }

    if (transferSelect) {
        transferSelect.addEventListener('change', updateCardDisplay);
    }

    // Button click events
    if (bookNowBtn) {
        bookNowBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log("Book Now button clicked");
            submitBooking();
        });
    }

    if (payNowBtn) {
        payNowBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log("Pay Now button clicked");
            submitBookingAndPayment();
        });
    }

    // Overlay pay now button
    const ccavenuePayBtn = document.getElementById('ccavenuePayBtn');
    if (ccavenuePayBtn) {
        ccavenuePayBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('CCAvenue PAY NOW button clicked');
            
            if (window.currentBookingId && window.paymentDetails && window.paymentDetails.amount) {
                proceedToPayment(window.currentBookingId, window.paymentDetails.amount);
            } else {
                console.error('Missing payment data');
                alert('Payment details not available. Please try booking again.');
            }
        });
    }

    // Modal event listeners
    const closeModalBtn = document.getElementById('closeModalBtn');
    const modal = document.getElementById('chargePreviewModal');
    
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            if (modal) {
                modal.style.display = 'none';
            }
        });
    }

    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
        
        // Prevent modal close when clicking inside modal content
        const modalContent = modal.querySelector('.modal-content');
        if (modalContent) {
            modalContent.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    }

    // Initialize page
    fetchPrices();
    updateCardDisplay();

    // Handle session messages
    const sessionSnackbar = document.getElementById('sessionSnackbar');
    if (sessionSnackbar) {
        setTimeout(function() {
            sessionSnackbar.classList.remove('show');
        }, 3000);
    }
});
</script>
@if(session('success'))
  <div class="classic-snackbar success show" id="sessionSnackbar">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="classic-snackbar failed show" id="sessionSnackbar">{{ session('error') }}</div>
@endif
@if($errors->any())
  <div class="classic-snackbar failed show" id="sessionSnackbar">{{ $errors->first() }}</div>
@endif

<script>
// Handle session messages
document.addEventListener('DOMContentLoaded', function() {
  const sessionBar = document.getElementById('sessionSnackbar');
  if(sessionBar){
    setTimeout(function(){
      sessionBar.classList.remove('show');
    }, 3000);
  }
});
</script>

<div class="row overflow-hidden position-relative centered-content">
  <div class="col-lg-12 product-info appear anime-complete" data-anime="{ &quot;translate&quot;: [0, 0], &quot;opacity&quot;: [0,1], &quot;duration&quot;: 600, &quot;delay&quot;: 100, &quot;staggervalue&quot;: 150, &quot;easing&quot;: &quot;easeOutQuad&quot; }" style="translate: 0px;">
    <br>
    <h2 class="golden-heading">Overview</h2>
@if($detail && $detail->detailsOverview)
    <div class="activity-overview">
        {!! $detail->detailsOverview !!}
    </div>
@else
    <p>No overview available.</p>
@endif


    <h2 class="golden-heading">Important Information</h2>
    @if($detail && $detail->detailsIminfo)
      <ul>
        @foreach(explode('#cseparator', $detail->detailsIminfo) as $info)
          @if(trim($info) !== '')
            <li>{{ $info }}</li>
          @endif
        @endforeach
      </ul>
    @else
      <p>No important information found.</p>
    @endif
    
    <h2 class="golden-heading">Highlights</h2>
    @if($detail && $detail->detailsHighlights)
      <ul>
        @foreach(explode('#cseparator', $detail->detailsHighlights) as $highlight)
          @if(trim($highlight) !== '')
            <li>{{ $highlight }}</li>
          @endif
        @endforeach
      </ul>
    @else
      <p>No highlights for this activity.</p>
    @endif
  </div>
</div>

@include('footer')
