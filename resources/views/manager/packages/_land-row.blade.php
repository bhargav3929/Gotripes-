{{-- One trip length in "Tours, transfers and guide": net price per adult for each group size band. --}}
<tr data-row>
    <td><input type="number" class="wp-input" name="land[{{ $i }}][days]" min="1" max="60" value="{{ old("land.$i.days", $d) }}" aria-label="Trip length in days" style="max-width:110px;"></td>
    @foreach(\App\Models\TourPackageLandRate::GROUPS as $min => $label)
        @php $v = $d ? $package->landRates->first(fn ($r) => $r->days === (int) $d && $r->min_group === $min)?->adult_net : null; @endphp
        <td><input type="number" step="0.01" min="0" class="wp-input" name="land[{{ $i }}][g{{ $min }}]" value="{{ old("land.$i.g$min", $v) }}" aria-label="{{ $label }} adults, per adult"></td>
    @endforeach
    <td><button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" data-remove-row aria-label="Remove trip length"><i class="fas fa-trash"></i></button></td>
</tr>
