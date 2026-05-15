# White-Label Platform Audit

Audit of super-admin → tenant → main-site flow. Findings only (no fixes).
File:line references where they matter.

---

## 1. Architecture confusion (the big problem)

There are **5 admin-shaped panels**, each with its own auth model. None of them is the "tenant dashboard" end-to-end:

| Panel             | Path                | Auth                        | Purpose (intended)                |
|-------------------|---------------------|-----------------------------|-----------------------------------|
| Super admin       | `/superadmin`       | Laravel auth + `super.admin`| Platform owner, manage tenants    |
| Legacy admin      | `/admin`            | Laravel auth + `isAdmin`    | Global CMS (activities, ads, users)|
| Manager           | `/manager`          | **session-only** (`manager.auth`) | Tenant content + finance      |
| Client            | `/client`           | Laravel auth + `company.admin`    | Tenant data dashboards         |
| Referral / Partner| `/partner`          | `referral.agent`            | Affiliate agents                  |

Files: `routes/web.php:60,112,277,281,375,409`, `routes/superadmin.php:24`, `routes/client.php:13`.

- **A new white-label tenant has no clear "dashboard."** They can log in to `/manager` (session) and to `/client` (real auth) — both partially built, neither shows the same data.
- **Super-admin "Impersonate" lands on `/admin`** (`SuperAdmin/CompanyController.php:269`), the legacy CMS — not the tenant's manager or client dashboard. So the super admin sees global content tools, not the tenant's view.
- **Manager auth is session-flag only** (`Middleware/ManagerAuthMiddleware.php:12`). `auth()->user()` is `null` inside `/manager/*`, which silently breaks `CompanyScope` super-admin bypass and any controller that calls `auth()->id()`.

---

## 2. Login flows

1. **Manager login form field is `name="username"`** (`Manager/ManagerAuthController.php:30`) but the label was renamed "Email" (commit `fa4ffab`). Only the label changed; the request key + validation still says `username`.
2. **A tenant manager cannot log in from `gotrips.ai/manager/login`** — `IdentifyTenant` resolves the host to the default `gotrips` company, so the tenant-scope check (`ManagerAuthController.php:55-61`) rejects them with "this account does not belong to gotrips.ai". They must visit `<sub>.gotrips.ai/manager/login`. No redirect or hint.
3. **`/client/*` has no login route of its own.** The middleware redirects unauthenticated users to `route('login')` (`CompanyAdminMiddleware.php:14`), which is the global Laravel login — completely separate from `/manager/login`. Two parallel auth experiences for the same tenant person.
4. **Super-admin login + role check is fine** (`Middleware/SuperAdminMiddleware.php:18`).
5. **Session `company_id` is never cleared on logout** (Manager / Client / Auth logout). After impersonation or a tenant subdomain visit, the cached `company_id` (`IdentifyTenant.php:77-82`) sticks across navigation and can mis-bind the tenant.

---

## 3. Subdomain routing & branding (`fortune.gotrips.ai`)

6. **Manager sidebar logo is hardcoded GoTrips** (`resources/views/layouts/manager.blade.php:377`) — `assets/index_files/logo.png`. Brand text "Go Trips" also hardcoded (`:378`). Tenant logged in to their own subdomain still sees GoTrips branding inside the panel.
7. **Manager sidebar has no `@feature(...)` gates.** All menu items (Ad Slots, Announcements, Activities, Finance, Bookings, Bank, Withdrawals, Settings) appear regardless of which services the super admin granted (`layouts/manager.blade.php:393-443`).
8. **Contact-us Google Maps iframe is hardcoded to "Ayn Al Amir Tourism"** location (`resources/views/contact-us.blade.php:459`). Even on a tenant subdomain that has its own `address`, the embedded map shows GoTrips' building.
9. **Two different "default logo" fallbacks**: `Company::getLogoUrlAttribute` returns `images/default-logo.png` (`Models/Company.php:168`); `header.blade.php:5` returns `assets/index_files/logo.png`. The first path likely doesn't exist.
10. **`IdentifyTenant.php:77-82` checks session BEFORE host.** If the session cookie is `.gotrips.ai`-scoped, opening tenant A's dashboard then visiting tenant B's subdomain still resolves to A until the session clears.
11. **`@feature('esim')` is missing from header/footer nav.** The `/esim` route IS gated (`web.php:261`), but the link in nav has no wrapper — any tenant without eSIM still sees the link, gets a 404 on click.

---

## 4. Service-access (which tenant can use what)

12. **`Company::hasFeature` returns TRUE if `features` is empty** (`Models/Company.php:225`). On the create-company form (`SuperAdmin/CompanyController.php:90-92`), if no checkboxes are ticked, code defaults to **all features**. So "leaving everything unchecked" silently grants full access — the opposite of what the UI implies.
13. **`ManagerSettingsController::updateFeatures` lets a tenant edit their own `features` array** (`routes/web.php:288`). A tenant can self-grant any service the super admin withheld.
14. **`UAEActivity`, `Announcement`, `Emirates`, `UAEVisaMaster` do NOT use `BelongsToCompany`.** They're global tables.
   - `ManagerActivitiesController::index/store/destroy` operates on the global activities table (`Manager/ManagerActivitiesController.php:20,81,164`). Tenant A's manager edits the same row tenant B sees.
   - Same for `ManagerAnnouncementsController` (`:13,31`) and ad slots (`HomepageAd` does have the trait, so ads ARE tenant-scoped).
15. **Public-page service gating only blocks the route, not the nav link** for some services (point 11 above). Where `@feature(...)` is used (header lines 1043-1112, footer 179-193) it is only for activities/visas/tours/shop/careers/pay_online/hajj_umrah — eSIM is missing.

---

## 5. Manager dashboard "metrics"

16. **The manager dashboard shows zero financial metrics.** It only shows three content widgets: ad slot count, announcement count, activity count (`resources/views/manager/dashboard.blade.php:13-90`). No revenue, no bookings, no commission, no withdrawal balance, no recent customers. Finance is sidebar-only.
17. **Two parallel commission readouts disagree.** `ClientDashboardController.php:62` shows `ReferralTracking::where('status','pending')->sum('commission_amount')`. `ManagerFinanceController.php:33` shows `TenantCommission::where('status','available')`. These are different tables, different numbers, different states. A tenant looking at `/client` and `/manager/finance` will see two different "pending commissions."
18. **`ClientDashboardController` queries only work because of `BelongsToCompany` global scope** — the controller itself never explicitly filters by `company_id` (`Client/ClientDashboardController.php:47-77`). For models without the trait (`UAEVApplication` — wait, it has it; `NomodTransaction` — has it; `EsimOrder` — has it), it works; but anywhere `auth()->user()` is null (manager session auth), the scope's super-admin check (`Scopes/CompanyScope.php:18-22`) is unreliable.

---

## 6. Payment / commission flow

19. **Commission is recorded for activity bookings ONLY.** `CommissionService::record()` is invoked from exactly one place: `ActivityBookingController.php:231`. eSIM purchases (`EsimController.php:338`), visa applications, hotel/flight bookings (`NomodController`) generate **no commission**. The "commission-based revenue model" only works for activities right now.
20. **Commission is recorded BEFORE payment is confirmed.** `ActivityBookingController.php:224-237` saves the booking + records the commission entry, then proceeds to email/initiate payment. There is no reversal hook on payment failure or cancellation. Failed bookings inflate `total_commission` and `pending_commission`.
21. **`pending → available` requires the super admin to click "Release Commissions" manually** (`SuperAdmin/WithdrawalController.php:101-111`). Otherwise commissions stay stuck in `pending` forever. There is no scheduled job, no auto-release on payment confirmation.
22. **`requestWithdrawal` does not deduct from available balance** (`Manager/ManagerFinanceController.php:171`). Validation only checks `amount <= available` (`:159`), so a tenant can submit two simultaneous withdrawals each equal to the full available balance — both pass validation.
23. **Withdrawal "approve" does nothing financial** (`SuperAdmin/WithdrawalController.php:34`) — only flips status to `approved`. No state change to the underlying commissions, no decrement of pending balance. The actual commission settlement happens only on `markPaid`.
24. **Rejected withdrawals do not return commissions to `available`** (`SuperAdmin/WithdrawalController.php:38-48`). Status flips to `rejected` and that's it. If commissions were already linked, they may stay linked to a dead withdrawal.
25. **`markPaid` decrements `pending_commission` by withdrawal amount** (`SuperAdmin/WithdrawalController.php:86`). But `pending_commission` was never incremented by the withdrawal — it tracks unpaid commissions. This conflates two ledgers and will produce negative-or-zero values that the controller then clamps (`:87-89`).
26. **`Company::pending_commission` ≠ `TenantCommission::status='pending'`.** The aggregate column (`migrations/...add_commission_to_companies.php`) is incremented when a commission row is created (`CommissionService.php:66`) and decremented on paid withdrawal — it does not track the actual `pending` row count after release. They drift.
27. **Three different withdrawal systems coexist**: `TenantWithdrawal` (super admin), `ReferralWithdrawalRequest` (admin/referrals), agent-side via `ReferralWithdrawalController`. State machines disagree (`approved`/`processing`/`paid`/`complete`). Easy to confuse one for the other in the UI.

---

## 7. Customer-facing inbox / lead routing

28. **Contact-us submissions never reach the tenant.** `ContactController::submit` sends to `config('mail.mailers.smtp.address')`, falling back to `amer@aynalamirtourism.com` (`ContactController.php:35,45`). It does not look up `app('current_company')->email`. A lead from `fortune.gotrips.ai/contact-us` goes to GoTrips, not Fortune.
29. **Activity booking confirmations also fixed-recipient.** `ActivityBookingController.php:258-272` uses `config('mail.from.address')` for the admin recipient, ignoring the tenant. Tenant manager cannot see their own bookings via email.
30. **Job application + visa submission emails** are not tenant-aware either (`JobApplicationController`, `UAEVisaController.php:119`).

---

## 8. Super-admin company creation form gaps

31. **Form does not surface "what features = no access vs all access"** — given finding #12, the create form is misleading.
32. **Hostinger subdomain auto-provision swallows errors** (`SuperAdmin/CompanyController.php:130-132`). The company gets created with a subdomain on file, but the DNS / vhost may not exist; visiting `<sub>.gotrips.ai` 404s at the server, not at Laravel.
33. **No way for the super admin to set the tenant's contact email/phone/address from the create form.** Only `email` (the company's primary contact) is collected (`:67`). Address/phone are editable only on the edit screen. Until then, contact-us shows nothing on the tenant's site (good, but no warning).
34. **`is_super_admin` flag is never set when creating tenant admin** (`:116-122`). User created with role `company_owner`. That's correct, but the form has no toggle to make a tenant user a super admin if needed.
35. **Toggle-status / extend-subscription / change-plan have no audit trail.**

---

## 9. Test scenarios the codebase fails (the "20+ ways" you asked for)

Each line is "what should happen" → "what actually happens."

1. SA creates tenant `fortune` with **only** `visas` checked → tenant should see only Visa nav. **Pass** for routes; **fails** for sidebar (#7) and eSIM nav link (#11).
2. SA creates tenant with **no features ticked** → tenant should see no services. **Fails**: gets full access (#12).
3. Tenant manager logs into `gotrips.ai/manager/login` with their own credentials → should redirect to their subdomain. **Fails**: rejected with "doesn't belong to gotrips.ai" (#2).
4. Tenant logs in at `<sub>.gotrips.ai/manager/login` → sees branded panel. **Fails**: GoTrips logo + "Go Trips" name in sidebar (#6).
5. Tenant edits their own features in `/manager/settings/features` → should be read-only. **Fails**: full edit access, can self-grant services (#13).
6. Customer visits tenant subdomain home → header logo, footer name, contact info should be tenant's. **Mostly pass** (recent commits 6b42fe1, 85c2356) **except** the contact-us Google Map (#8) and default-logo path inconsistency (#9).
7. Customer fills contact-us on tenant subdomain → lead emails the tenant manager. **Fails**: emails GoTrips (#28).
8. Customer books an activity on tenant subdomain → booking confirmation goes to tenant + customer. **Fails**: only customer + GoTrips (#29).
9. Customer pays for an activity on tenant subdomain → commission row created `pending`. **Pass** but commission is recorded **before** the payment outcome (#20).
10. Activity payment fails / is canceled → commission row reversed. **Fails**: no reversal logic (#20).
11. Commission stays `pending` for 24h → tenant should be able to withdraw. **Fails**: only super admin's manual "Release" button does this (#21).
12. Tenant submits a withdrawal of full available balance → SA approves → SA marks paid → commission ledger settled. **Pass** when followed in this exact order; (#22-26 list partial-failure modes).
13. Tenant submits two simultaneous withdrawals each = full balance → second should reject. **Fails**: both pass validation (#22).
14. SA rejects a withdrawal → commissions return to `available`. **Fails**: stays as-is (#24).
15. Tenant ships an eSIM order → commission credited. **Fails**: only activity bookings credit commissions (#19). Same for visas, hotels, flights.
16. Tenant manager dashboard shows revenue/orders/commission. **Fails**: only ad-slot/announcement/activity counts (#16).
17. Tenant logs into `/client` and `/manager` → sees the same numbers. **Fails**: different tables (#17).
18. SA "Impersonate" tenant → lands on tenant's dashboard. **Fails**: lands on `/admin` legacy CMS (#1, #1 row 4).
19. SA visits `/superadmin/withdrawals` → sees totals across all tenants. **Pass**, but `all_time_commission` includes the gotrips main company.
20. SA creates tenant with auto-provision Hostinger subdomain → DNS + vhost active. **Untestable here**, error path silently swallowed (#32).
21. Tenant manager from company A logs out and a customer hits company B's subdomain on the same browser → site resolves to B. **Fails**: session cache may keep A (#10).
22. Tenant manager edits an "Activity" in `/manager/activities` → it should affect only their tenant. **Fails**: edits global table (#14).
23. Tenant manager creates an "Announcement" → it shows on their subdomain only. **Fails**: global table (#14).
24. Job application form submitted on tenant site → mailed to tenant. **Fails**: routed to GoTrips (#30).
25. SA deactivates a tenant → tenant subdomain returns 403. **Pass** (`IdentifyTenant.php:60`).
26. SA changes tenant's `commission_value` → next booking uses new rate. **Pass** (`CommissionService.php:14-21`).

---

## 10. Severity summary

**Blocking the white-label promise:**
- #1, #6, #7, #14, #19, #28 — branding leaks + content cross-contamination + commissions only work for one service.

**Financial correctness:**
- #20, #22, #24, #25, #26, #17 — commission can be over-counted, never reversed, and two systems show different totals.

**UX / login confusion:**
- #2, #3, #5, #16, #1 (impersonate target).

**Security:**
- #13 (self-elevation), #10 (cross-tenant session bleed), #34.

**Cosmetic / minor:**
- #9, #11, #28's fallback hardcoded fallback email, #15 (`@feature('esim')` missing).
