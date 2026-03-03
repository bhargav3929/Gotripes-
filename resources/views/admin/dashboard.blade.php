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
            <div class="stats-card action-card action-card-primary h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                    <i class="fas fa-plus-circle action-card-icon mb-3"></i>
                    <h6 class="text-white mb-2 fw-bold">Add New Activity</h6>
                    <p class="text-muted small mb-3">Create a new UAE activity listing</p>
                    <a href="{{ route('admin.uaeactivities.create') }}" class="btn btn-sm btn-action-gold">
                        Create Activity
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stats-card action-card action-card-secondary h-100">
                <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                    <i class="fas fa-list action-card-icon mb-3"></i>
                    <h6 class="text-white mb-2 fw-bold">View All Activities</h6>
                    <p class="text-muted small mb-3">Browse and manage all activities</p>
                    <a href="{{ route('admin.uaeactivities.index') }}" class="btn btn-sm btn-action-outline">
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
        border-radius: var(--radius-lg);
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.25);
        border-color: rgba(255, 255, 255, 0.08);
    }

    .stats-value {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.03em;
    }

    .stats-label {
        font-size: 0.8125rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stats-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .stats-icon-indigo { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
    .stats-icon-gold { background: rgba(255, 215, 0, 0.1); color: var(--primary-gold); }
    .stats-icon-blue { background: rgba(59, 130, 246, 0.1); color: var(--info); }
    .stats-icon-green { background: rgba(34, 197, 94, 0.1); color: var(--success); }

    .stats-trend {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.03);
    }

    /* Action Cards (Partner quick links) */
    .action-card { background: var(--card-bg); }
    .action-card-primary { border-color: var(--border-gold); }
    .action-card-secondary { border-color: var(--border-color); }
    .action-card-icon { font-size: 2rem; color: var(--primary-gold); }

    .btn-action-gold {
        background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
        color: #000;
        font-weight: 700;
        border-radius: var(--radius-sm);
        padding: 0.5rem 1.25rem;
        border: none;
    }
    .btn-action-gold:hover {
        color: #000;
        box-shadow: 0 4px 12px rgba(255, 215, 0, 0.25);
        transform: translateY(-1px);
    }

    .btn-action-outline {
        background: rgba(255, 215, 0, 0.06);
        color: var(--primary-gold);
        border: 1px solid var(--border-gold);
        font-weight: 700;
        border-radius: var(--radius-sm);
        padding: 0.5rem 1.25rem;
    }
    .btn-action-outline:hover {
        background: rgba(255, 215, 0, 0.12);
        color: var(--primary-gold);
        border-color: var(--primary-gold);
    }
</style>
@endsection