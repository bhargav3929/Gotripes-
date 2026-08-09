# e-Visa (Fluxir) — state of play

Last updated **2 Aug 2026**. Read this before touching anything under `/e-visa`.

---

## The one-line summary

The integration works end to end **except** that customers cannot pay, because
opening a Stripe Checkout Session requires the publishable key of the account
that created it — Fluxir's, not ours. Everything else the audit found was fixed
and deployed. **The next action is not code: it is sending the email in
[`fluxir-stripe-key-request.md`](fluxir-stripe-key-request.md).**

---

## Don't confuse the three visa systems

They share the word "visa" and nothing else. The manager portal mixes their
settings on the same screens, which is the main source of confusion.

| | **e-Visa (Fluxir)** — this doc | **UAE Visa** | **Saudi Visa** |
|---|---|---|---|
| Public page | `/e-visa` (81 countries) | `/uaevisa` | `/saudi-visas` |
| Controllers | `FluxirEvisaController` (storefront) + `FluxirVisaController` (post-payment) | `UAEVisaController` | `SaudiVisaController` |
| Table | `fluxir_visa_applications` | `uaev_application` | `tbl_saudi_visa_applications` |
| Fulfilment | **Automated** — Fluxir processes the visa | Manual, by staff | Manual, by staff |
| Price source | Fluxir's live net fee × global markup % | Manager: packages / pricing grid | Manager: per visa type |
| Payment | Fluxir-hosted Stripe | Nomod | Nomod |
| Supplier email | **None — Fluxir _is_ the supplier**, it receives the application via API | `visa_supplier_email` setting | Per-type supplier + company email |
| Manager orders | Orders → e-Visa | Orders → UAE Visa Applications | Orders → Saudi Visas |

---

## How the flow actually works

1. `GET /e-visa` — picker page. Countries come from Fluxir's catalog (24h cache,
   warmed hourly by `evisa:warm-catalog`).
2. `GET /e-visa/types` — visa options for destination + nationality. Prices come
   from **live service-intents**, the same source the application later uses, so
   the listed price cannot drift from the charged one.
3. `POST /e-visa/scheme` — authoritative fee + the document scheme, which drives
   the dynamic form. Different countries require different documents; nothing is
   hardcoded.
4. `POST /e-visa/apply` — validates required documents server-side **first**,
   then: create person → create trip → resolve intent → create service
   application → upload each document → `ReadyForPayment` → get checkout session.
5. Customer pays on Fluxir's hosted Stripe page. ← **currently blocked, see below**
6. `GET /visa/fluxir/success` — finalize-checkout, mark submitted, email the
   business team and the customer.
7. `evisa:reconcile` (every 10 min) catches anyone who paid but never returned,
   and relays approval/rejection emails.

### Pricing

Source of truth is **Fluxir's API**, not the dashboard. The manager sets one
**global markup %** (Manager → Visa Pricing → e-Visa markup, currently 15%).
Customer price = `ceil(net_fee × 1.15)`. Verified live: $96 → $111, $200 → $230.

Two caveats:

- The markup is **platform-wide** (`evisa_settings` has no `company_id`), so a
  tenant changing it changes prices for every site. The UI now says so.
- Under the pay-now model the customer is charged by **Fluxir's** Stripe account,
  so our margin does not automatically reach us. This is one of the open
  questions in the email.

---

## The blocker

`GET /api/app/trip/{tripId}/checkout` returns a Stripe **session id**
(`cs_live_…`). A session id can only be opened by the browser via
`Stripe(pk).redirectToCheckout({sessionId})`, using the publishable key of the
account that created the session. We don't have Fluxir's.

Until they send it (or return the session `url` instead), the storefront tells
the customer their application was saved and to contact support, quoting the
order id. Their documents are already lodged with Fluxir and the order is
visible in Manager → Orders → e-Visa, so staff can follow up.

**When they reply**, see the bottom of
[`fluxir-stripe-key-request.md`](fluxir-stripe-key-request.md) — one env var,
no code change:

```
FLUXIR_STRIPE_PUBLISHABLE_KEY=pk_live_...
```

Setting `FLUXIR_DEFERRED_PAYMENT=true` (invoicing model) would bypass Stripe
entirely and route payment through Nomod. That code is already written and
tested; it needs Fluxir to enable invoicing on the tenant.

---

## Fluxir API gotchas that cost real time

These are **not in Fluxir's documentation** and all of them fail silently.

- **`GET /api/app/travel-services/{id}` returns 405.** There is no read endpoint
  for a single application. Read state from the trip instead:
  `GET /api/app/trip/{tripId}` → `serviceApplications[]`. Our status poll used
  the 405 path and had therefore never updated anything since launch.
- **There is no `isPaid` field.** `checkoutStatus` is `"Unknown"` until settled.
  `FluxirService::applicationIsPaid()` treats an application as paid if
  `checkoutStatus` is paid/succeeded/completed **or** the state has left the
  payment gate — either alone is enough, so an upstream vocabulary change can't
  strand someone who genuinely paid.
- **`service-intents` needs `system.*` keys** in `tripContext.items`
  (`system.tripFrom`, `system.tripTo`, `system.tripOrigination`,
  `system.tripDestination`) or it tries to enumerate the whole catalog and
  fails with **414 RequestUriTooLong**.
- **The singular `serviceType` is required** on that call —
  `serviceTypesFilter` alone is rejected with 400.
- **`createServiceApplication` requires `providerName` + `serviceIntentKey`**
  despite the docs marking them optional.
- **Trip payload misspells destination** as `destinatonCode` (sic).
- **`serviceIntentKey` format** is `Visa-{provider}-{dest}-{visaTypeId}`, which
  is how we map a chosen visa type back to an intent.

---

## What was fixed on 2 Aug 2026

Commits `fa1076f`, `7c1bf8f`, `de1f2a0`.

The audit found that **no customer could ever pay**: the storefront redirected to
`checkout.stripe.com/pay/<id>`, a URL format Stripe retired, so every applicant
hit "Something went wrong" with their documents already lodged. Production data
confirmed **13 applications, 0 ever paid** — including one real customer
(`garipallypragathi@gmail.com`) who tried **6 times across 3 countries** on
17–21 July and could never complete a purchase.

| Fix | Detail |
|---|---|
| Payment redirect | Stripe.js `redirectToCheckout`; without the key, a clear message quoting the order id instead of a dead page |
| `evisa:reconcile` | Every 10 min: recovers paid-but-unfinalized applications, relays approve/reject emails. All side effects guarded by persisted flags, safe to re-run |
| Customer emails | `EvisaCustomerMail` — submitted / approved / rejected. Previously the customer got **nothing**, despite the page promising updates |
| Manager visibility | Orders → e-Visa, flags paid-but-rejected, re-submit action. Previously these orders appeared on **no screen at all** |
| Multi-tenant | Payment-callback lookups bypass `CompanyScope` (order ids are globally unique); checkout URLs built from the requesting host. Subdomain orders previously could never finalize |
| Required documents | Enforced server-side before anything is created locally or at Fluxir |
| Status poll | Now reads from the trip (the 405 bug above) |
| Cleanup | Dead v1 `/visa/fluxir/apply` route + orphaned view removed; country map no longer pasted three times; honest "temporarily unavailable" instead of listing 200 unbookable countries; `evisa:warm-catalog` hourly; markup field discloses it is platform-wide |

Cover: `tests/Feature/EvisaFlowTest.php` (11 tests), including a guard that fails
if the retired Stripe URL ever returns.

---

## Open items

1. **Send the email** — this is the only thing blocking sales.
2. **Consider contacting `garipallypragathi@gmail.com`**, who tried six times in
   July and could not buy. Their applications are in Manager → Orders → e-Visa.
3. **Decide the markup scope** — global today. Fine if intentional (FIFA works
   the same way), but if tenants should price independently, `evisa_settings`
   needs a `company_id`.
4. **Under real load**, `apply()` holds a PHP worker through ~6 sequential Fluxir
   calls. Fine at current volume; if e-visa traffic grows, move the chain into a
   queued job with a polling UI.

---

## Testing it yourself

```bash
# Catalog and prices (no auth needed)
curl -s "https://gotrips.ai/e-visa/types?country=ARE&nationality=IND" | jq

# Warm the cache / check the provider is reachable
php artisan evisa:warm-catalog

# See what reconcile would do, without touching anything
php artisan evisa:reconcile --minutes=0 --dry-run
```

A full submission needs a CSRF token from the page and two files posted as
`files[passportFile]` and `files[traveler.personalPhoto]`. Label test data
clearly (e.g. "Test by Bhargav") — the Fluxir tenant is **live**, and test
applications are visible in their dashboard.
