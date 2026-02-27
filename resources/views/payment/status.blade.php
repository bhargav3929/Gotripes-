<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Payment {{ ($response['order_status'] ?? '') === 'Success' ? 'Successful' : 'Status' }} - GoTrip</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #0a0a0a;
            color: #fff;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .status-wrapper {
            width: 100%;
            max-width: 480px;
            animation: fadeSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Status Icon */
        .status-icon-wrapper {
            text-align: center;
            margin-bottom: 28px;
        }

        .status-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            position: relative;
        }

        .status-icon.success {
            background: linear-gradient(135deg, rgba(74, 222, 128, 0.15) 0%, rgba(34, 197, 94, 0.08) 100%);
            border: 2px solid rgba(74, 222, 128, 0.3);
            color: #4ade80;
            box-shadow: 0 0 30px rgba(74, 222, 128, 0.15);
        }

        .status-icon.failed {
            background: linear-gradient(135deg, rgba(248, 113, 113, 0.15) 0%, rgba(239, 68, 68, 0.08) 100%);
            border: 2px solid rgba(248, 113, 113, 0.3);
            color: #f87171;
            box-shadow: 0 0 30px rgba(248, 113, 113, 0.15);
        }

        .status-icon.cancelled {
            background: linear-gradient(135deg, rgba(251, 191, 36, 0.15) 0%, rgba(245, 158, 11, 0.08) 100%);
            border: 2px solid rgba(251, 191, 36, 0.3);
            color: #fbbf24;
            box-shadow: 0 0 30px rgba(251, 191, 36, 0.15);
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .status-icon { animation: iconPulse 2s ease-in-out infinite; }

        /* Status Title */
        .status-title {
            text-align: center;
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 6px;
            letter-spacing: -0.3px;
        }

        .status-title.success { color: #4ade80; }
        .status-title.failed { color: #f87171; }
        .status-title.cancelled { color: #fbbf24; }

        .status-subtitle {
            text-align: center;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.4);
            margin-bottom: 32px;
            line-height: 1.5;
        }

        /* Card */
        .status-card {
            background: linear-gradient(165deg, #141414 0%, #0a0a0a 50%, #111 100%);
            border: 1px solid rgba(255, 210, 63, 0.12);
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        .card-header-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: rgba(255, 255, 255, 0.3);
            font-weight: 700;
            margin-bottom: 16px;
        }

        /* Detail rows */
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
        }

        .detail-row:not(:last-child) {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .detail-label {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.5);
            font-size: 14px;
        }

        .detail-label i {
            font-size: 14px;
            color: rgba(255, 210, 63, 0.5);
            width: 18px;
            text-align: center;
        }

        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
            font-variant-numeric: tabular-nums;
            text-align: right;
            max-width: 55%;
            word-break: break-all;
        }

        /* Amount highlight */
        .amount-row {
            background: linear-gradient(135deg, rgba(255, 210, 63, 0.08) 0%, rgba(255, 184, 0, 0.04) 100%);
            border: 1px solid rgba(255, 210, 63, 0.15);
            border-radius: 12px;
            padding: 16px 18px;
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .amount-label {
            font-size: 13px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.5);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .amount-value {
            font-size: 24px;
            font-weight: 900;
            color: #FFD23F;
            letter-spacing: -0.5px;
        }

        .amount-value small {
            font-size: 13px;
            font-weight: 700;
            color: rgba(255, 210, 63, 0.6);
            margin-left: 4px;
        }

        /* Error info */
        .error-card {
            background: rgba(248, 113, 113, 0.06);
            border: 1px solid rgba(248, 113, 113, 0.15);
            border-radius: 12px;
            padding: 16px 18px;
            margin-bottom: 20px;
        }

        .error-card p {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 4px;
        }

        .error-card p:last-child { margin-bottom: 0; }

        .error-card strong {
            color: rgba(248, 113, 113, 0.8);
        }

        /* Buttons */
        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .btn-primary-gold {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, #FFD700 0%, #FFB800 100%);
            color: #000;
            border: none;
            padding: 14px 20px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(255, 215, 0, 0.2);
        }

        .btn-primary-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.3);
            filter: brightness(1.08);
            color: #000;
            text-decoration: none;
        }

        .btn-outline {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: transparent;
            color: rgba(255, 255, 255, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.12);
            padding: 14px 20px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            border-color: rgba(255, 210, 63, 0.3);
            color: #FFD23F;
            background: rgba(255, 210, 63, 0.05);
            text-decoration: none;
        }

        /* Footer */
        .status-footer {
            text-align: center;
            margin-top: 28px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.2);
            letter-spacing: 0.3px;
        }

        .status-footer i {
            color: rgba(74, 222, 128, 0.4);
            margin-right: 3px;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .status-wrapper { max-width: 100%; }
            .status-card { padding: 18px; }
            .status-title { font-size: 20px; }
            .amount-value { font-size: 20px; }
            .btn-group { flex-direction: column; }
            .status-icon { width: 68px; height: 68px; font-size: 30px; }
        }
    </style>
</head>
<body>
    <div class="status-wrapper">
        {{-- Status Icon --}}
        <div class="status-icon-wrapper">
            @if (($response['order_status'] ?? '') === 'Success')
                <div class="status-icon success">
                    <i class="bi bi-check-lg"></i>
                </div>
            @elseif (($response['order_status'] ?? '') === 'Cancelled')
                <div class="status-icon cancelled">
                    <i class="bi bi-x-lg"></i>
                </div>
            @else
                <div class="status-icon failed">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
            @endif
        </div>

        {{-- Status Title --}}
        @if (($response['order_status'] ?? '') === 'Success')
            <h1 class="status-title success">Payment Successful</h1>
            <p class="status-subtitle">Your booking has been confirmed. A confirmation email has been sent to your inbox.</p>
        @elseif (($response['order_status'] ?? '') === 'Cancelled')
            <h1 class="status-title cancelled">Payment Cancelled</h1>
            <p class="status-subtitle">Your payment was cancelled. No charges have been applied to your account.</p>
        @else
            <h1 class="status-title failed">Payment Failed</h1>
            <p class="status-subtitle">We couldn't process your payment. Please try again or contact support.</p>
        @endif

        {{-- Error Details (only for failure/cancelled) --}}
        @if (($response['order_status'] ?? '') !== 'Success')
            @if (!empty($response['failure_message']) && $response['failure_message'] !== 'Not provided')
                <div class="error-card">
                    <p><strong>Reason:</strong> {{ $response['failure_message'] }}</p>
                </div>
            @endif
        @endif

        {{-- Transaction Details --}}
        <div class="status-card">
            <div class="card-header-label">Transaction Details</div>

            <div class="detail-row">
                <div class="detail-label">
                    <i class="bi bi-hash"></i>
                    <span>Order ID</span>
                </div>
                <div class="detail-value">{{ $response['order_id'] ?? 'N/A' }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">
                    <i class="bi bi-credit-card"></i>
                    <span>Payment Method</span>
                </div>
                <div class="detail-value">{{ $response['payment_mode'] ?? 'Online Checkout' }}</div>
            </div>

            @if (!empty($response['tracking_id']) || !empty($response['bank_ref_no']))
            <div class="detail-row">
                <div class="detail-label">
                    <i class="bi bi-upc-scan"></i>
                    <span>Reference</span>
                </div>
                <div class="detail-value">{{ $response['bank_ref_no'] ?? $response['tracking_id'] ?? 'N/A' }}</div>
            </div>
            @endif

            <div class="detail-row">
                <div class="detail-label">
                    <i class="bi bi-check2-circle"></i>
                    <span>Status</span>
                </div>
                <div class="detail-value">
                    @if (($response['order_status'] ?? '') === 'Success')
                        <span style="color: #4ade80; font-weight: 700;">Paid</span>
                    @elseif (($response['order_status'] ?? '') === 'Cancelled')
                        <span style="color: #fbbf24; font-weight: 700;">Cancelled</span>
                    @else
                        <span style="color: #f87171; font-weight: 700;">Failed</span>
                    @endif
                </div>
            </div>

            {{-- Amount --}}
            @if (!empty($response['amount']))
            <div class="amount-row">
                <span class="amount-label">Amount</span>
                <span class="amount-value">{{ number_format((float)($response['amount'] ?? 0), 2) }}<small>{{ $response['currency'] ?? 'AED' }}</small></span>
            </div>
            @endif
        </div>

        {{-- Action Buttons --}}
        <div class="btn-group">
            <a href="{{ url('/') }}" class="btn-primary-gold">
                <i class="bi bi-house-fill"></i>
                Home
            </a>
            @if (($response['order_status'] ?? '') !== 'Success')
            <a href="{{ url('/') }}" class="btn-outline">
                <i class="bi bi-arrow-clockwise"></i>
                Try Again
            </a>
            @else
            <a href="{{ url('/') }}" class="btn-outline">
                <i class="bi bi-compass"></i>
                Explore More
            </a>
            @endif
        </div>

        {{-- Footer --}}
        <p class="status-footer">
            <i class="bi bi-shield-check"></i> Secured by 256-bit SSL encryption
        </p>
    </div>
</body>
</html>
