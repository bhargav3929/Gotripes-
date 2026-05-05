{{--
    Reusable table component for order list pages.

    Variables:
      $rows     (LengthAwarePaginator) the records to render
      $columns  (array) [['label' => 'Header', 'render' => fn($row) => '...', 'html' => true (optional)]]
      $empty    (string) message when paginator is empty

    Each column's `render` closure receives the row and returns a string.
    Set `'html' => true` if the closure returns rendered HTML (e.g. badges).
--}}
@php
    $empty ??= 'No records to show.';
@endphp

<div class="orders-card">
    @if($rows->isEmpty())
        <div class="orders-empty">
            <i class="fas fa-inbox"></i>
            <p>{{ $empty }}</p>
        </div>
    @else
        <div class="orders-table-wrap">
            <table class="orders-table">
                <thead>
                    <tr>
                        @foreach($columns as $col)
                            <th>{{ $col['label'] ?? '' }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                        <tr>
                            @foreach($columns as $col)
                                <td>
                                    @if(!empty($col['html']))
                                        {!! $col['render']($row) !!}
                                    @else
                                        {{ $col['render']($row) }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($rows->hasPages())
            <div class="orders-pagination">
                {{ $rows->withQueryString()->links() }}
            </div>
        @endif
    @endif
</div>

{{-- Shared CSS for all order pages — only injected once per request via @once --}}
@once
<style>
    .orders-card { background:#1a1a1a; border:1px solid rgba(255,215,0,0.18); border-radius:12px; overflow:hidden; }
    .orders-card + .orders-card { margin-top:14px; }
    .orders-searchbar {
        display:flex; gap:8px; flex-wrap:wrap; align-items:center;
        padding:14px 16px; background:#1a1a1a;
        border:1px solid rgba(255,215,0,0.18); border-radius:12px; margin-bottom:14px;
    }
    .orders-search-input,
    .orders-searchbar input[type=date],
    .orders-searchbar select {
        flex: 1 1 180px; min-width:140px;
        padding:8px 12px; height:38px; border-radius:8px;
        background:#222; border:1px solid rgba(255,215,0,0.15);
        color:#f0f0f0; font-size:13px;
    }
    .orders-search-input:focus,
    .orders-searchbar input[type=date]:focus,
    .orders-searchbar select:focus {
        outline:none; border-color:#FFD700; box-shadow:0 0 0 2px rgba(255,215,0,0.2);
    }
    .orders-btn {
        display:inline-flex; align-items:center; gap:6px;
        padding:8px 14px; height:38px; font-size:13px; font-weight:600;
        border-radius:8px; cursor:pointer; text-decoration:none; border:none;
        transition:all .15s;
    }
    .orders-btn-primary { background:linear-gradient(135deg,#FFD700,#FFA500); color:#1a1a1a; }
    .orders-btn-primary:hover { transform:translateY(-1px); box-shadow:0 4px 14px rgba(255,215,0,.3); }
    .orders-btn-ghost   { background:transparent; color:#aaa; border:1px solid rgba(255,255,255,.1); }
    .orders-btn-ghost:hover { background:rgba(255,255,255,.05); color:#fff; }
    .orders-btn-sm      { padding:5px 12px; height:30px; font-size:12px; }

    .orders-table-wrap { overflow-x:auto; }
    .orders-table { width:100%; border-collapse:collapse; font-size:13px; }
    .orders-table thead { background:#0f0f0f; }
    .orders-table th {
        padding:12px 16px; text-align:left; font-weight:600; font-size:11px;
        text-transform:uppercase; letter-spacing:0.5px;
        color:rgba(255,215,0,.85); border-bottom:1px solid rgba(255,215,0,.15);
    }
    .orders-table td {
        padding:14px 16px; color:#e0e0e0;
        border-bottom:1px solid rgba(255,255,255,.04);
    }
    .orders-table tbody tr:hover { background:rgba(255,215,0,.03); }
    .orders-table tbody tr:last-child td { border-bottom:none; }

    .badge { display:inline-block; padding:3px 10px; font-size:11px; font-weight:600;
             border-radius:99px; letter-spacing:.3px; }
    .badge-paid       { background:rgba(34,197,94,.15);  color:#4ade80; border:1px solid rgba(34,197,94,.3); }
    .badge-pending    { background:rgba(234,179,8,.15);  color:#facc15; border:1px solid rgba(234,179,8,.3); }
    .badge-failed     { background:rgba(214,54,56,.15);  color:#f87171; border:1px solid rgba(214,54,56,.3); }
    .badge-info       { background:rgba(59,130,246,.15); color:#60a5fa; border:1px solid rgba(59,130,246,.3); }
    .badge-default    { background:rgba(255,255,255,.06); color:#aaa; border:1px solid rgba(255,255,255,.08); }

    .orders-empty { padding:60px 20px; text-align:center; color:#666; }
    .orders-empty i { font-size:36px; opacity:.4; margin-bottom:10px; display:block; }
    .orders-empty p { margin:0; font-size:14px; }

    .orders-pagination { padding:12px 16px; background:#0f0f0f; border-top:1px solid rgba(255,215,0,.08); }
    .orders-pagination .pagination { margin:0; justify-content:flex-end; }

    /* Detail page */
    .orders-detail-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(280px,1fr)); gap:14px; }
    .orders-detail-card h3 { color:#FFD700; font-size:14px; font-weight:600;
                              text-transform:uppercase; letter-spacing:.5px;
                              padding:12px 16px; margin:0;
                              border-bottom:1px solid rgba(255,215,0,.1); }
    .orders-detail-list { padding:6px 16px; margin:0; list-style:none; }
    .orders-detail-list li { padding:8px 0; border-bottom:1px solid rgba(255,255,255,.04);
                              display:flex; justify-content:space-between; align-items:flex-start; gap:14px; }
    .orders-detail-list li:last-child { border-bottom:none; }
    .orders-detail-list .label { color:#888; font-size:12px; flex-shrink:0; }
    .orders-detail-list .value { color:#f0f0f0; font-size:13px; text-align:right; word-break:break-word; }

    .orders-toolbar { display:flex; gap:10px; margin-bottom:14px; }
</style>
@endonce
