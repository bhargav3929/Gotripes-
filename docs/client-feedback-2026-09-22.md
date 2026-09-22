# Client feedback — round 3 (Amer, WhatsApp, 22 Sep 2026)

Verbatim from the screenshots Bhargav forwarded. Amer replied after round 2 went
live on 19 Sep. Spelling is his.

## What he sent

> Thank you - the branding looks super

> *thinking of creating a b2b login for e-sim*, but contract generation is pending
> for sign up - want to give contract and thn giv access

> Our product support needs a coordnator before we sign agencies - i was being
> asked by a agency to give access - contract is rquired..

> will ask the supplier too.

(8:13 PM)

> sure

> I am trying to prepare a b2b contract for e-sim, if contract is ready we can
> load it in portal for auto sign and start giving access to agents internally

(8:14 PM, with the link https://gotrips.ai/freelancer/register)

> this area also needs your attention

Two older screenshots came with the batch, both from 18 Sep and both already
answered by round 2:

> can we have logo clickable to revert to homepage  (3:14 PM)

> also please mention as home so that users may understand after reading  (3:48 PM)

Those two are live since 19 Sep — the selector logo links to the homepage and
carries a gold HOME tag. Nothing to redo; worth showing him in the meeting.

## What he is actually asking for

| # | Ask | Reading |
|---|-----|---------|
| C1 | A B2B login for eSIM | A separate sign-in for agencies selling eSIM, not the freelancer storefront |
| C2 | Contract before access | An agency signs the B2B contract during sign-up; access is only granted once it is signed |
| C3 | "load it in portal for auto sign" | The contract PDF is uploaded in the manager portal, shown to the agency at sign-up, signed on screen, stored against the account |
| C4 | Product support coordinator before signing agencies | A named person handling agency support — the `customer_care` role shipped on 15 Sep already does this; he needs to appoint someone |
| C5 | https://gotrips.ai/freelancer/register "needs your attention" | See the audit below |

Waiting on Amer: the contract text itself, and whatever the supplier says.

## C5 — what is wrong with the freelancer registration page

Checked live on 22 Sep at 1440px and 390px.

The gold ring panel from round 1 *is* on the page (`freelancer/register.blade.php:97`
includes `partials.gold-medallion-page`, and the live HTML carries `gm-ring`,
`gm-disc`, `gm-brand`). What is wrong is everything inside it:

- The page's own palette is still the old bright yellow (`--primary-gold:#FFD700`,
  `--secondary-gold:#FFA500` at the top of the file), so the CTA, the headings
  and the icons fight the brushed gold of the ring around them.
- On a phone the whole left column is dropped, so the page loses the logo, the
  GoTrips name and every reason to sign up — a stranger sees a bare form.
- At 1440px the hero copy sits low, leaving a large empty band across the top.
- It asks for bank details (bank name, account holder, account number, IBAN,
  SWIFT) before the person has agreed to anything. That is the wrong order, and
  it is exactly where the contract step belongs.
- There is no terms checkbox and nothing is stored about consent. Sign-up is
  instant: `FreelancerSignupController` creates the `ReferralAgent` with
  `status = 'active'` and logs the person straight in — no review, no email.
- The page sells "Become a freelancer" while the ask is a B2B agency flow, so
  the two audiences are being sent to one form.

## What already exists (audit, 22 Sep)

Three separate ways to sign up, with three different levels of control:

| Flow | URL | What it creates | Contract? | Approval? | eSIM access |
|---|---|---|---|---|---|
| Freelancer | `/freelancer/register` | `referral_agents` row | none | none, instant login | sells by referral link, earns commission |
| Agent | `/agent/register` | `agent_applications` row | **none** | manager approves → creates `users` row, `role=company_agent` | yes — `esim` is one of `User::AGENT_SERVICES`, picked at sign-up, enforced by `EnsureAgentService` |
| B2B agency | `/agency/register` | `b2b_partners` row | **yes** | manager approves; login needs approved + active + licence valid | none — the `/agency` portal is a dashboard and a contract download |

So the contract machinery Amer wants already exists, but on the flow that has no
eSIM access, and the eSIM access exists on the flow that has no contract:

- `b2b_partners` already stores `contract_type` (national/international),
  `contract_pdf_path`, `signature_full_name`, `signature_agreed`, `signature_ip`,
  `signed_at` (migration `2026_08_14_000001`).
- `B2bPartnerContractService` renders the PDF at sign-up and stores it on the
  public disk; the partner can download it at `/agency/contract`.
- The two contract templates are explicit placeholders — `resources/views/partners/contracts/national.blade.php`
  and `international.blade.php` both carry a "SAMPLE DOCUMENT — NOT FINAL LEGAL
  TEXT" banner. **This is where Amer's text goes.**
- The contract lives in blade files, so no one can change it from the portal.
  "Load it in portal for auto sign" is the missing piece.
- `agent_applications` has no contract, terms, or signature of any kind
  (`AgentSignupController` says so in its docblock).

## Plan

**Decision needed first:** which flow is "the B2B login for eSIM" — the agent
portal (has eSIM, needs a contract) or the agency portal (has the contract,
needs eSIM)? Recommendation: keep one. Add the contract step to the agent flow,
since eSIM access, per-service permissions and the manager approval queue are
already working there, and point agencies at it.

Then, in order:

1. **Contract in the portal.** Manager → Settings → Contracts: upload a PDF or
   edit the text per contract type, keep versions, mark one current. Sign-up
   shows the current version; the signed copy and its version are stored against
   the account, so an old signature still proves what was signed.
2. **Signature step at sign-up.** Copy what `/agency/register` already does:
   full name typed, checkbox agreed, IP and timestamp stored, PDF generated.
3. **Gate access on it.** No signature, no approval; the manager queue shows the
   signed contract next to the trade licence.
4. **Freelancer page.** Swap the yellow for the brand gold, keep the brand
   visible on phones, move bank details behind the first step, add the terms
   checkbox, and split the audiences so an agency is sent to the B2B flow.

Blocked on Amer: the contract text, and the supplier's answer.

## Status

**Built on local, 22 Sep. Not deployed.** Decisions taken with Bhargav: the
agent portal becomes the B2B eSIM login (it already grants eSIM access), and
the mechanism is built now against a draft contract so Amer's final text is an
upload, not a code change.

What works end to end on 127.0.0.1:8000:

| Piece | Where |
|---|---|
| Publish a contract — paste text or upload a PDF, versioned, one marked current | Manager → Team → **Agent Contract** (`/manager/contracts`) |
| Applicant reads it and signs — types their name, ticks the box | `/agent/register`, and the homepage pop-up as a fifth step |
| Signature stored — name, IP, timestamp, version, signed PDF | `agent_applications` + `contract_documents` |
| Manager sees the signature and downloads the signed copy | Agent Applications → the application → **Agreement** card |
| No signature, no approval | `ManagerAgentApplicationsController@approve` blocks it |
| Signature follows the account after approval | copied onto `users` |

Checked in a browser: published a draft contract, registered an agency through
both the full page and the pop-up, reviewed and approved it, opened the
generated PDF (contract text + signature block + SHA-256 of what was signed).
Neither the pop-up nor the sign-up page scrolls at 1440, 1600, 1920 or 390px.
29 tests pass (`AgentContract|CustomerCare|SupportTicket|Agent`).

Deliberate behaviours worth knowing:

- With no contract published, registration and approval behave exactly as
  before. Publishing one turns the requirement on; "Stop asking for a
  signature" turns it off again.
- A version that anyone has signed cannot be deleted — it is the evidence.
- Applications made *before* a contract was published cannot be approved once
  one exists. The review screen says so, and they must register again.
- An uploaded PDF cannot be written into, so the signed record is a
  certificate naming the version and the file's SHA-256.

### Still to do

- **C5 freelancer page** — not started. See the audit above.
- Amer's real contract text, then publish it as version 2.
- Amer to name the support coordinator (the `customer_care` login exists).
