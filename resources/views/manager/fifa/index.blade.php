@extends('layouts.manager')

@section('title', 'FIFA World Cup 2026 Tickets')
@section('page-title', 'FIFA World Cup 2026 Tickets')

@push('styles')
<style>
    .fifa-markup-card { display:flex; flex-wrap:wrap; align-items:center; gap:18px; }
    .fifa-markup-card .markup-input-wrap { position:relative; max-width:160px; }
    .fifa-markup-card .markup-input-wrap input { padding-right:30px; text-align:right; font-weight:700; font-size:18px; }
    .fifa-markup-card .markup-input-wrap .pct { position:absolute; right:12px; top:50%; transform:translateY(-50%); color:var(--wp-text-muted); font-weight:600; }
    .fifa-stage-head { font-size:13px; font-weight:700; letter-spacing:.4px; text-transform:uppercase; color:var(--wp-text-muted); margin:28px 0 12px; display:flex; align-items:center; gap:10px; }
    .fifa-stage-head::after { content:''; flex:1; height:1px; background:var(--wp-border-light); }
    .fifa-match-card { margin-bottom:14px; }
    .fifa-match-top { display:flex; align-items:center; gap:12px; padding:12px 16px; border-bottom:1px solid var(--wp-border-light); }
    .fifa-match-code { font-size:11px; font-weight:700; color:var(--wp-primary); background:rgba(255,215,0,.12); padding:2px 8px; border-radius:4px; flex-shrink:0; }
    .fifa-match-title { font-weight:700; color:var(--wp-text); font-size:15px; }
    .fifa-match-actions { margin-left:auto; display:flex; gap:6px; align-items:center; }
    .fifa-cost { color:var(--wp-text-secondary); }
    .fifa-sell { color:var(--wp-primary); font-weight:700; }
    .fifa-inactive { opacity:.5; }
</style>
@endpush

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">FIFA World Cup 2026 Tickets</h1>
    <div style="display:flex; gap:8px;">
        <a href="{{ route('manager.fifa-tickets.requests') }}" class="wp-btn wp-btn-secondary">
            <i class="fas fa-inbox"></i> Customer Requests
            @if($newCount > 0)<span class="wp-badge wp-badge-amber" style="margin-left:6px;">{{ $newCount }} new</span>@endif
        </a>
        <button type="button" class="wp-btn wp-btn-primary" data-bs-toggle="modal" data-bs-target="#addMatchModal">
            <i class="fas fa-plus"></i> Add Match
        </button>
    </div>
</div>

{{-- ── Global profit margin ─────────────────────── --}}
<div class="wp-card" style="padding:18px 20px; margin-bottom:20px;">
    <form action="{{ route('manager.fifa-tickets.markup') }}" method="POST" class="fifa-markup-card">
        @csrf
        <div>
            <div style="font-weight:700; color:var(--wp-text); font-size:14px;"><i class="fas fa-percentage" style="color:var(--wp-primary);"></i> Profit Margin</div>
            <div style="font-size:12px; color:var(--wp-text-muted); max-width:420px;">Applied on top of every supplier price to produce the customer price shown on the website.</div>
        </div>
        <div class="markup-input-wrap">
            <input type="number" name="markup_percent" class="wp-input" step="0.01" min="0" max="1000"
                   value="{{ rtrim(rtrim(number_format($setting->markup_percent, 2), '0'), '.') }}" required>
            <span class="pct">%</span>
        </div>
        <button type="submit" class="wp-btn wp-btn-primary"><i class="fas fa-save"></i> Save Margin</button>
        <div style="font-size:12px; color:var(--wp-text-muted);">
            e.g. supplier <span class="fifa-cost">$1,000</span> &rarr; customer
            <span class="fifa-sell">${{ number_format(1000 * (1 + $setting->markup_percent/100), 0) }}</span>
        </div>
    </form>
</div>

@php $byStage = $matches->groupBy('stage'); @endphp

@forelse($byStage as $stage => $stageMatches)
    <div class="fifa-stage-head"><i class="fas fa-futbol"></i> {{ $stage }} <span style="color:var(--wp-text-muted); -webkit-text-fill-color:initial;">({{ $stageMatches->count() }})</span></div>

    @foreach($stageMatches as $match)
    <div class="wp-card fifa-match-card {{ $match->is_active ? '' : 'fifa-inactive' }}">
        <div class="fifa-match-top">
            <span class="fifa-match-code">{{ $match->match_code }}</span>
            <span class="fifa-match-title">{{ $match->team_a }} <span style="color:var(--wp-text-muted); font-weight:400;">vs</span> {{ $match->team_b }}</span>
            @unless($match->is_active)<span class="wp-badge wp-badge-blue">Hidden</span>@endunless
            <div class="fifa-match-actions">
                <button class="wp-btn wp-btn-secondary wp-btn-sm js-add-ticket"
                        data-match="{{ $match->id }}" data-title="{{ $match->team_a }} vs {{ $match->team_b }}">
                    <i class="fas fa-plus"></i> Ticket
                </button>
                <button class="wp-btn wp-btn-secondary wp-btn-sm js-edit-match"
                        data-id="{{ $match->id }}" data-a="{{ $match->team_a }}" data-b="{{ $match->team_b }}"
                        data-stage="{{ $match->stage }}" data-active="{{ $match->is_active ? 1 : 0 }}">
                    <i class="fas fa-pen"></i>
                </button>
                <form action="{{ route('manager.fifa-tickets.matches.destroy', $match->id) }}" method="POST"
                      onsubmit="return confirm('Delete this match and ALL its ticket listings?');">
                    @csrf @method('DELETE')
                    <button class="wp-btn wp-btn-danger wp-btn-sm"><i class="fas fa-trash-alt"></i></button>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <table class="wp-table">
                <thead>
                    <tr>
                        <th style="width:60px;">Qty</th>
                        <th style="width:110px;">Category</th>
                        <th>Seat</th>
                        <th style="width:120px;">Supplier</th>
                        <th style="width:130px;">Customer</th>
                        <th style="width:80px;">Status</th>
                        <th style="width:110px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($match->tickets as $t)
                    <tr class="{{ $t->is_active ? '' : 'fifa-inactive' }}">
                        <td><strong>{{ $t->quantity }}×</strong></td>
                        <td><span class="wp-badge wp-badge-amber">{{ $t->category }}</span></td>
                        <td style="font-size:12px; color:var(--wp-text-secondary);">{{ $t->seat_label ?: '—' }}</td>
                        <td class="fifa-cost">${{ number_format($t->supplier_price, 0) }}</td>
                        <td class="fifa-sell">${{ number_format($t->customer_price, 0) }}</td>
                        <td>
                            @if($t->is_active)<span class="wp-badge wp-badge-blue">Live</span>
                            @else<span class="text-muted-wp" style="font-size:12px;">Hidden</span>@endif
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <button class="wp-btn wp-btn-secondary wp-btn-sm js-edit-ticket"
                                        data-id="{{ $t->id }}" data-qty="{{ $t->quantity }}" data-cat="{{ $t->category }}"
                                        data-block="{{ $t->block }}" data-row="{{ $t->seat_row }}"
                                        data-price="{{ $t->supplier_price }}" data-active="{{ $t->is_active ? 1 : 0 }}">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <form action="{{ route('manager.fifa-tickets.listings.destroy', $t->id) }}" method="POST"
                                      onsubmit="return confirm('Remove this ticket listing?');">
                                    @csrf @method('DELETE')
                                    <button class="wp-btn wp-btn-danger wp-btn-sm"><i class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="empty-row"><td colspan="7"><div style="padding:14px 0; color:var(--wp-text-muted);">No ticket listings — add one.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
@empty
    <div class="wp-card" style="padding:48px; text-align:center;">
        <i class="fas fa-futbol" style="font-size:34px; color:var(--wp-border); display:block; margin-bottom:12px;"></i>
        <p style="color:var(--wp-text-muted);">No matches yet. Click <strong>Add Match</strong> to begin.</p>
    </div>
@endforelse

{{-- ════════════════ MODALS ════════════════ --}}

{{-- Add Match --}}
<div class="modal fade" id="addMatchModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" action="{{ route('manager.fifa-tickets.matches.store') }}" method="POST">
      @csrf
      <div class="modal-header"><h5 class="modal-title">Add Match</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="wp-form-group"><label class="wp-form-label">Match Code</label><input name="match_code" class="wp-input" placeholder="e.g. M45" required></div>
        <div class="wp-form-group"><label class="wp-form-label">Team A</label><input name="team_a" class="wp-input" required></div>
        <div class="wp-form-group"><label class="wp-form-label">Team B</label><input name="team_b" class="wp-input" required></div>
        <div class="wp-form-group" style="margin-bottom:0;"><label class="wp-form-label">Stage</label>
          @include('manager.fifa._stage_options')
        </div>
      </div>
      <div class="modal-footer"><button type="button" class="wp-btn wp-btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="wp-btn wp-btn-primary">Add Match</button></div>
    </form>
  </div>
</div>

{{-- Edit Match --}}
<div class="modal fade" id="editMatchModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="editMatchForm" method="POST">
      @csrf @method('PUT')
      <div class="modal-header"><h5 class="modal-title">Edit Match</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="wp-form-group"><label class="wp-form-label">Team A</label><input name="team_a" id="em_a" class="wp-input" required></div>
        <div class="wp-form-group"><label class="wp-form-label">Team B</label><input name="team_b" id="em_b" class="wp-input" required></div>
        <div class="wp-form-group"><label class="wp-form-label">Stage</label>@include('manager.fifa._stage_options', ['selectId' => 'em_stage'])</div>
        <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--wp-text-secondary);">
          <input type="checkbox" name="is_active" id="em_active" value="1"> Visible on website
        </label>
      </div>
      <div class="modal-footer"><button type="button" class="wp-btn wp-btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="wp-btn wp-btn-primary">Save</button></div>
    </form>
  </div>
</div>

{{-- Add Ticket --}}
<div class="modal fade" id="addTicketModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" action="{{ route('manager.fifa-tickets.listings.store') }}" method="POST">
      @csrf
      <input type="hidden" name="match_id" id="at_match">
      <div class="modal-header"><h5 class="modal-title">Add Ticket — <span id="at_title" style="color:var(--wp-primary);"></span></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row">
          <div class="col-4 wp-form-group"><label class="wp-form-label">Quantity</label><input type="number" name="quantity" class="wp-input" value="1" min="1" max="50" required></div>
          <div class="col-8 wp-form-group"><label class="wp-form-label">Category</label><input name="category" class="wp-input" placeholder="Cat 1 / Cat 2 RV" required></div>
        </div>
        <div class="row">
          <div class="col-6 wp-form-group"><label class="wp-form-label">Block</label><input name="block" class="wp-input" placeholder="219"></div>
          <div class="col-6 wp-form-group"><label class="wp-form-label">Row</label><input name="seat_row" class="wp-input" placeholder="BB"></div>
        </div>
        <div class="wp-form-group" style="margin-bottom:0;"><label class="wp-form-label">Supplier Price (USD, per ticket)</label><input type="number" name="supplier_price" class="wp-input" step="0.01" min="0" required></div>
      </div>
      <div class="modal-footer"><button type="button" class="wp-btn wp-btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="wp-btn wp-btn-primary">Add Ticket</button></div>
    </form>
  </div>
</div>

{{-- Edit Ticket --}}
<div class="modal fade" id="editTicketModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="editTicketForm" method="POST">
      @csrf @method('PUT')
      <div class="modal-header"><h5 class="modal-title">Edit Ticket</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <div class="row">
          <div class="col-4 wp-form-group"><label class="wp-form-label">Quantity</label><input type="number" name="quantity" id="et_qty" class="wp-input" min="1" max="50" required></div>
          <div class="col-8 wp-form-group"><label class="wp-form-label">Category</label><input name="category" id="et_cat" class="wp-input" required></div>
        </div>
        <div class="row">
          <div class="col-6 wp-form-group"><label class="wp-form-label">Block</label><input name="block" id="et_block" class="wp-input"></div>
          <div class="col-6 wp-form-group"><label class="wp-form-label">Row</label><input name="seat_row" id="et_row" class="wp-input"></div>
        </div>
        <div class="wp-form-group"><label class="wp-form-label">Supplier Price (USD, per ticket)</label><input type="number" name="supplier_price" id="et_price" class="wp-input" step="0.01" min="0" required></div>
        <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--wp-text-secondary);">
          <input type="checkbox" name="is_active" id="et_active" value="1"> Live on website
        </label>
      </div>
      <div class="modal-footer"><button type="button" class="wp-btn wp-btn-secondary" data-bs-dismiss="modal">Cancel</button><button class="wp-btn wp-btn-primary">Save</button></div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
    var matchBase  = "{{ url('manager/fifa-tickets/matches') }}";
    var listBase   = "{{ url('manager/fifa-tickets/listings') }}";

    $('.js-add-ticket').on('click', function () {
        $('#at_match').val($(this).data('match'));
        $('#at_title').text($(this).data('title'));
        new bootstrap.Modal('#addTicketModal').show();
    });

    $('.js-edit-match').on('click', function () {
        var d = $(this).data();
        $('#editMatchForm').attr('action', matchBase + '/' + d.id);
        $('#em_a').val(d.a); $('#em_b').val(d.b); $('#em_stage').val(d.stage);
        $('#em_active').prop('checked', d.active == 1);
        new bootstrap.Modal('#editMatchModal').show();
    });

    $('.js-edit-ticket').on('click', function () {
        var d = $(this).data();
        $('#editTicketForm').attr('action', listBase + '/' + d.id);
        $('#et_qty').val(d.qty); $('#et_cat').val(d.cat);
        $('#et_block').val(d.block); $('#et_row').val(d.row);
        $('#et_price').val(d.price); $('#et_active').prop('checked', d.active == 1);
        new bootstrap.Modal('#editTicketModal').show();
    });
});
</script>
@endpush
