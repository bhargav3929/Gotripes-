@extends('layouts.manager')

@section('title', 'Supplier rates · ' . $submission->get('package.name'))
@section('page-title', 'Supplier forms')

@push('styles')
<style>
    .sf-layout { display:grid; grid-template-columns:minmax(0, 1fr) 340px; gap:16px; align-items:start; }
    .sf-apply { position:sticky; top:16px; }
    .sf-dl { display:grid; grid-template-columns:160px 1fr; gap:8px 16px; font-size:14px; margin:0; }
    .sf-dl dt { color:var(--wp-text-muted); font-weight:400; }
    .sf-dl dd { margin:0; white-space:pre-line; }
    .sf-money { white-space:nowrap; text-align:right; }
    .sf-money small { display:block; color:var(--wp-text-muted); }
    .sf-was { color:var(--wp-text-muted); text-decoration:line-through; font-size:12px; display:block; }
    .sf-photos { display:grid; grid-template-columns:repeat(auto-fill, minmax(140px, 1fr)); gap:8px; }
    .sf-photos a { display:block; border-radius:8px; overflow:hidden; aspect-ratio:4/3; background:#1a1a1a; }
    .sf-photos img { width:100%; height:100%; object-fit:cover; }
    .sf-check { display:flex; gap:8px; align-items:center; font-size:13px; margin-bottom:8px; cursor:pointer; }
    .sf-check input:disabled + span { opacity:.45; }
    @media (max-width: 1100px) { .sf-layout { grid-template-columns:1fr; } .sf-apply { position:static; } }
</style>
@endpush

@section('content')
@php
    $s = $submission;
    $cur = $currency;
    $prev = $previous;
    $money = fn ($v) => $v === null || $v === '' ? null : rtrim(rtrim(number_format((float) $v, 2), '0'), '.');
    // A price cell: the supplier's price, the AED it becomes, and the old price if it changed since their last send.
    $cell = function ($v, $old = '__none') use ($money, $cur) {
        if ($v === null || $v === '') {
            return '<span class="text-muted-wp">–</span>';
        }
        $html = e($cur . ' ' . $money($v)) . '<small data-aed="' . e((float) $v) . '"></small>';
        if ($old !== '__none' && (string) $money($old) !== (string) $money($v)) {
            $html .= '<span class="sf-was">was ' . e($old === null ? 'empty' : $money($old)) . '</span>';
        }
        return $html;
    };
    $prevTour = fn ($days, $g) => $prev ? collect($prev->get('tours', []))->firstWhere('days', $days)["g$g"] ?? null : '__none';
    $groupCols = $s->get('group_pricing') ? array_keys($groups) : [1];
@endphp

<div class="wp-page-header">
    <div>
        <a href="{{ route('manager.supplier-forms.index') }}" class="text-muted-wp" style="font-size:13px;"><i class="fas fa-arrow-left"></i> Supplier forms</a>
        <h1 class="wp-page-title">{{ $s->get('package.name') }}</h1>
        <p class="text-muted-wp" style="margin:4px 0 0;">
            {{ $s->get('package.country') }} · from {{ $s->invite?->supplier_name }} · {{ $s->created_at->format('j M Y, H:i') }}
            @if($prev) · <strong>updated rates</strong> (changes since {{ $prev->created_at->format('j M') }} are marked) @endif
        </p>
    </div>
    @if($s->status === 'applied')
        <span class="wp-badge wp-badge-green" style="align-self:center;">Applied {{ $s->applied_at?->format('j M Y') }}@if($s->appliedPackage) to {{ $s->appliedPackage->title }}@endif</span>
    @endif
</div>

<div class="sf-layout">
<div>
    <div class="wp-card">
        <div class="wp-card-header"><i class="fas fa-building text-secondary-wp"></i> Supplier</div>
        <div class="wp-card-body">
            <dl class="sf-dl">
                <dt>Company</dt><dd>{{ $s->get('company.name') }}</dd>
                <dt>Contact</dt><dd>{{ $s->get('company.contact') }}</dd>
                <dt>Email</dt><dd><a href="mailto:{{ $s->get('company.email') }}">{{ $s->get('company.email') }}</a></dd>
                <dt>Phone / WhatsApp</dt><dd>{{ $s->get('company.phone') }}</dd>
            </dl>
        </div>
    </div>

    <div class="wp-card">
        <div class="wp-card-header"><i class="fas fa-suitcase-rolling text-secondary-wp"></i> Package</div>
        <div class="wp-card-body">
            <dl class="sf-dl">
                <dt>Name</dt><dd>{{ $s->get('package.name') }}</dd>
                <dt>Country</dt><dd>{{ $s->get('package.country') }}</dd>
                @foreach(['places' => 'Places covered', 'description' => 'Description', 'included' => 'Included', 'excluded' => 'Not included'] as $k => $l)
                    @if(filled($s->get("package.$k")))<dt>{{ $l }}</dt><dd>{{ $s->get("package.$k") }}</dd>@endif
                @endforeach
                <dt>Prices valid</dt><dd>{{ $s->get('package.valid_from') ? \Carbon\Carbon::parse($s->get('package.valid_from'))->format('j M Y') : 'now' }} – {{ $s->get('package.valid_to') ? \Carbon\Carbon::parse($s->get('package.valid_to'))->format('j M Y') : 'until changed' }}</dd>
                <dt>Prices in</dt><dd>{{ $cur }}</dd>
            </dl>
        </div>
    </div>

    <div class="wp-card">
        <div class="wp-card-header"><i class="fas fa-bed text-secondary-wp"></i> Hotels and room prices <span class="text-muted-wp" style="font-weight:400;">· net, per room, per night</span></div>
        <div class="wp-card-body" style="padding:0;">
            @if(!$s->hotels())
                <p class="text-muted-wp" style="padding:16px 24px; margin:0;">No hotels: this supplier only sells the tours.</p>
            @else
            <div class="table-responsive">
                <table class="wp-table">
                    <thead><tr><th>Level</th><th>Hotel</th><th class="sf-money">Single</th><th class="sf-money">Double</th><th class="sf-money">Triple</th><th class="sf-money">Extra bed</th><th>Meals · times</th></tr></thead>
                    <tbody>
                        @foreach(collect($s->hotels())->sortKeysDesc() as $stars => $h)
                            @php $ph = $prev ? ($prev->hotels()[$stars] ?? []) : null; @endphp
                            <tr>
                                <td style="color:var(--wp-primary); white-space:nowrap;">{{ str_repeat('★', $stars) }}</td>
                                <td>{{ $h['name'] }}<br><small class="text-muted-wp">{{ $h['location'] }}</small></td>
                                @foreach(['single', 'double', 'triple', 'extra_bed'] as $f)
                                    <td class="sf-money">{!! $cell($h[$f] ?? null, $ph === null ? '__none' : ($ph[$f] ?? null)) !!}</td>
                                @endforeach
                                <td style="font-size:13px;">
                                    {{ collect($h['meals'] ?? [])->map(fn ($m) => $mealPlans[$m] ?? $m)->join(', ') ?: 'Breakfast' }}<br>
                                    <small class="text-muted-wp">In {{ $h['check_in'] ?? '14:00' }} · out {{ $h['check_out'] ?? '12:00' }}</small>
                                    @if(filled($h['late_policy'] ?? null))<br><small class="text-muted-wp">{{ $h['late_policy'] }}</small>@endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <div class="wp-card">
        <div class="wp-card-header"><i class="fas fa-route text-secondary-wp"></i> Tour price <span class="text-muted-wp" style="font-weight:400;">· per person, whole trip, everything except the hotel</span></div>
        <div class="wp-card-body" style="padding:0;">
            @if(!$s->get('tours'))
                <p class="text-muted-wp" style="padding:16px 24px; margin:0;">No tour prices.</p>
            @else
            <div class="table-responsive">
                <table class="wp-table">
                    <thead><tr><th>Trip length</th>@foreach($groupCols as $g)<th class="sf-money">{{ count($groupCols) > 1 ? 'Group of ' . $groups[$g] : 'Per person' }}</th>@endforeach</tr></thead>
                    <tbody>
                        @foreach($s->get('tours') as $t)
                            <tr>
                                <td>{{ $t['days'] }} days</td>
                                @foreach($groupCols as $g)<td class="sf-money">{!! $cell($t["g$g"] ?? null, $prevTour($t['days'], $g)) !!}</td>@endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
            <p class="text-muted-wp" style="padding:12px 24px; margin:0; font-size:13px;">Children pay <strong>{{ $s->get('child_percent') ?? '–' }}%</strong> of the adult tour price · children up to age <strong>{{ $s->get('child_free_age') ?? '–' }}</strong> share the parents' room free.</p>
        </div>
    </div>

    @if($s->get('extras'))
    <div class="wp-card">
        <div class="wp-card-header"><i class="fas fa-plus-circle text-secondary-wp"></i> Extras customers can add</div>
        <div class="wp-card-body" style="padding:0;">
            <div class="table-responsive">
                <table class="wp-table">
                    <thead><tr><th>Type</th><th>Extra</th><th class="sf-money">Price</th><th>Charged</th></tr></thead>
                    <tbody>
                        @foreach($s->get('extras') as $e)
                            <tr>
                                <td>{{ $categories[$e['category']] ?? $e['category'] }}@if($e['meal_code'] ?? null)<br><small class="text-muted-wp">{{ $mealPlans[$e['meal_code']] ?? '' }}</small>@endif</td>
                                <td>{{ $e['name'] }}</td>
                                <td class="sf-money">{!! (float) $e['price'] === 0.0 ? 'Included' : $cell($e['price']) !!}</td>
                                <td>{{ $bases[$e['basis']] ?? $e['basis'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if($s->get('days'))
    <div class="wp-card">
        <div class="wp-card-header"><i class="fas fa-list-ol text-secondary-wp"></i> Day-by-day plan</div>
        <div class="wp-card-body">
            <dl class="sf-dl">
                @foreach($s->get('days') as $d)
                    <dt>Day {{ $d['day'] }}</dt><dd><strong>{{ $d['title'] }}</strong>@if(filled($d['description']))<br>{{ $d['description'] }}@endif</dd>
                @endforeach
            </dl>
        </div>
    </div>
    @endif

    @if($s->get('seasons'))
    <div class="wp-card">
        <div class="wp-card-header"><i class="fas fa-sun text-secondary-wp"></i> Peak seasons</div>
        <div class="wp-card-body" style="padding:0;">
            <table class="wp-table">
                <thead><tr><th>Name</th><th>From</th><th>To</th><th class="sf-money">Extra</th></tr></thead>
                <tbody>
                    @foreach($s->get('seasons') as $x)
                        <tr><td>{{ $x['name'] }}</td><td>{{ \Carbon\Carbon::parse($x['from'])->format('j M Y') }}</td><td>{{ \Carbon\Carbon::parse($x['to'])->format('j M Y') }}</td><td class="sf-money">+{{ $money($x['percent']) }}%</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="wp-card">
        <div class="wp-card-header" style="display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap;">
            <span><i class="fas fa-images text-secondary-wp"></i> Photos <span class="text-muted-wp" style="font-weight:400;">· {{ count((array) $s->photos) }}</span></span>
            @if($s->photos)
                <a href="{{ route('manager.supplier-forms.photos-zip', $s->id) }}" class="wp-btn wp-btn-secondary wp-btn-sm"><i class="fas fa-download"></i> Download all</a>
            @endif
        </div>
        <div class="wp-card-body">
            @if($s->photos)
                <div class="sf-photos">
                    @foreach($s->photos as $i => $p)
                        <a href="{{ route('manager.supplier-forms.photo', [$s->id, $i]) }}?download=1" title="Download {{ $p['name'] }}"><img src="{{ route('manager.supplier-forms.photo', [$s->id, $i]) }}" alt="{{ $p['name'] }}" loading="lazy"></a>
                    @endforeach
                </div>
            @endif
            @if(filled($s->get('photo_link')))
                <p style="margin:12px 0 0;">More photos: <a href="{{ $s->get('photo_link') }}" target="_blank" rel="noopener noreferrer">{{ \Illuminate\Support\Str::limit($s->get('photo_link'), 70) }}</a></p>
            @endif
            @if(!$s->photos && !filled($s->get('photo_link')))
                <p class="text-muted-wp" style="margin:0;">No photos sent.</p>
            @endif
        </div>
    </div>

    @if(filled($s->get('notes')))
    <div class="wp-card">
        <div class="wp-card-header"><i class="fas fa-sticky-note text-secondary-wp"></i> Notes from the supplier</div>
        <div class="wp-card-body" style="white-space:pre-line;">{{ $s->get('notes') }}</div>
    </div>
    @endif
</div>

<div class="sf-apply">
    <div class="wp-card">
        <div class="wp-card-header"><i class="fas fa-magic text-secondary-wp"></i> Fill a package with these rates</div>
        <div class="wp-card-body">
            <form method="POST" action="{{ route('manager.supplier-forms.apply', $s->id) }}" id="applyForm">
                @csrf
                <div class="wp-form-group">
                    <label class="wp-form-label" for="target">Package</label>
                    <select class="wp-input" id="target" name="target" required>
                        <option value="new" @selected(!$target)>Create a new package from this</option>
                        @foreach($packages as $p)
                            <option value="{{ $p->id }}" @selected((int) old('target', $target) === $p->id)>{{ $p->title }} · {{ $p->country }}</option>
                        @endforeach
                    </select>
                    <p class="wp-form-help" id="newHelp">Uses the supplier's package name, description and photos, with a 15% margin. You can change any of it afterwards.</p>
                </div>
                <div class="wp-form-group">
                    <label class="wp-form-label" for="rate">Exchange rate: 1 {{ $cur }} = ? AED</label>
                    <input type="number" step="0.0001" min="0.0001" class="wp-input" id="rate" name="rate" value="{{ old('rate', $rate) }}" @readonly($cur === 'AED') required>
                    <p class="wp-form-help">Prices are saved in AED. The AED amount is shown under every price on this page.</p>
                </div>
                <div class="wp-form-group">
                    <span class="wp-form-label">Fill in</span>
                    @foreach($sections as $key => $label)
                        <label class="sf-check"><input type="checkbox" name="sections[]" value="{{ $key }}" @checked(isset($available[$key])) @disabled(!isset($available[$key]))><span>{{ $label }}@unless(isset($available[$key])) <small>(none sent)</small>@endunless</span></label>
                    @endforeach
                    <p class="wp-form-help">Each ticked part replaces that part of the package. Your travel insurance options are kept.</p>
                </div>
                <button class="wp-btn wp-btn-primary" style="width:100%;"><i class="fas fa-check"></i> {{ $s->status === 'applied' ? 'Apply again' : 'Apply to package' }}</button>
                <p class="wp-form-help" style="margin-top:8px;">You land on the package's Booking setup to check the Price check table.</p>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var rate = document.getElementById('rate'), cur = @json($cur);
    function paint() {
        var r = parseFloat(rate.value) || 0;
        document.querySelectorAll('[data-aed]').forEach(function (el) {
            el.textContent = cur === 'AED' || !r ? '' : '≈ AED ' + (parseFloat(el.dataset.aed) * r).toLocaleString('en', { maximumFractionDigits: 2 });
        });
    }
    rate.addEventListener('input', paint); paint();
    var target = document.getElementById('target'), help = document.getElementById('newHelp');
    function toggle() { help.style.display = target.value === 'new' ? '' : 'none'; }
    target.addEventListener('change', toggle); toggle();
    document.getElementById('applyForm').addEventListener('submit', function (e) {
        if (target.value !== 'new' && !confirm('Replace the ticked parts of "' + target.options[target.selectedIndex].text + '" with these rates?')) { e.preventDefault(); }
    });
})();
</script>
@endpush
