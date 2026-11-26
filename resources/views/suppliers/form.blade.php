@extends('suppliers._layout')

@section('title', 'Your rates')

@php
    $v = fn (string $key, $default = null) => old($key, data_get($prefill, $key, $default));
    $tours = array_values((array) $v('tours', [])) ?: [[]];
    $extras = array_values((array) $v('extras', [])) ?: [[]];
    $days = array_values((array) $v('days', [])) ?: [[]];
    $seasons = array_values((array) $v('seasons', [])) ?: [[]];
    $groupPricing = (bool) $v('group_pricing');
    $cur = $v('currency', 'USD');
    $sections = [
        'company' => 'Your company', 'package' => 'The package', 'hotels' => 'Hotels and room prices',
        'tours' => 'Tour price', 'extras' => 'Extras', 'days' => 'Day-by-day plan',
        'seasons' => 'Peak seasons', 'photos' => 'Photos and notes',
    ];
@endphp

@section('styles')
    .wrap { max-width: 1080px; margin: 0 auto; padding: 48px 16px 160px; display: grid; grid-template-columns: 200px minmax(0, 1fr); gap: 48px; }
    .toc { position: sticky; top: 96px; align-self: start; font-size: 14px; }
    .toc ol { list-style: none; margin: 0; padding: 0; display: grid; gap: 4px; counter-reset: s; }
    .toc a { display: flex; gap: 8px; padding: 6px 8px; border-radius: var(--r); color: var(--ink-2); text-decoration: none; transition: background-color .15s ease; }
    .toc a::before { counter-increment: s; content: counter(s); color: var(--muted); width: 16px; }
    .toc a:hover, .toc a.on { background: var(--card); color: var(--ink); }
    .intro { margin-bottom: 48px; }
    .sec { background: var(--card); border: 1px solid var(--line); border-radius: var(--r-lg); padding: 32px; margin-bottom: 24px; scroll-margin-top: 88px; }
    .sec-head { margin-bottom: 24px; }
    .sec-head p { margin: 0; color: var(--muted); font-size: 15px; }
    .num { color: var(--muted); font-weight: 300; margin-right: 8px; }
    .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 24px; }
    .grid4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
    .span2 { grid-column: span 2; }
    .req::after { content: ' *'; color: var(--err); }
    .money { position: relative; }
    .money input { padding-left: 48px; }
    .money .cur { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-size: 13px; font-weight: 600; color: var(--muted); pointer-events: none; }
    .hotel { border: 1px solid var(--line); border-radius: var(--r); margin-bottom: 16px; background: #fff; }
    .hotel summary { list-style: none; cursor: pointer; padding: 16px; display: flex; align-items: center; gap: 12px; font-weight: 600; }
    .hotel summary::-webkit-details-marker { display: none; }
    .hotel summary .stars { color: #c79a00; letter-spacing: 2px; min-width: 76px; }
    .hotel summary .state { margin-left: auto; font-weight: 400; font-size: 13px; color: var(--muted); }
    .hotel summary .chev { transition: transform .15s ease; color: var(--muted); }
    .hotel[open] summary .chev { transform: rotate(90deg); }
    .hotel-body { padding: 0 16px 24px; display: grid; gap: 16px; }
    .meals { display: flex; flex-wrap: wrap; gap: 8px 16px; }
    .check { display: inline-flex; align-items: center; gap: 8px; font-size: 15px; cursor: pointer; }
    .check input { width: 18px; height: 18px; accent-color: var(--ink); }
    .rows { display: grid; gap: 12px; }
    .row { display: grid; gap: 12px; align-items: end; padding: 16px; border: 1px solid var(--line); border-radius: var(--r); background: #fff; position: relative; }
    .row-tour { grid-template-columns: 140px minmax(160px, 260px); padding-right: 72px; align-items: start; }
    .grouped .row-tour { grid-template-columns: repeat(auto-fill, minmax(132px, 1fr)); }
    .row-tour .rm, .row-extra .rm, .row-season .rm { position: absolute; top: 16px; right: 16px; }
    .row-extra { grid-template-columns: 1.1fr 1.4fr 130px 1fr; padding-right: 72px; }
    .row-day { grid-template-columns: 80px 1fr 40px; }
    .row-day textarea { grid-column: 2 / 3; min-height: 72px; }
    .row-season { grid-template-columns: 1.4fr 1fr 1fr 110px; padding-right: 72px; }
    .rm { width: 40px; height: 46px; border: 1px solid var(--line); background: none; border-radius: var(--r); cursor: pointer; color: var(--muted); font-size: 20px; line-height: 1; transition: color .15s ease, border-color .15s ease; }
    .rm:hover { color: var(--err); border-color: var(--err); }
    [data-band]:not(.show) { display: none; }
    .add { margin-top: 16px; }
    .photos-old { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 12px; margin-bottom: 16px; }
    .ph { display: grid; gap: 8px; }
    .ph-img { position: relative; display: block; border-radius: var(--r); overflow: hidden; aspect-ratio: 4/3; cursor: pointer; border: 2px solid transparent; transition: border-color .15s ease; }
    .ph-img img { width: 100%; height: 100%; object-fit: cover; display: block; transition: opacity .15s ease; }
    .ph-img input { position: absolute; top: 8px; left: 8px; width: 18px; height: 18px; accent-color: var(--ink); }
    .ph-img input:not(:checked) + img { opacity: .3; }
    .ph:has(.ph-cover input:checked) .ph-img { border-color: var(--ink); }
    .ph-cover { display: inline-flex; align-items: center; gap: 8px; font-size: 14px; cursor: pointer; }
    .ph-cover input { width: 16px; height: 16px; accent-color: var(--ink); }
    .photos-new:empty { display: none; }
    .photos-new { margin-top: 16px; }
    .kids { margin-top: 48px; padding-top: 32px; border-top: 1px solid var(--line); }
    .kids h3, .estimate h3 { font-size: 17px; margin: 0 0 8px; }
    .kid-band { display: grid; grid-template-columns: 120px 180px 1fr; gap: 16px; align-items: end; padding: 16px 0; border-bottom: 1px dashed var(--line); }
    .kid-band:last-of-type { border-bottom: 0; }
    .kid-tag { font-weight: 600; font-size: 14px; align-self: center; }
    .kid-say { margin: 0; font-size: 14px; color: var(--ink-2); align-self: center; }
    .estimate { margin-top: 32px; border: 1px solid var(--line-strong); border-radius: var(--r); background: #fff; padding: 24px; }
    .est-head { margin-bottom: 16px; }
    .est-table { width: 100%; border-collapse: collapse; font-size: 14px; font-variant-numeric: tabular-nums; }
    .est-table th { text-align: left; font-weight: 600; font-size: 12px; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); padding: 8px 8px 8px 0; border-bottom: 1px solid var(--line); }
    .est-table td { padding: 12px 8px 12px 0; border-bottom: 1px solid var(--line); vertical-align: top; }
    .est-table td.n, .est-table th.n { text-align: right; padding-right: 0; }
    .est-table tr:last-child td { border-bottom: 0; }
    .est-table small { display: block; color: var(--muted); font-size: 12px; margin-top: 2px; }
    .est-warn { margin: 16px 0 0; padding: 12px 16px; border-radius: var(--r); background: var(--err-soft); color: var(--err); font-size: 14px; }
    .drop { border: 1px dashed var(--line-strong); border-radius: var(--r); padding: 24px; text-align: center; background: #fff; }
    .drop input { width: 100%; }
    .send { position: fixed; bottom: 0; left: 0; right: 0; background: rgba(247, 244, 236, .92); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border-top: 1px solid var(--line); z-index: 15; }
    .send-in { max-width: 1080px; margin: 0 auto; padding: 16px; display: flex; gap: 16px; align-items: center; justify-content: flex-end; }
    .send-in .hint { margin-right: auto; }
    .draft { display: none; margin-bottom: 24px; align-items: center; gap: 16px; flex-wrap: wrap; }
    .draft.show { display: flex; }
    .draft button { font: inherit; font-weight: 600; background: var(--ink); color: #fff; border: 0; border-radius: var(--r); padding: 8px 16px; cursor: pointer; }
    .draft button.x { background: none; color: inherit; text-decoration: underline; padding: 8px 0; }
    .errs { margin: 8px 0 0; padding-left: 20px; }
    @media (max-width: 960px) { .wrap { grid-template-columns: 1fr; gap: 0; } .toc { display: none; } }
    @media (max-width: 720px) {
        .sec { padding: 24px 16px; }
        .grid2, .grid4 { grid-template-columns: 1fr 1fr; }
        .grid2 > .field { grid-column: span 2; }
        .grid2 > .field.half { grid-column: span 1; }
        .row-tour, .grouped .row-tour, .row-extra, .row-season { grid-template-columns: 1fr 1fr; padding-right: 64px; }
        .row-day { grid-template-columns: 72px 1fr; padding-right: 64px; }
        .row-day textarea { grid-column: 1 / -1; }
        .row .rm { position: absolute; top: 16px; right: 16px; }
        .row-extra > .field:nth-child(2) { grid-column: span 2; }
        .row-season > .field:first-child { grid-column: span 2; }
        .kid-band { grid-template-columns: 1fr 1fr; }
        .kid-say { grid-column: 1 / -1; }
        .estimate { padding: 16px; }
        .est-table .hide-sm { display: none; }
        .send-in .hint { display: none; }
        .send-in .btn { width: 100%; }
    }
@endsection

@section('bar')
    <div class="bar-meta">
        <span>Code <strong style="font-family:ui-monospace,Menlo,monospace;">{{ $invite->display_code }}</strong> · {{ $invite->supplier_name }}</span>
        <form method="POST" action="{{ route('suppliers.leave') }}">@csrf<button>Close</button></form>
    </div>
@endsection

@section('content')
<div class="wrap">
    <nav class="toc" aria-label="Form sections">
        <ol>
            @foreach($sections as $id => $label)<li><a href="#{{ $id }}">{{ $label }}</a></li>@endforeach
        </ol>
    </nav>

    <form method="POST" action="{{ route('suppliers.submit') }}" enctype="multipart/form-data" id="supplierForm" novalidate>
        @csrf
        <div class="intro rise">
            <h1>{{ $last ? 'Update your rates' : 'Your rates' }}</h1>
            <p class="lead">
                @if($last)
                    This is what you sent on {{ $last->created_at->format('j M Y') }}. Change what is different and send it again. <a href="{{ route('suppliers.preview') }}">See how travellers will see it</a>.
                @else
                    Enter your <strong>net prices</strong>, what you charge us. We add our margin on top for our customers. Fill in what you sell and leave the rest empty.
                @endif
            </p>
        </div>

        <div class="notice notice-info draft" id="draftNotice" role="status">
            <span>You have changes in this browser that were not sent yet.</span>
            <button type="button" id="draftRestore">Bring them back</button>
            <button type="button" class="x" id="draftDiscard">Discard</button>
        </div>

        @if($errors->any())
            <div class="notice notice-err" role="alert" style="margin-bottom:24px;">
                <strong>Please check {{ $errors->count() === 1 ? 'this' : 'these' }}:</strong>
                <ul class="errs">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                @if($errors->has('photos') || $errors->has('photos.*'))<div style="margin-top:8px;">Photos you picked were not kept. Pick them again.</div>@endif
            </div>
        @endif

        {{-- 1 --}}
        <section class="sec" id="company">
            <div class="sec-head"><h2><span class="num">1</span>Your company</h2><p>So our team knows who to contact about these rates.</p></div>
            <div class="grid2">
                <div class="field"><label for="c_name" class="req">Company name</label><input type="text" id="c_name" name="company[name]" value="{{ $v('company.name') }}" required class="@error('company.name') is-invalid @enderror"></div>
                <div class="field"><label for="c_contact" class="req">Contact person</label><input type="text" id="c_contact" name="company[contact]" value="{{ $v('company.contact') }}" required class="@error('company.contact') is-invalid @enderror"></div>
                <div class="field"><label for="c_email" class="req">Email</label><input type="email" id="c_email" name="company[email]" value="{{ $v('company.email') }}" required class="@error('company.email') is-invalid @enderror"></div>
                <div class="field"><label for="c_phone" class="req">Phone / WhatsApp</label><input type="tel" id="c_phone" name="company[phone]" value="{{ $v('company.phone') }}" required placeholder="+995 ..." class="@error('company.phone') is-invalid @enderror"></div>
            </div>
        </section>

        {{-- 2 --}}
        <section class="sec" id="package">
            <div class="sec-head"><h2><span class="num">2</span>The package</h2><p>What travellers get, in a few lines.</p></div>
            <div class="grid2">
                <div class="field"><label for="p_name" class="req">Package name</label><input type="text" id="p_name" name="package[name]" value="{{ $v('package.name') }}" required placeholder="e.g. Georgia: Tbilisi, Kazbegi and Kakheti" class="@error('package.name') is-invalid @enderror"></div>
                <div class="field"><label for="p_country" class="req">Country</label><input type="text" id="p_country" name="package[country]" value="{{ $v('package.country') }}" required class="@error('package.country') is-invalid @enderror"></div>
                <div class="field span2"><label for="p_places">Cities and places covered</label><input type="text" id="p_places" name="package[places]" value="{{ $v('package.places') }}" placeholder="e.g. Tbilisi, Mtskheta, Gudauri, Kazbegi, Signagi"></div>
                <div class="field span2"><label for="p_desc">Short description</label><textarea id="p_desc" name="package[description]" maxlength="3000">{{ $v('package.description') }}</textarea></div>
                <div class="field"><label for="p_inc">Included</label><textarea id="p_inc" name="package[included]" maxlength="3000" placeholder="One per line, e.g.&#10;Airport pickup and drop&#10;Private car with driver&#10;Entry tickets">{{ $v('package.included') }}</textarea></div>
                <div class="field"><label for="p_exc">Not included</label><textarea id="p_exc" name="package[excluded]" maxlength="3000" placeholder="e.g.&#10;Flights&#10;Lunch and dinner&#10;Tips">{{ $v('package.excluded') }}</textarea></div>
                <div class="field half"><label for="p_from">Prices valid from</label><input type="date" id="p_from" name="package[valid_from]" value="{{ $v('package.valid_from') }}"></div>
                <div class="field half"><label for="p_to">Prices valid until</label><input type="date" id="p_to" name="package[valid_to]" value="{{ $v('package.valid_to') }}" class="@error('package.valid_to') is-invalid @enderror"></div>
                <div class="field half"><label for="currency" class="req">Your prices are in</label>
                    <select id="currency" name="currency" required>
                        @foreach($currencies as $c)<option value="{{ $c }}" @selected($cur === $c)>{{ $c }}</option>@endforeach
                    </select>
                    <small>Use this currency for every price on the form.</small>
                </div>
            </div>
        </section>

        {{-- 3 --}}
        <section class="sec" id="hotels">
            <div class="sec-head"><h2><span class="num">3</span>Hotels and room prices</h2><p>For each hotel level you offer: the price <strong>for the whole room, for one night</strong>. Only the double room price is required. Leave a level empty if you don't offer it.</p></div>
            @error('hotels')<p class="err" role="alert">{{ $message }}</p>@enderror
            @foreach($stars as $s)
                @php $named = filled($v("hotels.$s.name")); $meals = (array) $v("hotels.$s.meals", ['breakfast']); @endphp
                <details class="hotel" @if($named || $s === 3 || $errors->has("hotels.$s.*")) open @endif>
                    <summary>
                        <span class="stars" aria-hidden="true">{{ str_repeat('★', $s) }}</span><span>{{ $s }}-star hotel</span>
                        <span class="state" data-hotel-state="{{ $s }}">{{ $named ? $v("hotels.$s.name") : 'Not offered' }}</span>
                        <span class="chev" aria-hidden="true">›</span>
                    </summary>
                    <div class="hotel-body">
                        <div class="grid2">
                            <div class="field"><label for="h{{ $s }}_name">Hotel name</label><input type="text" id="h{{ $s }}_name" name="hotels[{{ $s }}][name]" value="{{ $v("hotels.$s.name") }}" data-hotel-name="{{ $s }}"></div>
                            <div class="field"><label for="h{{ $s }}_loc">Area / city</label><input type="text" id="h{{ $s }}_loc" name="hotels[{{ $s }}][location]" value="{{ $v("hotels.$s.location") }}"></div>
                        </div>
                        <div class="grid4">
                            @foreach(['single' => 'Single room', 'double' => 'Double room', 'triple' => 'Triple room', 'extra_bed' => 'Extra bed'] as $f => $l)
                                <div class="field">
                                    <label for="h{{ $s }}_{{ $f }}" @class(['req' => $f === 'double'])>{{ $l }}</label>
                                    <div class="money"><span class="cur" data-cur>{{ $cur }}</span><input type="number" inputmode="decimal" step="0.01" min="0" id="h{{ $s }}_{{ $f }}" name="hotels[{{ $s }}][{{ $f }}]" value="{{ $v("hotels.$s.$f") }}" class="@error("hotels.$s.$f") is-invalid @enderror"></div>
                                    @error("hotels.$s.$f")<span class="err">{{ $message }}</span>@enderror
                                </div>
                            @endforeach
                        </div>
                        <small class="hint" style="margin-top:-8px;">Per room per night. Single: one person alone in the room. Triple: a room for 3. Extra bed: a third person in a double room. With no triple or extra bed price, children share their parents' room at the child % (section 4). A cruise cabin counts as a room.</small>
                        <div class="field"><span class="label">Meals included in the room price</span>
                            <div class="meals">
                                @foreach($mealPlans as $code => $label)
                                    <label class="check"><input type="checkbox" name="hotels[{{ $s }}][meals][]" value="{{ $code }}" @checked(in_array($code, $meals, true))> {{ $label }}</label>
                                @endforeach
                            </div>
                        </div>
                        <div class="grid4">
                            <div class="field"><label for="h{{ $s }}_in">Check-in</label><input type="time" id="h{{ $s }}_in" name="hotels[{{ $s }}][check_in]" value="{{ $v("hotels.$s.check_in", '14:00') }}"></div>
                            <div class="field"><label for="h{{ $s }}_out">Check-out</label><input type="time" id="h{{ $s }}_out" name="hotels[{{ $s }}][check_out]" value="{{ $v("hotels.$s.check_out", '12:00') }}"></div>
                            <div class="field span2"><label for="h{{ $s }}_late">Early / late check-in and check-out</label><input type="text" id="h{{ $s }}_late" name="hotels[{{ $s }}][late_policy]" value="{{ $v("hotels.$s.late_policy") }}" placeholder="e.g. Late check-out until 16:00 for 50% of a night"></div>
                        </div>
                    </div>
                </details>
            @endforeach
        </section>

        {{-- 4 --}}
        <section class="sec" id="tours">
            <div class="sec-head"><h2><span class="num">4</span>Tour price</h2><p>Everything except the hotel (airport pickup, sightseeing, tickets, guide, car), <strong>per person, for the whole trip</strong>. One row for each trip length you sell. Only the hotel? Enter 0.</p></div>
            @error('tours')<p class="err" role="alert">{{ $message }}</p>@enderror
            <label class="check" style="margin-bottom:16px;"><input type="checkbox" name="group_pricing" value="1" id="groupPricing" @checked($groupPricing)> My price per person is lower for bigger groups</label>
            <div class="rows @if($groupPricing) grouped @endif" id="tourRows">
                @foreach($tours as $i => $t)
                    @include('suppliers._tour-row', ['i' => $i, 't' => $t])
                @endforeach
            </div>
            <template id="tourTpl">@include('suppliers._tour-row', ['i' => '__I__', 't' => []])</template>
            <button type="button" class="btn btn-ghost add" data-add="tour">+ Add a trip length</button>
            <div class="kids" id="childPolicy">
                <h3>Children's prices</h3>
                <p class="hint" style="margin:0 0 16px;">Three age bands. Children share their parents' room.</p>
                <div class="kid-band">
                    <span class="kid-tag">Free</span>
                    <div class="field"><label for="child_free_age">Up to and including age</label><input type="number" inputmode="numeric" min="0" max="17" id="child_free_age" name="child_free_age" value="{{ $v('child_free_age') }}" data-kid="free" class="@error('child_free_age') is-invalid @enderror"></div>
                    <p class="kid-say" data-kid-say="free"></p>
                </div>
                <div class="kid-band">
                    <span class="kid-tag">Child price</span>
                    <div class="field"><label for="child_percent">% of the adult price</label><input type="number" inputmode="decimal" min="0" max="100" step="1" id="child_percent" name="child_percent" value="{{ $v('child_percent') }}" data-kid="percent" class="@error('child_percent') is-invalid @enderror"></div>
                    <p class="kid-say" data-kid-say="percent"></p>
                </div>
                <div class="kid-band">
                    <span class="kid-tag">Adult price</span>
                    <div class="field"><label for="child_adult_age">From age</label><input type="number" inputmode="numeric" min="1" max="18" id="child_adult_age" name="child_adult_age" value="{{ $v('child_adult_age', 12) }}" data-kid="adult" class="@error('child_adult_age') is-invalid @enderror"></div>
                    <p class="kid-say" data-kid-say="adult"></p>
                </div>
                @error('child_adult_age')<p class="err">{{ $message }}</p>@enderror
                <p class="hint" style="margin:16px 0 0;">The child % is of everything an adult pays: their half of a double room and the tour price.</p>
            </div>

            <div class="estimate" id="estimate" aria-live="polite">
                <div class="est-head">
                    <h3>Check your prices</h3>
                    <p class="hint" style="margin:0;">What we pay you, worked out from sections 3 and 4. Our margin is added on top for travellers.</p>
                </div>
                <div id="estimateBody"><p class="hint" style="margin:0;">Add a hotel with its double room price and a trip length to see totals here.</p></div>
            </div>
        </section>

        {{-- 5 --}}
        <section class="sec" id="extras">
            <div class="sec-head"><h2><span class="num">5</span>Extras</h2><p>Things travellers can add, like a guide in another language, a bigger car, an airport transfer or meals. Something already included? Enter 0.</p></div>
            <div class="rows" id="extraRows">
                @foreach($extras as $i => $e)
                    @include('suppliers._extra-row', ['i' => $i, 'e' => $e])
                @endforeach
            </div>
            <template id="extraTpl">@include('suppliers._extra-row', ['i' => '__I__', 'e' => []])</template>
            <button type="button" class="btn btn-ghost add" data-add="extra">+ Add an extra</button>
        </section>

        {{-- 6 --}}
        <section class="sec" id="days">
            <div class="sec-head"><h2><span class="num">6</span>Day-by-day plan</h2><p>What happens each day. Travellers see this before they book.</p></div>
            <div class="rows" id="dayRows">
                @foreach($days as $i => $d)
                    @include('suppliers._day-row', ['i' => $i, 'd' => $d])
                @endforeach
            </div>
            <template id="dayTpl">@include('suppliers._day-row', ['i' => '__I__', 'd' => []])</template>
            <button type="button" class="btn btn-ghost add" data-add="day">+ Add a day</button>
        </section>

        {{-- 7 --}}
        <section class="sec" id="seasons">
            <div class="sec-head"><h2><span class="num">7</span>Peak seasons</h2><p>Dates when your prices are higher, and by how much. Leave empty if your prices are the same all year.</p></div>
            <div class="rows" id="seasonRows">
                @foreach($seasons as $i => $x)
                    @include('suppliers._season-row', ['i' => $i, 'x' => $x])
                @endforeach
            </div>
            <template id="seasonTpl">@include('suppliers._season-row', ['i' => '__I__', 'x' => []])</template>
            <button type="button" class="btn btn-ghost add" data-add="season">+ Add a peak season</button>
        </section>

        {{-- 8 --}}
        <section class="sec" id="photos">
            <div class="sec-head"><h2><span class="num">8</span>Photos and notes</h2><p>Hotel rooms, places on the tour, the car. Up to {{ $maxPhotos }} photos, JPG, PNG or WEBP, 8 MB each.</p></div>
            <p class="hint" style="margin:0 0 16px;"><strong>Cover photo:</strong> choose the one travellers see first, at the top of your package. If you don't choose, the first photo is used.</p>
            @php $coverPick = old('cover', $photos ? 'old:0' : ''); @endphp
            @if($photos)
                <p class="label" style="margin:0 0 8px;">Photos you sent before <span class="hint">(untick to remove)</span></p>
                <div class="photos-old">
                    @foreach($photos as $i => $p)
                        <div class="ph">
                            <label class="ph-img" title="{{ $p['name'] }}"><input type="checkbox" name="keep_photos[]" value="{{ $i }}" checked data-keep aria-label="Keep {{ $p['name'] }}"><img src="{{ route('suppliers.photo', $i) }}" alt="{{ $p['name'] }}" loading="lazy"></label>
                            <label class="ph-cover"><input type="radio" name="cover" value="old:{{ $i }}" @checked($coverPick === "old:$i")> Cover</label>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="drop">
                <input type="file" name="photos[]" id="photoInput" accept="image/jpeg,image/png,image/webp" multiple aria-label="Add photos">
                <p class="hint" id="photoNote" style="margin:8px 0 0;">Pick several at once.</p>
            </div>
            <div class="photos-old photos-new" id="newPhotos"></div>
            @error('photos')<p class="err">{{ $message }}</p>@enderror
            <div class="grid2" style="margin-top:24px;">
                <div class="field span2"><label for="photo_link">Link to more photos <span class="hint">(Google Drive, Dropbox…)</span></label><input type="url" id="photo_link" name="photo_link" value="{{ $v('photo_link') }}" placeholder="https://" class="@error('photo_link') is-invalid @enderror"></div>
                <div class="field span2"><label for="notes">Anything else we should know</label><textarea id="notes" name="notes" maxlength="3000" placeholder="Cancellation terms, payment terms, minimum group size…">{{ $v('notes') }}</textarea></div>
            </div>
        </section>

        <div class="send">
            <div class="send-in">
                <span class="hint">You can change and send it again any time with your code.</span>
                <button class="btn btn-primary" id="sendBtn">{{ $last ? 'Send updated rates' : 'Send my rates' }}</button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var form = document.getElementById('supplierForm');
    var counters = { tour: {{ count($tours) }}, extra: {{ count($extras) }}, day: {{ count($days) }}, season: {{ count($seasons) }} };
    var boxes = { tour: 'tourRows', extra: 'extraRows', day: 'dayRows', season: 'seasonRows' };
    var prefix = { tour: 'tours', extra: 'extras', day: 'days', season: 'seasons' };

    function addRow(kind) {
        var tpl = document.getElementById(kind + 'Tpl').innerHTML.replace(/__I__/g, counters[kind]++);
        var box = document.getElementById(boxes[kind]);
        box.insertAdjacentHTML('beforeend', tpl);
        var row = box.lastElementChild;
        if (kind === 'day') {
            var n = row.querySelector('input[type=number]');
            if (n && !n.value) { n.value = box.children.length; }
        }
        bands(); meals(row);
        return row;
    }
    document.querySelectorAll('[data-add]').forEach(function (b) {
        b.addEventListener('click', function () { var r = addRow(b.dataset.add); var f = r.querySelector('input, select'); if (f) f.focus(); });
    });
    form.addEventListener('click', function (e) {
        var rm = e.target.closest('[data-remove]');
        if (!rm) return;
        var row = rm.closest('.row'), box = row.parentElement;
        if (box.children.length > 1) { row.remove(); } else { row.querySelectorAll('input, textarea').forEach(function (i) { i.value = ''; }); }
        save();
    });

    // Group prices
    var gp = document.getElementById('groupPricing');
    function bands() {
        document.querySelectorAll('[data-band]').forEach(function (el) { el.classList.toggle('show', gp.checked); });
        document.getElementById('tourRows').classList.toggle('grouped', gp.checked);
        document.querySelectorAll('[data-first-band-label]').forEach(function (el) { el.textContent = gp.checked ? el.dataset.groupLabel : el.dataset.oneLabel; });
    }
    gp.addEventListener('change', bands);

    // Meal extras: ask which meal plan
    function meals(scope) {
        (scope || document).querySelectorAll('[data-extra-cat]').forEach(function (sel) {
            var w = sel.closest('.row').querySelector('[data-meal-wrap]');
            w.style.display = sel.value === 'meal_plan' ? '' : 'none';
        });
    }
    form.addEventListener('change', function (e) { if (e.target.matches('[data-extra-cat]')) meals(e.target.closest('.row')); });

    // Currency labels
    var cur = document.getElementById('currency');
    cur.addEventListener('change', function () { document.querySelectorAll('[data-cur]').forEach(function (el) { el.textContent = cur.value; }); });

    // Hotel header shows the name
    form.addEventListener('input', function (e) {
        if (e.target.matches('[data-hotel-name]')) {
            document.querySelector('[data-hotel-state="' + e.target.dataset.hotelName + '"]').textContent = e.target.value.trim() || 'Not offered';
        }
    });

    // Photos: count and size check before sending
    var max = {{ $maxPhotos }}, input = document.getElementById('photoInput'), note = document.getElementById('photoNote');
    function photoCheck() {
        var kept = document.querySelectorAll('[data-keep]:checked').length, files = Array.from(input.files || []);
        var big = files.filter(function (f) { return f.size > 8 * 1024 * 1024; });
        var msg = files.length ? files.length + ' new photo' + (files.length > 1 ? 's' : '') + ' picked.' : 'Pick several at once.';
        var bad = '';
        if (big.length) bad = big.map(function (f) { return f.name; }).join(', ') + ' is over 8 MB.';
        else if (kept + files.length > max) bad = 'That is ' + (kept + files.length) + ' photos; the most is ' + max + '. Add a link for the rest.';
        note.textContent = bad || msg;
        note.className = bad ? 'err' : 'hint';
        return !bad;
    }
    var newBox = document.getElementById('newPhotos'), urls = [];
    function previews() {
        urls.forEach(function (u) { URL.revokeObjectURL(u); }); urls = [];
        var picked = (form.querySelector('input[name=cover]:checked') || {}).value || '';
        newBox.innerHTML = '';
        Array.from(input.files || []).forEach(function (f, j) {
            var u = URL.createObjectURL(f); urls.push(u);
            var d = document.createElement('div'); d.className = 'ph';
            d.innerHTML = '<span class="ph-img"><img alt=""></span><label class="ph-cover"><input type="radio" name="cover" value="new:' + j + '"> Cover</label>';
            d.querySelector('img').src = u; d.querySelector('img').alt = f.name;
            newBox.appendChild(d);
        });
        // Keep the choice if it still exists, else the first photo
        var still = picked && form.querySelector('input[name=cover][value="' + picked + '"]');
        var first = still || form.querySelector('input[name=cover]');
        if (first) first.checked = true;
    }
    input.addEventListener('change', function () { photoCheck(); previews(); });
    document.querySelectorAll('[data-keep]').forEach(function (k) { k.addEventListener('change', photoCheck); });

    // Children's age bands, said in words
    function num(el) { var x = el ? parseFloat(el.value) : NaN; return isNaN(x) ? null : x; }
    var kidEl = { free: document.getElementById('child_free_age'), percent: document.getElementById('child_percent'), adult: document.getElementById('child_adult_age') };
    function kids() {
        var free = num(kidEl.free), pct = num(kidEl.percent), adult = num(kidEl.adult);
        var say = function (k, t) { document.querySelector('[data-kid-say="' + k + '"]').textContent = t; };
        say('free', free === null ? 'No free age.' : 'Ages 0 to ' + free + ' travel free.');
        var from = free === null ? 0 : free + 1, to = adult === null ? null : adult - 1;
        say('percent', pct === null ? 'Enter a %.' : (to !== null && to < from ? 'No child price band.' : 'Ages ' + from + (to === null ? ' and up' : ' to ' + to) + ' pay ' + pct + '% of the adult price.'));
        say('adult', adult === null ? 'Children never pay the adult price.' : 'From age ' + adult + ', the full adult price.');
    }

    // Totals from the room and tour prices, so suppliers see what they entered adds up
    function money(x) { var whole = Math.round(x) === x; return (cur.value || '') + ' ' + x.toLocaleString(undefined, { minimumFractionDigits: whole ? 0 : 2, maximumFractionDigits: 2 }); }
    function estimate() {
        var hotels = [];
        @foreach($stars as $s)
            (function (s) {
                var name = (document.getElementById('h' + s + '_name').value || '').trim();
                var dbl = num(document.getElementById('h' + s + '_double'));
                if (name && dbl > 0) hotels.push({ stars: s, name: name, double: dbl, single: num(document.getElementById('h' + s + '_single')), extra: num(document.getElementById('h' + s + '_extra_bed')), triple: num(document.getElementById('h' + s + '_triple')) });
            })({{ $s }});
        @endforeach
        var trips = [];
        document.querySelectorAll('[data-tour-row]').forEach(function (r) {
            var d = num(r.querySelector('[data-tour-days]'));
            if (!(d >= 1)) return;
            var p = {}; r.querySelectorAll('[data-tour-price]').forEach(function (i) { var x = num(i); if (x !== null) p[i.dataset.tourPrice] = x; });
            if (p['1'] === undefined && !Object.keys(p).length) return;
            trips.push({ days: d, prices: p });
        });
        var body = document.getElementById('estimateBody');
        if (!hotels.length && !trips.length) { body.innerHTML = '<p class="hint" style="margin:0;">Add a hotel with its double room price and a trip length to see totals here.</p>'; return; }
        if (!trips.length) trips = [{ days: 2, prices: { '1': 0 }, guess: true }];
        if (!hotels.length) hotels = [{ stars: 0, name: 'No hotel', double: 0, single: 0 }];

        var bandsOn = gp.checked, sizes = [1, 2, 3, 5, 7, 10];
        function tourPP(t, group) { // price per person for a group of this size: the band it falls in, else the one to its left
            var best = t.prices['1'] || 0;
            sizes.forEach(function (g) { if ((bandsOn || g === 1) && g <= group && t.prices[String(g)] !== undefined) best = t.prices[String(g)]; });
            return best;
        }
        var pct = (num(kidEl.percent) || 0) / 100, kidAge = (num(kidEl.free) === null ? 0 : num(kidEl.free) + 1);
        var parties = [
            { who: '1 adult', note: 'alone in the room', f: function (h, n, t) { return (h.single > 0 ? h.single : h.double) * n + tourPP(t, 1); } },
            { who: '2 adults', note: 'sharing a double', f: function (h, n, t) { return h.double * n + 2 * tourPP(t, 2); } },
            { who: '2 adults + 1 child', note: 'child aged ' + kidAge + ', at ' + Math.round(pct * 100) + '%', f: function (h, n, t) {
                var room = h.triple > 0 ? h.triple : (h.extra > 0 ? h.double + h.extra : h.double + h.double / 2 * pct);
                return room * n + (2 + pct) * tourPP(t, 3); } }
        ];
        var html = '';
        trips.forEach(function (t) {
            var n = Math.max(1, t.days - 1);
            html += '<table class="est-table"><thead><tr><th>' + (t.guess ? 'Per ' + n + ' night' + (n > 1 ? 's' : '') : t.days + ' days, ' + n + ' night' + (n > 1 ? 's' : '')) + '</th>';
            hotels.forEach(function (h) { html += '<th class="n">' + (h.stars ? h.stars + '★ ' : '') + escapeHtml(h.name) + '</th>'; });
            html += '</tr></thead><tbody>';
            parties.forEach(function (p) {
                html += '<tr><td>' + p.who + '<small>' + p.note + '</small></td>';
                hotels.forEach(function (h) { html += '<td class="n"><strong>' + money(Math.round(p.f(h, n, t) * 100) / 100) + '</strong></td>'; });
                html += '</tr>';
            });
            html += '</tbody></table>';
        });
        var allIn = hotels.some(function (h) { return h.double > 0; }) && trips.some(function (t) { return !t.guess && tourPP(t, 2) > 0; });
        html += '<p class="hint" style="margin:16px 0 0;">Room prices × nights, plus the tour price per person.' + (allIn ? ' If your room price already includes the tours (a cruise, for example), set the tour price to 0 so it is not charged twice.' : '') + '</p>';
        body.innerHTML = html;
    }
    function escapeHtml(t) { var d = document.createElement('div'); d.textContent = t; return d.innerHTML; }
    form.addEventListener('input', function (e) { if (e.target.closest('#hotels, #tours')) { estimate(); kids(); } });
    form.addEventListener('change', function (e) { if (e.target.closest('#hotels, #tours') || e.target === cur) { estimate(); kids(); } });
    form.addEventListener('click', function (e) { if (e.target.closest('[data-remove]')) setTimeout(estimate, 0); });

    // Tour price rows: point at the exact empty box
    function mark(el, msg) {
        el.classList.add('is-invalid');
        var f = el.closest('.field');
        if (f && !f.querySelector('.err[data-js]')) { var sp = document.createElement('span'); sp.className = 'err'; sp.dataset.js = '1'; sp.textContent = msg; f.appendChild(sp); }
    }
    form.addEventListener('input', function (e) {
        if (e.target.classList.contains('is-invalid') && e.target.value.trim()) {
            e.target.classList.remove('is-invalid');
            var sp = e.target.closest('.field') && e.target.closest('.field').querySelector('.err'); if (sp) sp.remove();
        }
    });
    function checkTours() {
        var bad = null, seen = {};
        document.querySelectorAll('[data-tour-row]').forEach(function (r, i) {
            var d = r.querySelector('[data-tour-days]'), p1 = r.querySelector('[data-tour-price="1"]');
            var anyPrice = Array.from(r.querySelectorAll('[data-tour-price]')).some(function (x) { return x.value.trim() !== ''; });
            if (!d.value.trim() && !anyPrice) return;
            if (!d.value.trim() || !(parseInt(d.value, 10) >= 1)) { mark(d, 'Enter the number of days.'); bad = bad || d; }
            else if (seen[d.value]) { mark(d, 'There are two rows for ' + d.value + ' days.'); bad = bad || d; }
            else if (!anyPrice) { mark(p1, 'Enter the price per person (0 if you only sell the hotel).'); bad = bad || p1; }
            seen[d.value] = true;
        });
        return bad;
    }

    // Section highlight
    var links = document.querySelectorAll('.toc a');
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (es) {
            es.forEach(function (en) {
                if (en.isIntersecting) links.forEach(function (a) { a.classList.toggle('on', a.getAttribute('href') === '#' + en.target.id); });
            });
        }, { rootMargin: '-40% 0px -55% 0px' });
        document.querySelectorAll('.sec').forEach(function (s) { io.observe(s); });
    }

    // Unsent changes stay in this browser until sent
    var key = 'gt-supplier-draft-{{ $invite->id }}-{{ $last?->id ?? 0 }}';
    var store = { get: function () { try { return JSON.parse(localStorage.getItem(key) || 'null'); } catch (e) { return null; } },
                  set: function (v) { try { localStorage.setItem(key, JSON.stringify(v)); } catch (e) {} },
                  del: function () { try { localStorage.removeItem(key); } catch (e) {} } };
    function snapshot() {
        var out = [];
        Array.from(form.elements).forEach(function (el) {
            if (!el.name || el.type === 'file' || el.name === '_token') return;
            if (el.type === 'checkbox' || el.type === 'radio') out.push([el.name, el.value, el.checked]);
            else out.push([el.name, el.value]);
        });
        return out;
    }
    var timer, dirty = false;
    function save() { dirty = true; clearTimeout(timer); timer = setTimeout(function () { store.set({ at: Date.now(), fields: snapshot() }); }, 400); }
    form.addEventListener('input', save);
    form.addEventListener('change', save);

    function ensureRow(name) {
        var m = name.match(/^(tours|extras|days|seasons)\[(\d+)\]/);
        if (!m) return;
        var kind = Object.keys(prefix).find(function (k) { return prefix[k] === m[1]; });
        var guard = 0;
        while (!form.querySelector('[name^="' + m[1] + '[' + m[2] + ']"]') && guard++ < 60) { addRow(kind); }
    }
    function restore(d) {
        d.fields.forEach(function (f) { ensureRow(f[0]); });
        d.fields.forEach(function (f) {
            var els = form.querySelectorAll('[name="' + CSS.escape(f[0]) + '"]');
            els.forEach(function (el) {
                if (el.type === 'checkbox' || el.type === 'radio') { if (el.value === f[1]) el.checked = !!f[2]; }
                else el.value = f[1];
            });
        });
        bands(); meals(); kids(); estimate();
        document.querySelectorAll('[data-hotel-name]').forEach(function (el) { el.dispatchEvent(new Event('input', { bubbles: true })); });
        cur.dispatchEvent(new Event('change'));
    }
    var draft = store.get(), notice = document.getElementById('draftNotice');
    var hasErrors = {{ $errors->any() ? 'true' : 'false' }};
    if (draft && !hasErrors) {
        notice.classList.add('show');
        document.getElementById('draftRestore').addEventListener('click', function () { restore(draft); notice.classList.remove('show'); });
        document.getElementById('draftDiscard').addEventListener('click', function () { store.del(); notice.classList.remove('show'); });
    }

    var sending = false;
    form.addEventListener('submit', function (e) {
        if (!photoCheck()) { e.preventDefault(); input.scrollIntoView({ behavior: 'smooth', block: 'center' }); return; }
        var missing = Array.from(form.querySelectorAll('[required]')).find(function (el) { return !el.value.trim(); }) || checkTours();
        if (missing) {
            e.preventDefault();
            missing.classList.add('is-invalid');
            missing.scrollIntoView({ behavior: 'smooth', block: 'center' });
            setTimeout(function () { missing.focus(); }, 300);
            return;
        }
        store.set({ at: Date.now(), fields: snapshot() }); // kept until the thank-you page
        sending = true;
        var b = document.getElementById('sendBtn'); b.disabled = true; b.textContent = 'Sending…';
    });
    window.addEventListener('beforeunload', function (e) { if (dirty && !sending) { e.preventDefault(); e.returnValue = ''; } });

    bands(); meals(); kids(); estimate();
    var firstErr = document.querySelector('.sec .is-invalid');
    if (firstErr) { firstErr.scrollIntoView({ block: 'center' }); }
})();
</script>
@endpush
