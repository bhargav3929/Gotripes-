@include('header')

<style>
  @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

  :root {
    --premium-gold: #FFD700;
    --premium-gold-gradient: linear-gradient(135deg, #FFD700 0%, #D4AF37 50%, #B8960C 100%);
    --dark-bg: #050505;
    --card-bg: rgba(20, 20, 20, 0.6);
    --input-bg: rgba(0, 0, 0, 0.4);
    --input-border: #333;
    --text-muted: #aaaaaa;
  }

  body {
    background-color: var(--dark-bg);
    font-family: 'Outfit', sans-serif;
    color: #fff;
  }

  /* Headings */
  .premium-title {
    font-size: 38px;
    font-weight: 800;
    letter-spacing: 2px;
    margin-bottom: 30px;
    background: var(--premium-gold-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-transform: uppercase;
    display: inline-block;
  }

  .section-title-sm {
    color: var(--premium-gold);
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 20px;
    text-transform: uppercase;
    letter-spacing: 1px;
  }

  p,
  li {
    color: var(--text-muted);
    line-height: 1.7;
    font-size: 15px;
  }

  strong {
    color: #fff;
  }

  .golden-strong {
    color: var(--premium-gold) !important;
    font-weight: 700;
  }

  /* Cards & Layout */
  .content-section {
    padding: 80px 0;
  }

  .glass-card {
    background: var(--card-bg);
    border: 1px solid rgba(255, 215, 0, 0.1);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
  }

  .status-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 20px;
    height: 100%;
    transition: all 0.3s ease;
  }

  .status-card:hover {
    border-color: rgba(255, 215, 0, 0.3);
    background: rgba(255, 255, 255, 0.05);
  }

  .status-card-label {
    font-size: 14px;
    text-transform: uppercase;
    color: var(--premium-gold);
    letter-spacing: 1px;
    margin-bottom: 15px;
    display: block;
    font-weight: 600;
  }

  /* Form Styles */
  .premium-form .form-label {
    font-size: 12px;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
    color: #888;
    margin-bottom: 8px;
  }

  .premium-form .form-control,
  .premium-form .form-select {
    background: var(--input-bg);
    border: 1px solid var(--input-border);
    color: #fff;
    height: 48px;
    border-radius: 8px;
    padding: 0 15px;
    font-size: 14px;
    transition: all 0.3s ease;
  }

  .premium-form .form-control:focus {
    border-color: var(--premium-gold);
    box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.1);
    background: #000;
  }

  /* Custom File Input */
  .premium-form input[type="file"] {
    padding-top: 10px;
    font-size: 13px;
  }

  /* Radios */
  .form-check-input {
    background-color: transparent;
    border-color: #555;
  }

  .form-check-input:checked {
    background-color: var(--premium-gold);
    border-color: var(--premium-gold);
  }

  .form-check-label {
    color: #ddd;
    font-size: 14px;
  }

  /* Submit Button */
  .btn-gold {
    background: var(--premium-gold-gradient);
    border: none;
    color: #000;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 2px;
    padding: 16px 40px;
    border-radius: 50px;
    min-width: 250px;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(255, 215, 0, 0.15);
  }

  .btn-gold:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 40px rgba(255, 215, 0, 0.25);
  }

  .btn-gold:disabled {
    opacity: 0.7;
    cursor: not-allowed;
  }

  /* Roadmap Steps */
  .step-number {
    font-size: 40px;
    font-weight: 800;
    color: rgba(255, 255, 255, 0.05);
    position: absolute;
    top: -15px;
    left: 20px;
    z-index: 0;
  }

  .info-card {
    background: #111;
    border: 1px solid #222;
    border-radius: 16px;
    padding: 30px;
    position: relative;
    overflow: hidden;
    height: 100%;
  }

  .info-card-content {
    position: relative;
    z-index: 1;
  }

  /* Snackbar */
  #snackbar {
    visibility: hidden;
    min-width: 280px;
    background: #333;
    color: #fff;
    text-align: center;
    border-radius: 8px;
    padding: 12px 20px;
    position: fixed;
    z-index: 1050;
    left: 50%;
    bottom: 30px;
    transform: translateX(-50%);
    font-size: 14px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
  }

  @media (max-width: 768px) {
    .premium-title {
      font-size: 28px;
    }

    .content-section {
      padding: 40px 0;
    }

    .glass-card {
      padding: 25px;
    }
  }
</style>

<!-- Intro Section -->
<section class="content-section pb-4">
  <div class="container text-center">
    <h1 class="premium-title">Working in the UAE</h1>
    <p class="mx-auto text-muted" style="max-width: 800px;">
      Finding a job in the UAE requires strategy, persistence, and the right approach. Whether you are on a visit visa
      or looking to switch careers, follow our roadmap to success.
    </p>
  </div>
</section>

<!-- Stats/Timeline Section -->
<section class="container pb-5">
  <div class="row g-4 justify-content-center">
    <div class="col-lg-10">
      <div class="p-4"
        style="background: linear-gradient(to right, #111, #0a0a0a); border-radius: 16px; border: 1px solid #222;">
        <h3 class="section-title-sm text-center mb-4"><i class="bi bi-clock-history me-2"></i> Typical Job Hunt Timeline
          (60-Day Visit Visa)</h3>
        <div class="row text-center g-4">
          <div class="col-md-4">
            <div class="p-3">
              <h5 style="color: #fff;">Weeks 1-2</h5>
              <p class="small mb-0">Finalize CV, apply online, attend walk-ins</p>
            </div>
          </div>
          <div class="col-md-4 border-start border-end border-secondary">
            <div class="p-3">
              <h5 style="color: #fff;">Weeks 3-4</h5>
              <p class="small mb-0">Expand search, network, follow up leads</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3">
              <h5 style="color: #fff;">Weeks 5-6</h5>
              <p class="small mb-0">Interviews, offers, visa processing start</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Application Form -->
<section class="content-section"
  style="background: url('{{ asset('assets/index_files/I1.jpeg') }}') center center/cover fixed;">
  <div style="background: rgba(0,0,0,0.85); padding: 80px 0;">
    <div class="container-fluid px-4"> <!-- Wider container for form -->
      <div class="row justify-content-center">
        <div class="col-xl-11 col-xxl-10">
          <div class="glass-card">
            <div class="text-center mb-5">
              <h2 class="premium-title" style="font-size: 32px;">Submit Your Application</h2>
              <p>Join our database of professionals. Fill out the form below accurately.</p>
            </div>

            <form id="jobApplicationForm" method="POST" action="{{ route('job.application.submit') }}"
              enctype="multipart/form-data" class="premium-form">
              @csrf

              <!-- Status Checks -->
              <div class="row mb-5 justify-content-center">
                <div class="col-md-5 mb-3 mb-md-0">
                  <div class="status-card text-center">
                    <label class="status-card-label"><i class="bi bi-briefcase me-2"></i> Current Status</label>
                    <div class="d-flex justify-content-center gap-4">
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="job_status" id="hired" value="hired"
                          required>
                        <label class="form-check-label" for="hired">Already Hired</label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="job_status" id="available" value="available"
                          required>
                        <label class="form-check-label" for="available">Available</label>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-5">
                  <div class="status-card text-center">
                    <label class="status-card-label"><i class="bi bi-geo-alt me-2"></i> Current Location</label>
                    <div class="d-flex justify-content-center gap-4">
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="location_status" id="inside_uae"
                          value="inside_uae" required>
                        <label class="form-check-label" for="inside_uae">Inside UAE</label>
                      </div>
                      <div class="form-check">
                        <input class="form-check-input" type="radio" name="location_status" id="outside_uae"
                          value="outside_uae" required>
                        <label class="form-check-label" for="outside_uae">Outside UAE</label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Input Grid -->
              <h5 class="text-white mb-4 border-bottom border-secondary pb-2">Personal & Professional Details</h5>

              <div class="row g-3 g-xl-4"> <!-- Dense grid -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="John Doe" required>
                  </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <label for="age" class="form-label">Age</label>
                  <input type="number" id="age" name="age" class="form-control" min="18" max="65" placeholder="e.g. 28"
                    required>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <label for="nationality" class="form-label">Nationality</label>
                  <input type="text" id="nationality" name="nationality" class="form-control" placeholder="e.g. Indian"
                    required>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <label for="mobile" class="form-label">Mobile Number</label>
                  <input type="tel" id="mobile" name="mobile" class="form-control" placeholder="+971..."
                    pattern="[0-9]{10,15}" required>
                </div>

                <div class="col-lg-3 col-md-4 col-sm-6">
                  <label for="email" class="form-label">Email Address</label>
                  <input type="email" id="email" name="email" class="form-control" placeholder="name@example.com"
                    required>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <label for="profession" class="form-label">Profession</label>
                  <input type="text" id="profession" name="profession" class="form-control"
                    placeholder="e.g. Accountant" required>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <label for="experience" class="form-label">Years of Experience</label>
                  <input type="number" id="experience" name="experience" class="form-control" min="0"
                    placeholder="e.g. 5" required>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <label for="visa_status" class="form-label">Visa Status</label>
                  <input type="text" id="visa_status" name="visa_status" class="form-control"
                    placeholder="e.g. Visit Visa" required>
                </div>

                <div class="col-lg-3 col-md-4 col-sm-6">
                  <label for="expected_salary" class="form-label">Expected Salary (AED)</label>
                  <input type="text" id="expected_salary" name="expected_salary" class="form-control"
                    placeholder="e.g. 5000" required>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <label for="last_company" class="form-label">Last Company</label>
                  <input type="text" id="last_company" name="last_company" class="form-control"
                    placeholder="Previous Employer" required>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <label for="last_location" class="form-label">Last Location</label>
                  <input type="text" id="last_location" name="last_location" class="form-control"
                    placeholder="e.g. Dubai" required>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <label for="preferred_location" class="form-label">Preferred Location</label>
                  <input type="text" id="preferred_location" name="preferred_location" class="form-control"
                    placeholder="e.g. Abu Dhabi" required>
                </div>

                <div class="col-lg-3 col-md-4 col-sm-6">
                  <label for="notice_period" class="form-label">Notice Period</label>
                  <input type="text" id="notice_period" name="notice_period" class="form-control"
                    placeholder="e.g. Immediate" required>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <label for="reference_name" class="form-label">Reference Name</label>
                  <input type="text" id="reference_name" name="reference_name" class="form-control"
                    placeholder="Optional">
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <label for="reference_position" class="form-label">Reference Position</label>
                  <input type="text" id="reference_position" name="reference_position" class="form-control"
                    placeholder="Optional">
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                  <label for="reference_mobile" class="form-label">Reference Mobile</label>
                  <input type="tel" id="reference_mobile" name="reference_mobile" class="form-control"
                    placeholder="Optional">
                </div>
              </div>

              <!-- Documents -->
              <h5 class="text-white mb-4 mt-5 border-bottom border-secondary pb-2">Documents (PDF/Image)</h5>
              <div class="row g-4">
                <div class="col-md-6">
                  <label for="resume" class="form-label">Upload Resume (CV)</label>
                  <input type="file" id="resume" name="resume" class="form-control"
                    accept="application/pdf,image/*,.doc,.docx" required>
                </div>
                <div class="col-md-6">
                  <label for="passport" class="form-label">Passport Type Photo</label>
                  <input type="file" id="passport" name="passport" class="form-control" accept="image/*,application/pdf"
                    required>
                </div>
              </div>

              <!-- Submit -->
              <div class="text-center mt-5">
                <button type="submit" id="submitBtn" class="btn-gold">
                  <span class="btn-text">SUBMIT APPLICATION</span>
                </button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Detailed Roadmap Info -->
<section class="content-section">
  <div class="container">
    <h2 class="premium-title text-center mb-5 d-block">Your Roadmap to Employment</h2>

    <div class="row g-4">
      <div class="col-md-6 col-lg-4">
        <div class="info-card">
          <span class="step-number">01</span>
          <div class="info-card-content">
            <h4 class="section-title-sm">Optimized CV</h4>
            <p>Essential: A UAE-style CV (2 pages max), clear Passport/Visa copies, detailed certificates, professional
              photo, and a customized cover letter.</p>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="info-card">
          <span class="step-number">02</span>
          <div class="info-card-content">
            <h4 class="section-title-sm">Daily Applications</h4>
            <p>Apply early morning on top sites: LinkedIn, Indeed.ae, Dubizzle, NaukriGulf, and GulfTalent. Consistency
              is key.</p>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="info-card">
          <span class="step-number">03</span>
          <div class="info-card-content">
            <h4 class="section-title-sm">Walk-In Interviews</h4>
            <p>The fastest route for many roles. Check Facebook groups and Indeed for daily walk-in schedules in Dubai
              and Sharjah.</p>
          </div>
        </div>
      </div>

      <div class="col-md-6 col-lg-4">
        <div class="info-card">
          <span class="step-number">04</span>
          <div class="info-card-content">
            <h4 class="section-title-sm">Networking</h4>
            <p>Update your LinkedIn headline to "Available in UAE". Connect with recruiters and send polite,
              professional messages.</p>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="info-card">
          <span class="step-number">05</span>
          <div class="info-card-content">
            <h4 class="section-title-sm">The Offer</h4>
            <p>Once hired, the company handles your visa conversion. You usually do not need to exit the country.</p>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="info-card" style="border-color: #dc3545;">
          <span class="step-number" style="color: rgba(220, 53, 69, 0.1);">⚠</span>
          <div class="info-card-content">
            <h4 class="section-title-sm" style="color: #dc3545;">Avoid Scams</h4>
            <p class="text-white">Never pay for a job offer, interview, or "processing fee". Legitimate employers cover
              all visa costs.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Snackbar Container -->
<div id="snackbar"></div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Check for valid phone numbers on input
    const phoneInput = document.getElementById('mobile');
    phoneInput.addEventListener('input', function (e) {
      this.value = this.value.replace(/[^0-9+]/g, '');
    });

    document.getElementById('jobApplicationForm').addEventListener('submit', function (e) {
      e.preventDefault();

      const btn = document.getElementById('submitBtn');
      const originalText = btn.innerHTML;

      // Validation for radio buttons
      if (!document.querySelector('input[name="job_status"]:checked')) {
        showSnackbar('Please select Job Status', 'warning'); return;
      }
      if (!document.querySelector('input[name="location_status"]:checked')) {
        showSnackbar('Please select Location Status', 'warning'); return;
      }

      // Loading State
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> SUBMITTING...';

      const formData = new FormData(this);

      fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(async res => {
          if (!res.ok) throw new Error(await res.text());
          return res.text(); // or res.json() depending on backend
        })
        .then(data => {
          showSnackbar('Application Submitted Successfully!', 'success');
          this.reset();
          window.scrollTo({ top: 0, behavior: 'smooth' });
        })
        .catch(err => {
          console.error(err);
          showSnackbar('Submission Failed. Please try again.', 'error');
        })
        .finally(() => {
          btn.disabled = false;
          btn.innerHTML = originalText;
        });
    });

    // File validation
    function validateFile(input, maxMB) {
      if (input.files[0] && input.files[0].size > maxMB * 1024 * 1024) {
        showSnackbar(`File too large. Max allowed is ${maxMB}MB`, 'error');
        input.value = '';
      }
    }
    document.getElementById('resume').addEventListener('change', function () { validateFile(this, 5); });
    document.getElementById('passport').addEventListener('change', function () { validateFile(this, 2); });
  });

  function showSnackbar(msg, type) {
    const el = document.getElementById('snackbar');
    el.innerText = msg;

    if (type === 'error') el.style.background = 'linear-gradient(135deg, #dc3545, #b02a37)';
    else if (type === 'warning') {
      el.style.background = '#ffc107';
      el.style.color = '#000';
    }
    else el.style.background = 'linear-gradient(135deg, #198754, #146c43)'; // success

    el.style.visibility = 'visible';
    setTimeout(() => { el.style.visibility = 'hidden'; }, 3000);
  }
</script>

@include('footer')