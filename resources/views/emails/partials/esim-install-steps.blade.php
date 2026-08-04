{{--
    Activation instructions shipped inside the QR email.

    The client's support line was fielding "the eSIM arrived but how do I turn
    it on?" calls, so the steps travel with the QR rather than living on the
    website only. Built from tables and inline styles because Outlook and Gmail
    strip <style> blocks, flexbox and grid.

    Expects: $smdpAddress, $matchingId (both nullable).
--}}
@php
    // Numbered-step row. Kept as a closure so the three platform blocks below
    // stay readable instead of repeating 12 lines of table markup each.
    $step = function (int $n, string $text, string $accent) {
        return '<tr>'
            . '<td width="30" valign="top" style="padding:0 10px 12px 0;">'
            . '<div style="width:24px;height:24px;line-height:24px;border-radius:12px;background:' . $accent . ';'
            . 'color:#ffffff;font-size:12px;font-weight:700;text-align:center;">' . $n . '</div>'
            . '</td>'
            . '<td valign="top" style="padding:2px 0 12px;font-size:13px;line-height:1.55;color:#374151;">'
            . $text
            . '</td>'
            . '</tr>';
    };
@endphp

<div style="margin:26px 0 0;">
    <div style="font-size:15px;font-weight:700;color:#111827;margin-bottom:4px;">
        How to switch it on
    </div>
    <p style="margin:0 0 18px;font-size:13px;line-height:1.55;color:#6b7280;">
        Pick your phone below. Do this <strong>before you fly</strong>, while you still have Wi-Fi.
    </p>

    {{-- ── iPhone ─────────────────────────────────────────────── --}}
    <div style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;margin-bottom:14px;">
        <div style="background:#111827;padding:11px 16px;color:#ffffff;font-size:13px;font-weight:700;">
            iPhone &nbsp;·&nbsp; iOS 16 and newer
        </div>
        <div style="padding:16px 16px 4px;">
            <table style="width:100%;border-collapse:collapse;">
                {!! $step(1, 'Open <strong>Settings</strong> → <strong>Mobile Service</strong> (or <strong>Cellular</strong>).', '#111827') !!}
                {!! $step(2, 'Tap <strong>Add eSIM</strong> → <strong>Use QR Code</strong>.', '#111827') !!}
                {!! $step(3, 'Scan the QR code above from a second screen — a laptop, or this email opened on another phone.', '#111827') !!}
                {!! $step(4, 'Name the plan <strong>Travel</strong> so you can tell it apart from your home SIM.', '#111827') !!}
                {!! $step(5, 'Keep your home number as <strong>Default</strong> for calls, and set <strong>Travel</strong> for <strong>Mobile Data</strong>.', '#111827') !!}
                {!! $step(6, 'Turn <strong>Data Roaming ON</strong> for the Travel plan. The eSIM will not connect without it.', '#111827') !!}
            </table>
        </div>
    </div>

    {{-- ── Samsung / Android ──────────────────────────────────── --}}
    <div style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;margin-bottom:14px;">
        <div style="background:#1f4e9c;padding:11px 16px;color:#ffffff;font-size:13px;font-weight:700;">
            Samsung &nbsp;·&nbsp; and other Android phones
        </div>
        <div style="padding:16px 16px 4px;">
            <table style="width:100%;border-collapse:collapse;">
                {!! $step(1, 'Open <strong>Settings</strong> → <strong>Connections</strong> → <strong>SIM manager</strong>.', '#1f4e9c') !!}
                {!! $step(2, 'Tap <strong>Add eSIM</strong> → <strong>Scan QR code from service provider</strong>.', '#1f4e9c') !!}
                {!! $step(3, 'Scan the QR code above, then tap <strong>Add</strong> and confirm.', '#1f4e9c') !!}
                {!! $step(4, 'Under <strong>Mobile data</strong>, choose the new eSIM.', '#1f4e9c') !!}
                {!! $step(5, 'Turn <strong>Data roaming ON</strong> for it, then restart the phone once.', '#1f4e9c') !!}
            </table>
        </div>
    </div>

    {{-- ── Manual, no QR scanner available ────────────────────── --}}
    @if($smdpAddress || $matchingId)
        <div style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
            <div style="background:#b45309;padding:11px 16px;color:#ffffff;font-size:13px;font-weight:700;">
                No second screen? &nbsp;·&nbsp; Enter it by hand
            </div>
            <div style="padding:16px;">
                <p style="margin:0 0 12px;font-size:13px;line-height:1.55;color:#374151;">
                    On the same <strong>Add eSIM</strong> screen choose
                    <strong>Enter details manually</strong>, then copy these two values across.
                    This works without installing any app.
                </p>
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    @if($smdpAddress)
                        <tr>
                            <td style="padding:8px 10px;background:#f9fafb;border:1px solid #eef0f3;color:#6b7280;width:150px;">SM-DP+ address</td>
                            <td style="padding:8px 10px;background:#f9fafb;border:1px solid #eef0f3;color:#111827;font-weight:700;word-break:break-all;font-family:'Courier New',monospace;">{{ $smdpAddress }}</td>
                        </tr>
                    @endif
                    @if($matchingId)
                        <tr>
                            <td style="padding:8px 10px;background:#f9fafb;border:1px solid #eef0f3;color:#6b7280;">Activation code</td>
                            <td style="padding:8px 10px;background:#f9fafb;border:1px solid #eef0f3;color:#111827;font-weight:700;word-break:break-all;font-family:'Courier New',monospace;">{{ $matchingId }}</td>
                        </tr>
                    @endif
                </table>
                <p style="margin:12px 0 0;font-size:12px;line-height:1.5;color:#9ca3af;">
                    Leave any "confirmation code" field blank unless your phone insists on one.
                </p>
            </div>
        </div>
    @endif
</div>
