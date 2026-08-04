{{-- Storefront visibility state for one activity. Paired with _visibility-toggle. --}}
@if($activity->isVisible)
    <span class="vis-pill vis-pill-live"><i class="fas fa-circle"></i> Live</span>
@else
    <span class="vis-pill vis-pill-hidden"><i class="fas fa-eye-slash"></i> Hidden</span>
@endif
