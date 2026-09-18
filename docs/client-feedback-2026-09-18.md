# Client feedback round 2 — WhatsApp, 17–18 Sep 2026 (AMER)

Verbatim from five screenshots. Amer replied to the deployed round-1 work.
Typos preserved. Our reading is in the table at the bottom.

---

## 1. Emirates route selector (8:42 PM)

Amer quoted our line "Registration pop-up — now circular, black and gold, with our logo"
over a screenshot of the **visa route selector**, and wrote:

> if we can reduce the external thinkness... looks too much where as dubai and sharjah borders thickness is little

## 2. Registration pop-up (8:45 PM)

Over a screenshot of the Create Partner Account pop-up:

> This is nice

> but we need to make use of space, and **thickness of golden border be reduced.**, wondering if we make form without scroll bar...

## 3. Amer's own mock-up (8:47 PM)

He edited the pop-up screenshot himself and sent it:

> i have altered bit

What his mock-up shows: logo, title and step dots as now; the fields area wider;
**NEXT centred on its own row, CANCEL smaller and centred directly beneath it.**

## 4. Help button (9:10–9:16 PM)

> Perfect

> Also the help button we use same theme

> Something unique and someone standing out

(then an icon that did not render — shown as a "?" box)

Bhargav sent a screenshot of gotrips.ai with the bottom-right **Help** button circled: "this one right"

> Didnt get you

> Help button down right for tickets you mean

Bhargav: "yes"

## 5. Selector logo (3:14 PM, next day)

Over the route selector with the logo circled in red:

> can we have logo clickable to revert to homepage

Bhargav's addition: **"also please mention as home so that users may understand after reading"**

---

## Action items

| # | Where | Ask | Status |
|---|-------|-----|--------|
| B1 | Visa route selector | Outer gold ring is now too thick next to the thin Dubai/Sharjah rings — reduce it and balance the two | **Live** — outer ring 8.5% → 4.5%; Dubai/Sharjah rings now 10%, matching the logo |
| B2 | Registration pop-up | Reduce the gold ring thickness | **Live** — halved, via the shared ring token |
| B3 | Registration pop-up | Use the space better and remove the scroll bar from the form | **Live** — four short steps instead of three; no step scrolls at any tested size |
| B4 | Registration pop-up | Follow Amer's mock-up: NEXT centred on its own row, CANCEL smaller and centred beneath it | **Live** |
| B5 | Help button (bottom right, support tickets) | Same black-and-gold theme; unique, stands out | **Live** — gold-ringed medallion, support agent inside, HELP tag, gold halo |
| B6 | Visa route selector | Logo clickable, goes to the homepage | **Live** |
| B7 | Visa route selector | Visible "Home" label on the logo so users understand it | **Live** — gold HOME tag with a house icon |

Note on B1: round 1 asked to make this ring *thicker* to match the logo (2.2% → 8.5% of the disc).
Amer now finds 8.5% too heavy. The target is balance with the three inner circles, not the maximum.

## Bugs found and fixed along the way

These explain part of what Amer saw, so they are worth knowing:

- **The Dubai and Sharjah rings never showed gold.** Their CSS put a plain colour in the
  first background layer, which is invalid; because it used a CSS variable the browser
  silently discarded the whole background. Amer was only ever seeing a faint edge. Fixed.
- **Dubai always looked different from Sharjah.** The selector put keyboard focus on the
  first circle when it opened, drawing a focus ring on Dubai alone. It now focuses the
  dialog, so both circles look identical until someone interacts.
- **The registration pop-up's close button (×) was invisible since round 1.** The disc
  clipped everything outside its inner edge, including the × on the ring. It now shows.

## Verification (local, 18 Sep 2026)

- All four wizard steps fit with no scroll bar at 1920×1080, 1440×900, 1440×800 and
  1366×780 (circle), and 1366×700 and 375×812 (rounded card).
- In circle mode every field and button sits inside the gold ring at every size.
- Wizard walked end to end: empty steps are refused, Next/Back move correctly, step 4
  shows Create Partner Account, and submitting with no service is refused.
- Logo HOME link lands on the homepage.
- Help panel opens clear of the new button on desktop and phone.
- 19 feature tests pass.

Deployed to gotrips.ai on 19 Sep 2026 (commit e1f3c35). Checked live on gotrips.ai, /uaevisa, staging and amer after the webhook pull: all 200, all round-2 markers present.
