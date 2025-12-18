@include('header')

<!-- Page Title Section -->
<section class="page-title-big-typography cover-background"
    style="background-image: url('{{ asset('assets/index_files/blog-1.jpg') }}'); margin-top: 96px;">
    <div class="container">
        <div class="row extra-very-small-screen align-items-center">
            <div class="col-lg-7 col-sm-8 position-relative page-title-extra-small">
                <h1 class="mb-20px text-shadow-medium" style="color: #fff !important; font-size: 38px;">PRIVACY POLICY</h1>
            </div>
        </div>
    </div>
</section>

<!-- Main Content Section -->
<section class="aboutus position-relative py-5">
    <div class="container">
        <div class="row align-items-start justify-content-start">
            <div class="col-xl-12 col-lg-12 col-md-12">

                <!-- ✅ Styling for better readability -->
                <style>
                    .aboutus p {
                        font-size: 20px;
                        line-height: 1.7;
                        color: #ddd;
                        margin-bottom: 20px;
                    }

                    .aboutus h3 {
                        font-size: 28px;
                        color: #FFD23F;
                        margin-top: 40px;
                        margin-bottom: 15px;
                    }

                    .aboutus ul {
                        padding-left: 25px;
                        margin-bottom: 30px;
                    }

                    .aboutus ul li {
                        font-size: 19px;
                        line-height: 1.7;
                        color: #ccc;
                        margin-bottom: 12px;
                        list-style-type: disc;
                    }

                    .aboutus ul li::marker {
                        color: #FFD23F;
                    }
                </style>

                <!-- ✅ Privacy Policy Content -->
                <h3>Introduction</h3>
                <p>Welcome to Ayn Al Amir. We are committed to protecting your privacy and ensuring that your personal information is handled in a safe and responsible manner. This Privacy Policy outlines how we collect, use, and protect your information when you visit our website (www.gotrips.ai) and use our services.</p>

                <h3>Information We Collect</h3>
                <ul>
                    <li><strong>Personal Information:</strong> We may collect personal information such as your name, email address, phone number, and payment details when you make a booking or contact us.</li>
                    <li><strong>Non-Personal Information:</strong> We may collect non-personal information such as your IP address, browser type, and browsing behavior on our website.</li>
                </ul>

                <h3>How We Use Your Information</h3>
                <ul>
                    <li><strong>To Provide Services:</strong> We use your personal information to process bookings, provide customer support, and communicate with you about your travel arrangements.</li>
                    <li><strong>To Improve Our Services:</strong> We use non-personal information to analyze website usage, improve our services, and enhance user experience.</li>
                    <li><strong>Marketing:</strong> With your consent, we may use your information to send you promotional offers and updates about our services.</li>
                </ul>

                <h3>Information Sharing and Disclosure</h3>
                <ul>
                    <li><strong>Third-Party Service Providers:</strong> We may share your information with third-party service providers who assist us in providing our services, such as payment processors and travel partners.</li>
                    <li><strong>Legal Requirements:</strong> We may disclose your information if required by law or in response to legal requests.</li>
                </ul>

                <h3>Data Security</h3>
                <p>We implement appropriate security measures to protect your personal information from unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the internet or electronic storage is completely secure.</p>

                <h3>Your Rights</h3>
                <ul>
                    <li><strong>Access and Correction:</strong> You have the right to access and correct your personal information held by us.</li>
                    <li><strong>Opt-Out:</strong> You can opt-out of receiving marketing communications from us at any time by following the unsubscribe instructions in our emails.</li>
                </ul>

                <h3>Cookies</h3>
                <p>Our website uses cookies to enhance your browsing experience. Cookies are small data files stored on your device that help us understand how you use our website and improve our services. You can manage your cookie preferences through your browser settings.</p>

                <h3>Changes to This Privacy Policy</h3>
                <p>We may update this Privacy Policy from time to time. Any changes will be posted on our website, and your continued use of our services constitutes acceptance of the updated policy.</p>

                <h3>Contact Us</h3>
                <p>If you have any questions or concerns about this Privacy Policy, please contact us at <a href="mailto:info@gotrips.ai" style="color:#FFD23F;">info@gotrips.ai</a>.</p>

            </div>
        </div>
    </div>
</section>

@include('footer')
