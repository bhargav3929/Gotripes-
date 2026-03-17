<!DOCTYPE html>
<html>
<head>
    <title>Payment Status Update</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #FFD23F 0%, #e0b000 100%); color: #000; padding: 25px; text-align: center; border-radius: 8px; margin-bottom: 30px; }
        .status-success { background-color: #d4edda; color: #155724; padding: 20px; border-radius: 8px; border: 2px solid #c3e6cb; margin: 20px 0; }
        .status-failed { background-color: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px; border: 2px solid #f5c6cb; margin: 20px 0; }
        .status-cancelled { background-color: #fff3cd; color: #856404; padding: 20px; border-radius: 8px; border: 2px solid #ffeaa7; margin: 20px 0; }
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .info-table th, .info-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .info-table th { background-color: #f8f9fa; font-weight: bold; }
        .footer { margin-top: 30px; text-align: center; color: #666; font-size: 14px; border-top: 2px solid #FFD23F; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ ucfirst($bookingType) }} Booking - Payment Update</h1>
            @if(isset($data['order_id']))
            <p>Order ID: {{ $data['order_id'] }}</p>
            @endif
        </div>

        <!-- Payment Status -->
        <div class="{{ $isSuccess ? 'status-success' : ($isFailed ? 'status-failed' : 'status-cancelled') }}">
            <h2 style="margin-top: 0;">
                @if($isSuccess)
                    ✅ Payment Completed Successfully!
                @elseif($isFailed)
                    ❌ Payment Failed
                @else
                    ⚠️ Payment Cancelled
                @endif
            </h2>
            
            <p><strong>Status:</strong> {{ $paymentStatus }}</p>
            
            @if(isset($data['tracking_id']))
            <p><strong>Tracking ID:</strong> {{ $data['tracking_id'] }}</p>
            @endif
            
            @if(isset($data['amount']))
            <p><strong>Amount:</strong> {{ $data['amount'] }} AED</p>
            @endif
        </div>

        <!-- Booking Details -->
        <h3>Booking Details:</h3>
        <table class="info-table">
            @if($bookingType === 'visa')
                <tr><th>Application ID</th><td>{{ $data['id'] ?? 'N/A' }}</td></tr>
                <tr><th>Name</th><td>{{ $data['UAEV_first_name'] ?? '' }} {{ $data['UAEV_last_name'] ?? '' }}</td></tr>
                <tr><th>Email</th><td>{{ $data['UAEV_email'] ?? 'N/A' }}</td></tr>
                <tr><th>Phone</th><td>{{ $data['UAEV_phone'] ?? 'N/A' }}</td></tr>
                <tr><th>Visa Duration</th><td>{{ $data['UAEV_visaDuration'] ?? 'N/A' }} Days</td></tr>
                <tr><th>Booking Created</th><td>{{ $data['UAEV_created_date'] ?? 'N/A' }}</td></tr>
            @elseif($bookingType === 'activity')
                <tr><th>Booking ID</th><td>{{ $data['id'] ?? 'N/A' }}</td></tr>
                <tr><th>Name</th><td>{{ $data['name'] ?? 'N/A' }}</td></tr>
                <tr><th>Email</th><td>{{ $data['email'] ?? 'N/A' }}</td></tr>
                <tr><th>Phone</th><td>{{ $data['phone'] ?? 'N/A' }}</td></tr>
                <tr><th>Activity</th><td>{{ $data['activity_name'] ?? 'N/A' }}</td></tr>
                <tr><th>Activity Date</th><td>{{ $data['booking_date'] ?? 'N/A' }}</td></tr>
                <!--<tr><th>Booking Created</th><td>{{ $data['created_at'] ?? 'N/A' }}</td></tr>-->
            @endif
        </table>

        <!-- Payment Details -->
        @if(isset($data['payment_mode']) || isset($data['bank_ref_no']))
        <h3>Payment Details:</h3>
        <table class="info-table">
            @if(isset($data['payment_mode']))
            <tr><th>Payment Mode</th><td>{{ $data['payment_mode'] }}</td></tr>
            @endif
            @if(isset($data['bank_ref_no']))
            <tr><th>Bank Reference</th><td>{{ $data['bank_ref_no'] }}</td></tr>
            @endif
            @if(isset($data['failure_message']) && $isFailed)
            <tr><th>Failure Reason</th><td>{{ $data['failure_message'] }}</td></tr>
            @endif
        </table>
        @endif

        <!-- Next Steps -->
        <div style="background-color: #e8f4fd; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h4>Next Steps:</h4>
            @if($isSuccess)
                <ul>
                    <li>Your payment has been confirmed</li>
                    <li>Processing will begin within 1-2 business days</li>
                    <li>You will receive updates via email and SMS</li>
                    <li>Keep your tracking ID for reference</li>
                </ul>
            @elseif($isFailed)
                <ul>
                    <li>Payment could not be processed</li>
                    <li>Please try again or contact support</li>
                    <li>Your booking is still reserved for 24 hours</li>
                </ul>
            @else
                <ul>
                    <li>Payment was cancelled by user</li>
                    <li>You can retry payment anytime</li>
                    <li>Your booking details are saved</li>
                </ul>
            @endif
        </div>

        <div class="footer">
            <p><strong>Customer Support</strong></p>
            <p>For queries, contact us with your Order ID: {{ $data['order_id'] ?? 'N/A' }}</p>
        </div>
    </div>
</body>
</html>
