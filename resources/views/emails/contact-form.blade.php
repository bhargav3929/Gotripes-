<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f8;
            margin: 0;
            padding: 20px;
            color: #333333;
        }
        .container {
            max-width: 600px;
            background-color: #ffffff;
            margin: auto;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2a7ae2;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
            margin: 12px 0;
        }
        strong {
            color: #222222;
        }
        .message {
            background-color: #f1f5f9;
            padding: 15px 20px;
            border-radius: 6px;
            border-left: 4px solid #2a7ae2;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>New Contact Form Submission</h2>

        <p><strong>Name:</strong> {{ $data['name'] }}</p>
        <p><strong>Email:</strong> {{ $data['email'] }}</p>
        <p><strong>Phone:</strong> {{ $data['phone'] }}</p>
        <p><strong>Regarding:</strong> {{ $data['booking_city'] }}</p>
        <p><strong>Message:</strong></p>
        <p class="message">{{ $data['message'] }}</p>
    </div>
</body>
</html>
