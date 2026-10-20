@extends('layouts.superadmin')

@section('title', 'Developer Theme · ' . $company->name)

@push('styles')
<style>
    .theme-state { display:flex; align-items:center; gap:24px; flex-wrap:wrap; padding:24px; }
    .theme-state .dot { width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:8px; }
    .theme-state .dot.on { background: var(--success); box-shadow:0 0 0 4px rgba(34,197,94,.15); }
    .theme-state .dot.off { background: var(--text-muted); }
    .theme-state .big { font-size:1.15rem; font-weight:700; color:var(--text-white); }
    .theme-state .meta { color:var(--text-muted); font-size:.85rem; }
    .theme-state .meta code { color:var(--text-light); }
    .copy-field { display:flex; gap:8px; }
    .copy-field input { font-family: ui-monospace, Menlo, monospace; font-size:.8rem; }
    .step-list { margin:0; padding-left:18px; color:var(--text-muted); font-size:.85rem; line-height:1.7; }
    .step-list strong { color:var(--text-light); font-weight:600; }
    .history td { vertical-align: top; font-size:.85rem; }
    .history .msg { max-width:420px; color:var(--text-muted); white-space:normal; }
    .history code { font-size:.8rem; }
    .form-switch .form-check-input { width:2.6em; height:1.35em; cursor:pointer; }
    .form-switch .form-check-label { padding-left:8px; cursor:pointer; }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('superadmin.companies.show', $company) }}" class="btn btn-outline-secondary btn-sm" aria-label="Back to company">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="page-title mb-0">Developer Theme</h1>
            <small class="text-muted">{{ $company->name }} · {{ $company->subdomain }}.gotrips.ai</small>
        </div>
    </div>
</div>

{{-- Success / error flashes are shown by the layout; validation errors are not. --}}
@if($errors->any())
    <div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
@endif

{{-- Current state --}}
<div class="card mb-4">
    <div class="theme-state">
        <div>
            <div class="big"><span class="dot {{ $company->theme_enabled && $live ? 'on' : 'off' }}"></span>
                @if($company->theme_enabled && $live) Live on the tenant site
                @elseif($company->theme_enabled) Switched on, nothing deployed yet
                @else Switched off
                @endif
            </div>
            <div class="meta mt-1">
                @if($live)
                    Version #{{ $live->id }}
                    @if($live->shortSha())· commit <code>{{ $live->shortSha() }}</code>@endif
                    · {{ $live->file_count }} files · {{ number_format($live->total_bytes / 1024, 1) }} KB
                    · {{ $live->created_at->format('M d, Y H:i') }} by {{ $live->triggered_by ?? 'unknown' }}
                @elseif(!$company->theme_enabled)
                    The tenant site shows the standard look. Nothing from the theme repository is loaded.
                @else
                    Deploy from GitHub or upload a zip to publish the first version.
                @endif
            </div>
        </div>
        @if($company->subdomain)
            <a href="{{ request()->getScheme() }}://{{ $company->subdomain }}.{{ preg_replace('/^www\./', '', parse_url(config('app.url'), PHP_URL_HOST) ?: 'gotrips.ai') }}" target="_blank" rel="noopener" class="btn btn-outline-secondary btn-sm ms-auto">
                <i class="fas fa-external-link-alt me-1"></i>Open tenant site
            </a>
        @endif
    </div>
</div>

<div class="row g-4">
    {{-- Settings --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><i class="fas fa-sliders-h me-2"></i>Settings</div>
            <div class="card-body">
                <form method="POST" action="{{ route('superadmin.companies.theme.update', $company) }}">
                    @csrf @method('PUT')
                    <div class="form-check form-switch mb-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="theme_enabled" name="theme_enabled" value="1" @checked(old('theme_enabled', $company->theme_enabled))>
                        <label class="form-check-label" for="theme_enabled">
                            <strong>Developer theme enabled</strong>
                            <span class="d-block text-muted small">Off = the theme never loads and GitHub pushes are refused. Takes effect on the next page load.</span>
                        </label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="theme_repo">GitHub repository</label>
                        <input type="text" class="form-control form-control-sm" id="theme_repo" name="theme_repo" placeholder="gotrips-themes/{{ $company->slug }}" value="{{ old('theme_repo', $company->theme_repo) }}">
                        <small class="text-muted">As <code>owner/name</code>. Only this repository can deploy to this company.</small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="theme_branch">Branch that deploys</label>
                        <input type="text" class="form-control form-control-sm" id="theme_branch" name="theme_branch" value="{{ old('theme_branch', $company->theme_branch ?: 'main') }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i>Save settings</button>
                </form>
            </div>
        </div>
    </div>

    {{-- GitHub connection --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><i class="fab fa-github me-2"></i>GitHub webhook</div>
            <div class="card-body">
                @unless($hasToken)
                    <div class="alert alert-warning small py-2">
                        <code>TENANT_THEMES_GITHUB_TOKEN</code> is not set in <code>.env</code>. Public repositories still deploy; private ones will fail until it is set.
                    </div>
                @endunless
                <label class="form-label">Payload URL</label>
                <div class="copy-field mb-3">
                    <input type="text" class="form-control form-control-sm" value="{{ $webhookUrl }}" readonly id="wh-url">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-copy="wh-url">Copy</button>
                </div>

                <label class="form-label">Secret</label>
                @if($company->theme_webhook_secret)
                    <div class="copy-field mb-2">
                        <input type="password" class="form-control form-control-sm" value="{{ $company->theme_webhook_secret }}" readonly id="wh-secret" autocomplete="off">
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-reveal="wh-secret">Show</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-copy="wh-secret">Copy</button>
                    </div>
                @else
                    <p class="text-muted small mb-2">No secret yet. Generate one, then paste it into GitHub.</p>
                @endif
                <form method="POST" action="{{ route('superadmin.companies.theme.secret', $company) }}" class="mb-3"
                      @if($company->theme_webhook_secret) onsubmit="return confirm('Generate a new secret? The GitHub webhook stops working until you paste the new one there.')" @endif>
                    @csrf
                    <button type="submit" class="btn btn-outline-warning btn-sm"><i class="fas fa-key me-1"></i>{{ $company->theme_webhook_secret ? 'Rotate secret' : 'Generate secret' }}</button>
                </form>

                <ol class="step-list">
                    <li>In the repository on GitHub: <strong>Settings → Webhooks → Add webhook</strong>.</li>
                    <li>Payload URL and Secret from above. Content type <strong>application/json</strong>.</li>
                    <li>Events: <strong>Just the push event</strong>. Save. GitHub sends a ping; it should show a green tick.</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header"><i class="fas fa-rocket me-2"></i>Deploy</div>
            <div class="card-body">
                <div class="row g-4 align-items-end">
                    <div class="col-md-4">
                        <p class="mb-2"><strong>Deploy latest from GitHub</strong></p>
                        <p class="text-muted small">Publishes the newest commit on <code>{{ $company->theme_branch ?: 'main' }}</code> now, without waiting for a push.</p>
                        <form method="POST" action="{{ route('superadmin.companies.theme.deploy', $company) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm" @disabled(!$company->theme_repo)><i class="fab fa-github me-1"></i>Deploy latest</button>
                        </form>
                    </div>
                    <div class="col-md-5">
                        <p class="mb-2"><strong>Upload a theme zip</strong></p>
                        <p class="text-muted small">For a tenant without GitHub. Same checks as a push.</p>
                        <form method="POST" action="{{ route('superadmin.companies.theme.upload', $company) }}" enctype="multipart/form-data" class="d-flex gap-2">
                            @csrf
                            <input type="file" name="theme_zip" accept=".zip" class="form-control form-control-sm" required>
                            <button type="submit" class="btn btn-outline-primary btn-sm text-nowrap"><i class="fas fa-upload me-1"></i>Upload</button>
                        </form>
                    </div>
                    <div class="col-md-3">
                        <p class="mb-2"><strong class="text-danger">Wipe theme</strong></p>
                        <p class="text-muted small">Deletes every published file. The site returns to the standard look.</p>
                        <form method="POST" action="{{ route('superadmin.companies.theme.wipe', $company) }}" onsubmit="return confirm('Delete every published theme file for {{ addslashes($company->name) }}?')">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-trash me-1"></i>Wipe</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- History --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header"><i class="fas fa-history me-2"></i>History</div>
            <div class="table-responsive">
                <table class="table history mb-0">
                    <thead>
                        <tr><th>#</th><th>When</th><th>Source</th><th>Commit</th><th>Result</th><th>Details</th><th>By</th><th></th></tr>
                    </thead>
                    <tbody>
                    @forelse($deployments as $d)
                        <tr>
                            <td>{{ $d->id }}</td>
                            <td class="text-nowrap">{{ $d->created_at->format('M d, H:i') }}</td>
                            <td class="text-nowrap">{{ $d->sourceLabel() }}</td>
                            <td>@if($d->shortSha())<code>{{ $d->shortSha() }}</code>@else<span class="text-muted">—</span>@endif</td>
                            <td>
                                @if($live && $live->id === $d->id)
                                    <span class="badge bg-success">Live</span>
                                @elseif($d->isSuccess())
                                    <span class="badge bg-secondary">OK</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </td>
                            <td class="msg">{{ $d->message }}</td>
                            <td class="text-nowrap small">{{ $d->triggered_by }}</td>
                            <td class="text-end">
                                @if($d->isAvailable() && !($live && $live->id === $d->id))
                                    <form method="POST" action="{{ route('superadmin.companies.theme.rollback', [$company, $d]) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary btn-sm text-nowrap">Make live</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">
                            No deployments yet. Connect the repository and push, or upload a zip.
                        </td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @if($deployments->hasPages())
                <div class="card-body">{{ $deployments->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('[data-copy]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var input = document.getElementById(btn.dataset.copy);
        navigator.clipboard.writeText(input.value).then(function () {
            var t = btn.textContent; btn.textContent = 'Copied'; setTimeout(function () { btn.textContent = t; }, 1500);
        });
    });
});
document.querySelectorAll('[data-reveal]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var input = document.getElementById(btn.dataset.reveal);
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.textContent = show ? 'Hide' : 'Show';
    });
});
</script>
@endpush
