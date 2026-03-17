<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Payment Cancelled</title>

    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        body {
            background-color: #121212;
            color: #d4af37;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            height: 100vh;
        }

        .custom-container {
            background-color: #1e1e1e;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.4);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }

        h1 {
            color: #f9d342;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        a.btn-custom {
            background-color: #d4af37;
            color: #121212;
            font-weight: 700;
            text-transform: uppercase;
            border-radius: 25px;
            box-shadow: 0 3px 8px rgba(212, 175, 55, 0.6);
            padding: 12px 30px;
        }

        a.btn-custom:hover {
            background-color: #f9d342;
            color: #121212;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="custom-container">
            <h1>Payment Cancelled</h1>
            <p>Your payment was cancelled. No charges were made.</p>
            <a href="{{ url('/') }}" class="btn btn-custom">Return to Home</a>
        </div>
    </div>

    <!-- Bootstrap JS Bundle (Optional, if you need JS components) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
