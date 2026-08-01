<!DOCTYPE html>
<html>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <div style="max-width:560px;margin:0 auto;padding:24px 16px;">
        <div style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #ececec;">
            <div style="background:#0f172a;padding:22px 28px;">
                <div style="color:#ffffff;font-size:18px;font-weight:700;letter-spacing:-0.01em;">
                    @if($stage === 'approved')
                        Your e-Visa has been approved 🎉
                    @elseif($stage === 'rejected')
                        Update on your e-Visa application
                    @else
                        Your e-Visa application is in progress
                    @endif
                </div>
                <div style="color:#94a3b8;font-size:13px;margin-top:4px;">Reference {{ $application->order_id }}</div>
            </div>

            <div style="padding:24px 28px;">
                <p style="margin:0 0 18px;font-size:14px;line-height:1.6;color:#374151;">
                    Dear {{ trim(($application->first_name ?? '') . ' ' . ($application->last_name ?? '')) ?: 'Traveller' }},
                </p>
                <p style="margin:0 0 18px;font-size:14px;line-height:1.6;color:#374151;">
                    @if($stage === 'approved')
                        Great news — your e-Visa application has been <strong>approved</strong>.
                        The visa document will be delivered to this email address. Please keep a
                        printed or digital copy with you when you travel.
                    @elseif($stage === 'rejected')
                        Unfortunately your e-Visa application was <strong>not approved</strong> by the
                        issuing authority. Our team has been notified and will contact you about
                        next steps, including any refund you may be entitled to. You can also reply
                        to this email to reach us directly.
                    @else
                        We have received your payment and your application has been submitted for
                        processing. You will receive further emails as its status changes — most
                        applications are decided within the processing time shown when you applied.
                    @endif
                </p>

                <table style="width:100%;border-collapse:collapse;font-size:14px;">
                    @foreach([
                        'Applicant'   => trim(($application->first_name ?? '') . ' ' . ($application->last_name ?? '')),
                        'Destination' => $application->destination_code,
                        'Nationality' => $application->nationality,
                        'Arrival'     => optional($application->arrival_date)->format('d M Y'),
                        'Departure'   => optional($application->departure_date)->format('d M Y'),
                        'Amount paid' => $application->amount ? trim(($application->currency ?? 'USD') . ' ' . $application->amount) : null,
                    ] as $label => $value)
                        @continue($value === null || $value === '')
                        <tr>
                            <td style="padding:9px 0;width:150px;color:#6b7280;vertical-align:top;border-bottom:1px solid #f0f0f0;">{{ $label }}</td>
                            <td style="padding:9px 0;color:#111827;font-weight:600;border-bottom:1px solid #f0f0f0;">{{ $value }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>

            <div style="padding:16px 28px;background:#fafafa;border-top:1px solid #f0f0f0;color:#9ca3af;font-size:12px;line-height:1.5;">
                Questions? Just reply to this email and our team will help.
                Please quote your reference {{ $application->order_id }}.
            </div>
        </div>
    </div>
</body>
</html>
