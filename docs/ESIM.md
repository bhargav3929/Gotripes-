# eSIM (MontyeSIM) — state of play

Last updated **2 Aug 2026**. Read this before touching anything under `/esim`.

---

## The one-line summary

The integration works: 187 of the provider's 194 countries sell real plans, and
a purchased eSIM is genuinely issued and activates. The code faults found on
2 Aug are fixed and deployed. **The blocker now is money, not code — the
reseller wallet holds about $10.91, which covers roughly 1–8 sales.**

---

## THE WALLET — read this first

MontyeSIM is **prepaid**. Every eSIM we sell is bought instantly from a wallet
balance. When it runs dry, the customer pays us and the assign fails.

| | |
|---|---|
| Balance (2 Aug 2026) | **$10.91** |
| Typical cheapest plan | ~$1.26 |
| Median plan | ~$8.50 |
| Mean plan | ~$15.32 |

**For 2,000 customers you need roughly $2,500 (all cheapest) to $17,000
(median) to $30,000 (mean) in the wallet.** At $10.91 the event fails after the
first handful of sales.

Top up in the MontyeSIM reseller portal. `php artisan esim:reconcile` prints
the balance on every run and shouts below $50.

Since 2 Aug the storefront **refuses a sale it cannot fund** rather than taking
the money and failing afterwards — but that turns a funding problem into lost
sales, not into working eSIMs. Only a top-up fixes it.

---

## How the flow works

1. `GET /esim` — country picker. Countries come from `AvailableCountries` (24h cache).
2. `POST /esim/bundles` — plans for a country. **ISO3 codes only** (`ARE`, not `AE`;
   the provider 400s on 2-letter codes). Prices: provider net (USD) → AED at the
   fixed peg (`ESIM_USD_TO_AED`, 3.6725) → × markup (`ESIM_MARKUP_PERCENT`, 20%,
   or the tenant's own `markup_percentage`).
3. `POST /esim/purchase` — re-fetches the bundle from the provider for
   server-side price verification, checks the wallet can cover it, creates the
   order (`ORDESIM{id}`), opens a Nomod checkout.
4. Customer pays on Nomod.
5. `NomodController` marks it paid → `EsimProvisioningService::provision()` →
   `POST /Bundles` (direct assign). **This is the point of no return**: it charges
   the wallet and issues the eSIM.
6. `sendQrEmail()` reads the installation credentials and emails the customer
   their QR from GoTrips.
7. `esim:reconcile` (every 10 min) repairs anything that fell through.

### Purchase model

We use **Flow A (direct assign)**, not the documented Reserve → Complete. So
payment is taken *before* the bundle is secured. Reserve+Complete would be
safer (reserve first, complete after payment, cancel to refund the wallet on
failure) and is worth moving to if failure rates ever bite.

---

## The QR code — how the customer gets their eSIM

**Two emails go out.** MontyeSIM sends its own on assign, and since 2 Aug
GoTrips sends one too (`EsimQrMail`).

We send our own because the provider's email is invisible to us — we could
never answer "did the customer actually receive what they paid for?". Ours
records `qr_sent_at`, shows in Manager → Orders → eSIM, and is resendable.

The QR payload is **not on the assign response** (that returns only `iccid`,
`order_id`, `remaining_wallet_balance`). It comes from
`GET /Orders?order_id=…`, which returns:

- `activation_code` — the full `LPA:1$<smdp>$<matching_id>` string the QR encodes
- `smdp_address` and `matching_id` — for manual installation
- `otp`, `plan_status`, `iccid`

Our email renders the QR image and prints the SM-DP+ details underneath, so a
customer can always install manually.

### Does it actually activate?

Yes — verified on the two real orders that exist:

| Order | Bundle | Provider status | Profile |
|---|---|---|---|
| ORDESIM35 | Indonesia 1GB/7d | Successful | Released, Active |
| ORDESIM40 | Thailand 1GB/7d | Successful | Released, Active |

`profile_status: Released` = the eSIM is issued and installable.
`plan_status: Plan Not Started` on a fresh eSIM is **normal** — the allowance
starts counting when the eSIM first attaches to a network at the destination.

---

## What was fixed on 2 Aug 2026 (commit `6483f1c`)

### Fake plans were sellable — the big one

When the provider returned no bundles, the page fell back to four hardcoded
"demo" plans (`esim_1GB_7D`, `esim_3GB_15D`, `esim_5GB_30D`, `esim_UNL_30D`)
with invented prices — and they were **fully purchasable**. The customer paid
real money through Nomod, then provisioning called MontyeSIM with a bundle code
that has no counterpart upstream and failed every time. Order **ORDESIM22**
shows a real customer had already reached that path.

It was reachable in normal operation: **7 of 194 countries return no bundles at
all** — American Samoa, Belize, Comoros, Cuba, Kosovo, Mauritania, Russia — and
*any* timeout on *any* country produced the same fallback. Under event load a
slow upstream would have turned every destination into phantom stock.

Removed, along with the hardcoded pricing table behind it and the static
190-country list (which turned an outage into "every plan is sold out").
The page now says plainly when there is nothing to sell.

### Everything else

| Fix | Detail |
|---|---|
| Wallet guard | A sale is refused if the wallet cannot cover the bundle's net cost. Fails **open** when the balance is unknown — refusing every sale over an unreadable balance is worse than an alerted, retryable assign failure |
| GoTrips QR email | `EsimQrMail` + `qr_sent_at`; orders list flags provisioned-but-never-emailed; "Resend" sends ours *and* asks the provider for theirs |
| `esim:reconcile` | Was read-only. Now repairs: paid-but-never-assigned → provision; assigned-but-no-QR → email it; issued-upstream-but-unknown-here → adopt. Runs every 10 min, prints the wallet balance |
| Model cleanup | Dropped `user_id`, `total_amount`, `status`, `referral_agent_id` from `$fillable` plus the `user()` / `referralAgent()` relations — **none of those columns exist** on `esim_orders`, so assigning any of them threw |

Cover: `tests/Feature/EsimIntegrityTest.php`, including a guard that fails if
the placeholder plans ever return, and a check that `$fillable` matches the table.

---

## Known issues still open

1. **Wallet balance** — see the top. Nothing else matters until it is funded.
2. **Records can drift from the provider.** ORDESIM40 was Successful and Active
   at MontyeSIM while our row still read "unpaid, pending" — the wallet had been
   charged for an eSIM our own database did not know existed. `esim:reconcile`
   now detects and adopts this case, but the root cause (assign succeeding while
   our write fails) is unproven.
3. **Intermittent filesystem errors on the Hostinger host.** Twice during the
   audit, a request 500'd with `Failed to open stream` on a core file
   (`app/Helpers/tenant.php`, `app/Providers/AppServiceProvider.php`), and
   `git status` repeatedly reported a *different random* set of tracked files as
   deleted while `file_exists()` on those same paths returned true. The files are
   all present. This looks like the shared-hosting storage layer occasionally
   dropping stat results — worth raising with Hostinger before a high-traffic
   event, since under load it surfaces as random 500s.
4. **Direct assign, not Reserve+Complete** — payment is taken before the bundle
   is secured (see above).
5. **Conversion is very low**: of 40 orders, 36 unpaid, 3 failed, 1 paid. Worth
   checking whether the Nomod checkout step is losing people.

---

## Testing it yourself

```bash
# Reconcile + see the wallet balance (safe, reports before repairing)
php artisan esim:reconcile --dry-run

# Actually repair
php artisan esim:reconcile
```

```bash
# Plans for a country — ISO3 only
curl -s -X POST https://gotrips.ai/esim/bundles \
  -H "X-CSRF-TOKEN: <token from the page>" \
  -d "country_code=ARE"
```

Provider API directly (`https://resellerapi.montyesim.com/api/v0`): `POST
/Agent/login` → `Access-Token` header on everything else. `GET
/Bundles?country_code=ARE&currency_code=USD`, `GET /Orders`, `GET
/Orders?order_id=…` (QR fields), `GET /Orders/Consumption?order_id=…`.

Label test data clearly ("Test by Bhargav"). **A real assign spends real wallet
money** — there is no sandbox.
