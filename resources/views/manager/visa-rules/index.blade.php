@extends('layouts.manager')

@section('title', 'Visa Rules')
@section('page-title', 'Visa Rules')

@section('content')
<div class="wp-page-header">
    <div>
        <h1 class="wp-page-title">Visa Rules</h1>
        <p class="wp-form-help" style="margin:0">Shown to customers in step 1 of the booking wizard, as soon as they pick their passport.</p>
    </div>
</div>

<div class="wp-card">
    <div class="wp-card-header"><i class="fas fa-plus"></i> Add or update a rule</div>
    <div class="wp-card-body">
        <form method="POST" action="{{ route('manager.visa-rules.store') }}">
            @csrf
            @include('manager.visa-rules._fields', ['rule' => null])
            <button class="wp-btn wp-btn-primary"><i class="fas fa-save"></i> Save rule</button>
            <p class="wp-form-help">Saving a destination + passport that already has a rule updates it. Use <strong>Everyone else</strong> for the most common case (e.g. visa-free), then add exceptions per passport.</p>
        </form>
    </div>
</div>

@if($destinations->count() > 1)
<div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:12px;">
    <a href="{{ route('manager.visa-rules.index') }}" class="wp-btn wp-btn-sm {{ request('destination') ? 'wp-btn-secondary' : 'wp-btn-primary' }}">All</a>
    @foreach($destinations as $d)
        <a href="{{ route('manager.visa-rules.index', ['destination' => $d]) }}" class="wp-btn wp-btn-sm {{ request('destination') === $d ? 'wp-btn-primary' : 'wp-btn-secondary' }}">{{ $d }}</a>
    @endforeach
</div>
@endif

<div class="wp-card">
    <div class="table-responsive">
        <table class="wp-table">
            <thead><tr><th>Destination</th><th>Passport</th><th>Requirement</th><th>Stay</th><th>Processing</th><th style="text-align:right">Fee</th><th>Documents</th><th></th></tr></thead>
            <tbody>
            @forelse($rules as $r)
                <tr>
                    <td>{{ $r->destination }}</td>
                    <td>{{ $r->nationality === '*' ? 'Everyone else' : $r->nationality }}</td>
                    <td><span class="wp-badge {{ $r->tone() === 'ok' ? 'wp-badge-green' : 'wp-badge-amber' }}">{{ $r->label() }}</span></td>
                    <td>{{ $r->max_stay_days ? $r->max_stay_days . ' days' : '—' }}</td>
                    <td>{{ $r->processing_days !== null ? $r->processing_days . ' days' : '—' }}</td>
                    <td style="text-align:right">{{ $r->fee !== null ? number_format($r->fee, 2) : '—' }}</td>
                    <td style="max-width:280px">{{ $r->documents }}</td>
                    <td style="white-space:nowrap">
                        <details style="display:inline-block">
                            <summary class="wp-btn wp-btn-secondary wp-btn-sm">Edit</summary>
                            <form method="POST" action="{{ route('manager.visa-rules.update', $r->id) }}" style="margin-top:8px; min-width:320px">
                                @csrf @method('PUT')
                                @include('manager.visa-rules._fields', ['rule' => $r])
                                <button class="wp-btn wp-btn-primary wp-btn-sm">Save</button>
                            </form>
                        </details>
                        <form method="POST" action="{{ route('manager.visa-rules.destroy', $r->id) }}" style="display:inline" onsubmit="return confirm('Remove this visa rule?')">
                            @csrf @method('DELETE')
                            <button class="wp-btn wp-btn-danger wp-btn-sm" aria-label="Remove rule"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center; padding:40px 16px; color:var(--wp-text-muted);">
                    No visa rules yet. Customers see "we will confirm the visa requirement for your passport" until you add one for their destination.
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($rules->hasPages())<div class="wp-card-footer">{{ $rules->links() }}</div>@endif
</div>
@endsection
