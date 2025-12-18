<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Payment Status</title>
    <style>
        body {
            background-color: #121212;
            color: #d4af37; /* golden text */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            background-color: #1e1e1e; /* darker card */
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.4);
            max-width: 600px;
            width: 100%;
        }
        h1 {
            font-weight: 700;
            border-bottom: 3px solid #d4af37;
            padding-bottom: 10px;
            margin-bottom: 30px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #f9d342;
        }
        p {
            font-size: 1.1rem;
            margin: 15px 0;
        }
        .success {
            color: #a4c639; /* bright green */
            font-weight: 600;
            font-size: 1.25rem;
            text-align: center;
        }
        .failure {
            color: #fa7268; /* muted red */
            font-weight: 600;
            font-size: 1.25rem;
            text-align: center;
        }
        strong {
            color: #f9d342; /* lighter gold for strong emphasis */
        }
        hr {
            border: none;
            border-top: 1px solid #d4af37;
            margin: 30px 0;
            opacity: 0.3;
        }
        ul {
            list-style: none;
            padding: 0;
            font-size: 1.1rem;
        }
        ul li {
            padding: 10px 0;
            border-bottom: 1px solid #333;
        }
        a {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 30px;
            background-color: #d4af37;
            color: #121212;
            font-weight: 700;
            text-decoration: none;
            text-transform: uppercase;
            border-radius: 25px;
            text-align: center;
            transition: background-color 0.3s ease, color 0.3s ease;
            box-shadow: 0 3px 8px rgba(212, 175, 55, 0.6);
        }
        a:hover {
            background-color: #f9d342;
            color: #1e1e1e;
            box-shadow: 0 4px 12px rgba(249, 211, 66, 0.9);
        }
        @media (max-width: 480px) {
            body {
                padding: 20px 10px;
            }
            .container {
                padding: 20px 25px;
            }
            p, ul li {
                font-size: 1rem;
            }
            a {
                padding: 10px 25px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Payment Status</h1>

        @if (($response['order_status'] ?? '') === 'Success')
            <p class="success">Your payment was successful! Thank you for your purchase.</p>
        @else
            <p class="failure">Payment failed or was declined.</p>
            <p><strong>Status Message:</strong> {{ $response['status_message'] ?? 'No details available' }}</p>
            <p><strong>Failure Reason:</strong> {{ $response['failure_message'] ?? 'Not provided' }}</p>
        @endif

        <hr>

        <h3>Transaction Details:</h3>
        <ul>
            <li><strong>Order ID:</strong> {{ $response['order_id'] ?? 'N/A' }}</li>
            <li><strong>Amount:</strong> {{ $response['amount'] ?? 'N/A' }} {{ $response['currency'] ?? 'N/A' }}</li>
            <li><strong>Payment Mode:</strong> {{ $response['payment_mode'] ?? 'N/A' }}</li>
            <li><strong>Bank Reference No:</strong> {{ $response['bank_ref_no'] ?? 'N/A' }}</li>
            <li><strong>Tracking ID:</strong> {{ $response['tracking_id'] ?? 'N/A' }}</li>
        </ul>

        <a href="{{ url('/') }}">Return to Home</a>
    </div>
</body>
</html>
