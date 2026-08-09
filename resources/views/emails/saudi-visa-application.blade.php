<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000000; /* Black background */
            margin: 0;
            padding: 20px;
            color: #d4af37; /* Gold text */
        }
        .container {
            max-width: 700px;
            margin: auto;
            padding: 28px 32px;
            border-radius: 8px;
            background-color: #121212; /* Very dark charcoal */
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.6);
            color: #d4af37;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        tr {
            border-bottom: 1px solid #3e2f00;
        }
        td {
            padding: 12px 10px;
            vertical-align: top;
        }
        td:first-child {
            background-color: #d4af37; /* Gold for label */
            color: #000000; /* Black text on gold */
            width: 35%;
            font-weight: 600;
            white-space: nowrap;
        }
        td:last-child {
            background-color: #2c2300; /* Dark gold background for values */
            color: #f8e9a1; /* Pale gold text */
            width: 65%;
        }
        .message {
            background-color: #333333;
            padding: 13px 18px;
            border-radius: 6px;
            border-left: 4px solid #d4af37;
            margin-top: 18px;
            white-space: pre-wrap;
            color: #d4af37;
            font-weight: 600;
        }
        .footer {
            margin-top: 28px;
            text-align: center;
            font-size: 12px;
            color: #a88633;
        }
    </style>
</head>
<body>
    <div class="container">
        <table>
            <tr><td>Application ID:</td><td>{{ $data['id'] ?? '' }}</td></tr>
            <tr><td>Order ID:</td><td>{{ $data['order_id'] ?? '' }}</td></tr>
            <tr><td>Visa Type:</td><td>{{ $data['visa_type'] ?? '' }}</td></tr>
            <tr><td>Full Name:</td><td>{{ $data['full_name'] ?? (trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''))) }}</td></tr>
            <tr><td>Gender:</td><td>{{ $data['gender'] ?? '' }}</td></tr>
            <tr><td>Date of Birth:</td><td>{{ $data['dob'] ?? '' }}</td></tr>
            <tr><td>Nationality:</td><td>{{ $data['nationality'] ?? '' }}</td></tr>
            <tr><td>Passport Number:</td><td>{{ $data['passport_number'] ?? '' }}</td></tr>
            <tr><td>Passport Expiry:</td><td>{{ $data['passport_expiry'] ?? '' }}</td></tr>

            <tr><td>Phone:</td><td>{{ $data['phone'] ?? '' }}</td></tr>
            <tr><td>Email:</td><td>{{ $data['email'] ?? '' }}</td></tr>

            <tr><td>Price:</td><td>AED {{ isset($data['price']) ? number_format((float) $data['price'], 2) : '' }}</td></tr>
            <tr><td>Payment Status:</td><td>{{ ucfirst($data['payment_status'] ?? 'paid') }}</td></tr>
            <tr><td>Created Date:</td><td>{{ $data['created_at'] ?? \Illuminate\Support\Carbon::now() }}</td></tr>
        </table>

        @php
            // Name every file actually attached, so the supplier can tell at a
            // glance whether the Emirates ID / GCC residence copy came through.
            $attached = ['Passport copy', 'photo'];
            if (!empty($data['emirates_id_path']))    { $attached[] = 'Emirates ID / GCC residence copy'; }
            if (!empty($data['additional_doc_path'])) { $attached[] = 'additional document'; }
            $last = array_pop($attached);
        @endphp
        <div class="message">
            <strong>{{ $attached ? implode(', ', $attached) . ' and ' . $last : $last }} are attached.</strong>
        </div>
        <div class="footer">
            Powered by GOTRIPS &mdash; Saudi Visa System
        </div>
    </div>
</body>
</html>
