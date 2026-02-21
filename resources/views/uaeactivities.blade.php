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

    /* Activity Box Styling */
    .blog_inner_page {
        margin-bottom: 30px;
        height: 100%;
    }
    
    /* Fixed-size container for each activity box */
    .activity-box-container {
        display: flex;
        flex-direction: column;
        height: 450px; /* Fixed height for all boxes */
        background: #111;
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 2px solid transparent;
    }
    
    .activity-box-container:hover {
        transform: translateY(-8px);
        box-shadow: 0 10px 30px rgba(255, 210, 63, 0.3);
        border-color: #FFD23F;
    }
    
    .box_images {
        width: 100%;
        height: 240px; /* Fixed height for images */
        background: #000;
        overflow: hidden;
        flex-shrink: 0;
        position: relative;
    }
    
    .box_images img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .activity-box-container:hover .box_images img {
        transform: scale(1.08);
    }
    
    .blog_box {
        background: #111;
        padding: 20px 15px;
        margin: 0;
        position: relative;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    .blog_box h3 {
        font-family: "B";
        line-height: 1.4;
        color: #cbcaca;
        font-size: 19px;
        margin: 0 0 15px 0;
        text-align: center;
        min-height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        word-break: break-word;
        overflow-wrap: anywhere;
        font-weight: 600;
    }
    
    .blog_box p {
        color: #cbcaca;
        font-size: 16px;
        text-align: center;
        margin: 10px 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 24px;
    }
    
    .location-icon {
        color: #FFD23F;
        font-size: 14px;
        margin-right: 5px;
    }
    
    .author {
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px solid rgba(255, 210, 63, 0.3);
    }
    
    .price {
        font-size: 20px;
        font-weight: bold;
        color: #FFD23F !important;
        text-shadow: 0 0 10px rgba(255, 210, 63, 0.5);
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
                        <a href="/dubai-global-village?id={{ $activity->activityID }}" style="text-decoration: none;">
                            <div class="activity-box-container">
                                <div class="box_images">
                                    <img src="{{ asset($activity->activityImage) }}" alt="{{ $activity->activityName }}" data-no-retina="">
                                </div>
                                <div class="blog_box">
                                    <h3>
                                        {{ $activity->activityName }}<span style="color: #FFD23F;">.</span>
                                    </h3>
                                    <p>
                                        <i class="fas fa-map-marker-alt location-icon"></i>
                                        {{ $activity->activityLocation }}
                                    </p>
                                    <div class="author d-flex justify-content-center align-items-center position-relative overflow-hidden">
                                        <span class="price" data-amount="{{ number_format($activity->activityPrice, 2) }}">
                                            ${{ number_format($activity->activityPrice, 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>
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
<script src="{{ asset('assets/uaeactivities_files/twk-app.js.download') }}" charset="UTF-8" crossorigin="*"></script>
<script async src="{{ asset('assets/uaeactivities_files/1ij5c3v7a') }}" charset="UTF-8" crossorigin="*"></script>
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
