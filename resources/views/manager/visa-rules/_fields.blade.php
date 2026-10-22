@php $p = $rule ? 'r' . $rule->id . '_' : 'new_'; @endphp
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:12px;">
    <div class="wp-form-group"><label class="wp-form-label" for="{{ $p }}destination">Destination</label>
        <select class="wp-select" id="{{ $p }}destination" name="destination" required>
            @foreach($destinations->isNotEmpty() ? $destinations->merge($countries)->unique() : $countries as $c)
                <option value="{{ $c }}" @selected(($rule?->destination ?? old('destination')) === $c)>{{ $c }}</option>
            @endforeach
        </select></div>
    <div class="wp-form-group"><label class="wp-form-label" for="{{ $p }}nationality">Passport</label>
        <select class="wp-select" id="{{ $p }}nationality" name="nationality" required>
            <option value="*" @selected(($rule?->nationality ?? old('nationality', '*')) === '*')>Everyone else</option>
            @foreach($countries as $c)<option value="{{ $c }}" @selected(($rule?->nationality ?? old('nationality')) === $c)>{{ $c }}</option>@endforeach
        </select></div>
    <div class="wp-form-group"><label class="wp-form-label" for="{{ $p }}requirement">Requirement</label>
        <select class="wp-select" id="{{ $p }}requirement" name="requirement" required>
            @foreach($requirements as $k => $label)<option value="{{ $k }}" @selected(($rule?->requirement ?? old('requirement')) === $k)>{{ $label }}</option>@endforeach
        </select></div>
    <div class="wp-form-group"><label class="wp-form-label" for="{{ $p }}max_stay_days">Max stay (days)</label>
        <input type="number" min="1" class="wp-input" id="{{ $p }}max_stay_days" name="max_stay_days" value="{{ $rule?->max_stay_days ?? old('max_stay_days') }}"></div>
    <div class="wp-form-group"><label class="wp-form-label" for="{{ $p }}processing_days">Processing (days)</label>
        <input type="number" min="0" class="wp-input" id="{{ $p }}processing_days" name="processing_days" value="{{ $rule?->processing_days ?? old('processing_days') }}"></div>
    <div class="wp-form-group"><label class="wp-form-label" for="{{ $p }}fee">Visa fee (AED, approx.)</label>
        <input type="number" step="0.01" min="0" class="wp-input" id="{{ $p }}fee" name="fee" value="{{ $rule?->fee ?? old('fee') }}"></div>
</div>
<div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
    <div class="wp-form-group"><label class="wp-form-label" for="{{ $p }}documents">Documents needed</label>
        <textarea class="wp-textarea" rows="2" id="{{ $p }}documents" name="documents" placeholder="e.g. Passport valid 6 months, return ticket, hotel booking">{{ $rule?->documents ?? old('documents') }}</textarea></div>
    <div class="wp-form-group"><label class="wp-form-label" for="{{ $p }}notes">Notes for the customer</label>
        <textarea class="wp-textarea" rows="2" id="{{ $p }}notes" name="notes">{{ $rule?->notes ?? old('notes') }}</textarea></div>
</div>
