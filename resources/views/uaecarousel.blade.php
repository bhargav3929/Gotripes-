
<link href="https://fonts.googleapis.com/css?family=Rajdhani&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
<style>
  .slide-inner .overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5); /* semi-transparent black */
  z-index: 1;
}

.slide-inner .container {
  position: relative;
  z-index: 2; /* Ensure text is above the overlay */
}

</style>

<!-- start of hero -->
<section class="hero-slider hero-style">
  <div class="swiper-container">
    <div class="swiper-wrapper">
      <!-- Slide 1 -->
      <div class="swiper-slide">
        <div class="slide-inner">
          <div class="overlay"></div>
          <img class="slide-img" src="assets/Global Village _ Ayn Al Amir Tourism L.L.C_files/gallery2055761129.jpg" alt="">
          <div class="container center">
            <div data-swiper-parallax="300" class="slide-title">
              <h2 style="margin-right:-550px; ">Global Village</h2>
            </div>
            <div data-swiper-parallax="400" class="slide-text">
              <p  style="margin-right:-550px; ">Dubai - United Arab Emirates</p>
            </div>

          </div>
        </div>
      </div>

      <!-- Slide 2 -->
      <div class="swiper-slide">
        <div class="slide-inner">
          <div class="overlay"></div>
          <img class="slide-img" src="assets/Global%20Village%20_%20Ayn%20Al%20Amir%20Tourism%20L.L.C_files/gallery60752940.jpg" alt="">
          <div class="container center">
            <div data-swiper-parallax="300" class="slide-title">
              <h2 style="margin-right:-550px; ">Global Village</h2>
            </div>
            <div data-swiper-parallax="400" class="slide-text">
              <p style="margin-right:-550px; ">Dubai - United Arab Emirates</p>
            </div>

          </div>
        </div>
      </div>
      <div class="swiper-slide">
        <div class="slide-inner">
          <div class="overlay"></div>
          <img class="slide-img" src="assets/Global%20Village%20_%20Ayn%20Al%20Amir%20Tourism%20L.L.C_files/gallery321521848.jpg" alt="">
          <div class="container center">
            <div data-swiper-parallax="300" class="slide-title">
              <h2 style="margin-right:-550px; ">Global Village</h2>
            </div>
            <div data-swiper-parallax="400" class="slide-text">
              <p style="margin-right:-550px; ">Dubai - United Arab Emirates</p>
            </div>

          </div>
        </div>
      </div>


      
    </div>


  </div>
</section>
<!-- end of hero slider -->

<!-- CSS -->
<style>
.center{
    text-align: center;

}
  h2 {
    line-height: 1.1;
  }

  .container {
    width: 1200px;
    padding: 0 15px;
    margin: 0 auto;
  }

  .hero-slider {
    width: 100%;
    height: 600px;
    position: relative;
    display: flex;
    z-index: 0;
  }

  @media (max-width: 991px) {
    .hero-slider {
      height: 600px;
    }
  }

  @media (max-width: 767px) {
    .hero-slider {
      height: 500px;
    }
  }

  .swiper-container {
    width: 100%;
    height: 100%;
    position: absolute;
    left: 0;
    top: 0;
  }

  .swiper-slide {
    overflow: hidden;
    color: #fff;
  }

  .slide-inner {
    width: 100%;
    height: 100%;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: left;
    overflow: hidden;
  }

  .slide-inner .slide-img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 0;
  }

  .slide-inner .container {
    position: relative;
    z-index: 2;
  }

  .hero-style .slide-title,
  .hero-style .slide-text,
  .hero-style .slide-btns {
    max-width: 690px;
  }

  .hero-style .slide-title h2 {
    font-size: 100px;
    font-weight: 600;
    color: #ffffff;
    margin: 0 0 40px;
    text-transform: capitalize;
    transition: all .4s ease;
  }

  @media (max-width: 1199px) {
    .hero-style .slide-title h2 {
      font-size: 75px;
    }
  }

  @media (max-width: 991px) {
    .hero-style .slide-title h2 {
      font-size: 50px;
      margin-bottom: 35px;
    }
  }

  @media (max-width: 767px) {
    .hero-style .slide-title h2 {
      font-size: 35px;
      margin-bottom: 30px;
    }
  }

  .hero-style .slide-text p {
    opacity: 0.8;
    font-size: 32px;
    font-weight: 500;
    color: #ffffff;
    margin: 0 0 40px;
  }

  @media (max-width: 767px) {
    .hero-style .slide-text p {
      font-size: 16px;
      margin-bottom: 30px;
    }
  }

  .slide-btns > a:first-child {
    margin-right: 10px;
  }

  .theme-btn,
  .theme-btn-s2 {
    background-color: #ffffff;
    font-size: 20px;
    font-weight: 500;
    color: #2b3b95;
    padding: 9px 32px;
    border: 0;
    border-radius: 3px;
    text-transform: uppercase;
    display: inline-block;
    line-height: initial;
    transition: all .4s ease;
  }

  a {
    text-decoration: none;
  }

  .theme-btn-s2 {
    background-color: rgba(255, 255, 255, 0.9);
    color: #131e4a;
  }

  .theme-btn:hover,
  .theme-btn-s2:hover {
    background-color: #2b3b95;
    color: #fff;
  }

  .theme-btn-s3 {
    font-size: 16px;
    font-weight: 500;
    color: #ffffff;
    text-transform: uppercase;
  }

  i.fa-chevron-circle-right {
    height: 22px;
    width: 22px;
  }

  @media (max-width: 991px) {
    .theme-btn,
    .theme-btn-s2,
    .theme-btn-s3 {
      font-size: 13px;
      padding: 15px 25px;
    }
  }

  @media (max-width: 767px) {
    .theme-btn,
    .theme-btn-s2 {
      padding: 13px 20px;
      font-size: 13px;
    }
  }

  .swiper-button-prev,
  .swiper-button-next {
    background: transparent;
    width: 55px;
    height: 55px;
    border: 2px solid #d4d3d3;
    border-radius: 55px;
    opacity: 0;
    visibility: hidden;
    transition: all .3s ease;
  }

  .hero-slider:hover .swiper-button-prev,
  .hero-slider:hover .swiper-button-next {
    opacity: 1;
    visibility: visible;
  }

  .swiper-button-prev {
    left: 25px;
    transform: translateX(50px);
  }

  .swiper-button-next {
    right: 25px;
    transform: translateX(-50px);
  }

  .swiper-pagination-bullet {
    width: 12px;
    height: 12px;
    background: #fff;
    opacity: 0.3;
    transition: all .2s ease;
  }

  .swiper-pagination-bullet-active {
    opacity: 1;
  }

  .swiper-container-horizontal > .swiper-pagination-bullets {
    bottom: 50px;
    left: 50%;
    transform: translateX(-50%);
    max-width: 1200px;
    padding: 0 15px;
  }

  @media (max-width: 767px) {
    .swiper-container-horizontal > .swiper-pagination-bullets {
      bottom: 30px;
    }
  }
</style>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    pagination: {
      el: '.swiper-pagination',
      clickable: true,
    },
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
