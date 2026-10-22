<tr data-row>
    <td><select class="wp-select" name="addons[{{ $i }}][category]" data-addon-cat aria-label="Option type">
        @foreach(\App\Models\TourPackageAddon::CATEGORIES as $k => $label)<option value="{{ $k }}" @selected(old("addons.$i.category", $a?->category) === $k)>{{ $label }}</option>@endforeach
    </select></td>
    <td><input class="wp-input" name="addons[{{ $i }}][label]" value="{{ old("addons.$i.label", $a?->label) }}" placeholder="e.g. Arabic-speaking guide" aria-label="Option name"></td>
    <td><select class="wp-select" name="addons[{{ $i }}][code]" data-meal-code @disabled(old("addons.$i.category", $a?->category) !== 'meal_plan') aria-label="Meal plan">
        @foreach(\App\Models\TourPackageHotel::MEAL_PLANS as $k => $label)<option value="{{ $k }}" @selected(old("addons.$i.code", $a?->code) === $k)>{{ $label }}</option>@endforeach
    </select></td>
    <td><input type="number" step="0.01" min="0" class="wp-input" name="addons[{{ $i }}][net_price]" value="{{ old("addons.$i.net_price", $a?->net_price ?? 0) }}" aria-label="Net price"></td>
    <td><select class="wp-select" name="addons[{{ $i }}][charge_basis]" aria-label="Charged">
        @foreach(\App\Models\TourPackageAddon::BASES as $k => $label)<option value="{{ $k }}" @selected(old("addons.$i.charge_basis", $a?->charge_basis ?? 'per_booking') === $k)>{{ $label }}</option>@endforeach
    </select></td>
    <td><input class="wp-input" name="addons[{{ $i }}][description]" value="{{ old("addons.$i.description", $a?->description) }}" aria-label="Note for customers"></td>
    <td><button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" data-remove-row aria-label="Remove option"><i class="fas fa-trash"></i></button></td>
</tr>
