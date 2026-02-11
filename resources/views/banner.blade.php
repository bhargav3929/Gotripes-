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
  <!-- Owl Carousel CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

  <style>
    /* 🎯 UNIQUE SCOPED STYLES - Prefixed with .partner-registration-page */
    .partner-registration-page .custom-banner {
      padding-top: 200px;
      /* Header(80) + SearchBar(71) + Ticker(45) + buffer */
      background: #000;
    }

    .partner-registration-page .custom-banner .image-overlay {
      position: relative;
      overflow: hidden;
      min-height: calc(100vh - 200px);
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
      top: 0;
      left: 0;
      width: 100%;
      min-height: calc(100vh - 200px);
      background: rgba(0, 0, 0, 0.5);
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
        padding-top: 170px;
        /* Mobile header + search + ticker */
      }

      .partner-registration-page .custom-banner .image-overlay {
        min-height: calc(100vh - 170px);
        height: auto;
        background-position: top center;
        background-attachment: scroll;
      }

      .partner-registration-page .custom-banner .overlay {
        min-height: calc(100vh - 170px);
        position: relative;
        height: auto;
      }
    }

    /* Desktop adjustments */
    @media (min-width: 768px) {
      .partner-registration-page .custom-banner {
        padding-top: 200px;
        /* Desktop header + search + ticker */
      }

      .partner-registration-page .custom-banner .image-overlay {
        min-height: calc(100vh - 200px);
      }

      .partner-registration-page .custom-banner .overlay {
        min-height: calc(100vh - 200px);
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
      font-family: 'Outfit', sans-serif;
      /* Changed from Satisfy to Outfit */
      font-style: italic;
      font-weight: 300;
    }

    .partner-registration-page .custom-banner .heading-text {
      color: #fff;
    }

    .partner-registration-page .custom-banner .cta-button {
      background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
      color: #000;
      font-size: 1.1rem;
      font-weight: 700;
      border-radius: 50px;
      padding: 14px 40px;
      text-align: center;
      border: none;
      white-space: nowrap;
      transition: all 0.3s ease;
      cursor: pointer;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      box-shadow: 0 5px 20px rgba(255, 215, 0, 0.3);
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

    /* 🎯 PREMIUM MODAL STYLES - Inspired by Pay Online Page */
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    .partner-registration-modal {
      display: none;
      position: fixed;
      z-index: 10000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.9);
      backdrop-filter: blur(10px);
      animation: partnerModalFadeIn 0.3s ease-in-out;
      overflow-y: auto;
      padding: 20px 0;
    }

    .partner-registration-modal .partner-modal-content {
      background: linear-gradient(165deg, #0e0e0e 0%, #080808 100%);
      margin: 20px auto;
      padding: 0;
      border: 1px solid rgba(255, 215, 0, 0.15);
      border-radius: 24px;
      width: 95%;
      max-width: 800px;
      box-shadow:
        0 25px 80px rgba(0, 0, 0, 0.8),
        0 0 60px rgba(255, 215, 0, 0.08),
        inset 0 1px 0 rgba(255, 255, 255, 0.03);
      animation: partnerModalSlideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
      color: #ffffff;
      position: relative;
      font-family: 'Outfit', sans-serif;
    }

    .partner-registration-modal .partner-modal-content::before {
      content: '';
      position: absolute;
      top: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 60%;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(255, 215, 0, 0.6), transparent);
    }

    .partner-registration-modal .partner-modal-header {
      background: transparent;
      padding: 20px 35px 5px;
      border-radius: 24px 24px 0 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: none;
    }

    .partner-registration-modal .partner-modal-header h2 {
      margin: 0;
      color: #FFD700;
      font-family: 'Outfit', sans-serif;
      font-size: 22px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 3px;
      text-shadow: 0 0 40px rgba(255, 215, 0, 0.3);
    }

    .partner-registration-modal .partner-modal-close {
      color: #666;
      font-size: 32px;
      font-weight: 300;
      cursor: pointer;
      transition: all 0.3s ease;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid #2a2a2a;
    }

    .partner-registration-modal .partner-modal-close:hover {
      color: #FFD700;
      background: rgba(255, 215, 0, 0.1);
      border-color: rgba(255, 215, 0, 0.3);
      transform: rotate(90deg);
    }

    .partner-registration-modal .partner-modal-body {
      padding: 15px 35px 25px;
    }

    /* Premium Form Grid */
    .partner-registration-modal .partner-form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
    }

    .partner-registration-modal .partner-form-group {
      margin-bottom: 0;
    }

    .partner-registration-modal .partner-form-group label {
      display: block;
      margin-bottom: 8px;
      color: #FFD700;
      font-family: 'Outfit', sans-serif;
      font-size: 10px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1.5px;
    }

    .partner-registration-modal .partner-form-group input[type="text"],
    .partner-registration-modal .partner-form-group input[type="email"],
    .partner-registration-modal .partner-form-group input[type="password"],
    .partner-registration-modal .partner-form-group input[type="tel"],
    .partner-registration-modal .partner-form-group input[type="date"] {
      width: 100%;
      height: 44px;
      background: linear-gradient(145deg, #0e0e0e 0%, #0a0a0a 100%);
      border: 1px solid #222;
      border-radius: 10px;
      padding: 0 16px;
      color: #fff;
      font-family: 'Outfit', sans-serif;
      font-size: 14px;
      font-weight: 500;
      transition: all 0.25s ease;
      box-sizing: border-box;
      box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .partner-registration-modal .partner-form-group input:hover {
      border-color: rgba(255, 215, 0, 0.25);
      background: linear-gradient(145deg, #141414 0%, #0e0e0e 100%);
    }

    .partner-registration-modal .partner-form-group input:focus {
      outline: none;
      border-color: #FFD700;
      background: linear-gradient(145deg, #151515 0%, #101010 100%);
      box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1), inset 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    .partner-registration-modal .partner-form-group input::placeholder {
      color: #555;
      font-weight: 400;
    }

    /* File Input Premium Style */
    .partner-registration-modal .partner-form-group input[type="file"] {
      width: 100%;
      height: 44px;
      background: linear-gradient(145deg, #0e0e0e 0%, #0a0a0a 100%);
      border: 1px solid #222;
      border-radius: 10px;
      padding: 0;
      color: transparent;
      font-family: 'Outfit', sans-serif;
      font-size: 0;
      cursor: pointer;
      transition: all 0.25s ease;
      position: relative;
      overflow: hidden;
      box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3);
      display: flex;
      align-items: center;
    }

    .partner-registration-modal .partner-form-group input[type="file"]:hover {
      border-color: rgba(255, 215, 0, 0.4);
      background: linear-gradient(145deg, #141414 0%, #0e0e0e 100%);
    }

    .partner-registration-modal .partner-form-group input[type="file"]::file-selector-button {
      background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
      color: #000;
      border: none;
      padding: 0 20px;
      border-radius: 8px;
      font-family: 'Outfit', sans-serif;
      font-weight: 700;
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
      cursor: pointer;
      margin: 4px;
      height: calc(100% - 8px);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      transition: all 0.25s ease;
    }

    .partner-registration-modal .partner-form-group input[type="file"]::file-selector-button:hover {
      background: linear-gradient(135deg, #fff 0%, #f0f0f0 100%);
    }

    .partner-registration-modal .partner-error-msg {
      display: block;
      color: #ff6b6b;
      font-size: 11px;
      margin-top: 6px;
      min-height: 14px;
      font-weight: 500;
    }

    /* Full Width Field */
    .partner-registration-modal .partner-full-width {
      grid-column: 1 / -1;
    }

    /* Emirates Section Premium Style */
    .partner-registration-modal .partner-emirates-section-title {
      color: #FFD700;
      font-family: 'Outfit', sans-serif;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 2px;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .partner-registration-modal .partner-emirates-section-title::before {
      content: '';
      width: 4px;
      height: 16px;
      background: #FFD700;
      border-radius: 2px;
    }

    .partner-registration-modal .partner-form-helper-text {
      font-size: 10px;
      color: #666;
      margin-bottom: 12px;
      line-height: 1.4;
    }

    .partner-registration-modal .partner-emirates-checkbox-container {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
      padding: 0;
      background: transparent;
      border: none;
    }

    .partner-registration-modal .partner-emirate-checkbox-item {
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: flex-start;
      padding: 0 16px;
      background: linear-gradient(145deg, #0e0e0e 0%, #0a0a0a 100%);
      border: 1px solid #222;
      border-radius: 50px;
      cursor: pointer;
      transition: all 0.25s ease;
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.02);
      height: 38px;
      box-sizing: border-box;
    }

    .partner-registration-modal .partner-emirate-checkbox-item:hover {
      border-color: rgba(255, 215, 0, 0.3);
      background: linear-gradient(145deg, #141414 0%, #0e0e0e 100%);
      transform: translateY(-1px);
    }

    .partner-registration-modal .partner-emirate-checkbox-item.selected {
      border-color: #FFD700;
      background: linear-gradient(145deg, rgba(255, 215, 0, 0.08) 0%, rgba(255, 215, 0, 0.03) 100%);
      box-shadow: 0 0 15px rgba(255, 215, 0, 0.1), inset 0 1px 0 rgba(255, 215, 0, 0.1);
    }

    .partner-registration-modal .partner-emirate-checkbox {
      width: 14px;
      height: 14px;
      min-width: 14px;
      min-height: 14px;
      margin: 0 10px 0 0;
      accent-color: #FFD700;
      cursor: pointer;
      flex-shrink: 0;
      vertical-align: middle;
    }

    .partner-registration-modal .partner-emirate-label {
      color: #ddd;
      font-family: 'Outfit', sans-serif;
      font-size: 10px;
      font-weight: 600;
      cursor: pointer;
      user-select: none;
      white-space: nowrap;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      line-height: 38px;
    }

    /* Premium Buttons */
    .partner-registration-modal .partner-form-actions {
      display: flex;
      gap: 12px;
      margin-top: 18px;
    }

    .partner-registration-modal .partner-submit-btn {
      flex: 2;
      height: 46px;
      background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
      color: #000;
      border: none;
      border-radius: 50px;
      font-family: 'Outfit', sans-serif;
      font-size: 14px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 3px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 20px rgba(255, 215, 0, 0.2);
    }

    .partner-registration-modal .partner-submit-btn:hover:not(:disabled) {
      background: linear-gradient(135deg, #fff 0%, #f0f0f0 100%);
      transform: translateY(-2px);
      box-shadow: 0 8px 30px rgba(255, 255, 255, 0.15);
    }

    .partner-registration-modal .partner-submit-btn:disabled {
      background: #1a1a1a;
      color: #444;
      cursor: not-allowed;
      box-shadow: none;
      transform: none;
    }

    .partner-registration-modal .partner-cancel-btn {
      flex: 1;
      height: 46px;
      background: transparent;
      color: #666;
      border: 1px solid #2a2a2a;
      border-radius: 50px;
      font-family: 'Outfit', sans-serif;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 2px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .partner-registration-modal .partner-cancel-btn:hover {
      color: #fff;
      border-color: #444;
      background: rgba(255, 255, 255, 0.03);
    }

    /* Animations */
    @keyframes partnerModalFadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    @keyframes partnerModalSlideIn {
      from {
        transform: translateY(-30px) scale(0.98);
        opacity: 0;
      }

      to {
        transform: translateY(0) scale(1);
        opacity: 1;
      }
    }

    /* Responsive */
    @media (max-width: 768px) {
      .partner-registration-modal .partner-form-grid {
        grid-template-columns: 1fr;
      }

      .partner-registration-modal .partner-modal-body {
        padding: 20px 25px 30px;
      }

      .partner-registration-modal .partner-modal-header {
        padding: 25px 25px 10px;
      }

      .partner-registration-modal .partner-modal-header h2 {
        font-size: 20px;
        letter-spacing: 2px;
      }

      .partner-registration-modal .partner-form-actions {
        flex-direction: column;
      }

      .partner-registration-modal .partner-emirates-checkbox-container {
        grid-template-columns: repeat(2, 1fr);
      }
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

    /* ============================================
       SUPPLIER ADVERTISEMENT CAROUSEL
       For airlines, top agencies, and offers
       ============================================ */
    .supplier-ad-carousel {
      width: 100%;
      padding: 15px 0;
      background: linear-gradient(180deg, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0.1) 100%);
      overflow: hidden;
      margin-bottom: 20px;
    }

    .supplier-ad-carousel .ad-container {
      display: flex;
      gap: 20px;
      justify-content: center;
      align-items: center;
      flex-wrap: wrap;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
    }

    .supplier-ad-carousel .ad-slot {
      background: rgba(10, 10, 10, 0.85);
      border: 1px solid rgba(255, 215, 0, 0.2);
      border-radius: 12px;
      padding: 12px 20px;
      min-width: 180px;
      max-width: 220px;
      text-align: center;
      cursor: pointer;
      transition: all 0.4s ease;
      backdrop-filter: blur(10px);
      animation: adPulse 3s ease-in-out infinite;
    }

    .supplier-ad-carousel .ad-slot:nth-child(1) {
      animation-delay: 0s;
    }

    .supplier-ad-carousel .ad-slot:nth-child(2) {
      animation-delay: 0.5s;
    }

    .supplier-ad-carousel .ad-slot:nth-child(3) {
      animation-delay: 1s;
    }

    .supplier-ad-carousel .ad-slot:nth-child(4) {
      animation-delay: 1.5s;
    }

    .supplier-ad-carousel .ad-slot:nth-child(5) {
      animation-delay: 2s;
    }

    @keyframes adPulse {

      0%,
      100% {
        opacity: 0.85;
        transform: scale(1);
        box-shadow: 0 4px 15px rgba(255, 215, 0, 0.1);
      }

      50% {
        opacity: 1;
        transform: scale(1.02);
        box-shadow: 0 6px 25px rgba(255, 215, 0, 0.2);
      }
    }

    .supplier-ad-carousel .ad-slot:hover {
      border-color: #FFD700;
      transform: scale(1.05) !important;
      box-shadow: 0 8px 30px rgba(255, 215, 0, 0.25) !important;
    }

    .supplier-ad-carousel .ad-slot .ad-icon {
      font-size: 24px;
      color: #FFD700;
      margin-bottom: 8px;
    }

    .supplier-ad-carousel .ad-slot .ad-title {
      font-family: 'Outfit', sans-serif;
      font-size: 12px;
      font-weight: 700;
      color: #FFD700;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 4px;
    }

    .supplier-ad-carousel .ad-slot .ad-subtitle {
      font-family: 'Outfit', sans-serif;
      font-size: 10px;
      color: #aaa;
      line-height: 1.3;
    }

    /* Mobile responsive ads */
    @media (max-width: 768px) {
      .supplier-ad-carousel .ad-container {
        gap: 10px;
        padding: 0 10px;
      }

      .supplier-ad-carousel .ad-slot {
        min-width: 140px;
        max-width: 160px;
        padding: 10px 15px;
      }

      .supplier-ad-carousel .ad-slot .ad-icon {
        font-size: 20px;
      }

      .supplier-ad-carousel .ad-slot .ad-title {
        font-size: 10px;
      }

      .supplier-ad-carousel .ad-slot .ad-subtitle {
        font-size: 9px;
      }
    }
  </style>
</head>

<body>
  <div class="partner-registration-page">
    <div class="custom-banner">
      <div class="image-overlay">
        <div class="overlay">

          <!-- Supplier ads will be a subtle rotating single ad below the search panel -->


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

                    <!-- Country Dropdown - Dynamically populated -->
                    <div class="partner-form-group">
                      <label for="partnerCountry">Country</label>
                      <select id="partnerCountry" name="country" required
                        style="width:100%; padding:12px 15px; background:#000; border:1px solid #444; color:#fff; border-radius:8px; font-size:14px;">
                        <option value="">Select your country</option>
                      </select>
                      <span class="partner-error-msg" id="partnerCountry-error"></span>
                    </div>

                    <div class="partner-form-group">
                      <label for="partnerPassword">Password</label>
                      <input type="password" id="partnerPassword" name="password" placeholder="Create a secure password"
                        required>
                      <span class="partner-error-msg" id="partnerPassword-error"></span>
                    </div>

                    <div class="partner-form-group">
                      <label for="partnerLicenseExpiry">Trade License Expiry Date</label>
                      <input type="date" id="partnerLicenseExpiry" name="license_expiry" required>
                      <span class="partner-error-msg" id="partnerLicenseExpiry-error"></span>
                    </div>

                    <div class="partner-form-group">
                      <label for="partnerDocument">Trade License Documents</label>
                      <input type="file" name="partner_documents[]" id="partnerDocument"
                        accept=".pdf,.doc,.docx,.png,.jpg,.jpeg" multiple required>
                      <span class="partner-error-msg" id="partnerDocument-error"></span>
                    </div>





                    <!-- Emirates (Full width) -->
                    <div class="partner-form-group partner-full-width">
                      <div class="partner-emirates-section-title">Select Emirates</div>
                      <p class="partner-form-helper-text">Choose the emirates you want to operate in (multiple selection
                        allowed):</p>

                      <div class="partner-emirates-checkbox-container" id="partnerEmiratesContainer">
                        <div class="partner-emirate-checkbox-item" onclick="toggleEmirateSelection(this)">
                          <input type="checkbox" id="partnerEmirate_1" name="emirates[]" value="1"
                            class="partner-emirate-checkbox">
                          <label for="partnerEmirate_1" class="partner-emirate-label">Abu Dhabi</label>
                        </div>
                        <div class="partner-emirate-checkbox-item" onclick="toggleEmirateSelection(this)">
                          <input type="checkbox" id="partnerEmirate_2" name="emirates[]" value="2"
                            class="partner-emirate-checkbox">
                          <label for="partnerEmirate_2" class="partner-emirate-label">Dubai</label>
                        </div>
                        <div class="partner-emirate-checkbox-item" onclick="toggleEmirateSelection(this)">
                          <input type="checkbox" id="partnerEmirate_3" name="emirates[]" value="3"
                            class="partner-emirate-checkbox">
                          <label for="partnerEmirate_3" class="partner-emirate-label">Sharjah</label>
                        </div>
                        <div class="partner-emirate-checkbox-item" onclick="toggleEmirateSelection(this)">
                          <input type="checkbox" id="partnerEmirate_4" name="emirates[]" value="4"
                            class="partner-emirate-checkbox">
                          <label for="partnerEmirate_4" class="partner-emirate-label">Ajman</label>
                        </div>
                        <div class="partner-emirate-checkbox-item" onclick="toggleEmirateSelection(this)">
                          <input type="checkbox" id="partnerEmirate_5" name="emirates[]" value="5"
                            class="partner-emirate-checkbox">
                          <label for="partnerEmirate_5" class="partner-emirate-label">Umm Al Quwain</label>
                        </div>
                        <div class="partner-emirate-checkbox-item" onclick="toggleEmirateSelection(this)">
                          <input type="checkbox" id="partnerEmirate_6" name="emirates[]" value="6"
                            class="partner-emirate-checkbox">
                          <label for="partnerEmirate_6" class="partner-emirate-label">Ras Al Khaimah</label>
                        </div>
                        <div class="partner-emirate-checkbox-item" onclick="toggleEmirateSelection(this)">
                          <input type="checkbox" id="partnerEmirate_7" name="emirates[]" value="7"
                            class="partner-emirate-checkbox">
                          <label for="partnerEmirate_7" class="partner-emirate-label">Fujairah</label>
                        </div>
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
            <div class="mt-0 w-100">
              @include('banner0')
            </div>

            <div class="overlay-content d-flex flex-column flex-md-row justify-content-center align-items-center w-100 mt-0 mb-0"
                 style="gap: 1.2rem; visibility: visible; opacity: 1; margin-top: 15px !important; margin-bottom: 10px !important;">
              <div class="hero-line m-0 text-center text-md-start d-flex align-items-baseline gap-2">
                <span class="tagline-text" style="font-size: 1.0rem; color: #FFD23F; font-style: italic;">Join as a</span>
                <span class="heading-text" style="font-size: 1.5rem; color: #ffffff; font-weight: 700;">Partner / Customer</span>
              </div>
              <button class="cta-button" id="partnerRegisterBtn" style="margin: 0;">Register Now</button>
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
    // Toggle emirate checkbox selection with visual feedback
    function toggleEmirateSelection(element) {
      const checkbox = element.querySelector('input[type="checkbox"]');
      if (checkbox) {
        checkbox.checked = !checkbox.checked;
        if (checkbox.checked) {
          element.classList.add('selected');
        } else {
          element.classList.remove('selected');
        }
      }
    }
  </script>

  <script>
    // 🎯 UNIQUE SCOPED JAVASCRIPT - All variables/functions prefixed with 'Partner'
    (function () {
      'use strict';

      // Partner Registration Modal Functionality
      document.addEventListener('DOMContentLoaded', function () {
        console.log('🚀 Partner registration modal initializing...');

        // Load countries from API for the country dropdown
        const countrySelect = document.getElementById('partnerCountry');
        if (countrySelect) {
          fetch('https://restcountries.com/v3.1/all?fields=name')
            .then(res => res.json())
            .then(countries => {
              // Sort countries alphabetically
              countries.sort((a, b) => a.name.common.localeCompare(b.name.common));
              countries.forEach(country => {
                const option = document.createElement('option');
                option.value = country.name.common;
                option.textContent = country.name.common;
                countrySelect.appendChild(option);
              });
              console.log('✅ Countries loaded successfully');
            })
            .catch(err => {
              console.error('❌ Failed to load countries:', err);
            });
        }

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
          partnerPhoneInput.addEventListener('input', function (e) {
            // Remove any character that isn't a number, +, -, space, or parentheses
            let value = e.target.value.replace(/[^0-9+\-\s()]/g, '');
            e.target.value = value;
          });

          // Prevent paste of non-numeric content
          partnerPhoneInput.addEventListener('paste', function (e) {
            e.preventDefault();
            let paste = (e.clipboardData || window.clipboardData).getData('text');
            // Clean pasted content to only allow numbers and phone characters
            let cleanPaste = paste.replace(/[^0-9+\-\s()]/g, '');
            this.value = cleanPaste;
          });

          // Enhanced validation on blur
          partnerPhoneInput.addEventListener('blur', function () {
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

        // Open Modal
        partnerRegisterBtn.addEventListener('click', function (e) {
          e.preventDefault();
          console.log('📝 Opening partner registration modal...');

          // Show modal
          partnerModal.style.display = 'block';
          document.body.style.overflow = 'hidden';
        });

        // Close Modal Functions
        function partnerCloseModalFunction() {
          console.log('❌ Closing partner modal...');
          partnerModal.style.display = 'none';
          document.body.style.overflow = 'auto';
          partnerClearErrors();
          partnerRegistrationForm.reset();
          // Clear emirate selections
          document.querySelectorAll('.partner-emirate-checkbox-item').forEach(item => {
            item.classList.remove('selected');
          });
        }

        partnerCloseModal.addEventListener('click', partnerCloseModalFunction);
        partnerCancelBtn.addEventListener('click', partnerCloseModalFunction);

        // Close modal when clicking outside
        window.addEventListener('click', function (event) {
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
        partnerRegistrationForm.addEventListener('submit', function (e) {
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
          input.addEventListener('blur', function () {
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
          items: 6,
          margin: 15,
          loop: true,
          autoplay: true,
          autoplayTimeout: 0,
          smartSpeed: 4000,
          autoplaySpeed: 4000,
          autoplayHoverPause: false,
          dots: false,
          responsive: {
            0: { items: 2 },
            576: { items: 3 },
            768: { items: 4 },
            992: { items: 5 },
            1200: { items: 6 }
          }
        });

        // Force linear transition for smooth continuous scroll
        var owlStage = document.querySelector('.partner-registration-page .owl-stage');
        if (owlStage) {
          owlStage.style.transitionTimingFunction = 'linear';
        }
        // Re-apply linear on every translate event
        $('.partner-registration-page .owl-carousel').on('translate.owl.carousel', function() {
          $(this).find('.owl-stage').css('transition-timing-function', 'linear');
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