@extends('layouts.manager')

@section('title', 'Flight & Hotel Bookings')
@section('page-title', 'Flight & Hotel Bookings')

@section('content')
@php
    $typeOptions = ['' => 'All types', 'flight' => 'Flights', 'hotel' => 'Hotels'];
    $statusOptions = ['' => 'All statuses', 'paid' => 'Paid', 'pending' => 'Pending', 'failed' => 'Failed', 'cancelled' => 'Cancelled'];

    $typeSelect = '<select name="type">';
    foreach ($typeOptions as $v => $l) {
        $sel = request('type') === $v ? ' selected' : '';
        $typeSelect .= '<option value="'.e($v).'"'.$sel.'>'.e($l).'</option>';
    }
    $typeSelect .= '</select>';

    $statusSelect = '<select name="status">';
    foreach ($statusOptions as $v => $l) {
        $sel = request('status') === $v ? ' selected' : '';
        $statusSelect .= '<option value="'.e($v).'"'.$sel.'>'.e($l).'</option>';
    }
    $statusSelect .= '</select>';
@endphp

@include('manager.orders._search-bar', [
    'placeholder' => 'Search by order id or checkout id...',
    'extra' => $typeSelect . $statusSelect,
])

@include('manager.orders._table', [
    'rows'  => $transactions,
    'empty' => 'No flight or hotel bookings yet.',
    'columns' => [
        ['label' => 'Order ID',  'render' => fn($t) => $t->order_id ?: ('#'.$t->id)],
        ['label' => 'Type', 'html' => true, 'render' => fn($t) =>
            '<span class="badge badge-info">'.e(ucfirst($t->booking_type ?: 'unknown')).'</span>'
        ],
        ['label' => 'Amount',    'render' => fn($t) => number_format((float) $t->amount, 2).' '.($t->currency ?: 'AED')],
        ['label' => 'Status', 'html' => true, 'render' => function($t) {
            $s = strtolower($t->status ?? 'pending');
            $cls = $s === 'paid' ? 'badge-paid'
                 : ($s === 'pending' ? 'badge-pending'
                 : (in_array($s, ['failed','cancelled','expired']) ? 'badge-failed' : 'badge-default'));
            return '<span class="badge '.$cls.'">'.e(ucfirst($s)).'</span>';
        }],
        ['label' => 'Checkout',  'render' => fn($t) => $t->checkout_id ?: '—'],
        ['label' => 'Date',      'render' => fn($t) => $t->created_at?->format('d M Y H:i') ?: '—'],
    ],
])
@endsection
