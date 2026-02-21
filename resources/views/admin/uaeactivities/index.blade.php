@extends('layouts.admin')

@section('title', 'Manage UAE Activities')

@section('page-title', 'Manage UAE Activities')

@section('content')

@php
    // Use the variables passed from controller
    $totalFilteredCount = $activities->total();
@endphp

<style>
    :root {
        --primary-black: #1a1a1a;
        --secondary-black: #2d2d2d;
        --tertiary-black: #3d3d3d;
        --primary-gold: #ffd700;
        --secondary-gold: #ffed4e;
        --tertiary-gold: #ffc107;
        --text-white: #ffffff;
        --text-light: #e9ecef;
        --text-muted: #adb5bd;
        --border-gold: rgba(255, 215, 0, 0.6);
    }

    body {
        background-color: var(--primary-black) !important;
        color: var(--text-white) !important;
    }

    .gold-theme {
        background: linear-gradient(135deg, var(--primary-black) 0%, var(--secondary-black) 100%);
        color: var(--primary-gold);
        border-radius: 15px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    .btn-gold {
        background: linear-gradient(45deg, var(--primary-gold), var(--secondary-gold)) !important;
        color: var(--primary-black) !important;
        border: none !important;
        font-weight: 600 !important;
        transition: all 0.3s ease !important;
        border-radius: 8px !important;
    }
    
    .btn-gold:hover {
        background: linear-gradient(45deg, var(--secondary-gold), var(--primary-gold)) !important;
        color: var(--primary-black) !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
    }
    
    .table-dark-gold {
        background-color: var(--primary-black) !important;
        color: var(--text-white) !important;
        margin-bottom: 0;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .table-dark-gold th {
        background: linear-gradient(45deg, var(--secondary-black), var(--primary-black)) !important;
        color: var(--primary-gold) !important;
        border-color: var(--primary-gold) !important;
        font-weight: 600 !important;
        text-transform: uppercase;
        letter-spacing: 1px;
        vertical-align: middle;
        padding: 18px 12px;
        font-size: 13px;
        border-bottom: 2px solid var(--primary-gold);
    }
    
    .table-dark-gold td {
        background-color: var(--secondary-black) !important;
        border-color: #555 !important;
        color: var(--text-white) !important;
        vertical-align: middle;
        padding: 15px 12px;
        border-top: 1px solid #555;
    }
    
    .table-dark-gold tbody tr {
        transition: all 0.3s ease;
    }
    
    .table-dark-gold tbody tr:hover {
        background: linear-gradient(45deg, var(--tertiary-black), var(--secondary-black)) !important;
        color: var(--text-white) !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 215, 0, 0.2);
    }

    .table-dark-gold tbody tr:hover td {
        background-color: transparent !important;
    }
    
    .alert-gold {
        background: linear-gradient(45deg, var(--primary-gold), var(--secondary-gold)) !important;
        color: var(--primary-black) !important;
        border: none !important;
        font-weight: 600 !important;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
    }
    
    .card-dark-gold {
        background: linear-gradient(135deg, var(--primary-black) 0%, var(--secondary-black) 100%) !important;
        border: 2px solid var(--primary-gold) !important;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }
    
    .card-dark-gold .card-header {
        background: linear-gradient(45deg, var(--primary-gold), var(--secondary-gold)) !important;
        color: var(--primary-black) !important;
        font-weight: 600 !important;
        border-radius: 13px 13px 0 0;
        border-bottom: 2px solid var(--primary-gold);
        padding: 20px;
    }

    /* Partner Filter Info Box */
    .partner-filter-info {
        background: rgba(23, 162, 184, 0.1) !important;
        border: 2px solid #17a2b8 !important;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        color: #17a2b8 !important;
    }

    .partner-filter-icon {
        color: #17a2b8 !important;
        font-size: 1.2rem;
        margin-right: 10px;
    }
    
    /* Created By Column Styling */
    .creator-info {
        color: var(--secondary-gold) !important;
        font-size: 13px !important;
        font-weight: 500 !important;
    }
    
    .creator-icon {
        color: var(--tertiary-gold) !important;
        margin-right: 6px;
    }

    .creator-match {
        color: #28a745 !important;
        font-weight: 600 !important;
    }

    .creator-other {
        color: var(--text-muted) !important;
    }
    
    .img-preview {
        border: 2px solid var(--primary-gold) !important;
        border-radius: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .img-preview:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.4);
        border-color: var(--secondary-gold) !important;
    }
    
    .activity-name {
        color: var(--primary-gold) !important;
        font-weight: 600 !important;
        font-size: 15px !important;
    }
    
    .location-text {
        color: var(--text-white) !important;
        font-size: 14px !important;
    }
    
    .emirate-text {
        color: var(--secondary-gold) !important;
        font-size: 14px !important;
        font-weight: 500 !important;
    }
    
    .location-icon {
        color: var(--primary-gold) !important;
        margin-right: 8px;
    }
    
    .emirate-icon {
        color: var(--secondary-gold) !important;
        margin-right: 8px;
    }
    
    .no-image-placeholder {
        background: rgba(255, 215, 0, 0.1) !important;
        border: 2px dashed var(--primary-gold) !important;
        border-radius: 8px;
        padding: 20px 10px;
        color: var(--primary-gold) !important;
        font-size: 12px;
        text-align: center;
    }
    
    .stats-info {
        background: rgba(255, 215, 0, 0.1) !important;
        border: 1px solid var(--primary-gold) !important;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .stats-item {
        text-align: center;
        color: var(--primary-gold) !important;
    }
    
    .stats-number {
        font-size: 24px !important;
        font-weight: bold !important;
        color: var(--primary-gold) !important;
    }
    
    .stats-label {
        font-size: 12px !important;
        color: var(--text-muted) !important;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    /* Enhanced Action Buttons */
    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .btn-action {
        padding: 6px 12px !important;
        font-size: 12px !important;
        border-radius: 6px;
        font-weight: 600 !important;
        transition: all 0.3s ease;
        min-width: 70px;
    }
    
    .btn-edit {
        background: linear-gradient(45deg, var(--tertiary-gold), var(--secondary-gold)) !important;
        color: var(--primary-black) !important;
        border: none !important;
    }
    
    .btn-edit:hover {
        background: linear-gradient(45deg, var(--secondary-gold), var(--tertiary-gold)) !important;
        color: var(--primary-black) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
    }
    
    .btn-delete {
        background: linear-gradient(45deg, #dc3545, #ff4757) !important;
        color: white !important;
        border: none !important;
    }
    
    .btn-delete:hover {
        background: linear-gradient(45deg, #ff4757, #dc3545) !important;
        color: white !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }

    /* Mobile Card Styling */
    .mobile-card {
        transition: all 0.3s ease !important;
        border: 1px solid var(--border-gold) !important;
        background: linear-gradient(135deg, var(--secondary-black) 0%, var(--tertiary-black) 100%) !important;
    }

    .mobile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.2) !important;
    }

    .mobile-card .card-body {
        background: transparent !important;
    }

    .border-gold {
        border-color: var(--border-gold) !important;
    }

    .text-light-muted {
        color: var(--text-light) !important;
    }

    .fs-7 {
        font-size: 0.8rem !important;
    }

    .mobile-activity-img {
        max-width: 100%;
        height: 120px;
        object-fit: cover;
        border: 2px solid var(--primary-gold) !important;
        border-radius: 8px;
    }

    .btn-warning-custom {
        background: linear-gradient(135deg, var(--tertiary-gold), #fd7e14) !important;
        border: 1px solid var(--tertiary-gold) !important;
        color: var(--primary-black) !important;
        font-weight: 500 !important;
        min-height: 40px;
    }

    .btn-warning-custom:hover {
        background: linear-gradient(135deg, #e0a800, #dc6502) !important;
        border-color: #d39e00 !important;
        color: var(--primary-black) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4) !important;
    }

    .btn-danger-custom {
        background: linear-gradient(135deg, #dc3545, #c82333) !important;
        border: 1px solid #dc3545 !important;
        color: var(--text-white) !important;
        font-weight: 500 !important;
        min-height: 40px;
    }

    .btn-danger-custom:hover {
        background: linear-gradient(135deg, #c82333, #a71e2a) !important;
        border-color: #bd2130 !important;
        color: var(--text-white) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4) !important;
    }

    /* Text Visibility Improvements */
    .card-title {
        color: var(--text-white) !important;
        font-weight: 600 !important;
    }

    .card-text {
        color: var(--text-light) !important;
    }

    .text-muted {
        color: var(--text-muted) !important;
    }

    small {
        color: var(--text-muted) !important;
    }

    .badge {
        background-color: var(--primary-gold) !important;
        color: var(--primary-black) !important;
        font-weight: 600 !important;
    }

    /* Empty State Styling */
    .empty-state-mobile, .empty-state-desktop {
        text-align: center;
        padding: 60px 20px;
        background: rgba(255, 215, 0, 0.05) !important;
        border-radius: 15px;
        border: 2px dashed var(--primary-gold) !important;
    }

    .empty-state-icon {
        color: var(--primary-gold) !important;
        font-size: 4rem !important;
        margin-bottom: 20px;
    }

    .empty-state-title {
        color: var(--primary-gold) !important;
        font-size: 1.5rem !important;
        font-weight: 600 !important;
        margin-bottom: 10px;
    }

    .empty-state-text {
        color: var(--text-muted) !important;
        margin-bottom: 30px;
    }
    
    /* Custom Delete Modal - keeping existing styles */
    .delete-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        animation: fadeIn 0.3s ease;
    }
    
    .delete-modal.show {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }
    
    .delete-modal-content {
        background: linear-gradient(135deg, var(--primary-black) 0%, var(--secondary-black) 100%) !important;
        border: 3px solid var(--primary-gold) !important;
        border-radius: 15px;
        padding: 0;
        width: 90%;
        max-width: 500px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
        animation: slideIn 0.3s ease;
        overflow: hidden;
    }
    
    .delete-modal-header {
        background: linear-gradient(45deg, #dc3545, #ff4757) !important;
        color: white !important;
        padding: 20px 25px;
        border-bottom: 2px solid var(--primary-gold);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .delete-modal-title {
        font-size: 20px !important;
        font-weight: 600 !important;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        color: white !important;
    }
    
    .delete-modal-close {
        background: none !important;
        border: none !important;
        color: white !important;
        font-size: 24px !important;
        cursor: pointer;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.3s ease;
    }
    
    .delete-modal-close:hover {
        background: rgba(255, 255, 255, 0.2) !important;
        transform: rotate(90deg);
    }
    
    .delete-modal-body {
        padding: 30px 25px;
        text-align: center;
    }
    
    .delete-modal-icon {
        color: var(--primary-gold) !important;
        font-size: 4rem !important;
        margin-bottom: 20px;
    }
    
    .delete-modal-text {
        color: var(--text-white) !important;
        font-size: 18px !important;
        margin-bottom: 10px;
        font-weight: 600 !important;
    }
    
    .delete-modal-subtext {
        color: var(--text-muted) !important;
        font-size: 14px !important;
        margin-bottom: 30px;
        line-height: 1.5;
    }
    
    .delete-modal-activity {
        background: rgba(255, 215, 0, 0.1) !important;
        border: 1px solid var(--primary-gold) !important;
        border-radius: 8px;
        padding: 15px;
        margin: 20px 0;
        color: var(--primary-gold) !important;
        font-weight: 600 !important;
    }
    
    .delete-modal-footer {
        display: flex;
        gap: 15px;
        justify-content: center;
        padding: 0 25px 25px 25px;
    }
    
    .modal-btn {
        flex: 1;
        padding: 12px 20px !important;
        border: none !important;
        border-radius: 8px;
        font-weight: 600 !important;
        font-size: 16px !important;
        cursor: pointer;
        transition: all 0.3s ease;
        min-width: 120px;
    }
    
    .modal-btn-cancel {
        background: linear-gradient(45deg, #6c757d, #868e96) !important;
        color: white !important;
    }
    
    .modal-btn-cancel:hover {
        background: linear-gradient(45deg, #868e96, #6c757d) !important;
        color: white !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4);
    }
    
    .modal-btn-delete {
        background: linear-gradient(45deg, #dc3545, #ff4757) !important;
        color: white !important;
    }
    
    .modal-btn-delete:hover {
        background: linear-gradient(45deg, #ff4757, #dc3545) !important;
        color: white !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }
    
    .modal-btn-delete.processing {
        background: #666 !important;
        cursor: not-allowed;
        opacity: 0.7;
    }
    
    .modal-btn-delete.processing:hover {
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
    
    /* Custom Simple Pagination */
    .custom-pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin-top: 30px;
        flex-wrap: wrap;
    }
    
    .pagination-info {
        color: var(--primary-gold) !important;
        font-weight: 600 !important;
        background: rgba(255, 215, 0, 0.1) !important;
        padding: 8px 16px;
        border-radius: 20px;
        border: 1px solid var(--primary-gold) !important;
    }
    
    .pagination-nav {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }
    
    .page-btn {
        background: linear-gradient(45deg, var(--secondary-black), var(--primary-black)) !important;
        border: 2px solid var(--primary-gold) !important;
        color: var(--primary-gold) !important;
        padding: 8px 12px;
        border-radius: 8px;
        font-weight: 600 !important;
        text-decoration: none !important;
        transition: all 0.3s ease;
        min-width: 40px;
        text-align: center;
    }
    
    .page-btn:hover {
        background: linear-gradient(45deg, var(--primary-gold), var(--secondary-gold)) !important;
        color: var(--primary-black) !important;
        text-decoration: none !important;
        transform: translateY(-2px);
    }
    
    .page-btn.active {
        background: linear-gradient(45deg, var(--primary-gold), var(--secondary-gold)) !important;
        color: var(--primary-black) !important;
    }
    
    .page-btn.disabled {
        background: #555 !important;
        color: #999 !important;
        border-color: #555 !important;
        cursor: not-allowed;
    }
    
    .page-btn.disabled:hover {
        background: #555 !important;
        color: #999 !important;
        transform: none;
    }
    
    /* Mobile Responsive */
    @media (max-width: 992px) {
        .card-header {
            flex-direction: column !important;
            text-align: center !important;
            gap: 15px;
        }
        
        .stats-info {
            margin-bottom: 15px;
        }
    }
    
    @media (max-width: 768px) {
        .table-dark-gold th,
        .table-dark-gold td {
            padding: 10px 6px !important;
            font-size: 12px !important;
        }
        
        .img-preview {
            width: 60px !important;
            height: 45px !important;
        }
        
        .action-buttons {
            flex-direction: column;
            gap: 5px;
        }
        
        .btn-action {
            width: 100% !important;
            min-width: auto;
        }
        
        .activity-name {
            font-size: 13px !important;
        }
        
        .location-text {
            font-size: 12px !important;
        }
        
        .emirate-text {
            font-size: 12px !important;
        }
        
        .creator-info {
            font-size: 11px !important;
        }
        
        .custom-pagination {
            flex-direction: column;
            gap: 15px;
        }
        
        .delete-modal-content {
            width: 95% !important;
            margin: 20px;
        }

        .delete-modal-footer {
            flex-direction: column;
        }
        
        .modal-btn {
            width: 100% !important;
        }

        .mobile-card .card-body {
            padding: 15px !important;
        }

        .btn {
            font-size: 0.8rem !important;
            padding: 0.5rem 0.75rem !important;
        }

        .mobile-activity-img {
            height: 100px;
        }
    }
    
    @media (max-width: 576px) {
        .table-responsive {
            border: none;
        }
        
        .table-dark-gold {
            font-size: 11px !important;
        }
        
        .table-dark-gold th,
        .table-dark-gold td {
            padding: 8px 4px !important;
        }
        
        .img-preview {
            width: 50px !important;
            height: 40px !important;
        }
        
        .stats-info {
            padding: 10px;
        }
        
        .stats-number {
            font-size: 18px !important;
        }
        
        .empty-state-mobile, .empty-state-desktop {
            padding: 40px 15px;
        }
        
        .empty-state-icon {
            font-size: 3rem !important;
        }
        
        .delete-modal-header {
            padding: 15px 20px;
        }
        
        .delete-modal-body {
            padding: 20px 15px;
        }
        
        .delete-modal-title {
            font-size: 18px !important;
        }
        
        .delete-modal-text {
            font-size: 16px !important;
        }

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

        h3 {
            font-size: 1.1rem !important;
        }
    }

    /* Touch Device Optimizations */
    @media (hover: none) and (pointer: coarse) {
        .btn {
            min-height: 48px !important;
        }
        
        .mobile-card:hover {
            transform: none !important;
        }

        .img-preview:hover {
            transform: none !important;
        }

        .table-dark-gold tbody tr:hover {
            transform: none !important;
        }
    }

    /* Reduced Motion */
    @media (prefers-reduced-motion: reduce) {
        .mobile-card,
        .img-preview,
        .table-dark-gold tbody tr {
            animation: none !important;
            transition: none !important;
        }
    }

    /* Container Responsive */
    @media (min-width: 1200px) {
        .container-fluid {
            max-width: 1400px;
            margin: 0 auto;
        }
    }

    /* Additional Text Contrast Improvements */
    .table-dark-gold .activity-name {
        color: var(--primary-gold) !important;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    .table-dark-gold .location-text {
        color: var(--text-white) !important;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    .table-dark-gold .emirate-text {
        color: var(--secondary-gold) !important;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    /* Button text contrast */
    .btn-gold, .btn-warning-custom {
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }

    .btn-danger-custom, .btn-delete {
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    /* Alert text visibility */
    .alert-gold {
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }

    /* Card header text */
    .card-dark-gold .card-header {
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }

    /* Modal text improvements */
    .delete-modal-title {
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    /* Mobile card improvements */
    .mobile-card .activity-name {
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.7);
    }

    .mobile-card .text-light-muted {
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }
</style>

<div class="container-fluid px-2 px-md-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card card-dark-gold">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-map-marked-alt me-2 d-none d-sm-inline"></i>
                        <span class="d-none d-sm-inline">UAE Activities </span>Management
                        @if($isApprovedPartner)
                            <small class="text-dark ms-2">(Your Activities)</small>
                        @endif
                    </h3>
                    <a href="{{ route('admin.uaeactivities.create') }}" class="btn btn-gold">
                        <i class="fas fa-plus me-1"></i> 
                        <span class="d-none d-sm-inline">Add New Activity</span>
                        <span class="d-sm-none">Add New</span>
                    </a>
                </div>
                <div class="card-body p-2 p-sm-3 p-md-4">
                    @if(session('success'))
                        <div class="alert alert-gold alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Partner Filter Information -->
                    @if($isApprovedPartner)
                    <div class="partner-filter-info">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle partner-filter-icon"></i>
                            <div>
                                <strong>Partner View Active:</strong> You are viewing only the activities you have created.
                                <br><small>As an approved partner, you can only manage your own UAE activities. Logged in as: <strong>{{ strtolower($user->name) }}</strong></small>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Statistics Info -->
                    @if($totalFilteredCount > 0)
                    <div class="stats-info">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-item">
                                    <div class="stats-number">{{ $activities->total() }}</div>
                                    <div class="stats-label">
                                        @if($isApprovedPartner)
                                            Your Activities
                                        @else
                                            Total Activities
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-item">
                                    <div class="stats-number">{{ $activities->currentPage() }}</div>
                                    <div class="stats-label">Current Page</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-item">
                                    <div class="stats-number">{{ $activities->lastPage() }}</div>
                                    <div class="stats-label">Total Pages</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Mobile Cards View (Hidden on Desktop) -->
                    <div class="d-block d-lg-none">
                        @forelse($activities as $index => $activity)
                            <div class="card border-gold mb-3 mobile-card">
                                <div class="card-body p-3">
                                    <!-- Mobile Card Header -->
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="flex-grow-1">
                                            <h6 class="card-title activity-name mb-1">
                                                {{ $activity->activityName ?? 'Untitled Activity' }}
                                            </h6>
                                            <p class="card-text text-light-muted mb-2 fs-7">
                                                <i class="fas fa-location-dot me-1" style="color: var(--primary-gold);"></i>
                                                {{ $activity->activityLocation ?? 'Location not specified' }}
                                            </p>
                                            <!-- Emirates info for mobile -->
                                            @if($activity->emirate)
                                            <p class="card-text text-light-muted mb-2 fs-7">
                                                <i class="fas fa-flag me-1" style="color: var(--secondary-gold);"></i>
                                                {{ $activity->emirate->emiratesName }}
                                            </p>
                                            @endif
                                            <!-- Created By info for mobile -->
                                            <p class="card-text mb-2 fs-7">
                                                <i class="fas fa-user creator-icon"></i>
                                                <span class="creator-info {{ strtolower($activity->createdBy ?? '') === strtolower($user->name) ? 'creator-match' : 'creator-other' }}">
                                                    Created by: {{ $activity->createdBy ?? 'Unknown' }}
                                                    @if(strtolower($activity->createdBy ?? '') === strtolower($user->name))
                                                        <i class="fas fa-check-circle ms-1"></i>
                                                    @endif
                                                </span>
                                            </p>
                                            @if($activity->activityPrice)
                                            <small class="text-muted">
                                                <i class="fas fa-dollar-sign"></i> 
                                                {{ number_format($activity->activityPrice, 2) }} AED
                                            </small>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Mobile Image Preview -->
                                    <div class="mb-3 text-center">
                                        @php
                                            $firstImage = null;
                                            if ($activity->details && $activity->details->activityImage) {
                                                $images = explode('#cseparator', $activity->details->activityImage);
                                                $firstImage = trim($images[0] ?? null);
                                            } elseif ($activity->activityImage) {
                                                $firstImage = trim($activity->activityImage);
                                            }
                                        @endphp
                                        
                                        @if($firstImage)
                                            <img src="{{ asset($firstImage) }}" 
                                                 alt="Activity Image" 
                                                 class="img-fluid rounded border border-gold mobile-activity-img"
                                                 onerror="this.parentElement.innerHTML='<div class=\'no-image-placeholder\'><i class=\'fas fa-image fa-2x\'></i><br>Image Error</div>'">
                                        @else
                                            <div class="no-image-placeholder">
                                                <i class="fas fa-image fa-2x"></i>
                                                <div>No Image Available</div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Mobile Action Buttons -->
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.uaeactivities.edit', $activity) }}" 
                                           class="btn btn-warning-custom btn-sm flex-fill">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger-custom btn-sm flex-fill delete-btn"
                                                data-activity-name="{{ $activity->activityName ?? 'this activity' }}"
                                                data-activity-id="{{ $activity->id }}">
                                            <i class="fas fa-trash me-1"></i>Delete
                                        </button>
                                    </div>

                                    <!-- Hidden form for mobile delete -->
                                    <form method="POST" 
                                          action="{{ route('admin.uaeactivities.destroy', $activity) }}" 
                                          class="d-none delete-form">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </div>
                        @empty
                        <div class="empty-state-mobile">
                            <div class="empty-state-icon">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <h4 class="empty-state-title">
                                @if($isApprovedPartner)
                                    No Activities Found
                                @else
                                    No UAE Activities Found
                                @endif
                            </h4>
                            <p class="empty-state-text">
                                @if($isApprovedPartner)
                                    You haven't created any activities yet. Start by adding your first UAE activity to get started!
                                @else
                                    You haven't created any activities yet. Start by adding your first UAE activity to get started!
                                @endif
                            </p>
                            <a href="{{ route('admin.uaeactivities.create') }}" class="btn btn-gold btn-lg">
                                <i class="fas fa-plus me-2"></i>Create Your First Activity
                            </a>
                        </div>
                        @endforelse
                    </div>

                    <!-- Desktop Table View (Hidden on Mobile) -->
                    @if($totalFilteredCount > 0)
                    <div class="d-none d-lg-block">
                        <div class="table-responsive">
                            <table class="table table-dark-gold">
                                <thead>
                                    <tr>
                                        <th style="width: 5%;">
                                            <i class="fas fa-hashtag"></i> S.No
                                        </th>
                                        <th style="width: 22%;">
                                            <i class="fas fa-tag"></i> Activity Details
                                        </th>
                                        <th style="width: 15%;">
                                            <i class="fas fa-map-marker-alt"></i> Location
                                        </th>
                                        <th style="width: 12%;">
                                            <i class="fas fa-flag"></i> Emirates
                                        </th>
                                        <th style="width: 15%;">
                                            <i class="fas fa-user"></i> Created By
                                        </th>
                                        <th style="width: 16%;">
                                            <i class="fas fa-image"></i> Image
                                        </th>
                                        <th style="width: 15%;">
                                            <i class="fas fa-cogs"></i> Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activities as $index => $activity)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $activities->firstItem() + $index }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="activity-name">
                                                    {{ $activity->activityName ?? 'Untitled Activity' }}
                                                </div>
                                                @if($activity->activityPrice)
                                                <small class="text-muted">
                                                    <i class="fas fa-dollar-sign"></i> 
                                                    {{ number_format($activity->activityPrice, 2) }} AED
                                                </small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="location-text">
                                                    <i class="fas fa-location-dot location-icon"></i>
                                                    {{ $activity->activityLocation ?? 'Location not specified' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="emirate-text">
                                                    <i class="fas fa-flag emirate-icon"></i>
                                                    {{ $activity->emirate->emiratesName ?? 'Not Assigned' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="creator-info {{ strtolower($activity->createdBy ?? '') === strtolower($user->name) ? 'creator-match' : 'creator-other' }}">
                                                    <i class="fas fa-user creator-icon"></i>
                                                    {{ $activity->createdBy ?? 'Unknown' }}
                                                    @if(strtolower($activity->createdBy ?? '') === strtolower($user->name))
                                                        <i class="fas fa-check-circle ms-1" title="Your Activity"></i>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $firstImage = null;
                                                    if ($activity->details && $activity->details->activityImage) {
                                                        $images = explode('#cseparator', $activity->details->activityImage);
                                                        $firstImage = trim($images[0] ?? null);
                                                    } elseif ($activity->activityImage) {
                                                        $firstImage = trim($activity->activityImage);
                                                    }
                                                @endphp
                                                
                                                @if($firstImage)
                                                    <img src="{{ asset($firstImage) }}" 
                                                         alt="Activity Image" 
                                                         class="img-preview" 
                                                         style="width: 80px; height: 60px; object-fit: cover;"
                                                         onerror="this.parentElement.innerHTML='<div class=\'no-image-placeholder\'><i class=\'fas fa-image\'></i><br>Image Error</div>'">
                                                @else
                                                    <div class="no-image-placeholder">
                                                        <i class="fas fa-image fa-2x"></i>
                                                        <div>No Image</div>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="{{ route('admin.uaeactivities.edit', $activity) }}" 
                                                       class="btn btn-edit btn-action d-inline-flex align-items-center justify-content-center"
                                                       title="Edit Activity">
                                                        <i class="fas fa-edit me-1"></i>
                                                        <span class="d-none d-xl-inline">Edit</span>
                                                    </a>
                                                    <form method="POST" 
                                                          action="{{ route('admin.uaeactivities.destroy', $activity) }}" 
                                                          class="d-inline delete-form"
                                                          data-activity-name="{{ $activity->activityName ?? 'this activity' }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" 
                                                                class="btn btn-delete btn-action delete-btn"
                                                                title="Delete Activity"
                                                                data-activity-name="{{ $activity->activityName ?? 'this activity' }}"
                                                                data-activity-id="{{ $activity->id }}">
                                                            <i class="fas fa-trash"></i>
                                                            <span class="d-none d-xl-inline">Delete</span>
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

                    <!-- Custom Pagination -->
                    @if($activities->hasPages())
                    <div class="custom-pagination">
                        <div class="pagination-info">
                            @if($isApprovedPartner)
                                Showing your activities {{ $activities->firstItem() }} to {{ $activities->lastItem() }} 
                                of {{ $activities->total() }} results
                            @else
                                Showing {{ $activities->firstItem() }} to {{ $activities->lastItem() }} 
                                of {{ $activities->total() }} results
                            @endif
                        </div>
                        
                        <div class="pagination-nav">
                            {{-- First Page --}}
                            @if($activities->currentPage() > 1)
                                <a href="{{ $activities->url(1) }}" class="page-btn">1</a>
                            @endif
                            
                            {{-- Previous Pages --}}
                            @if($activities->currentPage() > 3)
                                <span class="page-btn disabled">...</span>
                            @endif
                            
                            @for($i = max(2, $activities->currentPage() - 1); $i <= min($activities->lastPage() - 1, $activities->currentPage() + 1); $i++)
                                @if($i == $activities->currentPage())
                                    <span class="page-btn active">{{ $i }}</span>
                                @else
                                    <a href="{{ $activities->url($i) }}" class="page-btn">{{ $i }}</a>
                                @endif
                            @endfor
                            
                            {{-- Next Pages --}}
                            @if($activities->currentPage() < $activities->lastPage() - 2)
                                <span class="page-btn disabled">...</span>
                            @endif
                            
                            {{-- Last Page --}}
                            @if($activities->currentPage() < $activities->lastPage() && $activities->lastPage() > 1)
                                <a href="{{ $activities->url($activities->lastPage()) }}" class="page-btn">{{ $activities->lastPage() }}</a>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($totalFilteredCount == 0)
                    <!-- Empty State for Desktop -->
                    <div class="d-none d-lg-block">
                        <div class="empty-state-desktop">
                            <div class="empty-state-icon">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <h4 class="empty-state-title">
                                @if($isApprovedPartner)
                                    No Activities Found
                                @else
                                    No UAE Activities Found
                                @endif
                            </h4>
                            <p class="empty-state-text">
                                @if($isApprovedPartner)
                                    You haven't created any activities yet. Start by adding your first UAE activity to get started!<br>
                                    <small class="text-warning">Make sure you're creating activities with your login name: <strong>{{ strtolower($user->name) }}</strong></small>
                                @else
                                    You haven't created any activities yet. Start by adding your first UAE activity to get started!
                                @endif
                            </p>
                            <a href="{{ route('admin.uaeactivities.create') }}" class="btn btn-gold btn-lg">
                                <i class="fas fa-plus me-2"></i>Create Your First Activity
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Delete Confirmation Modal -->
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
                <i class="fas fa-trash-alt"></i>
            </div>
            <div class="delete-modal-text">
                Are you sure you want to delete this activity?
            </div>
            <div class="delete-modal-activity" id="activityToDelete">
                Activity Name Will Appear Here
            </div>
            <div class="delete-modal-subtext">
                This action cannot be undone. The activity and all its details will be permanently removed from the system.
            </div>
        </div>
        <div class="delete-modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="modal-btn modal-btn-delete" id="confirmDeleteBtn">
                <i class="fas fa-trash"></i> Delete Activity
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let deleteForm = null;
    
    // Handle delete button clicks
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            e.preventDefault();
            
            const deleteBtn = e.target.closest('.delete-btn');
            const activityName = deleteBtn.getAttribute('data-activity-name') || 'this activity';
            const form = deleteBtn.closest('.mobile-card')?.querySelector('.delete-form') || 
                        deleteBtn.closest('form.delete-form');
            
            if (form) {
                showDeleteModal(activityName, form);
            } else {
                console.error('Delete form not found');
            }
        }
    });
    
    function showDeleteModal(activityName, form) {
        deleteForm = form;
        document.getElementById('activityToDelete').textContent = activityName;
        document.getElementById('deleteModal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    window.closeDeleteModal = function() {
        document.getElementById('deleteModal').classList.remove('show');
        document.body.style.overflow = 'auto';
        deleteForm = null;
    }
    
    // Handle confirm delete
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (deleteForm) {
            const btn = this;
            btn.classList.add('processing');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            btn.disabled = true;
            
            // Submit the form
            deleteForm.submit();
        }
    });
    
    // Close modal on outside click
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('deleteModal').classList.contains('show')) {
            closeDeleteModal();
        }
    });
    
    // Auto-hide success alerts after 5 seconds
    const successAlert = document.querySelector('.alert-gold');
    if (successAlert) {
        setTimeout(function() {
            successAlert.classList.remove('show');
            setTimeout(function() {
                successAlert.remove();
            }, 150);
        }, 5000);
    }
});
</script>

@endsection
