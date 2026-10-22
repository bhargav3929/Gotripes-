{{-- Booking wizard settings. Shown when Package Type is "Booking wizard". $package may be null (create). --}}
@php $pk = $package ?? null; @endphp
<div class="wp-card" id="wizardSettings" @if(old('package_type', $pk?->package_type) !== 'wizard') hidden @endif>
    <div class="wp-card-header"><i class="fas fa-route text-secondary-wp"></i> Booking wizard pricing</div>
    <div class="wp-card-body">
        <div class="wp-form-group">
            <label class="wp-form-label" for="margin_percent">Your profit margin %</label>
            <input type="number" step="0.01" min="0" max="500" class="wp-input" id="margin_percent" name="margin_percent"
                   value="{{ old('margin_percent', $pk?->margin_percent ?? 15) }}">
            <p class="wp-form-help">Enter the supplier's <strong>net</strong> rates on the setup page; this margin is added to every line the customer pays. Customers never see net prices.</p>
        </div>
        <div class="wp-form-group" style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div>
                <label class="wp-form-label" for="child_max_age">Child rate up to age</label>
                <input type="number" min="1" max="17" class="wp-input" id="child_max_age" name="child_max_age" value="{{ old('child_max_age', $pk?->child_max_age ?? 11) }}">
            </div>
            <div>
                <label class="wp-form-label" for="infant_max_age">Infant rate up to age</label>
                <input type="number" min="0" max="5" class="wp-input" id="infant_max_age" name="infant_max_age" value="{{ old('infant_max_age', $pk?->infant_max_age ?? 1) }}">
            </div>
        </div>
        <p class="wp-form-help">Older children pay the adult rate.</p>
        <label class="wp-check" style="display:flex; gap:8px; align-items:flex-start; margin-top:8px;">
            <input type="hidden" name="insurance_required" value="0">
            <input type="checkbox" name="insurance_required" value="1" @checked(old('insurance_required', $pk?->insurance_required))>
            <span>Travel insurance is compulsory for this destination</span>
        </label>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var sel = document.querySelector('select[name=package_type]'), box = document.getElementById('wizardSettings');
        if (sel && box) sel.addEventListener('change', function () { box.hidden = sel.value !== 'wizard'; });
    });
</script>
