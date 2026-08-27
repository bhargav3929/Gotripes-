<!DOCTYPE html>
<html>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <div style="max-width:560px;margin:0 auto;padding:24px 16px;">
        <div style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #ececec;">
            <div style="background:#0f172a;padding:22px 28px;">
                <div style="color:#dba617;font-size:18px;font-weight:700;letter-spacing:-0.01em;">Trade License Expiring Soon</div>
            </div>

            <div style="padding:24px 28px;">
                <p style="margin:0 0 14px;font-size:14px;line-height:1.6;color:#374151;">
                    Hi {{ $agent->name }},
                </p>
                <p style="margin:0 0 14px;font-size:14px;line-height:1.6;color:#374151;">
                    The trade license on file for <strong>{{ $agent->company_name }}</strong>
                    (license #{{ $agent->trade_license_number }}) expires on
                    <strong>{{ optional($agent->trade_license_expiry_date)->format('d M Y') }}</strong>.
                </p>
                <p style="margin:0 0 14px;font-size:14px;line-height:1.6;color:#374151;">
                    To avoid any interruption to your agent account, please renew your trade license and submit the updated document before it expires. Accounts with an expired license are automatically disabled.
                </p>
            </div>

            <div style="padding:16px 28px;background:#fafafa;border-top:1px solid #f0f0f0;color:#9ca3af;font-size:12px;line-height:1.5;">
                This is an automated notification from GoTrips.
            </div>
        </div>
    </div>
</body>
</html>
