@extends('layouts.admin')

@section('title', 'Manage Users')

@section('page-title', 'Manage Users')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-lg border-0 animate-fade-in">
                <div class="card-header">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                        <h3 class="card-title">
                            <i class="fas fa-users-cog me-2"></i>Manage Users
                        </h3>
                        <div class="card-tools">
                            @can('user_create')
                            <a href="{{ route('admin.users.create') }}" 
                               class="btn btn-primary btn-mobile animate-scale">
                                <i class="fas fa-plus me-1"></i> 
                                <span class="d-none d-sm-inline">New User</span>
                                <span class="d-sm-none">Add New</span>
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
                <div class="card-body p-2 p-sm-3 p-md-4">
                    @if(session('success'))
                        <div class="alert alert-success-custom alert-dismissible fade show animate-fade-in mb-3 mb-md-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- DESKTOP TABLE VIEW WITH FILE DOWNLOADS AND APPROVE/REJECT BUTTONS -->
                    <div class="d-none d-lg-block">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover datatable datatable-User">
                                <thead>
                                    <tr>
                                        <th style="width: 80px; text-align: center;">
                                            <i class="fas fa-hashtag me-1"></i>Sl. No
                                        </th>
                                        <th style="width: 180px;">
                                            <i class="fas fa-user me-1"></i>Name
                                        </th>
                                        <th style="width: 160px;">
                                            <i class="fas fa-envelope me-1"></i>Email
                                        </th>
                                        <th style="width: 200px;">
                                            <i class="fas fa-map-marker-alt me-1"></i>Selected Emirates
                                        </th>
                                        <th class="text-center" style="width: 140px;">
                                            <i class="fas fa-info-circle me-1"></i>Status
                                        </th>
                                        <th class="text-center" style="width: 160px;">
                                            <i class="fas fa-file-download me-1"></i>Documents
                                        </th>
                                        <th class="text-center" style="width: 220px;">
                                            <i class="fas fa-cogs me-1"></i>Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $index => $user)
                                    @php
                                        $isPartner = $user->email_verified_at && str_contains($user->email_verified_at, 'rseparator');
                                        $parts = $isPartner ? explode('rseparator', $user->email_verified_at) : [];
                                        $status = isset($parts[1]) ? $parts[1] : '0';
                                        $emirates = isset($parts[0]) && !empty($parts[0]) ? explode(',', $parts[0]) : [];
                                        $adminComments = isset($parts[2]) ? trim($parts[2]) : '';
                                        $emiratesNames = [];
                                        if (!empty($emirates) && $emirates[0] !== '') {
                                            $emiratesNames = DB::table('tbl_emirates')
                                                ->whereIn('emiratesID', $emirates)
                                                ->where('isActive', 1)
                                                ->pluck('emiratesName')
                                                ->toArray();
                                        }
                                        $filePaths = json_decode($user->partner_document_path, true) ?? [];
                                    @endphp
                                    
                                    <tr data-entry-id="{{ $user->id }}">
                                        <td class="text-center opacity-50">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="user-info">
                                                <strong class="text-white fs-6">{{ $user->name }}</strong>
                                                @if($user->phone)
                                                    <div class="mt-1">
                                                        <small class="text-light-muted">
                                                            <i class="fas fa-phone me-1"></i>{{ $user->phone }}
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-white fs-6">{{ $user->email }}</td>
                                        <td>
                                            @if(!empty($emiratesNames))
                                                <div class="emirates-list">
                                                    @foreach($emiratesNames as $emirateName)
                                                        <span class="badge bg-info-custom emirates-badge mb-1 me-1">
                                                            <i class="fas fa-map-pin me-1"></i>{{ $emirateName }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                                <div class="mt-1">
                                                    <small class="text-light-muted">
                                                        {{ count($emiratesNames) }} Emirates Selected
                                                    </small>
                                                </div>
                                            @else
                                                <span class="text-muted">
                                                    <i class="fas fa-minus me-1"></i>No Emirates
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($isPartner)
                                                @if($status == '0')
                                                    <span class="badge bg-warning text-dark status-badge">
                                                        <i class="fas fa-clock me-1"></i>Pending
                                                    </span>
                                                    <div class="mt-1"><small class="text-light-muted">Partner</small></div>
                                                @elseif($status == '1')
                                                    <span class="badge bg-success status-badge">
                                                        <i class="fas fa-check me-1"></i>Approved
                                                    </span>
                                                    <div class="mt-1"><small class="text-light-muted">Partner</small></div>
                                                @elseif($status == '2')
                                                    <span class="badge bg-danger status-badge">
                                                        <i class="fas fa-times me-1"></i>Rejected
                                                    </span>
                                                    <div class="mt-1"><small class="text-light-muted">Partner</small></div>
                                                @endif
                                                @if(!empty($adminComments))
                                                    <div class="mt-1">
                                                        <small class="text-info" data-bs-toggle="tooltip" title="{{ $adminComments }}">
                                                            <i class="fas fa-comment me-1"></i>Has Notes
                                                        </small>
                                                    </div>
                                                @endif
                                            @else
                                                @if($user->email_verified_at)
                                                    <span class="badge bg-success-custom status-badge">
                                                        <i class="fas fa-check me-1"></i>Verified
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary status-badge">
                                                        <i class="fas fa-user me-1"></i>Regular
                                                    </span>
                                                @endif
                                                <div class="mt-1"><small class="text-light-muted">User</small></div>
                                            @endif
                                        </td>
                                        <!-- FILES COLUMN WITH DOWNLOAD BUTTONS -->
                                        <td class="text-center">
                                            @if(count($filePaths))
                                                @foreach($filePaths as $i => $filePath)
                                                    <a href="{{ asset($filePath) }}" download
                                                       class="btn btn-outline-primary btn-sm mb-1"
                                                       title="Download Document {{ $i + 1 }}">
                                                        <i class="fas fa-download"></i>
                                                        {{ __('File') }} {{ $i + 1 }}
                                                    </a><br>
                                                @endforeach
                                            @else
                                                <span class="text-muted">No files</span>
                                            @endif
                                        </td>
                                        <!-- ACTION COLUMN with Approve/Reject logic -->
                                        <td class="text-center">
                                            @if($isPartner && $status == '0')
                                                <!-- Approval/Rejection buttons for pending partners -->
                                                <div class="btn-group btn-group-sm mb-2 d-block" role="group">
                                                    <button type="button" 
                                                            class="btn btn-success btn-sm animate-scale approve-btn me-1 equal-btn"
                                                            data-user-id="{{ $user->id }}"
                                                            data-user-name="{{ $user->name }}"
                                                            title="Approve Partner">
                                                        <i class="fas fa-check me-1"></i>Approve
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-danger btn-sm animate-scale reject-btn equal-btn"
                                                            data-user-id="{{ $user->id }}"
                                                            data-user-name="{{ $user->name }}"
                                                            title="Reject Partner">
                                                        <i class="fas fa-times me-1"></i>Reject
                                                    </button>
                                                </div>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('admin.users.edit', $user->id) }}" 
                                                       class="btn btn-warning-custom btn-sm animate-scale"
                                                       title="Edit User">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            @else
                                                <!-- Regular edit/delete buttons -->
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('admin.users.edit', $user->id) }}" 
                                                       class="btn btn-warning-custom btn-sm animate-scale"
                                                       title="Edit User">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-danger-custom btn-sm animate-scale delete-btn"
                                                            data-user-id="{{ $user->id }}"
                                                            data-user-name="{{ $user->name }}"
                                                            title="Delete User">
                                                        <i class="fas fa-trash me-1"></i>Delete
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="empty-state-desktop">
                                                <i class="fas fa-users text-gold mb-3" style="font-size: 4rem; opacity: 0.5;"></i>
                                                <h4 class="text-white mb-2">No Users Found</h4>
                                                <p class="text-light-muted mb-3">No users have been created yet. Get started by adding your first user.</p>
                                                @can('user_create')
                                                <a href="{{ route('admin.users.create') }}" 
                                                   class="btn btn-primary btn-lg">
                                                    <i class="fas fa-plus me-2"></i>Create Your First User
                                                </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Mobile/tablet cards code unchanged... -->
            </div>
        </div>
    </div>
<form id="deleteForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<!-- Custom Delete Confirmation Popup -->
<div id="deleteModal" class="delete-modal">
    <div class="delete-modal-content">
        <div class="delete-modal-header">
            <h4 class="delete-modal-title">
                <i class="fas fa-exclamation-triangle"></i>
                Confirm Deletion
            </h4>
            <button type="button" class="delete-modal-close" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="delete-modal-body">
            <div class="delete-modal-icon">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="delete-modal-text">
                Are you sure you want to delete this user?
            </div>
            <div class="delete-modal-user" id="userToDelete">
                User Name Will Appear Here
            </div>
            <div class="delete-modal-subtext">
                This action cannot be undone. The user and all associated data will be permanently removed from the system.
            </div>
        </div>
        <div class="delete-modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="modal-btn modal-btn-delete" id="confirmDeleteBtn">
                <i class="fas fa-trash"></i> Delete User
            </button>
        </div>
    </div>
</div>

<!-- Partner Approval/Rejection Modal -->
<div id="statusModal" class="delete-modal">
    <div class="delete-modal-content">
        <div class="delete-modal-header" id="statusModalHeader">
            <h4 class="delete-modal-title" id="statusModalTitle">
                <i class="fas fa-user-check"></i>
                Confirm Partner Status
            </h4>
            <button type="button" class="delete-modal-close" onclick="closeStatusModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="delete-modal-body">
            <div class="delete-modal-icon" id="statusModalIcon">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="delete-modal-text" id="statusModalText">
                Are you sure you want to approve this partner?
            </div>
            <div class="delete-modal-user" id="partnerToUpdate">
                Partner Name Will Appear Here
            </div>
            <div class="delete-modal-subtext" id="statusModalSubtext">
                The partner will receive an email notification about this status change.
            </div>
            
            <!-- Optional Admin Notes -->
            <div class="mt-3">
                <label for="modalAdminNotes" class="form-label text-light-muted">Admin Notes (Optional)</label>
                <textarea class="form-control bg-dark text-white border-gold" 
                          id="modalAdminNotes" 
                          rows="3" 
                          placeholder="Add any notes or comments for this status change..."></textarea>
            </div>
        </div>
        <div class="delete-modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="closeStatusModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="modal-btn" id="confirmStatusBtn">
                <i class="fas fa-check"></i> Confirm
            </button>
        </div>
    </div>
</div>

<!-- Enhanced Mobile-First Responsive Styles with Fixed Colors -->
<style>
    .equal-btn {
    width: 110px;     /* adjust as needed */
    text-align: center;
}

    /* CSS Variables for Better Color Management */
    :root {
        --primary-gold: #ffd700;
        --darker-bg: #1a1a1a;
        --light-dark: #2a2a2a;
        --text-light: #ffffff;
        --text-muted: #e0e0e0;
        --gradient-primary: linear-gradient(135deg, #ffd700, #ffed4e);
    }

    /* Custom Font Size Classes */
    .fs-8 { font-size: 0.7rem !important; }
    .fs-7 { font-size: 0.8rem !important; }
    .fs-6 { font-size: 0.95rem !important; }

    /* Enhanced Text Visibility with Better Contrast */
    .text-light-muted {
        color: var(--text-muted) !important;
        font-weight: 400;
    }

    .text-white {
        color: var(--text-light) !important;
        font-weight: 500;
    }

    .text-gold {
        color: var(--primary-gold) !important;
        font-weight: 600;
    }

    /* Mobile Button Styling */
    .btn-mobile {
        min-height: 44px !important;
        padding: 0.6rem 1rem !important;
        font-size: 0.875rem !important;
        font-weight: 600;
    }

    /* Enhanced Alert Styling */
    .alert-success-custom {
        background: rgba(40, 167, 69, 0.25) !important;
        border: 2px solid rgba(40, 167, 69, 0.7) !important;
        color: #2ecc71 !important;
        border-radius: 8px;
        font-weight: 500;
    }

    /* Mobile Card Styling with Better Contrast */
    .mobile-card {
        transition: all 0.3s ease;
        border: 2px solid rgba(255, 215, 0, 0.4) !important;
        background: rgba(42, 42, 42, 0.98) !important;
    }

    .mobile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3) !important;
        border-color: rgba(255, 215, 0, 0.7) !important;
    }

    .bg-light-dark {
        background-color: var(--light-dark) !important;
    }

    .border-gold {
        border-color: var(--primary-gold) !important;
    }

    /* Statistics Info with Enhanced Visibility */
    .stats-info {
        background: rgba(255, 215, 0, 0.12) !important;
        border: 2px solid var(--primary-gold) !important;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .stats-item {
        text-align: center;
        color: var(--primary-gold);
    }

    .stats-number {
        font-size: 28px;
        font-weight: 700;
        color: var(--primary-gold);
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }

    .stats-label {
        font-size: 13px;
        color: #cccccc;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
    }

    /* Enhanced Badge Styling */
    .bg-success-custom {
        background: linear-gradient(135deg, #28a745, #20c997) !important;
        color: #ffffff !important;
        font-size: 0.75rem;
        padding: 0.4em 0.7em;
        font-weight: 600;
        border-radius: 6px;
    }

    .bg-info-custom {
        background: linear-gradient(135deg, #17a2b8, #138496) !important;
        color: #ffffff !important;
        font-size: 0.7rem;
        padding: 0.3em 0.6em;
        font-weight: 600;
        border-radius: 4px;
    }

    /* Enhanced Emirates Badge Styling */
    .emirates-badge {
        font-size: 0.7rem !important;
        padding: 0.3em 0.6em !important;
        font-weight: 600 !important;
        border-radius: 4px !important;
        background: linear-gradient(135deg, #17a2b8, #138496) !important;
        color: #ffffff !important;
        display: inline-block;
        white-space: nowrap;
    }

    .emirates-badge-mobile {
        font-size: 0.65rem !important;
        padding: 0.25em 0.5em !important;
        font-weight: 600 !important;
        border-radius: 3px !important;
        background: linear-gradient(135deg, #17a2b8, #138496) !important;
        color: #ffffff !important;
        display: inline-block;
        white-space: nowrap;
    }

    .emirates-list {
        line-height: 1.8;
    }

    .emirates-mobile-list {
        line-height: 1.6;
    }

    /* Enhanced Status Badge Styling */
    .status-badge {
        font-size: 0.75rem !important;
        padding: 0.4em 0.8em !important;
        font-weight: 600 !important;
        border-radius: 6px !important;
        min-width: 90px;
    }

    /* Enhanced Button Styling with Better Contrast */
    .btn-warning-custom {
        background: linear-gradient(135deg, #ffc107, #fd7e14) !important;
        border: 2px solid #ffc107 !important;
        color: #000000 !important;
        font-weight: 600;
        min-height: 40px;
        text-shadow: none;
    }

    .btn-warning-custom:hover {
        background: linear-gradient(135deg, #e0a800, #dc6502) !important;
        border-color: #d39e00 !important;
        color: #000000 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4) !important;
    }

    .btn-danger-custom {
        background: linear-gradient(135deg, #dc3545, #c82333) !important;
        border: 2px solid #dc3545 !important;
        color: #ffffff !important;
        font-weight: 600;
        min-height: 40px;
    }

    .btn-danger-custom:hover {
        background: linear-gradient(135deg, #c82333, #a71e2a) !important;
        border-color: #bd2130 !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4) !important;
    }

    /* Approve/Reject Button Styling */
    .approve-btn {
        background: linear-gradient(135deg, #28a745, #20c997) !important;
        border: 2px solid #28a745 !important;
        color: #ffffff !important;
        font-weight: 600;
    }

    .approve-btn:hover {
        background: linear-gradient(135deg, #20c997, #17a2b8) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4) !important;
    }

    .reject-btn {
        background: linear-gradient(135deg, #dc3545, #c82333) !important;
        border: 2px solid #dc3545 !important;
        color: #ffffff !important;
        font-weight: 600;
    }

    .reject-btn:hover {
        background: linear-gradient(135deg, #c82333, #a71e2a) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4) !important;
    }

    /* Desktop Table Styling with Enhanced Visibility */
    .table-header-gold th {
        background: var(--gradient-primary) !important;
        color: var(--darker-bg) !important;
        font-weight: 700 !important;
        border: 2px solid var(--primary-gold) !important;
        vertical-align: middle !important;
        font-size: 0.9rem;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        padding: 12px 8px;
    }

    .table-dark {
        background: var(--light-dark) !important;
        color: var(--text-light) !important;
    }

    .table-dark td {
        border-color: rgba(255, 215, 0, 0.3) !important;
        vertical-align: middle !important;
        padding: 12px 8px !important;
        background: transparent !important;
        color: var(--text-main) !important;
    }

    .table-row-hover:hover {
        background: rgba(255, 215, 0, 0.15) !important;
    }

    .table-row-hover:hover td {
        background: rgba(255, 215, 0, 0.15) !important;
    }

    /* Responsive Table */
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid rgba(255, 215, 0, 0.3);
    }

    /* User Info */
    .user-info {
        min-height: 45px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* Form Control Styling for Modal */
    .form-control.bg-dark {
        background-color: rgba(42, 42, 42, 0.9) !important;
        border-color: var(--primary-gold) !important;
        color: var(--text-light) !important;
    }

    .form-control.bg-dark:focus {
        background-color: rgba(42, 42, 42, 0.95) !important;
        border-color: var(--primary-gold) !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25) !important;
        color: var(--text-light) !important;
    }

    .form-control.bg-dark::placeholder {
        color: #aaa !important;
    }

    /* Enhanced Delete Modal */
    .delete-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.85);
        animation: fadeIn 0.3s ease;
    }

    .delete-modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .delete-modal-content {
        background: var(--dark-bg);
        border: 1px solid var(--border-color);
        border-radius: 24px;
        padding: 0;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        animation: slideIn 0.3s ease;
        overflow: hidden;
    }

    .delete-modal-header {
        background: #1a1d27;
        color: white;
        padding: 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .delete-modal-header.approve {
        background: linear-gradient(45deg, #28a745, #20c997) !important;
    }

    .delete-modal-header.reject {
        background: linear-gradient(45deg, #dc3545, #ff4757) !important;
    }

    .delete-modal-title {
        font-size: 20px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #ffffff !important;
    }

    .delete-modal-close {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        padding: 0;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .delete-modal-close:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: rotate(90deg);
    }

    .delete-modal-body {
        padding: 30px 25px;
        text-align: center;
    }

    .delete-modal-icon {
        color: var(--primary-gold);
        font-size: 4rem;
        margin-bottom: 20px;
    }

    .delete-modal-text {
        color: #ffffff !important;
        font-size: 18px;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .delete-modal-subtext {
        color: #cccccc !important;
        font-size: 14px;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .delete-modal-user {
        background: rgba(255, 215, 0, 0.15);
        border: 2px solid var(--primary-gold);
        border-radius: 8px;
        padding: 15px;
        margin: 20px 0;
        color: var(--primary-gold) !important;
        font-weight: 700;
    }

    .delete-modal-footer {
        display: flex;
        gap: 15px;
        justify-content: center;
        padding: 0 25px 25px 25px;
    }

    .modal-btn {
        flex: 1;
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 120px;
    }

    .modal-btn-cancel {
        background: linear-gradient(45deg, #6c757d, #868e96);
        color: white !important;
    }

    .modal-btn-cancel:hover {
        background: linear-gradient(45deg, #868e96, #6c757d);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4);
    }

    .modal-btn-delete {
        background: linear-gradient(45deg, #dc3545, #ff4757);
        color: white !important;
    }

    .modal-btn-delete:hover {
        background: linear-gradient(45deg, #ff4757, #dc3545);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }

    .modal-btn.approve-action {
        background: linear-gradient(45deg, #28a745, #20c997) !important;
        color: white !important;
    }

    .modal-btn.approve-action:hover {
        background: linear-gradient(45deg, #20c997, #17a2b8) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
    }

    .modal-btn.reject-action {
        background: linear-gradient(45deg, #dc3545, #ff4757) !important;
        color: white !important;
    }

    .modal-btn.reject-action:hover {
        background: linear-gradient(45deg, #ff4757, #dc3545) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }

    .modal-btn.processing {
        background: #666 !important;
        cursor: not-allowed;
        opacity: 0.7;
    }

    .modal-btn.processing:hover {
        transform: none;
        box-shadow: none;
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideIn {
        from { 
            transform: scale(0.8) translateY(-50px);
            opacity: 0;
        }
        to { 
            transform: scale(1) translateY(0);
            opacity: 1;
        }
    }

    /* DataTables Custom Styling */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        color: var(--primary-gold) !important;
    }

    .dataTables_wrapper .dataTables_length select,
    .dataTables_wrapper .dataTables_filter input {
        background: var(--light-dark) !important;
        border: 2px solid var(--primary-gold) !important;
        color: var(--text-light) !important;
        border-radius: 4px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        background: linear-gradient(45deg, var(--light-dark), var(--darker-bg)) !important;
        border: 2px solid var(--primary-gold) !important;
        color: var(--primary-gold) !important;
        margin: 0 2px;
        border-radius: 4px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--gradient-primary) !important;
        color: var(--darker-bg) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--gradient-primary) !important;
        color: var(--darker-bg) !important;
    }

    /* Hide DataTables Buttons Container */
    .dt-buttons {
        display: none !important;
    }

    /* Enhanced Badge */
    .badge {
        font-size: 0.8rem !important;
        padding: 0.4em 0.7em !important;
        font-weight: 600 !important;
        border-radius: 6px !important;
    }






    .equal-btn {
    width: 110px;     /* adjust as needed */
    text-align: center;
}

    /* BREAKPOINT: Extra Small (Phone) */
    @media (max-width: 575.98px) {
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        
        .card-body {
            padding: 1rem !important;
        }
        
        .card-header {
            padding: 0.75rem 1rem !important;
        }
        
        .mobile-card .card-body {
            padding: 0.75rem !important;
        }
        
        .btn {
            font-size: 0.8rem !important;
            padding: 0.5rem 0.75rem !important;
        }
        
        h3 {
            font-size: 1.1rem !important;
        }
        
        .empty-state-mobile {
            padding: 1.5rem 0.5rem;
        }
        
        .delete-modal-content {
            width: 95%;
            margin: 20px;
        }
        
        .delete-modal-footer {
            flex-direction: column;
        }
        
        .modal-btn {
            width: 100%;
        }

        .stats-number {
            font-size: 22px;
        }

        .fs-6 {
            font-size: 0.85rem !important;
        }
    }

    /* BREAKPOINT: Small (Large Phone) */
    @media (min-width: 576px) and (max-width: 767.98px) {
        .fs-sm-7 { font-size: 0.8rem !important; }
        .fs-sm-6 { font-size: 0.9rem !important; }
        .fs-sm-5 { font-size: 1rem !important; }
    }

    /* BREAKPOINT: Medium (Tablet) */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .card-body {
            padding: 2rem !important;
        }
    }

    /* BREAKPOINT: Large (Desktop) */
    @media (min-width: 992px) {
        .fs-md-5 { font-size: 1rem !important; }
        .fs-md-4 { font-size: 1.25rem !important; }
        
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }

    /* BREAKPOINT: Extra Large (Large Desktop) */
    @media (min-width: 1200px) {
        .container-fluid {
            max-width: 1400px;
            margin: 0 auto;
        }
    }

    /* Touch Device Optimizations */
    @media (hover: none) and (pointer: coarse) {
        .btn {
            min-height: 48px !important;
        }
        
        .animate-scale:hover {
            transform: none !important;
        }
        
        .mobile-card:hover {
            transform: none !important;
        }
    }

    /* Reduced Motion */
    @media (prefers-reduced-motion: reduce) {
        .animate-fade-in,
        .animate-scale,
        .mobile-card {
            animation: none !important;
            transition: none !important;
        }
    }

    /* High Contrast Mode Support */
    @media (prefers-contrast: high) {
        .text-light-muted {
            color: #ffffff !important;
        }
        
        .table-dark td {
            border-width: 3px !important;
        }

        .mobile-card {
            border-width: 3px !important;
        }
    }

    /* Landscape Mobile */
    @media (max-height: 500px) and (orientation: landscape) and (max-width: 991px) {
        .mobile-card .card-body {
            padding: 0.75rem !important;
        }
        
        .empty-state-mobile {
            padding: 1rem 0.5rem;
        }
        
        .delete-modal-header {
            padding: 15px 20px;
        }
        
        .delete-modal-body {
            padding: 20px 15px;
        }
        
        .delete-modal-title {
            font-size: 18px;
        }
        
        .delete-modal-text {
            font-size: 16px;
        }
    }
</style>
@endsection

@push('script-alt')
<script>
$(function () {
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Custom delete modal functionality
    let currentUserId = null;
    let currentAction = null;

    // Add click event to all delete buttons
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        
        currentUserId = userId;
        showDeleteModal(userName);
    });

    // Add click event to approve buttons
    $(document).on('click', '.approve-btn', function(e) {
        e.preventDefault();
        
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        
        currentUserId = userId;
        currentAction = 'approve';
        showStatusModal(userName, 'approve');
    });

    // Add click event to reject buttons
    $(document).on('click', '.reject-btn', function(e) {
        e.preventDefault();
        
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        
        currentUserId = userId;
        currentAction = 'reject';
        showStatusModal(userName, 'reject');
    });

    // Confirm delete button
    $('#confirmDeleteBtn').on('click', function() {
        if (currentUserId) {
            // Show processing state
            $(this).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');
            $(this).addClass('processing').prop('disabled', true);
            
            // Set up and submit the form
            const deleteForm = $('#deleteForm');
            deleteForm.attr('action', `{{ route('admin.users.index') }}/${currentUserId}`);
            
            setTimeout(() => {
                deleteForm.submit();
            }, 500);
        }
    });

    // Confirm status update button
    $('#confirmStatusBtn').on('click', function() {
        if (currentUserId && currentAction) {
            // Show processing state
            const originalHtml = $(this).html();
            $(this).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
            $(this).addClass('processing').prop('disabled', true);
            
            // Get admin notes
            const adminNotes = $('#modalAdminNotes').val();
            const statusValue = currentAction === 'approve' ? '1' : '2';
            
            // Get CSRF token
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            
            // AJAX request to update status
            $.ajax({
                url: `/admin/partner-status/${currentUserId}`,
                type: 'PUT',
                data: {
                    status: statusValue,
                    admin_notes: adminNotes,
                    _token: csrfToken
                },
                success: function(response) {
                    console.log('Status update response:', response);
                    
                    if (response.success) {
                        // Show success message
                        const message = currentAction === 'approve' ? 
                            'Partner has been approved successfully!' : 
                            'Partner has been rejected successfully!';
                            
                        // Add success alert
                        $('.card-body').prepend(`
                            <div class="alert alert-success-custom alert-dismissible fade show animate-fade-in mb-3 mb-md-4" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                ${message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `);
                        
                        closeStatusModal();
                        
                        // Reload page after short delay to show updated status
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        console.error('Status update failed:', response);
                        alert('Error: ' + (response.message || 'Failed to update status'));
                        closeStatusModal();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                    
                    let errorMessage = 'Network error occurred. Please try again.';
                    
                    if (xhr.status === 404) {
                        errorMessage = 'User not found.';
                    } else if (xhr.status === 422) {
                        errorMessage = 'Invalid data provided.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error occurred.';
                    }
                    
                    alert(errorMessage);
                    closeStatusModal();
                }
            });
        }
    });

    // Close modal events
    $(document).on('click', '#deleteModal .delete-modal-close, #deleteModal .modal-btn-cancel', function() {
        closeDeleteModal();
    });

    $(document).on('click', '#statusModal .delete-modal-close, #statusModal .modal-btn-cancel', function() {
        closeStatusModal();
    });

    // Close modal on outside click
    $(document).on('click', '#deleteModal', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    $(document).on('click', '#statusModal', function(e) {
        if (e.target === this) {
            closeStatusModal();
        }
    });

    // Escape key to close modal
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
            closeStatusModal();
        }
    });

    // Auto-hide success alerts
    const successAlert = $('.alert-success-custom');
    if (successAlert.length) {
        setTimeout(() => {
            successAlert.fadeOut();
        }, 5000);
    }

    // DataTables configuration WITHOUT BUTTONS
    $.extend(true, $.fn.dataTable.defaults, {
        order: [[ 0, 'asc' ]],
        pageLength: 25,
    });
    
    $('.datatable-User:not(.ajaxTable)').DataTable({ 
        responsive: true,
        columnDefs: [
            { orderable: false, targets: [3, 5] } // Disable sorting for Emirates and Actions columns
        ]
    });
    
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
});

function showDeleteModal(userName) {
    $('#userToDelete').text(userName);
    $('#deleteModal').addClass('show');
    $('body').css('overflow', 'hidden');
}

function closeDeleteModal() {
    $('#deleteModal').removeClass('show');
    $('body').css('overflow', '');
    
    // Reset confirm button
    const confirmBtn = $('#confirmDeleteBtn');
    confirmBtn.html('<i class="fas fa-trash"></i> Delete User');
    confirmBtn.removeClass('processing').prop('disabled', false);
    
    currentUserId = null;
}

function showStatusModal(userName, action) {
    const modal = $('#statusModal');
    const header = $('#statusModalHeader');
    const title = $('#statusModalTitle');
    const icon = $('#statusModalIcon');
    const text = $('#statusModalText');
    const subtext = $('#statusModalSubtext');
    const confirmBtn = $('#confirmStatusBtn');
    
    $('#partnerToUpdate').text(userName);
    $('#modalAdminNotes').val('');
    
    if (action === 'approve') {
        header.removeClass('reject').addClass('approve');
        title.html('<i class="fas fa-user-check"></i> Approve Partner');
        icon.html('<i class="fas fa-user-check"></i>');
        text.text('Are you sure you want to approve this partner?');
        subtext.text('The partner will gain access to the system and receive an approval notification.');
        confirmBtn.removeClass('reject-action').addClass('approve-action');
        confirmBtn.html('<i class="fas fa-check"></i> Approve Partner');
    } else {
        header.removeClass('approve').addClass('reject');
        title.html('<i class="fas fa-user-times"></i> Reject Partner');
        icon.html('<i class="fas fa-user-times"></i>');
        text.text('Are you sure you want to reject this partner?');
        subtext.text('The partner will be denied access and receive a rejection notification.');
        confirmBtn.removeClass('approve-action').addClass('reject-action');
        confirmBtn.html('<i class="fas fa-times"></i> Reject Partner');
    }
    
    modal.addClass('show');
    $('body').css('overflow', 'hidden');
}

function closeStatusModal() {
    $('#statusModal').removeClass('show');
    $('body').css('overflow', '');
    
    // Reset confirm button
    const confirmBtn = $('#confirmStatusBtn');
    confirmBtn.removeClass('processing approve-action reject-action').prop('disabled', false);
    
    currentUserId = null;
    currentAction = null;
}
</script>
@endpush
