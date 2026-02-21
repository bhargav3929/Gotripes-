@include('header')

<!-- start page title -->
<section class="top-space-margin page-title-big-typography cover-background"
    style="background-image: url('{{ asset('assets/index_files/blog-1.jpg') }}'); margin-top: 96px;">
    <div class="container">
        <div class="row extra-very-small-screen align-items-center">
            <div class="col-lg-7 col-sm-8 position-relative page-title-extra-small appear anime-child anime-complete"
                data-anime="{ &quot;el&quot;: &quot;childs&quot;, &quot;opacity&quot;: [0, 1], &quot;translateX&quot;: [-30, 0], &quot;duration&quot;: 800, &quot;delay&quot;: 0, &quot;staggervalue&quot;: 300, &quot;easing&quot;: &quot;easeOutQuad&quot; }">
                <<h1 class="mb-20px text-shadow-medium" style="color: #fff !important; font-size: 38px;">
                    TERMS AND CONDITIONS
                </h1>
            </div>
        </div>
    </div>
</section>
<!-- end page title -->

<!-- start section -->
<section class="aboutus position-relative">
    <div class="container">
        <div class="row align-items-start justify-content-start">
            <div class="col-xl-12 col-lg-12 col-md-10 appear anime-child anime-complete"
                data-anime="{ &quot;el&quot;: &quot;childs&quot;, &quot;translateY&quot;: [30, 0], &quot;opacity&quot;: [0,1], &quot;duration&quot;: 600, &quot;delay&quot;: 0, &quot;staggervalue&quot;: 300, &quot;easing&quot;: &quot;easeOutQuad&quot; }">

                <style>
                    .aboutus p {
                        font-size: 20px;
                        line-height: 1.8;
                        color: #ddd;
                        margin-bottom: 20px;
                    }

                    .aboutus h3 {
                        font-size: 30px;
                        color: #FFD23F;
                        margin-top: 40px;
                        margin-bottom: 15px;
                        text-transform: uppercase;
                        font-weight: bold;
                    }

                    .aboutus ul {
                        padding-left: 25px;
                        margin-bottom: 30px;
                    }

                    .aboutus ul li {
                        font-size: 20px;
                        line-height: 1.8;
                        color: #ccc;
                        margin-bottom: 12px;
                        list-style-type: disc;
                    }

                    .aboutus ul li strong {
                        display: block;
                        list-style: none;
                        font-weight: bold;
                        text-transform: uppercase;
                        margin-left: -25px;
                    }

                    .aboutus ul li strong::before {
                        content: none;
                    }

                    .aboutus ul li::marker {
                        color: #FFD23F;
                    }
                </style>

                <h3>Introduction</h3>
                <p>Welcome to Ayn Al Amir. By accessing our website (www.gotrips.ai) and using our services, you agree
                    to comply with and be bound by the following terms and conditions.</p>

                <h3>Definitions</h3>
                <ul>
                    <li><strong>We, Us, Our:</strong> Refers to Ayn Al Amir.</li>
                    <li><strong>You, User:</strong> Refers to any individual accessing our website or using our
                        services.</li>
                    <li><strong>Services:</strong> Includes all travel-related services provided by Ayn Al Amir.</li>
                </ul>

                <h3>Booking and Payment</h3>
                <ul>
                    <li>All bookings are subject to availability and confirmation.</li>
                    <li>Payments must be made in full at the time of booking.</li>
                    <li>We accept various payment methods, including credit/debit cards and online transfers.</li>
                </ul>

                <h3>Cancellations and Refunds</h3>
                <ul>
                    <li>Cancellations must be made in accordance with our cancellation policy.</li>
                    <li>Refunds will be processed based on the terms outlined at the time of booking.</li>
                    <li>Certain bookings may be non-refundable or subject to cancellation fees.</li>
                </ul>

                <h3>User Responsibilities</h3>
                <ul>
                    <li>Users must provide accurate and complete information during the booking process.</li>
                    <li>Users are responsible for ensuring they meet all travel requirements, including visas and
                        health regulations.</li>
                </ul>

                <h3>Limitation of Liability</h3>
                <ul>
                    <li>Ayn Al Amir is not liable for any direct, indirect, incidental, or consequential damages
                        arising from the use of our services.</li>
                    <li>We are not responsible for any delays, cancellations, or changes in travel arrangements caused
                        by third-party providers.</li>
                </ul>

                <h3>Privacy Policy</h3>
                <ul>
                    <li>Our Privacy Policy outlines how we collect, use, and protect your personal information.</li>
                    <li>By using our services, you consent to the collection and use of your information as described
                        in our Privacy Policy.</li>
                </ul>

                <h3>Changes to Terms and Conditions</h3>
                <ul>
                    <li>We reserve the right to modify these terms and conditions at any time.</li>
                    <li>Any changes will be posted on our website, and continued use of our services constitutes
                        acceptance of the updated terms.</li>
                </ul>

                <h3>Governing Law</h3>
                <ul>
                    <li>These terms and conditions are governed by the laws of [Your Country/State].</li>
                    <li>Any disputes arising from these terms will be subject to the exclusive jurisdiction of the
                        courts in [Your Country/State].</li>
                </ul>

                <h3>Contact Us</h3>
                <ul>
                    <li>For any questions or concerns regarding these terms and conditions, please contact us at
                        info@gotrips.ai.</li>
                </ul>
            </div>
        </div>
    </div>
</section>
<!-- end section -->

<!-- WHATSAPP -->
<div class="whats">
    <a target="_blank"
        href="https://api.whatsapp.com/send/?phone=971543651065&amp;text&amp;type=phone_number&amp;app_absent=0">
        <img src="assets/termsandconditions_files/whats.gif" class="img-fluid" data-no-retina="">
    </a>
</div>

<style>
    .whats {
        width: 60px;
        position: fixed;
        bottom: 3%;
        left: 1%;
        z-index: 10000;
        border-radius: 90px;
        overflow: hidden;
    }

    .whats img {
        height: 60px;
    }
</style>

<!-- FOOTER & Scripts (keep your existing footer and Tawk.to scripts here) -->
@include('footer')
