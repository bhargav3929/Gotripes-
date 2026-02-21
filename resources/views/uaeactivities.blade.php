@include('header')

<style>
    /* Static Content Styling */
    .static-content {
        color: #ccc;
        font-size: 18px;
        line-height: 1.8;
        margin-bottom: 50px;
        text-align: justify;
        text-justify: inter-word;
        max-width: 1000px;
        margin-left: auto;
        margin-right: auto;
        padding: 20px 40px;
        background: rgba(24, 24, 24, 0.8);
        border-radius: 12px;
        border-left: 4px solid #FFD23F;
    }

    /* Activity Box Styling - LUXURY UPGRADE */
    .blog_inner_page {
        margin-bottom: 40px;
        height: 100%;
    }
    
    .activity-box-container {
        display: flex;
        flex-direction: column;
        height: 520px; /* Increased height for better spacing */
        background: linear-gradient(180deg, #161616 0%, #0c0c0c 100%);
        border-radius: 20px; /* More modern rounded corners */
        overflow: hidden;
        transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        border: 1px solid rgba(255, 215, 0, 0.05);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
        position: relative;
    }
    
    .activity-box-container:hover {
        transform: translateY(-12px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.8), 0 0 20px rgba(255, 215, 0, 0.15);
        border-color: rgba(255, 215, 0, 0.4);
    }
    
    .box_images {
        width: 100%;
        height: 280px; /* Taller image area */
        background: #000;
        overflow: hidden;
        flex-shrink: 0;
        position: relative;
    }

    /* Gradient overlay on image for better text separation if needed */
    .box_images::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 40%;
        background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);
        z-index: 1;
    }
    
    .box_images img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.8s cubic-bezier(0.2, 0, 0.2, 1);
    }
    
    .activity-box-container:hover .box_images img {
        transform: scale(1.15);
    }

    /* Category Badge */
    .activity-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(8px);
        color: #FFD700;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        z-index: 5;
        border: 1px solid rgba(255, 215, 0, 0.2);
    }
    
    .blog_box {
        padding: 25px 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .blog_box h3 {
        font-family: inherit;
        line-height: 1.3;
        color: #ffffff;
        font-size: 22px;
        margin: 0 0 8px 0;
        text-align: left; /* Aligned left for more professional look */
        min-height: 58px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        font-weight: 700;
        letter-spacing: -0.2px;
    }
    
    .location-tag {
        color: rgba(255, 255, 255, 0.5);
        font-size: 14px;
        text-align: left;
        margin: 0 0 20px 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .location-icon {
        color: #FFD23F;
        font-size: 13px;
    }
    
    .author {
        margin-top: auto;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }
    
    .price-wrapper {
        display: flex;
        flex-direction: column;
    }

    .price-label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, 0.4);
        margin-bottom: 2px;
    }
    
    .price {
        font-size: 24px !important;
        font-weight: 800 !important;
        color: #FFD700 !important;
        text-shadow: 0 0 20px rgba(255, 215, 0, 0.2);
    }
    
    /* Grid Layout */
    .activities-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
        justify-content: center;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .activity-item {
        width: 100%;
        max-width: 380px;
        margin: 0 auto;
    }
    
    /* Responsive adjustments */
    @media (max-width: 1200px) {
        .activities-grid {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
    }
    
    @media (max-width: 768px) {
        .activities-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .activity-box-container {
            height: 420px;
        }
        
        .blog_box h3 {
            font-size: 17px;
            min-height: 50px;
        }
        
        .static-content {
            padding: 20px;
            font-size: 16px;
            line-height: 1.6;
        }
    }
    
    @media (max-width: 576px) {
        .activity-item {
            max-width: none;
        }
        
        .activity-box-container {
            height: 380px;
        }
        
        .box_images {
            height: 200px;
        }
    }
    
    /* Page Background */
    html, body {
        background: #000 !important;
    }
    
    /* Loading animation */
    .activity-box-container {
        opacity: 0;
        animation: fadeInUp 0.6s ease forwards;
    }
    
    .activity-box-container:nth-child(1) { animation-delay: 0.1s; }
    .activity-box-container:nth-child(2) { animation-delay: 0.2s; }
    .activity-box-container:nth-child(3) { animation-delay: 0.3s; }
    .activity-box-container:nth-child(4) { animation-delay: 0.4s; }
    .activity-box-container:nth-child(5) { animation-delay: 0.5s; }
    .activity-box-container:nth-child(6) { animation-delay: 0.6s; }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* WhatsApp Button */
    .whats {
        width: 60px;
        position: fixed;
        bottom: 3%;
        left: 1%;
        z-index: 10000;
        border-radius: 90px;
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    
    .whats:hover {
        transform: scale(1.1);
    }
    
    .whats img { 
        height: 60px; 
        width: 60px;
    }

    /* Modal trigger button - PREMIUM CTA UPGRADE */
    .book-now-overlay {
        background: linear-gradient(135deg, #FFD700 0%, #FFB800 100%);
        color: #000 !important;
        padding: 12px 28px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 15px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: none;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-left: auto;
    }

    .book-now-overlay:hover {
        background: #FFFFFF;
        color: #000 !important;
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 10px 25px rgba(255, 215, 0, 0.4);
    }

    .book-now-overlay:active {
        transform: translateY(-2px);
    }

    /* Price display tweak */
    .author {
        border-top: 1px solid rgba(255, 215, 0, 0.15) !important;
        padding-top: 20px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }
    
    .price {
        font-size: 24px !important;
        font-weight: 800 !important;
        letter-spacing: -0.5px;
    }

    /* Snackbar for notifications */
    .classic-snackbar {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 20000;
        color: #111;
        border-radius: 5px;
        font-size: 16px;
        font-weight: 600;
        padding: 12px 25px;
        text-align: center;
        display: none;
        opacity: 0;
        transition: opacity 0.4s;
        box-shadow: 0 3px 15px rgba(0,0,0,0.3);
        background: #ffef8e;
        border-bottom: 4px solid #FFD700;
    }
    .classic-snackbar.success { background: #50cb4a; color: #111; border-bottom: 4px solid #9be58d;}
    .classic-snackbar.failed { background: #fa5353; color: #fff; border-bottom: 4px solid #c44f4f;}
    .classic-snackbar.show { display: block; opacity: 1; }

    /* Loader inside buttons */
    .btn-loader {
        display: inline-block;
        width: 18px;
        height: 18px;
        border: 2px solid rgba(0,0,0,0.3);
        border-radius: 50%;
        border-top-color: #000;
        animation: spin 1s linear infinite;
        margin-right: 8px;
        vertical-align: middle;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* Form icon adjustment */
    .form-icon-inside {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 2;
        color: #000 !important;
    }
</style>

<!-- Start Section -->
<section class="about industries" style="padding-top: 150px; background: #000;">
    <div class="container">
        <!-- Static Content Section - Properly Formatted -->
        <div class="static-content">
            <h2 style="color: #FFD23F; text-align: center; margin-bottom: 25px; font-size: 28px; font-weight: bold;">
                Discover Amazing UAE Activities
            </h2>
            <p style="margin-bottom: 20px;">
                The UAE offers a wide range of activities, from desert safaris and dune bashing to luxury shopping in world-class malls. Visitors can explore iconic landmarks like the Burj Khalifa and Sheikh Zayed Grand Mosque, each offering breathtaking views and rich cultural experiences.
            </p>
            <p style="margin-bottom: 20px;">
                Water sports such as jet skiing, snorkeling, and sailing are popular along the beautiful coastline, where crystal-clear waters meet pristine beaches. The country's strategic location provides perfect conditions for both adventure seekers and relaxation enthusiasts.
            </p>
            <p style="margin-bottom: 0;">
                The UAE also hosts vibrant cultural festivals and sporting events throughout the year, showcasing its rich heritage and modern lifestyle. From traditional souks to contemporary art galleries, there's something for every traveler to discover and enjoy.
            </p>
        </div>
        
        <!-- Activities Grid -->
        <div class="activities-grid">
            @forelse($activities as $activity)
                <div class="activity-item">
                    <div class="blog_inner_page">
                        <div class="activity-box-container position-relative">
                            <!-- Premium Badge -->
                            <div class="activity-badge">UAE Experience</div>
                            
                            <a href="/dubai-global-village?id={{ $activity->activityID }}" style="text-decoration: none;">
                                <div class="box_images">
                                    <img src="{{ asset($activity->activityImage) }}" alt="{{ $activity->activityName }}" data-no-retina="">
                                </div>
                            </a>
                            <div class="blog_box">
                                <a href="/dubai-global-village?id={{ $activity->activityID }}" style="text-decoration: none;">
                                    <h3>{{ $activity->activityName }}</h3>
                                    <div class="location-tag">
                                        <i class="fas fa-map-marker-alt location-icon"></i>
                                        {{ $activity->activityLocation }}
                                    </div>
                                </a>
                                
                                <div class="author">
                                    <div class="price-wrapper">
                                        <span class="price-label">Starting From</span>
                                        <span class="price" data-amount="{{ $activity->activityPrice }}">
                                            AED {{ number_format($activity->activityPrice, 2) }}
                                        </span>
                                    </div>
                                    <button type="button" class="book-now-overlay open-booking-modal" 
                                        data-id="{{ $activity->activityID }}"
                                        data-name="{{ $activity->activityName }}"
                                        data-price="{{ $activity->activityPrice }}">
                                        Book Now
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
@empty
                <div style="text-align: center; color: #fff; font-size: 18px; margin-top: 50px;">
                    <i class="fas fa-exclamation-circle" style="font-size: 48px; color: #FFD23F; margin-bottom: 20px;"></i>
                    <p>No activities found at the moment.</p>
                    <p style="color: #ccc;">Please check back later for exciting new activities!</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- WhatsApp Button -->
<div class="whats">
    <a target="_blank" href="https://api.whatsapp.com/send/?phone=971543651065&amp;text&amp;type=phone_number&amp;app_absent=0">
        <img src="{{ asset('assets/uaeactivities_files/whats.gif') }}" class="img-fluid" data-no-retina="" alt="WhatsApp Contact">
    </a>
</div>

<!-- Tawk.to Scripts -->
<img src="{{ asset('assets/uaeactivities_files/matomo.php') }}" alt="" style="border: 0px;" data-no-retina="">
<script src="{{ asset('assets/uaeactivities_files/twk-main.js.download') }}" charset="UTF-8" crossorigin="*"></script>
<script src="{{ asset('assets/uaeactivities_files/twk-vendor.js.download') }}" charset="UTF-8" crossorigin="*"></script>
<script src="{{ asset('assets/uaeactivities_files/twk-chunk-vendors.js.download') }}" charset="UTF-8" crossorigin="*"></script>
<script src="{{ asset('assets/uaeactivities_files/twk-chunk-common.js.download') }}" charset="UTF-8" crossorigin="*"></script>
<script src="{{ asset('assets/uaeactivities_files/twk-runtime.js.download') }}" charset="UTF-8" crossorigin="*"></script>
@include('partials.activity_booking_modal')

<div class="classic-snackbar" id="mainSnackbar"></div>

<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/67a073313a8427326078f27b/1ij5c3v7a';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>

@include('footer')



