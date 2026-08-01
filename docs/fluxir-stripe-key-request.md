# Email to Fluxir — Stripe publishable key (blocks all e-Visa payments)

**To:** Kirill Gandyl <kirill@fluxir.com>
**Subject:** GoTrips — Stripe publishable key needed to complete checkout (tenant 3a1b95b5-b962-fb0d-42b8-7136173992ce)

---

Hi Kirill,

We have the e-Visa flow working end to end against your API — person, trip,
service intent, document uploads and `ReadyForPayment` all succeed, and
`GET /api/app/trip/{tripId}/checkout` returns a session id such as
`cs_live_b1DPaqtOUs0g5BY9JBHRRNURSFRPdFPAwQsk8v9WuP7ERLV3m0mJ3bOGAa`.

The one step we cannot complete is sending the customer to that session.
A Stripe Checkout Session id can only be opened by the browser through
Stripe.js, using the **publishable key of the Stripe account that created the
session** — which is your account, not ours:

```js
Stripe('pk_live_…').redirectToCheckout({ sessionId: 'cs_live_…' });
```

So we need **one of these two things**:

1. **Your Stripe publishable key** (`pk_live_…`). This key is designed to be
   public — it is embedded in the page of every Stripe-powered site and cannot
   be used to move money, read customers, or issue refunds. This is our
   preferred option and unblocks us immediately.

2. **The session URL instead of the session id.** Every Checkout Session has a
   `url` property. If the checkout endpoint can return that (or a second field
   alongside `result`), we would redirect straight to it and need no key at all.

Either one is a five-minute change on our side.

**Why this is urgent:** until this is resolved, no customer can pay for an
e-Visa through GoTrips. Applications reach your system with documents attached
and then stop at the payment step.

Two smaller questions while we are here:

- **Settlement.** We add our margin on top of your net fee when displaying the
  price, but the Stripe checkout charges the customer through your account. Can
  you confirm how our margin reaches us under the pay-now model? If it does not,
  we would rather move to the invoicing model we discussed in June — which
  brings us to:

- **Invoicing model.** Is the invoicing arrangement (`prohibitDeferredPayment=false`,
  `creditLimit > 0`) something you can enable on this tenant now? Our
  implementation for it is already built and behind a config flag; with it
  enabled we would collect payment on our own gateway and settle with you
  monthly, which also resolves the margin question above.

Thanks,
Bhargav — GoTrips

---

## Once they reply

**If they send a publishable key:** add it to production `.env` and clear config
cache. No code change is needed — the storefront already calls
`redirectToCheckout` and only falls back to the "contact support" message
because the key is absent.

```
FLUXIR_STRIPE_PUBLISHABLE_KEY=pk_live_...
```

**If they can return the session URL instead:** `FluxirService::getCheckout()`
already exposes whatever the response carries; surface it as `checkout_url` in
`FluxirEvisaController::apply()` and the storefront will use it automatically —
its submit handler redirects to `d.checkout_url` before it ever looks at the
session id.

**If they enable invoicing:** set `FLUXIR_DEFERRED_PAYMENT=true`. Payment then
runs through Nomod on our side and the Stripe key stops mattering entirely.

**Verify after any of the above** by placing a real test application at
https://gotrips.ai/e-visa and confirming the payment page loads. Then check
Manager → Orders → e-Visa for the record, and that the customer received the
confirmation email.
