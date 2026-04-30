@extends('layouts.referral')

@section('title', 'My Profile')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-12">
            <h5 class="mb-0">My Profile</h5>
            <small class="text-muted">Manage your account settings</small>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <!-- Profile Form -->
            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-user me-2 text-gold"></i>Personal Information</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('referral.profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control form-control-sm @error('name') is-invalid @enderror"
                                       value="{{ old('name', $agent->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control form-control-sm" value="{{ $agent->email }}" disabled>
                                <small class="text-muted">Cannot be changed</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control form-control-sm @error('phone') is-invalid @enderror"
                                       value="{{ old('phone', $agent->phone) }}" placeholder="+971 50 123 4567">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Country</label>
                                <input type="text" name="country" list="profile-country-options"
                                       class="form-control form-control-sm @error('country') is-invalid @enderror"
                                       value="{{ old('country', $agent->country) }}" placeholder="Start typing">
                                <datalist id="profile-country-options"></datalist>
                                @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <div>
                                    <span class="badge bg-{{ $agent->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($agent->status) }}</span>
                                </div>
                            </div>
                        </div>

                        <hr class="my-3 border-dark">

                        <div class="section-title"><i class="fas fa-lock me-2 text-gold"></i>Change Password</div>
                        <small class="text-muted d-block mb-2">Leave blank to keep current password</small>

                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Current</label>
                                <input type="password" name="current_password" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">New</label>
                                <input type="password" name="new_password" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Confirm</label>
                                <input type="password" name="new_password_confirmation" class="form-control form-control-sm">
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-gold btn-sm"><i class="fas fa-save me-1"></i>Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Account Summary -->
            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-chart-pie me-2 text-gold"></i>Account Summary</div>
                <div class="card-body text-center">
                    <div class="profile-avatar mb-2">{{ strtoupper(substr($agent->name, 0, 2)) }}</div>
                    <div class="fw-500">{{ $agent->name }}</div>
                    <small class="text-muted">{{ $agent->email }}</small>

                    <hr class="border-dark my-2">

                    <div class="info-row"><span>Member Since</span><span>{{ $agent->created_at->format('M Y') }}</span></div>
                    <div class="info-row"><span>Total Sales</span><span>{{ number_format($agent->total_sales) }}</span></div>
                    <div class="info-row"><span>Total Earnings</span><span class="text-success">AED {{ number_format($agent->total_earnings, 2) }}</span></div>
                </div>
            </div>

            <!-- Referral Link -->
            <div class="card">
                <div class="card-header"><i class="fas fa-link me-2 text-gold"></i>Your Referral Link</div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Code</small>
                        <div class="code-box">{{ $agent->referral_code }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">URL</small>
                        <div class="url-box">{{ $agent->referral_url }}</div>
                    </div>
                    <button class="btn btn-gold btn-sm w-100 copy-btn" data-copy="{{ $agent->referral_url }}">
                        <i class="fas fa-copy me-1"></i>Copy Link
                    </button>
                    <div class="text-center mt-2">
                        <small class="text-muted">Commission: </small>
                        <span class="text-gold">
                            @if($agent->commission_type === 'percentage')
                                {{ $agent->commission_value }}%
                            @else
                                AED {{ number_format($agent->commission_value, 2) }}
                            @endif
                            per order
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .container { max-width: 1000px; }
    .text-gold { color: var(--primary-gold) !important; }

    /* Card */
    .card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
    }
    .card-header {
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        padding: 10px 14px;
        font-size: 0.8rem;
        font-weight: 500;
        color: #fff;
    }
    .card-body { padding: 14px; }

    /* Form */
    .form-label {
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-bottom: 2px;
    }
    .form-control {
        background: var(--light-dark);
        border: 1px solid var(--border-color);
        color: var(--text-main);
        font-size: 0.8rem;
    }
    .form-control:focus {
        background: var(--light-dark);
        border-color: var(--primary-gold);
        box-shadow: none;
    }
    .form-control-sm { padding: 6px 10px; }
    .section-title { font-size: 0.8rem; font-weight: 500; color: #fff; margin-bottom: 4px; }

    /* Profile */
    .profile-avatar {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 700;
        color: #000;
        margin: 0 auto;
    }
    .fw-500 { font-weight: 500; color: #fff; }
    .info-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.75rem;
        padding: 4px 0;
    }
    .info-row span:first-child { color: var(--text-muted); }

    /* Code boxes */
    .code-box {
        background: rgba(255, 215, 0, 0.1);
        border: 1px solid var(--border-gold);
        border-radius: 6px;
        padding: 6px 10px;
        font-family: monospace;
        font-size: 0.85rem;
        color: var(--primary-gold);
        text-align: center;
    }
    .url-box {
        background: var(--light-dark);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 6px 10px;
        font-family: monospace;
        font-size: 0.65rem;
        color: var(--text-muted);
        word-break: break-all;
    }

    /* Buttons */
    .btn-gold {
        background: linear-gradient(135deg, var(--primary-gold), var(--secondary-gold));
        border: none;
        color: #000;
        font-weight: 600;
    }
    .btn-sm { padding: 6px 12px; font-size: 0.75rem; }

    /* Text */
    h5 { color: #fff !important; }
    small { font-size: 0.65rem; }
</style>

@push('scripts')
<script>
document.querySelectorAll('.copy-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        navigator.clipboard.writeText(this.dataset.copy).then(() => {
            const orig = this.innerHTML;
            this.innerHTML = '<i class="fas fa-check me-1"></i>Copied!';
            setTimeout(() => this.innerHTML = orig, 2000);
        });
    });
});

(function () {
    const datalist = document.getElementById('profile-country-options');
    if (!datalist) return;
    fetch('https://restcountries.com/v3.1/all?fields=name')
        .then(r => r.ok ? r.json() : Promise.reject())
        .then(data => {
            data.map(c => c.name?.common).filter(Boolean)
                .sort((a, b) => a.localeCompare(b))
                .forEach(n => {
                    const o = document.createElement('option');
                    o.value = n;
                    datalist.appendChild(o);
                });
        })
        .catch(() => {});
})();
</script>
@endpush
@endsection
