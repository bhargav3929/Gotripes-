<!DOCTYPE html>
<html>

<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Partner Registration</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Tom Select (searchable select dropdown) -->
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <!-- Owl Carousel CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

  <style>
    /* 🎯 UNIQUE SCOPED STYLES - Prefixed with .partner-registration-page */
    .partner-registration-page .custom-banner {
      padding-top: 0;
      /* Header spacing handled by body margin-top in header.blade.php */
      background: #000;
    }

    .partner-registration-page .custom-banner .image-overlay {
      position: relative;
      overflow: hidden;
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
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      padding: 0.5rem 1rem 0.5rem;
      height: auto;
    }

    /* Mobile adjustments */
    @media (max-width: 767.98px) {
      .partner-registration-page .custom-banner {
        padding-top: 0;
        /* Header spacing handled by body margin-top */
      }

      .partner-registration-page .custom-banner .image-overlay {
        height: auto !important;
        overflow: visible !important;
        clip: auto !important;
        clip-path: none !important;
        -webkit-clip-path: none !important;
        background-position: top center;
        background-attachment: scroll;
      }

      .partner-registration-page .custom-banner .overlay {
        position: relative;
        height: auto !important;
        overflow: visible !important;
        clip: auto !important;
        clip-path: none !important;
        padding-bottom: 0.8rem;
      }
    }

    /* Desktop adjustments */
    @media (min-width: 768px) {
      .partner-registration-page .custom-banner {
        padding-top: 0;
        /* Header spacing handled by body margin-top */
      }

      .partner-registration-page .custom-banner .image-overlay {
        height: auto;
      }

      .partner-registration-page .custom-banner .overlay {
        justify-content: flex-start;
        height: auto;
        padding-bottom: 1rem;
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
      max-width: 1140px;
      max-height: 88vh;
      display: flex;
      flex-direction: column;
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
      flex-shrink: 0;
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
      overflow-y: scroll;
      scrollbar-width: thin;
      scrollbar-color: #FFD700 rgba(255, 255, 255, 0.06);
    }
    /* Forced visible always — not just when content happens to overflow by
       a lot — so it never reads as just a thin stray line. */
    .partner-registration-modal .partner-modal-body::-webkit-scrollbar {
      width: 14px;
    }
    .partner-registration-modal .partner-modal-body::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.06);
      border-radius: 8px;
      margin: 4px 0;
    }
    .partner-registration-modal .partner-modal-body::-webkit-scrollbar-thumb {
      background-color: #FFD700;
      border-radius: 8px;
      border: 3px solid #141414;
      background-clip: padding-box;
      min-height: 40px;
    }
    .partner-registration-modal .partner-modal-body::-webkit-scrollbar-thumb:hover {
      background-color: #FFA500;
      background-clip: padding-box;
    }

    /* 3-column layout — same pattern as the Agent Registration form: one
       card per section, laid out side by side instead of stacked. */
    .partner-registration-modal .partner-columns {
      display: grid;
      /* Column 1 packs 2 fields per row (name/phone, email/company, license
         no/expiry) so it needs more room than columns 2-3 to avoid clipping
         placeholder text like "your.email@domain.com". */
      grid-template-columns: minmax(430px, 1.7fr) minmax(210px, 1fr) minmax(210px, 1fr);
      gap: 16px;
      align-items: start;
    }
    .partner-registration-modal .partner-col {
      background: rgba(255, 255, 255, 0.02);
      border: 1px solid rgba(255, 215, 0, 0.12);
      border-radius: 12px;
      padding: 16px;
      display: flex;
      flex-direction: column;
      gap: 12px;
    }
    .partner-registration-modal .partner-col-title {
      color: #FFD700;
      font-family: 'Outfit', sans-serif;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 2px;
      padding-bottom: 8px;
      margin-bottom: 2px;
      border-bottom: 1px solid rgba(255, 215, 0, 0.15);
    }
    .partner-registration-modal .partner-col .partner-form-group {
      margin-bottom: 0;
    }
    /* Pairs two fields per row inside a column — same technique as the
       Agent Registration form's .field-row. */
    .partner-registration-modal .partner-field-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
    }
    /* Secondary row below the 3 columns — license document + credentials,
       same as the Agent Registration form's .extra-row. */
    .partner-registration-modal .partner-extra-row {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 16px;
      margin-top: 16px;
    }
    @media (max-width: 900px) {
      .partner-registration-modal .partner-extra-row {
        grid-template-columns: 1fr;
      }
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
    .partner-registration-modal .partner-form-group input[type="date"],
    .partner-registration-modal .partner-form-group select {
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

    .partner-registration-modal .partner-form-group input:hover,
    .partner-registration-modal .partner-form-group select:hover {
      border-color: rgba(255, 215, 0, 0.25);
      background: linear-gradient(145deg, #141414 0%, #0e0e0e 100%);
    }

    .partner-registration-modal .partner-form-group input:focus,
    .partner-registration-modal .partner-form-group select:focus {
      outline: none;
      border-color: #FFD700;
      background: linear-gradient(145deg, #151515 0%, #101010 100%);
      box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1), inset 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    /* The select itself is styled white-on-dark, but a native option list
       renders with its own background — Chromium/Firefox honor option
       background/color and it stays readable; browsers that ignore it fall
       back to system dark-on-white, which is still readable, just off-theme. */
    .partner-registration-modal .partner-form-group select option {
      background: #141414;
      color: #ffffff;
      padding: 8px 12px;
    }

    /* Chrome paints autofilled fields with its own white background by
       default, ignoring the input's own styling unless overridden this way. */
    .partner-registration-modal .partner-form-group input:-webkit-autofill,
    .partner-registration-modal .partner-form-group input:-webkit-autofill:hover,
    .partner-registration-modal .partner-form-group input:-webkit-autofill:focus,
    .partner-registration-modal .partner-form-group input:-webkit-autofill:active {
      -webkit-box-shadow: 0 0 0 1000px #0e0e0e inset !important;
      -webkit-text-fill-color: #fff !important;
      caret-color: #fff !important;
      transition: background-color 9999s ease-in-out 0s;
    }

    /* UAE / Outside toggle */
    .partner-registration-modal .partner-uae-toggle {
      display: flex;
      gap: 8px;
    }
    .partner-registration-modal .partner-uae-option {
      flex: 1;
      margin: 0 !important;
      display: flex;
      align-items: center;
      gap: 6px;
      border: 1px solid #222;
      border-radius: 8px;
      padding: 9px 10px;
      cursor: pointer;
      font-size: 11px;
      font-weight: 600;
      text-transform: none;
      letter-spacing: normal;
      color: #ddd;
      background: linear-gradient(145deg, #0e0e0e 0%, #0a0a0a 100%);
      transition: all 0.2s ease;
    }
    .partner-registration-modal .partner-uae-option:has(input:checked) {
      border-color: #FFD700;
      background: linear-gradient(145deg, rgba(255, 215, 0, 0.08) 0%, rgba(255, 215, 0, 0.03) 100%);
    }
    .partner-registration-modal .partner-uae-option input {
      accent-color: #FFD700;
      cursor: pointer;
    }

    /* Services checkboxes */
    .partner-registration-modal .partner-services-grid {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .partner-registration-modal .partner-service-option {
      display: flex;
      align-items: center;
      gap: 10px;
      border: 1px solid #222;
      border-radius: 8px;
      padding: 10px 14px;
      min-height: 38px;
      background: linear-gradient(145deg, #0e0e0e 0%, #0a0a0a 100%);
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .partner-registration-modal .partner-service-option:hover {
      border-color: rgba(255, 215, 0, 0.3);
    }
    .partner-registration-modal .partner-service-option input {
      width: 14px;
      height: 14px;
      min-width: 14px;
      min-height: 14px;
      margin: 0;
      accent-color: #FFD700;
      cursor: pointer;
      flex-shrink: 0;
    }
    .partner-registration-modal .partner-service-option label {
      flex: 1;
      min-width: 0;
      margin: 0 !important;
      color: #ddd;
      font-size: 11px;
      font-weight: 600;
      text-transform: none;
      letter-spacing: normal;
      cursor: pointer;
      display: block;
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
      grid-template-columns: 1fr;
      gap: 8px;
      padding: 0;
      background: transparent;
      border: none;
    }

    .partner-registration-modal .partner-emirate-checkbox-item {
      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: flex-start;
      padding: 0 14px;
      background: linear-gradient(145deg, #0e0e0e 0%, #0a0a0a 100%);
      border: 1px solid #222;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.25s ease;
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.02);
      height: 38px;
      box-sizing: border-box;
      width: 100%;
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

    .partner-registration-modal .partner-back-btn {
      flex: 1;
      height: 46px;
      background: transparent;
      color: #FFD700;
      border: 1px solid rgba(255, 215, 0, 0.35);
      border-radius: 50px;
      font-family: 'Outfit', sans-serif;
      font-size: 12px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 2px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .partner-registration-modal .partner-back-btn:hover {
      background: rgba(255, 215, 0, 0.06);
      border-color: rgba(255, 215, 0, 0.6);
    }

    /* Step wizard: three sections, one visible at a time. Amer asked for
       this explicitly on the 2026-08-25 call — filling section 1 and
       clicking Next should be what reveals section 2, not everything on
       one screen. */
    .partner-registration-modal .partner-step-indicator {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin-bottom: 22px;
    }
    .partner-registration-modal .partner-step-dot {
      display: flex;
      align-items: center;
      gap: 8px;
      font-family: 'Outfit', sans-serif;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #555;
    }
    .partner-registration-modal .partner-step-dot .partner-step-num {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 24px;
      height: 24px;
      border-radius: 50%;
      border: 1px solid #333;
      color: #666;
      font-size: 12px;
      transition: all 0.25s ease;
    }
    .partner-registration-modal .partner-step-dot.is-active .partner-step-num {
      border-color: #FFD700;
      color: #000;
      background: linear-gradient(135deg, #FFD700 0%, #D4AF37 100%);
    }
    .partner-registration-modal .partner-step-dot.is-active {
      color: #FFD700;
    }
    .partner-registration-modal .partner-step-dot.is-done .partner-step-num {
      border-color: #FFD700;
      color: #FFD700;
      background: transparent;
    }
    .partner-registration-modal .partner-step-dot .partner-step-label {
      display: none;
    }
    @media (min-width: 640px) {
      .partner-registration-modal .partner-step-dot .partner-step-label {
        display: inline;
      }
    }
    .partner-registration-modal .partner-step-line {
      width: 28px;
      height: 1px;
      background: #2a2a2a;
    }
    .partner-registration-modal .partner-columns.partner-wizard {
      grid-template-columns: 1fr;
    }
    .partner-registration-modal .partner-col.partner-step {
      display: none;
    }
    .partner-registration-modal .partner-col.partner-step.is-active {
      display: block;
      animation: partnerModalFadeIn 0.25s ease;
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
    /* Stacks earlier than the extra-row's 900px breakpoint: below ~1024px
       viewport the modal itself isn't wide enough to fit Column 1's
       minmax(430px, ...) track without squeezing, so drop to single-column
       before that point rather than clipping text. */
    @media (max-width: 1024px) {
      .partner-registration-modal .partner-columns {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 768px) {
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

      /* Column 1's paired fields (name/phone, email/company, license
         no/expiry) don't have room for full placeholders at phone widths
         even once the outer 3-column layout has already stacked. */
      .partner-registration-modal .partner-field-row {
        grid-template-columns: 1fr;
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

    /* --- Tom Select Custom Styling for Partner form --- */
    .ts-wrapper.partner-select {
        padding: 0 !important;
        border: none !important;
        background: transparent !important;
    }
    .ts-wrapper.partner-select .ts-control {
        width: 100% !important;
        height: 44px !important;
        background: linear-gradient(145deg, #0e0e0e 0%, #0a0a0a 100%) !important;
        border: 1px solid #222 !important;
        border-radius: 10px !important;
        padding: 0 16px !important;
        color: #fff !important;
        font-family: 'Outfit', sans-serif !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        display: flex !important;
        align-items: center !important;
        cursor: pointer !important;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3) !important;
        transition: all 0.25s ease;
        position: relative !important;
    }
    .ts-wrapper.partner-select.focus .ts-control {
        border-color: #FFD700 !important;
        background: linear-gradient(145deg, #151515 0%, #101010 100%) !important;
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1), inset 0 1px 3px rgba(0, 0, 0, 0.2) !important;
    }
    .ts-wrapper.partner-select .ts-control .item {
        color: #fff !important;
        line-height: 42px !important;
        height: 42px !important;
        display: inline-flex !important;
        align-items: center !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .ts-wrapper.partner-select .ts-control input {
        color: #fff !important;
        font-size: 14px !important;
        font-family: 'Outfit', sans-serif !important;
        padding: 0 !important;
        line-height: 42px !important;
        height: 42px !important;
        margin: 0 !important;
        display: inline-flex !important;
        align-items: center !important;
    }
    html[data-theme="light"] .ts-wrapper.partner-select .ts-control .item {
        color: var(--gt-text) !important;
    }
    .ts-wrapper.partner-select.single .ts-control:after {
        border-color: #FFD700 transparent transparent transparent !important;
        border-width: 6px 5px 0 5px !important;
        right: 20px !important;
    }
    .ts-wrapper.partner-select.single.dropdown-active .ts-control:after {
        border-color: transparent transparent #FFD700 transparent !important;
        border-width: 0 5px 6px 5px !important;
    }
    .ts-dropdown {
        background: #0d0d0d !important;
        border: 1px solid #2a2a2a !important;
        border-radius: 10px !important;
        box-shadow: 0 20px 60px rgba(0,0,0,0.7) !important;
        margin-top: 4px !important;
        z-index: 1000 !important;
        padding: 6px 0 !important;
    }
    .ts-dropdown .option {
        padding: 8px 14px !important;
        color: #ddd !important;
        font-family: 'Outfit', sans-serif !important;
        font-size: 14px !important;
        cursor: pointer !important;
    }
    .ts-dropdown .active,
    .ts-dropdown .option:hover {
        background: rgba(255, 215, 0, 0.12) !important;
        color: #fff !important;
    }
    .ts-dropdown .no-results {
        color: #888 !important;
    }

    /* --- Tom Select Light Mode Theme Overrides --- */
    html[data-theme="light"] .ts-wrapper.partner-select .ts-control {
        background: var(--gt-surface) !important;
        border: 1px solid var(--gt-border-strong) !important;
        color: var(--gt-text) !important;
        box-shadow: none !important;
    }
    html[data-theme="light"] .ts-wrapper.partner-select.focus .ts-control {
        border-color: var(--gt-gold-2) !important;
        background: #ffffff !important;
        box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.18) !important;
    }
    html[data-theme="light"] .ts-wrapper.partner-select .ts-control input {
        color: var(--gt-text) !important;
    }
    html[data-theme="light"] .ts-wrapper.partner-select.single .ts-control:after {
        border-color: var(--gt-gold) transparent transparent transparent !important;
    }
    html[data-theme="light"] .ts-wrapper.partner-select.single.dropdown-active .ts-control:after {
        border-color: transparent transparent var(--gt-gold) transparent !important;
    }
    html[data-theme="light"] .ts-dropdown {
        background: var(--gt-surface) !important;
        border: 1px solid var(--gt-border-strong) !important;
        box-shadow: var(--gt-shadow-lg) !important;
    }
    html[data-theme="light"] .ts-dropdown .option {
        color: var(--gt-text-body) !important;
    }
    html[data-theme="light"] .ts-dropdown .active,
    html[data-theme="light"] .ts-dropdown .option:hover {
        background: var(--gt-gold-soft) !important;
        color: var(--gt-gold) !important;
    }
    html[data-theme="light"] .ts-dropdown .no-results {
        color: var(--gt-text-muted) !important;
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

                  @php
                    $partnerEmirates = \App\Models\Emirates::getActiveEmirates();
                    $partnerCountries = collect(\App\Support\CountryCodes::all())
                      ->reject(fn ($c) => $c['name'] === 'United Arab Emirates')
                      ->values();
                  @endphp

                  <!-- Three sections, shown one at a time via Next/Back — the client
                       asked for this explicitly (2026-08-25 call): fill section 1,
                       click Next, section 2 appears, and so on. -->
                  <div class="partner-step-indicator">
                    <div class="partner-step-dot is-active" data-step-dot="1">
                      <span class="partner-step-num">1</span>
                      <span class="partner-step-label">Company Details</span>
                    </div>
                    <div class="partner-step-line"></div>
                    <div class="partner-step-dot" data-step-dot="2">
                      <span class="partner-step-num">2</span>
                      <span class="partner-step-label">Registering From</span>
                    </div>
                    <div class="partner-step-line"></div>
                    <div class="partner-step-dot" data-step-dot="3">
                      <span class="partner-step-num">3</span>
                      <span class="partner-step-label">Services</span>
                    </div>
                  </div>

                  <div class="partner-columns partner-wizard">

                    <!-- Step 1: Company / Contact Details -->
                    <div class="partner-col partner-step is-active" data-step="1">
                      <div class="partner-col-title">Company / Contact Details</div>

                      <div class="partner-field-row">
                        <div class="partner-form-group">
                          <label for="partnerName">Contact Name</label>
                          <input type="text" id="partnerName" name="name" placeholder="Enter your full name" required>
                          <span class="partner-error-msg" id="partnerName-error"></span>
                        </div>
                        <div class="partner-form-group">
                          <label for="partnerPhone">Contact No.</label>
                          <input type="tel" id="partnerPhone" name="phone" placeholder="+971 50 123 4567" required>
                          <span class="partner-error-msg" id="partnerPhone-error"></span>
                        </div>
                      </div>

                      <div class="partner-field-row">
                        <div class="partner-form-group">
                          <label for="partnerEmail">Email Address</label>
                          <input type="email" id="partnerEmail" name="email" placeholder="your.email@domain.com" required>
                          <span class="partner-error-msg" id="partnerEmail-error"></span>
                        </div>
                        <div class="partner-form-group">
                          <label for="partnerCompanyName">Business Name / Company</label>
                          <input type="text" id="partnerCompanyName" name="company_name" placeholder="Your company name" required>
                          <span class="partner-error-msg" id="partnerCompanyName-error"></span>
                        </div>
                      </div>

                      <div class="partner-field-row">
                        <div class="partner-form-group">
                          <label for="partnerLicenseNumber">Trade License No.</label>
                          <input type="text" id="partnerLicenseNumber" name="trade_license_number" placeholder="e.g. TL-12345" required maxlength="100">
                          <span class="partner-error-msg" id="partnerLicenseNumber-error"></span>
                        </div>
                        <div class="partner-form-group">
                          <label for="partnerLicenseExpiry">Trade License Expiry</label>
                          <input type="date" id="partnerLicenseExpiry" name="trade_license_expiry_date" required>
                          <span class="partner-error-msg" id="partnerLicenseExpiry-error"></span>
                        </div>
                      </div>

                      <div class="partner-form-group">
                        <label for="partnerAddress">Address</label>
                        <input type="text" id="partnerAddress" name="address" placeholder="Their address" required>
                        <span class="partner-error-msg" id="partnerAddress-error"></span>
                      </div>

                      <div class="partner-field-row">
                        <div class="partner-form-group">
                          <label for="partnerDocument">Trade License Document</label>
                          <input type="file" name="trade_license_document" id="partnerDocument"
                            accept=".pdf,.jpg,.jpeg,.png" required>
                          <p class="partner-form-helper-text">PDF, JPG or PNG, up to 5MB.</p>
                          <span class="partner-error-msg" id="partnerDocument-error"></span>
                        </div>
                      </div>
                      <div class="partner-field-row">
                        <div class="partner-form-group">
                          <label for="partnerPassword">Password</label>
                          <input type="password" id="partnerPassword" name="password" placeholder="Create a secure password"
                            required minlength="8">
                          <span class="partner-error-msg" id="partnerPassword-error"></span>
                        </div>
                        <div class="partner-form-group">
                          <label for="partnerPasswordConfirm">Confirm Password</label>
                          <input type="password" id="partnerPasswordConfirm" name="password_confirmation" placeholder="Re-enter your password"
                            required minlength="8">
                          <span class="partner-error-msg" id="partnerPasswordConfirm-error"></span>
                        </div>
                      </div>
                    </div>

                    <!-- Step 2: Registering From -->
                    <div class="partner-col partner-step" data-step="2">
                      <div class="partner-col-title">Registering From</div>

                      <div class="partner-form-group">
                        <label>Registering from UAE?</label>
                        <div class="partner-uae-toggle">
                          <label class="partner-uae-option">
                            <input type="radio" name="registering_from_uae" id="partnerUaeYes" value="1" checked>
                            In UAE
                          </label>
                          <label class="partner-uae-option">
                            <input type="radio" name="registering_from_uae" id="partnerUaeNo" value="0">
                            Outside UAE
                          </label>
                        </div>
                      </div>

                      <div class="partner-form-group" id="partnerEmirateBlock">
                        <label for="partnerEmirateSelect">Emirate</label>
                        <select id="partnerEmirateSelect" name="emirate">
                          <option value="">Select an Emirate...</option>
                          @foreach($partnerEmirates as $e)
                            <option value="{{ $e->emiratesName }}">{{ $e->emiratesName }}</option>
                          @endforeach
                        </select>
                        <span class="partner-error-msg" id="partnerEmirateSelect-error"></span>
                      </div>

                      <div class="partner-form-group" id="partnerCountryBlock" style="display:none;">
                        <label for="partnerCountrySelect">Country</label>
                        <select id="partnerCountrySelect" name="country">
                          <option value="">Select country...</option>
                          @foreach($partnerCountries as $c)
                            <option value="{{ $c['name'] }}" data-iso="{{ strtolower($c['iso']) }}">{{ $c['name'] }}</option>
                          @endforeach
                        </select>
                        <span class="partner-error-msg" id="partnerCountrySelect-error"></span>
                      </div>
                    </div>

                    <!-- Step 3: Services -->
                    <div class="partner-col partner-step" data-step="3">
                      <div class="partner-col-title">Which products or services do you want to sell through us?</div>

                      <div class="partner-services-grid">
                        @foreach(\App\Models\User::AGENT_SERVICES as $key => $label)
                          <div class="partner-service-option">
                            <input type="checkbox" id="partnerService_{{ $key }}" name="services[]" value="{{ $key }}">
                            <label for="partnerService_{{ $key }}">{{ $label }}</label>
                          </div>
                        @endforeach
                      </div>
                      <span class="partner-error-msg" id="partnerServices-error"></span>
                    </div>

                  </div>

                  <!-- Form Buttons -->
                  <div class="partner-form-actions">
                    <button type="button" class="partner-back-btn" id="partnerBackBtn" style="display:none;">Back</button>
                    <button type="button" class="partner-next-btn partner-submit-btn" id="partnerNextBtn">Next</button>
                    <button type="submit" class="partner-submit-btn" id="partnerSubmitBtn" style="display:none;">Create Partner Account</button>
                    <button type="button" class="partner-cancel-btn" id="partnerCancelBtn">Cancel</button>
                  </div>

                </form>
              </div>
            </div>
          </div>

          <div class="container">
            <div class="mt-0 w-100">
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
    (function () {
      'use strict';

      // Partner Registration Modal Functionality
      document.addEventListener('DOMContentLoaded', function () {
        console.log('🚀 Partner registration modal initializing...');

        // Modal Elements
        const partnerModal = document.getElementById('partnerRegistrationModal');
        const partnerRegisterBtn = document.getElementById('partnerRegisterBtn');
        const partnerCloseModal = document.getElementById('partnerCloseModal');
        const partnerCancelBtn = document.getElementById('partnerCancelBtn');
        const partnerRegistrationForm = document.getElementById('partnerRegistrationForm');

        // UAE-vs-Outside toggle — shows the Emirate select or the Country
        // select, never both, mirroring the Agent Registration form.
        const partnerUaeYes = document.getElementById('partnerUaeYes');
        const partnerUaeNo = document.getElementById('partnerUaeNo');
        const partnerEmirateBlock = document.getElementById('partnerEmirateBlock');
        const partnerCountryBlock = document.getElementById('partnerCountryBlock');
        const partnerEmirateSelect = document.getElementById('partnerEmirateSelect');
        const partnerCountrySelect = document.getElementById('partnerCountrySelect');

        // Keeps the phone widget's own dial-code flag (intl-tel-input, set up
        // by partials/intl-tel-init) in step with the Registering From
        // choice — otherwise it silently stays on its 'ae' default and an
        // Outside-UAE number gets saved with the wrong country code.
        function partnerSyncPhoneCountry() {
          const phoneInput = document.getElementById('partnerPhone');
          const iti = phoneInput && phoneInput.__iti;
          if (!iti) return;

          if (partnerUaeYes.checked) {
            iti.setCountry('ae');
            return;
          }
          const selectedOption = partnerCountrySelect.options[partnerCountrySelect.selectedIndex];
          const iso = selectedOption && selectedOption.dataset.iso;
          if (iso) iti.setCountry(iso);
        }

        function partnerSyncLocationFields() {
          const isUae = partnerUaeYes.checked;
          partnerEmirateBlock.style.display = isUae ? '' : 'none';
          partnerCountryBlock.style.display = isUae ? 'none' : '';
          partnerEmirateSelect.required = isUae;
          partnerCountrySelect.required = !isUae;
          partnerSyncPhoneCountry();
        }

        if (partnerUaeYes && partnerUaeNo) {
          partnerUaeYes.addEventListener('change', partnerSyncLocationFields);
          partnerUaeNo.addEventListener('change', partnerSyncLocationFields);
          partnerSyncLocationFields();
        }
        if (partnerCountrySelect) {
          partnerCountrySelect.addEventListener('change', partnerSyncPhoneCountry);
        }

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
            } else if (value.length < 10) {
              if (errorElement) {
                errorElement.textContent = 'Phone number must be at least 10 digits';
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
          partnerGoToStep(1);
        });

        // Close Modal Functions
        function partnerCloseModalFunction() {
          console.log('❌ Closing partner modal...');
          partnerModal.style.display = 'none';
          document.body.style.overflow = 'auto';
          partnerClearErrors();
          partnerRegistrationForm.reset();
          partnerGoToStep(1);
        }

        partnerCloseModal.addEventListener('click', partnerCloseModalFunction);
        partnerCancelBtn.addEventListener('click', partnerCloseModalFunction);

        // Close modal when clicking outside
        window.addEventListener('click', function (event) {
          if (event.target === partnerModal) {
            partnerCloseModalFunction();
          }
        });

        // --- Step wizard: 3 sections, one visible at a time ---
        const partnerSteps = Array.from(document.querySelectorAll('#partnerRegistrationModal .partner-step'));
        const partnerStepDots = Array.from(document.querySelectorAll('#partnerRegistrationModal .partner-step-dot'));
        const partnerBackBtn = document.getElementById('partnerBackBtn');
        const partnerNextBtn = document.getElementById('partnerNextBtn');
        const partnerSubmitBtn = document.getElementById('partnerSubmitBtn');
        let partnerCurrentStep = 1;
        const partnerTotalSteps = partnerSteps.length;

        function partnerStepIsValid(stepEl) {
          const fields = stepEl.querySelectorAll('input, select, textarea');
          let valid = true;
          fields.forEach(field => {
            if (field.type === 'radio') return; // radios are always one-of, no reportValidity spam
            if (field.offsetParent === null) return; // skip hidden fields (e.g. the country select while "In UAE")
            if (!field.checkValidity()) {
              valid = false;
              field.reportValidity();
            }
          });
          if (stepEl.dataset.step === '3') {
            const anyService = stepEl.querySelectorAll('input[name="services[]"]:checked').length > 0;
            if (!anyService) {
              document.getElementById('partnerServices-error').textContent = 'Select at least one product/service';
              valid = false;
            }
          }
          return valid;
        }

        function partnerGoToStep(step) {
          partnerCurrentStep = step;
          partnerSteps.forEach(el => el.classList.toggle('is-active', Number(el.dataset.step) === step));
          partnerStepDots.forEach(dot => {
            const dotStep = Number(dot.dataset.stepDot);
            dot.classList.toggle('is-active', dotStep === step);
            dot.classList.toggle('is-done', dotStep < step);
          });
          partnerBackBtn.style.display = step > 1 ? '' : 'none';
          partnerNextBtn.style.display = step < partnerTotalSteps ? '' : 'none';
          partnerSubmitBtn.style.display = step === partnerTotalSteps ? '' : 'none';
        }

        partnerNextBtn.addEventListener('click', function () {
          const stepEl = partnerSteps.find(el => Number(el.dataset.step) === partnerCurrentStep);
          if (stepEl && !partnerStepIsValid(stepEl)) return;
          if (partnerCurrentStep < partnerTotalSteps) partnerGoToStep(partnerCurrentStep + 1);
        });

        partnerBackBtn.addEventListener('click', function () {
          if (partnerCurrentStep > 1) partnerGoToStep(partnerCurrentStep - 1);
        });

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
            'password_confirmation': 'partnerPasswordConfirm',
            'company_name': 'partnerCompanyName',
            'address': 'partnerAddress',
            'trade_license_number': 'partnerLicenseNumber',
            'trade_license_expiry_date': 'partnerLicenseExpiry',
            'emirate': 'partnerEmirateSelect',
            'country': 'partnerCountrySelect',
            'registering_from_uae': 'partnerEmirateSelect',
            'services': 'partnerServices',
            'trade_license_document': 'partnerDocument'
          };

          let firstErrorStep = null;
          Object.keys(errors).forEach(field => {
            // Laravel keys array-item errors as "services.0" — strip the
            // index so it still matches fieldMapping's plain 'services'.
            const baseField = field.replace(/\.\d+$/, '');
            const mappedField = fieldMapping[baseField] || fieldMapping[field] || field;
            const errorElement = document.getElementById(mappedField + '-error');
            if (errorElement && errors[field][0]) {
              errorElement.textContent = errors[field][0];
              const stepEl = errorElement.closest('.partner-step');
              const stepNum = stepEl ? Number(stepEl.dataset.step) : null;
              if (stepNum && (firstErrorStep === null || stepNum < firstErrorStep)) firstErrorStep = stepNum;
            }
          });

          // A server-side error can land on a field the applicant already
          // stepped past (e.g. the email was taken by someone else since
          // step 1) — jump back to whichever step actually has the error
          // rather than leaving it silently hidden on step 3.
          if (firstErrorStep !== null) partnerGoToStep(firstErrorStep);
        }

        // Form submission with AJAX
        partnerRegistrationForm.addEventListener('submit', function (e) {
          e.preventDefault();
          console.log('📤 Partner form submitted');

          const submitBtn = partnerSubmitBtn;

          // Client-side nicety: require at least one service before even
          // hitting the server (which also enforces this).
          const selectedServices = document.querySelectorAll('input[name="services[]"]:checked');
          if (selectedServices.length === 0) {
            document.getElementById('partnerServices-error').textContent = 'Select at least one product/service';
            return;
          }

          const formData = new FormData(partnerRegistrationForm);

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

          // AJAX request — same self-service application pipeline as
          // /agent/register (agent_applications → manager review under
          // "Agent Applications" → approval activates the real account),
          // so this is no longer instant: it goes to a manager for review.
          fetch('{{ route('agent.register.submit') }}', {
            method: 'POST',
            body: formData,
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json'
            }
          })
            .then(response => {
              console.log('📡 Partner registration response status:', response.status);
              return response.json();
            })
            .then(data => {
              console.log('📋 Partner registration response data:', data);

              if (data.success) {
                alert('🎉 Application received! Our team will review your trade license and be in touch once it\'s approved.');
                partnerCloseModalFunction();
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