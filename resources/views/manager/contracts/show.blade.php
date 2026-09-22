@extends('layouts.manager')

@section('title', 'Contract v' . $contract->version)
@section('page-title', 'Contract')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">{{ $contract->title }} <span class="wp-badge wp-badge-amber">v{{ $contract->version }}</span></h1>
    <a href="{{ route('manager.contracts.index') }}" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Contracts
    </a>
</div>

<style>
    .contract-paper {
        background: var(--wp-white);
        border: 1px solid rgba(255, 215, 0, 0.35);
        border-radius: 10px;
        padding: 40px 48px;
        max-width: 820px;
        white-space: pre-wrap;
        line-height: 1.7;
        font-size: 14px;
    }
    .contract-meta { color: var(--wp-text-muted); font-size: 12px; margin-bottom: 18px; }
</style>

<div class="contract-meta">
    Published {{ $contract->created_at->format('d M Y, H:i') }}
    by {{ optional($contract->author)->name ?? 'Unknown' }}
    · {{ $contract->signatureCount() }} signature(s)
</div>

<div class="contract-paper">{{ $contract->body }}</div>
@endsection
