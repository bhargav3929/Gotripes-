<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your customer care login</title>
</head>
<body style="margin:0;background:#f3f4f6;font-family:Arial,sans-serif;color:#1f2937;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:28px 12px;background:#f3f4f6;">
    <tr><td align="center">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;background:#fff;border-radius:14px;overflow:hidden;border:1px solid #e5e7eb;">
            <tr><td style="background:#0d0d0d;border-bottom:4px solid #d4af37;padding:24px 28px;color:#fff;">
                <div style="font-size:12px;letter-spacing:1.4px;text-transform:uppercase;color:#d4af37;">{{ $companyName }} Customer Care</div>
                <div style="font-size:22px;font-weight:700;margin-top:6px;">Welcome to the support team</div>
            </td></tr>
            <tr><td style="padding:28px;">
                <p style="font-size:16px;margin-top:0;">Hi {{ $staff->name }},</p>
                <p>A customer care login has been created for you at {{ $companyName }}. You will use it to answer customer support tickets from the manager portal.</p>

                <table role="presentation" width="100%" cellspacing="0" cellpadding="8" style="font-size:14px;margin-top:18px;background:#f9fafb;border-radius:8px;">
                    <tr><td style="color:#6b7280;width:150px;">Login page</td><td><a href="{{ $loginUrl }}" style="color:#0d0d0d;">{{ $loginUrl }}</a></td></tr>
                    <tr><td style="color:#6b7280;">Email</td><td>{{ $staff->email }}</td></tr>
                    <tr><td style="color:#6b7280;">Temporary password</td><td style="font-family:Menlo,Consolas,monospace;font-size:15px;letter-spacing:.5px;">{{ $temporaryPassword }}</td></tr>
                </table>

                <p style="margin:24px 0 0;">
                    <a href="{{ $loginUrl }}" style="display:inline-block;background:#d4af37;color:#0d0d0d;text-decoration:none;font-weight:700;padding:12px 22px;border-radius:8px;">Sign in to the support queue</a>
                </p>

                <p style="margin-top:24px;">What you will see after signing in:</p>
                <ul style="padding-left:20px;margin:0;">
                    <li style="margin-bottom:6px;">The <strong>Support Tickets</strong> queue, sorted so overdue tickets come first.</li>
                    <li style="margin-bottom:6px;">Tickets assigned to you arrive by email with a direct link.</li>
                    <li>A <strong>Help Guide</strong> that explains every product customers ask about.</li>
                </ul>

                <p style="margin-bottom:0;margin-top:24px;color:#6b7280;font-size:13px;">Keep this password private. If you did not expect this email, contact your manager.</p>
            </td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>
