<!-- SAMPLE DOCUMENT — NOT FINAL LEGAL TEXT. Placeholder content pending real
     contract copy from the legal team. Swap this file's content only —
     nothing else in the app needs to change once the real text is ready. -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; line-height: 1.6; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        h2 { font-size: 13px; margin-top: 20px; margin-bottom: 6px; }
        .sample-banner {
            border: 2px solid #d63638;
            color: #d63638;
            font-weight: bold;
            text-align: center;
            padding: 8px;
            margin-bottom: 20px;
        }
        .party-row { margin-bottom: 4px; }
        .party-row strong { display: inline-block; width: 140px; }
        .signature-block {
            margin-top: 40px;
            border-top: 1px solid #999;
            padding-top: 12px;
        }
    </style>
</head>
<body>
    <div class="sample-banner">SAMPLE DOCUMENT — NOT FINAL LEGAL TEXT</div>

    <h1>International Partner Agreement</h1>
    <p>This agreement governs the business relationship between GoTrips and the partner named below, for partners operating outside the United Arab Emirates.</p>

    <h2>Partner Details</h2>
    <div class="party-row"><strong>Company Name:</strong> {{ $partner->company_name }}</div>
    <div class="party-row"><strong>Contact Name:</strong> {{ $partner->contact_name }}</div>
    <div class="party-row"><strong>Email:</strong> {{ $partner->email }}</div>
    <div class="party-row"><strong>Phone:</strong> {{ $partner->phone }}</div>
    <div class="party-row"><strong>Country:</strong> {{ $partner->country }}</div>

    <h2>Terms (placeholder)</h2>
    <p>[Placeholder clause: scope of partnership, pricing/commission arrangement, term and termination, confidentiality, liability, governing law and jurisdiction, currency/payment terms — to be replaced with the finalized international partner agreement text.]</p>

    <div class="signature-block">
        <p><strong>Signed by:</strong> {{ $partner->signature_full_name }}</p>
        <p><strong>IP Address:</strong> {{ $partner->signature_ip }}</p>
        <p><strong>Signed at:</strong> {{ optional($partner->signed_at)->format('d M Y, H:i') }} UTC</p>
    </div>
</body>
</html>
