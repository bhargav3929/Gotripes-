<!DOCTYPE html>
<html>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <div style="max-width:560px;margin:0 auto;padding:24px 16px;">
        <div style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #ececec;">
            <div style="background:{{ $decision === 'approved' ? '#0f172a' : '#0f172a' }};padding:22px 28px;">
                <div style="color:{{ $decision === 'approved' ? '#5cbf70' : '#f56565' }};font-size:18px;font-weight:700;letter-spacing:-0.01em;">
                    Application {{ $decision === 'approved' ? 'Approved' : 'Not Approved' }}
                </div>
            </div>

            <div style="padding:24px 28px;">
                <p style="margin:0 0 14px;font-size:14px;line-height:1.6;color:#374151;">
                    Hi {{ $partner->contact_name }},
                </p>
                @if($decision === 'approved')
                    <p style="margin:0 0 14px;font-size:14px;line-height:1.6;color:#374151;">
                        Your B2B partner application for <strong>{{ $partner->company_name }}</strong> has been approved.
                        You can now log in to your agency dashboard.
                    </p>
                    <p style="margin:0 0 14px;">
                        <a href="{{ route('agency.login') }}" style="display:inline-block;background:#FFD700;color:#1a1a1a;font-weight:700;padding:10px 20px;border-radius:6px;text-decoration:none;font-size:14px;">Log In</a>
                    </p>
                @else
                    <p style="margin:0 0 14px;font-size:14px;line-height:1.6;color:#374151;">
                        Your B2B partner application for <strong>{{ $partner->company_name }}</strong> was not approved at this time.
                    </p>
                    @if($partner->rejection_reason)
                        <p style="margin:0 0 14px;font-size:14px;line-height:1.6;color:#374151;">
                            <strong>Reason:</strong> {{ $partner->rejection_reason }}
                        </p>
                    @endif
                    <p style="margin:0 0 14px;font-size:14px;line-height:1.6;color:#374151;">
                        If you believe this was a mistake or would like more information, please contact our team.
                    </p>
                @endif
            </div>

            <div style="padding:16px 28px;background:#fafafa;border-top:1px solid #f0f0f0;color:#9ca3af;font-size:12px;line-height:1.5;">
                This is an automated notification from GoTrips.
            </div>
        </div>
    </div>
</body>
</html>
