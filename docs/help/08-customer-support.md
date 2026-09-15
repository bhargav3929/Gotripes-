# Customer support tickets

Customers raise a ticket from the help window on any page, receive a ticket ID and an acknowledgement, and can track and reply to it. Staff work the queue in the manager portal, which is the system of record.

## Who uses this

- Customers who are stuck or have a question.
- Customer-care staff who answer tickets.
- Owners and managers who set support hours, targets and the support inbox, and who watch team performance.

## Where to find it

- Customer: the round help button in the corner of every public page.
- Staff: Manager, Customer Care, Support Tickets (gotrips.ai/manager/support).
- Settings: the "Workflow settings" button at the top of the Support Tickets page.

## Step by step (customer)

1. Press the help button. The window offers **Create a new ticket** or **Track an existing ticket**.
2. Create: describe the problem first (at least 10 characters), then give name, email and phone or WhatsApp number.
3. On submit the customer immediately sees the ticket ID, in the form GT-260915-ABC123, the automatic acknowledgement text, the support hours and the response target. The same is sent by email.
4. Track: enter the ticket ID. The conversation opens and refreshes itself every 20 seconds while the window is open.
5. Follow up: type a message under the conversation. This reopens the ticket if it was resolved. A closed ticket cannot be replied to; the customer must create a new one.

## Where a ticket goes

- **Manager portal, always.** Saved before any email is attempted, so it is never lost.
- **Email to the support inbox** set in Workflow settings (falls back to the company email), with reply-to set to the customer.
- **Email to the customer** on creation and on every staff reply.
- **WhatsApp**: built but waiting on Business API credentials. Each ticket shows "Awaiting API setup" until then.

## Step by step (staff)

1. Open Support Tickets. The top cards show active tickets, awaiting first reply, overdue, resolved in 30 days, and average first-response time.
2. The queue lists overdue tickets first, then tickets awaiting a first reply, then everything else, oldest first. Search by ticket ID, name, email, phone or issue; filter by status or "overdue only".
3. Open a ticket. The left side is the conversation; the right side shows contact details, the page the customer was on, priority, assignee and WhatsApp routing state.
4. Reply in the box. Choose the status to set (default "Waiting for customer") and the assignee ("Assign to me" by default). Sending records the first-response time and emails the customer.
5. Change status, priority or assignee without replying using the Update ticket box. Assigning to someone else emails them and adds an internal note that the customer never sees.

Statuses: New, Open, Waiting for customer, Resolved, Closed. Priorities: Low, Normal, High, Urgent. "Overdue" means no first reply and the target time has passed.

## Workflow settings (owner or manager only)

Support email, first-response target in minutes (default 240, allowed 15 minutes to 7 days), support hours text (default Monday to Saturday, 9:00 to 18:00 UAE time), the automatic acknowledgement text, and the WhatsApp staff number.

## The customer-care staff role (new, September 2026)

- The owner creates a customer-care user from the Workflow settings area on the Support Tickets page: name, email, phone. A welcome email with the login link and a temporary password is sent.
- They log in at gotrips.ai/manager/login and land on Support Tickets. The sidebar shows only Support Tickets and this help guide; every other page redirects back to the queue.
- They can be chosen as assignee, reply, and change status and priority. They cannot change workflow settings or create staff.
- When a ticket is assigned to them they receive "Ticket GT-... assigned to you" by email.

## Daily routine

1. Overdue first, then awaiting first reply, then the oldest open ticket.
2. Read the original problem and the source page before replying. Assign to yourself and send a useful first reply, not a placeholder.
3. Waiting for customer when you need something; Open while we own the next step; Resolved only when handled.
4. Never ask for passwords, card numbers or one-time codes.
5. Escalate: payment taken but nothing delivered (Urgent); eSIM install issues (check the provider order first); visa deposit or refund questions (product owner); fraud, threats or data exposure (a manager).
6. Before leaving, every open ticket has a status, an assignee and a note saying the next action.

## Known limits

- WhatsApp routing is off until API credentials are provided.
- Tracking needs only the ticket ID, so do not share IDs publicly.
- A mail failure is recorded on the ticket, not shown to the customer.

## Common questions

- **"I lost my ticket ID."** Search the queue by email and read it to the customer.
- **"Why did my ticket reopen?"** A customer follow-up always sets it back to Open.
- **"Can I add a private note?"** Not yet. Assignment notes are the only internal messages.
