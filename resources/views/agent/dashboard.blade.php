@extends('layouts.agent')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="wp-page-header">
    <div>
        <h1 class="wp-page-title">Welcome, {{ $agent->name }}</h1>
        <p style="color: var(--wp-text-muted); margin-top: 4px; font-size: 13px;">
            Agent account for {{ current_company()?->name ?? 'Go Trips' }} —
            access: {{ implode(' · ', $agent->agentServiceLabels()) ?: 'no services granted' }}
        </p>
    </div>
</div>

<div class="row g-3" style="margin-bottom: 8px;">
    @if(!is_null($packageCount))
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="wp-card" style="margin-bottom: 0;">
            <div class="wp-card-body" style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 8px; background: rgba(255,215,0,0.12); display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-suitcase-rolling" style="color: var(--wp-primary); font-size: 18px;"></i>
                </div>
                <div>
                    <div style="font-size: 22px; font-weight: 700; line-height: 1.2;">{{ $packageCount }}</div>
                    <div style="font-size: 12px; color: var(--wp-text-muted);">My Tour Packages</div>
                </div>
                <a href="{{ route('agent.packages.create') }}" class="wp-btn wp-btn-primary wp-btn-sm" style="margin-left: auto;">
                    <i class="fas fa-plus"></i> Add
                </a>
            </div>
        </div>
    </div>
    @endif

    @if(!is_null($activityCount))
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="wp-card" style="margin-bottom: 0;">
            <div class="wp-card-body" style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 8px; background: rgba(255,215,0,0.12); display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-hiking" style="color: var(--wp-primary); font-size: 18px;"></i>
                </div>
                <div>
                    <div style="font-size: 22px; font-weight: 700; line-height: 1.2;">{{ $activityCount }}</div>
                    <div style="font-size: 12px; color: var(--wp-text-muted);">My Activities</div>
                </div>
                <a href="{{ route('agent.activities.create') }}" class="wp-btn wp-btn-primary wp-btn-sm" style="margin-left: auto;">
                    <i class="fas fa-plus"></i> Add
                </a>
            </div>
        </div>
    </div>
    @endif

    @if(!is_null($esimOrderCount))
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="wp-card" style="margin-bottom: 0;">
            <div class="wp-card-body" style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 44px; height: 44px; border-radius: 8px; background: rgba(255,215,0,0.12); display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-sim-card" style="color: var(--wp-primary); font-size: 18px;"></i>
                </div>
                <div>
                    <div style="font-size: 22px; font-weight: 700; line-height: 1.2;">{{ $esimOrderCount }}</div>
                    <div style="font-size: 12px; color: var(--wp-text-muted);">eSIM Orders</div>
                </div>
                <a href="{{ route('agent.esim.index') }}" class="wp-btn wp-btn-secondary wp-btn-sm" style="margin-left: auto;">
                    <i class="fas fa-eye"></i> View
                </a>
            </div>
        </div>
    </div>
    @endif
</div>

@if(is_null($packageCount) && is_null($activityCount) && is_null($esimOrderCount))
<div class="wp-card">
    <div class="wp-card-body" style="text-align: center; padding: 48px 16px;">
        <i class="fas fa-lock" style="font-size: 32px; color: var(--wp-border); display: block; margin-bottom: 12px;"></i>
        <p style="color: var(--wp-text-muted); margin: 0;">
            No services have been granted to your account yet.<br>
            Ask your manager to enable Tour Packages, Activities, or eSIM for you.
        </p>
    </div>
</div>
@endif
@endsection
