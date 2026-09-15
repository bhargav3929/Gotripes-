# UAE visa services

Customers apply for a UAE visit visa online, pick the Emirate that issues it, upload documents, and pay by card. Staff and the supplier receive the application by email and manage it in the manager portal.

## Who uses this

- Customers applying for a Dubai, Sharjah or other Emirates visa for themselves or a group.
- Managers who set up packages, prices and deposits.
- Customer-care staff answering questions about status and refunds.

## Where to find it

- Customer page: gotrips.ai/uaevisa (menu item "UAE Visa Services").
- Manager setup: Manager, Visa, UAE Visa Services (page title "UAE Visa Packages and Pricing").
- Manager orders: Manager, Orders, UAE Visa Applications.

## Step by step (customer)

1. Open the UAE visa page. A black and gold circular popup appears with the GoTrips logo on top and one circle per Emirate below. Only Emirates that have an active package are shown. The customer must pick one; the rest of the form is priced off this choice.
2. Choose nationality and country of residence. Prices can differ by nationality, so the price updates after this step.
3. Choose the entry type and duration, and how many adults, children and infants are travelling.
4. Optional add-ons: hotel booking and airline ticket booking. Each carries a small fee set by the manager (AED 25 by default).
5. Enter arrival and departure dates, contact email and phone, and the lead traveller's name.
6. For each applicant, upload a passport copy, a passport photo and the airline ticket. Supporting documents are optional. The "Scan passport to auto-fill" button is optional: it reads names and passport numbers from a photo, and anything it cannot read is filled in later by the team.
7. If the chosen package takes a security deposit (Sharjah does), the form explains the refundable deposit per applicant and the non-refundable admin fee, and asks for the bank account where the refund should be sent.
8. Press "Pay Securely". The button shows the total and, underneath, the amount that is refundable after the visit.
9. Pay by card on the hosted checkout page. The customer returns to a payment status page and receives a payment email.

## What the customer sees

- Payment confirmation email with the order reference.
- Refund note: the security deposit is returned within 5 to 6 working days after the exit stamp is received, minus the admin fee.

## What the manager sees

- **Orders, UAE Visa Applications**: one row per application with status filter and search. The detail page shows applicants, documents, payment, deposit and refund amount. A "Mark Refund as Paid" button records that the deposit was returned.
- **UAE Visa Services** setup page, with these blocks:
  - Emirates: which Emirates are active and their picture in the popup.
  - Packages: one or more per Emirate, with name, type, description, default security deposit, admin fee and the supplier email addresses that receive applications.
  - Price rows per package: entry type, duration, traveller type (adult, child, infant), optional nationality, price. A row with no nationality is the default price.
  - Nationality deposits: a different deposit and fee for a specific nationality.
  - Service fees: the hotel booking and ticket booking add-on fees.
  - e-Visa markup for the global e-Visa product (see [Global e-Visa](11-global-evisa.md)).
- A new application is also emailed to the package's supplier addresses and to the company address.

## Known limits

- The current visa supplier's card is blocked (client side). Live testing waits on new supplier rates.
- If the passport scanning service is off, the form still works; details are entered by hand or filled in later.
- A deposit belongs to the package, not the Emirate. Any package given a deposit behaves like Sharjah.

## Common questions

- **"Why is Sharjah asking for my bank details?"** The Sharjah visa carries a refundable security deposit, and the bank details are where it is refunded.
- **"How much do I get back?"** The refundable amount is shown under the Pay Securely button and in the application detail.
- **"Can I apply for several people?"** Yes. Each applicant has their own document uploads.
- **"I paid but got no email."** Find the application in Orders, UAE Visa Applications, then ask the customer to check spam.
