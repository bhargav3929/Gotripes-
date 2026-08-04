@php
    $count = $esims->count();
@endphp
<!DOCTYPE html>
<html>
<body style="margin:0;padding:0;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <div style="max-width:560px;margin:0 auto;padding:24px 16px;">
        <div style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #ececec;">
            <div style="background:#0f172a;padding:22px 28px;">
                <div style="color:#ffffff;font-size:18px;font-weight:700;">
                    {{ $count > 1 ? "Your {$count} eSIMs are ready" : 'Your eSIM is ready' }}
                </div>
                <div style="color:#94a3b8;font-size:13px;margin-top:4px;">Reference {{ $order->order_reference }}</div>
            </div>

            <div style="padding:24px 28px;">
                <p style="margin:0 0 18px;font-size:14px;line-height:1.6;color:#374151;">
                    Hi {{ $order->customer_name ?: 'there' }},
                    @if($count > 1)
                        all <strong>{{ $count }}</strong> of your
                        <strong>{{ $order->bundle_name }}</strong> eSIMs for
                        <strong>{{ $order->country_name }}</strong> are active. Each QR code below
                        is a separate eSIM — give one to each traveller, and use each code only once.
                    @else
                        your
                        <strong>{{ $order->bundle_name }}</strong> eSIM for
                        <strong>{{ $order->country_name }}</strong> is active and ready to install.
                    @endif
                </p>

                @foreach($esims as $esim)
                    <div style="margin:22px 0;{{ $count > 1 ? 'border:1px solid #e5e7eb;border-radius:10px;padding:18px 16px;' : '' }}">
                        @if($count > 1)
                            <div style="font-size:13px;font-weight:700;color:#111827;margin-bottom:12px;text-align:center;">
                                eSIM {{ $loop->iteration }} of {{ $count }}
                            </div>
                        @endif

                        @if($esim['qrPng'])
                            {{-- Embedded, not hotlinked: survives remote-image blocking
                                 and cannot break if a third-party service disappears. --}}
                            <div style="text-align:center;">
                                <img src="{{ $message->embedData($esim['qrPng'], 'esim-qr-' . $esim['number'] . '.png', 'image/png') }}"
                                     alt="eSIM QR code {{ $loop->iteration }}" width="240" height="240"
                                     style="border:1px solid #e5e7eb;border-radius:8px;display:inline-block;">
                                <div style="font-size:12px;color:#6b7280;margin-top:8px;">
                                    Scan this from another device
                                </div>
                            </div>
                        @endif

                        @if($count > 1 && ($esim['smdpAddress'] || $esim['matchingId']))
                            {{-- Per-eSIM manual credentials. With several eSIMs in one
                                 email they have to sit beside their own QR, or the
                                 buyer cannot tell which code belongs to which. --}}
                            <table style="width:100%;border-collapse:collapse;font-size:12px;margin-top:14px;">
                                @if($esim['smdpAddress'])
                                    <tr>
                                        <td style="padding:6px 8px;background:#f9fafb;border:1px solid #eef0f3;color:#6b7280;width:120px;">SM-DP+</td>
                                        <td style="padding:6px 8px;background:#f9fafb;border:1px solid #eef0f3;color:#111827;font-weight:700;word-break:break-all;font-family:'Courier New',monospace;">{{ $esim['smdpAddress'] }}</td>
                                    </tr>
                                @endif
                                @if($esim['matchingId'])
                                    <tr>
                                        <td style="padding:6px 8px;background:#f9fafb;border:1px solid #eef0f3;color:#6b7280;">Activation code</td>
                                        <td style="padding:6px 8px;background:#f9fafb;border:1px solid #eef0f3;color:#111827;font-weight:700;word-break:break-all;font-family:'Courier New',monospace;">{{ $esim['matchingId'] }}</td>
                                    </tr>
                                @endif
                                @if($esim['iccid'])
                                    <tr>
                                        <td style="padding:6px 8px;background:#f9fafb;border:1px solid #eef0f3;color:#6b7280;">ICCID</td>
                                        <td style="padding:6px 8px;background:#f9fafb;border:1px solid #eef0f3;color:#6b7280;word-break:break-all;">{{ $esim['iccid'] }}</td>
                                    </tr>
                                @endif
                            </table>
                        @endif
                    </div>
                @endforeach

                {{-- Installation steps. With one eSIM the manual credentials are
                     shown inside this block; with several they are already printed
                     beside each QR above, so they are not repeated. --}}
                @include('emails.partials.esim-install-steps', [
                    'smdpAddress' => $count === 1 ? $esims->first()['smdpAddress'] : null,
                    'matchingId'  => $count === 1 ? $esims->first()['matchingId'] : null,
                ])

                @if($count === 1 && $esims->first()['iccid'])
                    <p style="margin:14px 0 0;font-size:12px;line-height:1.5;color:#9ca3af;">
                        ICCID (quote this if you contact us): <strong style="color:#6b7280;">{{ $esims->first()['iccid'] }}</strong>
                    </p>
                @endif

                <table style="width:100%;border-collapse:collapse;font-size:14px;margin-top:22px;">
                    @foreach([
                        'Plan'      => $order->bundle_name,
                        'eSIMs'     => $count > 1 ? $count : null,
                        'Data'      => $order->data_amount . ($count > 1 ? ' each' : ''),
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
                    Install {{ $count > 1 ? 'each eSIM' : 'the eSIM' }} before you travel while you
                    still have internet. The data allowance starts counting when the eSIM first
                    connects to a network at your destination.
                </p>
            </div>

            <div style="padding:16px 28px;background:#fafafa;border-top:1px solid #f0f0f0;color:#9ca3af;font-size:12px;line-height:1.5;">
                Need help installing? Reply to this email and quote {{ $order->order_reference }}.
            </div>
        </div>
    </div>
</body>
</html>
