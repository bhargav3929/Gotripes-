@extends('layouts.manager')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Welcome -->
<div class="wp-page-header">
    <h1 class="wp-page-title">Welcome, {{ session('manager_name', 'Manager') }}</h1>
</div>

<!-- At a Glance -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="wp-card">
            <div class="wp-card-header">
                <i class="fas fa-photo-video text-secondary-wp"></i>
                Hero Ad Slots
            </div>
            <div class="wp-card-body">
                <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 12px;">
                    <span style="font-size: 36px; font-weight: 700; color: var(--wp-primary);">{{ $adCount }}</span>
                    <span class="text-muted-wp">of 6 slots used</span>
                </div>
                <div style="height: 4px; background: var(--wp-border-light); border-radius: 2px; margin-bottom: 16px;">
                    <div style="height: 100%; width: {{ ($adCount/6)*100 }}%; background: var(--wp-primary); border-radius: 2px; transition: width 0.3s;"></div>
                </div>
                <a href="{{ route('manager.adslots.index') }}" class="wp-btn wp-btn-primary">
                    <i class="fas fa-cog"></i> Manage Slots
                </a>
                @if($adCount < 6)
                    <a href="{{ route('manager.adslots.create') }}" class="wp-btn wp-btn-secondary">
                        <i class="fas fa-plus"></i> Add New
                    </a>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="wp-card">
            <div class="wp-card-header">
                <i class="fas fa-rss text-secondary-wp"></i>
                News Ticker
            </div>
            <div class="wp-card-body">
                <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 12px;">
                    <span style="font-size: 36px; font-weight: 700; color: var(--wp-primary);">{{ $announcementCount }}</span>
                    <span class="text-muted-wp">active announcements</span>
                </div>
                <div style="display: flex; gap: 6px; margin-bottom: 16px; flex-wrap: wrap;">
                    <span class="wp-badge wp-badge-red">Breaking</span>
                    <span class="wp-badge wp-badge-amber">Trending</span>
                    <span class="wp-badge wp-badge-green">Exclusive</span>
                    <span class="wp-badge wp-badge-blue">New</span>
                </div>
                <a href="{{ route('manager.announcements.index') }}" class="wp-btn wp-btn-primary">
                    <i class="fas fa-cog"></i> Manage Ticker
                </a>
                <a href="{{ route('manager.announcements.create') }}" class="wp-btn wp-btn-secondary">
                    <i class="fas fa-plus"></i> Add New
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="wp-card">
            <div class="wp-card-header">
                <i class="fas fa-hiking text-secondary-wp"></i>
                UAE Activities
            </div>
            <div class="wp-card-body">
                <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 12px;">
                    <span style="font-size: 36px; font-weight: 700; color: var(--wp-primary);">{{ $activityCount }}</span>
                    <span class="text-muted-wp">active activities</span>
                </div>
                <div style="display: flex; gap: 6px; margin-bottom: 16px; flex-wrap: wrap;">
                    <span class="wp-badge wp-badge-amber">Dubai</span>
                    <span class="wp-badge wp-badge-green">Abu Dhabi</span>
                    <span class="wp-badge wp-badge-blue">+more</span>
                </div>
                <a href="{{ route('manager.activities.index') }}" class="wp-btn wp-btn-primary">
                    <i class="fas fa-cog"></i> Manage
                </a>
                <a href="{{ route('manager.activities.create') }}" class="wp-btn wp-btn-secondary">
                    <i class="fas fa-plus"></i> Add New
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Info -->
<div class="wp-card">
    <div class="wp-card-header">
        <i class="fas fa-info-circle text-secondary-wp"></i>
        Quick Info
    </div>
    <div class="wp-card-body" style="font-size: 13px; color: var(--wp-text-secondary);">
        <p style="margin-bottom: 8px;"><strong>Hero Ad Slots</strong> appear in the homepage carousel. You can upload images or videos (MP4). Maximum 6 slots, 4 visible at a time.</p>
        <p style="margin-bottom: 0;"><strong>News Ticker</strong> items scroll across the header. Each item can be tagged with a color-coded label: Breaking (red), Trending (gold), Exclusive (green), or New (blue).</p>
    </div>
</div>
@endsection
