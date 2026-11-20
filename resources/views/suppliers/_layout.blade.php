<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') · {{ $company?->name ?? 'GoTrips' }}</title>
    <link rel="icon" href="{{ $company?->logo_url ?? asset('assets/index_files/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #16140f;
            --ink-2: #4a463d;
            --muted: #6f6a5e;
            --line: #e4dfd2;
            --line-strong: #cfc8b6;
            --paper: #f7f4ec;
            --card: #fffdf8;
            --bar: #121110;
            --gold: #f2c200;
            --gold-ink: #6b5200;
            --gold-soft: #fff6cc;
            --ok: #1d7a3d;
            --ok-soft: #e5f4ea;
            --err: #b42318;
            --err-soft: #fdecea;
            --r: 8px;
            --r-lg: 14px;
        }
        * { box-sizing: border-box; }
        html { -webkit-text-size-adjust: 100%; }
        body { margin: 0; background: var(--paper); color: var(--ink); font: 300 16px/1.6 'Outfit', system-ui, sans-serif; }
        a { color: var(--gold-ink); }
        .bar { background: var(--bar); color: #fff; position: sticky; top: 0; z-index: 20; }
        .bar-in { max-width: 1080px; margin: 0 auto; padding: 12px 16px; display: flex; align-items: center; gap: 16px; justify-content: space-between; }
        .bar img { height: 36px; width: auto; display: block; }
        .bar-meta { font-size: 14px; color: #cfcab9; display: flex; align-items: center; gap: 16px; }
        .bar-meta strong { color: #fff; font-weight: 600; }
        .bar button { background: none; border: 1px solid #3a372f; color: #e8e3d4; border-radius: var(--r); padding: 6px 12px; font: inherit; font-size: 13px; cursor: pointer; transition: border-color .15s ease, opacity .15s ease; }
        .bar button:hover { border-color: var(--gold); }
        h1 { font-weight: 800; font-size: clamp(32px, 6vw, 52px); line-height: 1.05; letter-spacing: -0.03em; margin: 0 0 16px; }
        h2 { font-weight: 700; font-size: 24px; line-height: 1.2; letter-spacing: -0.02em; margin: 0 0 4px; }
        .lead { font-size: 18px; color: var(--ink-2); max-width: 640px; margin: 0; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; font: 600 16px/1 'Outfit', sans-serif; border-radius: var(--r); padding: 16px 24px; border: 0; cursor: pointer; text-decoration: none; transition: transform .15s ease, opacity .15s ease, background-color .15s ease; }
        .btn:active { transform: translateY(1px); }
        .btn-primary { background: var(--ink); color: #fff; }
        .btn-primary:hover { opacity: .88; }
        .btn-ghost { background: transparent; color: var(--ink); border: 1px dashed var(--line-strong); padding: 12px 16px; font-size: 14px; }
        .btn-ghost:hover { border-color: var(--ink); }
        .field { display: flex; flex-direction: column; gap: 6px; min-width: 0; }
        .field label, .label { font-size: 14px; font-weight: 500; color: var(--ink-2); }
        .field small, .hint { font-size: 13px; color: var(--muted); }
        input[type=text], input[type=email], input[type=tel], input[type=number], input[type=date], input[type=time], input[type=url], select, textarea {
            width: 100%; font: 400 16px/1.4 'Outfit', sans-serif; color: var(--ink); background: #fff; border: 1px solid var(--line-strong);
            border-radius: var(--r); padding: 12px 12px; transition: border-color .15s ease, box-shadow .15s ease;
        }
        textarea { min-height: 96px; resize: vertical; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: var(--ink); box-shadow: 0 0 0 3px rgba(242, 194, 0, .45); }
        .is-invalid { border-color: var(--err) !important; }
        .err { color: var(--err); font-size: 13px; font-weight: 500; }
        .notice { border-radius: var(--r); padding: 16px; font-size: 15px; }
        .notice-err { background: var(--err-soft); color: var(--err); }
        .notice-ok { background: var(--ok-soft); color: var(--ok); }
        .notice-info { background: var(--gold-soft); color: var(--gold-ink); }
        @media (prefers-reduced-motion: no-preference) {
            .rise { animation: rise .45s ease both; }
            @keyframes rise { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
        }
        @yield('styles')
    </style>
</head>
<body>
    <header class="bar">
        <div class="bar-in">
            <img src="{{ $company?->logo_url ?? asset('assets/index_files/logo.png') }}" alt="{{ $company?->name ?? 'GoTrips' }}">
            @yield('bar')
        </div>
    </header>
    @yield('content')
    @stack('scripts')
</body>
</html>
