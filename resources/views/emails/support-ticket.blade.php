<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $ticket->ticket_number }}</title>
</head>
<body style="margin:0;background:#f3f4f6;font-family:Arial,sans-serif;color:#1f2937;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:28px 12px;background:#f3f4f6;">
    <tr><td align="center">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:620px;background:#fff;border-radius:14px;overflow:hidden;border:1px solid #e5e7eb;">
            <tr><td style="background:#0d0d0d;border-bottom:4px solid #d4af37;padding:24px 28px;color:#fff;">
                <div style="font-size:12px;letter-spacing:1.4px;text-transform:uppercase;color:#d4af37;">{{ $companyName }} Support</div>
                <div style="font-size:22px;font-weight:700;margin-top:6px;">Ticket {{ $ticket->ticket_number }}</div>
            </td></tr>
            <tr><td style="padding:28px;">
                @if($kind === 'customer_created')
                    <p style="font-size:16px;margin-top:0;">Hi {{ $ticket->customer_name }},</p>
                    <p>We received your request and created ticket <strong>{{ $ticket->ticket_number }}</strong>.</p>
                    <p>{{ $ticket->company?->getSetting('support_auto_response') ?: 'Our team will review it during support hours and reply within the response target shown below.' }}</p>
                @elseif($kind === 'customer_reply')
                    <p style="font-size:16px;margin-top:0;">Hi {{ $ticket->customer_name }},</p>
                    <p>Our support team replied to your ticket:</p>
                    <div style="background:#f9fafb;border-left:4px solid #d4af37;padding:14px 16px;white-space:pre-wrap;">{{ $messageText }}</div>
                    <p style="margin-bottom:0;margin-top:20px;">Open the help window on {{ $companyName }} and choose <strong>Track an existing ticket</strong> to view the full conversation or reply.</p>
                @elseif($kind === 'staff_assigned')
                    <p style="font-size:16px;margin-top:0;">Hi {{ $ticket->assignee?->name ?? 'there' }},</p>
                    <p>Ticket <strong>{{ $ticket->ticket_number }}</strong> has been assigned to you. The customer is waiting for a reply from you.</p>
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="6" style="font-size:14px;">
                        <tr><td style="color:#6b7280;width:120px;">Customer</td><td>{{ $ticket->customer_name }}</td></tr>
                        <tr><td style="color:#6b7280;">Email</td><td>{{ $ticket->customer_email }}</td></tr>
                        <tr><td style="color:#6b7280;">Phone</td><td>{{ $ticket->customer_phone }}</td></tr>
                        <tr><td style="color:#6b7280;">Priority</td><td>{{ \App\Models\SupportTicket::PRIORITIES[$ticket->priority] ?? ucfirst($ticket->priority) }}</td></tr>
                        <tr><td style="color:#6b7280;">Status</td><td>{{ \App\Models\SupportTicket::STATUSES[$ticket->status] ?? ucfirst($ticket->status) }}</td></tr>
                    </table>
                    <div style="background:#f9fafb;border-left:4px solid #d4af37;padding:14px 16px;margin-top:18px;white-space:pre-wrap;">{{ $ticket->subject }}</div>
                    <p style="margin:24px 0 0;">
                        <a href="{{ route('manager.support.show', $ticket) }}" style="display:inline-block;background:#d4af37;color:#0d0d0d;text-decoration:none;font-weight:700;padding:12px 22px;border-radius:8px;">Open ticket {{ $ticket->ticket_number }}</a>
                    </p>
                    <p style="margin-bottom:0;margin-top:20px;color:#6b7280;font-size:13px;">Reply from the manager portal so response time and the full customer conversation are recorded.</p>
                @else
                    <p style="font-size:16px;margin-top:0;"><strong>{{ $kind === 'staff_followup' ? 'Customer follow-up' : 'New customer support request' }}</strong></p>
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="6" style="font-size:14px;">
                        <tr><td style="color:#6b7280;width:120px;">Customer</td><td>{{ $ticket->customer_name }}</td></tr>
                        <tr><td style="color:#6b7280;">Email</td><td>{{ $ticket->customer_email }}</td></tr>
                        <tr><td style="color:#6b7280;">Phone</td><td>{{ $ticket->customer_phone }}</td></tr>
                        <tr><td style="color:#6b7280;">Source</td><td style="word-break:break-all;">{{ $ticket->source_url ?: 'Not recorded' }}</td></tr>
                    </table>
                    <div style="background:#f9fafb;border-left:4px solid #d4af37;padding:14px 16px;margin-top:18px;white-space:pre-wrap;">{{ $messageText ?: $ticket->subject }}</div>
                    <p style="margin-bottom:0;margin-top:20px;">Reply from the manager portal so response time and the full customer conversation are recorded.</p>
                @endif

                @if($kind === 'customer_created')
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="6" style="font-size:14px;margin-top:18px;background:#f9fafb;border-radius:8px;">
                        <tr><td style="color:#6b7280;width:130px;">Support hours</td><td>{{ $ticket->company?->getSetting('support_hours', config('support.default_hours')) }}</td></tr>
                        <tr><td style="color:#6b7280;">Response target</td><td>Within {{ max(1, (int) ceil(($ticket->company?->getSetting('support_sla_minutes', config('support.default_sla_minutes')) ?? 240) / 60)) }} hours</td></tr>
                    </table>
                @endif
            </td></tr>
        </table>
    </td></tr>
</table>
</body>
</html>
