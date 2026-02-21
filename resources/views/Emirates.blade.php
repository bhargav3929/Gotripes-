@include('header')

<style>
    .emirates-page-wrapper {
        background: #000 !important;
        min-height: 100vh;
        padding: 0;
        margin: 0;
        margin-bottom: 0 !important;
        /* Add this */
        padding-bottom: 0 !important;
        /* Add this */
    }

    .emirates-page-wrapper .container-section {
        padding-top: 150px;
        padding-bottom: 0;
        /* Changed from 50px to 0 */
        background: #000;
        min-height: calc(100vh - 200px);
        margin-bottom: 0;
        /* Add this */
    }

    /* Additional fixes */
    .emirates-page-wrapper .container {
        margin-bottom: 0 !important;
        padding-bottom: 20px;
        /* Add small padding only inside container */
    }

    /* Ensure footer connects properly */
    body {
        margin: 0;
        padding: 0;
        background: #000;
    }

    /* Remove any default margins that might cause gaps */
    .emirates-page-wrapper::after {
        content: '';
        display: block;
        clear: both;
        height: 0;
        margin: 0;
        padding: 0;
    }

    /* Prevent global style conflicts - scope styles to emirates page only */
    .emirates-page-wrapper {
        background: #000 !important;
        min-height: 100vh;
        padding: 0;
        margin: 0;
    }

    .emirates-page-wrapper .container-section {
        padding-top: 150px;
        padding-bottom: 50px;
        background: #000;
        min-height: calc(100vh - 200px);
    }

    /* Emirates Selection Styles */
    .emirates-page-wrapper .emirates-header {
        text-align: center;
        margin-bottom: 60px;
        color: #FFD23F;
    }

    .emirates-page-wrapper .emirates-header h1 {
        font-size: 42px;
        font-weight: bold;
        margin-top: 50px;
        margin-bottom: 20px;
        text-shadow: 0 0 20px rgba(255, 210, 63, 0.5);
    }

    .emirates-page-wrapper .emirates-header p {
        font-size: 18px;
        color: #ccc;
        max-width: 800px;
        margin: 0 auto;
        line-height: 1.6;
    }

    .emirates-page-wrapper .emirates-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .emirates-page-wrapper .emirate-card {
        background: #111;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.4s ease;
        border: 2px solid transparent;
        position: relative;
        cursor: pointer;
        text-decoration: none !important;
        color: inherit;
        display: block;
    }

    .emirates-page-wrapper .emirate-card:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 15px 40px rgba(255, 210, 63, 0.4);
        border-color: #FFD23F;
        text-decoration: none !important;
        color: inherit;
    }

    .emirates-page-wrapper .emirate-image {
        width: 100%;
        height: 220px;
        background: #000;
        position: relative;
        overflow: hidden;
    }

    .emirates-page-wrapper .emirate-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .emirates-page-wrapper .emirate-card:hover .emirate-image img {
        transform: scale(1.1);
    }

    .emirates-page-wrapper .emirate-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(0, 0, 0, 0.3), rgba(255, 210, 63, 0.1));
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .emirates-page-wrapper .emirate-card:hover .emirate-overlay {
        opacity: 1;
    }

    .emirates-page-wrapper .emirate-content {
        padding: 25px;
        position: relative;
    }

    .emirates-page-wrapper .emirate-name {
        font-size: 24px;
        font-weight: bold;
        color: #FFD23F;
        margin-bottom: 15px;
        text-align: center;
    }

    .emirates-page-wrapper .emirate-description {
        color: #bbb;
        font-size: 15px;
        line-height: 1.5;
        margin-bottom: 20px;
        text-align: center;
    }

    .emirates-page-wrapper .activities-count {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255, 210, 63, 0.9);
        color: #000;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }

    .emirates-page-wrapper .emirate-arrow {
        position: absolute;
        bottom: 20px;
        right: 25px;
        font-size: 20px;
        color: #FFD23F;
        transition: transform 0.3s ease;
    }

    .emirates-page-wrapper .emirate-card:hover .emirate-arrow {
        transform: translateX(5px);
    }

    /* Default image placeholder */
    .emirates-page-wrapper .default-image-placeholder {
        width: 100%;
        height: 220px;
        background: linear-gradient(45deg, #333, #555);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FFD23F;
        font-size: 24px;
        transition: transform 0.4s ease;
    }

    .emirates-page-wrapper .emirate-card:hover .default-image-placeholder {
        transform: scale(1.1);
    }

    /* Activities Display Styles */
    .emirates-page-wrapper .static-content {
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

    .emirates-page-wrapper .breadcrumb-nav {
        margin-top: 50px;
        margin-bottom: 30px;
    }

    .emirates-page-wrapper .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
        list-style: none;
        display: flex;
        flex-wrap: wrap;
    }

    .emirates-page-wrapper .breadcrumb-item {
        display: flex;
        align-items: center;
    }

    .emirates-page-wrapper .breadcrumb-item+.breadcrumb-item::before {
        content: ">";
        padding: 0 8px;
        color: #ccc;
    }

    .emirates-page-wrapper .breadcrumb-item a {
        color: #FFD23F;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .emirates-page-wrapper .breadcrumb-item a:hover {
        color: #fff;
    }

    .emirates-page-wrapper .breadcrumb-item.active {
        color: #ccc;
    }

    /* Activity Box Styling */
    .emirates-page-wrapper .blog_inner_page {
        margin-bottom: 30px;
        height: 100%;
    }

    .emirates-page-wrapper .activity-box-container {
        display: flex;
        flex-direction: column;
        height: 450px;
        background: #111;
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 2px solid transparent;
    }

    .emirates-page-wrapper .activity-box-container:hover {
        transform: translateY(-8px);
        box-shadow: 0 10px 30px rgba(255, 210, 63, 0.3);
        border-color: #FFD23F;
    }

    .emirates-page-wrapper .box_images {
        width: 100%;
        height: 240px;
        background: #000;
        overflow: hidden;
        flex-shrink: 0;
        position: relative;
    }

    .emirates-page-wrapper .box_images img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .emirates-page-wrapper .activity-box-container:hover .box_images img {
        transform: scale(1.08);
    }

    .emirates-page-wrapper .box_images .default-image-placeholder {
        height: 240px;
    }

    .emirates-page-wrapper .blog_box {
        background: #111;
        padding: 20px 15px;
        margin: 0;
        position: relative;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .emirates-page-wrapper .blog_box h3 {
        font-family: "B", sans-serif;
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

    .emirates-page-wrapper .blog_box p {
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

    .emirates-page-wrapper .location-icon {
        color: #FFD23F;
        font-size: 14px;
        margin-right: 5px;
    }

    .emirates-page-wrapper .author {
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px solid rgba(255, 210, 63, 0.3);
    }

    .emirates-page-wrapper .price {
        font-size: 20px;
        font-weight: bold;
        color: #FFD23F !important;
        text-shadow: 0 0 10px rgba(255, 210, 63, 0.5);
    }

    .emirates-page-wrapper .activities-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
        justify-content: center;
        max-width: 1200px;
        margin: 0 auto;
    }

    .emirates-page-wrapper .activity-item {
        width: 100%;
        max-width: 380px;
        margin: 0 auto;
    }

    /* Animation delays */
    .emirates-page-wrapper .emirate-card:nth-child(1),
    .emirates-page-wrapper .activity-item:nth-child(1) {
        animation: fadeInUp 0.6s ease 0.1s both;
    }

    .emirates-page-wrapper .emirate-card:nth-child(2),
    .emirates-page-wrapper .activity-item:nth-child(2) {
        animation: fadeInUp 0.6s ease 0.2s both;
    }

    .emirates-page-wrapper .emirate-card:nth-child(3),
    .emirates-page-wrapper .activity-item:nth-child(3) {
        animation: fadeInUp 0.6s ease 0.3s both;
    }

    .emirates-page-wrapper .emirate-card:nth-child(4),
    .emirates-page-wrapper .activity-item:nth-child(4) {
        animation: fadeInUp 0.6s ease 0.4s both;
    }

    .emirates-page-wrapper .emirate-card:nth-child(5),
    .emirates-page-wrapper .activity-item:nth-child(5) {
        animation: fadeInUp 0.6s ease 0.5s both;
    }

    .emirates-page-wrapper .emirate-card:nth-child(6),
    .emirates-page-wrapper .activity-item:nth-child(6) {
        animation: fadeInUp 0.6s ease 0.6s both;
    }

    .emirates-page-wrapper .emirate-card:nth-child(7),
    .emirates-page-wrapper .activity-item:nth-child(7) {
        animation: fadeInUp 0.6s ease 0.7s both;
    }

    .emirates-page-wrapper .emirate-card:nth-child(8),
    .emirates-page-wrapper .activity-item:nth-child(8) {
        animation: fadeInUp 0.6s ease 0.8s both;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive */
    @media (max-width: 1200px) {

        .emirates-page-wrapper .emirates-grid,
        .emirates-page-wrapper .activities-grid {
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
    }

    @media (max-width: 768px) {

        .emirates-page-wrapper .emirates-grid,
        .emirates-page-wrapper .activities-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .emirates-page-wrapper .emirates-header h1 {
            font-size: 32px;
        }

        .emirates-page-wrapper .activity-box-container {
            height: 420px;
        }

        .emirates-page-wrapper .blog_box h3 {
            font-size: 17px;
            min-height: 50px;
        }

        .emirates-page-wrapper .static-content {
            padding: 20px;
            font-size: 16px;
            line-height: 1.6;
        }

        .emirates-page-wrapper .container-section {
            padding-top: 120px;
        }
    }

    @media (max-width: 576px) {
        .emirates-page-wrapper .activity-item {
            max-width: none;
        }

        .emirates-page-wrapper .activity-box-container {
            height: 380px;
        }

        .emirates-page-wrapper .box_images {
            height: 200px;
        }

        .emirates-page-wrapper .box_images .default-image-placeholder {
            height: 200px;
        }

        .emirates-page-wrapper .container-section {
            padding-top: 100px;
        }
    }

    /* WhatsApp Button - positioned globally */
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

    /* Back button styling */
    .emirates-page-wrapper .back-btn {
        background: #FFD23F;
        color: #000;
        padding: 12px 30px;
        border-radius: 25px;
        text-decoration: none;
        font-weight: bold;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }

    .emirates-page-wrapper .back-btn:hover {
        background: #fff;
        color: #000;
        text-decoration: none;
        transform: translateX(-5px);
    }

    /* No results styling */
    .emirates-page-wrapper .no-results {
        text-align: center;
        color: #fff;
        font-size: 18px;
        margin-top: 50px;
        grid-column: 1/-1;
        padding: 40px 20px;
    }

    .emirates-page-wrapper .no-results i {
        font-size: 48px;
        color: #FFD23F;
        margin-bottom: 20px;
        display: block;
    }

    .emirates-page-wrapper .no-results p {
        margin-bottom: 10px;
    }

    .emirates-page-wrapper .no-results p:last-of-type {
        color: #ccc;
        margin-bottom: 30px;
    }
</style>

<div class="emirates-page-wrapper">
    <section class="container-section">
        <div class="container">
            @if($emirate)
                {{-- ACTIVITIES VIEW FOR SPECIFIC EMIRATE --}}
                <!-- Breadcrumb -->
                <div class="breadcrumb-nav">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('emirates.index') }}">
                                    <i class="fas fa-arrow-left"></i> Back to Emirates
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ $emirate->emiratesName }} Activities
                            </li>
                        </ol>
                    </nav>
                </div>

                <!-- Emirate Header -->
                <div class="static-content">
                    <h2
                        style="color: #FFD23F; text-align: center; margin-bottom: 25px; font-size: 28px; font-weight: bold;">
                        {{ $emirate->emiratesName }} Activities
                    </h2>
                    <p style="margin-bottom: 0; text-align: center;">
                        {{ $emirate->emiratesDescription }}
                    </p>
                </div>

                <!-- Activities Grid -->
                <div class="activities-grid">
                    @forelse($activities as $activity)
                        <div class="activity-item">
                            <div class="blog_inner_page">
                                <!-- Updated link to pass emiratesID as query parameter -->
                                <a href="{{ route('activities.detail', ['id' => $activity->activityID, 'emirateId' => $emirate->emiratesID]) }}"
                                    style="text-decoration: none;">

                                    <div class="activity-box-container">
                                        <div class="box_images">
                                            @if($activity->activityImage)
                                                <img src="{{ asset($activity->activityImage) }}" alt="{{ $activity->activityName }}"
                                                    onerror="this.onerror=null; this.style.display='none'; this.parentNode.innerHTML='<div class=\'default-image-placeholder\'><i class=\'fas fa-image\'></i></div>';"
                                                    data-no-retina="">
                                            @else
                                                <div class="default-image-placeholder">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="blog_box">
                                            <h3>
                                                {{ $activity->activityName }}<span style="color: #FFD23F;">.</span>
                                            </h3>
                                            <p>
                                                <i class="fas fa-map-marker-alt location-icon"></i>
                                                {{ $activity->activityLocation }}
                                            </p>
                                            <div
                                                class="author d-flex justify-content-center align-items-center position-relative overflow-hidden">
                                                <span class="price"
                                                    data-amount="{{ number_format($activity->activityPrice, 2) }}">
                                                    ${{ number_format($activity->activityPrice, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="no-results">
                            <i class="fas fa-exclamation-circle"></i>
                            <p>No activities found for {{ $emirate->emiratesName }} at the moment.</p>
                            <p>Please check back later for exciting new activities!</p>
                            <div>
                                <a href="{{ route('emirates.index') }}" class="back-btn">
                                    <i class="fas fa-arrow-left"></i> Back to Emirates
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>

            @else
                {{-- EMIRATES SELECTION VIEW --}}
                <div class="emirates-header">
                    <h1>Explore UAE Emirates</h1>
                    <p>
                        Discover amazing activities across the seven emirates of the United Arab Emirates.
                        Each emirate offers unique experiences, from modern cityscapes to traditional heritage sites.
                        Select an emirate to explore available activities and adventures.
                    </p>
                </div>

                <div class="emirates-grid">
                    @php
                        // Map of manual data to use when we have database emirates
                        $manualData = [
                            'Abu Dhabi' => [
                                'desc' => 'The nation’s majestic capital, blending grand cultural landmarks like the Sheikh Zayed Grand Mosque with the high-speed thrills of Yas Island.',
                                'img' => asset('assets/emirates/abudhabi.png')
                            ],
                            'Dubai' => [
                                'desc' => 'A world-renowned icon of luxury and ambition, famous for its record-breaking skyscrapers, vibrant nightlife, and premier desert safari adventures.',
                                'img' => asset('assets/emirates/dubai.png')
                            ],
                            'Sharjah' => [
                                'desc' => 'Celebrated as the UAE’s cultural capital, this emirate offers a rich historical experience through its heritage sites, traditional souks, and acclaimed art museums.',
                                'img' => asset('assets/emirates/sharjah.png')
                            ],
                            'Ajman' => [
                                'desc' => 'A peaceful coastal gem known for its stunning white-sand beaches, luxury waterfront resorts, and a relaxed atmosphere perfect for a quiet getaway.',
                                'img' => asset('assets/emirates/ajman.png')
                            ],
                            'Umm Al Quwain' => [
                                'desc' => 'A tranquil retreat featuring lush mangrove forests and ancient archaeological sites, offering a glimpse into the traditional coastal life of the UAE.',
                                'img' => asset('assets/emirates/ummalquwain.png')
                            ],
                            'Ras Al Khaimah' => [
                                'desc' => 'An adventure enthusiast\'s paradise, home to the rugged Hajar Mountains, the world’s longest zipline, and beautiful terracotta dunes.',
                                'img' => asset('assets/emirates/rasalkhaimah.png')
                            ],
                            'Fujairah' => [
                                'desc' => 'The only emirate situated on the Gulf of Oman, famous for its spectacular mountain scenery and first-class scuba diving locations.',
                                'img' => asset('assets/emirates/fujairah.png')
                            ],
                            'Western Region' => [
                                'desc' => 'Explore the stunning Western Region of Abu Dhabi, home to the vast Liwa Desert, the breathtaking Empty Quarter, and hidden oases perfect for adventure seekers.',
                                'img' => asset('assets/emirates/westernregion.png')
                            ]
                        ];
                    @endphp

                    @forelse($emirates as $emirateItem)
                        @php
                            $name = $emirateItem->emiratesName;
                            $hasManual = isset($manualData[$name]);
                            $displayDesc = $hasManual ? $manualData[$name]['desc'] : Str::limit($emirateItem->emiratesDescription, 120);
                            $displayImg = $hasManual ? $manualData[$name]['img'] : (asset($emirateItem->emiratesImage) ?: 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?q=80&w=800');
                        @endphp
                        <a href="{{ route('emirates.index', ['emiratesID' => $emirateItem->emiratesID]) }}"
                            class="emirate-card">
                            <div class="emirate-image">
                                <img src="{{ $displayImg }}" alt="{{ $name }}"
                                    onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1512453979798-5ea266f8880c?q=80&w=800';">
                                <div class="emirate-overlay"></div>
                                @if($emirateItem->activities_count > 0)
                                    <div class="activities-count">
                                        {{ $emirateItem->activities_count }}
                                        {{ Str::plural('Activity', $emirateItem->activities_count) }}
                                    </div>
                                @endif
                            </div>
                            <div class="emirate-content">
                                <div class="emirate-name">
                                    {{ $name }}
                                </div>
                                <p class="emirate-description">
                                    {{ $displayDesc }}
                                </p>
                                <i class="fas fa-arrow-right emirate-arrow"></i>
                            </div>
                        </a>
                    @empty
                        <div class="no-results">
                            <i class="fas fa-exclamation-circle"></i>
                            <p>No emirates found at the moment.</p>
                            <p>Please check back later for exciting new activities!</p>
                        </div>
                    @endforelse
                </div>
            @endif
        </div>
    </section>
</div>

<!-- WhatsApp Button -->
<div class="whats">
    <a target="_blank"
        href="https://api.whatsapp.com/send/?phone=971543651065&amp;text&amp;type=phone_number&amp;app_absent=0">
        <img src="{{ asset('assets/uaeactivities_files/whats.gif') }}" class="img-fluid" alt="WhatsApp Contact">
    </a>
</div>

<!-- Tawk.to Scripts -->
<img src="{{ asset('assets/uaeactivities_files/matomo.php') }}" alt="" style="border: 0px;" data-no-retina="">
<script src="{{ asset('assets/uaeactivities_files/twk-main.js.download') }}" charset="UTF-8" crossorigin="*"></script>
<script src="{{ asset('assets/uaeactivities_files/twk-vendor.js.download') }}" charset="UTF-8" crossorigin="*"></script>
<script src="{{ asset('assets/uaeactivities_files/twk-chunk-vendors.js.download') }}" charset="UTF-8"
    crossorigin="*"></script>
<script src="{{ asset('assets/uaeactivities_files/twk-chunk-common.js.download') }}" charset="UTF-8"
    crossorigin="*"></script>
<script src="{{ asset('assets/uaeactivities_files/twk-runtime.js.download') }}" charset="UTF-8"
    crossorigin="*"></script>
<script src="{{ asset('assets/uaeactivities_files/twk-app.js.download') }}" charset="UTF-8" crossorigin="*"></script>
<script async src="{{ asset('assets/uaeactivities_files/1ij5c3v7a') }}" charset="UTF-8" crossorigin="*"></script>
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

@include('footer')