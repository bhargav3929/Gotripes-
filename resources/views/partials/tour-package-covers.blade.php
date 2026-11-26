{{-- What the price covers and what it doesn't, from the package's Included / Not included lists. --}}
@php $in = $package->includedList(); $out = $package->excludedList(); @endphp
@if($in || $out)
    <div class="tpw-covers @if(!empty($compact)) is-compact @endif">
        @if($in)
            <div class="tpw-covers-box tpw-covers-in">
                <h3>Included</h3>
                <ul>@foreach($in as $l)<li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>{{ $l }}</span></li>@endforeach</ul>
            </div>
        @endif
        @if($out)
            <div class="tpw-covers-box tpw-covers-out">
                <h3>Not included</h3>
                <ul>@foreach($out as $l)<li><i class="bi bi-x-circle-fill" aria-hidden="true"></i><span>{{ $l }}</span></li>@endforeach</ul>
            </div>
        @endif
    </div>
@endif
