{{-- One day of the plan. --}}
<div class="row row-day">
    <div class="field"><label for="d{{ $i }}_day">Day</label><input type="number" inputmode="numeric" min="1" max="60" id="d{{ $i }}_day" name="days[{{ $i }}][day]" value="{{ $d['day'] ?? (is_int($i) ? $i + 1 : '') }}"></div>
    <div class="field"><label for="d{{ $i }}_title">Title</label><input type="text" id="d{{ $i }}_title" name="days[{{ $i }}][title]" value="{{ $d['title'] ?? '' }}" placeholder="e.g. Arrival in Tbilisi, old town walk"></div>
    <button type="button" class="rm" data-remove aria-label="Remove this day">×</button>
    <textarea name="days[{{ $i }}][description]" aria-label="What happens this day" placeholder="What happens this day (optional)" maxlength="2000">{{ $d['description'] ?? '' }}</textarea>
</div>
