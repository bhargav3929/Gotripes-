{{-- The signed copy of a contract a manager pasted as text: the contract
     itself, then the signature block, in one file. Rendered by
     AgentContractService at registration; never shown in a browser. --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; line-height: 1.6; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        .meta { font-size: 10px; color: #666; margin-bottom: 18px; }
        .contract-body { white-space: pre-wrap; }
        .party-row { margin-bottom: 4px; }
        .party-row strong { display: inline-block; width: 150px; }
        .signature-block { margin-top: 36px; border-top: 1px solid #999; padding-top: 12px; }
        .signature-block h2 { font-size: 13px; margin: 0 0 8px; }
    </style>
</head>
<body>
    <h1>{{ $contract->title }}</h1>
    <div class="meta">Version {{ $contract->version }} · Signed {{ optional($application->signed_at)->format('d M Y, H:i') }} UTC</div>

    <div class="contract-body">{{ $contract->body }}</div>

    <div class="signature-block">
        <h2>Signature</h2>
        <div class="party-row"><strong>Signed by:</strong> {{ $application->signature_full_name }}</div>
        <div class="party-row"><strong>For:</strong> {{ $application->company_name }}</div>
        <div class="party-row"><strong>Email:</strong> {{ $application->email }}</div>
        <div class="party-row"><strong>Trade licence:</strong> {{ $application->trade_license_number }}</div>
        <div class="party-row"><strong>IP address:</strong> {{ $application->signature_ip }}</div>
        <div class="party-row"><strong>Signed at:</strong> {{ optional($application->signed_at)->format('d M Y, H:i') }} UTC</div>
        <div class="party-row"><strong>Document hash:</strong> {{ $contract->content_hash }}</div>
    </div>
</body>
</html>
