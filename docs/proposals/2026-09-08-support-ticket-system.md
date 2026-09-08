# GoTrips customer-support ticket system — implementation proposal

**Prepared for:** Amer Ali

**Prepared:** 8 September 2026

**Status:** Approved direction captured from the 3 September call and Bhargav's follow-up answers

## Outcome

Replace the third-party website chatbot with a GoTrips-owned support window. A customer can create a ticket or track an existing one; the customer-care team receives the work by email and, once credentials are supplied, WhatsApp; the manager portal remains the system of record and measures the team's response performance.

## Customer experience

1. The help button is available across storefront pages.
2. Opening it offers **Create a new ticket** or **Track an existing ticket**.
3. New-ticket flow asks for the problem first, then name, email and phone.
4. Submission immediately returns a unique ticket ID and a clear acknowledgement containing support hours and the first-response target.
5. Tracking requires both ticket ID and the ticket's email address. It shows status, timestamps and the customer-visible conversation without exposing manager notes or another tenant's data.
6. A tracked customer can add a follow-up message to the same ticket.

## Support-team experience

- A dedicated **Support Tickets** area in the manager portal provides search, status and SLA filters.
- The queue makes new, overdue and idle tickets visible first.
- Ticket detail shows customer contact data, source page, full conversation, assignment, reply box and status controls.
- The first manager reply records first-response time automatically.
- Every manager reply is retained under the replying user's name so the workflow can be handed to a future customer-care hire without tribal knowledge.

## Manager monitoring

The support dashboard covers both sides requested on the call:

- Customer activity: new/open/waiting/resolved totals, latest customer message and source page.
- Team performance: tickets awaiting first response, overdue tickets, average first-response time, oldest idle ticket, assignee and each staff reply timestamp.

Default SLA is four hours. Support hours and SLA are manager-configurable per company; defaults are Monday–Saturday, 09:00–18:00 UAE time.

## Channel routing

- **Email:** customer acknowledgement on creation, internal new-ticket notification to the configured support inbox, and customer notification on every manager reply.
- **WhatsApp:** the integration seam is built but disabled until the WhatsApp Business Cloud API phone-number ID, access token and staff destination number are supplied. Until then, each ticket visibly reports **Awaiting API setup** rather than pretending a WhatsApp message was sent.
- **Manager portal:** always receives and retains the ticket even if either external notification channel fails.

## Data and safety

- `support_tickets`: tenant, ticket ID, contact details, status, priority, assignment, SLA/response timestamps, source URL and channel-delivery status.
- `support_ticket_messages`: immutable customer, staff and system messages with sender and visibility metadata.
- Tenant scoping applies to every query.
- Public tracking requires ticket ID + matching email.
- Public endpoints are rate-limited and validated; notification failures never discard a ticket.
- The manager portal, not email or WhatsApp, is the authoritative record.

## Acceptance checklist

- Customer can create, receive an ID, track and follow up.
- Automatic acknowledgement states support hours and response target.
- Internal support email and customer emails are sent.
- WhatsApp delivery activates through environment credentials without a code change.
- Manager can search/filter, open, reply, assign and change status.
- Dashboard shows customer volume plus first-response and idle-ticket performance.
- Tenant isolation and public tracking privacy are covered by automated tests.
