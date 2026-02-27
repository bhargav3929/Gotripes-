@extends('layouts.admin')

@section('title', 'Manage UAE Activities')

@section('page-title', 'Manage UAE Activities')

@section('content')

@php
    $totalFilteredCount = $activities->total();
@endphp

<style>
    :root {
        --bg-primary: #0e0e0e;
        --bg-card: #161616;
        --bg-row: #1a1a1a;
        --bg-row-hover: #1f1f1f;
        --gold: #d4a845;
        --gold-light: #e8c76a;
        --gold-dim: rgba(212, 168, 69, 0.12);
        --gold-border: rgba(212, 168, 69, 0.18);
        --text-primary: #f0f0f0;
        --text-secondary: #8a8a8a;
        --text-dim: #5a5a5a;
        --red: #d94452;
        --red-hover: #e55565;
        --green: #34c759;
    }

    body { background: var(--bg-primary) !important; color: var(--text-primary) !important; }

    /* ── Page Container ── */
    .page-wrap {
        max-width: 1320px;
        margin: 0 auto;
        padding: 24px 16px;
    }

    /* ── Top Bar ── */
    .top-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
    }

    .top-bar h2 {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        letter-spacing: -0.3px;
    }

    .top-bar h2 span { color: var(--gold); }

    .btn-add {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--gold);
        color: #000;
        border: none;
        padding: 9px 18px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-add:hover {
        background: var(--gold-light);
        color: #000;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(212, 168, 69, 0.25);
    }

    /* ── Stats Strip ── */
    .stats-strip {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
    }

    .stat-chip {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--bg-card);
        border: 1px solid var(--gold-border);
        border-radius: 8px;
        padding: 10px 16px;
        flex: 1;
    }

    .stat-chip .stat-val {
        font-size: 20px;
        font-weight: 800;
        color: var(--gold);
        line-height: 1;
    }

    .stat-chip .stat-lbl {
        font-size: 11px;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        line-height: 1.2;
    }

    /* ── Alert ── */
    .alert-slim {
        background: rgba(52, 199, 89, 0.08);
        border: 1px solid rgba(52, 199, 89, 0.2);
        border-radius: 8px;
        padding: 12px 16px;
        color: var(--green);
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .alert-slim .btn-close {
        filter: invert(1);
        opacity: 0.4;
        margin-left: auto;
    }

    /* ── Partner Info ── */
    .partner-info {
        background: rgba(23, 162, 184, 0.06);
        border: 1px solid rgba(23, 162, 184, 0.15);
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #5bc0de;
    }

    /* ── Table ── */
    .activity-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 4px;
    }

    .activity-table thead th {
        background: transparent;
        color: var(--text-dim);
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 8px 14px 12px;
        border: none;
        white-space: nowrap;
    }

    .activity-table tbody tr {
        background: var(--bg-row);
        transition: all 0.15s ease;
    }

    .activity-table tbody tr:hover {
        background: var(--bg-row-hover);
    }

    .activity-table tbody td {
        padding: 12px 14px;
        border: none;
        vertical-align: middle;
        font-size: 13px;
    }

    .activity-table tbody tr td:first-child { border-radius: 10px 0 0 10px; }
    .activity-table tbody tr td:last-child { border-radius: 0 10px 10px 0; }

    /* ── Row Number ── */
    .row-num {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--gold-dim);
        color: var(--gold);
        border-radius: 6px;
        font-size: 12px;
        font-weight: 700;
    }

    /* ── Activity Info ── */
    .act-name {
        font-weight: 600;
        color: var(--text-primary);
        font-size: 14px;
        line-height: 1.3;
        margin-bottom: 2px;
    }

    .act-price {
        font-size: 12px;
        color: var(--gold);
        font-weight: 600;
    }

    .act-location, .act-emirate, .act-creator {
        font-size: 13px;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .act-location i, .act-emirate i, .act-creator i {
        color: var(--text-dim);
        font-size: 11px;
        width: 14px;
        text-align: center;
    }

    /* ── Image Preview ── */
    .img-thumb {
        width: 64px;
        height: 44px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid var(--gold-border);
        transition: transform 0.2s;
    }

    .img-thumb:hover {
        transform: scale(1.15);
    }

    .img-empty {
        width: 64px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--gold-dim);
        border-radius: 6px;
        color: var(--text-dim);
        font-size: 16px;
    }

    /* ── Action Buttons ── */
    .act-actions {
        display: flex;
        gap: 6px;
        white-space: nowrap;
    }

    .btn-act {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
    }

    .btn-act.edit {
        background: var(--gold-dim);
        color: var(--gold);
        border: 1px solid var(--gold-border);
    }

    .btn-act.edit:hover {
        background: var(--gold);
        color: #000;
        text-decoration: none;
    }

    .btn-act.del {
        background: rgba(217, 68, 82, 0.08);
        color: var(--red);
        border: 1px solid rgba(217, 68, 82, 0.18);
    }

    .btn-act.del:hover {
        background: var(--red);
        color: #fff;
    }

    /* ── Pagination ── */
    .pag-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 24px;
        flex-wrap: wrap;
    }

    .pag-info {
        font-size: 12px;
        color: var(--text-secondary);
        margin-right: 12px;
    }

    .pag-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.15s;
        background: var(--bg-card);
        color: var(--text-secondary);
        border: 1px solid rgba(255,255,255,0.06);
    }

    .pag-btn:hover {
        background: var(--gold-dim);
        color: var(--gold);
        text-decoration: none;
        border-color: var(--gold-border);
    }

    .pag-btn.active {
        background: var(--gold);
        color: #000;
        border-color: var(--gold);
    }

    .pag-btn.disabled {
        opacity: 0.3;
        pointer-events: none;
    }

    .pag-goto {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-left: 14px;
        font-size: 12px;
        color: var(--text-secondary);
    }

    .pag-goto input {
        width: 46px;
        height: 34px;
        text-align: center;
        border-radius: 6px;
        border: 1px solid rgba(255,255,255,0.1);
        background: var(--bg-card);
        color: var(--text-primary);
        font-size: 13px;
        font-weight: 600;
        outline: none;
        transition: border-color 0.15s;
        -moz-appearance: textfield;
    }

    .pag-goto input::-webkit-outer-spin-button,
    .pag-goto input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

    .pag-goto input:focus { border-color: var(--gold); }

    .pag-goto button {
        height: 34px;
        padding: 0 12px;
        border-radius: 6px;
        border: 1px solid var(--gold-border);
        background: var(--gold-dim);
        color: var(--gold);
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s;
    }

    .pag-goto button:hover { background: var(--gold); color: #000; }

    /* ── Mobile Cards ── */
    .m-card {
        background: var(--bg-card);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 10px;
        transition: border-color 0.2s;
    }

    .m-card:hover { border-color: var(--gold-border); }

    .m-card .m-name {
        font-weight: 600;
        font-size: 14px;
        color: var(--text-primary);
        margin-bottom: 6px;
    }

    .m-card .m-meta {
        font-size: 12px;
        color: var(--text-secondary);
        margin-bottom: 3px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .m-card .m-meta i { color: var(--text-dim); font-size: 11px; width: 14px; }

    .m-card .m-img {
        width: 100%;
        height: 140px;
        object-fit: cover;
        border-radius: 8px;
        margin: 12px 0;
    }

    .m-card .m-actions {
        display: flex;
        gap: 8px;
    }

    .m-card .m-actions .btn-act { flex: 1; justify-content: center; padding: 8px 0; }

    /* ── Empty State ── */
    .empty-box {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-dim);
    }

    .empty-box i { font-size: 40px; margin-bottom: 16px; color: var(--gold-dim); }
    .empty-box h4 { color: var(--text-secondary); font-size: 16px; margin-bottom: 8px; }
    .empty-box p { font-size: 13px; margin-bottom: 20px; }

    /* ── Delete Modal ── */
    .del-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(0,0,0,0.7);
        backdrop-filter: blur(4px);
        align-items: center;
        justify-content: center;
    }

    .del-modal.show { display: flex !important; }

    .del-modal-box {
        background: var(--bg-card);
        border: 1px solid var(--gold-border);
        border-radius: 16px;
        width: 90%;
        max-width: 420px;
        overflow: hidden;
        animation: modalSlide 0.25s ease;
    }

    @keyframes modalSlide {
        from { opacity: 0; transform: translateY(20px) scale(0.97); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .del-modal-head {
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(255,255,255,0.06);
    }

    .del-modal-head h4 {
        font-size: 16px;
        font-weight: 700;
        color: var(--red);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .del-modal-close {
        background: none;
        border: none;
        color: var(--text-dim);
        font-size: 18px;
        cursor: pointer;
        padding: 4px;
        transition: color 0.15s;
    }

    .del-modal-close:hover { color: var(--text-primary); }

    .del-modal-body {
        padding: 24px;
        text-align: center;
    }

    .del-modal-body .del-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: rgba(217, 68, 82, 0.1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        color: var(--red);
        font-size: 22px;
    }

    .del-modal-body p { font-size: 14px; color: var(--text-secondary); margin: 0 0 12px; }

    .del-activity-name {
        background: var(--gold-dim);
        border: 1px solid var(--gold-border);
        border-radius: 8px;
        padding: 10px 14px;
        color: var(--gold);
        font-weight: 600;
        font-size: 14px;
        margin: 12px 0 4px;
    }

    .del-sub { font-size: 12px !important; color: var(--text-dim) !important; }

    .del-modal-foot {
        display: flex;
        gap: 10px;
        padding: 0 24px 24px;
    }

    .del-modal-foot button {
        flex: 1;
        padding: 10px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.15s;
    }

    .btn-cancel-modal {
        background: rgba(255,255,255,0.06);
        color: var(--text-secondary);
    }

    .btn-cancel-modal:hover { background: rgba(255,255,255,0.1); color: var(--text-primary); }

    .btn-del-confirm {
        background: var(--red);
        color: #fff;
    }

    .btn-del-confirm:hover { background: var(--red-hover); }

    .btn-del-confirm.processing {
        background: var(--text-dim);
        cursor: not-allowed;
        opacity: 0.6;
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .page-wrap { padding: 16px 10px; }
        .top-bar { flex-wrap: wrap; gap: 10px; }
        .top-bar h2 { font-size: 17px; }
        .stats-strip { flex-direction: column; gap: 8px; }
        .stat-chip { padding: 8px 12px; }
        .stat-chip .stat-val { font-size: 17px; }
        .pag-bar { gap: 4px; }
        .del-modal-foot { flex-direction: column; }
    }

    @media (max-width: 576px) {
        .btn-add { font-size: 12px; padding: 8px 14px; }
        .m-card .m-img { height: 110px; }
    }
</style>

<div class="page-wrap">

    {{-- Top Bar --}}
    <div class="top-bar">
        <h2>
            <span>UAE</span> Activities
            @if($isApprovedPartner)
                <small style="font-size: 12px; color: var(--text-dim); font-weight: 400;">(Your Activities)</small>
            @endif
        </h2>
        <a href="{{ route('admin.uaeactivities.create') }}" class="btn-add">
            <i class="fas fa-plus"></i>
            <span class="d-none d-sm-inline">Add New Activity</span>
            <span class="d-sm-none">Add</span>
        </a>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="alert-slim" id="successAlert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        </div>
    @endif

    {{-- Partner Filter --}}
    @if($isApprovedPartner)
    <div class="partner-info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Partner View:</strong> Showing only your activities. Logged in as <strong>{{ strtolower($user->name) }}</strong>
    </div>
    @endif

    {{-- Stats Strip --}}
    @if($totalFilteredCount > 0)
    <div class="stats-strip">
        <div class="stat-chip">
            <div class="stat-val">{{ $activities->total() }}</div>
            <div class="stat-lbl">{{ $isApprovedPartner ? 'Your' : 'Total' }}<br>Activities</div>
        </div>
        <div class="stat-chip">
            <div class="stat-val">{{ $activities->currentPage() }}</div>
            <div class="stat-lbl">Current<br>Page</div>
        </div>
        <div class="stat-chip">
            <div class="stat-val">{{ $activities->lastPage() }}</div>
            <div class="stat-lbl">Total<br>Pages</div>
        </div>
    </div>
    @endif

    {{-- ═══════ Mobile Cards ═══════ --}}
    <div class="d-block d-lg-none">
        @forelse($activities as $index => $activity)
            <div class="m-card">
                <div class="m-name">{{ $activity->activityName ?? 'Untitled Activity' }}</div>
                <div class="m-meta">
                    <i class="fas fa-location-dot"></i>
                    {{ $activity->activityLocation ?? 'Location not specified' }}
                </div>
                @if($activity->emirate)
                <div class="m-meta">
                    <i class="fas fa-flag"></i>
                    {{ $activity->emirate->emiratesName }}
                </div>
                @endif
                <div class="m-meta">
                    <i class="fas fa-user"></i>
                    {{ $activity->createdBy ?? 'Unknown' }}
                    @if(strtolower($activity->createdBy ?? '') === strtolower($user->name))
                        <i class="fas fa-check-circle" style="color: var(--green); font-size: 10px;"></i>
                    @endif
                </div>
                @if($activity->activityPrice)
                <div class="m-meta" style="color: var(--gold);">
                    <i class="fas fa-dollar-sign"></i>
                    {{ number_format($activity->activityPrice, 2) }} AED
                </div>
                @endif

                @php
                    $firstImage = null;
                    // Always use main table image (stays in sync with frontend)
                    if ($activity->activityImage) {
                        $firstImage = trim($activity->activityImage);
                    } elseif ($activity->details && $activity->details->activityImage) {
                        $images = explode('#cseparator', $activity->details->activityImage);
                        $firstImage = trim($images[0] ?? null);
                    }
                @endphp

                @if($firstImage)
                    <img src="{{ asset($firstImage) }}" alt="{{ $activity->activityName }}" class="m-img"
                         onerror="this.style.display='none'">
                @endif

                <div class="m-actions">
                    <a href="{{ route('admin.uaeactivities.edit', $activity) }}" class="btn-act edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button type="button" class="btn-act del delete-btn"
                            data-activity-name="{{ $activity->activityName ?? 'this activity' }}"
                            data-activity-id="{{ $activity->id }}">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.uaeactivities.destroy', $activity) }}" class="d-none delete-form">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        @empty
            <div class="empty-box">
                <i class="fas fa-map-marked-alt"></i>
                <h4>{{ $isApprovedPartner ? 'No Activities Found' : 'No UAE Activities Found' }}</h4>
                <p>Start by adding your first UAE activity.</p>
                <a href="{{ route('admin.uaeactivities.create') }}" class="btn-add">
                    <i class="fas fa-plus"></i> Create Activity
                </a>
            </div>
        @endforelse
    </div>

    {{-- ═══════ Desktop Table ═══════ --}}
    @if($totalFilteredCount > 0)
    <div class="d-none d-lg-block">
        <div class="table-responsive">
            <table class="activity-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Activity</th>
                        <th>Location</th>
                        <th>Emirates</th>
                        <th>Created By</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($activities as $index => $activity)
                        <tr>
                            <td>
                                <span class="row-num">{{ $activities->firstItem() + $index }}</span>
                            </td>
                            <td>
                                <div class="act-name">{{ $activity->activityName ?? 'Untitled' }}</div>
                                @if($activity->activityPrice)
                                    <div class="act-price">{{ number_format($activity->activityPrice, 2) }} AED</div>
                                @endif
                            </td>
                            <td>
                                <div class="act-location">
                                    <i class="fas fa-location-dot"></i>
                                    {{ $activity->activityLocation ?? '—' }}
                                </div>
                            </td>
                            <td>
                                <div class="act-emirate">
                                    <i class="fas fa-flag"></i>
                                    {{ $activity->emirate->emiratesName ?? '—' }}
                                </div>
                            </td>
                            <td>
                                <div class="act-creator">
                                    <i class="fas fa-user"></i>
                                    {{ $activity->createdBy ?? 'Unknown' }}
                                    @if(strtolower($activity->createdBy ?? '') === strtolower($user->name))
                                        <i class="fas fa-check-circle" style="color: var(--green); font-size: 10px;"></i>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @php
                                    $firstImage = null;
                                    // Always use main table image (stays in sync with frontend)
                                    if ($activity->activityImage) {
                                        $firstImage = trim($activity->activityImage);
                                    } elseif ($activity->details && $activity->details->activityImage) {
                                        $images = explode('#cseparator', $activity->details->activityImage);
                                        $firstImage = trim($images[0] ?? null);
                                    }
                                @endphp
                                @if($firstImage)
                                    <img src="{{ asset($firstImage) }}" alt="" class="img-thumb"
                                         onerror="this.outerHTML='<div class=\'img-empty\'><i class=\'fas fa-image\'></i></div>'">
                                @else
                                    <div class="img-empty"><i class="fas fa-image"></i></div>
                                @endif
                            </td>
                            <td>
                                <div class="act-actions">
                                    <a href="{{ route('admin.uaeactivities.edit', $activity) }}" class="btn-act edit" title="Edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.uaeactivities.destroy', $activity) }}" class="d-inline delete-form"
                                          data-activity-name="{{ $activity->activityName ?? 'this activity' }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn-act del delete-btn"
                                                data-activity-name="{{ $activity->activityName ?? 'this activity' }}"
                                                data-activity-id="{{ $activity->id }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ═══════ Pagination ═══════ --}}
    @if($activities->hasPages())
    <div class="pag-bar">
        <div class="pag-info">
            {{ $activities->firstItem() }}–{{ $activities->lastItem() }} of {{ $activities->total() }}
        </div>

        {{-- Prev --}}
        <a href="{{ $activities->currentPage() > 1 ? $activities->url($activities->currentPage() - 1) : '#' }}"
           class="pag-btn {{ $activities->currentPage() == 1 ? 'disabled' : '' }}">
            <i class="fas fa-chevron-left" style="font-size:10px;"></i>
        </a>

        {{-- All page numbers --}}
        @for($i = 1; $i <= $activities->lastPage(); $i++)
            <a href="{{ $activities->url($i) }}" class="pag-btn {{ $i == $activities->currentPage() ? 'active' : '' }}">{{ $i }}</a>
        @endfor

        {{-- Next --}}
        <a href="{{ $activities->currentPage() < $activities->lastPage() ? $activities->url($activities->currentPage() + 1) : '#' }}"
           class="pag-btn {{ $activities->currentPage() == $activities->lastPage() ? 'disabled' : '' }}">
            <i class="fas fa-chevron-right" style="font-size:10px;"></i>
        </a>

        {{-- Go to page --}}
        <div class="pag-goto">
            <span>Go to</span>
            <input type="number" id="gotoPage" min="1" max="{{ $activities->lastPage() }}" placeholder="{{ $activities->currentPage() }}">
            <button onclick="goToPage()">Go</button>
        </div>
    </div>

    <script>
    function goToPage() {
        const input = document.getElementById('gotoPage');
        const page = parseInt(input.value);
        const max = {{ $activities->lastPage() }};
        if (page >= 1 && page <= max) {
            window.location.href = '{{ $activities->url(1) }}'.replace(/page=\d+/, 'page=' + page);
        } else {
            input.style.borderColor = 'var(--red)';
            setTimeout(() => input.style.borderColor = '', 1000);
        }
    }
    document.getElementById('gotoPage')?.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') goToPage();
    });
    </script>
    @endif

    {{-- Empty State Desktop --}}
    @if($totalFilteredCount == 0)
    <div class="d-none d-lg-block">
        <div class="empty-box">
            <i class="fas fa-map-marked-alt"></i>
            <h4>{{ $isApprovedPartner ? 'No Activities Found' : 'No UAE Activities Found' }}</h4>
            <p>
                @if($isApprovedPartner)
                    You haven't created any activities yet. Logged in as: <strong>{{ strtolower($user->name) }}</strong>
                @else
                    Start by adding your first UAE activity.
                @endif
            </p>
            <a href="{{ route('admin.uaeactivities.create') }}" class="btn-add">
                <i class="fas fa-plus"></i> Create Activity
            </a>
        </div>
    </div>
    @endif

</div>

{{-- Delete Modal --}}
<div id="deleteModal" class="del-modal">
    <div class="del-modal-box">
        <div class="del-modal-head">
            <h4><i class="fas fa-exclamation-triangle"></i> Delete Activity</h4>
            <button type="button" class="del-modal-close" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="del-modal-body">
            <div class="del-icon"><i class="fas fa-trash-alt"></i></div>
            <p>Are you sure you want to delete this?</p>
            <div class="del-activity-name" id="activityToDelete"></div>
            <p class="del-sub">This action cannot be undone.</p>
        </div>
        <div class="del-modal-foot">
            <button type="button" class="btn-cancel-modal" onclick="closeDeleteModal()">
                Cancel
            </button>
            <button type="button" class="btn-del-confirm" id="confirmDeleteBtn">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let deleteForm = null;

    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            e.preventDefault();
            const btn = e.target.closest('.delete-btn');
            const name = btn.getAttribute('data-activity-name') || 'this activity';
            const form = btn.closest('.m-card')?.querySelector('.delete-form') || btn.closest('form.delete-form');
            if (form) showDeleteModal(name, form);
        }
    });

    function showDeleteModal(name, form) {
        deleteForm = form;
        document.getElementById('activityToDelete').textContent = name;
        document.getElementById('deleteModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    window.closeDeleteModal = function() {
        document.getElementById('deleteModal').classList.remove('show');
        document.body.style.overflow = 'auto';
        deleteForm = null;
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (deleteForm) {
            this.classList.add('processing');
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            this.disabled = true;
            deleteForm.submit();
        }
    });

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('deleteModal').classList.contains('show')) {
            closeDeleteModal();
        }
    });

    // Auto-hide alert
    const alert = document.getElementById('successAlert');
    if (alert) setTimeout(() => { alert.style.opacity = '0'; alert.style.transition = 'opacity 0.3s'; setTimeout(() => alert.remove(), 300); }, 4000);
});
</script>

@endsection
