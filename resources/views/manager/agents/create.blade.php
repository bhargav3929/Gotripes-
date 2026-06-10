@extends('layouts.manager')

@section('title', 'Add Agent')
@section('page-title', 'Add Agent')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Add Agent</h1>
    <a href="{{ route('manager.agents.index') }}" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Agents
    </a>
</div>

<form action="{{ route('manager.agents.store') }}" method="POST">
    @csrf
    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-user"></i> Agent Details</div>
                <div class="wp-card-body">
                    <div class="wp-form-group">
                        <label class="wp-form-label">Full Name <span class="required">*</span></label>
                        <input type="text" name="name" class="wp-input" value="{{ old('name') }}" required maxlength="255" placeholder="e.g. Ravi Kumar">
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label">Email (used to log in) <span class="required">*</span></label>
                        <input type="email" name="email" class="wp-input" value="{{ old('email') }}" required maxlength="255" placeholder="agent@example.com">
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label">Phone</label>
                        <input type="text" name="phone" class="wp-input" value="{{ old('phone') }}" maxlength="30" placeholder="+91 ...">
                    </div>

                    <div class="wp-form-group" style="margin-bottom: 0;">
                        <label class="wp-form-label">Password <span class="required">*</span></label>
                        <div style="display: flex; gap: 8px;">
                            <input type="text" name="password" id="agentPassword" class="wp-input" value="{{ old('password') }}" required minlength="8" maxlength="72" autocomplete="new-password" placeholder="Min. 8 characters">
                            <button type="button" class="wp-btn wp-btn-secondary" style="white-space: nowrap;" onclick="generatePassword()">
                                <i class="fas fa-dice"></i> Generate
                            </button>
                        </div>
                        <p class="wp-form-help">You'll share this password with the agent. It is shown once more after saving.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-toggle-on"></i> Service Access</div>
                <div class="wp-card-body">
                    <p class="wp-form-help" style="margin: 0 0 12px;">
                        Tick the services this agent can manage in their portal.
                    </p>

                    @forelse($services as $key => $label)
                        <label style="display: flex; align-items: flex-start; gap: 10px; padding: 12px; border: 1px solid var(--wp-border-light); border-radius: 4px; margin-bottom: 8px; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="{{ $key }}"
                                   {{ in_array($key, old('services', [])) ? 'checked' : '' }}
                                   style="margin-top: 3px;">
                            <span>
                                <strong style="color: var(--wp-text);">
                                    @if($key === 'tours') <i class="fas fa-suitcase-rolling" style="color: var(--wp-primary);"></i>
                                    @elseif($key === 'activities') <i class="fas fa-hiking" style="color: var(--wp-primary);"></i>
                                    @elseif($key === 'esim') <i class="fas fa-sim-card" style="color: var(--wp-primary);"></i>
                                    @endif
                                    {{ $label }}
                                </strong>
                                <br>
                                <span style="font-size: 12px; color: var(--wp-text-muted);">
                                    @if($key === 'tours') Agent can add and manage their own tour packages.
                                    @elseif($key === 'activities') Agent can add and manage their own activities.
                                    @elseif($key === 'esim') Agent can view eSIM orders for your site.
                                    @endif
                                </span>
                            </span>
                        </label>
                    @empty
                        <p style="color: var(--wp-text-muted); margin: 0;">
                            None of the agent-eligible services (Tour Packages, Activities, eSIM)
                            are enabled for your site. Enable them under Settings → Features first.
                        </p>
                    @endforelse
                </div>
                @if(count($services))
                <div class="wp-card-footer">
                    <button type="submit" class="wp-btn wp-btn-primary">
                        <i class="fas fa-user-plus"></i> Create Agent
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function generatePassword() {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
        let pwd = '';
        const buf = new Uint32Array(12);
        crypto.getRandomValues(buf);
        buf.forEach(n => pwd += chars[n % chars.length]);
        document.getElementById('agentPassword').value = pwd;
    }
</script>
@endpush
