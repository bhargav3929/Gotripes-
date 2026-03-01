@extends('layouts.admin')

@section('content')
    <!-- Page Heading -->
    <div class="d-flex align-items-center justify-content-between mb-5">
        <div>
            @if(auth()->user()->isAdmin())
                <h1 class="h3 mb-1 fw-800 text-white">System Overview</h1>
                <p class="text-muted small mb-0">Welcome back, Admin. Here's what's happening today.</p>
            @else
                <h1 class="h3 mb-1 fw-800 text-white">Activities Dashboard</h1>
                <p class="text-muted small mb-0">Welcome back, {{ auth()->user()->name }}. Manage your activities below.</p>
            @endif
        </div>
        <div class="d-none d-md-block">
            <span class="badge bg-light-dark text-muted py-2 px-3 border border-secondary border-opacity-10">
                <i class="far fa-calendar-alt me-2"></i>{{ date('D, M d, Y') }}
            </span>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row g-4">

        @if(auth()->user()->isAdmin())
        <!-- Total Users Card (Admin Only) -->
        <div class="col-xl-3 col-md-6">
            <div class="stats-card stats-card-indigo h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stats-icon stats-icon-indigo">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stats-trend text-success">
                            <i class="fas fa-caret-up me-1"></i>12%
                        </div>
                    </div>
                    <h3 class="stats-value text-white mb-1">{{ $stats['total_users'] }}</h3>
                    <p class="stats-label text-muted mb-0">Total Users</p>
                </div>
            </div>
        </div>

        <!-- Total Balance Card (Admin Only) -->
        <div class="col-xl-3 col-md-6">
            <div class="stats-card stats-card-gold h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stats-icon stats-icon-gold">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="stats-trend text-success">
                            <i class="fas fa-caret-up me-1"></i>8.4%
                        </div>
                    </div>
                    <h3 class="stats-value text-white mb-1">AED 42,950</h3>
                    <p class="stats-label text-muted mb-0">Monthly Revenue</p>
                </div>
            </div>
        </div>

        <!-- Pending Tasks Card (Admin Only) -->
        <div class="col-xl-3 col-md-6">
            <div class="stats-card stats-card-blue h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stats-icon stats-icon-blue">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="stats-trend text-warning">
                            <i class="fas fa-circle me-1 small"></i>Active
                        </div>
                    </div>
                    <h3 class="stats-value text-white mb-1">{{ $stats['pending_approvals'] }}</h3>
                    <p class="stats-label text-muted mb-0">Pending Approvals</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Total Activities Card (Visible to All) -->
        <div class="{{ auth()->user()->isAdmin() ? 'col-xl-3 col-md-6' : 'col-xl-4 col-md-6' }}">
            <div class="stats-card stats-card-green h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="stats-icon stats-icon-green">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <div class="stats-trend text-success">
                            <i class="fas fa-check me-1"></i>Live
                        </div>
                    </div>
                    <h3 class="stats-value text-white mb-1">{{ $stats['total_activities'] }}</h3>
                    <p class="stats-label text-muted mb-0">Total Activities</p>
                </div>
            </div>
        </div>

        @if(auth()->user()->isActivitiesManager() && !auth()->user()->isAdmin())
        <!-- Quick link to Activities for manager -->
        <div class="col-xl-4 col-md-6">
            <div class="stats-card h-100" style="background: linear-gradient(135deg, #1a1a1a, #2a2a2a); border: 1px solid rgba(255,215,0,0.3);">
                <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                    <i class="fas fa-plus-circle text-warning mb-3" style="font-size: 2rem;"></i>
                    <h6 class="text-white mb-2 fw-bold">Add New Activity</h6>
                    <p class="text-muted small mb-3">Create a new UAE activity listing</p>
                    <a href="{{ route('admin.uaeactivities.create') }}" class="btn btn-sm" style="background: linear-gradient(45deg, #FFD700, #FFA500); color: #000; font-weight: 700; border-radius: 20px; padding: 8px 20px;">
                        Create Activity
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stats-card h-100" style="background: linear-gradient(135deg, #1a1a1a, #2a2a2a); border: 1px solid rgba(255,215,0,0.15);">
                <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                    <i class="fas fa-list text-warning mb-3" style="font-size: 2rem;"></i>
                    <h6 class="text-white mb-2 fw-bold">View All Activities</h6>
                    <p class="text-muted small mb-3">Browse and manage all activities</p>
                    <a href="{{ route('admin.uaeactivities.index') }}" class="btn btn-sm" style="background: rgba(255,215,0,0.1); color: #FFD700; border: 1px solid rgba(255,215,0,0.3); font-weight: 700; border-radius: 20px; padding: 8px 20px;">
                        View All
                    </a>
                </div>
            </div>
        </div>
        @endif

    </div>

<style>
    .fw-800 { font-weight: 800; }
    
    .stats-card {
        background: var(--light-dark);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        border-color: rgba(255, 255, 255, 0.1);
    }
    
    .stats-value {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.5px;
    }
    
    .stats-label {
        font-size: 0.85rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    
    .stats-icon-indigo { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
    .stats-icon-gold { background: rgba(255, 210, 63, 0.1); color: #FFD23F; }
    .stats-icon-blue { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .stats-icon-green { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
    
    .stats-trend {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.03);
    }
</style>
@endsection