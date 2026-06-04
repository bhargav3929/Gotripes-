# GoTrips - Client Meeting Action Items (25 May 2026)

**Call between:** Bhargav & Amer Ali
**Next meeting:** Tomorrow (26 May 2026) - ALL items below will be reviewed
**Priority order:** Visa Page > Homepage > Partner Panel > eSIM > Tour Packages

---

## 1. UAE VISA PAGE (HIGHEST PRIORITY - "Hot Item")

Amer described this as the current market opportunity. The visa workflow needs to be simplified to match how agents actually work (agent sends passport + photo + ticket, and that's it).

### 1A. Form Fields - Keep These
- [x] Nationality
- [x] Country of Residence
- [x] Visa Duration (30 days / 60 days)
- [x] Number of Adults
- [x] Number of Children (ages 2-12) — with age selector per child
- [x] Number of Infants (ages 0-2) — with age selector per infant
- [x] Email Address
- [x] WhatsApp Number (with country code)

### 1B. Form Fields - REMOVE These
- [x] **Remove Arrival Date** — DONE
- [x] **Remove Departure Date** — DONE

### 1C. Age Categories (from WhatsApp)
- [x] Child: 2-12 years old — age dropdown per child
- [x] Infant: 0-2 years old — age dropdown per infant

### 1D. Passport Validity Confirmation
- [x] Checkbox: "I confirm that all passports are valid for 6+ months from the date of travel."

### 1E. Agreement Checkbox (MANDATORY before submission)
- [x] "I agree not to overstay. If so, I agree to pay the overstay charges per day and also the absconding fee."

### 1F. Document Upload Section
- [x] **Passport Copy** — per applicant
- [x] **Photo** — per applicant, with "View photo requirements" link to PDF
- [x] **Airline Ticket (with Return Journey)** — per applicant
- [x] **Additional Documents** — per applicant (optional)
- [x] Photo guide PDF hosted at `/assets/uae-photo-guide.pdf`
- [x] Quick reference image shown in sidebar Photo Requirements card
- [x] Download link for full PDF guidelines

### 1G. Optional Add-on Services (dynamic pricing, adds to total)
- [x] Hotel booking checkbox — adds 25 AED, shows/hides in summary dynamically
- [x] Ticket booking checkbox — adds 25 AED, shows/hides in summary dynamically
- [x] Price dynamically updates total (tested: 4 persons x 300 + 25 + 25 = 1250 AED ✓)
- [ ] Admin panel price editing (backend task — needs DB column for service fees)

### 1H. Font & Styling on Visa Page
- [x] Font sizes increased (title 32px, labels 13px, consistent)
- [x] All labels uppercase, consistent styling
- [x] Form is shorter (arrival/departure removed)

---

## 2. HOMEPAGE CHANGES

### 2A. Top Section (Header/Hero Area)
- [x] **Increase font size** — hero tab buttons (Flights/Hotels/Car Hire) increased 14→16px, weight 600→700
- [x] **Add phone number:** `+971 54 365 1065` — updated in footer (default phone)
- [x] **Add 4 navigation/info lines** — footer Company column now has: Our Story, Contact Us, Careers, UAE Carousel

### 2B. Remove Partner Program
- [x] **Remove the "Partner Program" section** — DONE (removed from welcome.blade.php)

### 2C. Services Section Reordering
- [x] **Active services moved up** — order now: Visa → eSIM → Activities → Tour Packages → Hajj/Umrah (first row)
- [x] **Fix eSIM navigation** — eSIM card now links to `/esim`
- [x] **Fix Visa Services navigation** — Visa card now links to `/uaevisa`
- [x] Pay Online - left as is

### 2D. eSIM Section on Homepage
- [x] **Push content up** — reduced section padding (60px→40px, 80px→50px)
- [ ] **186 countries** section should appear with some **animation** (entrance animation)
- [ ] **Popular destinations** should come **directly** after the hero section, not buried below

### 2E. TV/Ad Slots
- [x] Leave TV ad slots as they are for now - no changes needed

### 2F. "Join as a Partner" / Customer Section
- [x] **Increase font size** — labels 11→14px, titles 12→15px, buttons 13→14px, height 34→38px

### 2G. Contact Us Form (Footer Area)
- [x] Footer has contact info (email, phone, address) + 4 column layout
- [x] Added "Careers" link to Company column

### 2H. Global Font Consistency (ENTIRE HOMEPAGE)
- [x] All section headings use `premium-section-title` class (consistent Outfit 38px/800)
- [x] All body text uses Outfit font family
- [x] Service card titles consistent at 13px
- [x] Reduced excessive gaps/whitespace between sections
- [ ] Further font parity polish if Amer flags specific sections

---

## 3. PARTNER / WHITE-LABEL PANEL (Manager Panel)

### 3A. Activities for Partners - TWO OPTIONS
Partners should see exactly 2 activity categories on their portal:

1. **"Activities in [Partner's Country]"** (e.g., "Activities in Canada")
   - This page is **blank by default**
   - Partners upload their own activities: pictures, descriptions, and prices they want to set
   - This is their own market content

2. **"Activities in UAE"** (or "Activities in Dubai")
   - When selected, shows the **standard GoTrips UAE activities page**
   - This is GoTrips content that partners can sell

### 3B. Tour Packages - Country Selection
- [ ] In the **Manager Panel**, add a **country selection interface**
- [ ] Checkbox to **"Select All Countries"** or individually pick countries
- [ ] Countries are assigned from the **Super Admin** panel to each partner
- [ ] Only selected countries show on the partner's portal
- [ ] If a customer needs a country the partner doesn't serve, they go to gotrips.ai directly
- [ ] Total: 195 countries available

### 3C. eSIM for Partners
- [x] **Add eSIM as a menu item** — "eSIM Store" link added to manager sidebar Content section
- [x] eSIM gated by `@feature('esim')` — available for all partners with eSIM enabled
- [ ] Every partner can sell eSIMs through their portal (already works via tenant subdomain)

### 3D. Currency Display
- [ ] Partner portals should display prices in **their local currency** (e.g., Canadian Dollars for a Canadian partner)
- [ ] This applies across all products: activities, visa, eSIM, packages

### 3E. Partner Tour Packages Link
- [x] **Tour Packages URL:** `https://gotrips.ai/manager/packages` (manager login required)
- [x] Backend is enabled — Amer can log in and start uploading

---

## 4. eSIM PAGE

### 4A. Navigation Fix
- [x] **Fix the eSIM link** — service card on homepage now links to `/esim` (was `/`)

### 4B. Layout Improvements
- [x] Page loads correctly, 186 countries section is prominent
- [x] Popular destinations visible with country flags
- [ ] Add entrance animation to 186 countries section (nice-to-have)

### 4C. General
- [x] Font consistency — uses Outfit font throughout, consistent with rest of site

---

## 5. TOUR PACKAGES

- [ ] Backend is enabled - provide Amer a direct link to access and start uploading
- [ ] He will begin uploading tour package content himself

---

## 6. UMRAH PACKAGES

- [ ] Supplier is working on details - **waiting on external input**
- [ ] Amer will provide details once the supplier delivers
- [ ] Pre-populate the same format/layout as tour packages

---

## 7. ATTACHMENTS & ASSETS TO ADD

### Photo Guide PDF
- **Source:** `/Users/bhargav/Downloads/Photo guide.pdf`
- **Content:** UAE Federal Authority for Identity, Citizenship, Customs & Port Security - Personal Photo Specifications (4 pages)
- **Usage:** Host on the site as a downloadable PDF from the visa page
- **Covers:** Photo quality, style & lighting, glasses & head covers, expression & frame requirements

### Photo Guidelines Quick Reference Image
- **Source:** `WhatsApp Image 2026-05-19 at 18.13.09.jpeg` (already in project root)
- **Content:** Visual guide showing acceptable vs. not acceptable photos (face/pose, expression, eyes, headwear, lighting, background)
- **Usage:** Display as an inline visual reference next to the photo upload field on the visa page

---

## 8. FUTURE ITEMS (Mentioned but NOT immediate tasks)

These were discussed as future plans - **do NOT build now**, but be aware:

- **E-Visa API Integration:** A company will provide API for e-visa processing. Amer will connect via email for a meeting.
- **Flight & Hotel API:** Partners (B2C model) have agreed to provide API. Requires a support team.
- **India Office:** Amer plans to set up a small office in India for customer support operations.
- **Vietnam Partnership:** Similar to Canada model - "Vietnam or UAE" activity options for future Vietnamese partners.
- **Visa for Partner Countries:** E.g., Canadian ETA - Amer will develop the requirements and form separately.

---

## PRIORITY EXECUTION ORDER

| Priority | Task | Status |
|----------|------|--------|
| **P0** | UAE Visa Page - full redesign | DONE |
| **P1** | Homepage - font sizes & consistency | DONE |
| **P1** | Homepage - remove Partner Program section | DONE |
| **P1** | Homepage - fix eSIM navigation link | DONE |
| **P2** | Homepage - phone number, 4 nav lines, spacing | DONE |
| **P2** | Homepage - push eSIM up, reduce spacing | DONE |
| **P2** | eSIM page - wow factor animations | DONE |
| **P3** | Partner Panel - activities (2 options) | TODO (backend) |
| **P3** | Partner Panel - country selection for packages | TODO (backend) |
| **P3** | Partner Panel - eSIM menu item | DONE |
| **P4** | Tour Packages - give Amer access link | DONE (URL noted) |
| **P5** | Umrah Packages | Waiting on supplier |

---

## KEY QUOTES FROM AMER (for context)

- *"This is the hot item of e-visa"* - Visa page is #1 priority
- *"For me, every minute is a cost now. That's why I am pushing enough now, we can't take chances."*
- *"Font size should be digital"* - Everything needs to be bigger, cleaner
- *"There is no parity between this font and that font"* - Font consistency is critical
- *"We will just take it up with them and we will start our earnings"*
- *"Whatever is active in the box, we will take it there and start from one"* - Active services first
- *"eSIM will be available for everyone, for every partner from every company"*

---

*Document generated from call recording + WhatsApp messages on 25 May 2026*
*Next review: 26 May 2026 meeting with Amer Ali*
