<!DOCTYPE html>
<html>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <div style="max-width:560px;margin:0 auto;padding:24px 16px;">
        <div style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #ececec;">
            <div style="background:#0f172a;padding:22px 28px;">
                <div style="color:#FFD700;font-size:18px;font-weight:700;letter-spacing:-0.01em;">Application Received</div>
            </div>

            <div style="padding:24px 28px;">
                <p style="margin:0 0 14px;font-size:14px;line-height:1.6;color:#374151;">
                    Hi {{ $application->name }},
                </p>
                <p style="margin:0 0 14px;font-size:14px;line-height:1.6;color:#374151;">
                    Thanks for applying to become a GoTrips agent. We've received your application and trade license.
                </p>
                <p style="margin:0 0 14px;font-size:14px;line-height:1.6;color:#374151;">
                    Your application is now under review. We'll email you as soon as a decision is made.
                </p>
                @if($generatedPassword)
                    <p style="margin:0 0 8px;font-size:14px;line-height:1.6;color:#374151;">
                        We've set a temporary password for your account — you'll use this to log in once approved:
                    </p>
                    <p style="margin:0 0 14px;">
                        <span style="display:inline-block;background:#f4f4f5;border:1px solid #e5e7eb;border-radius:6px;padding:8px 14px;font-family:monospace;font-size:15px;letter-spacing:0.5px;">{{ $generatedPassword }}</span>
                    </p>
                    <p style="margin:0 0 14px;font-size:13px;line-height:1.6;color:#6b7280;">
                        Keep this email until your application is approved — you'll need it to log in.
                    </p>
                @endif
            </div>

            <div style="padding:16px 28px;background:#fafafa;border-top:1px solid #f0f0f0;color:#9ca3af;font-size:12px;line-height:1.5;">
                This is an automated confirmation from GoTrips.
            </div>
        </div>
    </div>
</body>
</html>
