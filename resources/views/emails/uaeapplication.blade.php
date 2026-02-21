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
            <tr><td>Full Name:</td><td>{{ $data['UAEV_first_name'] ?? '' }} {{ $data['UAEV_last_name'] ?? '' }}</td></tr>
            <tr><td>Gender:</td><td>{{ $data['UAEV_gender'] ?? '' }}</td></tr>
            <tr><td>Date of Birth:</td><td>{{ $data['UAEV_dob'] ?? '' }}</td></tr>
            <tr><td>Nationality:</td><td>{{ $data['UAEV_nationality'] ?? '' }}</td></tr>
            <tr><td>Current Residence:</td><td>{{ $data['UAEV_residence'] ?? '' }}</td></tr>
            <tr><td>Profession:</td><td>{{ $data['UAEV_profession'] ?? '' }}</td></tr>
            <tr><td>Marital Status:</td><td>{{ $data['UAEV_marital_status'] ?? '' }}</td></tr>

            <tr><td>Arrival in UAE:</td><td>{{ $data['UAEV_arrival_date'] ?? '' }}</td></tr>
            <tr><td>Departure from UAE:</td><td>{{ $data['UAEV_departure_date'] ?? '' }}</td></tr>
            <tr><td>Visit Visa Duration:</td><td>{{ $data['UAEV_visaDuration'] ?? '' }} days</td></tr>

            <tr><td>Phone:</td><td>{{ $data['UAEV_phone'] ?? '' }}</td></tr>
            <tr><td>Email:</td><td>{{ $data['UAEV_email'] ?? '' }}</td></tr>

            <tr><td>Passport valid for 6+ months:</td><td>{{ (!empty($data['UAEV_passport_valid']) && $data['UAEV_passport_valid'] == 1) ? 'Yes' : 'No' }}</td></tr>
            <tr><td>Does NOT wish to stay over 30 days:</td><td>{{ (!empty($data['UAEV_not_stay_long']) && $data['UAEV_not_stay_long'] == 1) ? 'Yes' : 'No' }}</td></tr>

            <tr><td>Price:</td><td>{{ $data['UAEV_price'] ?? '' }}</td></tr>

            <tr><td>Created By:</td><td>{{ $data['UAEV_Created_by'] ?? (($data['UAEV_first_name'] ?? '') . ' ' . ($data['UAEV_last_name'] ?? '')) }}</td></tr>
            <tr><td>Created Date:</td><td>{{ $data['UAEV_created_date'] ?? \Illuminate\Support\Carbon::now() }}</td></tr>
        </table>

        <div class="message">
            <strong>Passport copy and photo are attached.</strong>
        </div>
        <div class="footer">
            Powered by GOTRIPS &mdash; UAE Visa System
        </div>
    </div>
</body>
</html>