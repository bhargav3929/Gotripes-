{{--
    Activation instructions shipped inside the QR email.

    The wording follows the steps the client supplied verbatim — including the
    "connect to Wi-Fi first" opener, the Pixel wording for Android, and the
    restart-if-no-network fallback on iPhone. Their support line fields these
    calls, so their phrasing wins over mine.

    Built from tables and inline styles because Outlook and Gmail strip <style>
    blocks, flexbox and grid.

    Expects: $smdpAddress, $matchingId, $iccid (all nullable).
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
            On iPhone
        </div>
        <div style="padding:16px 16px 4px;">
            <table style="width:100%;border-collapse:collapse;">
                {!! $step(1, 'Connect to <strong>Wi-Fi</strong>.', '#111827') !!}
                {!! $step(2, 'Open <strong>Settings</strong> → <strong>Cellular</strong> (or <strong>Mobile Data</strong>).', '#111827') !!}
                {!! $step(3, 'Tap <strong>Add eSIM</strong>.', '#111827') !!}
                {!! $step(4, 'Select <strong>Use QR Code</strong>.', '#111827') !!}
                {!! $step(5, 'Scan the QR code above.', '#111827') !!}
                {!! $step(6, 'Follow the on-screen prompts to install the eSIM.', '#111827') !!}
                {!! $step(7, 'Once installed: turn the eSIM <strong>On</strong>, set it as the <strong>Mobile Data</strong> line, and enable <strong>Data Roaming</strong> — a travel eSIM will not connect without it.', '#111827') !!}
                {!! $step(8, 'Restart your phone if the network does not appear immediately.', '#111827') !!}
            </table>
        </div>
    </div>

    {{-- ── Samsung / Android ──────────────────────────────────── --}}
    <div style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;margin-bottom:14px;">
        <div style="background:#1f4e9c;padding:11px 16px;color:#ffffff;font-size:13px;font-weight:700;">
            On Android &nbsp;·&nbsp; Samsung, Google Pixel, etc.
        </div>
        <div style="padding:16px 16px 4px;">
            <table style="width:100%;border-collapse:collapse;">
                {!! $step(1, 'Connect to <strong>Wi-Fi</strong>.', '#1f4e9c') !!}
                {!! $step(2, 'Open <strong>Settings</strong> → <strong>Connections</strong> (or <strong>Network &amp; Internet</strong>) → <strong>SIM Manager</strong>.', '#1f4e9c') !!}
                {!! $step(3, 'Tap <strong>Add eSIM</strong>.', '#1f4e9c') !!}
                {!! $step(4, 'Select <strong>Scan QR Code</strong>.', '#1f4e9c') !!}
                {!! $step(5, 'Scan the QR code above.', '#1f4e9c') !!}
                {!! $step(6, 'Complete the installation and enable the eSIM.', '#1f4e9c') !!}
                {!! $step(7, 'Set it as your <strong>preferred data SIM</strong> and enable <strong>Data Roaming</strong> if required.', '#1f4e9c') !!}
            </table>
        </div>
    </div>

    {{-- ── Manual, no QR scanner available ────────────────────── --}}
    @if($smdpAddress || $matchingId)
        <div style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden;">
            <div style="background:#b45309;padding:11px 16px;color:#ffffff;font-size:13px;font-weight:700;">
                Install manually instead
            </div>
            <div style="padding:16px;">
                <p style="margin:0 0 12px;font-size:13px;line-height:1.55;color:#374151;">
                    On your phone open <strong>Settings</strong> → <strong>Mobile Data</strong> →
                    <strong>Add eSIM</strong> → <strong>Enter details manually</strong>, then type:
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
                    @if(!empty($iccid))
                        <tr>
                            <td style="padding:8px 10px;background:#f9fafb;border:1px solid #eef0f3;color:#6b7280;">ICCID</td>
                            <td style="padding:8px 10px;background:#f9fafb;border:1px solid #eef0f3;color:#111827;font-weight:700;word-break:break-all;font-family:'Courier New',monospace;">{{ $iccid }}</td>
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
