{{--
  Shared partial: Country selector + conditional Emirate / Location fields
  Requires: $countries (config('countries')), $emirates (Emirates collection)
  Optional: $activity (for edit mode — pre-fills current values)
--}}
@php
$currentCountry  = old('country', $activity->country ?? ($defaultCountry ?? 'United Arab Emirates'));
$currentEmirate  = old('emiratesID', $activity->emiratesID ?? '');
$currentLocation = old('activityLocation', $activity->activityLocation ?? '');
$currentCurrency = old('activityCurrency', $activity->activityCurrency ?? '');
$isUAE           = strtolower(trim($currentCountry)) === 'united arab emirates';
@endphp

{{-- Country --}}
<div class="wp-form-group">
    <label class="wp-form-label">Country <span class="required">*</span></label>
    <select class="wp-select" name="country" id="activityCountry" required onchange="onActivityCountryChange(this)">
        @foreach($countries as $cName => $cData)
            <option value="{{ $cName }}"
                data-currency="{{ $cData['currency'] }}"
                {{ $currentCountry === $cName ? 'selected' : '' }}>
                {{ $cData['flag'] }} {{ $cName }}
            </option>
        @endforeach
    </select>
    <p class="wp-form-help">Activities are grouped by country on the public site.</p>
</div>

{{-- Emirate row — UAE only --}}
<div id="emirateRow" style="{{ $isUAE ? '' : 'display:none' }}">
    <div class="wp-form-group">
        <label class="wp-form-label">Emirate <span class="required">*</span></label>
        <select class="wp-select" name="emiratesID" id="emiratesID" {{ $isUAE ? 'required' : '' }}>
            <option value="">Select Emirate</option>
            @foreach($emirates as $emirate)
                <option value="{{ $emirate->emiratesID }}"
                    {{ $currentEmirate == $emirate->emiratesID ? 'selected' : '' }}>
                    {{ $emirate->emiratesName }}
                </option>
            @endforeach
        </select>
    </div>
</div>

{{-- Location (always visible, label changes for UAE vs other) --}}
<div class="wp-form-group">
    <label class="wp-form-label" id="locationLabel">
        {{ $isUAE ? 'Location' : 'City / Location' }} <span class="required">*</span>
    </label>
    <input type="text" class="wp-input" name="activityLocation" id="activityLocation"
           value="{{ $currentLocation }}" required
           placeholder="{{ $isUAE ? 'e.g. Dubai Marina' : 'e.g. Mumbai, Colaba' }}">
    <p class="wp-form-help" id="locationHelp">
        {{ $isUAE ? 'Specific area or address within the emirate.' : 'City and specific area or venue.' }}
    </p>
</div>

@once
@push('scripts')
<script>
var COUNTRY_CURRENCIES = @json(collect(config('countries'))->map(fn($d) => $d['currency'])->toArray());

function onActivityCountryChange(sel) {
    var country = sel.value;
    var isUAE   = (country === 'United Arab Emirates');

    // Toggle emirate row
    document.getElementById('emirateRow').style.display   = isUAE ? '' : 'none';
    document.getElementById('emiratesID').required         = isUAE;
    if (!isUAE) document.getElementById('emiratesID').value = '';

    // Location label + placeholder
    document.getElementById('locationLabel').innerHTML =
        (isUAE ? 'Location' : 'City / Location') + ' <span class="required">*</span>';
    document.getElementById('activityLocation').placeholder =
        isUAE ? 'e.g. Dubai Marina' : 'e.g. Mumbai, Colaba';
    document.getElementById('locationHelp').textContent =
        isUAE ? 'Specific area or address within the emirate.'
               : 'City and specific area or venue.';

    // Auto-fill currency only if the user hasn't changed it manually
    var currField = document.getElementById('activityCurrency');
    if (currField && !currField.dataset.manuallyChanged) {
        var suggested = COUNTRY_CURRENCIES[country] || 'USD';
        currField.value = suggested;
    }
}
</script>
@endpush
@endonce
