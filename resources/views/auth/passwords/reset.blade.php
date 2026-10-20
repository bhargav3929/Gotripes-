@extends('layouts.auth-card')

@section('title', 'Choose a new password')

@section('content')
    <div class="card-title">Choose a new password</div>
    <p class="card-sub">At least 8 characters, with letters and numbers. Saving signs you in and signs out every other device.</p>

    @if ($errors->has('email') || $errors->has('token'))
        <div class="login-error" role="alert">{{ $errors->first('email') ?: $errors->first('token') }}
            <a href="{{ route('password.request') }}" style="color:inherit;text-decoration:underline;">Request a new link</a>.</div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email">
        </div>

        <div class="form-group">
            <label for="password">New password</label>
            <div style="position:relative;">
                <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password" autofocus style="padding-right:64px;">
                <button type="button" id="togglePw" aria-label="Show password" style="position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:0;color:#FFD700;font-size:12px;cursor:pointer;font-family:inherit;">Show</button>
            </div>
            @error('password')<div class="field-err" role="alert">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label for="password-confirm">Confirm new password</label>
            <input id="password-confirm" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
        </div>

        <button type="submit" class="login-btn">Save password</button>
    </form>

    <script>
        (function () {
            var b = document.getElementById('togglePw'), p = document.getElementById('password'), c = document.getElementById('password-confirm');
            b.addEventListener('click', function () {
                var show = p.type === 'password';
                p.type = c.type = show ? 'text' : 'password';
                b.textContent = show ? 'Hide' : 'Show';
                b.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            });
        })();
    </script>
@endsection
