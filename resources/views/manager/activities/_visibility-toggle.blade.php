{{-- Hide/show an activity on the public site without deleting it. --}}
<form action="{{ route('manager.activities.toggle-visibility', $activity->activityID) }}" method="POST"
      onsubmit="return confirm(@js($activity->isVisible
          ? 'Hide "'.$activity->activityName.'" from the website? Nothing is deleted — you can put it back any time.'
          : 'Show "'.$activity->activityName.'" on the website again?'));">
    @csrf
    <button type="submit"
            class="wp-btn wp-btn-sm {{ $activity->isVisible ? 'wp-btn-secondary' : 'wp-btn-primary' }}"
            title="{{ $activity->isVisible ? 'Hide from website' : 'Show on website' }}">
        <i class="fas {{ $activity->isVisible ? 'fa-eye-slash' : 'fa-eye' }}"></i>
    </button>
</form>
