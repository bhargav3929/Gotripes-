<script src="{{ asset('assets/index_files/swiper-bundle.min.js') }}"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    var swiper = new Swiper(".flag-ticker", {
      loop: true,
      slidesPerView: "auto",
      allowTouchMove: false,
      speed: 1000,
      loopAdditionalSlides: 25,
      spaceBetween: 10,
      autoplay: {
        delay: 0,
        disableOnInteraction: false
      }
    });
  });

  var newsSwiper = new Swiper(".news-slider", {
    loop: true,
    slidesPerView: "auto",
    allowTouchMove: false,
    speed: 3000,
    spaceBetween: 30,
    loopAdditionalSlides: 3,
    autoplay: {
      delay: 0,
      disableOnInteraction: false
    }
  });

</script>

<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="whats">
  <a target="_blank"
    href="https://api.whatsapp.com/send/?phone=971543651065&amp;text&amp;type=phone_number&amp;app_absent=0">
    <i class="fab fa-whatsapp text-white"></i>
  </a>
</div>

<style>
  .whats {
    width: 60px;
    height: 60px;
    background-color: #25D366;
    color: white;
    position: fixed;
    bottom: 3%;
    left: 1%;
    z-index: 10000;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 4px 10px rgba(216, 216, 216, 0.681);
    transition: transform 0.2s;
  }

  .whats:hover {
    transform: scale(1.1);
  }

  a.text-white:hover {
    text-decoration: none !important;
  }

  .whats i {
    font-size: 28px;
  }

  /* --- Premium Footer Consistency --- */
  .gt-footer {
    background: #000 !important;
    color: #fff !important;
    font-family: 'Outfit', sans-serif !important;
  }

  .gt-footer-title {
    color: #FFD23F !important;
    font-size: 18px !important;
    font-weight: 700 !important;
    letter-spacing: 1px !important;
    margin-bottom: 25px !important;
    text-transform: uppercase !important;
  }

  .gt-footer-link {
    color: rgba(255, 255, 255, 0.8) !important;
    font-size: 15px !important;
    font-weight: 400 !important;
    text-decoration: none !important;
    transition: all 0.3s ease !important;
    display: inline-block !important;
    padding: 5px 0 !important;
  }

  .gt-footer-link:hover {
    color: #FFD23F !important;
    padding-left: 5px !important;
    text-decoration: none !important;
  }

  .gt-footer-social-link {
    color: #fff !important;
    font-size: 18px !important;
    margin-right: 15px !important;
    transition: all 0.3s ease !important;
  }

  .gt-footer-social-link:hover {
    color: #FFD23F !important;
    transform: translateY(-3px) !important;
  }

  @media (max-width: 576px) {
    .gt-footer {
      text-align: center !important;
    }
    .gt-footer-title {
      margin-bottom: 15px !important;
    }
    .gt-footer-social-link {
        margin: 0 10px !important;
    }
  }

  .gt-footer-copyright {
    font-size: 14px !important;
    color: rgba(255, 255, 255, 0.6) !important;
    border-top: 1px solid rgba(255, 255, 255, 0.05) !important;
    padding-top: 20px !important;
  }
</style>



<!-- Tawk.to Script -->
<script type="text/javascript">
  var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
  (function () {
    var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
    s1.async = true;
    s1.src = 'https://embed.tawk.to/67a073313a8427326078f27b/1ij5c3v7a';
    s1.charset = 'UTF-8';
    s1.setAttribute('crossorigin', '*');
    s0.parentNode.insertBefore(s1, s0);
  })();
</script>
<!--End of Tawk.to Script-->
<footer class="gt-footer footer-light pb-0">
  <div class="container">
    <div class="row pt-5 pb-3">
      <!-- Column 1 -->
      <div class="col-md-3 col-sm-6 mb-4">
        <h6 class="gt-footer-title">Popular Tours</h6>
        <ul class="list-unstyled">
          <li><a href="/activities" class="gt-footer-link">UAE Activities</a></li>
          <li><a href="/countriestour" class="gt-footer-link">Countries Tour</a></li>
          <li><a href="/dubai-global-village" class="gt-footer-link">Dubai Global Village</a></li>
          <li><a href="/lotus-cruise-dubai" class="gt-footer-link">Lotus Cruise Dubai</a></li>
        </ul>
      </div>

      <!-- Column 2 -->
      <div class="col-md-3 col-sm-6 mb-4">
        <h6 class="gt-footer-title">Our Services</h6>
        <ul class="list-unstyled">
          <li><a href="/shopnow" class="gt-footer-link">Shop Now</a></li>
          <li><a href="/lookingforajob" class="gt-footer-link">Looking for a Job</a></li>
          <li><a href="/visaservice" class="gt-footer-link">Visa Service</a></li>
          <li><a href="/uaevisa" class="gt-footer-link">UAE Visa</a></li>
        </ul>
      </div>

      <!-- Column 3 -->
      <div class="col-md-3 col-sm-6 mb-4">
        <h6 class="gt-footer-title">Company</h6>
        <ul class="list-unstyled">
          <li><a href="/ourstory" class="gt-footer-link">Our Story</a></li>
          <li><a href="/contact-us" class="gt-footer-link">Contact Us</a></li>
          <li><a href="/caro" class="gt-footer-link">UAE Carousel</a></li>
        </ul>
      </div>

      <!-- Column 4 (Social Icons) -->
      <div class="col-md-3 col-sm-6 mb-4">
        <h6 class="gt-footer-title">Follow Us</h6>
        <div class="social-links">
          <a class="gt-footer-social-link" href="https://www.facebook.com/" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
          <a class="gt-footer-social-link" href="http://www.twitter.com/" target="_blank"><i class="fa-brands fa-twitter"></i></a>
          <a class="gt-footer-social-link" href="http://www.instagram.com/" target="_blank"><i class="fa-brands fa-instagram"></i></a>
          <a class="gt-footer-social-link" href="http://www.dribbble.com/" target="_blank"><i class="fa-brands fa-dribbble"></i></a>
        </div>
      </div>
    </div>

    <div class="text-center mt-3 pb-3">
      <p class="gt-footer-copyright">
        Copyright ©
        <script>document.write(new Date().getFullYear());</script>
        All rights reserved | Designed and developed by
        <a href="https://scoriait.com" class="text-warning" target="_blank" style="text-decoration: none;">ScoriaIT</a>
      </p>
    </div>
  </div>
</footer>


<!-- start scroll progress -->
<div class="scroll-progress d-none d-xxl-block">
  <a href="/#" class="scroll-top" aria-label="scroll">
    <span class="scroll-text">Scroll</span><span class="scroll-line"><span class="scroll-point"></span></span>
  </a>
</div>
<!-- end scroll progress -->




<!-- start subscription popup -->


<div id="modal-popup3"
  class="zoom-anim-dialog mfp-hide col-xl-10 col-lg-10 col-md-11 col-11 contact-form-style-01 mfp-hide subscribe-popup mx-auto text-center modal-popup-main p-0"
  style="background: transparent;">

  <div class="container p-0">
    <div class="row justify-content-center m-0">

      <div class="col-12 p-0"
        style="border-radius: 20px; overflow: hidden; border: 1px solid rgba(255, 215, 0, 0.2); box-shadow: 0 50px 100px rgba(0,0,0,0.9);">
        <div class="row g-0">

          <!-- Image Section -->
          <div class="col-lg-5 cover-background d-none d-lg-block"
            style="background-image:url('https://gotrips.ai/assets/images/1855.jpg'); min-height: 500px;">
            <div
              style="background: linear-gradient(to right, rgba(0,0,0,0.3), rgba(17,17,17,1)); width: 100%; height: 100%;">
            </div>
          </div>

          <!-- Form Section -->
          <div class="col-lg-7 position-relative" style="background: #111;">

            <div class="p-5">
              <span class="fs-14 fw-600 text-uppercase d-block mb-2" style="color: #FFD700; letter-spacing: 1px;">24/7
                Support</span>
              <h3 class="fw-700 text-white mb-4" style="font-family: 'Outfit', sans-serif;">Need Expert Travel Advice?
              </h3>

              <form action="https://gotrips.ai/email-templates/contact-form.php" method="post" class="text-start">
                <div class="mb-3 position-relative">
                  <i class="bi bi-person position-absolute text-muted" style="top: 15px; left: 15px;"></i>
                  <input type="text" name="name" class="form-control" placeholder="YOUR NAME"
                    style="background: #050505; border: 1px solid #333; color: #fff; height: 50px; padding-left: 40px; border-radius: 8px;">
                </div>

                <div class="mb-3 position-relative">
                  <i class="bi bi-envelope position-absolute text-muted" style="top: 15px; left: 15px;"></i>
                  <input type="email" name="email" class="form-control" placeholder="YOUR EMAIL"
                    style="background: #050505; border: 1px solid #333; color: #fff; height: 50px; padding-left: 40px; border-radius: 8px;">
                </div>

                <div class="mb-3 position-relative">
                  <i class="bi bi-phone position-absolute text-muted" style="top: 15px; left: 15px;"></i>
                  <input type="tel" name="phone" class="form-control" placeholder="YOUR PHONE"
                    style="background: #050505; border: 1px solid #333; color: #fff; height: 50px; padding-left: 40px; border-radius: 8px;">
                </div>

                <div class="mb-4 position-relative">
                  <i class="bi bi-chat-dots position-absolute text-muted" style="top: 15px; left: 15px;"></i>
                  <textarea placeholder="HOW CAN WE HELP?" name="comment" class="form-control" rows="3"
                    style="background: #050505; border: 1px solid #333; color: #fff; padding-left: 40px; border-radius: 8px; padding-top: 15px;"></textarea>
                </div>

                <button class="btn w-100" type="submit"
                  style="background: linear-gradient(135deg, #FFD700 0%, #B8960C 100%); color: #000; font-weight: 800; padding: 15px; border-radius: 50px; text-transform: uppercase;">
                  Send Message
                </button>

                <div class="form-results mt-3 d-none text-white text-center"></div>
              </form>
            </div>

            <button title="Close (Esc)" type="button" class="mfp-close text-white"
              style="opacity: 1; top: 15px; right: 15px;">×</button>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
<!-- end subscription popup -->

<!-- javascript libraries -->
<script type="text/javascript" src="{{ asset('assets/index_files/jquery.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/index_files/vendors.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/index_files/main.js') }}"></script>



<script>
  const currencySymbols = {
    USD: "$",
    AED: "AED",
    SAR: "SAR",
    EUR: "€",
    INR: "₹",
    GBP: "£"
  };

  function updateCurrencyDisplay(currency) {
    const symbol = currencySymbols[currency] || "$";
    document.querySelectorAll(".price").forEach(el => {
      const amount = el.getAttribute("data-amount");
      const parsedAmount = parseFloat(amount);
      if (amount && !isNaN(parsedAmount)) {
        el.textContent = `${symbol} ${parsedAmount.toFixed(2)}`;
      } else {
        el.textContent = `${symbol} 0.00`;
      }
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    const dropdown = document.getElementById("currencyDropdown");
    const savedCurrency = localStorage.getItem("selectedCurrency") || "USD";

    dropdown.value = savedCurrency;
    updateCurrencyDisplay(savedCurrency);

    dropdown.addEventListener("change", function () {
      const selected = this.value;
      localStorage.setItem("selectedCurrency", selected);
      updateCurrencyDisplay(selected);
    });
  });
</script>
<!-- Template Javascript -->
<script src="{{ asset('lib/easing/easing.min.js') }}"></script>
<script src="{{ asset('lib/waypoints/waypoints.min.js') }}"></script>
<script src="{{ asset('lib/owlcarousel/owl.carousel.min.js') }}"></script>
<script src="{{ asset('lib/isotope/isotope.pkgd.min.js') }}"></script>
<script src="{{ asset('lib/lightbox/js/lightbox.min.js') }}"></script>

<!-- Contact Javascript File -->
<script src="{{ asset('mail/jqBootstrapValidation.min.js') }}"></script>
<script src="{{ asset('mail/contact.js') }}"></script>

<!-- Template Javascript -->
<script src="{{ asset('js/main.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
</body>

</html>