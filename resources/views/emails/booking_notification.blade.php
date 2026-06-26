<!DOCTYPE html>
<html>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <div style="max-width:560px;margin:0 auto;padding:24px 16px;">
        <div style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #ececec;">
            <div style="background:#0f172a;padding:22px 28px;">
                <div style="color:#ffffff;font-size:18px;font-weight:700;letter-spacing:-0.01em;">{{ $heading }}</div>
                @if(!empty($reference))
                    <div style="color:#94a3b8;font-size:13px;margin-top:4px;">Reference {{ $reference }}</div>
                @endif
            </div>

            <div style="padding:24px 28px;">
                <p style="margin:0 0 18px;font-size:14px;line-height:1.6;color:#374151;">{{ $intro }}</p>

                <table style="width:100%;border-collapse:collapse;font-size:14px;">
                    @foreach($rows as $label => $value)
                        @continue($value === null || $value === '')
                        <tr>
                            <td style="padding:9px 0;width:150px;color:#6b7280;vertical-align:top;border-bottom:1px solid #f0f0f0;">{{ $label }}</td>
                            <td style="padding:9px 0;color:#111827;font-weight:600;border-bottom:1px solid #f0f0f0;">{{ $value }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>

            <div style="padding:16px 28px;background:#fafafa;border-top:1px solid #f0f0f0;color:#9ca3af;font-size:12px;line-height:1.5;">
                This is an automated booking notification. Manage who receives these in
                Manager → Settings → Booking Notifications.
            </div>
        </div>
    </div>
</body>
</html>
