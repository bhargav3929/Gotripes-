{{-- The signed record for a contract a manager uploaded as a PDF. We cannot
     write inside that file, so this certificate stands beside it and names
     the version and the hash of the exact bytes the signer was shown. --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; line-height: 1.6; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        .meta { font-size: 10px; color: #666; margin-bottom: 20px; }
        h2 { font-size: 13px; margin: 24px 0 8px; }
        .party-row { margin-bottom: 4px; }
        .party-row strong { display: inline-block; width: 150px; }
        .hash { font-family: DejaVu Sans Mono, monospace; font-size: 9px; word-break: break-all; }
        .note { margin-top: 28px; border-top: 1px solid #999; padding-top: 10px; font-size: 10px; color: #444; }
    </style>
</head>
<body>
    <h1>Certificate of Signature</h1>
    <div class="meta">Issued {{ optional($application->signed_at)->format('d M Y, H:i') }} UTC</div>

    <h2>Contract signed</h2>
    <div class="party-row"><strong>Title:</strong> {{ $contract->title }}</div>
    <div class="party-row"><strong>Version:</strong> {{ $contract->version }}</div>
    <div class="party-row"><strong>File:</strong> {{ $contract->file_name }}</div>
    <div class="party-row"><strong>SHA-256:</strong></div>
    <div class="hash">{{ $contract->content_hash }}</div>

    <h2>Signed by</h2>
    <div class="party-row"><strong>Name:</strong> {{ $application->signature_full_name }}</div>
    <div class="party-row"><strong>For:</strong> {{ $application->company_name }}</div>
    <div class="party-row"><strong>Email:</strong> {{ $application->email }}</div>
    <div class="party-row"><strong>Trade licence:</strong> {{ $application->trade_license_number }}</div>
    <div class="party-row"><strong>IP address:</strong> {{ $application->signature_ip }}</div>
    <div class="party-row"><strong>Signed at:</strong> {{ optional($application->signed_at)->format('d M Y, H:i') }} UTC</div>

    <div class="note">
        The signer confirmed on screen that they had read and agreed to the
        contract named above before submitting their application. This
        certificate is evidence of that agreement and should be kept with the
        contract file whose hash it records.
    </div>
</body>
</html>
