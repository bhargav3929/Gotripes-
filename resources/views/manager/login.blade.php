<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manager Login - Go Trips</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/index_files/logo.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f0f0f1;
            min-height: 100vh;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 20px;
        }

        .login-logo {
            text-align: center; margin-bottom: 24px;
        }
        .login-logo img {
            width: 80px; height: 80px;
            border-radius: 12px; object-fit: cover;
        }
        .login-logo h1 {
            font-size: 20px; font-weight: 600;
            color: #1d2327; margin-top: 12px;
        }

        .login-card {
            background: #fff;
            border: 1px solid #c3c4c7;
            border-radius: 4px;
            padding: 26px 24px;
            width: 100%; max-width: 360px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .login-error {
            background: #fcecec;
            border-left: 4px solid #d63638;
            padding: 10px 12px;
            margin-bottom: 16px;
            font-size: 13px;
            color: #8a1f1f;
            border-radius: 0 4px 4px 0;
        }

        .form-group { margin-bottom: 16px; }

        .form-group label {
            display: block;
            font-size: 13px; font-weight: 600;
            color: #1d2327;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 6px 10px;
            border: 1px solid #8c8f94;
            border-radius: 4px;
            font-size: 14px; font-family: inherit;
            color: #1d2327;
            height: 36px;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .form-group input:focus {
            outline: none;
            border-color: #2271b1;
            box-shadow: 0 0 0 1px #2271b1;
        }

        .login-btn {
            width: 100%;
            padding: 8px 16px;
            background: #2271b1;
            border: 1px solid #2271b1;
            border-radius: 4px;
            color: #fff;
            font-size: 13px; font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
            font-family: inherit;
            height: 36px;
        }
        .login-btn:hover {
            background: #135e96;
            border-color: #135e96;
        }

        .login-footer {
            text-align: center;
            margin-top: 16px;
            font-size: 12px;
            color: #787c82;
        }
        .login-footer a { color: #2271b1; text-decoration: none; }
        .login-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-logo">
        <img src="{{ asset('assets/index_files/logo.png') }}" alt="Go Trips">
        <h1>Manager Portal</h1>
    </div>

    <div class="login-card">
        @if($errors->has('credentials'))
            <div class="login-error">
                {{ $errors->first('credentials') }}
            </div>
        @endif

        <form method="POST" action="{{ route('manager.login.submit') }}">
            @csrf

            <div class="form-group">
                <label for="username">Username</label>
                <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" required>
            </div>

            <button type="submit" class="login-btn">Log In</button>
        </form>
    </div>

    <div class="login-footer">
        <a href="{{ url('/') }}">Back to Go Trips</a>
    </div>
</body>
</html>
