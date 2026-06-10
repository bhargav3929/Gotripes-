@extends('layouts.manager')

@section('title', 'Edit Agent')
@section('page-title', 'Edit Agent')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Edit Agent — {{ $agent->name }}</h1>
    <a href="{{ route('manager.agents.index') }}" class="wp-btn wp-btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Agents
    </a>
</div>

<form action="{{ route('manager.agents.update', $agent->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row g-3">
        <div class="col-12 col-lg-7">
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-user"></i> Agent Details</div>
                <div class="wp-card-body">
                    <div class="wp-form-group">
                        <label class="wp-form-label">Full Name <span class="required">*</span></label>
                        <input type="text" name="name" class="wp-input" value="{{ old('name', $agent->name) }}" required maxlength="255">
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label">Email (used to log in) <span class="required">*</span></label>
                        <input type="email" name="email" class="wp-input" value="{{ old('email', $agent->email) }}" required maxlength="255">
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label">Phone</label>
                        <input type="text" name="phone" class="wp-input" value="{{ old('phone', $agent->phone) }}" maxlength="30">
                    </div>

                    <div class="wp-form-group">
                        <label class="wp-form-label">New Password</label>
                        <div style="display: flex; gap: 8px;">
                            <input type="text" name="password" id="agentPassword" class="wp-input" value="" minlength="8" maxlength="72" autocomplete="new-password" placeholder="Leave blank to keep current password">
                            <button type="button" class="wp-btn wp-btn-secondary" style="white-space: nowrap;" onclick="generatePassword()">
                                <i class="fas fa-dice"></i> Generate
                            </button>
                        </div>
                        <p class="wp-form-help">Set a new password only if the agent lost theirs — then share it with them.</p>
                    </div>

                    <div class="wp-form-group" style="margin-bottom: 0;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $agent->is_active) ? 'checked' : '' }}>
                            <span>
                                <strong style="color: var(--wp-text);">Account active</strong>
                                <br><span style="font-size: 12px; color: var(--wp-text-muted);">Untick to block this agent from logging in. Their listings stay live.</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="wp-card">
                <div class="wp-card-header"><i class="fas fa-toggle-on"></i> Service Access</div>
                <div class="wp-card-body">
                    @php $granted = old('services', $agent->agent_services ?? []); @endphp

                    @forelse($services as $key => $label)
                        <label style="display: flex; align-items: flex-start; gap: 10px; padding: 12px; border: 1px solid var(--wp-border-light); border-radius: 4px; margin-bottom: 8px; cursor: pointer;">
                            <input type="checkbox" name="services[]" value="{{ $key }}"
                                   {{ in_array($key, $granted) ? 'checked' : '' }}
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
                            None of the agent-eligible services are enabled for your site.
                        </p>
                    @endforelse
                </div>
                <div class="wp-card-footer">
                    <button type="submit" class="wp-btn wp-btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
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
