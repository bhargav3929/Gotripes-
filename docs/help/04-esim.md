# eSIM (travel mobile data)

Customers buy a data plan for the country they are visiting, receive a QR code by email, and install it on their phone before they fly. The plan is issued automatically the moment payment succeeds.

## Who uses this

- Travellers who want mobile data abroad without a physical SIM.
- Managers who watch orders and resend QR codes.
- Customer-care staff helping with installation problems.
- Agents with the eSIM service can view the order list.

## Where to find it

- Customer page: gotrips.ai/esim (menu item "eSIM").
- Manager orders: Manager, Orders, eSIM Orders.
- Manager markup: Manager, Settings, Preferences (markup percentage).
- Agent portal: eSIM Orders (read only).

## Step by step (customer)

1. Pick the destination country. The page loads the plans available for it: data amount, validity in days, and price in AED.
2. Choose a plan and enter name, email and phone. More than one eSIM can be bought in one order, one per traveller.
3. Pay by card on the hosted checkout page.
4. On successful payment the eSIM is issued automatically. Two emails arrive: one from GoTrips with the QR code, and one from the eSIM supplier.
5. Install the eSIM while still on Wi-Fi, before travelling. The email gives numbered steps for iPhone and for Android, plus manual details (SM-DP+ address, activation code, ICCID) for phones that cannot scan.
6. Watch the installation video if unsure. It is on the eSIM page and linked from the email. There is a landscape version for laptops and a portrait version for phones (portrait added September 2026).

## What the customer sees

- Email "Your eSIM is ready" with the QR code embedded, the plan summary, install steps and the video link.
- Data starts counting when the eSIM first connects to a network at the destination, not at install time.

## What the manager sees

- **Orders, eSIM Orders**: search by customer and filter by payment status. A flag shows orders that were issued but never emailed.
- Order detail: customer, bundle, payment, provisioning result, supplier order ID and ICCID.
- **Resend QR**: sends the GoTrips email again and asks the supplier to resend theirs.
- **Retry provisioning**: for a paid order that failed to issue.
- Every 10 minutes an automatic check repairs paid-but-not-issued and issued-but-not-emailed orders, and reports the supplier wallet balance.

## Pricing

Customer price = supplier price in USD, converted to AED at a fixed rate, plus the markup percentage set in Preferences (20 percent by default).

## Known limits

- **The supplier wallet is prepaid.** Every sale is paid from it instantly. The balance was about USD 8.66 on 15 September 2026, enough for only a handful of sales. When the balance cannot cover a plan, the page refuses the sale rather than taking money it cannot fulfil. Only a top-up fixes this.
- The supplier's reseller portal (where the wallet is topped up) only opens from India, not from the UAE.
- A few countries return no plans at all (for example Cuba and Russia). The page says so plainly.
- Payment is taken before the plan is issued. If issuing fails, the automatic check retries, and a manager can retry by hand.
- The customer's phone must support eSIM and must not be carrier-locked.

## Common questions

- **"I paid and got nothing."** Open the order in eSIM Orders. If it shows issued but not emailed, press Resend QR. If it failed to issue, press Retry. Ask the customer to check spam.
- **"The QR will not scan."** Use the manual details in the email. Never delete an installed eSIM while troubleshooting.
- **"Does it work now?"** Not until the destination. The plan shows "not started" until it connects abroad.
- **"Can I use one code on two phones?"** No. Each QR is one eSIM and works once.
