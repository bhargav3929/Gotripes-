{{-- One extra a traveller can add. --}}
@php $cur = old('currency', data_get($prefill, 'currency', 'USD')); $cat = $e['category'] ?? 'guide_language'; @endphp
<div class="row row-extra">
    <div class="field"><label for="e{{ $i }}_cat">Type</label>
        <select id="e{{ $i }}_cat" name="extras[{{ $i }}][category]" data-extra-cat>
            @foreach($categories as $k => $l)<option value="{{ $k }}" @selected($cat === $k)>{{ $l }}</option>@endforeach
        </select>
        <div data-meal-wrap style="margin-top:8px;">
            <select name="extras[{{ $i }}][meal_code]" aria-label="Which meal plan" class="@error("extras.$i.meal_code") is-invalid @enderror">
                <option value="">Which meal plan?</option>
                @foreach($mealPlans as $k => $l)@if($k !== 'room_only')<option value="{{ $k }}" @selected(($e['meal_code'] ?? '') === $k)>{{ $l }}</option>@endif @endforeach
            </select>
        </div>
    </div>
    <div class="field"><label for="e{{ $i }}_name">Name</label><input type="text" id="e{{ $i }}_name" name="extras[{{ $i }}][name]" value="{{ $e['name'] ?? '' }}" placeholder="e.g. Arabic-speaking guide"></div>
    <div class="field"><label for="e{{ $i }}_price">Price</label><div class="money"><span class="cur" data-cur>{{ $cur }}</span><input type="number" inputmode="decimal" step="0.01" min="0" id="e{{ $i }}_price" name="extras[{{ $i }}][price]" value="{{ $e['price'] ?? '' }}" class="@error("extras.$i.price") is-invalid @enderror"></div></div>
    <div class="field"><label for="e{{ $i }}_basis">Charged</label>
        <select id="e{{ $i }}_basis" name="extras[{{ $i }}][basis]">
            @foreach($bases as $k => $l)<option value="{{ $k }}" @selected(($e['basis'] ?? 'per_day') === $k)>{{ ucfirst($l) }}</option>@endforeach
        </select>
    </div>
    <button type="button" class="rm" data-remove aria-label="Remove this extra">×</button>
</div>
