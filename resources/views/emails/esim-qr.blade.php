<!DOCTYPE html>
<html>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <div style="max-width:560px;margin:0 auto;padding:24px 16px;">
        <div style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #ececec;">
            <div style="background:#0f172a;padding:22px 28px;">
                <div style="color:#ffffff;font-size:18px;font-weight:700;">Your eSIM is ready</div>
                <div style="color:#94a3b8;font-size:13px;margin-top:4px;">Reference {{ $order->order_reference }}</div>
            </div>

            <div style="padding:24px 28px;">
                <p style="margin:0 0 18px;font-size:14px;line-height:1.6;color:#374151;">
                    Hi {{ $order->customer_name ?: 'there' }}, your
                    <strong>{{ $order->bundle_name }}</strong> eSIM for
                    <strong>{{ $order->country_name }}</strong> is active and ready to install.
                </p>

                @if($qrPng)
                    {{-- Embedded, not hotlinked: survives remote-image blocking
                         and cannot break if a third-party service disappears. --}}
                    <div style="text-align:center;margin:22px 0;">
                        <img src="{{ $message->embedData($qrPng, 'esim-qr.png', 'image/png') }}"
                             alt="eSIM QR code" width="240" height="240"
                             style="border:1px solid #e5e7eb;border-radius:8px;display:inline-block;">
                        <div style="font-size:12px;color:#6b7280;margin-top:8px;">
                            Scan this from another device
                        </div>
                    </div>
                @endif

                <div style="background:#f9fafb;border:1px solid #eef0f3;border-radius:8px;padding:14px 16px;margin:18px 0;">
                    <div style="font-size:13px;font-weight:700;color:#111827;margin-bottom:8px;">
                        Install manually instead
                    </div>
                    <p style="margin:0 0 10px;font-size:13px;line-height:1.55;color:#4b5563;">
                        On your phone open <strong>Settings → Mobile Data → Add eSIM →
                        Enter details manually</strong>, then type:
                    </p>
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        @if($smdpAddress)
                        <tr>
                            <td style="padding:6px 0;color:#6b7280;width:150px;">SM-DP+ address</td>
                            <td style="padding:6px 0;color:#111827;font-weight:600;word-break:break-all;">{{ $smdpAddress }}</td>
                        </tr>
                        @endif
                        @if($matchingId)
                        <tr>
                            <td style="padding:6px 0;color:#6b7280;">Activation code</td>
                            <td style="padding:6px 0;color:#111827;font-weight:600;word-break:break-all;">{{ $matchingId }}</td>
                        </tr>
                        @endif
                        @if($order->monty_iccid)
                        <tr>
                            <td style="padding:6px 0;color:#6b7280;">ICCID</td>
                            <td style="padding:6px 0;color:#111827;font-weight:600;">{{ $order->monty_iccid }}</td>
                        </tr>
                        @endif
                    </table>
                </div>

                <table style="width:100%;border-collapse:collapse;font-size:14px;">
                    @foreach([
                        'Plan'      => $order->bundle_name,
                        'Data'      => $order->data_amount,
                        'Valid for' => $order->validity_days ? $order->validity_days . ' days' : null,
                        'Country'   => $order->country_name,
                        'Paid'      => $order->selling_price ? trim(($order->currency ?? 'AED') . ' ' . $order->selling_price) : null,
                    ] as $label => $value)
                        @continue($value === null || $value === '')
                        <tr>
                            <td style="padding:9px 0;width:150px;color:#6b7280;border-bottom:1px solid #f0f0f0;">{{ $label }}</td>
                            <td style="padding:9px 0;color:#111827;font-weight:600;border-bottom:1px solid #f0f0f0;">{{ $value }}</td>
                        </tr>
                    @endforeach
                </table>

                <p style="margin:18px 0 0;font-size:13px;line-height:1.55;color:#6b7280;">
                    Install the eSIM before you travel while you still have internet.
                    Your data allowance starts counting when the eSIM first connects
                    to a network at your destination.
                </p>
            </div>

            <div style="padding:16px 28px;background:#fafafa;border-top:1px solid #f0f0f0;color:#9ca3af;font-size:12px;line-height:1.5;">
                Need help installing? Reply to this email and quote {{ $order->order_reference }}.
            </div>
        </div>
    </div>
</body>
</html>
