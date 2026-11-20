{{-- One peak period: dates and the % added to the hotel and tour price. --}}
<div class="row row-season">
    <div class="field"><label for="s{{ $i }}_name">Name</label><input type="text" id="s{{ $i }}_name" name="seasons[{{ $i }}][name]" value="{{ $x['name'] ?? '' }}" placeholder="e.g. New Year" class="@error("seasons.$i.name") is-invalid @enderror"></div>
    <div class="field"><label for="s{{ $i }}_from">From</label><input type="date" id="s{{ $i }}_from" name="seasons[{{ $i }}][from]" value="{{ $x['from'] ?? '' }}"></div>
    <div class="field"><label for="s{{ $i }}_to">To</label><input type="date" id="s{{ $i }}_to" name="seasons[{{ $i }}][to]" value="{{ $x['to'] ?? '' }}"></div>
    <div class="field"><label for="s{{ $i }}_pct">% extra</label><input type="number" inputmode="decimal" min="0" max="300" step="1" id="s{{ $i }}_pct" name="seasons[{{ $i }}][percent]" value="{{ $x['percent'] ?? '' }}" placeholder="e.g. 20"></div>
    <button type="button" class="rm" data-remove aria-label="Remove this peak season">×</button>
</div>
