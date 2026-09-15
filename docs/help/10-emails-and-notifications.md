# Emails and notifications

Every automatic email the website sends, who receives it, and what triggers it, so staff can tell a customer exactly what they should have in their inbox.

## Who uses this

- Customer-care staff checking "did the customer get an email?"
- Managers deciding which mailbox to watch.

## Where to find it

Business addresses come from Manager, Settings, Booking Notifications. The support inbox comes from the Support Tickets workflow settings. Supplier addresses are set per activity, per visa package and per Saudi visa type.

## Support tickets

| When | To | Subject |
|---|---|---|
| Ticket created | Customer | We received your ticket GT-... (includes acknowledgement text, support hours and response target) |
| Ticket created | Support inbox | New support ticket GT-... (reply-to is the customer) |
| Customer follows up | Support inbox | Customer follow-up on GT-... |
| Staff reply | Customer | Update on ticket GT-... (includes the reply text) |
| Ticket assigned to a colleague | Assignee | Ticket GT-... assigned to you (new, September 2026) |
| Customer-care user created | New staff member | Welcome email with login link and temporary password |

WhatsApp messages are not sent until the API is configured.

## Payments (all products paid through the card checkout)

| When | To | Subject |
|---|---|---|
| Payment succeeds, fails or is cancelled | Customer | "... Booking - Payment Completed Successfully", "Payment Failed" or "Payment Cancelled" |
| Payment succeeds | Business notification addresses | Payment notification with order details |
| Activity payment succeeds | Activity supplier | Booking details without payment information |

## UAE visa

| When | To | Subject |
|---|---|---|
| Application submitted | Package supplier addresses and company address | New UAEV Visa Application (documents attached) |

The customer receives the payment email above. There is no separate "visa approved" email; staff inform the customer.

## Global e-Visa

| When | To | Subject |
|---|---|---|
| Application paid and submitted | Customer | Confirmation with the order ID |
| Provider approves | Customer | Your e-Visa has been approved |
| Provider rejects | Customer | Update on your e-Visa application |
| Application submitted | Business notification addresses | New e-Visa application |

## Saudi visa and Umrah

| When | To | Subject |
|---|---|---|
| Saudi visa submitted | Company email and supplier email for that visa type | New Saudi Visa Application (documents attached) |
| Umrah booking paid | Customer and business addresses | Payment emails as above |

## eSIM

| When | To | Subject |
|---|---|---|
| eSIM issued | Customer | Your eSIM is ready (QR code, install steps, video link) |
| eSIM issued | Customer | A second email from the eSIM supplier |
| Manager presses Resend QR | Customer | Both emails again |

## Activities and tours

| When | To | Subject |
|---|---|---|
| Activity booked | Business addresses and the activity's extra notification emails | Booking notification |
| Activity booked | Activity supplier | Supplier booking (no payment details) |
| Tour package enquiry | Package partner email and business addresses | Enquiry with travel date, travellers and message |

## Partners and agents

| When | To | Subject |
|---|---|---|
| Agent application submitted | Applicant | Your GoTrips Agent Application Has Been Received (temporary password if none was set) |
| Application approved or rejected | Applicant | Decision email, with the reason when rejected |
| B2B agency registers | Agency | Welcome email with contract |
| Agency approved or rejected | Agency | Decision email |
| Licence expires in 30 days | Partner and business | Trade licence expiring soon |
| Licence expired | Partner and business | Account auto-disabled |
| Partner submits renewal | Business | Renewal submitted, review and confirm |

## Other

| When | To | Subject |
|---|---|---|
| Contact Us form | Company | Contact Form Submission |
| Job application (Careers page) | Applicant and recruiter | Thank you for your job application; Job Application from ... |
| FIFA ticket request | Business | New ticket request |

## Known limits

- Emails go out after the order or ticket is saved. A failed email never blocks the order, but the customer will not know unless staff notice.
- Only support tickets record a delivery status (sent, partial, failed). Other failures are logged on the server.
- No email is sent when staff change a UAE or Saudi visa status. Use a support ticket reply.

## Common questions

- **"I got nothing after paying."** Confirm the order exists in the portal, then check spam and the email address on the order.
- **"Can we resend?"** Only the eSIM QR email and support replies can be resent from the portal.
