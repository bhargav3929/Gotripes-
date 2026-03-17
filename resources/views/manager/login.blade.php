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
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
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
            border-radius: 50%; object-fit: cover;
            border: 3px solid #FFD700;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
        }
        .login-logo h1 {
            font-size: 20px; font-weight: 600;
            color: #FFD700; margin-top: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .login-card {
            background: #2a2a2a;
            border: 2px solid #FFD700;
            border-radius: 12px;
            padding: 30px 28px;
            width: 100%; max-width: 380px;
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.15);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, #FFD700, #FFA500, #FFD700);
        }

        .login-error {
            background: rgba(214, 54, 56, 0.15);
            border-left: 4px solid #d63638;
            padding: 10px 12px;
            margin-bottom: 16px;
            font-size: 13px;
            color: #f56565;
            border-radius: 0 4px 4px 0;
        }

        .form-group { margin-bottom: 18px; }

        .form-group label {
            display: block;
            font-size: 13px; font-weight: 600;
            color: #f0f0f0;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid rgba(255, 215, 0, 0.3);
            border-radius: 6px;
            font-size: 14px; font-family: inherit;
            color: #f0f0f0;
            background: #1a1a1a;
            height: 42px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-group input::placeholder {
            color: #666;
        }
        .form-group input:focus {
            outline: none;
            border-color: #FFD700;
            box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.25);
            background: #222;
        }

        .login-btn {
            width: 100%;
            padding: 10px 16px;
            background: linear-gradient(135deg, #FFD700, #FFA500);
            border: 1px solid #FFD700;
            border-radius: 6px;
            color: #1a1a1a;
            font-size: 14px; font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
            height: 42px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .login-btn:hover {
            background: linear-gradient(135deg, #FFA500, #FFD700);
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4);
            transform: translateY(-1px);
        }

        .login-footer {
            text-align: center;
            margin-top: 16px;
            font-size: 12px;
            color: #999;
        }
        .login-footer a { color: #FFD700; text-decoration: none; }
        .login-footer a:hover { color: #FFA500; text-decoration: underline; }
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
