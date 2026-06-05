# GoTrips — Work Summary

Covers the fixes and features delivered in response to the May 29 meeting, plus
environment/deployment issues found along the way.

---

## 1. Environment & setup
- Resolved "not getting latest code": the repo was fine — the problem was **6+
  duplicate project folders** and **14 stale `php artisan serve` processes**
  serving an old folder. Consolidated to a single working folder and one server.
- Fixed the live deployment: the production server was stuck several commits
  behind because uncommitted edits on the server blocked every auto-deploy. Backed
  up and reset the server to match `main`.

## 2. Meeting bug fixes — DONE & LIVE (PR #24)

### Payment gateway "Server error" (critical)
- Cause: customer phone not in valid E.164; the Pay Online form **doubled the
  country code** because a phone widget conflicted with a manual dropdown. Nomod
  rejected every payment with `customer_invalid_phone_number`.
- Fix: E.164 normalization in `app/Services/NomodService.php`; de-doubling logic
  and widget opt-out in `resources/views/payonline.blade.php`.

### Contact Us missing map
- Added a head-office map fallback for the main site —
  `resources/views/contact-us.blade.php`.

### Font sizes (Careers + Pay Online)
- Increased sub-readable font sizes — `resources/views/lookingforajob.blade.php`,
  `resources/views/payonline.blade.php`.

### Tour Packages "flags disappearing" bug
- Adding one package hid all other countries. Now shows all countries, completed
  first (clickable), pending as "Coming Soon" — `routes/web.php`,
  `resources/views/tour-packages.blade.php`.

## 3. New work — BUILT & verified locally

### eSIM "Buy" button fix
- Same phone-doubling bug on the eSIM form. Fixed in
  `resources/views/esim.blade.php`.

### Tour Packages full redesign
- Database: `database/migrations/2026_06_05_000001_redesign_tbl_travel_packages.php`
  adds `package_type`, partner contacts, adult/child/infant pricing, and a new
  `tbl_travel_package_images` table. Models: `app/Models/TravelPackage.php`,
  `app/Models/TravelPackageImage.php`.
- Admin forms (both panels): rich-text editor, per-person pricing, partner
  email/WhatsApp, Enquire/Purchase type, multi-image gallery.
  - `app/Http/Controllers/Admin/TravelPackageController.php` + `resources/views/admin/packages/*`
  - `app/Http/Controllers/Manager/ManagerTravelPackagesController.php` + `resources/views/manager/packages/*`
- Public detail page: `resources/views/tour-package-detail.blade.php` — image
  gallery, formatted description, Enquire (WhatsApp/email) vs Purchase (live price
  calculator → Nomod checkout).

### NomodService hardening
- Truncate item name to Nomod's 100-character limit (`app/Services/NomodService.php`).

## 4. Still open
- eSIM USD->AED pricing bug (eSIM plans priced in USD but charged as AED — selling
  at a loss). Parked.
- e-Visa API integration — blocked on portal login + docs.
- laurelturk.com activities page — blocked on tenant data.
