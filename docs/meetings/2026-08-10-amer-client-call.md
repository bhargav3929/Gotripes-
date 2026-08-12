# Client call — Amer Ali (Ayn Al Amir Tourism) — 10 Aug 2026

**Recording:** https://fathom.video/share/9RVhjmSSyoNZkgBL4NX4JxCsyUzfQ5De (52 mins)
**Logged:** 13 Aug 2026

## Participants

| Name | Role |
|---|---|
| Amer Ali | Client / employer. Gives nearly all the product direction in this call. |
| Bhargav K | Developer (us). |
| Ayn Al Amir Tourism ("Sahil") | Amer's on-ground ops person in UAE. Talks only about business ops — cars, drivers, suppliers — not the website. |

> **Read the transcript with care: Fathom's speaker labels are unreliable and swap mid-sentence.**
> Several lines labelled "Bhargav K" are plainly Amer giving instructions (e.g. at `21:39`, *"So firstly, fix this image"*), and some labelled "Amer Ali" are Bhargav answering. Where the label and the content disagree, trust the content. The full transcript is preserved verbatim at the bottom of this file so the wording can always be re-checked rather than trusting this summary alone.

---

# A. Clear — understood, no clarification needed

Ordered roughly by how Amer prioritised them. **Amer's overriding instruction (`37:50`–`38:29`): stop product work this week and build the B2B/B2C registration flow first.** Everything in the eSIM/visa lists below is queued behind that unless he says otherwise.

## A1. Priority for the week — B2B/B2C registration

| # | Change | Source |
|---|---|---|
| 1 | Build the registration process for **B2B and B2C**. This is the week's only focus; other products pause. | `35:00`, `37:50` |
| 2 | Add a **country** field to registration (today there is only Emirates). | `36:01` |
| 3 | The UAE-specific fields must be **hidden unless the country is UAE**. *"Once we are selecting any country, these fields shouldn't show. It will show only when he is selecting UAE."* | `36:01`–`36:32` |
| 4 | **Trade licence upload** must capture an **expiry date**. | `36:35` |
| 5 | **Expiry notification** — a warning goes to us *and* a copy to the agency ahead of expiry ("one month or like that"). | `36:35`–`37:00` |
| 6 | **Auto-disable the account** if the trade licence is not valid/renewed. Rationale is legal exposure: *"tomorrow they have uploaded and they are not having that trade authority and dealing with someone and it becomes a fraud means then we are also in that loop."* | `37:00`–`37:30` |
| 7 | Send a **welcome/confirmation email** when the account is created. | `37:00` |
| 8 | Contract flow: country decides **national vs international contract**, the correct one is **auto-generated to their account on the website**, they **e-sign on the site**, and it then comes **to us for approval**. | `35:24`–`35:57` |
| 9 | Amer is getting the real contract PDFs from his legal team. Until they arrive, **build the workflow against sample PDFs**. | `38:29`–`38:53` |

## A2. eSIM

| # | Change | Source |
|---|---|---|
| 10 | **Fix the eSIM image** — it is not loading. | `21:39` |
| 11 | Put the **"how to install" instructions into the purchase email** the customer receives after buying an eSIM. | `21:39` |
| 12 | Keep the instructions **on the website page as well** — both, as agreed in an earlier meeting. | `21:39`–`22:20` |
| 13 | Add a **small yellow box/button near the top of the page** reading something like *"How to install — scroll down for the instructions"*, because customers will not scroll to find the steps on their own. | `22:20`–`22:56` |

## A3. Hajj & Umrah / Going Saudi

| # | Change | Source |
|---|---|---|
| 14 | **Reorder the service tabs to: Saudi Visa → Transport → Catering Services → Hotels.** Bhargav's rationale (visa, then transport, then food, then hotel) was put to Amer and he accepted it — *"That's perfect."* | `23:29`–`24:17` |

## A4. UAE visa — customer form

| # | Change | Source | Where |
|---|---|---|---|
| 15 | Add **"(Optional)"** to the yellow *"Scan passport to auto-fill"* headline. It currently reads as mandatory; customers must know they can still type details manually. | `51:00` | `resources/views/uaevisa.blade.php:851` |
| 16 | Confirm the **Standard Visa / Tourist Visa duplication is removed** from the dropdown. Amer asked directly whether it was done. | `50:35` | — |

## A5. UAE visa — manager/admin panel

| # | Change | Source | Where |
|---|---|---|---|
| 17 | **Bank details must be a plain paragraph, not a bulleted list.** Reason is operational: when issuing a refund they want to select the whole block in one go and paste it into WhatsApp or the bank portal — *"Instead of removing bullet one step, two steps, three steps, we'll just copy all this and paste it."* | `28:30`–`29:01` | `resources/views/manager/orders/visa-detail.blade.php` — currently `<li>` items |
| 18 | **Remove the duplicate package-creation screen.** There are two places to create a UAE visa package; Amer wants only the wizard, and a package created there should land straight in the active pricing matrix. *"Why are we having two dashboards for creation? … It is creating a confusion."* | `31:02`–`32:32` | — |

## A6. Testing

| # | Change | Source |
|---|---|---|
| 19 | Amer says the five live products — **eSIM, Global e-Visa, UAE Visa Services, Hajj & Umrah, Activities** — have **workflow issues and need testing**. He did not enumerate them. | `34:34`–`35:22` |

---

# B. Needs your clarity — I would be guessing

## B1. Hero / banner images — portrait redesign `26:54`–`27:41`

Amer wants the hero images **portrait instead of landscape**, the **GoTrips logo much more prominent**, and **"a little bit of yellow" on the borders**. His reasoning: right now Dubai and Sharjah look like the headline act and GoTrips looks incidental — *"it is us who are giving this Dubai."*

**What I need:**
1. Which section exactly? I believe this is the homepage hero ad slots (`resources/views/banner0.blade.php`, the TV 1–5 boxes), but confirm.
2. Is this a **code change** (make the boxes portrait) or a **content change** (upload new portrait artwork)? The current images are landscape — forcing a portrait box will crop them badly unless new artwork is supplied.
3. Target aspect ratio (e.g. 3:4, 4:5)?
4. Logo — how large, and where in the frame?
5. Yellow borders — the existing gold `#FFD700`, and what thickness?

## B2. Global e-Visa markup field `32:15`–`34:31`

**Important: a global e-visa markup already exists in the code** — `EvisaSetting.markup_percent`, defaulting to **15%**, applied to Fluxir's net fee. So this is probably not "build it", it is "surface it where Amer expects and make it per-agency".

**What I need:**
1. **Placement.** Amer said the box could go *"either way, from here or here"* and then *"here is better for reading… in the top box"* — but I only have audio, not his cursor. Which page/panel, exactly? A screenshot settles it.
2. **Per-agency percentages.** He wants different rates per agency — *"for Bhargav I gave 10 percent, for Sahil I gave 15 percent."* That needs the B2B agency accounts to exist first. **Should this wait until the registration work (A1) is done?** I assume yes.
3. Does a per-agency percentage **override** the global for that agency only, with the global as the fallback for everyone else (including B2C)? That is what I would build unless told otherwise.
4. He said the global e-visa markup *"should be separate"* from the visa package pricing screen. Separate **page**, or just a separate **box on the same page**?

## B3. "We shouldn't have this part" — what exactly gets deleted? `32:20`–`32:37`

Right after the duplicate-dashboard point (A5 #18) Amer says *"We shouldn't have this part. We shouldn't have this part… we just need to remove all these things off."* He was pointing at his shared screen. **I will not guess at deleting panel sections.** Please mark up a screenshot of the UAE visa panel showing precisely which blocks go. Item #18 (the duplicate creation screen) is the part I am confident about; anything beyond that is unconfirmed.

## B4. Registration — mechanics not settled

1. **E-signature:** a third-party service (DocuSign/Zoho Sign) or an in-house drawn-signature capture? This is a cost and compliance decision, and it changes the build substantially.
2. **B2C fields:** B2C presumably has no trade licence and no contract. What does a B2C registration actually collect, and does it need approval at all — or is B2C instant?
3. **Approval:** who approves a pending agency — super admin, or manager? What does the agency see while pending, and do they get an email on approval/rejection?
4. **Expiry lead time:** *"one month or like that"* — is 30 days correct, and should it repeat (e.g. 30/15/7 days) or fire once?
5. **Auto-disable scope:** block login entirely, or let them log in but block new bookings? The second is usually kinder and still safe — confirm.

## B5. eSIM image `21:39`

*"Firstly, fix this image. Image is not loading right previously."* — he was pointing at his screen. Which image, on which page? A screenshot or URL. I do not want to guess and "fix" the wrong asset.

## B6. Transport add-on `41:52`

Amer floated charging extra for an Abu Dhabi → Dubai pickup and *"add to that"* on the packages. Is that a **website change** (a paid add-on option on package booking, like the existing hotel/ticket add-ons) or purely an **ops/pricing arrangement** handled offline? It sat inside an ops conversation, so I have not assumed it is a dev task.

## B7. Sharjah vs Dubai visa `51:00`

*"Now removed, the Sharjah visa and Dubai visa look different"* — I cannot tell whether this is Amer confirming something is now correct, or reporting that they wrongly look different. Please confirm which.

---

# C. Not website work — ops items (recorded only so they are not mistaken for dev tasks later)

These came from Amer and Sahil discussing ground operations. **No code involved.**

- Hold the driver search until a car is secured — *"we cannot afford to have a driver without the car"* (`41:17`).
- Source the Nissan X-Trail; budget AED 10,000; a possible dealer contact in Musaffa (`40:43`).
- Find private transport for Abu Dhabi ↔ Dubai transfers (`41:52`) — see B6 for the possible website angle.
- Email agencies about selling eSIMs in bundles of 5/10/15/20, directing them to the website; offer office walk-in help (`43:00`).
- Email the Timor suppliers again for packages (`44:09`).
- Collect supplier WhatsApp numbers for all target countries **except South Korea** (already covered) and email them the pricing-pattern flyer showing the required multi-currency format — KRW/USD/AED/CNY (`45:02`–`49:23`).

---

# D. Full transcript (verbatim)

> Preserved unedited, including Fathom's speaker mislabelling and its inline ACTION ITEM markers, so any future reading can go back to the source wording rather than this summary.

```
0:00 - Bhargav K (bhargav17748@gmail.com)
  I am just on my offline messages. I am going to reply them meanwhile. Then you can just revert.

0:08 - Amer Ali
  Sahil, are you there?

0:10 - Bhargav K (bhargav17748@gmail.com)
  Yes, yes. Good morning. Good morning. Okay.

0:13 - Ayn Al Amir Tourism
  Okay. I am just putting myself on mute. Sahil, you can continue with mute also.

0:20 - Amer Ali
  Once Bhargav is ready, he will come back.

0:22 - Ayn Al Amir Tourism
  I think I will check with Karsha also if it is going.

0:25 - Amer Ali
  Okay? Okay. Thank you.

0:26 - Bhargav K (bhargav17748@gmail.com)
  Thank you. Hey Amir. Hi, hi, yes, I can hear you. Sorry, was on mute.

21:13 - Amer Ali
  Yeah, I'll just share my screen and update you quickly because I already wasted so much of time. Yes, I had planned to move with another thing, but it's okay.
  ACTION ITEM: Fix eSIM image; add How to Install button; include instructions in purchase email - WATCH: https://fathom.video/share/9RVhjmSSyoNZkgBL4NX4JxCsyUzfQ5De?timestamp=1288.9999  I'm also opening the website. Yeah.

21:39 - Bhargav K (bhargav17748@gmail.com)
  So firstly, fix this image. Image is not loading right previously. Yes. Yeah. After that, the next fix is like when they download it, when they buy the eSIM, in the mail, they receive and how to install instruction in the mail.  Okay, and also here also in this page also have given them instruction because in the previous meeting we decided to have it on our website and as well as in on the mail as well.  Perfect.

22:20 - Amer Ali
  On this one, just make a note, just add another button how to install on the first page only just a small, small yellow color the box saying how to install scroll down for the instructions or something like that because nobody will only have that steps in.  How to install scroll down for the instructions.

22:49 - Bhargav K (bhargav17748@gmail.com)
  Okay. In your way, a nice tagline.

22:56 - Amer Ali
  Because I can see everything is In place, how to install your eSIM, in the same way, just for instructions or activation, just scroll down.  Yeah.
  ACTION ITEM: Reorder services: Saudi visa > Transport > Catering > Hotels - WATCH: https://fathom.video/share/9RVhjmSSyoNZkgBL4NX4JxCsyUzfQ5De?timestamp=1398.9999

23:16 - Bhargav K (bhargav17748@gmail.com)
  Next, I fixed this. Before, it was like breaking nano.

23:22 - Amer Ali
  It is responsible based on the screen size. It gets adjusted.

23:27 - Bhargav K (bhargav17748@gmail.com)
  Yes.

23:29 - Amer Ali
  In this part, a small correction, catering services should go after hotels or before hotels. Saudi visa, transport, catering services, and hotels.

23:46 - Bhargav K (bhargav17748@gmail.com)
  Okay. Normally, when you see the pattern, you take visa. Once you take visa, you go for transport. Transport, when you land in there, you look for food.  After food only or the hotels.

24:00 - Ayn Al Amir Tourism
  People who want hotels will go in this series.

24:05 - Amer Ali
  Okay.

24:06 - Bhargav K (bhargav17748@gmail.com)
  So that's the pattern I am looking.

24:09 - Amer Ali
  So that's easy for us. Yeah. Yeah. That's perfect. Yeah.

24:17 - Bhargav K (bhargav17748@gmail.com)
  And I can see that you have changed the subheading also.

24:20 - Amer Ali
  The folder going Saudi. Yeah. Yes.

24:31 - Bhargav K (bhargav17748@gmail.com)
  So next I have removed these options. Before it was like 4. Right now I made just 2. Mm hmm.  hmm.

24:45 - Ayn Al Amir Tourism
  Here. Okay. I have my doubts.

24:48 - Amer Ali
  Okay. I'll be... Once you just finish it, I will tell you what is it. Yeah. I have studied this one.  I have made a note and everything is...

25:00 - Bhargav K (bhargav17748@gmail.com)
  That's fine.

25:00 - Amer Ali
  But my clarifications regarding these things are not. Maybe you can help me understand what are the changes and why.  Okay. Yeah.

25:12 - Bhargav K (bhargav17748@gmail.com)
  Thank you. So here you said some changes, right? What are those changes? I didn't understand in that chat. Actually, this is going like a rectangular box, where our logo is very small.

26:54 - Amer Ali
  I wanted the picture not in the landscape model, This portrait model, where the logo is more visible. The pictures are good, the ask is nice, borders need a little bit of yellow.  And the logo should be more, you know, it is us who are giving this Dubai. Up now, the attraction looks like Dubai and Sharjah is prime, than us.  Now, what we need to do is, we as a logo are offering this, Dubai and Sharjah. Okay.

27:41 - Bhargav K (bhargav17748@gmail.com)
  I hope you got it. Yeah. So here I have added the bank details as well. Yes. And when you are adding, when the customer is adding these bank details, where are they getting captured?  I mean, they will be stored here in the admin. Okay, right.
  ACTION ITEM: Update bank details: remove bullets; format as paragraph - WATCH: https://fathom.video/share/9RVhjmSSyoNZkgBL4NX4JxCsyUzfQ5De?timestamp=1699.9999

28:16 - Amer Ali
  Let's say if we want to retrieve it for one of the customer, how do we retrieve it? I mean, if you want to see, you can see it from here.

28:28 - Bhargav K (bhargav17748@gmail.com)
  Right.

28:30 - Amer Ali
  Can we just remove these bullets, the bullet points dots, and keep it only as a paragraph?

28:38 - Bhargav K (bhargav17748@gmail.com)
  Yeah, we can keep it as a paragraph.

28:40 - Amer Ali
  When we want to refund, what we'll do is we'll just copy all. Instead of removing bullet one step, two steps, three steps, we'll just copy all this and paste it to WhatsApp or in any bank area.  Okay.

29:01 - Bhargav K (bhargav17748@gmail.com)
  Yeah, you are having some doubt here, right?

29:09 - Amer Ali
  Yeah, and you are done, huh? Yeah.

29:13 - Bhargav K (bhargav17748@gmail.com)
  Okay. Yes.

29:15 - Amer Ali
  Please open UAVisa panel. Yeah, panel. This scroll bar is looking nice and now it is friendly for us to scroll up and down.  Mhm.

29:30 - Bhargav K (bhargav17748@gmail.com)
  Okay. Aha.

29:31 - Amer Ali
  In this one, I understand this part as, can you close this, or else I will share my screen, okay?  Okay. One minute. First I will log in as... Right, in the UAE visa, visa services, okay, now can I, can I share the screen?  Yeah, can share the screen. Okay, I'll enter screen, right, see, you're able to see, right?

30:32 - Bhargav K (bhargav17748@gmail.com)
  Yeah, I can see your screen.

30:34 - Amer Ali
  Okay, in this one, everything is very much clear, I can add all the company's name, left-hand side, left-hand side, the panel is everything is very clear, this is a dashboard to enable and disable, correct?  Yeah. Once we create the package, it falls here, and we can enable disable. you can add the supplier and enable.
  ACTION ITEM: Audit UAE visa panel; remove duplicate package creation; auto-route new packages to pricing matrix - WATCH: https://fathom.video/share/9RVhjmSSyoNZkgBL4NX4JxCsyUzfQ5De?timestamp=1858.9999  Yeah.

31:00 - Bhargav K (bhargav17748@gmail.com)
  Okay.

31:02 - Amer Ali
  Once this is being created, it is going here, correct?

31:07 - Bhargav K (bhargav17748@gmail.com)
  Yeah.

31:09 - Amer Ali
  Why are we having two dashboards for creation? Here we have created the package, create the visa package. Here we have created in the left hand side, select Emirate, Abu Dhabi, or whatever it is, we have selected it and we have created the package, then it is falling into pricing matrix.  Yeah.

31:38 - Bhargav K (bhargav17748@gmail.com)
  This is the pricing matrix.

31:39 - Amer Ali
  I want to understand why are we having another dashboard here to add another package? Maybe in the past we have added that for some other reason, I guess.

31:53 - Bhargav K (bhargav17748@gmail.com)
  I'll check like why we are having it. Yeah. Yeah. And if it is not needed, it. Yeah, exactly. We don't need it because .  It is creating a confusion. I am looking for a wizard where you have already given me a wizard here.

32:08 - Amer Ali
  I am creating my package. Let's say I'm creating Dubai Visa package name, something like that.

32:15 - Bhargav K (bhargav17748@gmail.com)
  And I gave this everything, created a package.
  ACTION ITEM: Add Global e-Visa markup field; enable per-agency % - WATCH: https://fathom.video/share/9RVhjmSSyoNZkgBL4NX4JxCsyUzfQ5De?timestamp=1946.9999

32:20 - Amer Ali
  It should automatically fall in this row. We shouldn't have this part. We shouldn't have this part.

32:32 - Bhargav K (bhargav17748@gmail.com)
  It should fall in the active pricing grid.

32:37 - Amer Ali
  So we just need to remove all these things off and accept global e-visa markup, which is a box which should go either way, from here or here, just as per your field.  Here is better for reading. You can add here in the box here saying like, This is exclusive for global e-visa markup here in the top box because that is a separate field right for us so you can have a box here okay and we can change percentage case by case because once we are creating b2b accounts what happens is like we will have that markup case by case we'll have another page like let's say for Bhargav I gave 10 percent for Sahil I gave 15 percent maybe he is one of our top seller we are giving him for 10 percent only like that we'll keep on playing case by case that is a separate field if you feel like it should be here it's fine or else just have another field created for this one the global  Global visa should be separate because tomorrow we are adding agencies into it, we will add the percentage case by case.  It will not be by default. Maybe somebody is selling only two visas, but whereas someone is selling daily 10 visas.  So our percentage also varies by selling.

34:31 - Bhargav K (bhargav17748@gmail.com)
  This is the main concern from this.

34:34 - Amer Ali
  Going ahead, like I said, for this one, as of now, I can see we have these two, three products in place.
  ACTION ITEM: Deliver B2B/B2C registration w/ Amer: contracts, e-sign, UAE-only fields, license expiry, auto-disable, welcome email - WATCH: https://fathom.video/share/9RVhjmSSyoNZkgBL4NX4JxCsyUzfQ5De?timestamp=2089.9999  E-SIM, Global visa, UA visa services, but you need to just test those because there are workflow issues. And haja numra.  One, two, one, two, three, One, four, five products actually, activities also, one, two, three, four, and five, five products, right now what I can see like we should start with the registration process for B2B and B2C.  Yeah.

35:22 - Bhargav K (bhargav17748@gmail.com)
  Our next step. Yeah.

35:24 - Amer Ali
  When they start registering, I am working with the legal teams to have a national and international contract. Okay. When they say like they are UAE, the field should select as like local contract should be auto-generated to their account on the website itself.  Then they will do the digital signature from the website itself. Then they will be entitled to, or to us for the approval.  Okay.

35:57 - Bhargav K (bhargav17748@gmail.com)
  Let's say Bhargav has one, is in. interested in this one.

36:01 - Amer Ali
  He will do, he will go register now. He will select all those fields. And when he is selecting the country, now we have the Emirates, but we will have the country also here.  country is here. Once we are selecting any country, these fields shouldn't show. Okay. It will show only when he is selecting UAE.

36:32 - Bhargav K (bhargav17748@gmail.com)
  UAE. Okay.

36:35 - Amer Ali
  Once he is selecting, once he has uploaded, this trade license should have the fields of auto expiry. Okay. Whenever he is uploading, whatever the trade license he is having, it should have a notification for us.  This trade license is upload your trade license. Thank you. Thank you. One month or like that notice for us and for one a copy for them okay so what happens is this copy should go to mail for their once they create the account they have to receive a mail as well right yeah register or else it should be in an auto disable that account okay what happens is like we will stay in a legal way of doing business tomorrow they have uploaded and they are not having that trade authority and dealing with someone and it becomes a fraud means then we are also in that loop okay this is the concern I just need to get the store to PDF files from the legal based on this products we have once you do this we will  Meanwhile, work on this registration process. As of now, we will stop working on the products and we will just have that registration thing perfect.  Then only we will move with the other products.

38:20 - Bhargav K (bhargav17748@gmail.com)
  Okay. This week, take it as for the registration process for national and international thing.

38:29 - Amer Ali
  I am speaking with the legal team how to have this done for the contract. You lay the workflow. I am supposed to get that PDF or you just upload with a sample PDF for national and international auto digital signature.  Then we will take it from there. Okay.

38:53 - Bhargav K (bhargav17748@gmail.com)
  Okay. Okay.

38:55 - Amer Ali
  I think you are in Bhargav.

38:58 - Bhargav K (bhargav17748@gmail.com)
  We will have just small.

39:00 - Amer Ali
  So with Sahil. He is new to these meetings. Actually, Sahil is from Calcutta and he is working with me in ground here and he supports me a lot.  So on ground basis, how the workflow is being set, he will give some small updates so that you will be aware also what is happening here.  Sahil, over to you.

39:26 - Ayn Al Amir Tourism
  First of all, let me get started. So this week customer ratio is good, but still your voice is not clear, Sahil.

39:41 - Amer Ali
  It's okay.

39:42 - Ayn Al Amir Tourism
  If you want to communicate in Hindi also, it's fine.

39:55 - Amer Ali
  Hello. Can you hear me? Yes. If you want to turn off the AC, can turn off. Your voice is not clear.

40:08 - Ayn Al Amir Tourism
  No? Yes.

40:10 - Amer Ali
  Okay.

40:12 - Ayn Al Amir Tourism
  So this new customer ratio is good, but still you need to focus on other things like getting a driver.  Okay. So for that, I'm uploading the driver vacancy post daily on groups, or some groups, and Facebook groups also, and social media.  And I also shared the vacancy post with Zatak Guy. He said he will help us finding driver. Okay.

40:42 - Bhargav K (bhargav17748@gmail.com)
  So yeah.

40:43 - Ayn Al Amir Tourism
  And for the Nissan X-Trail car, yesterday I got two messages regarding the car. One person leaves in Musaffa. He asked me about our budget.  So I tell him our budget is 10,000. team, King Thank Thank So I think he is some kind of car dealer.

41:05 - Bhargav K (bhargav17748@gmail.com)
  He said he will check the car in our budget.
  ACTION ITEM: Pause driver hiring until car secured - WATCH: https://fathom.video/share/9RVhjmSSyoNZkgBL4NX4JxCsyUzfQ5De?timestamp=2466.9999

41:08 - Ayn Al Amir Tourism
  So you have to wait and see what to reply. Okay, right.

41:17 - Amer Ali
  Abi, as of now for this one search, hold the driver search. We will just, you just need to focus on tracing the car.  Okay. Maybe if needed, I will be the driver. But first we need the car before driver. We cannot afford to have a driver without the car.  And so hold that process of search. you. This is necessary.

41:48 - Ayn Al Amir Tourism
  But our main is the car. Yeah.
  ACTION ITEM: Source private transport for Abu Dhabi–Dubai transfers; add extra-charge option to packages - WATCH: https://fathom.video/share/9RVhjmSSyoNZkgBL4NX4JxCsyUzfQ5De?timestamp=2532.9999

41:52 - Amer Ali
  Please go on. Yeah. And for this, a lot of packages I upload in daily on social media. In that case just a new thought like if you need to arrange a pickup from here to Dubai you will need to pay extra charges if he's ready we will do the transport to give speak to someone in the market there is way or someone like we need a transport private transport are you arranging it if that is the case then add to that and say okay for we will arrange the Dubai also if you are willing to pay extra and he was also
  ACTION ITEM: Email agencies re: eSIM bundles (5/10/15/20); direct to website - WATCH: https://fathom.video/share/9RVhjmSSyoNZkgBL4NX4JxCsyUzfQ5De?timestamp=2584.9999

43:00 - Ayn Al Amir Tourism
  Asking for eSIM. So are we ready to provide eSIM? Yes.

43:07 - Amer Ali
  Yes, we are ready.

43:09 - Ayn Al Amir Tourism
  Yes, we are ready.

43:15 - Amer Ali
  You can also speak to those agencies who are going in the groups saying we can sell in the bundles, not just for one, but for many if you like to.  In the bundle of 5, 10, 15, 20, like that. All they need to do is go to our website and then just pay us and just have it happen.  Bhargav has uploaded the steps how to install. If you go on the website and see how it does. Or if he is in your local...  In your local area, ask him to visit our office and help him how to buy and check out. Okay.  I got his number.

44:09 - Ayn Al Amir Tourism
  So I will ask him to come visit our office. Perfect. Okay. That's sorted. And for the Timor yesterday packages, I have got their contacts.
  ACTION ITEM: Email Timor suppliers again for packages - WATCH: https://fathom.video/share/9RVhjmSSyoNZkgBL4NX4JxCsyUzfQ5De?timestamp=2657.9999  Yeah. Perfect.

44:22 - Amer Ali
  But still did not get any packages from them. But I am in touch with them. No problem. No problem.  told you to send mail again.

44:30 - Ayn Al Amir Tourism
  So after the meeting, I will send the mail again.

44:32 - Amer Ali
  See, since both are there online, I just want to say why we are doing this is once Bhargav is ready with this, we will start with two packages and we need, when you are sending it to 10 or 15 suppliers, we are filtering the best one in this and have them communication established and their contact number and this WhatsApp will be sitting on the website.  for the platform. Thank

45:00 - Ayn Al Amir Tourism
  So that's the pattern.

45:02 - Amer Ali
  Why we are sending 100 people because we will filter the best ones of it. We will know their service only by anonymous way as a mystery shopper.  And when they are not following what you need, you can copy our slide and send them the area. We are just sending this in our TikTok or wherever we want.  This is the pricing pattern we are looking. If you can give us this, we will be happy to involve in our future.  This is the message you convey if they are not following. Take our slide, screenshot it and send it to it.

45:43 - Bhargav K (bhargav17748@gmail.com)
  Not completely, only the pricing.

45:47 - Amer Ali
  They will get an idea who we are and how we want to work. If they fit in our case, this is good for our future.  Start with like South Korea. South Korea don't work. With them, because I already have those contacts, but just share it on the screen for as a TikTok, but don't work on them, but go back to this, all these countries and convey this, this is the pricing we are looking, go back to all those flyers for the pricing, send them the screenshot in the same email, brother, send me us, send us this pricing pattern, which is help us to boost in the market.  Then they will get the understanding what we are looking and what they are providing. If you see today's flyer, you will see that pricing pattern, South Korea in KRW, USD, AED, CNY, all those pricing, this is the pricing we are looking for, don't waste our time, just give it in this way, if you can give it, we will do it or else leave it, mark it, mark it, mark

47:00 - Ayn Al Amir Tourism
  received a payment of 3000, so when the payment is received, I will receive it.

47:06 - Amer Ali
  Has it gone through?

47:10 - Ayn Al Amir Tourism
  He said this customer registered the payment. It's in process. Okay, okay. I received the payment.

47:21 - Amer Ali
  Has he ticketed or you have booked it?

47:28 - Bhargav K (bhargav17748@gmail.com)
  Okay. I'm sorry, I am not following.
  ACTION ITEM: Collect supplier WhatsApps; email pricing-pattern flyer to non–South Korea suppliers - WATCH: https://fathom.video/share/9RVhjmSSyoNZkgBL4NX4JxCsyUzfQ5De?timestamp=2860.9999

47:40 - Amer Ali
  Hello? Yeah, I am very happy. Okay, okay. Okay, fine. Right. Abhi, next week, going on ahead, you will focus on all these countries, retrieve their WhatsApp numbers.  And email them back on saying, like, this is the pricing format we are looking for. It does help our customers.  This is the way our customers are asking. This will be the reply to them. Your voice is not clear?

48:22 - Ayn Al Amir Tourism
  Yes.

48:24 - Amer Ali
  Maybe come near to the speaker and the PC and just go on, if you are not using the headphone.  Maybe the mic is near to the screen, speaker. You have to speak there.

48:40 - Ayn Al Amir Tourism
  I have to connect with every country, right? Yes. All those, Uruguay, all those countries you mentioned, go back to the…

49:04 - Amer Ali
  So you will mail them saying like, this is the pricing pattern we require. If you can share us, that will be great.  This week you need to collect all those WhatsApp numbers from these points.

49:23 - Ayn Al Amir Tourism
  You don't need to work for South Korea, we are there, but just go on the social media with this flyer.

49:35 - Amer Ali
  This is a perfect update from our team. I am just working on both the patterns. This is it from my side also.  think we are done.

49:49 - Ayn Al Amir Tourism
  I have parallelly given the update what we are going to do on this one.

49:57 - Bhargav K (bhargav17748@gmail.com)
  Anything, any doubt from anyone, please.

50:00 - Ayn Al Amir Tourism
  Be free to ask me.

50:02 - Bhargav K (bhargav17748@gmail.com)
  No, everything is clear for my end.

50:08 - Amer Ali
  Okay, that's perfect.

50:10 - Bhargav K (bhargav17748@gmail.com)
  Then let's rock on. Thank you so much for your kind support. Both of you are doing a great job in this part.

50:18 - Amer Ali
  My part is to just keep on correcting where it is returned.

50:22 - Ayn Al Amir Tourism
  It's not that I am sitting here to find out the mistakes, but it is the way how market needs it, that's why correcting you all.

50:31 - Amer Ali
  Yeah.

50:33 - Bhargav K (bhargav17748@gmail.com)
  Okay.

50:35 - Amer Ali
  Marko, as soon as you gave me the UAE in the way we want it. And have you removed that duplication of standard visa and tourist visa?  Yeah.
  ACTION ITEM: Add (Optional) to Scan to autofill headline - WATCH: https://fathom.video/share/9RVhjmSSyoNZkgBL4NX4JxCsyUzfQ5De?timestamp=3049.9999

50:49 - Bhargav K (bhargav17748@gmail.com)
  Okay. In this drop down. Yes. Okay.

50:52 - Ayn Al Amir Tourism
  Because it is continuously flashing and the changes, can you confirm that both the changes

51:00 - Amer Ali
  Or, now removed, the Sharjah visa and Dubai visa look different, and in the scan to autofill, put a bracket in the yellow headline saying like optional, so that they can even enter manual, yeah, if they want to enter, it is seeming like this is very mandatory, to do it, okay, yeah, okay, that's it from my side, I will rework on the Bhargav, the things, eSIM, UAVs, and the things you have provided me, if I need some change as per the customer, maybe I will request you, yeah, yeah, sure, okay, that's it guys,

52:00 - Bhargav K (bhargav17748@gmail.com)
  Thank you so much.

52:00 - Amer Ali
  I feel great having a great day. Great thing with me. Same thing here.

52:06 - Bhargav K (bhargav17748@gmail.com)
  Thank you. Thank you. Thank you. Have a nice week. Bye-bye.
```
