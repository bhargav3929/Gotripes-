@extends('layouts.manager')

@section('title', 'Global e-Visa')
@section('page-title', 'Global e-Visa')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Global e-Visa</h1>
</div>

<style>
    /* Same gold-glow depth treatment as the Create Package popup, so this
       page doesn't sit flat next to the rest of the redesigned admin area. */
    .gt-card {
        border: 1px solid rgba(255, 215, 0, 0.35);
        border-radius: 10px;
        box-shadow: 0 0 0 1px rgba(255, 215, 0, 0.06), 0 0 40px rgba(255, 215, 0, 0.08), 0 16px 40px rgba(0, 0, 0, 0.45);
        overflow: hidden;
    }
    .gt-card .wp-card-header {
        position: relative;
        display: flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(180deg, rgba(255, 215, 0, 0.06), transparent);
    }
    .gt-card .wp-card-header::after {
        content: '';
        position: absolute; left: 0; right: 0; bottom: -1px; height: 2px;
        background: linear-gradient(90deg, var(--wp-primary), rgba(255, 215, 0, 0));
    }
    .gt-card-header-icon {
        display: inline-flex; align-items: center; justify-content: center;
        width: 30px; height: 30px; border-radius: 50%; flex-shrink: 0;
        background: rgba(255, 215, 0, 0.12); color: var(--wp-primary); font-size: 13px;
    }
    .gt-card .wp-card-body { padding: 20px; }
    .gt-card .wp-card-footer,
    .gt-card .text-end { }

    .gt-intro {
        border: 1px solid var(--wp-border-light);
        border-left: 3px solid var(--wp-primary);
        border-radius: 4px;
        padding: 12px 16px;
        margin-bottom: 24px;
        color: var(--wp-text-secondary);
        font-size: 13px;
        line-height: 1.6;
    }

    .gt-checkbox {
        width: 18px; height: 18px;
        accent-color: var(--wp-primary);
        cursor: pointer;
    }

    .gt-agent-row-name {
        display: flex; align-items: center; gap: 10px;
    }
    .gt-agent-avatar {
        display: inline-flex; align-items: center; justify-content: center;
        width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
        background: rgba(255, 215, 0, 0.12); color: var(--wp-primary);
        font-size: 13px; font-weight: 700;
    }
</style>

<p class="gt-intro">
    Separate from UAE Visa package pricing on the <a href="{{ route('manager.visa-pricing.index') }}">Visa Services</a> page.
    This section governs the <code>/e-visa</code> storefront: its platform-wide markup, and each agent's commission on the e-Visas they sell.
</p>

<div class="row">
    <div class="col-lg-4">
        <div class="wp-card gt-card">
            <div class="wp-card-header">
                <span class="gt-card-header-icon"><i class="fas fa-globe"></i></span>
                Global e-Visa Markup
            </div>
            <div class="wp-card-body">
                <form action="{{ route('manager.visa-pricing.evisa-markup.update') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="wp-form-group">
                        <label class="wp-form-label">Profit Margin (%)</label>
                        <input type="number" class="wp-input" name="markup_percent" value="{{ old('markup_percent', $evisaMarkup ?? 15) }}" step="0.01" min="0" max="1000" required>
                        <p class="wp-form-help">Added on top of the supplier's net fee for every e-Visa on the <code>/e-visa</code> storefront. A $100 net visa at 15% sells for $115.</p>
                        <p class="wp-form-help" style="color:var(--wp-warning,#b8860b);">
                            <i class="fas fa-triangle-exclamation"></i>
                            <strong>Platform-wide.</strong> Unlike agent commissions, this applies to every site on GoTrips, not just yours.
                        </p>
                    </div>
                    <button type="submit" class="wp-btn wp-btn-secondary w-100">
                        <i class="fas fa-save"></i> Save e-Visa Markup
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="wp-card gt-card">
            <div class="wp-card-header">
                <span class="gt-card-header-icon"><i class="fas fa-user-tag"></i></span>
                Agent e-Visa Commissions
            </div>
            <div class="wp-card-body">
                @if($agents->isEmpty())
                    <p class="mb-0">
                        No agents yet. Create one from
                        <a href="{{ route('manager.agents.index') }}">Manager &rarr; Agents</a>,
                        then come back here to set their e-Visa commission.
                    </p>
                @else
                    <p class="wp-form-help" style="margin: 0 0 12px;">
                        Tick "Access" to let an agent sell e-Visas, and set the commission percentage they earn.
                    </p>
                    <form action="{{ route('manager.evisa-settings.agent-commissions.update') }}" method="POST">
                        @csrf @method('PUT')
                        <div class="table-responsive">
                            <table class="wp-table">
                                <thead>
                                    <tr>
                                        <th>Agent</th>
                                        <th style="width: 90px;">Access</th>
                                        <th style="width: 140px;">Commission (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($agents as $agent)
                                        <tr>
                                            <td>
                                                <div class="gt-agent-row-name">
                                                    <span class="gt-agent-avatar">{{ strtoupper(substr($agent->name, 0, 1)) }}</span>
                                                    <span>
                                                        <strong>{{ $agent->name }}</strong><br>
                                                        <span class="text-secondary-wp" style="font-size:12px;">{{ $agent->email }}</span>
                                                        @if(!$agent->is_active)
                                                            <span class="wp-badge wp-badge-red ms-1" style="font-size:10px;">Inactive</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="hidden" name="agents[{{ $agent->id }}][enabled]" value="0">
                                                <input type="checkbox" class="gt-checkbox" name="agents[{{ $agent->id }}][enabled]" value="1" {{ $agent->hasService('evisa') ? 'checked' : '' }}>
                                            </td>
                                            <td>
                                                <input type="number" class="wp-input" name="agents[{{ $agent->id }}][commission_percent]" value="{{ $agent->evisa_commission_percent }}" step="0.01" min="0" max="100" placeholder="e.g. 10">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <button type="submit" class="wp-btn wp-btn-primary">
                                <i class="fas fa-save me-1"></i> Save All
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
