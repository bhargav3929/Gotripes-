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
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.users.create') }}" 
                               class="btn btn-primary btn-mobile animate-scale">
                                <i class="fas fa-plus me-1"></i> 
                                <span class="d-none d-sm-inline">New User</span>
                                <span class="d-sm-none">Add New</span>
                            </a>
                            @endif
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
                                        <th class="text-center" style="width: 150px;">
                                            <i class="fas fa-shield-alt me-1"></i>Access
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
                                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="user-info">
                                                <strong class="text-white">{{ $user->name }}</strong>
                                                @if($user->phone)
                                                    <div class="mt-1">
                                                        <small class="text-light-muted">
                                                            <i class="fas fa-phone me-1"></i>{{ $user->phone }}
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
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
                                                    <span class="badge badge-status badge-status-regular">
                                                        <i class="fas fa-user"></i>Regular
                                                    </span>
                                                @endif
                                                <div class="mt-1"><small class="text-light-muted">User</small></div>
                                            @endif
                                        </td>
                                        <!-- ACCESS/ROLE COLUMN -->
                                        <td class="text-center">
                                            @php
                                                $userRolesList = $user->roles->pluck('title')->toArray();
                                            @endphp
                                            @if(in_array('Admin', $userRolesList))
                                                <span class="badge rounded-pill px-3 py-1 badge-gold">
                                                    <i class="fas fa-shield-alt me-1"></i>Full Access
                                                </span>
                                            @elseif(!empty($userRolesList))
                                                @foreach($userRolesList as $roleName)
                                                    <span class="badge rounded-pill px-2 py-1 mb-1 badge-role">
                                                        {{ $roleName }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted fs-7">No role</span>
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
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                @if($isPartner && $status == '0')
                                                    <button type="button"
                                                            class="btn-icon btn-icon-approve approve-btn"
                                                            data-user-id="{{ $user->id }}"
                                                            data-user-name="{{ $user->name }}"
                                                            title="Approve Partner">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button"
                                                            class="btn-icon btn-icon-reject reject-btn"
                                                            data-user-id="{{ $user->id }}"
                                                            data-user-name="{{ $user->name }}"
                                                            title="Reject Partner">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                                   class="btn-icon btn-icon-edit"
                                                   title="Edit User">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                @if(!($isPartner && $status == '0'))
                                                <button type="button"
                                                        class="btn-icon btn-icon-delete delete-btn"
                                                        data-user-id="{{ $user->id }}"
                                                        data-user-name="{{ $user->name }}"
                                                        title="Delete User">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5">
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
    /* Compact icon-only action buttons */
    .btn-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid transparent;
        background: transparent;
        cursor: pointer;
        transition: all 0.15s ease;
        font-size: 0.75rem;
        text-decoration: none;
        color: var(--text-muted);
    }
    .btn-icon:hover { color: var(--text-main); }
    .btn-icon-edit { color: var(--primary-gold); border-color: rgba(255, 215, 0, 0.12); background: rgba(255, 215, 0, 0.04); }
    .btn-icon-edit:hover { background: var(--primary-gold); color: #000; border-color: var(--primary-gold); }
    .btn-icon-delete { color: var(--text-muted); border-color: rgba(255, 255, 255, 0.06); background: rgba(255, 255, 255, 0.02); }
    .btn-icon-delete:hover { background: var(--danger); color: #fff; border-color: var(--danger); }
    .btn-icon-approve { color: var(--success); border-color: rgba(34, 197, 94, 0.15); background: rgba(34, 197, 94, 0.06); }
    .btn-icon-approve:hover { background: var(--success); color: #fff; border-color: var(--success); }
    .btn-icon-reject { color: var(--text-muted); border-color: rgba(255, 255, 255, 0.06); background: rgba(255, 255, 255, 0.02); }
    .btn-icon-reject:hover { background: var(--danger); color: #fff; border-color: var(--danger); }

    /* Emirates Badge */
    .emirates-badge {
        font-size: 0.6875rem;
        padding: 0.2em 0.5em;
        font-weight: 600;
        border-radius: 4px;
        background: rgba(59, 130, 246, 0.12);
        color: var(--info);
        display: inline-block;
        white-space: nowrap;
    }

    .emirates-list { line-height: 1.8; }
    .emirates-mobile-list { line-height: 1.6; }

    /* User Info */
    .user-info {
        min-height: 45px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* Modal textarea */
    .form-control.bg-dark {
        background-color: rgba(255, 255, 255, 0.04) !important;
        border-color: var(--border-color) !important;
        color: var(--text-main) !important;
    }
    .form-control.bg-dark:focus {
        border-color: var(--primary-gold) !important;
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1) !important;
    }

    /* Modal variants for status changes */
    .delete-modal-header.approve {
        background: rgba(34, 197, 94, 0.1) !important;
        border-bottom: 1px solid rgba(34, 197, 94, 0.15);
    }
    .delete-modal-header.reject {
        background: rgba(239, 68, 68, 0.1) !important;
        border-bottom: 1px solid rgba(239, 68, 68, 0.15);
    }
    .delete-modal-header.approve .delete-modal-title { color: var(--success) !important; }
    .delete-modal-header.approve .delete-modal-title i { color: var(--success); }
    .delete-modal-header.reject .delete-modal-title { color: var(--danger) !important; }
    .delete-modal-header.reject .delete-modal-title i { color: var(--danger); }
    .delete-modal-header.approve .delete-modal-close,
    .delete-modal-header.reject .delete-modal-close { color: var(--text-muted) !important; }
    .modal-btn.approve-action {
        background: var(--success) !important;
        color: #fff !important;
    }
    .modal-btn.approve-action:hover { background: #16a34a !important; }
    .modal-btn.reject-action {
        background: var(--danger) !important;
        color: #fff !important;
    }
    .modal-btn.reject-action:hover { background: #dc2626 !important; }

    /* DataTables overrides */
    .dt-buttons { display: none !important; }

    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        color: var(--text-muted) !important;
        font-size: 0.8125rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        background: var(--card-bg) !important;
        border: 1px solid var(--border-color) !important;
        color: var(--text-muted) !important;
        border-radius: 6px;
        margin: 0 2px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: rgba(255, 215, 0, 0.08) !important;
        color: var(--primary-gold) !important;
        border-color: var(--border-gold) !important;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--primary-gold) !important;
        color: #000 !important;
        border-color: var(--primary-gold) !important;
    }

    /* Mobile */
    @media (max-width: 575.98px) {
        .delete-modal-footer { flex-direction: column; }
        .modal-btn { width: 100%; }
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
