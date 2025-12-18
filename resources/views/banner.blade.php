<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Registration</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
/* 🎯 UNIQUE SCOPED STYLES - Prefixed with .partner-registration-page */
.partner-registration-page .custom-banner {
  padding-top: 80px; /* Added proper header spacing */
}

.partner-registration-page .custom-banner .image-overlay {
  position: relative;
  overflow: hidden;
  min-height: calc(100vh - 80px); /* Subtract header padding */
  height: auto;
  background: url("{{ asset('assets/index_files/s1.jpg') }}") no-repeat center center;
  background-size: cover;
  display: flex;
  flex-direction: column;
}

.partner-registration-page .custom-banner .image-overlay img {
  object-fit: cover;
  width: 100%;
  height: 100%;
  display: block;
}

.partner-registration-page .custom-banner .overlay {
  position: relative;
  top: 0; left: 0;
  width: 100%;
  min-height: calc(100vh - 80px);
  background: rgba(0,0,0,0.5);
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  padding: 2rem 1rem 4rem;
  height: auto;
  min-height: inherit;
}

/* Mobile adjustments */
@media (max-width: 767.98px) {
  .partner-registration-page .custom-banner {
    padding-top: 60px; /* Smaller header padding for mobile */
  }
  
  .partner-registration-page .custom-banner .image-overlay {
    min-height: calc(100vh - 60px);
    height: auto;
    background-position: top center;
    background-attachment: scroll;
  }
  
  .partner-registration-page .custom-banner .overlay {
    min-height: calc(100vh - 60px);
    position: relative;
    height: auto;
  }
}

/* Desktop adjustments */
@media (min-width: 768px) {
  .partner-registration-page .custom-banner {
    padding-top: 90px; /* Larger header padding for desktop */
  }
  
  .partner-registration-page .custom-banner .image-overlay {
    min-height: calc(100vh - 90px);
  }
  
  .partner-registration-page .custom-banner .overlay {
    min-height: calc(100vh - 90px);
    justify-content: center;
    height: auto;
    padding-bottom: 2rem;
  }
  
  .partner-registration-page .custom-banner .image-overlay {
    background-position: top center;
    background-attachment: fixed;
  }
  
  .partner-registration-page .custom-banner .hero-line {
    margin-top: 2rem;
  }
  
  .partner-registration-page .custom-banner .cta-button {
    margin-top: 3rem;
  }
}

.partner-registration-page .custom-banner .overlay-content {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
  margin-bottom: 2rem;
  text-align: center;
  visibility: hidden;
  opacity: 0;
  transition: opacity 0.6s ease;
}

.partner-registration-page .custom-banner .overlay-content.visible {
  visibility: visible;
  opacity: 1;
}

.partner-registration-page .custom-banner .hero-line {
  font-size: 2rem;
  font-weight: 600;
  margin: 0;
}

.partner-registration-page .custom-banner .tagline-text {
  color: #FFD23F;
  font-family: 'Satisfy', cursive;
}

.partner-registration-page .custom-banner .heading-text {
  color: #fff;
}

.partner-registration-page .custom-banner .cta-button {
  color: white;
  font-size: 1.2rem;
  font-weight: 600;
  border-radius: 10px;
  background: rgba(200, 48, 0, 0.7);
  padding: 0.5rem 1.5rem;
  text-align: center;
  border: none;
  white-space: nowrap;
  transition: background 0.2s;
  cursor: pointer;
}

.partner-registration-page .custom-banner .cta-button:hover {
  background: #cc5500;
}

.partner-registration-page .custom-banner #carousel-wrapper {
  visibility: hidden;
  opacity: 0;
  transition: opacity 0.5s ease;
}

.partner-registration-page .custom-banner #carousel-wrapper.visible {
  visibility: visible;
  opacity: 1;
}

.partner-registration-page .custom-banner .owl-carousel .item img {
  border-radius: 15px;
  width: 100%;
  height: auto;
  object-fit: cover;
}

/* 🎯 UNIQUE MODAL STYLES - Prefixed to avoid conflicts */
.partner-registration-modal {
  display: none;
  position: fixed;
  z-index: 10000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.8);
  backdrop-filter: blur(5px);
  animation: partnerModalFadeIn 0.3s ease-in-out;
}

.partner-registration-modal .partner-modal-content {
  background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
  margin: 3% auto;
  padding: 0;
  border: 2px solid #d4af37;
  border-radius: 15px;
  width: 90%;
  max-width: 600px;
  box-shadow: 0 20px 60px rgba(212, 175, 55, 0.3);
  animation: partnerModalSlideIn 0.3s ease-out;
  color: #ffffff;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
}

.partner-registration-modal .partner-modal-header {
  background: linear-gradient(90deg, #d4af37 0%, #f4d03f 100%);
  padding: 20px 30px;
  border-radius: 13px 13px 0 0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.partner-registration-modal .partner-modal-header h2 {
  margin: 0;
  color: #000000;
  font-size: 24px;
  font-weight: 600;
}

.partner-registration-modal .partner-modal-close {
  color: #000000;
  font-size: 28px;
  font-weight: bold;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 30px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.partner-registration-modal .partner-modal-close:hover,
.partner-registration-modal .partner-modal-close:focus {
  background-color: rgba(0, 0, 0, 0.1);
  transform: scale(1.1);
}

.partner-registration-modal .partner-modal-body {
  padding: 30px;
}

.partner-registration-modal .partner-form-group {
  margin-bottom: 20px;
}

.partner-registration-modal .partner-form-group label {
  display: block;
  margin-bottom: 8px;
  color: #d4af37;
  font-weight: 500;
  font-size: 14px;
}

.partner-registration-modal .partner-form-group input {
  width: 100%;
  padding: 12px 15px;
  background-color: rgba(255, 255, 255, 0.1);
  border: 1px solid #444444;
  border-radius: 8px;
  color: #ffffff;
  font-size: 16px;
  transition: all 0.3s ease;
  box-sizing: border-box;
}

.partner-registration-modal .partner-form-group input:focus {
  outline: none;
  border-color: #d4af37;
  background-color: rgba(255, 255, 255, 0.15);
  box-shadow: 0 0 10px rgba(212, 175, 55, 0.3);
}

.partner-registration-modal .partner-form-group input::placeholder {
  color: #888888;
}

.partner-registration-modal .partner-error-msg {
  display: block;
  color: #ff6b6b;
  font-size: 12px;
  margin-top: 5px;
  min-height: 16px;
}

.partner-registration-modal .partner-form-actions {
  display: flex;
  gap: 15px;
  margin-top: 30px;
}

.partner-registration-modal .partner-submit-btn {
  flex: 1;
  background: linear-gradient(90deg, #d4af37 0%, #f4d03f 100%);
  color: #000000;
  border: none;
  padding: 12px 20px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.partner-registration-modal .partner-submit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
}

.partner-registration-modal .partner-submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

.partner-registration-modal .partner-cancel-btn {
  flex: 1;
  background: transparent;
  color: #ffffff;
  border: 1px solid #666666;
  padding: 12px 20px;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.partner-registration-modal .partner-cancel-btn:hover {
  background-color: rgba(255, 255, 255, 0.1);
  border-color: #d4af37;
}

/* 🎯 UNIQUE Emirates checkbox styling */
.partner-registration-modal .partner-emirates-checkbox-container {
  max-height: 200px;
  overflow-y: auto;
  border: 1px solid #444444;
  border-radius: 8px;
  padding: 15px;
  background-color: rgba(255, 255, 255, 0.05);
  min-height: 50px;
}

.partner-registration-modal .partner-emirates-loading {
  text-align: center;
  color: #d4af37;
  padding: 20px;
  font-style: italic;
}

.partner-registration-modal .partner-emirate-checkbox-item {
  display: block;
  margin-bottom: 12px;
  padding: 10px;
  border-radius: 6px;
  transition: background-color 0.3s ease;
  position: relative;
}

.partner-registration-modal .partner-emirate-checkbox-item:hover {
  background-color: rgba(212, 175, 55, 0.1);
}

.partner-registration-modal .partner-emirate-checkbox-item:last-child {
  margin-bottom: 0;
}

.partner-registration-modal .partner-emirate-checkbox {
  width: 18px;
  height: 18px;
  margin-right: 10px;
  accent-color: #d4af37;
  cursor: pointer;
  vertical-align: middle;
  position: relative;
  top: -1px;
}

.partner-registration-modal .partner-emirate-label {
  color: #ffffff;
  font-size: 14px;
  cursor: pointer;
  user-select: none;
  display: inline;
  vertical-align: middle;
  line-height: normal;
}

.partner-registration-modal .partner-form-helper-text {
  font-size: 12px;
  color: #888888;
  margin-bottom: 10px;
  margin-top: -5px;
}

/* Scrollbar styling for emirates container */
.partner-registration-modal .partner-emirates-checkbox-container::-webkit-scrollbar {
  width: 6px;
}

.partner-registration-modal .partner-emirates-checkbox-container::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 3px;
}

.partner-registration-modal .partner-emirates-checkbox-container::-webkit-scrollbar-thumb {
  background: #d4af37;
  border-radius: 3px;
}

.partner-registration-modal .partner-emirates-checkbox-container::-webkit-scrollbar-thumb:hover {
  background: #f4d03f;
}

/* Loading state */
.partner-registration-modal .partner-loading {
  position: relative;
  overflow: hidden;
}

.partner-registration-modal .partner-loading::after {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.3), transparent);
  animation: partnerLoading 1.5s infinite;
}

/* 🎯 UNIQUE Animations */
@keyframes partnerModalFadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes partnerModalSlideIn {
  from {
    transform: translateY(-50px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes partnerLoading {
  0% { left: -100%; }
  100% { left: 100%; }
}

/* Responsive Design */
@media (max-width: 600px) {
  .partner-registration-modal .partner-modal-content {
    margin: 5% auto;
    width: 95%;
  }
  
  .partner-registration-modal .partner-modal-body {
    padding: 20px;
  }
  
  .partner-registration-modal .partner-form-actions {
    flex-direction: column;
  }
}

/* 🎯 2-COLUMN FORM LAYOUT */
.partner-registration-modal .partner-form-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
}

/* Desktop: 2 columns */
@media (min-width: 768px) {
  .partner-registration-modal .partner-form-grid {
    grid-template-columns: 1fr 1fr;
    column-gap: 25px;
  }

  /* Emirates full width */
  .partner-registration-modal .partner-full-width {
    grid-column: span 2;
  }
}

/* Increase Modal Width */
.partner-registration-page .partner-registration-modal .partner-modal-content {
  max-width: 1000px !important;
  width: 95% !important;
}


.partner-modal-content {
  width: 100% !important;
  flex: 0 0 100% !important;
}


.form-group {
    width: 100%;
    display: block;
}

.full-width-input {
    width: 100% !important;
    display: block;
    box-sizing: border-box;
}

.golden-label {
    color: #d4af37;
    font-weight: 600;
}


.full-width-input {
    width: 100% !important;
    max-width: 100% !important;
    display: block;
}

#partnerDocument {
    width: 100% !important;
}





input[type="file"] {
    background-color: #000 !important;
    color: #fff !important;
    border: 1px solid #444 !important;
    padding: 10px !important;
    border-radius: 6px;
}

</style>
</head>

<body>
<div class="partner-registration-page">
  <div class="custom-banner">
    <div class="image-overlay">
      <div class="overlay">

        <div class="overlay-content flex-column flex-md-row">
          <div class="hero-line m-0 mt-md-5">
            <span class="tagline-text">Join as a</span>
            <span class="heading-text">Partner / Customer</span>
          </div>
          <button class="cta-button" id="partnerRegisterBtn">Register Now</button>
        </div>

        <!-- Registration Modal -->
        <div id="partnerRegistrationModal" class="partner-registration-modal">
          <div class="partner-modal-content">
            <div class="partner-modal-header">
              <h2>Create Partner Account</h2>
              <span class="partner-modal-close" id="partnerCloseModal">&times;</span>
            </div>

            <div class="partner-modal-body">
              <form id="partnerRegistrationForm">
                @csrf

                <!-- GRID WRAPPER -->
                <div class="partner-form-grid">

                  <div class="partner-form-group">
                    <label for="partnerName">Full Name</label>
                    <input type="text" id="partnerName" name="name" placeholder="Enter your full name" required>
                    <span class="partner-error-msg" id="partnerName-error"></span>
                  </div>

                  <div class="partner-form-group">
                    <label for="partnerPhone">Phone Number</label>
                    <input type="tel" id="partnerPhone" name="phone" placeholder="+971 50 123 4567" required>
                    <span class="partner-error-msg" id="partnerPhone-error"></span>
                  </div>

                  <div class="partner-form-group">
                    <label for="partnerEmail">Email Address</label>
                    <input type="email" id="partnerEmail" name="email" placeholder="your.email@domain.com" required>
                    <span class="partner-error-msg" id="partnerEmail-error"></span>
                  </div>

                  <div class="partner-form-group">
                    <label for="partnerPassword">Password</label>
                    <input type="password" id="partnerPassword" name="password" placeholder="Create a secure password" required>
                    <span class="partner-error-msg" id="partnerPassword-error"></span>
                  </div>





<!-- File upload -->
<div class="container-fluid">
   <div class="form-group mb-3 w-100" style="width:100% !important;">

        <label for="partnerDocument" class="form-label golden-label">
            Upload Trade License Documents(Max 4 Files)
        </label>
        <input type="file"
               name="partner_documents[]"
               id="partnerDocument"
               class="form-control full-width-input"
               accept=".pdf,.doc,.docx,.png,.jpg,.jpeg"
               multiple
               required>
    </div>
</div>






                  <!-- Emirates (Full width) -->
                   <div class="partner-form-group partner-full-width">
                    <label class="form-label">Select Emirates (Partner Access)</label>
                    <p class="partner-form-helper-text">Choose the emirates you want to operate in:</p>

                    <div class="partner-emirates-checkbox-container" id="partnerEmiratesContainer">
                      <div class="partner-emirates-loading">Loading emirates...</div>
                    </div>

                    <span class="partner-error-msg" id="partnerEmirates-error"></span>
                  </div>

                </div> 




     



                

                

                <!-- Form Buttons -->
                <div class="partner-form-actions">
                  <button type="submit" class="partner-submit-btn">Create Partner Account</button>
                  <button type="button" class="partner-cancel-btn" id="partnerCancelBtn">Cancel</button>
                </div>

              </form>
            </div>
          </div>
        </div>

        <div class="container mb-md-5">
          <div id="carousel-wrapper" class="mb-4">
            <div class="owl-carousel owl-theme">
              @foreach($carouselImages as $image)
                <div class="item">
                  <img src="{{ asset($image->imgPath) }}" alt="Carousel Image">
                </div>
              @endforeach
            </div>
          </div>

          <div class="mt-3 w-100">
            @include('banner0')
          </div>
        </div>

      </div>
    </div>
  </div>
</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Include jQuery & Owl Carousel -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<script>
// 🎯 UNIQUE SCOPED JAVASCRIPT - All variables/functions prefixed with 'Partner'
(function() {
  'use strict';
  
  // Partner Registration Modal Functionality
  document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Partner registration modal initializing...');
    
    // Modal Elements
    const partnerModal = document.getElementById('partnerRegistrationModal');
    const partnerRegisterBtn = document.getElementById('partnerRegisterBtn');
    const partnerCloseModal = document.getElementById('partnerCloseModal');
    const partnerCancelBtn = document.getElementById('partnerCancelBtn');
    const partnerRegistrationForm = document.getElementById('partnerRegistrationForm');
    const partnerEmiratesContainer = document.getElementById('partnerEmiratesContainer');

    // 🎯 NEW: Phone Number Validation
    const partnerPhoneInput = document.getElementById('partnerPhone');
    
    if (partnerPhoneInput) {
      // Allow only numbers, +, -, spaces, and parentheses
      partnerPhoneInput.addEventListener('input', function(e) {
        // Remove any character that isn't a number, +, -, space, or parentheses
        let value = e.target.value.replace(/[^0-9+\-\s()]/g, '');
        e.target.value = value;
      });

      // Prevent paste of non-numeric content
      partnerPhoneInput.addEventListener('paste', function(e) {
        e.preventDefault();
        let paste = (e.clipboardData || window.clipboardData).getData('text');
        // Clean pasted content to only allow numbers and phone characters
        let cleanPaste = paste.replace(/[^0-9+\-\s()]/g, '');
        this.value = cleanPaste;
      });

      // Enhanced validation on blur
      partnerPhoneInput.addEventListener('blur', function() {
        const value = this.value.trim();
        const errorElement = document.getElementById('partnerPhone-error');
        
        if (!value) {
          if (errorElement) {
            errorElement.textContent = 'Phone number is required';
          }
        } else if (value.length < 7) {
          if (errorElement) {
            errorElement.textContent = 'Phone number must be at least 7 digits';
          }
        } else if (!/^[+]?[0-9\-\s()]+$/.test(value)) {
          if (errorElement) {
            errorElement.textContent = 'Please enter a valid phone number';
          }
        } else {
          if (errorElement) {
            errorElement.textContent = '';
          }
        }
      });
    }

    // Check if elements exist
    if (!partnerModal || !partnerRegisterBtn || !partnerCloseModal || !partnerCancelBtn || !partnerRegistrationForm) {
        console.error('❌ Partner modal elements not found!');
        return;
    }

    console.log('✅ Partner modal elements found, setting up functionality...');

    // Open Modal and Load Emirates
    partnerRegisterBtn.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('📝 Opening partner registration modal...');
        
        // Show loading message
        partnerEmiratesContainer.innerHTML = '<div class="partner-emirates-loading">Loading emirates...</div>';
        
        // Show modal first
        partnerModal.style.display = 'block';
        document.body.style.overflow = 'hidden';
        
        // Then load emirates
        partnerLoadEmirates();
    });

    // Close Modal Functions
    function partnerCloseModalFunction() {
        console.log('❌ Closing partner modal...');
        partnerModal.style.display = 'none';
        document.body.style.overflow = 'auto';
        partnerClearErrors();
        partnerRegistrationForm.reset();
        // Reset emirates container
        partnerEmiratesContainer.innerHTML = '<div class="partner-emirates-loading">Loading emirates...</div>';
    }

    partnerCloseModal.addEventListener('click', partnerCloseModalFunction);
    partnerCancelBtn.addEventListener('click', partnerCloseModalFunction);

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === partnerModal) {
            partnerCloseModalFunction();
        }
    });

    // Load emirates function
    function partnerLoadEmirates() {
        console.log('🌍 Loading partner emirates...');
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            console.error('❌ CSRF token not found');
            partnerEmiratesContainer.innerHTML = '<div class="partner-emirates-loading" style="color: #ff6b6b;">Error: Please refresh the page</div>';
            return;
        }
        
        fetch('/get-emirates', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('📡 Partner emirates response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Partner emirates data received:', data);
            if (data.success && data.emirates) {
                partnerRenderEmiratesCheckboxes(data.emirates);
            } else {
                console.error('❌ Failed to load partner emirates:', data);
                partnerEmiratesContainer.innerHTML = '<div class="partner-emirates-loading" style="color: #ff6b6b;">Failed to load emirates. Please try again.</div>';
            }
        })
        .catch(error => {
            console.error('❌ Error loading partner emirates:', error);
            partnerEmiratesContainer.innerHTML = '<div class="partner-emirates-loading" style="color: #ff6b6b;">Network error. Please check your connection and try again.</div>';
        });
    }

    // Render emirates checkboxes with proper HTML structure
    function partnerRenderEmiratesCheckboxes(emirates) {
        console.log('🎨 Rendering partner emirates checkboxes:', emirates);
        const container = partnerEmiratesContainer;
        
        if (!emirates || emirates.length === 0) {
            container.innerHTML = '<div class="partner-emirates-loading" style="color: #ff6b6b;">No emirates available</div>';
            return;
        }
        
        let html = '';
        emirates.forEach(emirate => {
            // Clean the emirate name and ID
            const emirateName = String(emirate.name || '').trim();
            const emirateId = String(emirate.id || '').trim();
            
            if (emirateName && emirateId) {
                html += `
                    <div class="partner-emirate-checkbox-item">
                        <input type="checkbox" 
                               id="partnerEmirate_${emirateId}" 
                               name="emirates[]" 
                               value="${emirateId}" 
                               class="partner-emirate-checkbox">
                        <label for="partnerEmirate_${emirateId}" class="partner-emirate-label">${emirateName}</label>
                    </div>
                `;
            }
        });
        
        if (html) {
            container.innerHTML = html;
            console.log('✅ Partner emirates checkboxes rendered successfully');
        } else {
            container.innerHTML = '<div class="partner-emirates-loading" style="color: #ff6b6b;">No valid emirates found</div>';
        }
    }

    // Clear error messages
    function partnerClearErrors() {
        const errorElements = document.querySelectorAll('.partner-error-msg');
        errorElements.forEach(element => {
            element.textContent = '';
        });
    }

    // Display error messages
    function partnerDisplayErrors(errors) {
        partnerClearErrors();
        
        const fieldMapping = {
            'name': 'partnerName',
            'phone': 'partnerPhone', 
            'email': 'partnerEmail',
            'password': 'partnerPassword',
            'emirates': 'partnerEmirates'
        };
        
        Object.keys(errors).forEach(field => {
            const mappedField = fieldMapping[field] || field;
            const errorElement = document.getElementById(mappedField + '-error');
            if (errorElement && errors[field][0]) {
                errorElement.textContent = errors[field][0];
            }
        });
    }

    // Form submission with AJAX
    partnerRegistrationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('📤 Partner form submitted');
        
        const submitBtn = document.querySelector('.partner-submit-btn');
        const formData = new FormData(partnerRegistrationForm);
        
        // Get selected emirates
        const selectedEmirates = [];
        const checkboxes = document.querySelectorAll('input[name="emirates[]"]:checked');
        checkboxes.forEach(checkbox => {
            selectedEmirates.push(checkbox.value);
        });

        console.log('📋 Selected emirates:', selectedEmirates);

        // Validate emirates selection
        if (selectedEmirates.length === 0) {
            document.getElementById('partnerEmirates-error').textContent = 'Please select at least one emirate';
            return;
        }

        // Add emirates to form data
        formData.append('selected_emirates', selectedEmirates.join(','));
        
        // Add loading state
        submitBtn.disabled = true;
        submitBtn.classList.add('partner-loading');
        submitBtn.textContent = 'Creating Account...';
        
        // Clear previous errors
        partnerClearErrors();
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            alert('CSRF token not found. Please refresh the page.');
            partnerResetButton();
            return;
        }
        
        console.log('📡 Sending partner registration request...');
        
        // AJAX request
        fetch('/register', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => {
            console.log('📡 Partner registration response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('📋 Partner registration response data:', data);
            
            if (data.success) {
                alert('🎉 Partner registration successful! Welcome aboard!');
                partnerCloseModalFunction();
                window.location.reload();
            } else if (data.errors) {
                partnerDisplayErrors(data.errors);
            } else {
                alert('Registration failed: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('❌ Partner registration error:', error);
            alert('Network error occurred. Please try again.');
        })
        .finally(() => {
            partnerResetButton();
        });
        
        function partnerResetButton() {
            submitBtn.disabled = false;
            submitBtn.classList.remove('partner-loading');
            submitBtn.textContent = 'Create Partner Account';
        }
    });

    // 🎯 UPDATED: Real-time validation with enhanced phone validation
    const partnerInputs = document.querySelectorAll('#partnerRegistrationForm input[type="text"], #partnerRegistrationForm input[type="email"], #partnerRegistrationForm input[type="password"]');
    partnerInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                const fieldName = this.name;
                const errorElement = document.getElementById(this.id + '-error');
                if (errorElement) {
                    errorElement.textContent = `${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)} is required`;
                }
            } else {
                const errorElement = document.getElementById(this.id + '-error');
                if (errorElement) {
                    errorElement.textContent = '';
                }
            }
        });
    });

    console.log('🎉 Partner modal initialization complete');
  });

  // Original carousel code (unchanged)
  window.addEventListener('load', function () {
    console.log('🎠 Window loaded, initializing partner carousel...');
    
    const wrapper = document.getElementById('carousel-wrapper');
    const overlayContent = document.querySelector('.partner-registration-page .custom-banner .overlay-content');
    
    $(".partner-registration-page .custom-banner .owl-carousel").owlCarousel({
      items: 3,
      margin: 20,
      loop: true,
      autoplay: true,
      autoplayTimeout: 2500,
      smartSpeed: 300,
      dots: false,
      responsive: {
        0: { items: 1 },
        768: { items: 2 },
        1024: { items: 3 }
      }
    });

    if (wrapper) {
      wrapper.classList.add('visible');
    }
    if (overlayContent) {
      setTimeout(() => overlayContent.classList.add('visible'), 100);
    }
    
    console.log('✅ Partner carousel initialized');
  });

})(); // End of scoped IIFE
</script>

</body>
</html>
