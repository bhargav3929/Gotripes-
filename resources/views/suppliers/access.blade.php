@extends('suppliers._layout')

@section('title', 'Supplier rates')

@section('styles')
    .gate { max-width: 1080px; margin: 0 auto; padding: 96px 16px; display: grid; grid-template-columns: 1.2fr 1fr; gap: 64px; align-items: start; }
    .gate-what { list-style: none; padding: 0; margin: 32px 0 0; display: grid; gap: 16px; }
    .gate-what li { display: grid; grid-template-columns: 32px 1fr; gap: 12px; color: var(--ink-2); }
    .gate-what b { display: inline-flex; width: 32px; height: 32px; border-radius: 50%; background: var(--gold); color: var(--ink); align-items: center; justify-content: center; font-weight: 700; font-size: 14px; }
    .gate-card { background: var(--card); border: 1px solid var(--line); border-radius: var(--r-lg); padding: 32px; }
    .code-input { font: 700 28px/1 ui-monospace, Menlo, Consolas, monospace !important; letter-spacing: .12em; text-transform: uppercase; text-align: center; padding: 16px !important; }
    @media (max-width: 860px) { .gate { grid-template-columns: 1fr; gap: 32px; padding: 48px 16px; } .gate-card { padding: 24px; } }
@endsection

@section('content')
<main class="gate">
    <section class="rise">
        <h1>Send us your rates</h1>
        <p class="lead">For hotels and ground handlers working with {{ $company?->name ?? 'GoTrips' }}. Fill in your package once; when your prices change, come back with the same code and update them.</p>
        <ul class="gate-what">
            <li><b>1</b><span><strong>Your hotels</strong> for each star level, with the price per room per night.</span></li>
            <li><b>2</b><span><strong>Your tour price</strong> per person: transfers, sightseeing, guide and car.</span></li>
            <li><b>3</b><span><strong>Extras, day-by-day plan, peak seasons</strong> and photos.</span></li>
        </ul>
    </section>
    <section class="gate-card rise" style="animation-delay:.08s">
        <h2>Enter your access code</h2>
        <p class="hint" style="margin:0 0 24px;">The {{ $company?->name ?? 'GoTrips' }} team sent it to you with this link.</p>
        <form method="POST" action="{{ route('suppliers.access') }}">
            @csrf
            <div class="field">
                <label for="code" class="sr-only" style="position:absolute;left:-9999px;">Access code</label>
                <input type="text" id="code" name="code" class="code-input @error('code') is-invalid @enderror" value="{{ old('code', request('code')) }}" placeholder="XXXX-XXXX" autocomplete="one-time-code" autocapitalize="characters" spellcheck="false" maxlength="30" required autofocus>
                @error('code')<span class="err" role="alert">{{ $message }}</span>@enderror
            </div>
            <button class="btn btn-primary" style="width:100%; margin-top:16px;">Open the form</button>
        </form>
        <p class="hint" style="margin:24px 0 0;">No code? Ask your contact at {{ $company?->name ?? 'GoTrips' }}{{ $company?->email ? ' (' . $company->email . ')' : '' }}.</p>
    </section>
</main>
@endsection
