<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $authTenant = current_company();
        $authName = $authTenant?->name ?? 'Manager Portal';
        $authLogo = $authTenant?->logo_url ?? asset('assets/index_files/logo.png');
        $authFavicon = $authTenant?->favicon_url ?? asset('assets/index_files/logo.png');
    @endphp
    <title>@yield('title', 'Account') - {{ $authName }}</title>
    <link rel="icon" type="image/png" href="{{ $authFavicon }}">

    {{-- Self-contained on purpose: the stock Laravel auth pages pulled
         cssAdmin/app.css and jsAdmin/app.js, which are not deployed, so they
         rendered as unstyled HTML. --}}
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh; display: flex; flex-direction: column;
            align-items: center; justify-content: center; padding: 20px;
        }
        .login-logo { text-align: center; margin-bottom: 24px; }
        .login-logo img {
            width: 80px; height: 80px; border-radius: 50%; object-fit: cover;
            border: 3px solid #FFD700; box-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
        }
        .login-logo h1 {
            font-size: 20px; font-weight: 600; color: #FFD700; margin-top: 12px;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .login-card {
            background: #2a2a2a; border: 2px solid #FFD700; border-radius: 12px;
            padding: 30px 28px; width: 100%; max-width: 380px;
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.15); position: relative; overflow: hidden;
        }
        .login-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, #FFD700, #FFA500, #FFD700);
        }
        .card-title { font-size: 18px; font-weight: 700; color: #f0f0f0; margin-bottom: 6px; }
        .card-sub { font-size: 13px; color: #aaa; line-height: 1.5; margin-bottom: 20px; }
        .login-error, .login-ok {
            padding: 10px 12px; margin-bottom: 16px; font-size: 13px; border-radius: 0 4px 4px 0; line-height: 1.45;
        }
        .login-error { background: rgba(214, 54, 56, 0.15); border-left: 4px solid #d63638; color: #f56565; }
        .login-ok { background: rgba(70, 180, 80, 0.15); border-left: 4px solid #46b450; color: #7ad47f; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; font-size: 13px; font-weight: 600; color: #f0f0f0; margin-bottom: 6px; }
        .form-group input {
            width: 100%; padding: 10px 14px; border: 1px solid rgba(255, 215, 0, 0.3); border-radius: 6px;
            font-size: 14px; font-family: inherit; color: #f0f0f0; background: #1a1a1a; height: 42px;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-group input:focus {
            outline: none; border-color: #FFD700; box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.25); background: #222;
        }
        .field-err { color: #f56565; font-size: 12px; margin-top: 6px; }
        .login-btn {
            width: 100%; padding: 10px 16px; background: linear-gradient(135deg, #FFD700, #FFA500);
            border: 1px solid #FFD700; border-radius: 6px; color: #1a1a1a; font-size: 14px; font-weight: 700;
            cursor: pointer; transition: all 0.2s; font-family: inherit; height: 42px;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .login-btn:hover { background: linear-gradient(135deg, #FFA500, #FFD700); box-shadow: 0 4px 15px rgba(255, 215, 0, 0.4); transform: translateY(-1px); }
        .login-btn:disabled { opacity: .6; cursor: wait; transform: none; }
        .login-footer { text-align: center; margin-top: 16px; font-size: 12px; color: #999; }
        .login-footer a { color: #FFD700; text-decoration: none; }
        .login-footer a:hover { color: #FFA500; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-logo">
        <img src="{{ $authLogo }}" alt="{{ $authName }}">
        <h1>Manager Portal</h1>
    </div>

    <div class="login-card">
        @yield('content')
    </div>

    <div class="login-footer">
        <a href="{{ route('manager.login') }}">Back to sign in</a>
    </div>
</body>
</html>
