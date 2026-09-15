# Client feedback — WhatsApp, 15 Sep 2026 (AMER, +971 50 557 4373)

Verbatim transcription of two WhatsApp screenshots. Amer replied to Bhargav's
"micro report" of the last delivery round by quoting each report item and adding
his comments **in bold**. Non-bold text below is our own report text that he
quoted back; bold text is Amer's comment on that item.

Typos are preserved as written. Our interpretation is in the "Action items"
section at the bottom, not in the transcription.

---

## Screenshot 1

> @Bhargav , Thanks for Micro report and work done is massive - much appreciated
>
> Adding few comments by breaking down and highlighting them n bold

### 1. Customer support ticket system — Completed and deployed  (12:19 PM)

Quoted report text:
- Added ticket creation, tracking, customer replies, unique ticket IDs, acknowledgements, email notifications, assignment, status management, and response-time reporting.
- Manager customer-care dashboard — Completed and deployed
  - Added ticket queue, ticket details, replies, assignments, SLA tracking, overdue indicators, and dashboard statistics.

Amer's comment:
> **This is very essential item deployed - Can we do demo next week in our weekly meeting and see the workflow**

### 2. Tawk chatbot replacement — Completed and deployed  (Edited 12:21 PM)

Quoted report text:
- Removed Tawk and replaced it with the GoTrips-owned support widget.

Amer's comment:
> **This Widget should definitely give us a branded look- will test- looks vry professional**
>
> Will login a ticket and then bring doubts on work flow

### 3. WhatsApp support integration — Code completed; credentials pending  (12:23 PM)

Quoted report text:
- Integration is ready but disabled until WhatsApp Business API credentials are provided.

Amer's comment:
> **Will discuss on this.cost , we do this once our products are ready like Esim – 4 more products**

### 4. UAE visa Emirate availability — Completed and deployed  (12:24 PM)

Quoted report text:
- Enabled packages for every active Emirate while preserving the special Sharjah deposit behavior.

Amer's comment:
> **Supplier's card blocked, we are bit late here, trying to resource new suppliers so that we test after getting rateds**

Quoted report text (same message):
- UAE visa selection popup — Completed and deployed

---

## Screenshot 2

### 5. UAE visa selection popup / route selector (continued)  (Edited 12:25 PM)

Quoted report text:
- Rebuilt the selector with the requested circular black-and-gold design and company branding.
  - Updated iPhone, Android, same-phone QR, compatibility, roaming, and troubleshooting instructions.

Amer's comment:
> **This is very beautiful Ciruclar design thickness should be increased for golden border not matching with our Logo golden border thickness**
>
> If we do same for customer registration pop window also as circular for all registrations forms

### 6. eSIM email instructions — Completed and deployed  (12:28 PM)

Quoted report text:
- Updated installation steps and added a link to the installation video.
- Created an eight-scene, narrated and captioned 78-second Full HD video with background music.

Amer's comment:
> **Can we have this 9:11 screen size too and log of our company in beginning of video and ending.**

### 7. Turkey eSIM confirmed active and used  (12:29 PM)

Quoted report text:
- Wallet balance confirmed as USD 8.66.
- Client support proposal — Draft completed; not sent
  - Prepared the implementation proposal and client-facing message.

Amer's comment:
> **Sure, at your pace – not in rush**

### 8. Customer-care handover document — Completed  (12:30 PM)

Quoted report text:
- Documented support-team operations and configuration requirements.
- Database migration — Completed on production
  - Created and migrated the support-ticket and ticket-message tables.

Amer's comment:
> **Whre is the ticket hitting? Email, and Manager dashboard... can we assign/ route this ticket to an employee joined or login only to handle customer care.**

### 9. Product help guide (new request)  (Edited 12:31 PM)

> We will also need a product help guide for all work you do for website.. worth explring evry piece of wor[k] you are doing. but as a stranger its hard as a quick refernce for us and users product wise.

### 10. Sign-off  (12:34 PM)

> Thank you and wishing Sahil and Bhargav and team members, good day

---

## Action items — status

Last updated 15 September 2026, after deployment. Every "Done" below is live on
https://gotrips.ai and verified there, not just on a developer machine.

| # | Item | Amer's ask | Status |
|---|------|-----------|--------|
| A1 | Support ticket system + manager dashboard | Demo the workflow at next week's meeting | **Ready** — script + video, see below |
| A2 | GoTrips support widget (Tawk replacement) | He will log a ticket and come back with doubts | **With Amer** |
| A3 | WhatsApp Business API | Cost to discuss; do it after eSIM + 4 products | **Parked by Amer** |
| A4 | UAE visa Emirate availability + Sharjah deposit | Supplier card blocked; will test after rates | **With Amer** |
| A5a | Route selector gold border too thin | Match the logo's border thickness | **Done — deployed** |
| A5b | Same circular treatment on registration forms | Pop-up *and* all registration forms | **Done — deployed** |
| A6a | eSIM video in "9:11" screen size | A portrait cut | **Done — deployed** (read as 9:16, see open question) |
| A6b | Company logo at start and end of the video | Title cards both ends | **Done — deployed** |
| A7 | Client support proposal (draft, not sent) | "At your pace, not in rush" | **Parked** |
| A8a | "Where is the ticket hitting?" | Explain where a ticket lands | **Answered** |
| A8b | Customer-care employee login | Someone who logs in only to handle support | **Done — deployed** |
| A9 | Product help guide | Per-product reference for staff and users | **Done — deployed** |

**Eight of twelve delivered. Four sit with Amer.** Nothing is waiting on us.

### What "Done" means for each one

| # | Evidence | Where it lives | Commit |
|---|----------|----------------|--------|
| A5a | Ring raised from 2.2% to 8.5% of the disc, brushed-gold gradient, geometry re-fitted. Verified at 1440px and 375px | `resources/views/partials/emirate_selector_modal.blade.php`, tokens in `partials/gold-medallion-tokens.blade.php` | `5af70f4` |
| A5b | Pop-up rebuilt as a true disc; agent, agency, freelancer and referral pages given the same ring via one shared partial. No field, id, validation or route changed | `banner.blade.php`, `partials/gold-medallion-page.blade.php`, 4 register views | `5af70f4`, `e826763` |
| A6a | 1080x1920, 84.3s, portrait-native layout (not the wide cut scaled down). Linked under the player and in the QR email | `videos/esim-install-guide-portrait/`, `public/assets/esim/how-to-install-esim-portrait.mp4` | `e826763` |
| A6b | Logo intro + outro scenes, re-rendered to 84.3s | `videos/esim-install-guide/compositions/frames/00`, `09` | `5af70f4` |
| A8a | Manager portal always (saved before any email), the configured support inbox, and the customer. WhatsApp built but off until credentials arrive | `docs/help/08-customer-support.md`, demo script, both videos | `5af70f4` |
| A8b | `customer_care` role: lands on the ticket queue, bounced from every other manager route, appears as an assignee, emailed when assigned. Owner creates one from Support settings. 19 tests pass | `ManagerAuthMiddleware`, `ManagerSupportTicketController`, `SupportTicketNotifier`, `CustomerCareRoleTest` | `5af70f4` |
| A9 | 11 plain-language guides rendered at `/manager/help`, visible to customer-care staff too | `docs/help/`, `ManagerHelpController` | `5af70f4` |
| A1 | 10-minute demo script plus a 97s narrated screen recording of the whole flow | `docs/support/demo-walkthrough-2026-09.md`, `videos/walkthrough/` | `da73903`, `0db7f50` |

### Found and fixed along the way (not asked for)

- `public/assets/index_files/transparent_logo.png` has **no alpha channel** — the
  transparency checkerboard is painted into the pixels, so it rendered as a grey
  chequered ring in the first video render. A correctly masked version is at
  `public/assets/index_files/logo-circle-1024.png`. (`5af70f4`)

### Deployment record

| | |
|---|---|
| Deployed | 15 September 2026 |
| Commits | `5af70f4`, `da73903`, `e826763`, `0db7f50` |
| Method | Files verified byte-identical against production before upload, then FTP, then migration via the token-guarded runner, which was neutralised straight after |
| Migration | `2026_09_15_000001_add_customer_care_to_users_role_constraint` — ran on production |
| Verified live | homepage, eSIM, UAE visa, all four registration pages, manager login, manager help |
| Known incident | Pages 500'd intermittently for a few minutes after deploy: PHP opcache held replaced files after the webhook's `git pull`. Fixed with an opcache reset; 23/24 checks clean afterwards |

### Still to raise with Amer

1. **The ratio.** He wrote "9:11", which is not a real screen shape. Built as 9:16
   (1080x1920), the standard phone format. One command to rebuild if he meant
   something else.
2. **Production runs `APP_ENV=local`.** Pre-existing, `APP_DEBUG` is off so nothing
   leaks, but Laravel treats the live site as a development environment. Not changed
   without his say-so.

### Questions that were open, and how they were settled

| Question | Settled |
|---|---|
| Scope of "all registrations forms" | All of them: the pop-up plus the four full-page forms |
| 9:16 vs literal 9:11 | Built 9:16; still worth confirming with Amer |
| Customer-care login shape | A restricted role on the existing manager login, not a sixth portal |
| Help guide format | Markdown in `docs/help/`, rendered inside the manager portal |
