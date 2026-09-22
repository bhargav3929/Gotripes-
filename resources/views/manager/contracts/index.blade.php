@extends('layouts.manager')

@section('title', 'Agent Contract')
@section('page-title', 'Agent Contract')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Agent Contract</h1>
</div>

<style>
    .gt-card {
        border: 1px solid rgba(255, 215, 0, 0.35);
        border-radius: 10px;
        box-shadow: 0 0 0 1px rgba(255, 215, 0, 0.06), 0 0 40px rgba(255, 215, 0, 0.08), 0 16px 40px rgba(0, 0, 0, 0.45);
        overflow: hidden;
    }
    .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--wp-border-light); font-size: 13px; }
    .detail-row:last-child { border-bottom: none; }
    .detail-row span:first-child { color: var(--wp-text-muted); }
    .contract-status { display: flex; gap: 14px; align-items: flex-start; }
    .contract-status .dot { width: 10px; height: 10px; border-radius: 50%; margin-top: 6px; flex: 0 0 auto; }
    .dot-live { background: #35b37e; box-shadow: 0 0 0 4px rgba(53, 179, 126, .15); }
    .dot-off { background: #c9913a; box-shadow: 0 0 0 4px rgba(201, 145, 58, .15); }
    .source-toggle { display: flex; gap: 8px; margin-bottom: 18px; }
    .source-toggle button {
        flex: 1; padding: 12px 16px; border-radius: 8px; font-size: 13px; font-weight: 600;
        background: transparent; color: var(--wp-text-muted);
        border: 1px solid var(--wp-border-light); cursor: pointer; transition: all .18s ease;
    }
    .source-toggle button.is-on { border-color: rgba(255, 215, 0, .6); color: var(--wp-primary); background: rgba(255, 215, 0, .07); }
    .version-row td { vertical-align: middle; }
    .version-actions { display: flex; gap: 8px; justify-content: flex-end; }
</style>

{{-- session('success') is already printed by layouts.manager --}}
@if($errors->any())
    <div class="alert alert-danger">
        @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
@endif

<div class="row">
    <div class="col-lg-7">
        <div class="wp-card gt-card">
            <div class="wp-card-header"><i class="fas fa-file-signature text-secondary-wp"></i> What agents sign today</div>
            <div class="wp-card-body">
                @if($current)
                    <div class="contract-status">
                        <span class="dot dot-live"></span>
                        <div style="flex:1">
                            <strong>{{ $current->title }}</strong>
                            <span class="wp-badge wp-badge-amber" style="margin-left:6px;">v{{ $current->version }}</span>
                            <p class="wp-form-help mb-2">
                                Everyone who registers at <code>/agent/register</code> reads this and signs it
                                before their application reaches you. Signed copies are attached to each application.
                            </p>
                            <a href="{{ route('manager.contracts.show', $current->id) }}" target="_blank" class="wp-btn wp-btn-secondary">
                                <i class="fas fa-eye"></i> Read it
                            </a>
                            <form action="{{ route('manager.contracts.deactivate', $current->id) }}" method="POST" style="display:inline-block; margin-left:6px;">
                                @csrf
                                <button type="submit" class="wp-btn wp-btn-secondary"
                                        onclick="return confirm('Stop asking new applicants to sign? They will be able to register without an agreement.')">
                                    Stop asking for a signature
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="contract-status">
                        <span class="dot dot-off"></span>
                        <div>
                            <strong>No contract published</strong>
                            <p class="wp-form-help mb-0">
                                Agents can register without signing anything, and you can approve them as before.
                                Publish a version below to require a signature.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="wp-card gt-card">
            <div class="wp-card-header"><i class="fas fa-plus text-secondary-wp"></i> Publish a version</div>
            <div class="wp-card-body">
                <form action="{{ route('manager.contracts.store') }}" method="POST" enctype="multipart/form-data" id="contractForm">
                    @csrf
                    <div class="wp-form-group">
                        <label class="wp-form-label">Title <span class="required">*</span></label>
                        <input type="text" name="title" class="wp-input" required maxlength="200"
                               value="{{ old('title', 'GoTrips B2B Agent Agreement') }}">
                    </div>

                    <div class="source-toggle" role="tablist">
                        <button type="button" class="is-on" data-source="text" role="tab">Paste the text</button>
                        <button type="button" data-source="upload" role="tab">Upload a PDF</button>
                    </div>
                    <input type="hidden" name="source" id="contractSource" value="{{ old('source', 'text') }}">

                    <div class="wp-form-group" data-pane="text">
                        <label class="wp-form-label">Contract text</label>
                        <textarea name="body" class="wp-input" rows="14"
                                  placeholder="Paste the agreement here. It is shown to the applicant exactly as typed, and printed into the signed PDF with their signature underneath.">{{ old('body') }}</textarea>
                    </div>

                    <div class="wp-form-group" data-pane="upload" style="display:none;">
                        <label class="wp-form-label">Contract PDF</label>
                        <input type="file" name="file" class="wp-input" accept="application/pdf">
                        <p class="wp-form-help">
                            PDF, up to 10 MB. The applicant opens this file, and their signature is stored as a
                            certificate naming this version and the file's fingerprint.
                        </p>
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label" style="font-weight:400;">
                            <input type="checkbox" name="make_current" value="1" checked>
                            Make this the contract agents sign
                        </label>
                    </div>

                    <button type="submit" class="wp-btn wp-btn-primary">
                        <i class="fas fa-check"></i> Publish version {{ ($versions->max('version') ?? 0) + 1 }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="wp-card gt-card">
            <div class="wp-card-header"><i class="fas fa-layer-group text-secondary-wp"></i> Versions</div>
            <div class="wp-card-body">
                @forelse($versions as $version)
                    <div class="detail-row version-row" style="display:block;">
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
                            <div>
                                <strong>v{{ $version->version }}</strong> — {{ $version->title }}
                                @if($version->is_current)
                                    <span class="wp-badge wp-badge-green" style="margin-left:4px;">In use</span>
                                @endif
                                <div class="wp-form-help" style="margin:4px 0 0;">
                                    {{ $version->isUpload() ? 'PDF · ' . $version->file_name : 'Text' }}
                                    · {{ $version->created_at->format('d M Y') }}
                                    · {{ optional($version->author)->name ?? 'Unknown' }}
                                    · {{ $version->signatureCount() }} signature(s)
                                </div>
                            </div>
                            <div class="version-actions">
                                <a href="{{ route('manager.contracts.show', $version->id) }}" target="_blank" class="wp-btn wp-btn-secondary">View</a>
                                @if(!$version->is_current)
                                    <form action="{{ route('manager.contracts.activate', $version->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="wp-btn wp-btn-secondary">Use this</button>
                                    </form>
                                @endif
                                @if($version->signatureCount() === 0)
                                    <form action="{{ route('manager.contracts.destroy', $version->id) }}" method="POST"
                                          onsubmit="return confirm('Delete version {{ $version->version }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="wp-btn wp-btn-danger">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="wp-form-help mb-0">Nothing published yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    // Two ways to publish the same thing: paste the wording, or upload the
    // signed-off PDF from the lawyers. Only one pane is submitted.
    (function () {
        const hidden = document.getElementById('contractSource');
        const buttons = document.querySelectorAll('.source-toggle button');
        const panes = document.querySelectorAll('[data-pane]');

        function select(source) {
            hidden.value = source;
            buttons.forEach(b => b.classList.toggle('is-on', b.dataset.source === source));
            panes.forEach(p => { p.style.display = p.dataset.pane === source ? '' : 'none'; });
        }

        buttons.forEach(b => b.addEventListener('click', () => select(b.dataset.source)));
        select(hidden.value || 'text');
    })();
</script>
@endsection
