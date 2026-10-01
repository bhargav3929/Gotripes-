# Client feedback — round 5 (Amer, WhatsApp, 1 Oct 2026)

Three new asks from Amer, plus one report from the founder's laptop. This file is
the working record: what was asked, what was actually wrong, what changed, how it
was checked, and what is still open.

## What came in

| # | Source | Ask |
|---|--------|-----|
| E1 | Founder (screenshot) | "Why is this box shape coming on my founder's laptop when we have a circle shape for this?" — the **Create Partner Account** pop-up shows as a rounded box |
| E2 | Amer, 8:52 AM (screenshot of the welcome pop-up) | "Can we change this also to circular — welcome page" — the **Get Free Quote** pop-up |
| E3 | Amer, 8:56 AM (screenshot of `gotrips.ai/freelancer`) | "On eSIM page there is no link to go back to home page" |
| E4 | Supplier (MontyeSIM) via Amer, 8:13 PM | "Please send a list of ICCIDs that need to be cancelled… this information along with account balance is available on your reseller account." Amer: "can you send me ICCIDs — from eSIM portal, website not accessible from here" |

## E1 — why the founder saw a box (root cause)

**Not a bug in his laptop, and not new: a limit I built in on 17 Sep (round 2).**
The registration pop-ups were a circle only when the browser window was at least
**770px tall**; below that they fell back to a rounded box. I tested on tall Mac
windows and never on a short one.

The founder's screenshot is a 1338 × ~605 window (1338 × 682 minus the browser
toolbar and bookmarks bar). Reproduced on the live site: the pop-up measures
**560 × 504** there, the exact size of the box in his screenshot. A typical
Windows laptop (1366 × 768 screen) has a browser window about 600–650px tall, so
**most visitors on a Windows laptop never saw the circle.** The same rule was
copied into the freelancer pop-up on 27 Sep.

| Window | Before | After |
|---|---|---|
| 1338 × 605 (founder) | box | circle |
| 1366 × 650 (typical Windows laptop) | box | circle |
| 1280 × 600 | box | circle |
| 1440 × 790 (MacBook) | circle | circle |
| 1920 × 960 and up | circle | circle |
| tablet / phone (< 900px wide) | card | card (unchanged, by design) |

### The fix

A circle cannot be redrawn smaller than the inputs inside it, so instead of
switching shape the whole disc is **scaled to fit the window**
(`resources/views/partials/gold-medallion-fit.blade.php`, a small shared helper).
The disc is never drawn below 680px, so text stays readable; only a window too
short for that gets scaled down (smallest text is about 8.5px at the founder's
window size, 10px at 790px and above). The card fallback now starts below a
**536px-tall** window (landscape phones) and below 900px wide (phones, tablets),
where a circle genuinely cannot hold a form.

Applied to the partner pop-up (`banner.blade.php`), the freelancer pop-up
(`partials/freelancer_registration_modal.blade.php`) and the new lead pop-up.

**A second bug found while testing the fix:** a scaled disc keeps its full
layout size, which made the pop-up overlay scrollable, and a browser scrolls such
a container to reveal a focused field. A visitor tabbing through the form could
have shifted the circle up by up to 34px, clipping its top. Fixed with
`overflow: clip` on all three overlays (a clipped box cannot be scrolled).

## E2 — "Get Free Quote" pop-up made circular

`resources/views/partials/lead-popup.blade.php`, shown on the welcome page 5
seconds after arriving (once per session).

- Same black disc, brushed-gold ring, logo medallion and 45° close clasp as the
  Visa Route selector and the registration pop-ups (shared gold tokens).
- A circle cannot hold two columns, so the left marketing panel was folded down
  to one **"15+ years of excellence"** chip under the logo, and the form is one
  column: Name, Mobile (country picker + number), Service, then the gold
  **GET QUOTE** pill centred on its own row, like Next on the other pop-ups.
  The "Industry Legends" / "Best Price Guarantee" bullets and the "Our Expertise.
  Your Journey." headline were dropped for that reason — say if Amer wants them
  back somewhere.
- Every field name, id, the country picker and the submit path are unchanged.
- The success state ("Thank you!") is circular too.
- **Found and fixed on the way: the country picker only ever offered 6
  countries.** It fetched its list from `restcountries.com` in the visitor's
  browser; that service now answers with a redirect that lacks the headers a
  browser requires, so the fetch failed for everyone and the picker fell back to
  its built-in six (UAE, India, Saudi Arabia, US, UK, Pakistan). A visitor from
  Tanzania, Kenya or Nigeria could not pick their own country code. It now uses
  our own list (`App\Support\CountryCodes`, 177 countries) rendered by the
  server: instant, and no third party to break it. Same ordering as before:
  UAE, India, Saudi Arabia, UK, US first, then A to Z. Checked live that the
  old behaviour was real (6 entries, request failed).
- Small extras: the "Terms" line was `#444` on near-black (about 2:1 contrast) and
  is now readable; the Terms link is gold.
- Phones and tablets keep a rounded card with the same ring.

## E3 — no way back to Home on the freelancer page

The page in Amer's screenshot is `gotrips.ai/freelancer`, which is where the
**Become an eSIM Seller** button on `/esim` leads, so that is what "eSIM page"
means here.

What was wrong: the brand mark in its header was a link with no colour or
underline set, so the browser painted "Freelancers" in default link blue (purple
once visited), and it pointed back at `/freelancer` itself. The only route to
the GoTrips home page was a small `gotrips.ai` in the footer.

What changed:
- `freelancer/landing.blade.php`: brand mark restyled and now links to Home; a
  clearly labelled **HOME** button sits beside it (Amer asked for the word
  "Home" on the Visa selector logo for the same reason: so people understand).
  On phones "Freelancers" is dropped from the header so everything fits on one line.
- New `partials/home-pill.blade.php`: the same HOME button, fixed top-left, added
  to the other pages in that journey that had no header — partner sign-in
  (`/partner/login`) and partner register (`/partner/register`).

`/esim` itself already has Home: labelled in the nav on desktop; on a phone it is
the logo plus the menu. Left alone. If Amer meant `/esim` on a phone, a labelled
Home chip there is a small addition (the header is shared by every page, so it
needs deciding).

## E4 — ICCIDs for the supplier

Not a code change. Pulled the order list from the supplier's API (read-only
`GET /Orders`, the same data as the reseller portal Amer cannot open from where
he is). **Only 4 orders have ever been placed on the account**, 2 used and 2
unused. The eligible-to-cancel ones are the unused pair; the list and a ready
message are in the reply that accompanied this round. **Nothing was cancelled or
refunded.** The supplier only asked for a list; the decision on what to cancel is
Amer's.

## How it was checked

- `tests/browser/medallion-fit.mjs` (new, reusable): opens each pop-up at nine
  real window sizes (founder laptop, Windows laptop, 720p, MacBook, Full HD,
  tall 4K, tablet, phone, phone landscape), steps through every step, and
  measures: circle vs card as expected, drawn size fits the window, every
  control stays inside the inner gold ring with at least 8px to spare, smallest
  rendered text, and that focusing/scrolling to the last controls never moves
  the circle. **All pass on all three pop-ups.** Exits non-zero on failure so it
  can gate a deploy. Run it against production with
  `BASE_URL=https://gotrips.ai node tests/browser/medallion-fit.mjs`.
- Walked the whole partner pop-up with a real mouse in the founder's window size:
  validation, Next/Back, the phone country list, all five steps, the signature,
  and the submit (request intercepted so nothing was created).
- Walked the lead pop-up the same way: it opens by itself after 5s, an empty
  submit is refused, the country picker opens inside the window and searches,
  the request carries the country, phone and service, the success state shows,
  Close works. JS errors: none.
- Freelancer pop-up: opens from `?open=register` and from the button, closes.
- PHP suites for the contract, customer-care, support and application flows:
  46 passed.

## Still open / needs a decision

1. **Deploy.** Everything above is built and verified on local only. Production
   still shows the box, the old rectangular lead pop-up and the blue "Freelancers".
2. **`/partner/register` skips the contract.** Found while adding the Home
   button. It is the older referral-partner sign-up, still live (only the admin
   referral-settings page links to it). It creates an **active** account and logs
   the person straight in: no contract, no manager approval. Since 27 Sep the
   freelancer sign-up requires both, so this is a way round the rule Amer asked
   for. Not changed, because it may be a deliberate separate programme. Options:
   send it to the freelancer pop-up (recommended), or add the same gate.
   The page is also visibly cramped (text touches the gold ring).
3. The welcome pop-up's country picker still *defaults* to UAE (+971) for
   everyone; it does not detect the visitor's country the way the registration
   forms do since 27 Sep. They can now pick any of 177 countries (see above), so
   it is a convenience gap, not a blocker.
4. Amer to confirm what he wants cancelled with the supplier (E4).
