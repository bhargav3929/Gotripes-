# Email to Fluxir — the one thing that blocks e-Visa checkout

**To:** kirill@fluxir.com
**Subject:** Stripe publishable key needed — customers cannot pay (tenant 3a1b95b5-b962-fb0d-42b8-7136173992ce)

---

Hi Kirill,

Our e-Visa integration works end to end — person, trip, service intent, document
upload and `ReadyForPayment` all succeed, and your checkout endpoint returns a
session id, e.g. `cs_live_b1DPaqtOUs0g5BY9JBHRRNURSFRPdFPAwQsk8v9WuP7ERLV3m0mJ3bOGAa`.

We cannot open that session. Stripe only allows a Checkout Session to be opened
with the **publishable key of the account that created it** — your account:

```js
Stripe('pk_live_…').redirectToCheckout({ sessionId: 'cs_live_…' });
```

**Please send us one of these:**

1. Your Stripe **publishable** key (`pk_live_…`) — this is the public key meant
   to be embedded in web pages; it cannot move money, issue refunds or read
   customer data. **Preferred.**
2. Or return the session's `url` field alongside `result` from
   `GET /api/app/trip/{tripId}/checkout` — then we need no key at all.

This is blocking: no customer can currently pay for an e-Visa through GoTrips.

Two related questions:

- **Our margin.** We add a markup on top of your net fee, but the customer is
  charged through your Stripe account. How does our margin reach us?
- **Invoicing model.** Can you enable it on this tenant now
  (`prohibitDeferredPayment=false`, credit limit > 0)? Our side is already built
  — with it on, we collect payment ourselves and settle with you monthly, which
  also answers the margin question and removes the key requirement entirely.

Thanks,
Bhargav — GoTrips

---

## When they reply

**Publishable key** → add to production `.env`, clear config cache. No code change:

```
FLUXIR_STRIPE_PUBLISHABLE_KEY=pk_live_...
```

**Session URL instead** → surface it as `checkout_url` in
`FluxirEvisaController::apply()`; the storefront already redirects to
`d.checkout_url` before it looks at the session id.

**Invoicing enabled** → set `FLUXIR_DEFERRED_PAYMENT=true`. Payment moves to
Nomod and the Stripe key stops mattering.

**Then verify:** submit a real application at https://gotrips.ai/e-visa, confirm
the payment page loads, then check Manager → Orders → e-Visa for the record and
that the customer received the confirmation email.
