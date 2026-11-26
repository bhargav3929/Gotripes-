{{--
  B2B Tenant Portal Agreement, generated from Manager -> Tenant Agreements
  (ManagerTenantAgreementsController). Asked for by Amer on 26 Nov 2026:
  "leave the monthly rent and percentage to sell as blank.. we fill it
  manually case by case once the pdf is generated in our portal and sent via
  whatsapp". Rent and percentage are therefore always printed as blank lines;
  there is no field for them anywhere.

  Clauses describe how the platform actually works (tenant subdomain/domain
  and branding, Company::AVAILABLE_FEATURES modules, the /suppliers rate form
  with staff review before anything goes live, payments through GoTrips'
  Nomod account with the tenant's share credited to a balance it withdraws).
  Change them here if that changes.
--}}
@php
    $p = $agreement->partner;
    $blank = fn (int $n = 18) => str_repeat('_', $n);
    $value = fn (?string $v, int $n = 28) => filled($v) ? e($v) : $blank($n);

    $gtLegal = $platform?->business_name ?: 'Ayn Al Amir Tourism Trips Organizing LLC';
    $gtEmail = $platform?->email ?: 'admin@gotrips.ai';

    $start = \Illuminate\Support\Carbon::parse($p['start_date']);
    $termMonths = (int) $p['term_months'];
    $end = $start->copy()->addMonthsNoOverflow($termMonths)->subDay();

    $moduleNames = collect($p['modules'] ?? [])->map(fn ($m) => $modules[$m] ?? $m)->values();
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $agreement->reference }} B2B Tenant Portal Agreement</title>
    @include('contracts._letterhead', ['letterheadDocType' => 'B2B Tenant Portal Agreement'])
    <style>
        body { font-size: 10.5px; line-height: 1.55; }
        h1 { font-size: 18px; text-align: center; margin: 0 0 4px; letter-spacing: 0.5px; }
        .subtitle { text-align: center; font-size: 11px; color: #555; margin-bottom: 4px; }
        .ref { text-align: center; font-size: 10px; color: #8f6a16; margin-bottom: 18px; letter-spacing: 0.5px; }
        h2 { font-size: 12px; color: #8f6a16; border-bottom: 1px solid #e8dcb8; padding-bottom: 3px; margin: 16px 0 7px; page-break-after: avoid; }
        p { margin: 0 0 6px; }
        .cl { margin: 0 0 5px; padding-left: 34px; position: relative; }
        .cl .n { position: absolute; left: 0; top: 0; font-weight: bold; color: #444; }
        ul { margin: 2px 0 6px; padding-left: 40px; }
        li { margin-bottom: 2px; }
        .blank { font-weight: bold; letter-spacing: 0.5px; }

        table.parties { width: 100%; border-collapse: collapse; margin: 6px 0 12px; }
        table.parties td { width: 50%; vertical-align: top; border: 1px solid #e3d7b0; padding: 8px 10px; background: #fdfbf4; }
        table.parties .who { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #8f6a16; font-weight: bold; margin-bottom: 4px; }
        table.parties .name { font-size: 11.5px; font-weight: bold; margin-bottom: 4px; }
        table.parties .row { font-size: 9.5px; color: #333; margin-bottom: 1px; }
        table.parties .row span { color: #888; }

        table.sched { width: 100%; border-collapse: collapse; margin: 8px 0 12px; }
        table.sched th, table.sched td { border: 1px solid #ddd; padding: 7px 9px; font-size: 10.5px; text-align: left; vertical-align: top; }
        table.sched th { background: #faf6e8; color: #8f6a16; width: 34%; }
        table.sched td.fill { font-size: 12px; padding: 12px 9px; }

        table.sign { width: 100%; border-collapse: collapse; margin-top: 10px; page-break-inside: avoid; }
        table.sign td { width: 50%; vertical-align: top; padding: 0 10px 0 0; }
        table.sign td + td { padding: 0 0 0 10px; }
        .sign-box { border: 1px solid #e3d7b0; padding: 10px 12px; }
        .sign-box .who { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #8f6a16; font-weight: bold; }
        .sign-box .co { font-size: 10.5px; font-weight: bold; margin: 3px 0 10px; }
        .sign-line { margin: 0 0 12px; font-size: 10px; }
        .sign-line .lbl { color: #666; display: block; margin-bottom: 2px; }
        .sign-line .ln { border-bottom: 1px solid #999; height: 16px; }
        .sign-line .ln.tall { height: 34px; }
        .stamp { border: 1px dashed #bbb; height: 70px; text-align: center; color: #bbb; font-size: 9px; padding-top: 28px; }
        .new-page { page-break-before: always; }
        .initials { margin-top: 18px; font-size: 10px; }
        .note { font-size: 9.5px; color: #666; }
    </style>
</head>
<body>
    <h1>B2B TENANT PORTAL AGREEMENT</h1>
    <div class="subtitle">White-label travel portal on the GoTrips platform</div>
    <div class="ref">Reference {{ $agreement->reference }} &middot; Prepared {{ $agreement->created_at->format('d F Y') }}</div>

    <p>This B2B Tenant Portal Agreement (the &ldquo;Agreement&rdquo;) is made on the date of the last signature below, and takes effect on the Start Date stated in Schedule 1, between:</p>

    <table class="parties">
        <tr>
            <td>
                <div class="who">GoTrips</div>
                <div class="name">{{ $gtLegal }}</div>
                <div class="row"><span>Trading as:</span> GoTrips (gotrips.ai)</div>
                <div class="row"><span>Address:</span> {!! $value($platform?->address, 34) !!}</div>
                <div class="row"><span>Trade licence no.:</span> {{ $blank(22) }}</div>
                @if($platform?->tax_id)
                    <div class="row"><span>TRN:</span> {{ $platform->tax_id }}</div>
                @endif
                <div class="row"><span>Email:</span> {{ $gtEmail }}</div>
                <div class="row" style="margin-top:4px;">(&ldquo;<strong>GoTrips</strong>&rdquo;)</div>
            </td>
            <td>
                <div class="who">Partner</div>
                <div class="name">{{ $p['legal_name'] }}</div>
                <div class="row"><span>Trade licence no.:</span> {!! $value($p['trade_licence'] ?? null, 22) !!}</div>
                <div class="row"><span>Address:</span> {{ $p['address'] }}, {{ $p['country'] }}</div>
                <div class="row"><span>Contact:</span> {{ $p['contact_name'] }}@if(!empty($p['contact_title'])), {{ $p['contact_title'] }}@endif</div>
                <div class="row"><span>Email:</span> {{ $p['email'] }}</div>
                <div class="row"><span>Phone / WhatsApp:</span> {{ $p['phone'] }}</div>
                <div class="row" style="margin-top:4px;">(the &ldquo;<strong>Partner</strong>&rdquo;)</div>
            </td>
        </tr>
    </table>

    <p>GoTrips and the Partner are each a &ldquo;Party&rdquo; and together the &ldquo;Parties&rdquo;.</p>

    <h2>1. Definitions</h2>
    <div class="cl"><span class="n">1.1</span><strong>Platform</strong> means the GoTrips online travel platform, including its websites, manager portal, booking flows and payment integration.</div>
    <div class="cl"><span class="n">1.2</span><strong>Tenant Portal</strong> means the Partner&rsquo;s own branded website and manager portal running on the Platform, as set out in clause 2.</div>
    <div class="cl"><span class="n">1.3</span><strong>Modules</strong> means the Platform services switched on for the Tenant Portal, listed in Schedule 1.</div>
    <div class="cl"><span class="n">1.4</span><strong>Customer</strong> means any person who makes an enquiry or a Booking through the Tenant Portal.</div>
    <div class="cl"><span class="n">1.5</span><strong>Booking</strong> means a confirmed and paid order for any service made through the Tenant Portal, and <strong>Gross Booking Value</strong> means the total amount paid by the Customer for it, excluding VAT.</div>
    <div class="cl"><span class="n">1.6</span><strong>Partner Content</strong> means the tour packages, hotel and room rates, tour prices, extras, itineraries, descriptions, photos, logos and other material the Partner supplies.</div>
    <div class="cl"><span class="n">1.7</span><strong>Monthly Portal Rent</strong> and <strong>Sales Percentage</strong> mean the amounts stated in Schedule 1.</div>

    <h2>2. Scope of Services</h2>
    <div class="cl"><span class="n">2.1</span>GoTrips grants the Partner a non-exclusive, non-transferable right to operate a Tenant Portal on the Platform for the Term, on the terms of this Agreement.</div>
    <div class="cl"><span class="n">2.2</span>The Tenant Portal carries the Partner&rsquo;s name, logo and brand colours. It is served on a GoTrips sub-domain (for example <em>partnername.gotrips.ai</em>) or, once the Partner has pointed its domain to the Platform as GoTrips instructs, on the Partner&rsquo;s own domain.</div>
    <div class="cl"><span class="n">2.3</span>GoTrips will enable the Modules listed in Schedule 1. Modules that are not enabled are not shown on the Tenant Portal. Modules may be added or removed by written agreement, including by email or WhatsApp.</div>
    <div class="cl"><span class="n">2.4</span>The Partner receives manager logins to its Tenant Portal to view bookings and enquiries, manage its team, review its balance and request withdrawals.</div>
    <div class="cl"><span class="n">2.5</span><strong>Self-service tour packages.</strong> The Partner may submit its tour packages, hotels, room prices, tour prices, extras, itineraries and photos through the GoTrips supplier rate form, using an access code issued by GoTrips. The same code may be used again to send updated rates.</div>
    <div class="cl"><span class="n">2.6</span><strong>Review before publishing.</strong> Submitted rates and content do not go live automatically. GoTrips reviews each submission and applies it to the relevant package before it is shown to Customers, and may ask the Partner to correct or complete a submission, or decline to publish it.</div>
    <div class="cl"><span class="n">2.7</span>Some Modules (for example visas and eSIMs) are fulfilled through third-party suppliers or government authorities. Approval of a visa or similar application is decided by the relevant authority, not by GoTrips.</div>

    <h2>3. Fees</h2>
    <div class="cl"><span class="n">3.1</span><strong>Monthly Portal Rent.</strong> The Partner shall pay GoTrips a Monthly Portal Rent of <span class="blank">AED {{ $blank(16) }}</span> per month, as stated in Schedule 1, payable monthly in advance from the Start Date.</div>
    <div class="cl"><span class="n">3.2</span><strong>Sales Percentage.</strong> A Sales Percentage of <span class="blank">{{ $blank(10) }} %</span> of the Gross Booking Value applies to each Booking made through the Tenant Portal, as stated in Schedule 1.</div>
    <div class="cl"><span class="n">3.3</span>All amounts are in UAE Dirhams (AED) and exclude VAT. VAT at the prevailing UAE rate (currently 5%) is added where applicable, and GoTrips issues tax invoices accordingly.</div>
    <div class="cl"><span class="n">3.4</span>GoTrips invoices the Monthly Portal Rent monthly. Invoices are payable within seven (7) days of the invoice date by bank transfer or another method GoTrips accepts.</div>
    <div class="cl"><span class="n">3.5</span>If an amount remains unpaid fourteen (14) days after its due date, GoTrips may, after giving written notice, suspend the Tenant Portal until it is paid. Confirmed Bookings are still honoured during a suspension.</div>
    <div class="cl"><span class="n">3.6</span>The fees in Schedule 1 may only be changed by a written amendment signed or initialled by both Parties.</div>

    <h2>4. Payments and Settlement</h2>
    <div class="cl"><span class="n">4.1</span>Online payments made by Customers on the Tenant Portal are collected through GoTrips&rsquo; payment gateway account. GoTrips receives these payments and holds the amount due to the Partner until it is settled under this clause.</div>
    <div class="cl"><span class="n">4.2</span>For each paid Booking, the amount due to the Partner, calculated in accordance with Schedule 1, is recorded against the Partner&rsquo;s balance and is visible in the Partner&rsquo;s manager portal.</div>
    <div class="cl"><span class="n">4.3</span>Amounts may be held for a holding period before they become available for withdrawal, to cover refunds, cancellations and chargebacks.</div>
    <div class="cl"><span class="n">4.4</span>The Partner may request a withdrawal of its available balance through the manager portal to the bank account it has registered there. GoTrips settles approved withdrawal requests at least once each calendar month. Charges for international transfers are borne by the Partner.</div>
    <div class="cl"><span class="n">4.5</span>If a Booking is refunded or charged back, the related amount is reversed from the Partner&rsquo;s balance; if it has already been paid out, GoTrips may deduct it from later amounts due or invoice the Partner for it.</div>
    <div class="cl"><span class="n">4.6</span>GoTrips may set off any overdue Monthly Portal Rent or other amount owed by the Partner against the Partner&rsquo;s balance, and will show any set-off in the settlement statement.</div>

    <h2>5. Partner Obligations</h2>
    <div class="cl"><span class="n">5.1</span>Hold and keep valid every trade, tourism and other licence required for the services it offers, and provide copies to GoTrips on request.</div>
    <div class="cl"><span class="n">5.2</span>Keep its rates, availability, inclusions, exclusions and cancellation terms accurate and up to date, and send updated rates before they take effect (for example for seasonal changes).</div>
    <div class="cl"><span class="n">5.3</span>Honour every confirmed Booking for its own tour packages at the price and on the terms shown to the Customer at the time of booking, and provide the services with reasonable skill and care.</div>
    <div class="cl"><span class="n">5.4</span>Own, or hold a licence to use, all Partner Content, including photos and descriptions. The Partner grants GoTrips a non-exclusive, royalty-free licence to host, format, resize and display Partner Content on the Platform for the Term.</div>
    <div class="cl"><span class="n">5.5</span>Handle Customer questions and complaints about its own tour packages promptly, and co-operate with GoTrips on any Customer issue.</div>
    <div class="cl"><span class="n">5.6</span>Keep its manager logins confidential. The Partner is responsible for everything done under its logins and must tell GoTrips immediately of any unauthorised use.</div>
    <div class="cl"><span class="n">5.7</span>Not use the Tenant Portal for anything unlawful, misleading or harmful to Customers or to the Platform, and comply with all applicable laws, including UAE consumer protection law.</div>

    <h2>6. GoTrips Obligations</h2>
    <div class="cl"><span class="n">6.1</span>Set up the Tenant Portal with the Partner&rsquo;s branding and the Modules in Schedule 1, and connect the sub-domain or the Partner&rsquo;s own domain.</div>
    <div class="cl"><span class="n">6.2</span>Host, maintain and secure the Platform, and use reasonable efforts to keep the Tenant Portal available. Planned maintenance will, where practical, take place outside peak hours. GoTrips does not guarantee uninterrupted or error-free operation.</div>
    <div class="cl"><span class="n">6.3</span>Review the Partner&rsquo;s rate and content submissions within a reasonable time and publish those it approves.</div>
    <div class="cl"><span class="n">6.4</span>Provide the Partner with support during UAE business hours through the Platform&rsquo;s support channel, email or WhatsApp.</div>
    <div class="cl"><span class="n">6.5</span>Process Customer payments and settle amounts due to the Partner in accordance with clause 4.</div>

    <h2>7. Pricing and Rate Updates</h2>
    <div class="cl"><span class="n">7.1</span>The Partner submits net rates. The price shown to Customers is calculated by the Platform from those rates plus any margin set on the package, as agreed between the Parties.</div>
    <div class="cl"><span class="n">7.2</span>Updated rates take effect once GoTrips has reviewed and applied them. Bookings confirmed before an update keep the price at which they were booked.</div>
    <div class="cl"><span class="n">7.3</span>The Partner is responsible for the accuracy of the rates it submits. Where a published price is obviously wrong, either Party may ask for it to be corrected, and GoTrips may withdraw the affected package until it is.</div>

    <h2>8. Data Protection and Confidentiality</h2>
    <div class="cl"><span class="n">8.1</span>Each Party shall comply with UAE Federal Decree-Law No. 45 of 2021 on the Protection of Personal Data and any other applicable data protection law.</div>
    <div class="cl"><span class="n">8.2</span>Customer personal data (including names, contact details and passport details) may be used only to perform Bookings, provide support and meet legal obligations. Neither Party may sell it or use it for unrelated marketing without the Customer&rsquo;s consent.</div>
    <div class="cl"><span class="n">8.3</span>Each Party shall keep appropriate security measures in place and notify the other without undue delay of any personal data breach affecting the Tenant Portal.</div>
    <div class="cl"><span class="n">8.4</span>Each Party shall keep confidential the other&rsquo;s non-public business information, including the commercial terms in Schedule 1. This obligation survives termination of this Agreement.</div>

    <h2>9. Intellectual Property</h2>
    <div class="cl"><span class="n">9.1</span>The Platform, its software, design and the GoTrips name and marks remain the property of GoTrips. Nothing in this Agreement transfers them to the Partner.</div>
    <div class="cl"><span class="n">9.2</span>Partner Content and the Partner&rsquo;s name and marks remain the property of the Partner, subject to the licence in clause 5.4.</div>
    <div class="cl"><span class="n">9.3</span>The Partner shall not copy, resell, reverse-engineer or allow third parties to access the Platform other than as a Customer of the Tenant Portal.</div>

    <h2>10. Term and Termination</h2>
    <div class="cl"><span class="n">10.1</span>This Agreement starts on the Start Date and continues for the Term stated in Schedule 1. It then renews automatically for successive periods of the same length unless either Party gives at least thirty (30) days&rsquo; written notice before the end of the current period.</div>
    <div class="cl"><span class="n">10.2</span>Either Party may terminate this Agreement by written notice if the other commits a material breach and does not remedy it within fourteen (14) days of being asked to in writing.</div>
    <div class="cl"><span class="n">10.3</span>GoTrips may suspend the Tenant Portal immediately where it reasonably suspects fraud, unlawful activity or a security risk, and will inform the Partner promptly.</div>
    <div class="cl"><span class="n">10.4</span>On termination: the Tenant Portal is deactivated; Bookings already confirmed are honoured; a final settlement is made of amounts due to the Partner, less any amounts the Partner owes; and on request made within thirty (30) days GoTrips provides the Partner with an export of its own bookings data.</div>

    <h2>11. Liability</h2>
    <div class="cl"><span class="n">11.1</span>Neither Party is liable to the other for any indirect or consequential loss, or for loss of profit, revenue or goodwill.</div>
    <div class="cl"><span class="n">11.2</span>GoTrips&rsquo; total liability under this Agreement in any twelve (12) month period is limited to the Monthly Portal Rent paid by the Partner in that period.</div>
    <div class="cl"><span class="n">11.3</span>The Partner is responsible for, and shall indemnify GoTrips against claims arising from, its own tour packages and services, Partner Content, and its breach of this Agreement or of applicable law.</div>
    <div class="cl"><span class="n">11.4</span>Nothing in this Agreement limits liability for fraud, wilful misconduct or any liability that cannot be limited by law.</div>

    <h2>12. General</h2>
    <div class="cl"><span class="n">12.1</span><strong>Force majeure.</strong> Neither Party is liable for delay or failure caused by events beyond its reasonable control, including outages of third-party providers, government action or natural events.</div>
    <div class="cl"><span class="n">12.2</span><strong>Notices.</strong> Notices under this Agreement may be given by email to the addresses above. Day-to-day matters may also be handled on WhatsApp.</div>
    <div class="cl"><span class="n">12.3</span><strong>Assignment.</strong> The Partner may not assign or transfer this Agreement without GoTrips&rsquo; prior written consent.</div>
    <div class="cl"><span class="n">12.4</span><strong>Entire agreement.</strong> This Agreement, with Schedule 1, is the entire agreement between the Parties on its subject and replaces any earlier discussions. Amendments must be in writing and signed or initialled by both Parties.</div>
    <div class="cl"><span class="n">12.5</span><strong>Counterparts.</strong> This Agreement may be signed in counterparts, and a signed and stamped copy sent by email or WhatsApp is as valid as the original.</div>

    <h2>13. Governing Law and Jurisdiction</h2>
    <div class="cl"><span class="n">13.1</span>This Agreement is governed by the laws of the United Arab Emirates as applied in the Emirate of Abu Dhabi. Any dispute that cannot be settled amicably within thirty (30) days shall be submitted to the exclusive jurisdiction of the courts of Abu Dhabi.</div>

    <h2>Signatures</h2>
    <p>Signed by the authorised representatives of the Parties.</p>
    <table class="sign">
        <tr>
            <td>
                <div class="sign-box">
                    <div class="who">For GoTrips</div>
                    <div class="co">{{ $gtLegal }}</div>
                    <div class="sign-line"><span class="lbl">Name</span><div class="ln"></div></div>
                    <div class="sign-line"><span class="lbl">Title</span><div class="ln"></div></div>
                    <div class="sign-line"><span class="lbl">Signature</span><div class="ln tall"></div></div>
                    <div class="sign-line"><span class="lbl">Date</span><div class="ln"></div></div>
                    <div class="stamp">Company stamp</div>
                </div>
            </td>
            <td>
                <div class="sign-box">
                    <div class="who">For the Partner</div>
                    <div class="co">{{ $p['legal_name'] }}</div>
                    <div class="sign-line"><span class="lbl">Name</span><div class="ln"></div></div>
                    <div class="sign-line"><span class="lbl">Title</span><div class="ln"></div></div>
                    <div class="sign-line"><span class="lbl">Signature</span><div class="ln tall"></div></div>
                    <div class="sign-line"><span class="lbl">Date</span><div class="ln"></div></div>
                    <div class="stamp">Company stamp</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="new-page"></div>
    <h1 style="font-size:15px;">SCHEDULE 1 &mdash; COMMERCIAL TERMS</h1>
    <div class="ref">Forms part of Agreement {{ $agreement->reference }}</div>

    <table class="sched">
        <tr><th>Partner</th><td>{{ $p['legal_name'] }}, {{ $p['country'] }}</td></tr>
        <tr><th>Start Date</th><td>{{ $start->format('d F Y') }}</td></tr>
        <tr><th>Term</th><td>{{ $terms[$termMonths] ?? $termMonths . ' months' }} (until {{ $end->format('d F Y') }}), renewing under clause 10.1</td></tr>
        <tr><th>Tenant Portal address</th><td>{!! $value($p['portal_domain'] ?? null, 34) !!}</td></tr>
        <tr><th>Modules enabled</th><td>{!! $moduleNames->isNotEmpty() ? e($moduleNames->implode(', ')) : $blank(40) !!}</td></tr>
        <tr><th>Monthly Portal Rent</th><td class="fill"><span class="blank">AED {{ $blank(18) }}</span> per month, excluding VAT</td></tr>
        <tr><th>Sales Percentage</th><td class="fill"><span class="blank">{{ $blank(10) }} %</span> of the Gross Booking Value of each Booking, excluding VAT</td></tr>
        <tr><th>Settlement</th><td>Partner&rsquo;s available balance paid on withdrawal request, at least monthly (clause 4.4)</td></tr>
        <tr><th>Other agreed terms</th><td class="fill">{{ $blank(60) }}<br><br>{{ $blank(60) }}<br><br>{{ $blank(60) }}</td></tr>
    </table>

    <p class="note">The Monthly Portal Rent and Sales Percentage are completed by hand and initialled by both Parties. Amounts exclude VAT, which is added where applicable (clause 3.3).</p>

    <table class="sign" style="margin-top:22px;">
        <tr>
            <td><div class="sign-line"><span class="lbl">Initials for GoTrips</span><div class="ln tall"></div></div></td>
            <td><div class="sign-line"><span class="lbl">Initials for the Partner</span><div class="ln tall"></div></div></td>
        </tr>
    </table>
</body>
</html>
