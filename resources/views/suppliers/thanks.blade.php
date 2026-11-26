@extends('suppliers._layout')

@section('title', 'Rates received')

@section('styles')
    .done { max-width: 680px; margin: 0 auto; padding: 96px 16px; }
    .tick { width: 64px; height: 64px; border-radius: 50%; background: var(--ok-soft); color: var(--ok); display: flex; align-items: center; justify-content: center; margin-bottom: 32px; }
    .done-actions { display: flex; gap: 16px; flex-wrap: wrap; margin-top: 48px; }
@endsection

@section('bar')
    <div class="bar-meta"><span><strong>{{ $invite->supplier_name }}</strong></span></div>
@endsection

@section('content')
<main class="done rise">
    <div class="tick" aria-hidden="true"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></div>
    <h1>Thank you, we have your rates</h1>
    <p class="lead">The {{ $company?->name ?? 'GoTrips' }} team will check them and contact you if anything is unclear.</p>
    <p class="lead" style="margin-top:16px;">When your prices change, open this page again with your code <strong style="font-family:ui-monospace,Menlo,monospace; font-weight:700;">{{ $invite->display_code }}</strong>. The form opens with what you sent, so you only change the prices.</p>
    <div class="done-actions">
        <a href="{{ route('suppliers.preview') }}" class="btn btn-primary">Preview as a traveller</a>
        <a href="{{ route('suppliers.form') }}" class="btn btn-ghost" style="padding:16px 24px;">Change what I sent</a>
        <form method="POST" action="{{ route('suppliers.leave') }}">@csrf<button class="btn btn-ghost" style="padding:16px 24px;">Close</button></form>
    </div>
</main>
@endsection

@push('scripts')
<script>
try { Object.keys(localStorage).filter(function (k) { return k.indexOf('gt-supplier-draft-{{ $invite->id }}-') === 0; }).forEach(function (k) { localStorage.removeItem(k); }); } catch (e) {}
</script>
@endpush
