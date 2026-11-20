{{-- One trip length: price per person for the whole trip, optionally by group size. --}}
@php $cur = old('currency', data_get($prefill, 'currency', 'USD')); @endphp
<div class="row row-tour">
    <div class="field"><label for="t{{ $i }}_days">Days</label><input type="number" inputmode="numeric" min="1" max="60" id="t{{ $i }}_days" name="tours[{{ $i }}][days]" value="{{ $t['days'] ?? '' }}" placeholder="e.g. 4"></div>
    @foreach($groups as $g => $label)
        <div class="field" @if($g > 1) data-band @endif>
            <label for="t{{ $i }}_g{{ $g }}" @if($g === 1) data-first-band-label data-one-label="Price per person" data-group-label="1 person" @endif>{{ $g === 1 ? 'Price per person' : $label . ' people' }}</label>
            <div class="money"><span class="cur" data-cur>{{ $cur }}</span><input type="number" inputmode="decimal" step="0.01" min="0" id="t{{ $i }}_g{{ $g }}" name="tours[{{ $i }}][g{{ $g }}]" value="{{ $t['g' . $g] ?? '' }}"></div>
        </div>
    @endforeach
    <button type="button" class="rm" data-remove aria-label="Remove this trip length">×</button>
</div>
