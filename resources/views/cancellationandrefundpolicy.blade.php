@include('header')

<!-- Page Title Section -->
<section class="top-space-margin page-title-big-typography cover-background"
    style="background-image: url('{{ asset('assets/index_files/blog-1.jpg') }}'); margin-top: 96px;">
    <div class="container">
        <div class="row extra-very-small-screen align-items-center">
            <div class="col-lg-7 col-sm-8 position-relative page-title-extra-small">
                <h1 class="mb-20px text-shadow-medium" style="color: #fff !important; font-size: 38px;">Cancellation and Refund Policy</h1>

            </div>
        </div>
    </div>
</section>

<!-- Main Content Section -->
<section class="aboutus position-relative py-5">
    <div class="container">
        <div class="row align-items-start justify-content-start">
            <div class="col-xl-12 col-lg-12 col-md-12">

                <!-- ✅ Styling for clarity and better readability -->
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

                    .aboutus ul li strong {
  display: block;
  list-style: none;
  margin-left: -25px; /* adjust if needed */
}

.aboutus ul li strong::before {
  content: none;
}





                </style>

                <!-- ✅ Policy Content -->
                <h3>Introduction</h3>
                <p>At Ayn Al Amir, we understand that plans can change. Our cancellation and refund policy is designed to provide flexibility and clarity for our customers.</p>

                <h3>Cancellation Policy</h3>
                <ul>
                    <li><strong>Cancellation by Customer:</strong></li>
                    <li>Cancellations made more than 30 days before the scheduled departure date will receive a full refund.</li>
                    <li>Cancellations made between 15 to 30 days before the scheduled departure date will incur a 50% cancellation fee.</li>
                    <li>Cancellations made less than 15 days before the scheduled departure date are non-refundable.</li>
                    <li><strong>Cancellation by Ayn Al Amir:</strong></li>
                    <li>In the unlikely event that Ayn Al Amir cancels a trip, customers will receive a full refund or the option to reschedule.</li>
                </ul>

                <h3>Refund Policy</h3>
                <ul>
                    <li><strong>Refund Process:</strong></li>
                    <li>Refunds will be processed within 7-10 business days from the date of cancellation.</li>
                    <li>Refunds will be issued to the original payment method used at the time of booking.</li>
                    <li><strong>Non-Refundable Services:</strong></li>
                    <li>Certain services, such as special promotions, last-minute bookings, and non-refundable tickets, are not eligible for refunds.</li>
                </ul>

                <h3>Changes to Bookings</h3>
                <ul>
                    <li><strong>Modifications:</strong></li>
                    <li>Customers can request changes to their bookings up to 15 days before the scheduled departure date. Changes are subject to availability and may incur additional fees.</li>
                </ul>

                <h3>No-Show Policy</h3>
                <ul>
                    <li><strong>No-Show:</strong></li>
                    <li>If a customer fails to show up for the scheduled trip without prior notice, the booking will be considered a no-show and will not be eligible for a refund.</li>
                </ul>

                <h3>Contact Us</h3>
                <ul>
                    <li><strong>Support:</strong></li>
                    <li>For any questions or assistance regarding cancellations and refunds, please contact our customer support team at <a href="mailto:support@gotrips.ai" style="color: #FFD23F;">support@gotrips.ai</a>.</li>
                </ul>

            </div>
        </div>
    </div>
</section>

@include('footer')
