<tr data-row>
    <td><input class="wp-input" name="seasons[{{ $i }}][name]" value="{{ old("seasons.$i.name", $s?->name) }}" placeholder="e.g. New Year" aria-label="Season name"></td>
    <td><input type="date" class="wp-input" name="seasons[{{ $i }}][starts_on]" value="{{ old("seasons.$i.starts_on", $s?->starts_on?->toDateString()) }}" aria-label="From"></td>
    <td><input type="date" class="wp-input" name="seasons[{{ $i }}][ends_on]" value="{{ old("seasons.$i.ends_on", $s?->ends_on?->toDateString()) }}" aria-label="To"></td>
    <td><input type="number" step="0.01" min="0" class="wp-input" name="seasons[{{ $i }}][surcharge_percent]" value="{{ old("seasons.$i.surcharge_percent", $s?->surcharge_percent) }}" aria-label="Surcharge percent"></td>
    <td><button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" data-remove-row aria-label="Remove season"><i class="fas fa-trash"></i></button></td>
</tr>
