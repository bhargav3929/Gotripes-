@include('header')

<!-- start page title -->
<!-- About Us -->
<section class="aboutus position-relative mb-0 pb-3" style="margin-bottom: 0 !important; padding-bottom: 20px;">
    <div class="container">
        <div class="row align-items-center justify-content-center">

            <div class="col-lg-5 col-md-10 mb-4 mb-lg-0 appear anime-complete">
                <img src="assets/ourstory_files/1.jpg" class="img-fluid w-100" alt="About Us">
            </div>

            <div class="col-xl-7 col-lg-7 col-md-10 appear anime-child anime-complete about-us-text"
                 data-anime='{"el":"childs","translateY":[30,0],"opacity":[0,1],"duration":600,"delay":0,"staggervalue":300,"easing":"easeOutQuad"}'>

                <h2 class="golden-heading mb-2">ABOUT US</h2>

                <p class="w-100 w-lg-80 mx-auto mb-3 about-para">
                    Welcome to GOTRIPS, a part of Ayn Al Amir Tourism, a dynamic and innovative travel agency dedicated to providing unparalleled travel solutions and consultancy services. Established in January 2024 by Mr. Amer Ali Mohammed, our company is committed to excellence and driven by a passion for travel. With over 13 years of industry experience, Mr. Mohammed has built Ayn Al Amir Tourism L.L.C into a trusted partner for individuals and businesses seeking comprehensive travel solutions.
                </p>

                <p class="w-100 w-lg-80 mx-auto mb-3 about-para">
                    At GOTRIPS, we pride ourselves on our commitment to customer satisfaction, innovation, and continuous improvement. Our strategic partnership with Portway Systems enhances our capabilities, allowing us to offer state-of-the-art Travel Agency Management Software and Travel CRM solutions. This ensures a seamless and efficient travel experience for our clients.
                </p>

                <p class="mb-0 about-para">
                    At GOTRIPS, we pride ourselves on our commitment to customer satisfaction...
                </p>

            </div>
        </div>
    </div>
</section>


<!-- WhatsApp -->
<div class="whats" style="margin:0!important; padding:0!important;">
    <a target="_blank" href="https://api.whatsapp.com/send/?phone=971543651065">
        <img src="assets/ourstory_files/whats.gif" class="img-fluid" alt="WhatsApp">
    </a>
</div>

<!-- Contact Us -->
<section class="position-relative text-white pt-3 pb-5 contact-us-section" 
    style="margin-top: 0 !important; background: url('{{ asset('assets/index_files/kalifa.jpg') }}') center center / cover no-repeat;">
    <div class="position-absolute top-0 bottom-0 start-0 end-0" style="background-color: rgba(0,0,0,0.7); z-index: 0;"></div>
    <div class="container position-relative" style="z-index: 1;">
        <div class="row text-center">
            <div class="col-12">
              <div class="p-4 p-md-5 rounded shadow-lg" style="background-color: rgba(0,0,0,0.5);">
    <h4 class="golden-heading mb-3">Contact Us</h4>

    <p class="w-100 w-md-80 mx-auto mb-3 about-para">
        At <strong>Ayn Al Amir</strong>, your journey begins with a conversation. Whether you're dreaming of a relaxing beach holiday, an exciting adventure, a cultural escape, or a corporate travel solution, our team of experienced travel professionals is here to help you every step of the way.
    </p>

    <p class="w-100 w-md-80 mx-auto mb-3 about-para">
        We understand that planning a trip can be both exciting and overwhelming. That's why we're committed to making the process smooth, personalized, and stress-free.
    </p>

    <p class="w-100 w-md-80 mx-auto mb-4 about-para">
        Have a question? Need a quote? Want to discuss travel ideas? We welcome all inquiries and are happy to assist with bookings, visa guidance, group tours, or tailor-made travel plans.
    </p>
                    <!-- Map & Form Row -->
                    <div class="row mt-5 mb-5 align-items-stretch">
                        <!-- Map -->
                        <div class="col-lg-6 col-md-12 mb-4 d-flex flex-column">
                            <h5 class="text-yellow fw-bold mb-3">Follow map for the location:</h5>
                            <div class="flex-grow-1 position-relative overflow-hidden"
                                style="border: 9px solid #f5ce58; min-height: 400px;">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3654.465167809801!2d53.7285936!3d23.6593174!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e6767007471d31d%3A0x7d02367b5fcb1ff4!2sAyn%20Al%20Amir%20Tourism!5e0!3m2!1sen!2sin!4v1758352262359!5m2!1sen!2sin"
                                    class="w-100 h-100 border-0"
                                    allowfullscreen="" loading="lazy"></iframe>
                            </div>
                        </div>

                        <!-- Form -->
                        <div class="col-lg-6 col-md-12 mb-4 d-flex flex-column">
                            <div class="d-flex flex-column flex-grow-1">
                                <h5 class="text-yellow fw-bold mb-3">Looking for any help?</h5>
                                <div class="flex-grow-1 d-flex flex-column justify-content-between p-4 rounded shadow"
                                    style="background-color: rgba(0,0,0,0.5);">

                                    <div class="position-relative">
                                        <!-- Snackbar popup above form -->
                                        <div id="snackbar"
                                            style="position: absolute; left: 50%; top: -70px; transform: translateX(-50%);
                                                   background: #28a745; color: #fff; border-radius: 6px; padding: 16px 28px;
                                                   box-shadow: 0 6px 18px rgba(0,0,0,0.10); font-size: 16px; font-weight: 400;
                                                   z-index: 100; opacity: 0; transition: opacity 0.3s;">
                                            <span id="snackbarMessage"><strong>Email sent successfully! We will reply you shortly</strong></span>
                                        </div>

                                        <form id="contactForm" action="{{ route('contact.submit') }}" method="POST">
                                            @csrf
                                            <div class="row align-items-stretch">

                                                <div class="col-md-6 col-sm-12 mb-3">
                                                    <input class="form-control required" type="text" name="name" placeholder="Your name" required>
                                                </div>
                                                <div class="col-md-6 col-sm-12 mb-3">
                                                    <input class="form-control required" type="text" name="phone" placeholder="Your mobile" required>
                                                </div>
                                                <div class="col-md-6 col-sm-12 mb-3">
                                                    <input class="form-control required" type="email" name="email" placeholder="Your email" required>
                                                </div>
                                                <div class="col-md-6 col-sm-12 mb-3">
                                                    <select class="form-control" name="booking-city" required>
                                                        <option value="" selected disabled>Regarding</option>
                                                        <option value="Airline Tickets">Airline Tickets</option>
                                                        <option value="Hotel Bookings">Hotel Bookings</option>
                                                        <option value="Holiday Packages">Holiday Packages</option>
                                                        <option value="Car Rentals">Car Rentals</option>
                                                        <option value="Travel Insurance">Travel Insurance</option>
                                                        <option value="Visa Assistance">Visa Assistance</option>
                                                    </select>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <textarea class="form-control" rows="6" name="booking-address" placeholder="Message"></textarea>
                                                </div>
                                                <div class="col-12 text-center">
                                                    <button class="btn btn-warning text-white d-block w-100" type="submit" id="sendButton">
                                                        <span id="buttonText">Send message</span>
                                                        <span id="buttonSpinner" class="spinner-border spinner-border-sm ms-2"
                                                            role="status" aria-hidden="true" style="display:none;"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QR Circles Row -->
                    <h4 class="golden-heading text-center mb-4" style="font-weight:bold;">SCAN US</h4>
                    <div class="row align-items-start mb-5">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-4 mb-lg-0 d-flex flex-column align-items-start text-start">
                            <div class="circle-image mb-3 mx-auto mx-lg-0">
                                <img src="{{ asset('assets/index_files/LocationQR-2.jpg') }}" alt="Location QR" class="img-fluid">
                            </div>
                            <div class="text-center text-lg-start w-100">
                                <p class="mb-1 text-white fw-bold">Address</p>
                                <p class="mb-0" style="color: #debb55;">Office # 10, Beside Big Mart, Sanaiya, Beda Zayed Al Dhafra Region, Abu Dhabi, U.A.E</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-4 mb-lg-0 d-flex flex-column align-items-center text-center">
                            <div class="circle-image mb-3">
                                <img src="{{ asset('assets/index_files/logo.png') }}" alt="Logo" class="img-fluid">
                            </div>
                            <p class="mb-1 text-white fw-bold">Email</p>
                            <p class="mb-0" style="color: #debb55;">info@aynalamirtourism.com</p>
                        </div>
                        <div class="col-lg-4 col-md-12 col-sm-12 d-flex flex-column align-items-end text-end">
                            <div class="circle-image mb-3 mx-auto mx-lg-0">
                                <img src="{{ asset('assets/index_files/WhatsappQR.jpg') }}" alt="WhatsApp QR" class="img-fluid">
                            </div>
                            <div class="text-center text-lg-end w-100">
                                <p class="mb-1 text-white fw-bold">Call Us</p>
                                <p class="mb-0" style="color: #debb55;">+971- 54 365 1065</p>
                                <p class="mb-0" style="color: #debb55;">02 - 245 8519</p>
                                <p class="mb-0" style="color: #debb55;">+971- 50 557 4373</p>
                            </div>
                        </div>
                    </div>

                    <!-- Extra Info Row -->
                    <div class="row text-center mt-1">
                        <div class="col-12">
                            <div class="p-4 p-md-5 rounded shadow-lg" style="background-color: rgba(0,0,0,0.5);">
                                <p class="text-white mb-3">
                                    Your satisfaction is our priority, and we pride ourselves on offering attentive, personalized service to every client. Let us take care of the details, so you can focus on enjoying the journey.
                                </p>
                                <p class="text-white mb-3">
                                    📞 Reach out by phone, 📧 email us, or 📍 visit our office — we're always happy to help you explore the world with confidence and ease.
                                </p>
                                <h5 class="fw-bold text-yellow mt-4 mb-0">
                                    Let's make your travel dreams a reality!
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div style="padding: 32px 18px; background:rgb(0, 0, 0);">
    <h4 class="golden-heading text-center mb-4" style="font-weight:bold;">OUR CORE VALUES</h4>

    <!-- Vision and Mission Section -->
   <section class="py-0 pt-3 pb-5 four_boxes">
    <div class="container">
        <div class="row g-4 align-items-stretch">

            <!-- Feature box: Vision -->
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="feature-box gold-border text-center h-100 d-flex flex-column p-4">
                    <img src="assets/ourstory_files/vision.svg" class="img-fluid" style="height:70px; margin-bottom:15px;">
                    <div class="feature-box-content mt-auto">
                        <h3 class="alt-font fw-600 text-dark-gray fs-19 mb-3">Our Vision</h3>
                        <p class="justify-text vision-text">
                             Our vision is to become a leading global travel solutions provider, known for our dedication to excellence, innovation, and customer satisfaction. We aspire to redefine the travel industry and set new standards of excellence through our strategic partnerships and continuous improvement efforts.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Feature box: Mission -->
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="feature-box gold-border text-center h-100 d-flex flex-column p-4">
                    <img src="assets/ourstory_files/mission.svg" class="img-fluid" style="height:70px; margin-bottom:15px;">
                    <div class="feature-box-content mt-auto">
                        <h3 class="alt-font fw-600 text-dark-gray fs-19 mb-3">Our Mission</h3>
                        <p class="justify-text vision-text">
                             Our mission is to empower individuals and businesses to explore the world with confidence and convenience. We are committed to delivering exceptional service, fostering meaningful connections, and creating memorable travel experiences that exceed expectations.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>



    <!-- Core Values Section -->
    <section class="py-0 pt-5 pb-5 four_boxes" style="background: transparent;">
        <div class="container">
            <div class="row justify-content-center align-items-center mb-4">
                <div class="col text-center appear anime-complete"
                    data-anime="{ &quot;translateY&quot;: [50, 0], &quot;opacity&quot;: [0,1], &quot;duration&quot;: 1200, &quot;delay&quot;: 0, &quot;staggervalue&quot;: 150, &quot;easing&quot;: &quot;easeOutQuad&quot; }">
                </div>
            </div>
            <div class="row justify-content-center appear anime-child anime-complete"
                data-anime="{ &quot;el&quot;: &quot;childs&quot;, &quot;translateX&quot;: [50, 0], &quot;opacity&quot;: [0,1], &quot;duration&quot;: 1200, &quot;delay&quot;: 0, &quot;staggervalue&quot;: 150, &quot;easing&quot;: &quot;easeOutQuad&quot; }">
                
               <!-- Excellence -->
<div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 icon-with-text-style-10 transition-inner-all mb-4 mb-xl-0">
    <div class="feature-box text-center border-end border-xl-1 border-lg-0 border-md-1 border-sm-0 border-color-extra-medium-gray h-100">
        
        <div class="feature-box-icon feature-box-icon-rounded w-100px h-100px rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center" style="background-color: #f8f9fa;">
            <i class="bi bi-trophy icon-large text-warning" style="font-size: 2.5rem;"></i>
        </div>

        <div class="feature-box-content last-paragraph-no-margin">
            <h3 class="alt-font fw-600 text-dark-gray fs-19 d-inline-block mb-3">Excellence</h3>

            <p class="w-100 w-md-90 mx-auto about-para">
                We strive for excellence in everything we do, delivering superior service and value to our clients.
            </p>
        </div>

    </div>
</div>

                
              <!-- Integrity -->
<div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 icon-with-text-style-10 transition-inner-all mb-4 mb-xl-0">
    <div class="feature-box text-center border-end border-xl-1 border-lg-0 border-md-0 border-sm-0 border-color-extra-medium-gray h-100">
        <div class="feature-box-icon feature-box-icon-rounded w-100px h-100px rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center" style="background-color: #f8f9fa;">
            <i class="bi bi-shield-check icon-large text-success" style="font-size: 2.5rem;"></i>
        </div>
        <div class="feature-box-content last-paragraph-no-margin">
            <h3 class="alt-font fw-600 text-dark-gray fs-19 d-inline-block mb-3">Integrity</h3>
            <p class="w-100 w-md-90 mx-auto text-justify about-para">
                We uphold the highest ethical standards, building trust and credibility with our clients and partners.
            </p>
        </div>
    </div>
</div>

                
              
<!-- Innovation -->
<div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 icon-with-text-style-10 transition-inner-all mb-4 mb-xl-0">
    <div class="feature-box text-center border-end border-xl-1 border-lg-0 border-md-0 border-sm-0 border-color-extra-medium-gray h-100">

        <div class="feature-box-icon feature-box-icon-rounded w-100px h-100px rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center" style="background-color: #f8f9fa;">
            <i class="bi bi-lightbulb icon-large text-primary" style="font-size: 2.5rem;"></i>
        </div>

        <div class="feature-box-content last-paragraph-no-margin">
            <h3 class="alt-font fw-600 text-dark-gray fs-19 d-inline-block mb-3">Innovation</h3>

            <p class="w-100 w-md-90 mx-auto text-justify about-para">
                We embrace innovation and creativity, leveraging technology to drive growth and enhance customer experiences.
            </p>
        </div>

    </div>
</div>


                
                
                <!-- Collaboration -->
<div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 icon-with-text-style-10 transition-inner-all mb-4 mb-xl-0">
    <div class="feature-box text-center h-100">

        <div class="feature-box-icon feature-box-icon-rounded w-100px h-100px rounded-circle mb-3 mx-auto d-flex align-items-center justify-content-center" style="background-color: #f8f9fa;">
            <i class="bi bi-people-fill icon-large text-info" style="font-size: 2.5rem;"></i>
        </div>

        <div class="feature-box-content last-paragraph-no-margin">
            <h3 class="d-inline-block alt-font fw-600 text-dark-gray fs-19 mb-3">Collaboration</h3>
            <p class="w-100 w-md-90 mx-auto about-para">
                We believe in the power of collaboration and teamwork, fostering strong partnerships to achieve mutual success.
            </p>
        </div>

    </div>
</div>

            </div>
        </div>
    </section>
</div>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Show button spinner, hide text, disable button
    document.getElementById('buttonText').style.display = 'none';
    document.getElementById('buttonSpinner').style.display = 'inline-block';
    document.getElementById('sendButton').disabled = true;

    // Prepare data for AJAX
    let form = e.target;
    let formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
        },
        body: formData
    })
    .then(async response => {
        document.getElementById('buttonText').style.display = '';
        document.getElementById('buttonSpinner').style.display = 'none';
        document.getElementById('sendButton').disabled = false;

        if (response.ok) {
            form.reset();
            showSnackbar('Email sent successfully! We will reply you shortly', false);
        } else {
            let resJson = await response.json();
            let msg = 'Failed to send message. Please try again.';
            if (resJson.errors) {
                msg = Object.values(resJson.errors).flat().join('<br>');
            }
            showSnackbar(msg, true);
        }
    })
    .catch(error => {
        document.getElementById('buttonText').style.display = '';
        document.getElementById('buttonSpinner').style.display = 'none';
        document.getElementById('sendButton').disabled = false;
        showSnackbar('Failed to send message. Please try again.', true);
    });
});

function showSnackbar(message, isError = false) {
    var snackbar = document.getElementById('snackbar');
    var snackbarMsg = document.getElementById('snackbarMessage');

    if (!isError) {
        snackbarMsg.innerHTML = `<strong>${message}</strong>`;
        snackbar.style.background = "#28a745"; // Success green
    } else {
        snackbarMsg.innerHTML = message; // error not bold
        snackbar.style.background = "#d33"; // Error red
    }
    snackbar.style.display = 'block';
    snackbar.style.opacity = 1;

    setTimeout(function(){
        snackbar.style.opacity = 0;
        setTimeout(function(){
            snackbar.style.display = 'none';
        }, 400);
    }, 2500);
}
</script>

<!-- Styles for layout and spacing tweaks -->
<style>
    .page-top-spacer {
        margin-top: 120px;
    }
    
    /* Match Contact Us font style to About Us */
    .contact-us-section p {
        font-family: inherit;
        font-size: 16px;
        line-height: 1.6;
        font-weight: normal;
        color: white;
    }

    .gold-border {
        border: 4px solid gold;
        border-radius: 0;
        background: none;
        padding: 24px; 
        box-sizing: border-box;
        height: 100%;
    }

    .four_boxes .row {
        display: flex;
        gap: 0;
    }

    .four_boxes .col {
        flex: 1;
        display: flex;
    }

    .four_boxes .col .feature-box {
        width: 100%;
        height: 100%;
    }

    .separator {
        width: 1px;
        min-width: 1px;
        max-width: 1px;
        background: #fff;
        height: 100%;
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        flex: 0 0 1px;
    }

    .aboutus { 
        margin-bottom: 0 !important; 
    }
    
    .aboutus + .whats,
    .whats + section {
        margin-top: 0 !important;
    }
    
    section {
        margin-top: 0 !important;
    }
    
    /* Ensure both sections have identical typography for p tags */
    .aboutus p,
    .contact-us-section p {
        font-family: 'Roboto', sans-serif;
        font-size: 24px;
        line-height: 1.6;
        font-weight: 400;
        margin-bottom: 1rem;
    }

    /* Keep Contact Us text white for dark background */
    .contact-us-section p {
        color: #fff;
    }
    
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
    
    .golden-heading {
        background-color: rgb(255, 210, 63);
        color: white !important;
        padding: 10px 20px;
        border-radius: 0;
        margin-top: 38px;
        margin-bottom: 50px !important;
        display: block;
        width: 100%;
    }
    
    .text-yellow { 
        color: #FFD23F; 
    }
    
    .circle-image {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid #f5ce58;
        background: #000;
    }
    
    .circle-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Bootstrap responsive adjustments */
    @media (max-width: 1199.98px) {
        .separator {
            display: none !important;
        }
    }

    @media (max-width: 991.98px) {
        .circle-image {
            width: 120px;
            height: 120px;
        }
        
        .golden-heading {
            font-size: 1.5rem;
            padding: 8px 15px;
        }
    }

    @media (max-width: 767.98px) {
        .circle-image {
            width: 100px;
            height: 100px;
        }
        
        .feature-box-icon {
            width: 80px !important;
            height: 80px !important;
        }
        
        .feature-box-icon i {
            font-size: 2rem !important;
        }
    }

    @media (max-width: 575.98px) {
        .golden-heading {
            font-size: 1.25rem;
            padding: 6px 10px;
        }
        
        .circle-image {
            width: 80px;
            height: 80px;
        }
    }

    .about-us-text {
        font-family: 'Ranade', sans-serif;
    }

    .justify-text {
    text-align: justify !important;
}



.about-us-text p {
    font-family: 'Ranade', sans-serif !important;
}


.contact-us-section p {
    font-family: 'Ranade', sans-serif !important;
}

.vision-text {
    font-size: 24px !important;
}

.vision-text {
    font-size: 24px !important;
}





.about-para {
    text-align: left;           /* removes the weird word spacing */
    line-height: 1.6;           /* better spacing between lines */
    font-size: 16px;
    hyphens: auto;
    -webkit-hyphens: auto;
    -ms-hyphens: auto;
    word-break: break-word;
}



.icon-with-text-style-10 .feature-box {
    border-right: 1px solid #cfcfcf !important;
}

@media (max-width: 991px) {
    .icon-with-text-style-10 .feature-box {
        border-right: 0 !important;
    }
}






</style>

@include('footer')
