@include('header')

<style>
  .image-overlay {
    position: relative;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  .image-overlay img {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    object-fit: cover;
    z-index: -1;
  }
  .overlay {
    position: relative;
    width: 100%;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: stretch;
  }
  .form-container {
    background: rgba(0, 0, 0, 0.78);
    color: white;
    max-width: 1400px;
    width: 100%;
    border-radius: 8px;
    margin: 0 auto;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.13);
    padding: 32px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  .form-container h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #FFD23F;
  }
  .form-container p {
    text-align: center;
    margin-bottom: 26px;
  }
  input[type="text"],
  input[type="number"],
  input[type="file"],
  input[type="date"],
  input[type="email"],
  input[type="tel"],
  select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    margin-bottom: 7px;
    background: #15181A;
    color: #fff;
    resize: none;
    overflow: hidden;
  }
  .form-check-input {
    accent-color: #FFD23F;
    margin-right: 8px;
  }
  .form-check-label {
    color: #fff;
    cursor: pointer;
  }
  .submit-btn {
    background-color: #FFD23F;
    color: black;
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    font-size: 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-width: 150px;
  }
  .submit-btn:disabled {
    opacity: 0.8;
    cursor: not-allowed;
  }
  .spinner-border {
    width: 18px;
    height: 18px;
    border-width: 2px;
  }
  .golden-heading {
    background-color: rgb(255, 210, 63);
    color: white !important;
    padding: 10px 20px;
    border-radius: 0;
    margin-top: 40px;
    margin-bottom: 20px !important;
    display: block;
    width: 100%;
  }
  #snackbar {
    visibility: hidden;
    min-width: 250px;
    background-color: #4ed017;
    color: #020202;
    text-align: center;
    border-radius: 4px;
    padding: 16px;
    position: fixed;
    z-index: 1000;
    left: 50%;
    bottom: 30px;
    transform: translateX(-50%);
    font-size: 16px;
  }

  /* Status Section Styling */
  .status-section {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
  }
  
  .status-label {
    color: #FFD23F;
    /* font-weight: bold; */
    margin-bottom: 15px;
    display: block;
    font-size: 16px;
  }

  /* Responsive Design */
  @media (max-width: 1200px) {
    .form-container {
      max-width: 95%;
      padding: 25px 30px;
    }
  }

  @media (max-width: 991px) {
    .col-md-3 {
      flex: 0 0 50%;
      max-width: 50%;
    }
    .col-md-6 {
      flex: 0 0 100%;
      max-width: 100%;
      margin-bottom: 15px;
    }
  }

  @media (max-width: 768px) {
    .form-container {
      max-width: 100%;
      padding: 20px 15px;
      margin: 0 10px;
    }
    
    .col-md-3 {
      flex: 0 0 100%;
      max-width: 100%;
      margin-bottom: 15px;
    }
    
    .col-md-6 {
      flex: 0 0 100%;
      max-width: 100%;
      margin-bottom: 15px;
    }
    
    .status-section {
      padding: 15px;
    }
    
    .d-flex.gap-4 {
      flex-direction: column;
      gap: 10px !important;
    }
    
    .form-check {
      margin-bottom: 10px;
    }
  }

  @media (max-width: 576px) {
    .form-container { 
      padding: 15px 10px;
      margin: 0 5px;
    }
    .golden-heading { 
      padding: 7px 7px; 
      font-size: 18px;
    }
    .status-label {
      font-size: 14px;
    }
    .submit-btn {
      width: 100%;
      padding: 15px 20px;
    }
  }
</style>

<div class="aboutus" style="background-color: #000000;">
  <div class="container" style="background-color: #000000; color:#ccc; padding-top:50px; padding-bottom:50px;">
    <h2 class="golden-heading">Getting a Job in the UAE on a Visit Visa</h2>
    <p>Getting a job in the UAE on a visit visa is possible — thousands do it every month — but it requires speed, strategy, and persistence. Here's your complete roadmap:</p>
    <h3 class="golden-heading">🎯 Job Hunting Timeline Example (60-Day Visit Visa)</h3>
    <ul>
      <li><strong>Week 1–2:</strong> Finalize CV, apply online, attend walk-ins</li>
      <li><strong>Week 3–4:</strong> Expand search, follow up on leads, network</li>
      <li><strong>Week 5–6:</strong> Attend interviews, negotiate offer, get visa started</li>
    </ul>
  </div>
</div>

<div class="image-overlay">
  <img src="{{ asset('assets/index_files/I1.jpeg') }}" alt="Banner" />
  <div class="overlay">
    <div class="form-container">
      <h2>Job Application</h2>
      <p>Send your resume</p>
      <form id="jobApplicationForm" method="POST" action="{{ route('job.application.submit') }}" enctype="multipart/form-data">
        @csrf

        <!-- Status Sections Row with reduced spacing -->
        <div class="row mb-2">
          <!-- Job Status Section -->
          <div class="col-md-6 col-sm-12 mb-2">
            <div class="status-section">
              <label class="status-label form-label" style="font-size: 1.45rem;">💼 Job Status</label>
              <div class="d-flex gap-4 flex-wrap">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="job_status" id="hired" value="hired" required>
                  <label class="form-check-label" for="hired">Hired</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="job_status" id="available" value="available" required>
                  <label class="form-check-label" for="available">Available</label>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Current Location Status Section -->
          <div class="col-md-6 col-sm-12 mb-2">
            <div class="status-section">
              
<label class="status-label form-label" style="font-size: 1.45rem;">📍 Current Location Status</label>

              <div class="d-flex gap-4 flex-wrap">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="location_status" id="inside_uae" value="inside_uae" required>
                  <label class="form-check-label" for="inside_uae">Inside UAE</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="location_status" id="outside_uae" value="outside_uae" required>
                  <label class="form-check-label" for="outside_uae">Outside UAE</label>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Fields -->
        <div class="row g-3">
          <div class="col-md-3 col-sm-6 col-12">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" name="name" placeholder="Enter your full name" required />
          </div>

          <div class="col-md-3 col-sm-6 col-12">
            <label for="age" class="form-label">Age</label>
            <input type="number" id="age" name="age" min="18" max="65" placeholder="Enter your age" required />
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <label for="nationality" class="form-label">Nationality</label>
            <input type="text" id="nationality" name="nationality" placeholder="Enter your nationality" required />
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <label for="mobile" class="form-label">Mobile Number</label>
            <input type="tel" id="mobile" name="mobile" placeholder="Enter mobile number" pattern="[0-9]{10,15}" title="Enter only numbers (10-15 digits)" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required />
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required />
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <label for="profession" class="form-label">Profession</label>
            <input type="text" id="profession" name="profession" placeholder="Enter your profession" required />
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <label for="experience" class="form-label">Years of Experience</label>
            <input type="number" id="experience" name="experience" min="0" max="50" placeholder="Years of exp." required />
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <label for="visa_status" class="form-label">Visa Status</label>
            <input type="text" id="visa_status" name="visa_status" placeholder="Current visa status" required />
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <label for="expected_salary" class="form-label">Expected Salary</label>
            <input type="text" id="expected_salary" name="expected_salary" placeholder="Expected salary" required />
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <label for="last_company" class="form-label">Last Worked Company</label>
            <input type="text" id="last_company" name="last_company" placeholder="Company name" required />
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <label for="last_location" class="form-label">Company Location</label>
            <input type="text" id="last_location" name="last_location" placeholder="Company location" required />
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <label for="preferred_location" class="form-label">Preferred Location For Job</label>
            <input type="text" id="preferred_location" name="preferred_location" placeholder="Preferred job location" required />
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <label for="notice_period" class="form-label">Notice Period</label>
            <input type="text" id="notice_period" name="notice_period" placeholder="Notice period" required />
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <label for="reference_name" class="form-label">Reference Name</label>
            <input type="text" id="reference_name" name="reference_name" placeholder="Reference name" required />
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <label for="reference_position" class="form-label">Reference Position</label>
            <input type="text" id="reference_position" name="reference_position" placeholder="Reference position" required />
          </div>
          <div class="col-md-3 col-sm-6 col-12">
            <label for="reference_mobile" class="form-label">Reference Mobile</label>
            <input type="tel" id="reference_mobile" name="reference_mobile" placeholder="Reference mobile" pattern="[0-9]{10,15}" title="Enter only numbers" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required />
          </div>
        
          <!-- File Upload Fields -->
          <div class="col-md-6 col-12">
            <label for="resume" class="form-label">Upload Your Resume</label>
            <input type="file" id="resume" name="resume" accept="application/pdf,image/*,.doc,.docx" required />
            <small class="text-white d-block mt-1">Accepted formats: PDF, DOC, DOCX, Images (Max: 5MB)</small>
          </div>
          <div class="col-md-6 col-12">
            <label for="passport" class="form-label">Upload Your Passport Size Picture</label>
            <input type="file" id="passport" name="passport" accept="image/*,application/pdf" required />
            
<small class="text-white d-block mt-1">Accepted formats: Images, PDF (Max: 2MB)</small>

          </div>
          
          <!-- Submit Button -->
          <div class="col-12 text-center">
            <button type="submit" id="submitBtn" class="submit-btn mt-3">
              <span class="btn-text">Submit Application</span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="aboutus" style="background-color: #000000;">
  <div class="container" style="background-color: #000000; color:#ccc; padding-top:50px; padding-bottom:50px;">
    <h3 class="golden-heading">1. Understand the Visit Visa Rule</h3>
    <p>You're allowed to job hunt while on a visit or tourist visa. You cannot legally work until your visa is converted to a work (residency) visa by your employer. Typical visit visas last 30, 60, or 90 days (extendable once or twice).</p>

    <h3 class="golden-heading">2. Prepare a UAE-Optimized CV & Documents</h3>
    <p>Essential documents:</p>
    <ul>
      <li>UAE-style CV (2 pages max, no fancy design)</li>
      <li>Passport copy + Visit Visa copy</li>
      <li>Certificates (education, experience)</li>
      <li>Passport photo (white background)</li>
      <li>Cover letter (customized is best)</li>
    </ul>

    <h3 class="golden-heading">3. Apply for Jobs Daily – Online and Offline</h3>
    <p><strong>🔍 Top Job Sites in UAE</strong></p>
    <ul>
      <li>LinkedIn – White-collar, office, tech, HR, sales</li>
      <li>Indeed.ae – All sectors, including blue collar</li>
      <li>Dubizzle.com/jobs – Retail, cleaning, delivery, admin</li>
      <li>NaukriGulf.com – Indian expat jobs</li>
      <li>GulfTalent.com – Mid to high-end jobs</li>
    </ul>
    <p>⏰ Apply early each morning (recruiters often shortlist fresh CVs then).</p>

    <h3 class="golden-heading">4. Attend Walk-In Interviews (Fastest Hiring Route)</h3>
    <p>Daily walk-ins happen in Dubai, Sharjah, Abu Dhabi. Jobs: cleaning, hotel staff, sales, reception, security, warehouse, admin, travel agent.</p>
    <p><strong>📍 Where to find them:</strong></p>
    <ul>
      <li>Indeed UAE – Walk-in jobs</li>
      <li>Facebook groups: Jobs in Dubai – Urgent Hiring, Dubai Walk-in Interviews, Job Seekers UAE 2025</li>
    </ul>
    <p>👜 Carry: Printed CVs, passport & visa copy, photos, education/experience proof.</p>

    <h3 class="golden-heading">5. Join Job WhatsApp & Telegram Groups</h3>
    <p>Search on Telegram or Google:</p>
    <ul>
      <li>UAE Job Seekers Group</li>
      <li>Dubai Jobs Hiring Now</li>
      <li>Walk-in Interview UAE</li>
    </ul>
    <p>⚠ Avoid any group asking for money or deposits.</p>

    <h3 class="golden-heading">6. Use LinkedIn to Network & DM Recruiters</h3>
    <p>Update headline: "On Visit Visa | Seeking [Your Job Title] | Available in UAE for Immediate Joining". Follow & connect with recruiters, send polite messages with your CV.</p>

    <h3 class="golden-heading">7. Once You Get a Job Offer</h3>
    <p>The company must cancel your visit visa and apply for a work permit (employment visa). You'll then do a medical test, Emirates ID biometrics, and sign a labor contract.</p>
    <p>📌 You usually don't need to exit UAE; visa change can be done from within.</p>

    <h3 class="golden-heading">🛑 Avoid Scams</h3>
    <p>Never pay for jobs, offer letters, or fake HR interviews. A real employer handles your visa & never charges you.</p>
  </div>
</div>

<!-- Snackbar -->
<div id="snackbar"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission handling
    document.getElementById('jobApplicationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const btn = document.getElementById('submitBtn');
        const btnText = btn.querySelector('.btn-text');
        
        // Validate required radio buttons
        const jobStatusChecked = document.querySelector('input[name="job_status"]:checked');
        const locationStatusChecked = document.querySelector('input[name="location_status"]:checked');
        
        if (!jobStatusChecked) {
            showSnackbar('Please select your job status', 'error');
            return;
        }
        
        if (!locationStatusChecked) {
            showSnackbar('Please select your current location status', 'error');
            return;
        }
        
        // Show loading state
        btn.disabled = true;
        btnText.innerHTML = `<span class="spinner-border" role="status"></span> Sending Application...`;

        // Prepare form data
        const formData = new FormData(form);
        
        // Log form data for debugging (remove in production)
        console.log('Form Data:');
        for (let [key, value] of formData.entries()) {
            console.log(key, value);
        }

        // Submit form
        fetch(form.action, { 
            method: 'POST', 
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(async res => {
            if (!res.ok) {
                const errorText = await res.text();
                throw new Error(`Server error: ${res.status}`);
            }
            return res.text();
        })
        .then(data => {
            showSnackbar('Application submitted successfully! We will contact you soon.', 'success');
            form.reset();
            
            // Scroll to top after successful submission
            window.scrollTo({ top: 0, behavior: 'smooth' });
        })
        .catch(error => {
            console.error('Submission error:', error);
            showSnackbar('Failed to submit application. Please try again.', 'error');
        })
        .finally(() => {
            // Reset button state
            btn.disabled = false;
            btnText.textContent = 'Submit Application';
        });
    });

    // File input validation
    document.getElementById('resume').addEventListener('change', function() {
        validateFileSize(this, 5); // 5MB max for resume
    });
    
    document.getElementById('passport').addEventListener('change', function() {
        validateFileSize(this, 2); // 2MB max for passport photo
    });
});

// File size validation function
function validateFileSize(input, maxSizeMB) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const maxSize = maxSizeMB * 1024 * 1024; // Convert MB to bytes
        
        if (file.size > maxSize) {
            showSnackbar(`File size must be less than ${maxSizeMB}MB`, 'error');
            input.value = ''; // Clear the input
            return false;
        }
    }
    return true;
}

// Enhanced snackbar function
function showSnackbar(msg, type = 'success') {
    const snackbar = document.getElementById("snackbar");
    snackbar.textContent = msg;
    
    // Set background color based on type
    if (type === 'error') {
        snackbar.style.backgroundColor = '#dc3545';
        snackbar.style.color = '#ffffff';
    } else if (type === 'warning') {
        snackbar.style.backgroundColor = '#ffc107';
        snackbar.style.color = '#000000';
    } else {
        snackbar.style.backgroundColor = '#4ed017';
        snackbar.style.color = '#020202';
    }
    
    snackbar.style.visibility = "visible";
    
    setTimeout(() => { 
        snackbar.style.visibility = "hidden"; 
    }, 4000);
}
</script>

@include('footer')
