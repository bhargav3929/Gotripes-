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
        'Rates' => $package->rates->isNotEmpty(),
        'Hotels' => $package->hotels->isNotEmpty(),
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
        @if($package->rates->isNotEmpty())
            <a href="{{ route('tour-packages.book', $package->id) }}" target="_blank" rel="noopener" class="wp-btn wp-btn-primary"><i class="fas fa-external-link-alt"></i> Open the booking page</a>
        @endif
    </div>
</div>

@unless($package->rates->isNotEmpty())
    <div class="wp-notice wp-notice-info"><span><i class="fas fa-info-circle me-2"></i>Customers can book this package once it has at least one rate. Start with the supplier's rate sheet below.</span></div>
@endunless

<nav class="bs-steps">
    <a href="#rates" class="wp-btn wp-btn-secondary wp-btn-sm">1. Rates</a>
    <a href="#hotels" class="wp-btn wp-btn-secondary wp-btn-sm">2. Hotels</a>
    <a href="#addons" class="wp-btn wp-btn-secondary wp-btn-sm">3. Options</a>
    <a href="#itinerary" class="wp-btn wp-btn-secondary wp-btn-sm">4. Itinerary</a>
    <a href="#seasons" class="wp-btn wp-btn-secondary wp-btn-sm">5. Peak seasons</a>
</nav>

{{-- 1. Rates --}}
<div class="wp-card" id="rates">
    <div class="wp-card-header"><i class="fas fa-coins text-secondary-wp"></i> 1. Net rates per person, whole stay</div>
    <div class="wp-card-body">
        <form method="POST" action="{{ route('manager.packages.booking-setup.import', $package->id) }}" enctype="multipart/form-data" style="display:flex; gap:8px; align-items:end; flex-wrap:wrap; margin-bottom:16px;">
            @csrf
            <div class="wp-form-group" style="margin:0; flex:1; min-width:240px;">
                <label class="wp-form-label" for="rate_sheet">Import the supplier's rate sheet (CSV)</label>
                <input type="file" class="wp-input" id="rate_sheet" name="rate_sheet" accept=".csv,text/csv" required>
                <p class="wp-form-help">Columns: <code>days, hotel_stars, adult_net, child_net, infant_net, single_supplement_net</code>. In Excel: File → Save As → CSV. Importing replaces the rates below.</p>
            </div>
            <button class="wp-btn wp-btn-secondary"><i class="fas fa-file-import"></i> Import</button>
        </form>

        <form method="POST" action="{{ route('manager.packages.booking-setup.rates', $package->id) }}">
            @csrf @method('PUT')
            <div class="table-responsive">
                <table class="wp-table bs-table" id="rateTable">
                    <thead><tr><th>Days</th><th>Hotel ★</th><th>Adult</th><th>Child</th><th>Infant</th><th>Single supplement</th><th>Customer pays (adult)</th><th></th></tr></thead>
                    <tbody>
                        @foreach($package->rates->values() as $i => $r)
                            @include('manager.packages._rate-row', ['i' => $i, 'r' => $r])
                        @endforeach
                        @if($package->rates->isEmpty())
                            @include('manager.packages._rate-row', ['i' => 0, 'r' => null])
                        @endif
                    </tbody>
                </table>
            </div>
            <template id="rateRowTpl">@include('manager.packages._rate-row', ['i' => '__I__', 'r' => null])</template>
            <div style="display:flex; gap:8px; margin-top:12px;">
                <button type="button" class="wp-btn wp-btn-secondary" data-add-row="rate"><i class="fas fa-plus"></i> Add a row</button>
                <button class="wp-btn wp-btn-primary"><i class="fas fa-save"></i> Save rates</button>
            </div>
            <p class="wp-form-help">Net prices in AED per person for the whole trip, twin sharing. Each row is one trip length at one hotel category. The single supplement is charged once when one adult has a room alone.</p>
        </form>
    </div>
</div>

{{-- 2. Hotels --}}
<div class="wp-card" id="hotels">
    <div class="wp-card-header"><i class="fas fa-hotel text-secondary-wp"></i> 2. Hotels, check-in policy and meals</div>
    <div class="wp-card-body">
        @if($tiers->isEmpty())
            <p class="wp-form-help">Add rates first; a hotel box appears for each star category you price.</p>
        @else
        <form method="POST" action="{{ route('manager.packages.booking-setup.hotels', $package->id) }}">
            @csrf @method('PUT')
            @foreach($tiers as $stars)
                @php $h = $package->hotels->firstWhere('hotel_stars', $stars); $plans = $h?->meal_plans ?? ['breakfast']; @endphp
                <div class="bs-hotel">
                    <h3>{{ str_repeat('★', $stars) }} {{ $stars }}-star</h3>
                    <div class="bs-grid">
                        <div class="wp-form-group"><label class="wp-form-label">Hotel name</label><input class="wp-input" name="hotels[{{ $stars }}][name]" value="{{ old("hotels.$stars.name", $h?->name) }}" placeholder="Leave blank to hide"></div>
                        <div class="wp-form-group"><label class="wp-form-label">Area</label><input class="wp-input" name="hotels[{{ $stars }}][location]" value="{{ old("hotels.$stars.location", $h?->location) }}"></div>
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
    <div class="wp-card-header"><i class="fas fa-list-check text-secondary-wp"></i> 3. Options: guide, transport, meal supplements, airport transfer, insurance</div>
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
                            @foreach([['guide_language','English',0,'per_booking'],['transport','Standard bus',0,'per_booking'],['airport_transfer','Airport to hotel pickup',0,'per_booking'],['insurance','Travel insurance',0,'per_person']] as $i => [$cat, $label, $price, $basis])
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
            <p class="wp-form-help">A net price of 0 means included. Meal plan rows are the supplement on top of the room (breakfast is normally in the rate). Insurance is offered as optional unless you made it compulsory under Package details.</p>
        </form>
    </div>
</div>

{{-- 4. Itinerary --}}
<div class="wp-card" id="itinerary">
    <div class="wp-card-header"><i class="fas fa-route text-secondary-wp"></i> 4. Day-by-day itinerary</div>
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
    <div class="wp-card-header"><i class="fas fa-calendar-alt text-secondary-wp"></i> 5. Peak seasons</div>
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
    const targets = { rate: ['rateTable', 'rateRowTpl'], addon: ['addonTable', 'addonRowTpl'], day: ['dayList', 'dayRowTpl'], season: ['seasonTable', 'seasonRowTpl'] };
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
    // Meal-plan rows need a plan; other rows don't.
    document.addEventListener('change', e => {
        if (!e.target.matches('[data-addon-cat]')) return;
        e.target.closest('[data-row]').querySelector('[data-meal-code]').disabled = e.target.value !== 'meal_plan';
    });
})();
</script>
@endpush
