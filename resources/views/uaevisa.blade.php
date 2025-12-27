@include('header')

<style>
        .submit-btn {
        grid-column: span 2;
        justify-self: center;
        background-color: #28a745; /* Green background */
        color: white;
        font-weight: bold;
        padding: 16px 40px;
        font-size: 20px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        }

        .submit-btn:hover {
          background-color: #218838;
        }

        /* ========================
          Background Image Wrapper
        ========================== */
        .image-overlay {
          position: relative;
          min-height: 100vh;
          width: 100%;
        }

        .image-overlay img {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          object-fit: cover;
          z-index: -1;
        }

        /* ========================
          Overlay with Semi-Dark Background
        ========================== */
        .overlay {
          position: relative; /* not absolute, so it grows with content */
          width: 100%;
          background: rgba(0, 0, 0, 0.0);
          padding: 60px 20px;
          display: flex;
          justify-content: center;
          align-items: flex-start;
        }

        /* ========================
          Form Container
        ========================== */
        .form-container {
          background: rgba(0, 0, 0, 0.75);
          color: white;
          padding: 30px;
          max-width: 1000px;
          width: 100%;
          border-radius: 8px;
          box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }

        .form-container h2 {
          text-align: center;
          margin-bottom: 20px;
        }
         input.form-control,
  select.form-control,
  textarea.form-control {
    height: 45px;   /* Same height for text, select, date */
  }

  input[type="file"].form-control {
    height: auto;   /* Allow file input default height */
    padding: 8px;
  }

        /* ========================
          Form Layout
        ========================== */
        form {
          grid-template-columns: 1fr 1fr;
          gap: 20px;
        }

        .form-group {
          display: flex;
          flex-direction: column;
        }

        .form-group.full-width {
          grid-column: span 2;
        }

        label {
          margin-bottom: 5px;
          font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        input[type="date"],
        select {
          padding: 8px;
          border: 1px solid #ccc;
          border-radius: 4px;
          box-sizing: border-box;
        }

      /* Checkboxes */
      .form-check {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
      }
      .form-check-input {
        margin-right: 10px;
        margin-top: 0;
        accent-color: #FFD23F; /* optional: adds theme color to checkboxes/radios */
      }
      .form-check-inline {
        display: inline-flex;
        align-items: center;
        margin-right: 20px;
      }

        /* ========================
          Submit Button
        ========================== */
        .submit-btn {
          grid-column: span 2;
          justify-self: center;
          background-color: #FFD23F;
          color: black;
          padding: 12px 24px;
          border: none;
          border-radius: 4px;
          cursor: pointer;
          font-weight: bold;
          font-size: 16px;
        }

        .submit-btn:hover {
          background-color: #e0b000;
        }

        /* ========================
          Responsive
        ========================== */
        @media (max-width: 768px) {
          form {
            grid-template-columns: 1fr;
          }

          .submit-btn {
            width: 100%;
            grid-column: span 1;
          }

          .form-container {
            padding: 20px;
          }

          .overlay {
            padding: 30px 10px;
          }
        }

        .golden-heading {
    background-color: rgb(255, 210, 63);
    color: white !important;
    padding: 10px 20px;
    border-radius: 0;
    margin-top: 40px;
    margin-bottom: 20px !important; /* Ensure space below */
    display: block;
    width: 100%;
}
/* Small submit button is handled by Bootstrap .btn-sm */
.submit-btn {
  font-size: 0.9rem;
  padding: 0.4rem 1.2rem;
  position: relative;
}

/* White Spinner on Golden Background */
.spinner-border-white {
  width: 1rem;
  height: 1rem;
  border: 0.125rem solid transparent;
  border-top: 0.125rem solid #ffffff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Snackbar above the button, centered */
.snackbar {
  visibility: hidden;
  min-width: 160px;
  max-width: 260px;
  background-color: #28a745;
  color: #fff;
  text-align: center;
  border-radius: 4px;
  padding: 8px 16px;
  position: absolute;
  left: 50%;
  bottom: 120%;
  transform: translateX(-50%);
  z-index: 10;
  font-size: 0.95rem;
  opacity: 0;
  transition: opacity 0.3s, visibility 0s linear 0.3s;
  box-shadow: 0 2px 12px rgba(0,0,0,0.15);
  pointer-events: none;
}
.snackbar.show {
  visibility: visible;
  opacity: 1;
  transition: opacity 0.3s;
  pointer-events: auto;
}

/* Processing Snackbar - Blue-White Theme */
.processing-snackbar {
  visibility: hidden;
  min-width: 200px;
  max-width: 300px;
  background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
  color: #ffffff;
  font-weight: bold;
  text-align: center;
  border-radius: 6px;
  padding: 10px 18px;
  position: absolute;
  left: 50%;
  bottom: 120%;
  transform: translateX(-50%);
  z-index: 1060;
  font-size: 0.95rem;
  opacity: 0;
  transition: opacity 0.4s, visibility 0s linear 0.4s;
  box-shadow: 0 4px 20px rgba(0, 123, 255, 0.3);
  border: 1px solid #007bff;
  pointer-events: none;
}
.processing-snackbar.show {
  visibility: visible;
  opacity: 1;
  transition: opacity 0.4s;
}

.golden-text {
    color: #FFD23F !important;
    font-weight: bold;
}

.golden-strong {
    color: #FFD23F !important;
    font-weight: bold;
}

/* Enhanced Black-Golden Overlay */
#priceOverlay {
  background: rgba(0, 0, 0, 0.85) !important;
}

#priceOverlay > div {
  background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #000000 100%) !important;
  border: 2px solid #FFD23F !important;
  box-shadow: 0 0 30px rgba(255, 210, 63, 0.4), inset 0 0 20px rgba(255, 210, 63, 0.1) !important;
  color: #FFD23F !important;
}

#priceOverlay h4 {
  color: #FFD23F !important;
  text-shadow: 0 0 10px rgba(255, 210, 63, 0.5);
  margin-bottom: 20px;
  font-weight: bold;
}

#priceOverlay p {
  color: #ffffff !important;
  margin: 8px 0;
}

#priceOverlay strong {
  color: #FFD23F !important;
  font-weight: bold;
}

#priceOverlay hr {
  border-color: #FFD23F !important;
  opacity: 0.6;
}

#payButton {
  background: linear-gradient(135deg, #FFD23F 0%, #e0b000 100%) !important;
  color: #000000 !important;
  font-weight: bold;
  border: 2px solid #FFD23F !important;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(255, 210, 63, 0.3);
}

#payButton:hover {
  background: linear-gradient(135deg, #e0b000 0%, #FFD23F 100%) !important;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(255, 210, 63, 0.4);
}

#closeOverlay {
  background: rgba(255, 255, 255, 0.1) !important;
  border: 1px solid #FFD23F !important;
  color: #FFD23F !important;
  transition: all 0.3s ease;
}

#closeOverlay:hover {
  background: rgba(255, 210, 63, 0.2) !important;
  color: #ffffff !important;
}

.about-text {
    text-align: justify;
    text-justify: inter-word;
    text-align-last: left;
    hyphens: auto;
    line-height: 1.65;
    
    /* THE REAL FIX */
    max-width: 900px;          /* prevents stretched spacing */
    margin-left: auto;
    margin-right: auto;
}

.visa-section p {
    text-align: justify;
    text-justify: inter-word;
      line-height: 1.2;
}

.visa-section p {
    text-align: justify;
    text-justify: inter-word;
    line-height: 1.2;     /* clean spacing */

}



.golden-heading +ul {
  padding-left: 0 !important;
  margin-left: 0 !important;
  list-style-position: inside;
}





</style>

<div class="aboutus" style="background-color: #000000;">
<div class="container" style="background-color: #000000; color:#ccc; padding-top:50px; padding-bottom:50px;" >
    {{-- <h2 class="golden-heading">VISA REQUIREMENTS</h2> --}}
    <h2 class="golden-heading" style="font-size: 24px;">VISA REQUIREMENTS</h2>

   <p>
    <strong class="question-yellow">Do I need a visa to travel to the UAE?</strong><br><br>
       
People from different nationalities must satisfy different criteria to get a valid visa to enter United Arab Emirates (UAE). Read on to know about getting a UAE visa while travelling to any of the Emirates (Abu Dhabi, Dubai, Sharjah, Ajman, Umm Al Quwain, Ras Al Khaimah and Fujairah).</p>
  
<h3  class="golden-heading">VISA REQUIREMENTS FOR US CITIZENS</h3>
    <p>American citizens who have regular passports do not need to have a visa to visit the UAE. However, please make sure you fulfil following criteria's:</p>
    <ul>
      <li>1. Original passport signed by the bearer, should not expire within six (6) months from the expected time of arrival in the UAE.</li>
      <li>2. Confirmed round-trip airline ticket or airline ticket to other destination(s).</li>
      
    </ul>
    <ul>The visas are available upon arrival at the UAE airports and the American citizens can stay for 1 month in the UAE. However, if you are going to stay longer, you have to contact the immigration officer at the airport or the local immigration office in the UAE and apply for the same.</ul>
</div>
</div>

<div class="image-overlay" style="padding-top:80px;">
  <img src="assets/index_files/s4.jpg" alt="Banner" style="background-color: rgba(0,0,0,0.7); z-index: 0; height:100%"/>
  <div class="overlay">
  <div class="form-container">
  <h2 style="color:#FFD23F">Visit Visa Application</h2>
  
<form method="POST" action="{{ route('uaev.submit') }}" enctype="multipart/form-data">
    @csrf
  <div class="row">
    <!-- Nationality -->
    <div class="col-md-6 mb-3">
      <label for="nationality">Nationality</label>
      <select id="nationality" name="nationality" class="form-control" required >
        <option value="">-------</option>
      </select>
    </div>

    <!-- Residence -->
    <div class="col-md-6 mb-3">
      <label for="residence">I currently reside in</label>
      <select id="residence" name="residence" class="form-control" required>
        <option value="">-------</option>
      </select>
    </div>

    <!-- First & Last Name -->
    <div class="col-md-6 mb-3">
      <label for="first_name">Principal Applicant's First Name</label>
      <input type="text" id="first_name" name="first_name" class="form-control" required >
    </div>
    <div class="col-md-6 mb-3">
      <label for="last_name">Principal Applicant's Last Name</label>
      <input type="text" id="last_name" name="last_name" class="form-control" required>
    </div>

    <!-- ✅ Checkboxes -->
    <div class="col-12 mb-3">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="passport_valid" name="passport_valid" value="1">
        <label class="form-check-label" for="passport_valid">
          Passport is valid for at least 6 months
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="not_stay_long" name="not_stay_long" value="1">
        <label class="form-check-label" for="not_stay_long">
          I do NOT wish to stay in the UAE for more than 30 days
        </label>
      </div>
    </div>

    <!-- ✅ Radios -->
    <div class="col-12 mb-3">
      <label class="form-label">Gender</label><br>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="gender" id="male" value="Male"required>
        <label class="form-check-label" for="male">Male</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="gender" id="female" value="Female">
        <label class="form-check-label" for="female">Female</label>
      </div>
    </div>

    <!-- DOB, Arrival, Departure -->
    <div class="col-md-4 mb-3">
      <label for="dob">Date of Birth</label>
      <input type="date" id="dob" name="dob" class="form-control"required>
    </div>
    <div class="col-md-4 mb-3">
      <label for="arrival_date">Date of Arrival in UAE</label>
      <input type="date" id="arrival_date" name="arrival_date" class="form-control"required>
    </div>
    <div class="col-md-4 mb-3">
      <label for="departure_date">Date of Departure in UAE</label>
      <input type="date" id="departure_date" name="departure_date" class="form-control" required>
    </div>

    <!-- Phone & Email -->
    <div class="col-md-6 mb-3">
      <label for="phone">Phone Number (with country code)</label>
      <input type="tel" id="phone" name="phone" class="form-control" required placeholder="e.g., +971501234567">
    </div>
    <div class="col-md-6 mb-3">
      <label for="email">Email Address</label>
      <input type="email" id="email" name="email" class="form-control" required>
    </div>

    <!-- Profession & Marital Status -->
    <div class="col-md-6 mb-3">
      <label for="profession">Profession</label>
      <input type="text" id="profession" name="profession" class="form-control" required>
    </div>
    <div class="col-md-6 mb-3">
      <label for="marital_status">Marital Status</label>
      <select id="marital_status" name="marital_status" class="form-control"required>
        <option value="">-------</option>
        <option value="Single">Single</option>
        <option value="Married">Married</option>
      </select>
    </div>

    <!-- Uploads -->
    <div class="col-md-6 mb-3">
      <label for="passport_copy">Upload your passport copy</label>
      <input type="file" id="passport_copy" name="passport_copy" class="form-control" accept="application/pdf,image/*"required>
    </div>
    <div class="col-md-6 mb-3">
      <label for="passport_photo">Upload your passport size picture</label>
      <input type="file" id="passport_photo" name="passport_photo" class="form-control" accept="image/*" required>
    </div>

    <!-- Visa Duration & Price -->
    <div class="col-md-6 mb-3">
    <label for="visaDuration">Visit Visa Duration</label>
    <select id="visaDuration" name="visaDuration" class="form-control" required>
        <option value="">-- Select Duration --</option>
        @foreach($visaData as $visa)
            <option value="{{ $visa->UAEVisaDuration }}" data-price="{{ $visa->UAEVPrice }}">
                {{ $visa->UAEVisaDuration }} Days
            </option>
        @endforeach
    </select>
</div>
<div class="col-md-6 mb-3">
    <label for="price">Price (AED)</label>
    <input type="text" id="price" name="price" class="form-control" readonly placeholder="Select duration to view price">
</div>

    <!-- Submit -->
    <div class="col-12 text-center position-relative" style="display:inline-block;">

  <!-- Processing Snackbar (shown immediately on submit) - Now positioned above button -->
  <div id="processingSnackbar" class="processing-snackbar">Processing your application...</div>

  <!-- Regular Snackbar (shown after response) -->
  <div id="snackbar" class="snackbar"></div>

  <!-- Small Submit Button -->
  <button type="submit" class="submit-btn btn btn-success btn-sm" id="formSubmitBtn" style="min-width:100px;">
    <span class="btn-text">Submit</span>
    <span class="btn-spinner" style="display:none;">
      <span class="spinner-border-golden" role="status" aria-hidden="true"></span>
    </span>
  </button>
</div>

  </div>
  
</form>
<!-- Enhanced Overlay/Modal for Price Display with Black-Golden Theme -->
<div id="priceOverlay" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%;
    background: rgba(0,0,0,0.85); z-index: 1050; justify-content:center; align-items:center;">
  <div style="background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #000000 100%); 
              border: 2px solid #FFD23F; border-radius: 12px; max-width: 450px; padding:30px; text-align:center;
              box-shadow: 0 0 30px rgba(255, 210, 63, 0.4), inset 0 0 20px rgba(255, 210, 63, 0.1);
              color: #FFD23F;">
    <h4 style="color: #FFD23F; text-shadow: 0 0 10px rgba(255, 210, 63, 0.5); margin-bottom: 20px; font-weight: bold;">
      Payment Summary
    </h4>
    <p style="color: #ffffff; margin: 8px 0;">Price: <strong style="color: #FFD23F;"><span id="overlayPrice"></span> AED</strong></p>
    <p style="color: #ffffff; margin: 8px 0;">Tax (5%): <strong style="color: #FFD23F;"><span id="overlayTax"></span> AED</strong></p>
    <hr style="border-color: #FFD23F; opacity: 0.6;">
    <p style="font-size: 18px; color: #ffffff; margin: 15px 0;"><strong style="color: #FFD23F;">Total: <span id="overlayTotal"></span> AED</strong></p>
    
    <button id="payButton" style="padding: 12px 25px; 
            background: linear-gradient(135deg, #FFD23F 0%, #e0b000 100%); 
            border: 2px solid #FFD23F; color: #000000; border-radius: 6px; cursor: pointer; 
            font-weight: bold; font-size: 16px; margin: 10px 5px;
            transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(255, 210, 63, 0.3);">
        Pay Now
    </button>
    <br>
    <button id="closeOverlay" style="margin-top:15px; 
            background: rgba(255, 255, 255, 0.1); border: 1px solid #FFD23F; 
            padding: 8px 20px; cursor: pointer; color: #FFD23F; border-radius: 4px;
            transition: all 0.3s ease; font-size: 14px;">
        Close
    </button>
  </div>
</div>

<form id="ccavenuePaymentForm" method="POST"
      action="{{ config('services.ccavenue.url') }}" style="display:none;">
    <input type="hidden" name="encRequest" id="encRequest" value="">
    <input type="hidden" name="access_code" id="access_code" value="">
    <input type="hidden" name="merchant_id" id="merchant_id" value="">
</form>

  </div>
  </div>
</div>

<div class="aboutus" style="background-color: #000000;">
<div class="container visa-section" style="background-color: #000000; color:#ccc; padding-top:50px; padding-bottom:50px;" >
    
    <h3 class="golden-heading">VISA POLICY</h3>
    <p>Nationals of member nations of the Gulf Cooperation Council have freedom of movement in the UAE. All UAE visitors must hold a passport valid for at least six months. GCC nationals need to show their government-issued ID card.</p>

    <h3 class="golden-heading">VISA ON ARRIVAL</h3>
    <p><strong class="golden-strong">For GCC nationals:</strong> The citizens of GCC countries, i.e. the Gulf Cooperation Council, which includes Bahrain, Oman, Kuwait, Qatar and Saudi Arabia, do not require a visa to visit the UAE. The GCC residents who are not citizens but working as high-level officials such as doctors, managers, engineers or public sector employees and their families can have a 30-day non-renewable visa at all the airports in the UAE upon arrival.</p>

    <p><strong class="golden-strong">Stay up to 180 days:</strong> Nationals of Mexico are eligible to obtain a free UAE visa on arrival and can stay up to 180 days.</p>

    <p><strong class="golden-strong">Stay up to 90 days:</strong> Nationals of the following countries are eligible to obtain a free UAE visa on arrival and can stay up to 90 days: European Union countries (except Ireland), Argentina, Bahamas, Barbados, Belarus, Brazil, Chile, Colombia, Costa Rica, El Salvador, Honduras, Iceland, Israel, Kiribati, Liechtenstein, Maldives, Montenegro, Nauru, Norway, Paraguay, Peru, Russia, Saint Vincent and the Grenadines, San Marino, South Korea, Serbia, Seychelles, Solomon Islands, Switzerland, Uruguay.</p>

    <p><strong class="golden-strong">Stay up to 30 days:</strong> Nationals of the following countries are eligible to obtain a free UAE visa on arrival and can stay up to 30 days: Andorra, Australia, Brunei, Canada, China, Hong Kong, Ireland, Japan, Kazakhstan, Macao, Malaysia, Mauritius, Monaco, New Zealand, Singapore, Ukraine, United Kingdom, United States, Vatican City.</p>


<h3 class="golden-heading">OTHER VISAS</h3>

    <p><strong class="golden-strong">Substitute visas:</strong> Nationals of India who have a valid visa or residents of the United States or are residents of the European Union can obtain a free 14-day visa on arrival.</p>

    <p><strong class="golden-strong">Golden visa:</strong> The Golden visa is issued for investors, entrepreneurs and professional talent.</p>

    <p><strong class="golden-strong">Transit visa:</strong> Passengers on all international airlines may enter the UAE for up to 96 hours after obtaining a transit visa at the airport. Passengers also must have a hotel booking. This does not apply to Afghanistan, Iraq, Niger, Syria, Somalia, and Yemen nationals. Travellers in transit are exempt from entry fees for the first 48 hours; this can be extended for up to 96 hours for an additional fee of 50 AED.</p>

    <p><strong class="golden-strong">Tourist visa:</strong> The Tourist visa is a special category under the Visit visa and entitles the holder to a 30-day stay.</p>

    <p><strong class="golden-strong">Multiple-entry visa:</strong> Multiple-entry visas are issued to cruise ship passengers since their schedule entitles them to enter the country more than once in a single trip. Such visas are also issued to business visitors who are frequent visitors to the UAE due to their ties with a multinational company or a reputable local company. Multiple-entry Visas are valid for six months from the date of issue and the duration of each stay is 30 days. The visa is non-renewable. The individual must enter the UAE on a Visit Visa and then obtain a Multiple-entry Visa.</p>

    <p><strong class="golden-strong">Airline visa:</strong> Visitors usually require a sponsor, but visas can be arranged online through an airline if they travel by Air Arabia, Air Astana, Emirates, Etihad, flydubai, Turkish Airlines and a few more.</p>

    <h3  class="golden-heading">DOCUMENTS REQUIRED</h3>
    <ul>
      <li>Clear passport copy of the sponsor</li>
      <li>Clear passport copy of the sponsored person</li>
      <li>A copy of the salary certificate or employment contract of the sponsor (resident) must be attached</li>
      <li>Proof of family relationship (kinship)</li>
      <li>Travel Insurance</li>
      <li>Copy of confirmed flight booking</li>
      <li>Bank approval letter</li>
      <li>Passport-sized colour photographs</li>
      <li>Visa fee</li>
      <li>Visa application form, duly filled</li>
    </ul>

    <h3  class="golden-heading">RULES AND CONDITIONS</h3>
    <ul>
      <li>Entry into the UAE is subject to immigration approval.</li>
      <li>The ticket is non-refundable if the visa has been issued and utilised.</li>
      <li>Visa processing time is approximately three to four working days.</li>
      <li>Visa fees, once paid, are non-refundable.</li>
    </ul>

  </div>
</div>

<script>
document.getElementById('payButton').addEventListener('click', function () {
    const total = document.getElementById('overlayTotal').textContent;
    const orderId = window.orderId || '';

    if (!orderId) {
        alert('Order ID not set. Please try again.');
        return;
    }

    this.disabled = true;
    this.textContent = 'Processing...';

    fetch("{{ url('/ccavenue/initiate') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ order_id: orderId, amount: total })
    })
    .then(response => response.json())
    .then(data => {
        if (data.encryptedData && data.accessCode) {
            // Fill the hidden form
            document.getElementById('encRequest').value = data.encryptedData;
            document.getElementById('access_code').value = data.accessCode;
            document.getElementById('merchant_id').value = data.merchant_id;
            // Submit form on the main window (redirect)
            document.getElementById('ccavenuePaymentForm').submit();
        } else {
            alert('Payment initiation failed. Please try again.');
            this.disabled = false;
            this.textContent = 'Pay Now';
        }
    })
    .catch(() => {
        alert('Error contacting payment server.');
        this.disabled = false;
        this.textContent = 'Pay Now';
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Phone number validation - numeric only
        document.getElementById('phone').addEventListener('input', function(e) {
            // Allow +, numbers, spaces, hyphens, and parentheses for international formatting
            let value = e.target.value;
            // Remove any characters that are not digits, +, -, (, ), or space
            let cleaned = value.replace(/[^\d\+\-\(\)\s]/g, '');
            // Ensure + only appears at the beginning
            if (cleaned.indexOf('+') > 0) {
                cleaned = cleaned.replace(/\+/g, '');
                if (value.charAt(0) === '+') {
                    cleaned = '+' + cleaned;
                }
            }
            e.target.value = cleaned;
        });

        // Prevent non-numeric key presses (except allowed characters)
        document.getElementById('phone').addEventListener('keypress', function(e) {
            const allowedChars = /[\d\+\-\(\)\s]/;
            const key = String.fromCharCode(e.which);
            if (!allowedChars.test(key) && e.which !== 8 && e.which !== 0) {
                e.preventDefault();
            }
        });

        document.getElementById('visaDuration').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            document.getElementById('price').value = price ? price : '';
        });
    });
</script>

<script>
  function updatePrice() {
    const duration = document.getElementById("visaDuration").value;
    const priceInput = document.getElementById("price");

    if (duration === "30") {
      priceInput.value = "₹3,000"; // Example price for 30 days
    } else if (duration === "60") {
      priceInput.value = "₹5,500"; // Example price for 60 days
    } else {
      priceInput.value = "";
    }
  }

  document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();

    // Show immediate processing snackbar
    showProcessingSnackbar('Processing your application...');

    var submitBtn = document.getElementById('formSubmitBtn');
    var btnText = submitBtn.querySelector('.btn-text');
    var btnSpinner = submitBtn.querySelector('.btn-spinner');
    btnText.style.display = 'none';
    btnSpinner.style.display = 'inline-block';
    submitBtn.disabled = true;

    var formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    }).then(response => {
        if (response.ok) {
            return response.json();
        } else {
            return response.json().then(data => {
                throw new Error(data.message || 'Submission failed');
            });
        }
    }).then(data => {
        // Hide processing snackbar
        hideProcessingSnackbar();
        
        if (data.success) {
            showSnackbar(data.message);

            // Reset form
            this.reset();
            window.orderId = data.orderId;

            // Calculate tax and total
            var price = parseFloat(data.price) || 0;
            var tax = (price * 0.05).toFixed(2);
            var total = (price + parseFloat(tax)).toFixed(2);
            console.log('Received price:', data.price);

            // Fill overlay values
            document.getElementById('overlayPrice').innerText = price.toFixed(2);
            document.getElementById('overlayTax').innerText = tax;
            document.getElementById('overlayTotal').innerText = total;

            // Show overlay after a brief delay
            setTimeout(() => {
                document.getElementById('priceOverlay').style.display = 'flex';
            }, 500);
        } else {
            showSnackbar(data.message || 'Submission failed', true);
        }
    }).catch(error => {
        // Hide processing snackbar
        hideProcessingSnackbar();
        showSnackbar(error.message || 'Submission failed', true);
    }).finally(() => {
        btnText.style.display = 'inline';
        btnSpinner.style.display = 'none';
        submitBtn.disabled = false;
    });
});

document.getElementById('closeOverlay').addEventListener('click', function() {
    document.getElementById('priceOverlay').style.display = 'none';
});

// Processing Snackbar functions
function showProcessingSnackbar(msg) {
    var snackbar = document.getElementById('processingSnackbar');
    snackbar.textContent = msg;
    snackbar.className = "processing-snackbar show";
}

function hideProcessingSnackbar() {
    var snackbar = document.getElementById('processingSnackbar');
    snackbar.className = "processing-snackbar";
}

// Regular Snackbar function
function showSnackbar(msg, isError) {
    var snackbar = document.getElementById('snackbar');
    snackbar.textContent = msg;
    snackbar.style.backgroundColor = isError ? '#dc3545' : '#28a745';
    snackbar.className = "snackbar show";
    setTimeout(function(){
        snackbar.className = "snackbar";
    }, 2200);
}
</script>

<script>
  const countries = [
    "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Argentina", "Armenia", "Australia",
    "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium",
    "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil",
    "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Chad",
    "Chile", "China", "Colombia", "Comoros", "Congo", "Costa Rica", "Croatia", "Cuba", "Cyprus",
    "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt",
    "El Salvador", "Estonia", "Eswatini", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia",
    "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guyana", "Haiti",
    "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy",
    "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kuwait", "Kyrgyzstan", "Laos", "Latvia",
    "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Madagascar",
    "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Mauritania", "Mauritius", "Mexico", "Moldova",
    "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nepal",
    "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "North Macedonia",
    "Norway", "Oman", "Pakistan", "Panama", "Paraguay", "Peru", "Philippines", "Poland", "Portugal",
    "Qatar", "Romania", "Russia", "Rwanda", "Saudi Arabia", "Senegal", "Serbia", "Seychelles",
    "Singapore", "Slovakia", "Slovenia", "Somalia", "South Africa", "South Korea", "Spain", "Sri Lanka",
    "Sudan", "Suriname", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand",
    "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Uganda", "Ukraine",
    "United Arab Emirates", "United Kingdom", "United States", "Uruguay", "Uzbekistan", "Vanuatu",
    "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"
  ];

  function populateSelect(id) {
    const select = document.getElementById(id);
    countries.forEach(country => {
      const option = document.createElement("option");
      option.value = country;
      option.textContent = country;
      select.appendChild(option);
    });
  }

  // Populate both dropdowns
  populateSelect("nationality");
  populateSelect("residence");
</script>

@include('footer')
