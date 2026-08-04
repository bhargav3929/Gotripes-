# Client Call — Amer Ali / Bhargav K / Chaithanya K

**Date captured:** 2026-08-04
**Duration:** ~49 min
**Status legend:** ⬜ not started · 🟨 in progress · ✅ done & deployed · ⏸️ deferred by client · ❓ needs clarification

---

## 1. Changes I understood (actionable)

### A. eSIM

| # | Change | Status |
|---|---|---|
| A1 | Post-purchase QR email must ALSO contain activation instructions: iPhone steps, Samsung/Android steps, and manual SM-DP+ install steps. Client wants these as **small images / pictorial steps**, not a wall of text — "he's in holiday mode and not in the mood to read all our instructions". | ⬜ |
| A2 | Same instructions published on the **website** as a "How eSIM works / How to install" reference, visible **before** purchase. | ⬜ |
| A3 | eSIM landing page: the right-hand hero image is **broken / not loading**. Replace with a strong, on-brand contrasting image (travellers + phones + eSIM). Add tagline: **"Your eSIM ready in 2 minutes"** and **"186 countries"**. | ⬜ |
| A4 | eSIM checkout: add a **quantity / bundle selector** (buy 5, 10, 20 …) for tour operators moving groups of 20–30 on a bus. Price must recalculate on quantity, single checkout, and **all QR codes arrive in ONE email**. Today a tour operator would have to check out 50 times. | ⬜ |
| A5 | eSIM checkout form is too narrow — the **Pay button falls below the fold**. Stretch the form to use the left/right space so the whole form + Pay is visible in one view. | ⬜ |

### B. UAE Visa

| # | Change | Status |
|---|---|---|
| B1 | **Bug:** customers hit `CSRF token mismatch` when applying for a UAE visa and moving into the payment gateway. Reported in Amer's email, Sun 2026-07-26, subject "UAE visa panel". | ⬜ |
| B2 | Admin dashboard → UAE Visa: **remove three tabs** — *Emirates*, *Add-on Settings*, *Legacy Prices*. Leave **only two**: (1) create visa package/category, (2) pricing matrix. "I've seen there is no use for us in the other three." | ⬜ |
| B3 | Package-creation form, in this exact order:<br>1. **Emirate** — dropdown of the 7 emirates<br>2. If **Sharjah** (or any future emirate flagged deposit-type) is picked → extra fields appear: **security deposit / refundable amount**<br>3. **Visa / package type** — Urgent, Standard<br>4. **Visa for** — Adult / Child / Infant<br>5. **Duration** — 30 days, 60 days · **REMOVE 90 days, that visa no longer exists**<br>6. **Price** — whatever is typed here is what the website displays<br>7. **Supplier email address**<br>8. **Our company email address** — an editable field, so the job can be handed from one staffer to another without a code change | ⬜ |
| B4 | On successful checkout, send emails to: the **customer** (submitted + list of docs shared), the **supplier**, and **our company address**. Client said "four copies" — see ❓C3. | ⬜ |
| B5 | **Pricing-matrix tab**: after saving, show the live price grid of created packages with **Edit / Save / Delete / Disable** buttons, so price changes never require re-creating the package. | ⬜ |

### C. Hajj & Umrah / Saudi

| # | Change | Status |
|---|---|---|
| C1 | On the Hajj & Umrah landing section the **"Umrah by Air" card title is cropped** on smaller laptop screens — it renders as "Umrah by". Shift the Saudi Visa card left / widen the row so every card title fits. | ⬜ |

### D. Activities

| # | Change | Status |
|---|---|---|
| D1 | Admin → Activities Listing: add a per-activity **enable / disable (hide) toggle**, so an activity closed for maintenance can be pulled off the website without deleting it. "When we delete, that means we are wiping off all your hard work." | ⬜ |

### E. Global UI

| # | Change | Status |
|---|---|---|
| E1 | **Scrollbars in the dashboard are too thin.** Make them noticeably thicker on both the page and the inner scrolling panels, and change the grey thumb to the theme **yellow/gold**. Today it's so thin that the client resorts to tabbing down. | ⬜ |

### F. Content

| # | Change | Status |
|---|---|---|
| F1 | **About Us**: remove the partner-company references — Amer is no longer working with them, so their name must come off the website. A polished replacement script is coming from Amer later. | ⬜ |

### Already done (confirmed on the call — no action)

- eVisa / eSIM / Saudi **detail "View" pages** with full application status, passport details, mail-sent status, provider response, payment details — shipped, client happy.
- **Supplier email per Saudi visa type** (comma-separated for multiple suppliers) — shipped.
- **Hajj & Umrah required-documents** display on selecting a visa category — shipped, client happy.
- **Activities supplier email** is already separate per activity — confirmed.

---

## 2. Changes I need clarity on

| # | Question | Why it blocks / matters |
|---|---|---|
| ❓C1 | **eVisa "View" detail page — reorganise into a cleaner form/layout.** Amer explicitly said: *"just add it in a note as a pending one, but we will not go with this now."* Confirm this stays parked. | ⏸️ Deferred by client. Listed so it isn't lost. |
| ❓C2 | **eSIM reseller wallet reconciliation** — Amer purchased 2, Bhargav purchased 1 = 3 orders expected, only 1 QR surfaced. Where did the other 2 land? Need someone to open the MontyeSIM reseller account and reconcile orders vs. wallet spend. | Ops/investigation, may or may not be a code bug. Needs reseller-portal access. |
| ❓C3 | **"We will get four copies, emails"** — customer + supplier + our company = 3. What is the **4th** recipient? Manager panel? A second supplier? Or does the customer get two (payment receipt + documents-submitted)? | Determines the mailer fan-out for B4. |
| ❓C4 | **Sharjah security deposit** — is the refundable deposit charged **at checkout on top of** the visa price, or collected separately/offline? Is it shown as a separate line on the website price? | Changes the pricing model and the payment amount. |
| ❓C5 | **"Our company email address" field** — per package, or one global setting? Amer described it as a field on the package form, but the reasoning ("today Bhargav handles visas, tomorrow Kasha") suggests a single global setting is what he actually wants to change in one place. | Per-package = 20 edits when the owner changes. Recommend: global default, per-package override. |
| ❓C6 | **eSIM bundle quantity (A4)** — is there a maximum? Flat price × quantity, or **volume-discount tiers** for tour operators? | Affects pricing logic and whether the reseller wallet can cover a 50-unit order. |
| ❓C7 | **CSRF error (B1)** — need the exact page/step to reproduce. Amer's 2026-07-26 email has the screenshot; I need the URL and whether it happens on the apply form or on return from the payment gateway. | Session-lifetime vs. token-in-form vs. gateway-return are three different fixes. |
| ❓C8 | **About Us copy (F1)** — Amer is sending a polished script. Until then, do I just **strip** the partner references, or write interim replacement copy? | Recommend: strip now, swap in his script when it arrives. |
| ❓C9 | **Instruction images (A1)** — Amer shared iPhone/Samsung screenshots in the WhatsApp group. Do I have those assets, or should I produce clean branded step diagrams myself? | Client suggested "use ChatGPT on this one and use small images". |
| ❓C10 | **eSIM hero image (A3)** — supply a licensed/stock asset, or should I source/generate one? | Avoid shipping a placeholder twice. |
| ❓C11 | **Scrollbar "both sides" (E1)** — page scrollbar + every inner panel, or specifically the settings panel he was on? | I'll apply globally to the admin/manager theme unless told otherwise. |
| ❓C12 | **90-day UAE visa removal (B3.5)** — remove from the form only, or also **retire existing live 90-day packages** already sold/listed? | Data migration vs. form change. |

### Not engineering tasks (tracked, no code)

- **Microsoft 365 / official `@gotrips.ai` email** — Chaithanya to propose and initiate; ~3 seats to start (Amer, Bhargav, +1), Teams for internal comms replacing WhatsApp, OneDrive/SharePoint for backup. Suppliers currently see a non-GoTrips sender, which is hurting credibility.
- **Hiring a junior full-stack dev** — Chaithanya to share the JD/skill set; office at Uppal, near metro; freshers acceptable.
- **Backups** — confirmed Hostinger plan includes automatic backups.

---

## 3. Full call transcript (verbatim)

Bhargav K
Bhargav K
0:00

Yeah, just give me a minute. Yeah, you can see my screen, right?
Yes.

AA
Amer Ali
0:32
Yeah. Okay, okay, fine. Yeah.

Bhargav K
Bhargav K
0:52
Open the manager. Okay, good.

AA
Amer Ali
0:56

Both are required because either one... Right. We are missing if we are completing another site.
Yeah.

Bhargav K
Bhargav K
1:05

So first changes is like first what I did is like I have fixed those mailing issue.
Now, we will have a detailed report of each, for example, this eVisa.


So if you come and click on view here, you can get all the visas.
Yeah.

AA
Amer Ali
1:26
So before was only like, you can only check this data.

Bhargav K
Bhargav K
1:34
But now we've added an extra layer where you can just click on view and view everything like the passport details and is the mails has been sent from our end.

What is the status of that? And what is the data that is got from the provider payment details everything?


Yeah. Can, can this be organized in a, in a form or way?
The,

AA
Amer Ali
2:00
Click on view?

Bhargav K
Bhargav K
2:03
Yeah. This one in an organized way? Yes, we can have it like we can have it in a different organized way if we need.

Okay.

AA
Amer Ali
2:12
Just add it in a note as a pending one, but we will not go with this now because means we will focus on it.

Sure. Okay.

Bhargav K
Bhargav K
2:22
So the next thing is this in the e-visa, mean, e-sim also did the same thing I've added so that it is easy for us to track before what's happening is like when we are, I mean, like when I'm testing this e-sim and everything, I have, I've faced problems like tracking them.


If I do anything, where, where is that landing? Is it like the seriously fulfilling?
Where is the problem happening?


Is my mails has been sent from our end? Is there is a problem with an API?
So we can track everything.

Once they, once someone tried to do anything, we can come here and check what happened with their application full status.


I've done the same thing for Saudis. Sorry, sorry, sorry, sorry, please go into view.
Yeah. Tissim, Tissim, go into view.

AA
Amer Ali
3:12
Abhi, right now, yesterday you have purchased, have you purchased or you have just processed the order which was purchased before?

No, I have purchased the new one.

Bhargav K
Bhargav K
3:25
Okay, purchased the new one.

AA
Amer Ali
3:27

Have you got chance to check on our reseller account that how much money so far has been used?
Because you have purchased one, I have purchased two.

It should be flashing with three QR codes.

Bhargav K
Bhargav K
3:42
Of the one you have shared is one. So what happens to two other QR codes is my concern.

AA
Amer Ali
3:51
Okay. The problem is like before, when I am testing, I found out the QR code.

Bhargav K
Bhargav K
3:59
Uh, There will be a package in our system where QR code will be generated and sent in the mail before the QR code is getting generated and has been sent, but it is not opening.

AA
Amer Ali
4:10
For example, can you open the reseller account once? Yeah, I'll just show you in the mail as well.

Bhargav K
Bhargav K
4:17
What's happening before and what's now now, as you can see, you're getting in a QR code like this, but before it was not this case, it was like getting generated, this one, you got it as a customer or as a seller?


No, we got it as a customer. So you, the one I see is, is a customer format.
Yes, customer format.


You say that once they make the payment, they'll get a mail like your eSIM is ready.
just scan this and get activated.

Correct.

AA
Amer Ali
5:00

For this morning I was speaking to Indonesia. They have successfully activated it.
They are able to use it. What I did was I have just asked him to copy this SMDP address and the SIM address into the SIM panel, manage SIMs in the phone.


And he has copy-paged this address. Without installing the app, the eSIM got activated.
And we actually, one second, I am sharing you something which will help us later.

One minute. We just need to add a few things into that because eSIM is ready.

Bhargav K
Bhargav K
5:54
Everything is ready but the customer is not knowing the steps how to use it.

AA
Amer Ali
5:58
Okay. So I have just got the steps. I am sharing it in the group so that you can access.

This is for the SIM you have provided. This is for manual installation.

Bhargav K
Bhargav K
6:29
Okay, and item. One minute.

AA
Amer Ali
6:44
And now we have two phones which are trending.

Bhargav K
Bhargav K
6:51
This data needs to go there in the

AA
Amer Ali
7:00
The email so that they will stop bothering our customer services and just add some poster and in this one for Samsung there is and for iPhone there is I think just a minute huh sorry yeah take your time.

Bhargav K
Bhargav K
8:28
Yeah, this is for the Android, and... Okay, this one is for the iPhone.

AA
Amer Ali
9:18

iPhone and Samsung and who wants to install manually, these instructions have to come there.
Okay, do you want them to be, like before they buy on it, it's better to show them that these are the steps that you need to do.

Bhargav K
Bhargav K
9:32
On the website, you will show it.

AA
Amer Ali
9:36
But on the email, where you are replying the QR code after the purchase, they have to get these things, so that in the same email they have that QR code, they are just doing it in their way.


Okay. Or if you can just use ChatGPT on this one and use small images for this Android.
All right.


These steps for manually these steps and for iPhone these steps as a image wise.
If you keep this one, then you can, this will be easy for the customer to just go in the floor.


He's in the holiday mode and he is not in the mood to read all our instructions.
So this pictorial presentations in the email when he purchased the QR code will just help him have it done.

And secondly, for the websites, if you have this one ready, if they want to go as a reference how this eSIM works, then that will be perfect for them.


That's it from my side. I'll just share a note. I have already shared a note.
You just have to get on.

Yeah, okay.

Bhargav K
Bhargav K
11:04

So I think so. I've showed you till this one. The next is in Saudi visa.
Everywhere I've added the supplier.

So whoever mail you enter here in the supplier, if there are two suppliers, you can just hit comma and enter another mail.

So they get the details.

AA
Amer Ali
11:43
Here, can you go to dashboard, please? Go to the? Admin dashboard.

Bhargav K
Bhargav K
11:50
Yeah.

AA
Amer Ali
11:52
The Saudi visa.

Bhargav K
Bhargav K
11:58
See, now we have three.

AA
Amer Ali
12:00

One is Tourist Business Visa, one is Umrah Visa, and one is Multiple Entry.
In the Multiple Entry, these visas, do we have that option of adding the supplier email for each separate category?

Let's say I'm not doing my pricing with Multiple Entry Visa guy is not matching with the one supplier, but with another supplier it is matching.


So product wise, can we segregate this one or is it a standard template?
I'll select a category here and let's say Tourist Visa, Kasha is giving a better rate than Bhargav.

So I will add for Tourist Visa, Kasha's email.

Bhargav K
Bhargav K
12:52
Yeah, that's how you do it. Ah, okay, perfect. So, for example, if Kasha is giving a Better price, you can just enter their visa details and here you can just keep Kasha mail ID and if you click add, then you will get a new visa type with for their mail ID if the purchase happens tomorrow, tomorrow, if we tomorrow, if we are supposed to introduce some other category visa, are we able to add, yes, we will able to add a number of types, please carry on.

Thank you. So here we can edit the Sharjah, said right, this will be the amount in the Sharjah, when it will be showing the amount percentage.

AA
Amer Ali
14:36

Your update part is over now, we will talk on the changes, yeah?
Yeah. Let's start with the visa since you have opened this page.

Firstly, please go to the admin dashboard. I have already sent you an email, but still for For the clarifications, will send the matrix in a visa settings where, yeah, I'll be here, here I have, let me open the email, you can also send, open the email if you like.

Okay, my customers were trying to apply for the visa through this portal, but first of all, if you see there is a some error called CSRF token mismatch.

Yeah.

Bhargav K
Bhargav K
15:47
If you please open your email. Yeah. This one on?

AA
Amer Ali
16:13

Let me confirm the date when I said. Sunday, 7-26. The subject are UAE visa panel.
UAE visa panel. Yes.

Bhargav K
Bhargav K
16:42
See, my customer was applying for the visa and using the payment gateway.

AA
Amer Ali
16:48
So while he was using it, he had this problem.

Bhargav K
Bhargav K
16:54
Okay. I'll check this. No problem.

AA
Amer Ali
16:59
Just... Scroll down. This is problem number one. For the workflow, what we need is we just need to admin dashboard.


These fields need to be removed. The three. Now go into admin dashboard.
Okay.

Bhargav K
Bhargav K
17:25

This three fields Emirates, add on settings and legacy prices needs to be removed.
Okay.

AA
Amer Ali
17:34
The reason we will. If you see UAE visa services and add on settings there, the supplier email you have already added it.

Okay. Yeah, I'll be what we need. We need one panel just to have the pricing change and another panel just to

create the categories okay just two tabs are needed here only two tabs are needed where if you see in the email can you open the email if you see only these two settings are required ua visa supplier in email is not only two fields are enough in the admin dashboard and the workflow is like these are the requirements in the workflow okay Emirates when you drop down the list you will have the seven Emirates names there you will have the seven Emirates names this is where you are creating the package visa package okay uh then when selected

Bhargav K
Bhargav K
19:00
For Sharjah, all the fields are same for all the six emirates.

AA
Amer Ali
19:03
Only the Sharjah part is the deposit part. Okay. So if in case we are selecting Sharjah or tomorrow if any other emirates is introducing the deposit system, we should have an option to have the same like Sharjah.

Bhargav K
Bhargav K
19:23
Okay.

AA
Amer Ali
19:24

So here when we are selecting Sharjah, fields like security deposit refundable amount should come as an extra form.
Okay.

Then next field will be to create visa type, packages type. You need urgent or you need a standard way.

Okay. A visa for, and after that we select visa for adult child infant.

Bhargav K
Bhargav K
20:04
Let's say he has selected adult and our pricing should come as for asking for 30 days, 60 days, or 90 days.

AA
Amer Ali
20:15
Please remove the 90 days, the visa does not exist anymore. After that, a price should show in the drop-down list.


This price again you will be defining, Yes, there we will write the price there.
For our website to display what we write should be coming in the website there.

Here, when we are creating the email, let's say 30 days visa as discussed before, Kasha has better price than my price.

So I will add that supplier email address.

Bhargav K
Bhargav K
20:54
Okay. Then our company email address also.

AA
Amer Ali
21:00
will be in the field only. Why? Because today, because we are a small organization, we are using only one email address.


Tomorrow, this job will be given to another email, another guy with another email.
So that's why we keep this field, our company email address as you will just in the drop drop in the box, you will write our company email address so that we keep on changing.


Today, Bhargav is taking care of visas. Tomorrow, Bhargav gets busy with something else.
What he will do is, I will decide that no, Bhargav will not handle, Kasha will handle.

So we will go in the dashboard email and we will change from Bhargav to Kasha email.

Bhargav K
Bhargav K
21:57
Yeah. zweite Anthony. of. do Absolutely The jobs will be easily segregated.

AA
Amer Ali
22:02
And another field is, as usual, the supplier's email address.

Bhargav K
Bhargav K
22:12
Yes.

AA
Amer Ali
22:14
Now, in the workflow, what happens when customer uses the gateway, pays everything, and checks out, we will get four copies, emails.


One, to the customer, documents have been submitted. These are the documents you shared.
And one, to the other three email address, one to supplier, one to our company.


So that's how, next button, create the package and save the package.
Once the packages are displayed, once the packages are saved and the pricing matrix, then the active price grid matrix should display.

Okay, now we have created the package. right. All In the next screen, next tab, we will see what package we have created, what is the rate there.

Okay.

Bhargav K
Bhargav K
23:10
Here, you will give these two buttons.

AA
Amer Ali
23:13

One is to save, one is to delete that quality. Delete, disable, delete, save, delete, disable, save, that's it.
Edit.


Okay. So these four buttons only we will use for having this credit.
Tomorrow, the price has gone increased or reduced.


So from there only, we will just change the price. The package is as it is.
We will just play with the pricing there.

Okay.

Bhargav K
Bhargav K
23:55

You have introduced so well all the matrix, all the five taps.
But I.

AA
Amer Ali
24:00

I've seen there is no use for us in the other three.
Only two, one is for visa packages created, another is for the pricing matrix.

Only two simple and easy for anybody to future, let's say one of the new staff is joining, we will just give them the access to this board and ask them to enter the prices and another one will look at the customer care.

Okay, I understand.

Bhargav K
Bhargav K
24:32
Yes.

AA
Amer Ali
24:35

Anything related to visa, let me check to confirm you. Yes, that is the part.
Okay. And go back to the website.

Bhargav K
Bhargav K
24:58
Thank you. All right. you.

AA
Amer Ali
25:01
If you go into the Hajj and Umrah, yesterday night I have shared you a comment saying the necessary documents required should flash it as when we are selecting multiple entry or any, you have to change it already.

MashaAllah. Yeah.

Bhargav K
Bhargav K
25:31

Perfect. Perfect. This one will help us to easily communicate with them.
Yeah.

AA
Amer Ali
25:42
And if you go on the Hajj and Umrah part, land packages, Hajj and Umrah by bus and Hajj and Umrah by, yeah, perfectly done.

I can see that if you want to just move the Saudi visa a little bit of left inclin, then we can get a smaller screen also has a numra by air in a complete view.

If you see from my point of, from my laptop, I can see that one of the text is missing.

Bhargav K
Bhargav K
26:26
It is getting cropped. Yes, yes, correct. I mean to say yes, correct.

AA
Amer Ali
26:32
If you are looking at from my screen, if you see this one, you will see only a numra by.

Okay. So we just need to open our wider like that. So if you can adjust accordingly, then that will be.


Oh, great. Okay. Okay. Another. Okay. The question on the eSIM. Let's go back to eSIM.
Please make the note of this one and then I will tell you on the eSIM.

Okay.

Bhargav K
Bhargav K
27:10
Should I open the eSIM? Yeah. If you see, there is no picture there.

AA
Amer Ali
27:20
No, it is not loading, I guess.

Bhargav K
Bhargav K
27:22
Yeah.

AA
Amer Ali
27:27

Okay. If you scroll up. No, Up. Go on the main home.
See, right hand side there used to be a picture which is not visible anymore.

Yeah, yeah. So we just need to have some better picture here of people using sim and eSIM and like that.

Okay.

Bhargav K
Bhargav K
27:54
Which is contrasting with our website.

AA
Amer Ali
27:57
But the , yeah. Yeah. A physical one also, you know, we are not encouraging much of the images of us, which you feel like you have to put a picture there, having our mobiles, eSIM, travelers, or all these combination pictures have to come there.

Okay. And you will write a tag like this, your eSIM ready in two minutes, and 186 countries is must there to attract.


Okay. And one technical question, okay, open Thailand package, let's say we are buying the eSIM.
Okay. Anything, anything. Okay.


Now, we can see that this package of 1GB is purchased by an individual.
Yes. please open the package.

Bhargav K
Bhargav K
28:58
Yeah, any package.

AA
Amer Ali
29:02

Proceed to checkout. I can see that I am able to buy one package.
Let's say, I am selling this to a tour operator.


A tour operator will have a group of 20 to 30 people going in a bus.
Yeah. So I need an option where we can enable, let's say, how many bundles you want.


Now you have enabled it for one bundle. You can buy it and you are getting that.
We need to have that signs in this one saying like I will buy 5 or maybe I will buy a 20.

So that calculation should come in here saying like I will buy for 5 or 10 all in and go.

And all those QR codes should come into one email. That way makes it flexible for buyers, for the tour operators to take this option.


They cannot sit and do keep on complete checkout for 50 passengers on this website like this.
So if we have that bundle of 20, one package to 20, then it becomes easier for the any country explorers to tour operators to just simply pay the money, check out once, take those QR codes, pictures, and they will go and just install it for their customers there.

Bhargav K
Bhargav K
30:46
Okay. So this change is required in the ESIM. Okay.

AA
Amer Ali
30:52
So one field of having one, you know, of. So if we have to enable that one that is fine and second is again you need to stretch this form.

If you see the pay part is again going down, use the right and left spaces and make stretch it right and left so that it will come all in one visible way.

These are the changes for the this week from my side.

Bhargav K
Bhargav K
31:41
Yeah.

AA
Amer Ali
31:43

And one important thing is just please edit the text in the about us.
I will just give you a more polished script later.

But we just. I'm not working with the portway system, so I have sent you a script which needs to be changed in about us.

Okay, welcome, Bhargav. Because I am not working with them, so it will be challenging to have their name into our website.

Bhargav K
Bhargav K
32:20
Okay, yeah. That's it. I think I am done, Bhargav from my side.

AA
Amer Ali
32:47
Bhargav, I know you are not feeling well and I cannot pressurize more and more and insist more.

Bhargav K
Bhargav K
32:55
But we are in need of something.

AA
Amer Ali
33:00
to go in the market. So if you, I know you have multiple things and things, but if you focus and perfect one product, as of now, we can just start boosting it in the market.

Yeah, sure.

Bhargav K
Bhargav K
33:16

I feel like our already EV size and eSIM is already ready for me.
Only just we need to add those multiple things.

I'll do that also and then our eSIM will be fully ready and once EVs also, once I get a response from that team, then I will have a clear picture on that and we'll also make that ready.

Okay.

AA
Amer Ali
33:40

Activities, we have, if you go in the dashboard, admin dashboard of the activities.
Activities booking or activities listing?

Bhargav K
Bhargav K
33:53
Activities listing in the admin dashboard.

AA
Amer Ali
34:00
I'm just want to ensure, please edit one of the activity, just ensuring the supplier email is separate for each activity.

Bhargav K
Bhargav K
34:12
Okay, yeah, it is separate.

AA
Amer Ali
34:15
Perfect, because tomorrow we will go with one activity trading and we will just get that good pricing and then we will start selling that.


Okay. And is there a way that we can enable, disable the activities flashing on the website?
Okay, because tomorrow we will not sell one activity for time being, we will just disable it.

When we are selling, we will enable it.

Bhargav K
Bhargav K
34:43

Okay, if now we just want to remove these activities from showing here.
No, not that one, individual activities.

AA
Amer Ali
34:50

Now you have loaded, in UAE, you have 105 activities. If you can, let's say this activity.
If this attraction is closed for maintenance, so is there an option where we can disable this activity and when they are ready, we will enable it.

Bhargav K
Bhargav K
35:14
Yeah, understood. It shouldn't show in the website for time being. Yeah.

AA
Amer Ali
35:20
Because when we delete, that means we are wiping off all your hard work.

Bhargav K
Bhargav K
35:28

Yeah, we can add a button like here to hide when you click on that.
Those activities will be added.

Yes.

AA
Amer Ali
35:38
And one major, most important thing for our dashboard is if you see the scroll bar, this is very thin.


In a left hand side, the center one scroll bar, both sides you can say.
If you have a little thicker one, both sides, the gray one should be in yellow.

Bhargav K
Bhargav K
35:58
Budget.

AA
Amer Ali
35:58
to side. But referring to on That means other have have The one on the settings which we are changing, it is very thin.

This has to come as a thicker one, fatter one, so that for us it is easy to understand that we are scrolling.

Okay. This is always creating a challenge when using, we have to press one of it and keep playing with the tab, tab, tab, tab to go down.

Bhargav K
Bhargav K
36:28
Okay.

AA
Amer Ali
36:30
So if you fatten both sides, the scrolling bars, then for the website that that is very user friendly for us.

Yeah. Okay, this is what I can observe off. If you have any observation from your point of view, Kasha, please let know so that I hope this is little bit clarifies you for the online discussions.

Other than offlines.

Bhargav K
Bhargav K
37:07
Hello, Kasha.

CK
Chaithanya K
37:10
Yeah, seems to be good actually. Need to understand it better. Yeah, little takes time because both of you are not aware of the market trends which are changing here.

Correct.

AA
Amer Ali
37:24
So that's why I'm like, I'm just giving you the pain points and the solutions as per the market needs here.

Once this website goes in the flow, then you will get to understand like, okay, these are the trends now.

As of now, we are just seeing it as a from the customer point of view, what they want, how they will buy and how will they easily check out.

Yeah. Anything else, Kasha, you want to add? Okay.

CK
Chaithanya K
38:17
Yeah, I'm sorry. So, yeah, so currently, I see this, you can, where is this whole part, actually, you're getting it stored, the backend, you're hosting somewhere or?

Yeah, it is on Hostinger itself, on company Hostinger.

Bhargav K
Bhargav K
38:44
Okay, but we have, we have created a Hostinger, yeah, yeah.

CK
Chaithanya K
38:48
I'm sorry. Are you taking any backup?

Bhargav K
Bhargav K
38:52

Yeah, in the Hostinger, it backs up, in the plan itself, they provide automatic backups.
Aha.

CK
Chaithanya K
39:01

So that is one thing. And Amir, are we using the official email address?
No, Kasha. Why?

AA
Amer Ali
39:14
Because we were just getting started as a SAM. With the speed and current flow, I wasn't sure about which email plan I need to buy so that I can have everyone in the domain email address.

I would like to go if you have the knowledge about the subscription and everything so that we can use in a professional way with professional email addresses.

I am more into how for GoTrips email as a GoTrips like that.

CK
Chaithanya K
39:50
Whichever you propose.

AA
Amer Ali
39:51

Since you have a background of all these experiences, if you can propose, we are okay.
But I didn't have, to be honest, didn't have time for it.

listening. Thank

CK
Chaithanya K
40:01
No, no. I think even last time when we were starting the whole setup, we were actually discussing on it, but because it wasn't much required at that particular time, now we are on a proper stage wherein we will be expanding the solution or getting it offloaded, right, to the production.


So we should use, mean, because you will be contacting our new marketing or sales folks, reach out people.
So it is always good to have the marketing activities ready.


Through marketing, we have to have an official email address. It is always better.
It will look more in a better branding manner.


I agree. agree. There is a mismatch of communication in the marketing also where I am doing it.
Usually, they are asking a few questions.

Your GoTrips or your Ain Al Amir? Because I don't have any GoTrips domain.

AA
Amer Ali
41:06
I use it for the emails or communication like that. When I talk to supplier also, will go as Ain Al Amir when I'm, they are like, okay, you are a travel agency or you are having a GoTrips?

Which company is going?

CK
Chaithanya K
41:20

I'm like, okay, it is better. I've been thinking on it. Good that you highlighted this point.
Now we will just see how you want to propose.

So, I mean, Bhargav, do you have an idea on M365? On what?

Bhargav K
Bhargav K
41:39
Microsoft 365. Microsoft 365.

CK
Chaithanya K
41:42
I mean, like I've used that in the previous companies, but I have no idea on setup.

Bhargav K
Bhargav K
41:47
I mean, it is good for a company setup and everything.

CK
Chaithanya K
41:51
Right. Right.

Bhargav K
Bhargav K
41:53
So you have, I mean, what do you think for our utilization?

CK
Chaithanya K
41:57
So we can have a backup copy over there? As well, in our OneDrive or SharePoint, and we can also have this email addresses as an official email addresses, where we can communicate to each other as well as to the outside world.

Yes, it will be very professional. It's better to go with the GoTrips only because if we're going to message the suppliers for the GoTrips, it's better to go with the GoTrips.

Yeah, we can also have access to Insta, currently we're using WhatsApp, then we can move on to a better communication like Teams or something like that, if you're going with Microsoft.

Bhargav K
Bhargav K
42:35
Correct, correct. So we can use Teams.

CK
Chaithanya K
42:38
That's the right point actually, Ameribhav. Just a second. Okay. Okay. So, then we can also use SharePoint and OneDrive along with the Teams.

Yes, we can use that. Teams is more communicative way and we can also use Teams to connect our meetings as well, like this.


Yes, yes, yes. You don't need to use another recording, taking notes.
The Teams only we can have it. Correct, correct.

Bhargav K
Bhargav K
43:44
I think this is a perfect proposal, Kasha.

AA
Amer Ali
43:46

Even Bhargav is also okay. I am also very much excited about this communication via GoTrips.
Not just with internally, but externally also, I will get...

We image for the things our team is doing. Please drop the next step to you only, how we need to proceed about this one, then we will take it from there.

Bhargav K
Bhargav K
44:18
How many are the active users right now?

CK
Chaithanya K
44:22
You two and who else will be using right now?

AA
Amer Ali
44:26
As of now, we have for go trips, we will have active users as me, Bhargav, Tejasvi, and I can think of five people as of now.

CK
Chaithanya K
44:47
No, we can add up the users any point of time. currently to start off we can actually, I think three users are required from my perspective.

Yeah. It is you both and we have other. So that will make your life easy and then you can also have the sessions over there as Bhargav highlighted about the chat because what happens is you can actually have a secured discussions over there.


WhatsApp is an open platform. You might actually, you're giving out some confidential information to WhatsApp.
But if we're in Teams, it is sort of in a private cloud.

Perfect. So it will be, because now, and then I'll initiate this thing for you guys and I'll share you the ideas.

Next time we can, we can connect using, you can completely utilize it and I'll tell you how to, how to take up a backup and all of this stuff.

Yeah. That works.

Bhargav K
Bhargav K
45:54
Right.

CK
Chaithanya K
45:55
Take care. All the best. And anything from my side? We will just approach.

AA
Amer Ali
46:00
You you on the WhatsApp if something is required, but just waiting for the next steps for these emails and everything formalities.

And I think I am also done.

CK
Chaithanya K
46:11
Bhargav, do you want to add anything other than this?

Bhargav K
Bhargav K
46:15
No, no, nothing else. Everything is clear from my end. Right.

AA
Amer Ali
46:20
Kindly just request you to speed up the changes. I am very much excited for the next level after you make the changes because I need to make a lot of noise in the market.

CK
Chaithanya K
46:36
That's the idea of this.

Bhargav K
Bhargav K
46:39
We are in the process.

AA
Amer Ali
46:41
And another important thing is we will just keep looking for the skill sets for a junior, what is exactly required.

So that I will seek in the market. And you also see who is earned and Kasha will also be able to.

CK
Chaithanya K
46:57
If you post it, what are the skill sets? Yeah, I suppose we need, we need a full-time guy who can actually help out all the fixtures and anything in the back-end, Bhargav.


You should have, you should have someone assisting you. I think it's a high time now.
Yeah, I'll also share like the required skills that are required to manage them to you.

Bhargav K
Bhargav K
47:28
And I'll also find, I'll also see in my circle if there is any good guy who can perfectly align with our goals.

CK
Chaithanya K
47:35
Yeah, yeah, that would be better and it would be good if they can come over to office as well.

Bhargav K
Bhargav K
47:41
We are having our office in Uppal.

CK
Chaithanya K
47:44

Okay. It is near to our metro station, so it'll be very much handy for them too.
So, we'll see.

Bhargav K
Bhargav K
47:52
Fishers would be very much ideal.

CK
Chaithanya K
47:56
Even, even the experienced who are looking out, who haven't figured out what would

Bhargav K
Bhargav K
48:00
Also, that's fine.

CK
Chaithanya K
48:02
can see how they can actually help out to each other.

Bhargav K
Bhargav K
48:08
Share the GD.

CK
Chaithanya K
48:10
I will also look around. We will see. Okay, yeah.

Bhargav K
Bhargav K
48:17
Right. Take care.

CK
Chaithanya K
48:19
Okay, that's great.

AA
Amer Ali
48:21

Thank you both of you for taking our time and inshallah we will do good.
Yeah, this week we just need to lots of results and active participation from each of us.

Bhargav K
Bhargav K
48:34
Yeah.

CK
Chaithanya K
48:36
Thank you so much. Thank you. All Hill to ABC.

AA
Amer Ali
48:44

But this was a very, you know, I don't know how to say it, but amazing.
I didn't notice it.

It happens.

CK
Chaithanya K
48:54
Thank you.

AA
Amer Ali
48:54
Thank you. Bye.

CK
Chaithanya K
48:56
Bye. Bye.
