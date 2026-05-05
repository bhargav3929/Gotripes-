{{--
    Reusable search/filter bar.

    Variables:
      $action       (string) form GET target — defaults to current request URL
      $placeholder  (string) text for the q= input
      $extra        (string|null) raw HTML for additional filter inputs (selects, etc.)
--}}
@php
    $action      ??= url()->current();
    $placeholder ??= 'Search...';
    $extra       ??= null;
@endphp
<form method="GET" action="{{ $action }}" class="orders-searchbar">
    <input type="text"
           name="q"
           value="{{ request('q') }}"
           placeholder="{{ $placeholder }}"
           class="orders-search-input">

    {!! $extra !!}

    <button type="submit" class="orders-btn orders-btn-primary">
        <i class="fas fa-search"></i> Search
    </button>

    @if(request()->hasAny(['q', 'date_from', 'date_to', 'status', 'type']))
        <a href="{{ url()->current() }}" class="orders-btn orders-btn-ghost">
            <i class="fas fa-times"></i> Clear
        </a>
    @endif
</form>
