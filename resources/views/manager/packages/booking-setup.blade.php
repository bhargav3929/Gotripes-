@extends('layouts.manager')

@section('title', 'Booking setup · ' . $package->title)
@section('page-title', 'Booking setup')

@push('styles')
<style>
    .bs-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(110px, 1fr)); gap:8px; }
    .bs-table input, .bs-table select { min-width:80px; }
    .bs-table td { vertical-align:middle; }
    .bs-table select[data-meal-code]:disabled { visibility:hidden; }
    .bs-table select[data-addon-cat] { min-width:170px; }
    .bs-table input[name$="[label]"] { min-width:230px; }
    .bs-hotel { border:1px solid var(--wp-border-light); border-radius:8px; padding:16px; margin-bottom:12px; }
    .bs-hotel h3 { font-size:15px; margin:0 0 12px; }
    .bs-meals { display:flex; flex-wrap:wrap; gap:12px; }
    .bs-meals label { display:flex; gap:6px; align-items:center; font-size:13px; }
    .bs-day { border:1px solid var(--wp-border-light); border-radius:8px; padding:12px; margin-bottom:10px; display:grid; grid-template-columns:90px 90px 1fr; gap:10px; }
    .bs-day .bs-day-wide { grid-column:1 / -1; }
    .bs-day img { max-height:80px; border-radius:6px; }
    .bs-steps { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:16px; }
    .bs-steps a { font-size:13px; }
    .bs-status { display:flex; gap:16px; flex-wrap:wrap; font-size:13px; }
    .bs-status span { display:flex; align-items:center; gap:6px; }
    @media (max-width: 767px) { .bs-day { grid-template-columns:1fr 1fr; } }
</style>
@endpush

@section('content')
@php
    $ready = [
        'Hotel prices' => $pricedHotels->isNotEmpty(),
        'Tour prices' => $package->landRates->isNotEmpty(),
        'Options' => $package->addons->isNotEmpty(),
        'Itinerary' => $package->itineraryDays->isNotEmpty(),
    ];
@endphp
<div class="wp-page-header">
    <div>
        <h1 class="wp-page-title">{{ $package->title }}</h1>
        <div class="bs-status">
            @foreach($ready as $label => $ok)
                <span><i class="fas {{ $ok ? 'fa-check-circle' : 'fa-circle' }}" style="color: {{ $ok ? 'var(--wp-success)' : 'var(--wp-text-muted)' }}"></i>{{ $label }}</span>
            @endforeach
            <span>Margin <strong>{{ rtrim(rtrim(number_format($package->margin_percent, 2), '0'), '.') }}%</strong></span>
        </div>
    </div>
    <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <a href="{{ route('manager.packages.edit', $package->id) }}" class="wp-btn wp-btn-secondary"><i class="fas fa-pen"></i> Package details & margin</a>
        @if($package->hasPricing())
            <a href="{{ route('tour-packages.book', $package->id) }}" target="_blank" rel="noopener" class="wp-btn wp-btn-primary"><i class="fas fa-external-link-alt"></i> Open the booking page</a>
        @endif
    </div>
</div>

@if(!$package->usesRoomPricing() && $package->rates->isNotEmpty())
    <div class="wp-notice wp-notice-info"><span><i class="fas fa-info-circle me-2"></i>This package is still priced from its old per-person rates. Fill in parts 1 and 2 below and it switches to room and group pricing; the old rates are then removed.</span></div>
@elseif(!$package->hasPricing())
    <div class="wp-notice wp-notice-info"><span><i class="fas fa-info-circle me-2"></i>Customers can book this package once parts 1 and 2 are filled in: at least one hotel with a double room price, and the tour price for at least one trip length.</span></div>
@endif

<nav class="bs-steps">
    <a href="#rooms" class="wp-btn wp-btn-secondary wp-btn-sm">1. Hotels & room prices</a>
    <a href="#land" class="wp-btn wp-btn-secondary wp-btn-sm">2. Tours & transfers</a>
    <a href="#check" class="wp-btn wp-btn-secondary wp-btn-sm">Price check</a>
    <a href="#hotels" class="wp-btn wp-btn-secondary wp-btn-sm">3. Hotel details</a>
    <a href="#addons" class="wp-btn wp-btn-secondary wp-btn-sm">4. Options</a>
    <a href="#itinerary" class="wp-btn wp-btn-secondary wp-btn-sm">5. Itinerary</a>
    <a href="#seasons" class="wp-btn wp-btn-secondary wp-btn-sm">6. Peak seasons</a>
</nav>

{{-- 1. Hotels and room prices --}}
<div class="wp-card" id="rooms">
    <div class="wp-card-header"><i class="fas fa-bed text-secondary-wp"></i> 1. Hotels and room prices <span class="text-muted-wp" style="font-weight:400;">· net, per room, per night</span></div>
    <div class="wp-card-body">
        <form method="POST" action="{{ route('manager.packages.booking-setup.rooms', $package->id) }}">
            @csrf @method('PUT')
            <div class="table-responsive">
                <table class="wp-table bs-table" id="roomTable">
                    <thead><tr><th>Level</th><th>Hotel name</th><th>Area</th><th>Single room</th><th>Double room</th><th>Triple room <small>optional</small></th><th>Extra bed <small>optional</small></th></tr></thead>
                    <tbody>
                        @foreach([3, 4, 5, 2] as $stars)
                            @php $h = $package->hotels->firstWhere('hotel_stars', $stars); @endphp
                            <tr data-stars="{{ $stars }}">
                                <td style="white-space:nowrap; color:var(--wp-primary);">{{ str_repeat('★', $stars) }}</td>
                                <td><input class="wp-input" name="hotels[{{ $stars }}][name]" value="{{ old("hotels.$stars.name", $h?->name) }}" placeholder="Not offered" aria-label="{{ $stars }}-star hotel name"></td>
                                <td><input class="wp-input" name="hotels[{{ $stars }}][location]" value="{{ old("hotels.$stars.location", $h?->location) }}" aria-label="{{ $stars }}-star area"></td>
                                @foreach(['single_net' => 'Single', 'double_net' => 'Double', 'triple_net' => 'Triple', 'extra_bed_net' => 'Extra bed'] as $f => $l)
                                    <td><input type="number" step="0.01" min="0" class="wp-input" name="hotels[{{ $stars }}][{{ $f }}]" value="{{ old("hotels.$stars.$f", $h?->$f ?: null) }}" aria-label="{{ $stars }}-star {{ $l }} room per night"></td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <button class="wp-btn wp-btn-primary" style="margin-top:12px;"><i class="fas fa-save"></i> Save hotels & room prices</button>
            <p class="wp-form-help">What the hotel charges you for the whole room for one night, in AED. Fill in one row per hotel you sell; the <strong>double room</strong> price is required. The booking works out the rooms from who travels: 1 adult gets a single, 2 a double, 3 a triple (or a double with an extra bed), 4 two doubles, and so on. Leave Triple or Extra bed empty if the hotel has none.</p>
        </form>
    </div>
</div>

{{-- 2. Land: tours, transfers, guide --}}
@php
    $landDays = $package->landRates->pluck('days')->unique()->sort()->values();
    if ($landDays->isEmpty()) { $landDays = collect([null]); }
@endphp
<div class="wp-card" id="land">
    <div class="wp-card-header"><i class="fas fa-route text-secondary-wp"></i> 2. Trip length and tour price <span class="text-muted-wp" style="font-weight:400;">· everything except the hotel</span></div>
    <div class="wp-card-body">
        <form method="POST" action="{{ route('manager.packages.booking-setup.land', $package->id) }}">
            @csrf @method('PUT')
            <div class="table-responsive">
                <table class="wp-table bs-table" id="landTable">
                    <thead><tr><th>Trip length (days)</th>@foreach($groups as $min => $label)<th @if($min > 1) data-group-col @endif>{{ $min === 1 ? 'Price per person (AED)' : 'Group of ' . $label }}</th>@endforeach<th></th></tr></thead>
                    <tbody>
                        @foreach($landDays as $i => $d)
                            @include('manager.packages._land-row', ['i' => $i, 'd' => $d])
                        @endforeach
                    </tbody>
                </table>
            </div>
            <template id="landRowTpl">@include('manager.packages._land-row', ['i' => '__I__', 'd' => null])</template>
            <label style="display:flex; gap:8px; align-items:center; margin-top:12px; font-size:13px; cursor:pointer;">
                <input type="checkbox" id="groupPricing" @checked($package->landRates->contains(fn ($r) => $r->min_group > 1))>
                The supplier charges less per person for bigger groups (show group prices)
            </label>
            <div class="bs-grid" style="grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); margin-top:12px;">
                <div class="wp-form-group" style="margin:0;"><label class="wp-form-label" for="child_land_percent">Children pay this % of the adult price</label>
                    <input type="number" min="0" max="100" step="1" class="wp-input" id="child_land_percent" name="child_land_percent" value="{{ old('child_land_percent', rtrim(rtrim(number_format($package->child_land_percent, 2), '0'), '.')) }}"></div>
                <div class="wp-form-group" style="margin:0;"><label class="wp-form-label" for="child_sharing_max_age">Children up to this age share the parents' bed (no hotel cost)</label>
                    <input type="number" min="0" max="17" class="wp-input" id="child_sharing_max_age" name="child_sharing_max_age" value="{{ old('child_sharing_max_age', $package->child_sharing_max_age) }}"></div>
            </div>
            <div style="display:flex; gap:8px; margin-top:12px;">
                <button type="button" class="wp-btn wp-btn-secondary" data-add-row="land"><i class="fas fa-plus"></i> Add a trip length</button>
                <button class="wp-btn wp-btn-primary"><i class="fas fa-save"></i> Save tour prices</button>
            </div>
            <p class="wp-form-help"><strong>Trip length:</strong> the number of days customers can book (one row each, e.g. 3, or 3 and 5). <strong>Price per person:</strong> what the supplier charges you per adult for the whole trip for everything except the hotel: airport pickup, sightseeing, tickets, guide, car. Hotel only? Enter 0. Group prices are optional; an empty one uses the price to its left. Children (ages {{ $package->infant_max_age + 1 }}–{{ $package->child_max_age }}) pay the % above of the tour price; those older than the bed-sharing age also share an adult's room and pay that % of an adult's half of a double room. Infants are free; anyone older than {{ $package->child_max_age }} pays as an adult.</p>
        </form>
    </div>
</div>

{{-- Price check: what customers will pay for typical bookings --}}
<div class="wp-card" id="check">
    <div class="wp-card-header"><i class="fas fa-calculator text-secondary-wp"></i> Price check <span class="text-muted-wp" style="font-weight:400;">· what customers pay, with your {{ rtrim(rtrim(number_format($package->margin_percent, 2), '0'), '.') }}% margin</span></div>
    <div class="wp-card-body">
        @if(empty($priceCheck['rows']))
            <p class="wp-form-help">Fill in parts 1 and 2 and save. This table then shows the total a customer pays for common bookings, so you can spot a wrong number before anyone books.</p>
        @else
            <div class="table-responsive">
                <table class="wp-table" id="priceCheck">
                    <thead><tr><th>{{ $priceCheck['days'] }}-day trip</th>@foreach($priceCheck['hotels'] as $h)<th>{{ str_repeat('★', $h->hotel_stars) }} {{ $h->name }}</th>@endforeach</tr></thead>
                    <tbody>
                        @foreach($priceCheck['rows'] as $row)
                            <tr><td><strong>{{ $row['label'] }}</strong></td>
                                @foreach($row['cells'] as $c)
                                    <td>@if($c)<strong style="color:var(--wp-primary);">AED {{ number_format($c['total'], 0) }}</strong><br><small class="text-muted-wp">{{ $c['rooms'] }} · AED {{ number_format($c['per_person'], 0) }} each</small>@else — @endif</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="wp-form-help">Totals for the shortest trip, outside peak seasons (starting {{ $priceCheck['date'] }}), with the included options. "Each" is the total divided by everyone who pays.</p>
        @endif
    </div>
</div>

{{-- 2. Hotels --}}
<div class="wp-card" id="hotels">
    <div class="wp-card-header"><i class="fas fa-hotel text-secondary-wp"></i> 3. Hotel details: check-in, policies and meals</div>
    <div class="wp-card-body">
        @if($tiers->isEmpty())
            <p class="wp-form-help">Add your hotels in part 1 first; a box appears here for each one.</p>
        @else
        <form method="POST" action="{{ route('manager.packages.booking-setup.hotels', $package->id) }}">
            @csrf @method('PUT')
            @foreach($tiers as $stars)
                @php $h = $package->hotels->firstWhere('hotel_stars', $stars); $plans = $h?->meal_plans ?? ['breakfast']; @endphp
                <div class="bs-hotel">
                    <h3>{{ str_repeat('★', $stars) }} {{ $h?->name ?? $stars . '-star' }}</h3>
                    <div class="bs-grid">
                        <div class="wp-form-group"><label class="wp-form-label">Check-in from</label><input class="wp-input" name="hotels[{{ $stars }}][check_in_time]" value="{{ old("hotels.$stars.check_in_time", $h?->check_in_time ?? '14:00') }}" placeholder="14:00"></div>
                        <div class="wp-form-group"><label class="wp-form-label">Check-out by</label><input class="wp-input" name="hotels[{{ $stars }}][check_out_time]" value="{{ old("hotels.$stars.check_out_time", $h?->check_out_time ?? '12:00') }}" placeholder="12:00"></div>
                    </div>
                    <div class="bs-grid" style="grid-template-columns:1fr 1fr;">
                        <div class="wp-form-group"><label class="wp-form-label">Late check-in policy</label><textarea class="wp-textarea" rows="2" name="hotels[{{ $stars }}][late_check_in_policy]" placeholder="e.g. Reception is open 24 hours; tell us your arrival time.">{{ old("hotels.$stars.late_check_in_policy", $h?->late_check_in_policy) }}</textarea></div>
                        <div class="wp-form-group"><label class="wp-form-label">Late check-out policy</label><textarea class="wp-textarea" rows="2" name="hotels[{{ $stars }}][late_check_out_policy]" placeholder="e.g. Until 16:00 for 50% of a night, subject to availability.">{{ old("hotels.$stars.late_check_out_policy", $h?->late_check_out_policy) }}</textarea></div>
                    </div>
                    <div class="wp-form-group">
                        <label class="wp-form-label">Meals this hotel actually serves</label>
                        <div class="bs-meals">
                            @foreach($mealPlans as $code => $label)
                                <label><input type="checkbox" name="hotels[{{ $stars }}][meal_plans][]" value="{{ $code }}" @checked(in_array($code, old("hotels.$stars.meal_plans", $plans), true))> {{ $label }}</label>
                            @endforeach
                        </div>
                        <p class="wp-form-help">Many hotels have no lunch or dinner menu; customers are only offered the plans ticked here.</p>
                    </div>
                </div>
            @endforeach
            <button class="wp-btn wp-btn-primary"><i class="fas fa-save"></i> Save hotels</button>
        </form>
        @endif
    </div>
</div>

{{-- 3. Options --}}
<div class="wp-card" id="addons">
    <div class="wp-card-header"><i class="fas fa-list-check text-secondary-wp"></i> 4. Options: guide, transport, meal supplements, airport transfer, insurance</div>
    <div class="wp-card-body">
        <form method="POST" action="{{ route('manager.packages.booking-setup.addons', $package->id) }}">
            @csrf @method('PUT')
            <div class="table-responsive">
                <table class="wp-table bs-table" id="addonTable">
                    <thead><tr><th>Type</th><th>Option</th><th>Meal plan</th><th>Net price</th><th>Charged</th><th>Note for customers</th><th></th></tr></thead>
                    <tbody>
                        @foreach($package->addons->values() as $i => $a)
                            @include('manager.packages._addon-row', ['i' => $i, 'a' => $a])
                        @endforeach
                        @if($package->addons->isEmpty())
                            @foreach([['guide_language','English-speaking guide',0,'per_booking'],['transport','Private car with driver',0,'per_booking']] as $i => [$cat, $label, $price, $basis])
                                @include('manager.packages._addon-row', ['i' => $i, 'a' => new \App\Models\TourPackageAddon(['category' => $cat, 'label' => $label, 'net_price' => $price, 'charge_basis' => $basis])])
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
            <template id="addonRowTpl">@include('manager.packages._addon-row', ['i' => '__I__', 'a' => null])</template>
            <div style="display:flex; gap:8px; margin-top:12px;">
                <button type="button" class="wp-btn wp-btn-secondary" data-add-row="addon"><i class="fas fa-plus"></i> Add an option</button>
                <button class="wp-btn wp-btn-primary"><i class="fas fa-save"></i> Save options</button>
            </div>
            <p class="wp-form-help">A price of 0 means included: customers see it under "Included in your trip". Only add what this package really includes or sells. Meal plan rows are the supplement on top of the room (breakfast is normally in the rate). Insurance is offered as optional unless you made it compulsory under Package details.</p>
        </form>
    </div>
</div>

{{-- 4. Itinerary --}}
<div class="wp-card" id="itinerary">
    <div class="wp-card-header"><i class="fas fa-route text-secondary-wp"></i> 5. Day-by-day itinerary</div>
    <div class="wp-card-body">
        <form method="POST" action="{{ route('manager.packages.booking-setup.days', $package->id) }}" enctype="multipart/form-data">
            @csrf
            <div id="dayList">
                @foreach($package->itineraryDays->values() as $i => $d)
                    @include('manager.packages._day-row', ['i' => $i, 'd' => $d])
                @endforeach
                @if($package->itineraryDays->isEmpty())
                    @foreach(range(1, max(1, (int) ($durations->max() ?? 1))) as $n)
                        @include('manager.packages._day-row', ['i' => $n - 1, 'd' => new \App\Models\TourPackageDay(['day_number' => $n])])
                    @endforeach
                @endif
            </div>
            <template id="dayRowTpl">@include('manager.packages._day-row', ['i' => '__I__', 'd' => null])</template>
            <div style="display:flex; gap:8px; margin-top:12px;">
                <button type="button" class="wp-btn wp-btn-secondary" data-add-row="day"><i class="fas fa-plus"></i> Add a day</button>
                <button class="wp-btn wp-btn-primary"><i class="fas fa-save"></i> Save itinerary</button>
            </div>
            <p class="wp-form-help">Leave "For trip length" empty to use a day for every trip length (a 4-day trip shows days 1–4). Fill it in when a trip length has its own plan. Days without a title are removed.</p>
        </form>
    </div>
</div>

{{-- 5. Seasons --}}
<div class="wp-card" id="seasons">
    <div class="wp-card-header"><i class="fas fa-calendar-alt text-secondary-wp"></i> 6. Peak seasons</div>
    <div class="wp-card-body">
        <form method="POST" action="{{ route('manager.packages.booking-setup.seasons', $package->id) }}">
            @csrf @method('PUT')
            <div class="table-responsive">
                <table class="wp-table bs-table" id="seasonTable">
                    <thead><tr><th>Name</th><th>From</th><th>To</th><th>Surcharge %</th><th></th></tr></thead>
                    <tbody>
                        @foreach($package->seasons->values() as $i => $s)
                            @include('manager.packages._season-row', ['i' => $i, 's' => $s])
                        @endforeach
                        @if($package->seasons->isEmpty())
                            @include('manager.packages._season-row', ['i' => 0, 's' => null])
                        @endif
                    </tbody>
                </table>
            </div>
            <template id="seasonRowTpl">@include('manager.packages._season-row', ['i' => '__I__', 's' => null])</template>
            <div style="display:flex; gap:8px; margin-top:12px;">
                <button type="button" class="wp-btn wp-btn-secondary" data-add-row="season"><i class="fas fa-plus"></i> Add a peak period</button>
                <button class="wp-btn wp-btn-primary"><i class="fas fa-save"></i> Save seasons</button>
            </div>
            <p class="wp-form-help">Trips that start inside a peak period pay this percentage on top of the package rate (not on options).</p>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const margin = {{ (float) $package->margin_percent }} / 100;
    const targets = { land: ['landTable', 'landRowTpl'], addon: ['addonTable', 'addonRowTpl'], day: ['dayList', 'dayRowTpl'], season: ['seasonTable', 'seasonRowTpl'] };
    let counter = 1000;
    document.querySelectorAll('[data-add-row]').forEach(btn => btn.addEventListener('click', () => {
        const [listId, tplId] = targets[btn.dataset.addRow];
        const html = document.getElementById(tplId).innerHTML.replaceAll('__I__', String(counter++));
        const list = document.getElementById(listId);
        (list.tBodies ? list.tBodies[0] : list).insertAdjacentHTML('beforeend', html);
    }));
    document.addEventListener('click', e => {
        const b = e.target.closest('[data-remove-row]');
        if (b) b.closest('[data-row]').remove();
    });
    // Live "customer pays" preview next to each adult net price.
    document.addEventListener('input', e => {
        if (!e.target.matches('[data-net-adult]')) return;
        const out = e.target.closest('[data-row]').querySelector('[data-sell]');
        const v = parseFloat(e.target.value);
        out.textContent = isNaN(v) ? '—' : 'AED ' + (Math.round(v * (1 + margin) * 100) / 100).toLocaleString('en-US');
    });
    // Group-size prices are optional and hidden unless used.
    const groupBox = document.getElementById('groupPricing');
    const showGroups = () => document.querySelectorAll('#landTable [data-group-col]').forEach(el => el.hidden = !groupBox.checked);
    if (groupBox) { groupBox.addEventListener('change', showGroups); showGroups(); document.querySelectorAll('[data-add-row=land]').forEach(b => b.addEventListener('click', () => setTimeout(showGroups))); }
    // Meal-plan rows need a plan; other rows don't.
    document.addEventListener('change', e => {
        if (!e.target.matches('[data-addon-cat]')) return;
        e.target.closest('[data-row]').querySelector('[data-meal-code]').disabled = e.target.value !== 'meal_plan';
    });
})();
</script>
@endpush
