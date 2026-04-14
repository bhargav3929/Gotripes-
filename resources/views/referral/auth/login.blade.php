<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/index_files/logo.png') }}">

    <title>Partner Login - GoTrips</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-gold: #FFD700;
            --secondary-gold: #FFA500;
            --dark-bg: #0a0a0b;
            --darker-bg: #060608;
            --light-dark: #131316;
            --card-bg: #16161a;
            --border-color: rgba(255, 255, 255, 0.06);
            --border-gold: rgba(255, 215, 0, 0.15);
            --text-main: #e2e8f0;
            --text-muted: #8a8f98;
            --radius-md: 12px;
            --radius-lg: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--dark-bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary-gold);
            margin-bottom: 0.5rem;
        }

        .login-logo span {
            color: var(--text-main);
        }

        .login-subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .login-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 2rem;
        }

        .login-card h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-label {
            color: var(--text-main);
            font-weight: 500;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .form-control {
            background: var(--light-dark);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 1rem;
        }

        .form-control:focus {
            background: var(--light-dark);
            border-color: var(--primary-gold);
            color: var(--text-main);
            box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.15);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .input-group-text {
            background: var(--light-dark);
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            border-right: none;
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--primary-gold);
        }

        .btn-gold {
            background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
            border: none;
            color: #000;
            font-weight: 600;
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
            width: 100%;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-gold:hover {
            background: linear-gradient(135deg, var(--secondary-gold), var(--primary-gold));
            color: #000;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.25);
        }

        .form-check-input {
            background-color: var(--light-dark);
            border-color: var(--border-color);
        }

        .form-check-input:checked {
            background-color: var(--primary-gold);
            border-color: var(--primary-gold);
        }

        .form-check-label {
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border-radius: 8px;
        }

        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
        }

        .login-footer a {
            color: var(--primary-gold);
            text-decoration: none;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--border-color);
        }

        .divider span {
            padding: 0 1rem;
            color: var(--text-muted);
            font-size: 0.875rem;
        }

        /* Floating background elements */
        .bg-decoration {
            position: fixed;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.05), rgba(255, 165, 0, 0.02));
            filter: blur(60px);
            z-index: -1;
        }

        .bg-decoration.top-left {
            top: -100px;
            left: -100px;
        }

        .bg-decoration.bottom-right {
            bottom: -100px;
            right: -100px;
        }
        /* Text Visibility Fixes */
        p, span, div, li, small {
            color: #e2e8f0;
        }
        .text-muted {
            color: #a0aec0 !important;
        }
        h1, h2, h3, h4, h5, h6, strong {
            color: #ffffff !important;
        }
    </style>
</head>

<body>
    <div class="bg-decoration top-left"></div>
    <div class="bg-decoration bottom-right"></div>

    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">Go<span>Trips</span></div>
            <p class="login-subtitle">Partner Portal</p>
        </div>

        <div class="login-card">
            <h2><i class="fas fa-user-tie me-2" style="color: var(--primary-gold);"></i>Partner Login</h2>

            @if($errors->any())
            <div class="alert alert-danger mb-4">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('referral.login.submit') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email') }}" required autofocus
                               placeholder="partner@example.com">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control"
                               required placeholder="Enter your password">
                    </div>
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-gold">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>
            </form>
        </div>

        <div class="login-footer">
            <p class="text-muted mb-2">
                Not a partner yet?
                <a href="{{ url('/') }}">Visit GoTrips.ai</a>
            </p>
            <p class="text-muted small">
                <i class="fas fa-shield-alt me-1"></i>
                Secure login powered by GoTrips
            </p>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
