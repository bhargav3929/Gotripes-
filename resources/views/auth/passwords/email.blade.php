@extends('layouts.auth-card')

@section('title', 'Reset password')

@section('content')
    <div class="card-title">Forgot your password?</div>
    <p class="card-sub">Enter the email you sign in with. If it matches an account, we will send a link to choose a new password.</p>

    @if (session('status'))
        <div class="login-ok" role="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" onsubmit="var b=this.querySelector('button');b.disabled=true;b.textContent='Sending...';">
        @csrf
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', request('email')) }}" placeholder="you@example.com" required autofocus autocomplete="email">
            @error('email')<div class="field-err" role="alert">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="login-btn">Send reset link</button>
    </form>
@endsection
