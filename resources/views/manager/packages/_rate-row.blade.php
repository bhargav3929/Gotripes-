@php $margin = 1 + (float) ($package->margin_percent ?? 0) / 100; @endphp
<tr data-row>
    <td><input type="number" class="wp-input" name="rates[{{ $i }}][days]" min="1" max="60" value="{{ old("rates.$i.days", $r?->days) }}" aria-label="Days"></td>
    <td><select class="wp-select" name="rates[{{ $i }}][hotel_stars]" aria-label="Hotel stars">
        @foreach([3, 4, 5, 2, 1] as $s)<option value="{{ $s }}" @selected((int) old("rates.$i.hotel_stars", $r?->hotel_stars ?? 3) === $s)>{{ $s }}★</option>@endforeach
    </select></td>
    <td><input type="number" step="0.01" min="0" class="wp-input" name="rates[{{ $i }}][adult_net]" value="{{ old("rates.$i.adult_net", $r?->adult_net) }}" data-net-adult aria-label="Adult net"></td>
    <td><input type="number" step="0.01" min="0" class="wp-input" name="rates[{{ $i }}][child_net]" value="{{ old("rates.$i.child_net", $r?->child_net) }}" aria-label="Child net"></td>
    <td><input type="number" step="0.01" min="0" class="wp-input" name="rates[{{ $i }}][infant_net]" value="{{ old("rates.$i.infant_net", $r?->infant_net) }}" aria-label="Infant net"></td>
    <td><input type="number" step="0.01" min="0" class="wp-input" name="rates[{{ $i }}][single_supplement_net]" value="{{ old("rates.$i.single_supplement_net", $r?->single_supplement_net) }}" aria-label="Single supplement net"></td>
    <td data-sell style="white-space:nowrap; color:var(--wp-text-muted);">{{ $r ? 'AED ' . number_format($r->adult_net * $margin, 2) : '—' }}</td>
    <td><button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" data-remove-row aria-label="Remove row"><i class="fas fa-trash"></i></button></td>
</tr>
