<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Booking Confirmation - Payment Received</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 650px;
            margin: 0 auto;
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            border: 2px solid #FFD23F;
        }
        .header {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #000000 100%);
            color: #FFD23F;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        .header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 210, 63, 0.1) 50%, transparent 70%);
            pointer-events: none;
        }
        .header h1 { margin: 0; font-size: 26px; font-weight: bold; position: relative; z-index: 1; }
        .header p { position: relative; z-index: 1; margin: 10px 0 0; font-size: 14px; color: #e0e0e0; }
        .success-badge {
            display: inline-block;
            padding: 8px 20px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: #fff;
            border-radius: 25px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 15px;
        }
        .failed-badge {
            display: inline-block;
            padding: 8px 20px;
            background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
            color: #fff;
            border-radius: 25px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 15px;
        }
        .cancelled-badge {
            display: inline-block;
            padding: 8px 20px;
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: #1a1a1a;
            border-radius: 25px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 15px;
        }
        .content { padding: 35px 30px; background: linear-gradient(to bottom, #ffffff 0%, #fafafa 100%); }
        .greeting { font-size: 16px; color: #2c3e50; margin-bottom: 20px; }
        .greeting strong { color: #000; }
        .activity-card {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            color: #FFD23F;
            padding: 28px;
            margin: 25px 0;
            border-radius: 15px;
            text-align: center;
            border: 2px solid #FFD23F;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .activity-card h2 { margin: 0 0 12px; font-size: 22px; color: #FFD23F; }
        .activity-card p { margin: 6px 0; font-size: 15px; color: #e0e0e0; }
        .info-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border: 2px solid #FFD23F;
            border-radius: 12px;
            padding: 22px;
            margin: 20px 0;
            position: relative;
        }
        .info-box::before {
            content: '';
            position: absolute;
            top: 0; left: 0; width: 100%; height: 4px;
            background: linear-gradient(90deg, #FFD23F 0%, #ffc107 50%, #FFD23F 100%);
            border-radius: 12px 12px 0 0;
        }
        .section-header {
            margin: -22px -22px 18px -22px;
            padding: 12px 22px;
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            color: #FFD23F;
            font-size: 15px;
            font-weight: bold;
            border-radius: 10px 10px 0 0;
            border-bottom: 2px solid #FFD23F;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 12px 0;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child { border-bottom: none; }
        .label { font-weight: bold; color: #2c3e50; flex: 1; font-size: 14px; }
        .value { flex: 2; text-align: right; color: #34495e; font-weight: 500; }
        .amount-box {
            background: linear-gradient(135deg, #FFD23F 0%, #ffc107 100%);
            color: #1a1a1a;
            padding: 15px 25px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 20px;
            display: inline-block;
            margin: 12px 0;
            box-shadow: 0 6px 20px rgba(255, 210, 63, 0.4);
            border: 2px solid #e6ac00;
        }
        .next-steps {
            background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
            border: 2px solid #4caf50;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }
        .next-steps.failed {
            background: linear-gradient(135deg, #fce4ec 0%, #fbe9e7 100%);
            border-color: #e53935;
        }
        .next-steps.cancelled {
            background: linear-gradient(135deg, #fff8e1 0%, #fff3e0 100%);
            border-color: #ff9800;
        }
        .next-steps strong { color: #2c3e50; }
        .next-steps ul { margin: 12px 0; padding-left: 25px; line-height: 1.8; }
        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, #FFD23F 50%, transparent 100%);
            margin: 25px 0;
        }
        .footer {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #000000 100%);
            color: #FFD23F;
            text-align: center;
            padding: 30px 20px;
            font-size: 13px;
        }
        .footer::before {
            content: '';
            display: block;
            height: 3px;
            background: linear-gradient(90deg, #FFD23F 0%, #ffc107 50%, #FFD23F 100%);
            margin-bottom: 20px;
        }
        .support-box {
            background: rgba(255, 210, 63, 0.1);
            border: 1px solid rgba(255, 210, 63, 0.3);
            border-radius: 10px;
            padding: 15px;
            margin-top: 15px;
        }
        .icon { font-size: 16px; margin-right: 6px; }
        @media (max-width: 600px) {
            body { padding: 10px; }
            .header { padding: 25px 20px; }
            .content { padding: 25px 20px; }
            .info-box { padding: 18px; }
            .section-header { margin: -18px -18px 14px -18px; padding: 10px 18px; font-size: 13px; }
            .detail-row { flex-direction: column; padding: 8px 0; }
            .value { text-align: left; margin-top: 4px; font-weight: bold; color: #2c3e50; }
            .activity-card { padding: 20px 15px; }
            .amount-box { font-size: 18px; padding: 12px 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @if($isSuccess)
                <h1>Booking Confirmed!</h1>
                <p>Your payment has been successfully processed</p>
                <div><span class="success-badge">Payment Successful</span></div>
            @elseif($isFailed)
                <h1>Payment Failed</h1>
                <p>We were unable to process your payment</p>
                <div><span class="failed-badge">Payment Failed</span></div>
            @else
                <h1>Payment Cancelled</h1>
                <p>Your payment was cancelled</p>
                <div><span class="cancelled-badge">Cancelled</span></div>
            @endif
        </div>

        <div class="content">
            <div class="greeting">
                Dear <strong>{{ $data['name'] ?? 'Customer' }}</strong>,
                @if($isSuccess)
                    <br>Thank you for your booking! Your payment has been confirmed and your activity is reserved.
                @elseif($isFailed)
                    <br>Unfortunately, your payment could not be processed. Please try again or contact our support team.
                @else
                    <br>Your payment was cancelled. Your booking details are saved — you can retry payment anytime.
                @endif
            </div>

            <!-- Activity Details -->
            <div class="activity-card">
                <h2>{{ $data['activity_name'] ?? ($data['activityName'] ?? 'Activity Booking') }}</h2>
                @if(!empty($data['activityLocation']))
                    <p><span class="icon">📍</span>{{ $data['activityLocation'] }}</p>
                @endif
                @if(!empty($data['booking_date']))
                    <p><span class="icon">📅</span>{{ $data['booking_date'] }}</p>
                @elseif(!empty($data['date']))
                    <p><span class="icon">📅</span>{{ \Carbon\Carbon::parse($data['date'])->format('l, F j, Y') }}</p>
                @endif
                <p style="margin-top: 12px;"><span class="icon">🔖</span>Order ID: <strong>{{ $data['order_id'] ?? 'N/A' }}</strong></p>
            </div>

            <!-- Booking Details -->
            <div class="info-box">
                <h3 class="section-header"><span class="icon">🎫</span>Booking Details</h3>

                <div class="detail-row">
                    <span class="label"><span class="icon">👤</span>Name:</span>
                    <span class="value">{{ $data['name'] ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="label"><span class="icon">📧</span>Email:</span>
                    <span class="value">{{ $data['email'] ?? 'N/A' }}</span>
                </div>
                @if(!empty($data['phone']))
                <div class="detail-row">
                    <span class="label"><span class="icon">📞</span>Phone:</span>
                    <span class="value">{{ $data['phone'] }}</span>
                </div>
                @endif
                @if(!empty($data['adults']))
                <div class="detail-row">
                    <span class="label"><span class="icon">👥</span>Adults:</span>
                    <span class="value">{{ $data['adults'] }} person(s)</span>
                </div>
                @endif
                @if(!empty($data['childrens']) && $data['childrens'] > 0)
                <div class="detail-row">
                    <span class="label"><span class="icon">👶</span>Children:</span>
                    <span class="value">{{ $data['childrens'] }} person(s)</span>
                </div>
                @endif
                @if(!empty($data['transfer']) && $data['transfer'] !== 'without_transfer')
                <div class="detail-row">
                    <span class="label"><span class="icon">🚐</span>Transfer:</span>
                    <span class="value">
                        @php
                            $transferMap = [
                                'abu_dhabi' => 'Transport from Abu Dhabi',
                                'dubai' => 'Transport from Dubai',
                                'abu_dhabi_to_dubai' => 'Abu Dhabi to Dubai',
                                'any_emirates' => 'Any Emirates',
                                'without_transfer' => 'Without Transport',
                                'abu_bhabi' => 'Transport from Abu Dhabi',
                                'du_bai' => 'Transport from Dubai',
                            ];
                        @endphp
                        {{ $transferMap[$data['transfer']] ?? ucwords(str_replace('_', ' ', $data['transfer'])) }}
                    </span>
                </div>
                @endif
            </div>

            <!-- Payment Receipt -->
            <div class="info-box">
                <h3 class="section-header"><span class="icon">💳</span>Payment Receipt</h3>

                <div style="text-align: center; margin: 15px 0;">
                    <div class="amount-box">
                        {{ $data['amount'] ?? '0' }} {{ $data['currency'] ?? 'AED' }}
                    </div>
                </div>

                <div class="detail-row">
                    <span class="label"><span class="icon">📊</span>Status:</span>
                    <span class="value">
                        <strong style="color: {{ $isSuccess ? '#28a745' : ($isFailed ? '#dc3545' : '#ff9800') }};">
                            {{ $paymentStatus }}
                        </strong>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="label"><span class="icon">🔖</span>Order ID:</span>
                    <span class="value">{{ $data['order_id'] ?? 'N/A' }}</span>
                </div>
                @if(!empty($data['tracking_id']))
                <div class="detail-row">
                    <span class="label"><span class="icon">🔍</span>Tracking ID:</span>
                    <span class="value">{{ $data['tracking_id'] }}</span>
                </div>
                @endif
                @if(!empty($data['bank_ref_no']))
                <div class="detail-row">
                    <span class="label"><span class="icon">🏦</span>Reference:</span>
                    <span class="value">{{ $data['bank_ref_no'] }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <span class="label"><span class="icon">📅</span>Date:</span>
                    <span class="value">{{ $data['sent_at'] ?? now()->format('M j, Y g:i A') }}</span>
                </div>
            </div>

            <!-- Supplier / Organizer Details -->
            @if(!empty($data['supplierName']) || !empty($data['supplier_name']))
            <div class="info-box">
                <h3 class="section-header"><span class="icon">🏢</span>Supplier / Organizer</h3>

                <div class="detail-row">
                    <span class="label"><span class="icon">🏢</span>Name:</span>
                    <span class="value">{{ $data['supplierName'] ?? ($data['supplier_name'] ?? 'N/A') }}</span>
                </div>
                @if(!empty($data['supplierEmail']) || !empty($data['supplier_email']))
                <div class="detail-row">
                    <span class="label"><span class="icon">📧</span>Contact:</span>
                    <span class="value">{{ $data['supplierEmail'] ?? ($data['supplier_email'] ?? '') }}</span>
                </div>
                @endif
            </div>
            @endif

            <div class="divider"></div>

            <!-- Next Steps -->
            <div class="next-steps {{ $isFailed ? 'failed' : ($isCancelled ? 'cancelled' : '') }}">
                @if($isSuccess)
                    <strong><span class="icon">✅</span>What's Next?</strong>
                    <ul>
                        <li>Your booking is confirmed — no further action needed</li>
                        <li>The activity organizer has been notified</li>
                        <li>You'll receive activity details and instructions before your date</li>
                        <li>Keep your Order ID <strong>{{ $data['order_id'] ?? '' }}</strong> for reference</li>
                    </ul>
                @elseif($isFailed)
                    <strong><span class="icon">❌</span>What You Can Do:</strong>
                    <ul>
                        <li>Try again with a different payment method</li>
                        <li>Check with your bank if the transaction was blocked</li>
                        <li>Your booking is reserved for 24 hours</li>
                        <li>Contact our support team for assistance</li>
                    </ul>
                @else
                    <strong><span class="icon">⚠️</span>Payment Was Cancelled</strong>
                    <ul>
                        <li>No charges were made to your account</li>
                        <li>Your booking details are saved</li>
                        <li>You can retry payment at any time</li>
                        <li>Contact support if you need help</li>
                    </ul>
                @endif
            </div>
        </div>

        <div class="footer">
            <p><strong>{{ config('app.name', 'Go Trips') }}</strong></p>
            <p>Thank you for choosing us!</p>
            <div class="support-box">
                <p style="font-size: 14px; font-weight: bold; margin: 0 0 8px;">Need Help?</p>
                <p style="font-size: 13px; color: #e0e0e0; margin: 4px 0;">
                    📧 {{ config('mail.from.address', 'info@aynalamirtourism.com') }}
                </p>
                <p style="font-size: 13px; color: #e0e0e0; margin: 4px 0;">
                    📞 +971 55 831 3755 (9 AM - 9 PM GST)
                </p>
                <p style="font-size: 12px; color: #aaa; margin: 8px 0 0;">
                    Reference your Order ID: {{ $data['order_id'] ?? 'N/A' }}
                </p>
            </div>
            <p style="font-size: 11px; color: #888; margin-top: 15px;">
                This is an automated confirmation. Please do not reply directly to this email.
            </p>
        </div>
    </div>
</body>
</html>
