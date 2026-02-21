<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Activity Booking {{ $isAdmin ? 'Request' : 'Confirmation' }}</title>
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
            overflow: hidden;
        }
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 210, 63, 0.1) 50%, transparent 70%);
            pointer-events: none;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            position: relative;
            z-index: 1;
        }
        .header p {
            position: relative;
            z-index: 1;
        }
        .content {
            padding: 40px 30px;
            background: linear-gradient(to bottom, #ffffff 0%, #fafafa 100%);
        }
        .booking-details {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border: 2px solid #FFD23F;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            box-shadow: 0 8px 25px rgba(255, 210, 63, 0.15);
            position: relative;
        }
        .booking-details::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #FFD23F 0%, #ffc107 50%, #FFD23F 100%);
            border-radius: 12px 12px 0 0;
        }
        .section-header {
            margin: -25px -25px 20px -25px;
            padding: 15px 25px;
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            color: #FFD23F;
            font-size: 16px;
            font-weight: bold;
            border-radius: 10px 10px 0 0;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
            border-bottom: 2px solid #FFD23F;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .detail-row:hover {
            background: rgba(255, 210, 63, 0.05);
            padding: 12px 15px;
            margin: 15px -15px;
            border-radius: 8px;
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
        .highlight-box {
            background: {{ $isAdmin ? 'linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%)' : 'linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%)' }};
            border: 2px solid {{ $isAdmin ? '#2196f3' : '#FFD23F' }};
            padding: 20px;
            border-radius: 12px;
            margin: 25px 0;
            box-shadow: 0 4px 15px {{ $isAdmin ? 'rgba(33, 150, 243, 0.2)' : 'rgba(255, 210, 63, 0.3)' }};
            position: relative;
        }
        .highlight-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: {{ $isAdmin ? '#2196f3' : '#FFD23F' }};
            border-radius: 0 0 0 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            background: {{ $isAdmin ? 'linear-gradient(135deg, #2196f3 0%, #1976d2 100%)' : 'linear-gradient(135deg, #FFD23F 0%, #ffc107 100%)' }};
            color: {{ $isAdmin ? 'white' : '#1a1a1a' }};
            border-radius: 25px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px {{ $isAdmin ? 'rgba(33, 150, 243, 0.4)' : 'rgba(255, 210, 63, 0.4)' }};
        }
        .footer {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 50%, #000000 100%);
            color: #FFD23F;
            text-align: center;
            padding: 30px 20px;
            font-size: 14px;
            position: relative;
        }
        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #FFD23F 0%, #ffc107 50%, #FFD23F 100%);
        }
        .activity-info {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            color: #FFD23F;
            padding: 30px;
            margin: 25px 0;
            border-radius: 15px;
            text-align: center;
            border: 2px solid #FFD23F;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            position: relative;
        }
        .activity-info::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            border-radius: 15px;
            background: linear-gradient(45deg, #FFD23F, #ffc107, #FFD23F);
            z-index: -1;
        }
        .activity-info h2 {
            margin: 0 0 15px 0;
            color: #FFD23F;
            font-size: 24px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        .activity-info p {
            margin: 8px 0;
            font-size: 16px;
            color: #f0f0f0;
        }
        .amount-highlight {
            background: linear-gradient(135deg, #FFD23F 0%, #ffc107 100%);
            color: #1a1a1a;
            padding: 15px 25px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 20px;
            display: inline-block;
            margin: 15px 0;
            box-shadow: 0 8px 25px rgba(255, 210, 63, 0.4);
            border: 2px solid #e6ac00;
            text-shadow: 1px 1px 2px rgba(255,255,255,0.3);
        }
        .divider {
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, #FFD23F 50%, transparent 100%);
            margin: 30px 0;
            border-radius: 1px;
        }
        .icon {
            font-size: 18px;
            margin-right: 8px;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .container {
                border-radius: 10px;
            }
            .header {
                padding: 25px 20px;
            }
            .content {
                padding: 25px 20px;
            }
            .booking-details {
                padding: 20px 15px;
                margin: 20px 0;
            }
            .section-header {
                margin: -20px -15px 15px -15px;
                padding: 12px 15px;
                font-size: 14px;
            }
            .detail-row {
                flex-direction: column;
                padding: 10px 0;
            }
            .value {
                text-align: left;
                margin-top: 5px;
                font-weight: bold;
                color: #2c3e50;
            }
            .activity-info {
                padding: 20px 15px;
            }
            .amount-highlight {
                font-size: 18px;
                padding: 12px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><span class="icon">🎫</span>Activity Booking {{ $isAdmin ? 'Request' : 'Confirmation' }}</h1>
            <p style="margin: 15px 0 0 0;">
                <span class="status-badge">{{ $isAdmin ? 'New Request' : 'Confirmed' }}</span>
            </p>
        </div>
        
        <div class="content">
            @if($isAdmin)
                <div class="highlight-box">
                    <strong><span class="icon">📋</span>Action Required:</strong> New activity booking request received. Customer will be contacted for payment assistance.
                </div>
            @else
                <div class="highlight-box">
                    <strong><span class="icon">🎉</span>Thank you for your booking!</strong> Our team will contact you shortly to assist with payment and confirm your booking details.
                </div>
            @endif

            <div class="activity-info">
                <h2>{{ $booking['activityName'] ?? 'Activity Booking' }}</h2>
                <p><span class="icon">📍</span>{{ $booking['activityLocation'] ?? 'Location TBA' }}</p>
                <p><span class="icon">📅</span>{{ \Carbon\Carbon::parse($booking['date'])->format('l, F j, Y') }}</p>
                <p style="margin-top: 10px;"><span class="icon">🔖</span>Order ID: <strong>ORDAB{{ $booking['id'] ?? 'N/A' }}</strong></p>
            </div>

            <div class="booking-details">
                <h3 class="section-header">
                    <span class="icon">👤</span>Customer Information
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
                
                <div class="detail-row">
                    <span class="label"><span class="icon">🏠</span>Address:</span>
                    <span class="value">{{ $booking['address'] }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="label"><span class="icon">🌍</span>Nationality:</span>
                    <span class="value">{{ $booking['nationality'] }}</span>
                </div>
            </div>

            <div class="booking-details">
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
                                'abu_bhabi' => '🚐 Transport from Abu Dhabi',
                                'du_bai' => '🚐 Transport from Dubai',
                                'without_transfer' => '🚫 Without Transport'
                            ];
                            $transferDisplay = $transferMap[$booking['transfer']] ?? ucwords(str_replace('_', ' ', $booking['transfer']));
                        @endphp
                        {{ $transferDisplay }}
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="label"><span class="icon">💳</span>Payment Option:</span>
                    <span class="value">
                        @if($booking['paymentOption'] == 'book_with_us')
                            📞 Book With Us (Agent Assisted)
                        @else
                            💳 Book & Pay Yourself
                        @endif
                    </span>
                </div>
                
                @if(!empty($booking['remarks']))
                <div class="detail-row">
                    <span class="label"><span class="icon">📝</span>Special Requests:</span>
                    <span class="value">{{ $booking['remarks'] }}</span>
                </div>
                @endif
            </div>

            @if(!empty($booking['supplierName']))
            <div class="booking-details">
                <h3 class="section-header">
                    <span class="icon">🏢</span>Supplier / Organizer Details
                </h3>
                <div class="detail-row">
                    <span class="label"><span class="icon">🏢</span>Name:</span>
                    <span class="value">{{ $booking['supplierName'] }}</span>
                </div>
                @if(!empty($booking['supplierEmail']))
                <div class="detail-row">
                    <span class="label"><span class="icon">📧</span>Email:</span>
                    <span class="value">{{ $booking['supplierEmail'] }}</span>
                </div>
                @endif
            </div>
            @endif

            <div class="divider"></div>

            <div class="booking-details">
                <h3 class="section-header">
                    <span class="icon">💰</span>Payment Information
                </h3>
                
                <div style="text-align: center;">
                    <div class="amount-highlight">
                        <span class="icon">💰</span>Total Amount: {{ $booking['amount'] }} {{ $booking['currency'] }}
                    </div>
                </div>
                
                <div class="detail-row">
                    <span class="label"><span class="icon">📊</span>Status:</span>
                    <span class="value">
                        <span style="color: #ffc107; font-weight: bold;">
                            {{ ucfirst($booking['status']) }}
                        </span>
                    </span>
                </div>
                
                <div class="detail-row">
                    <span class="label"><span class="icon">📅</span>Booking Date:</span>
                    <span class="value">{{ \Carbon\Carbon::parse($booking['createDate'])->format('M j, Y g:i A') }}</span>
                </div>
            </div>

            @if($isAdmin)
                <div class="highlight-box">
                    <strong><span class="icon">📞</span>Next Steps:</strong>
                    <ul style="margin: 15px 0; padding-left: 25px; line-height: 1.8;">
                        <li>Contact customer within 2 hours</li>
                        <li>Confirm booking details</li>
                        <li>Assist with payment process</li>
                        <li>Send confirmation once payment is complete</li>
                    </ul>
                </div>
            @else
                <div class="highlight-box">
                    <strong><span class="icon">📞</span>What's Next?</strong>
                    <p>Our booking specialist will contact you within 2 hours to:</p>
                    <ul style="margin: 15px 0; padding-left: 25px; line-height: 1.8;">
                        <li>Confirm your booking details</li>
                        <li>Assist with secure payment</li>
                        <li>Provide activity information</li>
                        <li>Answer any questions you may have</li>
                    </ul>
                </div>
            @endif
        </div>

        <div class="footer">
            <p><strong>{{ config('app.name') }}</strong></p>
            <p>Thank you for choosing us for your activity booking!</p>
            <div class="divider" style="background: linear-gradient(90deg, transparent 0%, #FFD23F 50%, transparent 100%); margin: 20px 0;"></div>
            <p style="font-size: 14px; font-weight: bold; color: #FFD23F;">Need Assistance?</p>
            <p style="font-size: 13px; color: #e0e0e0; margin-top: 5px;">
                📧 Email: {{ config('mail.from.address') }}<br>
                📞 Support: +971 (0) 4 123 4567 (9 AM - 6 PM GST)
            </p>
            <p style="font-size: 12px; color: #cccccc; margin-top: 20px;">
                This is an automated email. Please do not reply directly to this message.
            </p>
        </div>
    </div>
</body>
</html>
