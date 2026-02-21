<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Admin Login</title>
    
    <!-- Bootstrap CSS (if needed) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .admin-card {
            background: #2a2a2a;
            border: 2px solid #d4af37;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(212, 175, 55, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 420px;
            position: relative;
            overflow: hidden;
        }

        .admin-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #d4af37, #f4d03f, #d4af37);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .admin-logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #d4af37;
            display: block;
            margin: 0 auto 30px;
            object-fit: cover;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.4);
        }

        .admin-title {
            text-align: center;
            color: #d4af37;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .admin-subtitle {
            text-align: center;
            color: #888;
            font-size: 14px;
            margin-bottom: 35px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .admin-input {
            width: 100%;
            padding: 15px 20px;
            background: #1a1a1a;
            border: 2px solid #444;
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .admin-input:focus {
            outline: none;
            border-color: #d4af37;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.3);
            background: #222;
        }

        .admin-input::placeholder {
            color: #666;
            font-weight: 500;
        }

        .admin-input.is-invalid {
            border-color: #e74c3c;
            box-shadow: 0 0 15px rgba(231, 76, 60, 0.3);
        }

        .invalid-feedback {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 8px;
            display: block;
        }

        .admin-checkbox-wrapper {
            display: flex;
            align-items: center;
            margin: 25px 0;
        }

        .admin-checkbox {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            accent-color: #d4af37;
        }

        .admin-checkbox-label {
            color: #ccc;
            font-size: 14px;
            cursor: pointer;
            user-select: none;
        }

        .admin-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #d4af37, #f4d03f);
            border: none;
            border-radius: 8px;
            color: #1a1a1a;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .admin-btn:hover {
            background: linear-gradient(45deg, #f4d03f, #d4af37);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4);
        }

        .admin-btn:active {
            transform: translateY(0);
        }

        .forgot-password-link {
            display: block;
            text-align: center;
            color: #d4af37;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .forgot-password-link:hover {
            color: #f4d03f;
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .admin-card {
                margin: 10px;
                padding: 30px 25px;
            }
            
            .admin-title {
                font-size: 24px;
            }
            
            .admin-logo {
                width: 80px;
                height: 80px;
            }
        }

        /* Dark scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        ::-webkit-scrollbar-thumb {
            background: #d4af37;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #f4d03f;
        }
    </style>
</head>
<body>
    <div class="admin-card">
        <img class="admin-logo" src="{{ asset('assets/index_files/logo.png') }}" alt="Admin Logo" />
        
        <h1 class="admin-title">Admin Portal</h1>
        <p class="admin-subtitle">Secure Administrator Access</p>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <input id="name" 
                       placeholder="Administrator Name" 
                       type="name" 
                       class="admin-input @error('name') is-invalid @enderror" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required 
                       autocomplete="name" 
                       autofocus>

                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <input id="password" 
                       placeholder="Password" 
                       type="password" 
                       class="admin-input @error('password') is-invalid @enderror" 
                       name="password" 
                       required 
                       autocomplete="current-password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="admin-checkbox-wrapper">
                <input class="admin-checkbox" 
                       type="checkbox" 
                       name="remember" 
                       id="remember" 
                       {{ old('remember') ? 'checked' : '' }}>
                <label class="admin-checkbox-label" for="remember">
                    {{ __('Keep me signed in') }}
                </label>
            </div>

            <button type="submit" class="admin-btn">
                {{ __('Access Admin Panel') }}
            </button>

            @if (Route::has('password.request'))
                <a class="forgot-password-link" href="{{ route('password.request') }}">
                    {{ __('Forgot Your Password?') }}
                </a>
            @endif
        </form>
    </div>
</body>
</html>
