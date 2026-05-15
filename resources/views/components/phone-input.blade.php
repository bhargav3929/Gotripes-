@props([
    'name'         => 'phone',
    'codeName'     => null,
    'numberName'   => null,
    'value'        => null,
    'defaultDial'  => '+971',
    'placeholder'  => '50 123 4567',
    'required'     => false,
    'id'           => null,
])

@php
    $codeName   = $codeName   ?? $name . '_country_code';
    $numberName = $numberName ?? $name . '_number';
    $id         = $id ?? $name;

    $countries = \App\Support\CountryCodes::all();

    // Determine selected dial + national number, honoring old() first, then stored value.
    [$splitDial, $splitNumber] = \App\Support\CountryCodes::split($value);

    $selectedDial = old($codeName, $splitDial !== '' ? $splitDial : $defaultDial);
    $nationalNum  = old($numberName, $splitNumber);
@endphp

<div class="input-group phone-input-group">
    <select name="{{ $codeName }}"
            class="form-select form-select-sm phone-country-select"
            aria-label="Country code"
            style="max-width: 130px; flex: 0 0 auto;">
        @foreach($countries as $c)
            <option value="{{ $c['dial'] }}"
                    data-iso="{{ $c['iso'] }}"
                    data-name="{{ $c['name'] }}"
                    @selected($selectedDial === $c['dial'])>
                {{ $c['flag'] }} {{ $c['dial'] }} &nbsp;{{ $c['name'] }}
            </option>
        @endforeach
    </select>
    <input type="tel"
           id="{{ $id }}"
           name="{{ $numberName }}"
           value="{{ $nationalNum }}"
           class="form-control form-control-sm"
           placeholder="{{ $placeholder }}"
           inputmode="tel"
           autocomplete="tel-national"
           @if($required) required @endif>
</div>

@once
    <style>
        .phone-input-group .phone-country-select {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            background-position: right 0.5rem center;
        }
        .phone-input-group .form-control {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }
        .phone-country-select option {
            color: #111;
            background: #fff;
        }
    </style>
@endonce
