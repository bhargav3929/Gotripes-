<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Activity Booking Notification</title>
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
            border: 2px solid #17a2b8;
        }
        .header {
            background: linear-gradient(135deg, #004e64 0%, #006d77 50%, #004e64 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.05) 50%, transparent 70%);
            pointer-events: none;
        }
        .header h1 {
            margin: 0;
            font-size: 26px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            position: relative;
            z-index: 1;
        }
        .header p {
            position: relative;
            z-index: 1;
            margin: 10px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .supplier-badge {
            display: inline-block;
            padding: 6px 14px;
            background: linear-gradient(135deg, #FFD23F 0%, #ffc107 100%);
            color: #1a1a1a;
            border-radius: 25px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 12px;
        }
        .content {
            padding: 35px 30px;
            background: linear-gradient(to bottom, #ffffff 0%, #fafafa 100%);
        }
        .greeting {
            font-size: 16px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .greeting strong {
            color: #006d77;
        }
        .info-box {
            background: linear-gradient(135deg, #e8f6f8 0%, #f0fafb 100%);
            border: 2px solid #17a2b8;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            position: relative;
        }
        .info-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #006d77 0%, #17a2b8 50%, #006d77 100%);
            border-radius: 12px 12px 0 0;
        }
        .section-header {
            margin: -20px -20px 18px -20px;
            padding: 12px 20px;
            background: linear-gradient(135deg, #004e64 0%, #006d77 100%);
            color: #ffffff;
            font-size: 15px;
            font-weight: bold;
            border-radius: 10px 10px 0 0;
            border-bottom: 2px solid #17a2b8;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 12px 0;
            padding: 10px 0;
            border-bottom: 1px solid #e0f0f1;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #2c3e50;
            flex: 1;
            font-size: 14px;
        }
        .value {
            flex: 2;
            text-align: right;
            color: #34495e;
            font-weight: 500;
        }
        .activity-card {
            background: linear-gradient(135deg, #004e64 0%, #006d77 100%);
            color: #ffffff;
            padding: 25px;
            margin: 20px 0;
            border-radius: 12px;
            text-align: center;
            border: 2px solid #17a2b8;
            box-shadow: 0 8px 25px rgba(0,77,100,0.3);
        }
        .activity-card h2 {
            margin: 0 0 10px;
            font-size: 22px;
            color: #FFD23F;
        }
        .activity-card p {
            margin: 6px 0;
            font-size: 15px;
            color: #e0f0f1;
        }
        .amount-highlight {
            background: linear-gradient(135deg, #FFD23F 0%, #ffc107 100%);
            color: #1a1a1a;
            padding: 12px 22px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 18px;
            display: inline-block;
            margin: 12px 0;
            box-shadow: 0 6px 20px rgba(255, 210, 63, 0.4);
        }
        .action-box {
            background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%);
            border: 2px solid #FFD23F;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
        }
        .action-box strong {
            color: #004e64;
        }
        .action-box ul {
            margin: 12px 0;
            padding-left: 25px;
            line-height: 1.8;
        }
        .action-box li {
            color: #2c3e50;
        }
        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, #17a2b8 50%, transparent 100%);
            margin: 25px 0;
        }
        .footer {
            background: linear-gradient(135deg, #004e64 0%, #006d77 50%, #004e64 100%);
            color: #ffffff;
            text-align: center;
            padding: 25px 20px;
            font-size: 13px;
        }
        .footer::before {
            content: '';
            display: block;
            height: 3px;
            background: linear-gradient(90deg, #17a2b8 0%, #FFD23F 50%, #17a2b8 100%);
            margin-bottom: 20px;
        }
        .icon { font-size: 16px; margin-right: 6px; }
        @media (max-width: 600px) {
            body { padding: 10px; }
            .container { border-radius: 10px; }
            .header { padding: 25px 20px; }
            .content { padding: 25px 20px; }
            .info-box { padding: 16px 14px; }
            .section-header { margin: -16px -14px 14px -14px; padding: 10px 14px; font-size: 13px; }
            .detail-row { flex-direction: column; padding: 8px 0; }
            .value { text-align: left; margin-top: 4px; font-weight: bold; color: #2c3e50; }
            .activity-card { padding: 18px 14px; }
            .amount-highlight { font-size: 16px; padding: 10px 18px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><span class="icon">📦</span>New Booking Notification</h1>
            <p>A new activity booking has been received</p>
            <div><span class="supplier-badge">Supplier Notification</span></div>
        </div>

        <div class="content">
            <div class="greeting">
                Dear <strong>{{ $supplierName }}</strong>,<br>
                A customer has booked one of your activities. Please review the booking details below and prepare accordingly.
            </div>

            <div class="activity-card">
                <h2>{{ $booking['activityName'] ?? 'Activity Booking' }}</h2>
                <p><span class="icon">📍</span>{{ $booking['activityLocation'] ?? 'Location TBA' }}</p>
                <p><span class="icon">📅</span>{{ \Carbon\Carbon::parse($booking['date'])->format('l, F j, Y') }}</p>
                <p style="margin-top: 10px;"><span class="icon">🔖</span>Booking Ref: <strong>ORDAB{{ $booking['id'] ?? 'N/A' }}</strong></p>
            </div>

            <div class="info-box">
                <h3 class="section-header">
                    <span class="icon">👤</span>Customer Details
                </h3>

                <div class="detail-row">
                    <span class="label"><span class="icon">👤</span>Name:</span>
                    <span class="value">{{ $booking['name'] }}</span>
                </div>

                <div class="detail-row">
                    <span class="label"><span class="icon">📧</span>Email:</span>
                    <span class="value">{{ $booking['email'] }}</span>
                </div>

                <div class="detail-row">
                    <span class="label"><span class="icon">📞</span>Phone:</span>
                    <span class="value">{{ $booking['phone'] }}</span>
                </div>

                @if(!empty($booking['nationality']))
                <div class="detail-row">
                    <span class="label"><span class="icon">🌍</span>Nationality:</span>
                    <span class="value">{{ $booking['nationality'] }}</span>
                </div>
                @endif
            </div>

            <div class="info-box">
                <h3 class="section-header">
                    <span class="icon">🎫</span>Booking Details
                </h3>

                <div class="detail-row">
                    <span class="label"><span class="icon">👥</span>Adults:</span>
                    <span class="value">{{ $booking['adults'] }} person(s)</span>
                </div>

                <div class="detail-row">
                    <span class="label"><span class="icon">👶</span>Children:</span>
                    <span class="value">{{ $booking['childrens'] }} person(s)</span>
                </div>

                <div class="detail-row">
                    <span class="label"><span class="icon">🚐</span>Transfer:</span>
                    <span class="value">
                        @php
                            $transferMap = [
                                'abu_dhabi' => '🚐 Transport within Abu Dhabi',
                                'dubai' => '🚐 Transport within Dubai',
                                'abu_dhabi_to_dubai' => '🚐 Abu Dhabi ⇄ Dubai',
                                'any_emirates' => '🚐 Any Emirates',
                                'abu_bhabi' => '🚐 Transport from Abu Dhabi',
                                'du_bai' => '🚐 Transport from Dubai',
                                'without_transfer' => '🚫 Without Transport'
                            ];
                            $transferDisplay = $transferMap[$booking['transfer']] ?? ucwords(str_replace('_', ' ', $booking['transfer']));
                        @endphp
                        {{ $transferDisplay }}
                    </span>
                </div>

                @if(!empty($booking['remarks']))
                <div class="detail-row">
                    <span class="label"><span class="icon">📝</span>Special Requests:</span>
                    <span class="value">{{ $booking['remarks'] }}</span>
                </div>
                @endif
            </div>

            <div class="divider"></div>


            <div class="info-box">
                <h3 class="section-header">
                    <span class="icon">📊</span>Booking Status
                </h3>

                <div class="detail-row">
                    <span class="label"><span class="icon">📊</span>Status:</span>
                    <span class="value">
                        <span style="color: #006d77; font-weight: bold;">
                            {{ ucfirst($booking['status'] ?? 'Pending') }}
                        </span>
                    </span>
                </div>

                <div class="detail-row">
                    <span class="label"><span class="icon">📅</span>Booking Date:</span>
                    <span class="value">{{ \Carbon\Carbon::parse($booking['createDate'] ?? now())->format('M j, Y g:i A') }}</span>
                </div>
            </div>

            <div class="action-box">
                <strong><span class="icon">📋</span>Action Required:</strong>
                <ul>
                    <li>Please acknowledge this booking within 2 hours</li>
                    <li>Prepare the activity for the scheduled date</li>
                    <li>Coordinate transport arrangements if applicable</li>
                    <li>Contact the customer if you need additional details</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            <p><strong>{{ config('app.name') }}</strong></p>
            <p>This is an automated supplier notification. Please do not reply directly to this message.</p>
            <p style="font-size: 11px; opacity: 0.7; margin-top: 10px;">
                For support, contact the GoTrips team at {{ config('mail.from.address') }}
            </p>
        </div>
    </div>
</body>
</html>
