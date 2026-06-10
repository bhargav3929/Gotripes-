@extends('layouts.manager')

@section('title', 'Agents')
@section('page-title', 'Agents')

@section('content')
<div class="wp-page-header">
    <div>
        <h1 class="wp-page-title">Agents</h1>
        <p style="color: var(--wp-text-muted); margin-top: 4px; font-size: 13px;">
            Agent accounts can log in at <code style="color: var(--wp-primary);">{{ route('agent.login') }}</code>
            and manage listings for the services you grant them.
        </p>
    </div>
    <a href="{{ route('manager.agents.create') }}" class="wp-btn wp-btn-primary">
        <i class="fas fa-user-plus"></i> Add Agent
    </a>
</div>

{{-- One-time credentials banner after creating an agent. The password is
     never shown again — the manager copies and shares it now. --}}
@if(session('agent_credentials'))
    @php $cred = session('agent_credentials'); @endphp
    <div class="wp-card" style="border-color: var(--wp-primary);">
        <div class="wp-card-header">
            <i class="fas fa-key" style="color: var(--wp-primary);"></i>
            Share these login details with {{ $cred['name'] }}
        </div>
        <div class="wp-card-body">
            <p style="color: var(--wp-text-muted); font-size: 12px; margin-bottom: 12px;">
                <i class="fas fa-exclamation-triangle" style="color: var(--wp-warning);"></i>
                The password is shown only once. Copy it now — if it's lost, set a new one from the agent's Edit page.
            </p>
            <div id="agentCredentials" style="background: #1a1a1a; border: 1px solid var(--wp-border); border-radius: 4px; padding: 14px 16px; font-family: monospace; font-size: 13px; line-height: 2;">
                Login page: {{ $cred['url'] }}<br>
                Email: {{ $cred['email'] }}<br>
                Password: {{ $cred['password'] }}
            </div>
            <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" style="margin-top: 10px;"
                    onclick="navigator.clipboard.writeText(document.getElementById('agentCredentials').innerText).then(() => { this.innerHTML = '<i class=&quot;fas fa-check&quot;></i> Copied'; });">
                <i class="fas fa-copy"></i> Copy details
            </button>
        </div>
    </div>
@endif

<div class="wp-card">
    <div class="table-responsive">
        <table class="wp-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>Agent</th>
                    <th>Services</th>
                    <th style="width: 130px;">Listings</th>
                    <th style="width: 110px;">Status</th>
                    <th style="width: 130px;">Last Login</th>
                    <th style="width: 190px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agents as $index => $agent)
                <tr>
                    <td style="color: var(--wp-text-muted);">{{ $agents->firstItem() + $index }}</td>
                    <td>
                        <strong style="color: var(--wp-text);">{{ $agent->name }}</strong>
                        <br><span style="font-size: 12px; color: var(--wp-text-muted);">{{ $agent->email }}</span>
                        @if($agent->phone)
                            <br><span style="font-size: 12px; color: var(--wp-text-muted);"><i class="fas fa-phone" style="font-size: 10px;"></i> {{ $agent->phone }}</span>
                        @endif
                    </td>
                    <td>
                        @forelse($agent->agentServiceLabels() as $label)
                            <span class="wp-badge wp-badge-amber" style="margin: 2px 2px 2px 0;">{{ $label }}</span>
                        @empty
                            <span class="text-muted-wp" style="font-size: 12px;">No services</span>
                        @endforelse
                    </td>
                    <td style="font-size: 12px; color: var(--wp-text-secondary);">
                        @php
                            $pc = $packageCounts[$agent->id] ?? 0;
                            $ac = $activityCounts[$agent->id] ?? 0;
                        @endphp
                        @if($pc) <span title="Tour packages"><i class="fas fa-suitcase-rolling" style="color: var(--wp-primary);"></i> {{ $pc }}</span> @endif
                        @if($ac) <span title="Activities" style="margin-left: 6px;"><i class="fas fa-hiking" style="color: var(--wp-primary);"></i> {{ $ac }}</span> @endif
                        @if(!$pc && !$ac) <span class="text-muted-wp">—</span> @endif
                    </td>
                    <td>
                        @if($agent->is_active)
                            <span class="wp-badge wp-badge-green">Active</span>
                        @else
                            <span class="wp-badge wp-badge-red">Deactivated</span>
                        @endif
                    </td>
                    <td style="font-size: 12px; color: var(--wp-text-secondary);">
                        {{ $agent->last_login_at?->format('d M Y H:i') ?? 'Never' }}
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px;">
                            <a href="{{ route('manager.agents.edit', $agent->id) }}" class="wp-btn wp-btn-secondary wp-btn-sm">
                                <i class="fas fa-pen"></i> Edit
                            </a>
                            @if($agent->is_active)
                            <form action="{{ route('manager.agents.destroy', $agent->id) }}" method="POST"
                                  onsubmit="return confirm('Deactivate this agent? They will no longer be able to log in. Their listings stay live.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm">
                                    <i class="fas fa-ban"></i> Deactivate
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="7">
                        <div style="padding: 24px 0; text-align: center;">
                            <i class="fas fa-user-tie" style="font-size: 32px; color: var(--wp-border); margin-bottom: 8px; display: block;"></i>
                            No agents yet.
                            <a href="{{ route('manager.agents.create') }}" style="color: var(--wp-primary);">Add your first agent.</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($agents->hasPages())
        <div class="wp-pagination">
            {{ $agents->links() }}
        </div>
    @endif
</div>
@endsection
