@extends('layouts.admin')

@section('title', 'Manage Ad TVs')

@section('page-title', 'Manage Ad TVs')

@section('content')
<div class="container-fluid px-2 px-md-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gold border-bottom-0">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                        <h3 class="card-title mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="fas fa-tv me-2"></i>
                            <span>Hero Ad TVs</span>
                        </h3>
                        <a href="{{ route('admin.homepageads.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Add Media to TV
                        </a>
                    </div>
                </div>

                <div class="card-body p-2 p-sm-3 p-md-4">
                    @if(session('success'))
                        <div class="alert alert-success-custom alert-dismissible fade show mb-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ $errors->first() }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Stats -->
                    <div class="stats-info mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="stats-item">
                                    <div class="stats-number">{{ $usedSlots ?? 0 }}</div>
                                    <div class="stats-label">Active TVs</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-item">
                                    <div class="stats-number">{{ $totalMedia ?? 0 }}</div>
                                    <div class="stats-label">Total Media Items</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-item">
                                    <div class="stats-number">5</div>
                                    <div class="stats-label">Max TV Slots</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info-custom mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        Each TV is like an airport display - it cycles through its media (photos slide, videos play). Add multiple images/videos to each TV slot.
                    </div>

                    <!-- TV Slots -->
                    @for($tv = 1; $tv <= 5; $tv++)
                        @php $mediaItems = isset($adSlots[$tv]) ? $adSlots[$tv] : collect(); @endphp
                        <div class="tv-slot-card mb-4">
                            <div class="tv-slot-header">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="tv-number">{{ $tv }}</div>
                                    <div>
                                        <h5 class="mb-0 fw-bold text-white">TV {{ $tv }}</h5>
                                        <small class="text-muted-gold">
                                            {{ $mediaItems->count() }} media item{{ $mediaItems->count() !== 1 ? 's' : '' }}
                                            @if($mediaItems->count() > 0)
                                                &bull; Will cycle through all media
                                            @else
                                                &bull; Empty - no ads showing
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <a href="{{ route('admin.homepageads.create') }}?tv={{ $tv }}" class="btn btn-sm btn-outline-gold">
                                    <i class="fas fa-plus me-1"></i> Add Media
                                </a>
                            </div>

                            <div class="tv-slot-body">
                                @if($mediaItems->count() > 0)
                                    <div class="media-grid">
                                        @foreach($mediaItems as $media)
                                            <div class="media-card">
                                                <div class="media-preview">
                                                    @if($media->mediaType === 'video')
                                                        <video muted class="media-thumb">
                                                            <source src="{{ asset($media->imgPath) }}" type="video/mp4">
                                                        </video>
                                                        <div class="media-type-badge video"><i class="fas fa-video"></i></div>
                                                    @else
                                                        <img src="{{ asset($media->imgPath) }}" alt="Ad" class="media-thumb">
                                                        <div class="media-type-badge image"><i class="fas fa-image"></i></div>
                                                    @endif
                                                    <div class="media-order">#{{ $media->displayOrder }}</div>
                                                </div>
                                                <div class="media-info">
                                                    <span class="media-duration">
                                                        @if($media->mediaType === 'video')
                                                            <i class="fas fa-film"></i> Video
                                                        @else
                                                            <i class="fas fa-clock"></i> {{ $media->duration ?? 5 }}s
                                                        @endif
                                                    </span>
                                                    <div class="media-actions">
                                                        <a href="{{ route('admin.homepageads.edit', $media) }}" class="btn btn-xs btn-warning-custom" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-xs btn-danger-custom delete-btn"
                                                                data-image-id="{{ $media->id }}"
                                                                data-image-title="Media #{{ $media->displayOrder }} in TV {{ $tv }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="empty-tv text-center py-4">
                                        <i class="fas fa-photo-video text-gold" style="font-size: 2rem; opacity: 0.4;"></i>
                                        <p class="text-muted mt-2 mb-0">No media in this TV yet</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

<!-- Delete Modal -->
<div id="deleteModal" class="delete-modal">
    <div class="delete-modal-content">
        <div class="delete-modal-header">
            <h4 class="delete-modal-title">
                <i class="fas fa-exclamation-triangle"></i> Remove Media
            </h4>
            <button type="button" class="delete-modal-close" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="delete-modal-body">
            <div class="delete-modal-icon"><i class="fas fa-image"></i></div>
            <div class="delete-modal-text">Remove this media from the TV?</div>
            <div class="delete-modal-image" id="imageToDelete"></div>
            <div class="delete-modal-subtext">It will no longer appear in the rotation.</div>
        </div>
        <div class="delete-modal-footer">
            <button type="button" class="modal-btn modal-btn-cancel" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="modal-btn modal-btn-delete" id="confirmDeleteBtn">
                <i class="fas fa-trash"></i> Remove
            </button>
        </div>
    </div>
</div>

<style>
    .alert-success-custom {
        background: rgba(40, 167, 69, 0.2) !important;
        border: 1px solid rgba(40, 167, 69, 0.5) !important;
        color: #28a745 !important;
        border-radius: 8px;
    }

    .alert-info-custom {
        background: rgba(255, 215, 0, 0.08) !important;
        border: 1px solid rgba(255, 215, 0, 0.25) !important;
        color: #d4af37 !important;
        border-radius: 8px;
        font-size: 14px;
    }

    .stats-info {
        background: rgba(255, 215, 0, 0.1);
        border: 1px solid #ffd700;
        border-radius: 8px;
        padding: 15px;
    }

    .stats-item { text-align: center; color: #ffd700; }
    .stats-number { font-size: 24px; font-weight: bold; color: #ffd700; }
    .stats-label { font-size: 12px; color: #aaa; text-transform: uppercase; letter-spacing: 1px; }

    /* TV Slot Card */
    .tv-slot-card {
        background: rgba(30, 30, 30, 0.95);
        border: 1px solid rgba(255, 215, 0, 0.2);
        border-radius: 12px;
        overflow: hidden;
    }

    .tv-slot-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        background: rgba(255, 215, 0, 0.05);
        border-bottom: 1px solid rgba(255, 215, 0, 0.15);
    }

    .tv-number {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #FFD700, #D4AF37);
        color: #000;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 18px;
    }

    .text-muted-gold { color: #8a7a3a !important; font-size: 12px; }

    .btn-outline-gold {
        border: 1px solid #ffd700;
        color: #ffd700;
        background: transparent;
        font-size: 12px;
        padding: 6px 14px;
        border-radius: 6px;
    }
    .btn-outline-gold:hover {
        background: rgba(255, 215, 0, 0.15);
        color: #fff;
        border-color: #ffd700;
    }

    .tv-slot-body { padding: 16px 20px; }

    /* Media Grid */
    .media-grid {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .media-card {
        width: 150px;
        background: rgba(0, 0, 0, 0.4);
        border: 1px solid rgba(255, 215, 0, 0.15);
        border-radius: 8px;
        overflow: hidden;
    }

    .media-preview {
        position: relative;
        height: 90px;
        overflow: hidden;
        background: #111;
    }

    .media-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .media-type-badge {
        position: absolute;
        top: 6px;
        left: 6px;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
    }
    .media-type-badge.video { background: rgba(23, 162, 184, 0.9); color: #fff; }
    .media-type-badge.image { background: rgba(255, 193, 7, 0.9); color: #000; }

    .media-order {
        position: absolute;
        top: 6px;
        right: 6px;
        background: rgba(0, 0, 0, 0.7);
        color: #ffd700;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
    }

    .media-info {
        padding: 8px 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .media-duration {
        font-size: 11px;
        color: #888;
    }

    .media-actions { display: flex; gap: 4px; }

    .btn-xs {
        padding: 3px 8px !important;
        font-size: 11px !important;
        min-height: auto !important;
        line-height: 1.4;
    }

    .btn-warning-custom {
        background: linear-gradient(135deg, #ffc107, #fd7e14) !important;
        border: none !important;
        color: #212529 !important;
    }
    .btn-danger-custom {
        background: linear-gradient(135deg, #dc3545, #c82333) !important;
        border: none !important;
        color: #fff !important;
    }

    .empty-tv { color: #666; }
    .text-gold { color: #ffd700 !important; }

    /* Delete Modal */
    .delete-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0; top: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.8);
    }
    .delete-modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .delete-modal-content {
        background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
        border: 3px solid #ffd700;
        border-radius: 15px;
        width: 90%;
        max-width: 450px;
        overflow: hidden;
    }
    .delete-modal-header {
        background: linear-gradient(45deg, #dc3545, #ff4757);
        color: white;
        padding: 16px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .delete-modal-title { font-size: 18px; font-weight: 600; margin: 0; display: flex; align-items: center; gap: 8px; }
    .delete-modal-close {
        background: none; border: none; color: white; font-size: 20px; cursor: pointer;
        width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;
        border-radius: 50%; transition: all 0.3s;
    }
    .delete-modal-close:hover { background: rgba(255,255,255,0.2); }
    .delete-modal-body { padding: 24px 20px; text-align: center; }
    .delete-modal-icon { color: #ffd700; font-size: 3rem; margin-bottom: 16px; }
    .delete-modal-text { color: #fff; font-size: 16px; margin-bottom: 8px; font-weight: 600; }
    .delete-modal-image {
        background: rgba(255,215,0,0.1); border: 1px solid #ffd700; border-radius: 8px;
        padding: 12px; margin: 16px 0; color: #ffd700; font-weight: 600;
    }
    .delete-modal-subtext { color: #aaa; font-size: 13px; }
    .delete-modal-footer { display: flex; gap: 12px; padding: 0 20px 20px; }
    .modal-btn {
        flex: 1; padding: 10px; border: none; border-radius: 8px;
        font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.3s;
    }
    .modal-btn-cancel { background: linear-gradient(45deg, #6c757d, #868e96); color: white; }
    .modal-btn-delete { background: linear-gradient(45deg, #dc3545, #ff4757); color: white; }

    @media (max-width: 575px) {
        .media-card { width: 120px; }
        .media-preview { height: 70px; }
        .tv-slot-header { flex-direction: column; gap: 10px; align-items: flex-start; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var currentImageId = null;

    document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            currentImageId = this.getAttribute('data-image-id');
            document.getElementById('imageToDelete').textContent = this.getAttribute('data-image-title');
            document.getElementById('deleteModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        });
    });

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (currentImageId) {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Removing...';
            this.disabled = true;
            var form = document.getElementById('deleteForm');
            form.action = '{{ route("admin.homepageads.index") }}/' + currentImageId;
            setTimeout(function() { form.submit(); }, 300);
        }
    });

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeDeleteModal();
    });

    // Auto-hide success alert
    var alert = document.querySelector('.alert-success-custom');
    if (alert) {
        setTimeout(function() {
            alert.style.opacity = '0';
            setTimeout(function() { alert.remove(); }, 300);
        }, 5000);
    }
});

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
    document.body.style.overflow = '';
    var btn = document.getElementById('confirmDeleteBtn');
    btn.innerHTML = '<i class="fas fa-trash"></i> Remove';
    btn.disabled = false;
}
</script>
@endsection
