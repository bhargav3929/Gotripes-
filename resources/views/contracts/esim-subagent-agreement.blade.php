{{--
  Letterheaded reproduction of GoTrips_B2B_SubAgent_Agreement.pdf, the plain
  Word-exported contract Amer sent 27 Sep 2026 asking for a professional
  letterhead (docs/client-feedback-2026-09-27.md, D5). This is a re-typeset
  copy of his text for visual review — NOT a byte-level edit of his file, and
  not yet published as the live Agent Contract. Proofread against the
  original before publishing (Manager -> Agent Contract) as a new version.
--}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    @include('contracts._letterhead', ['letterheadDocType' => 'B2B Sub-Agent Agreement'])
    <style>
        body { font-size: 11.5px; line-height: 1.55; }
        h1 { font-size: 19px; text-align: center; margin: 0 0 4px; letter-spacing: 0.5px; }
        h1 + .subtitle { text-align: center; font-size: 12px; color: #555; margin-bottom: 22px; }
        h2 { font-size: 13px; color: #8f6a16; border-bottom: 1px solid #e8dcb8; padding-bottom: 3px; margin: 20px 0 8px; }
        h3 { font-size: 11.5px; margin: 12px 0 4px; }
        p { margin: 0 0 8px; }
        ul { margin: 0 0 8px; padding-left: 18px; }
        li { margin-bottom: 4px; }
        table.tiers { width: 100%; border-collapse: collapse; margin: 8px 0 12px; }
        table.tiers th, table.tiers td { border: 1px solid #ddd; padding: 5px 8px; font-size: 11px; text-align: left; }
        table.tiers th { background: #faf6e8; color: #8f6a16; }
        .intro-parties { margin-bottom: 18px; }
    </style>
</head>
<body>
    <h1>B2B SUB-AGENT AGREEMENT</h1>
    <div class="subtitle">(eSIM Services)</div>

    <p class="intro-parties">
        This Sub-Agent Agreement (the &ldquo;Agreement&rdquo;) is entered into as of
        September 22, 2026 (the &ldquo;Effective Date&rdquo;), by and between:
    </p>
    <ol>
        <li>GoTrips, a travel and connectivity solutions provider headquartered in Dubai, United Arab
            Emirates, with contact email admin@gotrips.ai (hereinafter referred to as the &ldquo;Company&rdquo;), and</li>
        <li>The entity or individual registering as a sub-agent via the GoTrips Partner Program platform
            (hereinafter referred to as the &ldquo;Sub-Agent&rdquo;).</li>
    </ol>

    <h2>1. Purpose &amp; Appointment</h2>
    <p>The Company provides digital travel eSIM profiles covering over 186 countries. The Company hereby
        appoints the Sub-Agent, and the Sub-Agent accepts the appointment, as a non-exclusive commercial
        sub-agent to market, promote, and sell GoTrips eSIM data plans to end-users (travellers).</p>

    <h2>2. Sub-Agent Obligations &amp; Sales Process</h2>
    <ul>
        <li><strong>Product Presentation:</strong> The Sub-Agent shall market the eSIMs accurately, ensuring
            customers know they are purchasing a digital data-only profile delivered via QR code.</li>
        <li><strong>Compatibility Warning:</strong> The Sub-Agent must explicitly instruct end-users to verify
            device compatibility (carrier-unlocked and eSIM-capable devices) prior to final purchase.</li>
        <li><strong>Delivery Mechanism:</strong> The Sub-Agent acknowledges that QR codes, SM-DP+ addresses, and
            activation codes are generated instantly by the Company and delivered to the designated email.</li>
    </ul>

    <h2>3. Commission, Pricing, and Payments</h2>
    <h3>3.1 Commission Structure &amp; Tiers</h3>
    <p>The Sub-Agent shall earn a commission based on the net sales volume (excluding taxes and payment
        gateway transaction fees) generated through their unique Partner ID tracking system within a single
        calendar month. The commission percentage applies retroactively to all sales within that month as
        soon as a tier threshold is met:</p>
    <table class="tiers">
        <tr><th>Tier</th><th>Monthly Net Sales Volume (AED)</th><th>Commission Percentage</th></tr>
        <tr><td>Tier 1 (Base)</td><td>AED 0.00 &ndash; 5,000.00</td><td>10%</td></tr>
        <tr><td>Tier 2 (Silver)</td><td>AED 5,000.01 &ndash; 15,000.00</td><td>15%</td></tr>
        <tr><td>Tier 3 (Gold)</td><td>AED 15,000.01 &ndash; 30,000.00</td><td>18%</td></tr>
        <tr><td>Tier 4 (Platinum)</td><td>AED 30,000.01 and above</td><td>20%</td></tr>
    </table>

    <h3>3.2 Minimum Payout Thresholds</h3>
    <ul>
        <li><strong>Standard Threshold:</strong> The minimum threshold for a monthly commission payout is AED 200.00.</li>
        <li><strong>Rollover Policy:</strong> If the Sub-Agent's earned commission for a calendar month is below
            AED 200.00, the balance will automatically roll over to the next billing cycle. The accumulated
            balance will be paid out during the standard cycle of the month in which the total balance meets
            or exceeds the minimum threshold.</li>
        <li><strong>Inactivity Clause:</strong> If a Sub-Agent account remains inactive (generating zero sales)
            for more than six (6) consecutive months, GoTrips reserves the right to charge a maintenance fee
            or clear the remaining balance below the threshold after giving a 14-day notice.</li>
    </ul>

    <h3>3.3 Banking &amp; Transaction Fees</h3>
    <ul>
        <li><strong>Direct Bank Transfer:</strong> Payouts will be processed via electronic funds transfer (EFT)
            directly to the bank details registered in the Sub-Agent Portal.</li>
        <li><strong>Transfer Costs:</strong> GoTrips covers the transfer fees for local banks within the United
            Arab Emirates. For international cross-border transfers or intermediary bank fees, the costs will
            be deducted from the Sub-Agent's final payout amount.</li>
    </ul>

    <h2>4. Cross-Border &amp; Currency Conversion Clauses</h2>
    <h3>4.1 Base Currency and Exchange Rate Anchor</h3>
    <ul>
        <li><strong>Base Tracking Currency:</strong> All retail transactions, net sales calculations, and
            commission balances are tracked, recorded, and anchored in United Arab Emirates Dirhams (AED)
            inside the GoTrips Partner Platform.</li>
        <li><strong>Exchange Rate Source:</strong> For foreign sales or international payouts requiring
            conversion, the exchange rate will be calculated using the prevailing mid-market rate provided by
            xe.com (or the Company's processing bank) at 11:59 PM UAE time on the final calendar day of the
            applicable billing month.</li>
    </ul>

    <h3>4.2 International Payouts &amp; Local Currency Remittance</h3>
    <ul>
        <li><strong>Supported Payout Currencies:</strong> International Sub-Agents may request payouts
            exclusively in US Dollars (USD), Euros (EUR), or British Pounds (GBP). No other foreign currencies
            are supported.</li>
        <li><strong>Conversion and Risk Mitigation:</strong> The conversion from AED to the requested
            international currency will take place at the exact time of transaction processing by the
            Company's bank. The Company assumes no responsibility for foreign exchange fluctuations between
            the close of the billing cycle and the actual execution date of the bank transfer.</li>
        <li><strong>Conversion Fee:</strong> A standard 2.5% currency conversion and administrative processing
            fee will be applied to the converted amount on all cross-border payouts to cover intermediary
            banking conversion spreads.</li>
    </ul>

    <h3>4.3 International Banking Fees &amp; Deductions</h3>
    <ul>
        <li><strong>SHA (Shared) Transfer Fees:</strong> All international wire transfers (SWIFT) will be
            processed on a &ldquo;SHA&rdquo; basis. GoTrips will pay its sending bank fees, while the Sub-Agent
            is entirely responsible for any intermediary bank fees, correspondent bank routing fees, or
            receiving bank charges applied by their local financial institution.</li>
        <li><strong>Minimum International Payout:</strong> Due to high international wire transfer overheads,
            the minimum payout threshold for cross-border bank transfers outside the UAE is raised to an
            equivalent of AED 500.00. Any balance below this amount will seamlessly roll over to the next
            billing cycle.</li>
    </ul>

    <h2>5. Technical Support &amp; Customer Service</h2>
    <ul>
        <li><strong>Tier 1 Support:</strong> The Sub-Agent is responsible for standard first-line customer
            inquiries regarding checkout or basic purchase flows.</li>
        <li><strong>Tier 2 Support:</strong> For deep network issues, activation failures, or eSIM
            troubleshooting, the Sub-Agent shall direct customers to GoTrips Support via admin@gotrips.ai or
            the support ticketing channel. The Company aims to provide initial responses to escalated tickets
            within 4 hours during standard operational hours.</li>
    </ul>

    <h2>6. Refunds &amp; Troubleshooting Restrictions</h2>
    <ul>
        <li><strong>No Unilateral Deletions:</strong> The Sub-Agent must explicitly instruct customers not to
            delete an installed eSIM profile while experiencing technical difficulties.</li>
        <li><strong>Refund Eligibility:</strong> Refunds will not be issued if an eSIM is deleted prematurely
            without written authorization from GoTrips, or if the end-user's device is found to be
            carrier-locked.</li>
    </ul>

    <h2>7. Intellectual Property &amp; Marketing</h2>
    <p>The Company grants the Sub-Agent a non-exclusive, non-transferable, revocable license to use GoTrips
        branding, product descriptions, and installation guides solely for the duration of this Agreement to
        drive sales.</p>

    <h2>8. Term and Termination</h2>
    <p>This Agreement remains effective until terminated by either party. Either party may terminate this
        Agreement at any time, with or without cause, by giving seven (7) days written notice via email. Upon
        termination, any outstanding commissions lawfully earned prior to the termination date will be
        settled.</p>

    <h2>9. Governing Law &amp; Jurisdiction</h2>
    <p>This Agreement shall be governed by, interpreted, and construed in accordance with the laws of Dubai,
        United Arab Emirates. Any disputes arising from this contract that cannot be resolved amicably shall
        be subject to the exclusive jurisdiction of the Dubai Courts.</p>
</body>
</html>
