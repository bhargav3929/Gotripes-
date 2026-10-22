<div class="bs-day" data-row>
    <input type="hidden" name="days[{{ $i }}][id]" value="{{ $d?->id }}">
    <div class="wp-form-group" style="margin:0"><label class="wp-form-label">Day</label><input type="number" min="1" max="60" class="wp-input" name="days[{{ $i }}][day_number]" value="{{ old("days.$i.day_number", $d?->day_number) }}"></div>
    <div class="wp-form-group" style="margin:0"><label class="wp-form-label">For trip length</label><input type="number" min="1" max="60" class="wp-input" name="days[{{ $i }}][duration_days]" value="{{ old("days.$i.duration_days", $d?->duration_days) }}" placeholder="any"></div>
    <div class="wp-form-group" style="margin:0"><label class="wp-form-label">Title</label><input class="wp-input" name="days[{{ $i }}][title]" value="{{ old("days.$i.title", $d?->title) }}" placeholder="e.g. Arrival in Tbilisi and old town walk"></div>
    <div class="wp-form-group bs-day-wide" style="margin:0"><label class="wp-form-label">What happens that day</label><textarea class="wp-textarea" rows="2" name="days[{{ $i }}][description]">{{ old("days.$i.description", $d?->description) }}</textarea></div>
    <div class="bs-day-wide" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
        @if($d?->image_path)
            <img src="{{ asset($d->image_path) }}" alt="">
            <label style="font-size:13px;"><input type="checkbox" name="days[{{ $i }}][remove_image]" value="1"> Remove picture</label>
        @endif
        <input type="file" class="wp-input" style="max-width:320px" name="days[{{ $i }}][image]" accept="image/jpeg,image/png,image/webp" aria-label="Picture for this day">
        <button type="button" class="wp-btn wp-btn-secondary wp-btn-sm" data-remove-row style="margin-left:auto"><i class="fas fa-trash"></i> Remove day</button>
    </div>
</div>
