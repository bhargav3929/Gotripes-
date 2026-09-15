# Support ticket demo walkthrough

**For:** weekly client meeting with Amer, week of 21 September 2026
**Length:** about 10 minutes
**Goal:** show the full ticket journey end to end and answer "Where is the ticket hitting?"

## Pre-demo checklist (do the day before)

- [ ] Manager, Support Tickets, Workflow settings: support email set to the inbox you can open on screen.
- [ ] First-response target set (240 minutes) and support hours text checked.
- [ ] One customer-care user created from Workflow settings; welcome email received; you know the temporary password.
- [ ] Three sample tickets already in the queue: one **overdue** (created yesterday, no reply), one **Waiting for customer**, one **Resolved**. Use clearly fake names such as "Demo Customer".
- [ ] Two browser windows ready: window A is the public site with the help widget; window B is the manager portal logged in as owner. A third private window for the customer-care login.
- [ ] Mailbox open in a tab for the support inbox and for the demo customer address.
- [ ] Phone nearby for the mobile view of the widget.

## Where is the ticket hitting? (direct answer)

> A new ticket lands in **four places**:
> 1. **Manager portal, always.** It is saved there first, before any email is attempted, so it can never be lost.
> 2. **Email to the support inbox** you set in Workflow settings. The email's reply-to is the customer, but replies should be sent from the portal so response time is recorded.
> 3. **Email to the customer** with the ticket ID, the acknowledgement text, support hours and the response target.
> 4. **WhatsApp**: the code is ready but waiting on WhatsApp Business API credentials. Until then each ticket shows "Awaiting API setup" in the portal. Nothing is pretended.

## Script

**Minute 0 to 1. Set the scene.** "This is the channel a stuck customer uses instead of emailing you. Same window on every page, phone and desktop."

**Minute 1 to 3. Customer creates a ticket.** In window A press the help button, choose Create a new ticket, type a realistic problem ("Paid for Thailand eSIM, no QR received"), then name, email and phone. Point out the ticket ID (GT-2609xx-XXXXXX), the acknowledgement text, the hours and the response target. Switch to the mailbox: show the customer's acknowledgement email.

**Minute 3 to 4. Staff notification.** Open the support inbox: "New support ticket GT-...". Note the customer's page is recorded, and the reply-to.

**Minute 4 to 5. Manager queue.** Window B, Support Tickets. Show the five counters, the overdue sample ticket at the top with its red marker, then the new ticket. Open it: conversation on the left, contact details, source page, priority, assignee and "WhatsApp routing: Awaiting API setup" on the right.

**Minute 5 to 6. Owner assigns to customer care.** In Update ticket, set assignee to the customer-care user and save. Show the internal "Assigned to ... by ..." note in the thread. Open the assignee's mailbox: "Ticket GT-... assigned to you".

**Minute 6 to 7. Customer-care login.** Private window, gotrips.ai/manager/login as the customer-care user. It lands on Support Tickets. Sidebar shows only Support Tickets and Help Guide. Try the Dashboard address: it bounces back. "This person can only do customer care."

**Minute 7 to 8. Reply.** As customer care, open the ticket, write a reply, leave status Waiting for customer, send. Show the customer's "Update on ticket" email and that the counter "Awaiting first reply" dropped.

**Minute 8 to 9. Customer tracks and follows up.** Window A, help button, Track an existing ticket, paste the ID. Show the staff reply, then send a follow-up. Back in the portal the status is Open again and the support inbox has "Customer follow-up on GT-...".

**Minute 9 to 10. Resolve and stats.** Reply once more with status Resolved. Show the queue counters and the Dashboard tiles: open, awaiting, overdue. Close with the handover point: every reply is under the agent's name, so a new hire can take over any ticket.

## Likely questions and answers

1. **"Can we reply from email instead of the portal?"** You can, but the portal will not record it and the customer will not see it when tracking. Reply from the portal.
2. **"What if the customer-care person is off?"** The owner or any admin sees every ticket and can reassign. Nothing is locked to one person.
3. **"Can the customer-care login see orders or prices?"** No. Only Support Tickets and the help guide. Every other page redirects.
4. **"When does WhatsApp start?"** As soon as the Business API credentials are pasted into the settings. No code change. Cost is your call; you said after eSIM plus four more products.
5. **"How do we know the team is fast enough?"** The counters: average first-response time, awaiting first reply, overdue. The target is set in Workflow settings and the queue sorts overdue first.

## After the demo

Log the sample tickets as Closed so they do not skew the 30-day resolved count, and change the customer-care temporary password if the account will stay in use.
