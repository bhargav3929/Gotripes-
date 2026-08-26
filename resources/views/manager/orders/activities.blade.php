@extends('layouts.manager')

@section('title', 'Activity Bookings')
@section('page-title', 'Activity Bookings')

@section('content')
@php
    // Only 'pending', 'paid', and 'payment_failed' are ever actually written to
    // this table (see NomodController::updateBookingStatus() and the DB column
    // default). 'cancelled' is offered too since the Status badge already treats
    // it as a distinct, first-class value alongside 'payment_failed'.
    $statusOptions = [
        ''               => 'All statuses',
        'pending'        => 'Pending',
        'paid'           => 'Paid',
        'payment_failed' => 'Failed',
        'cancelled'      => 'Cancelled',
    ];
    $statusSelect = '<select name="status">';
    foreach ($statusOptions as $val => $lbl) {
        $sel = request('status') === $val ? ' selected' : '';
        $statusSelect .= '<option value="'.e($val).'"'.$sel.'>'.e($lbl).'</option>';
    }
    $statusSelect .= '</select>';
@endphp
@include('manager.orders._search-bar', [
    'placeholder' => 'Search by name, email, or phone...',
    'extra' => $statusSelect.'
        <input type="date" name="date_from" value="'.e(request('date_from')).'" placeholder="From">
        <input type="date" name="date_to"   value="'.e(request('date_to')).'"   placeholder="To">
    ',
])

@include('manager.orders._table', [
    'rows'  => $bookings,
    'empty' => 'No activity bookings yet.',
    'columns' => [
        ['label' => '#',         'render' => fn($b) => '#'.$b->id],
        ['label' => 'Customer',  'render' => fn($b) => $b->name . ' · ' . $b->email],
        ['label' => 'Phone',     'render' => fn($b) => $b->phone ?: '—'],
        ['label' => 'Date',      'render' => fn($b) => optional($b->date)->format('d M Y') ?: '—'],
        ['label' => 'Adults / Kids', 'render' => fn($b) => ($b->adults ?? 0).' / '.($b->childrens ?? 0)],
        ['label' => 'Amount',    'render' => fn($b) => number_format((float) $b->amount, 2).' '.($b->currency ?: 'AED')],
        ['label' => 'Status',    'html' => true, 'render' => function($b) {
            $s = strtolower($b->status ?? $b->paymentOption ?? 'pending');
            $cls = in_array($s, ['paid', 'success', 'completed']) ? 'badge-paid'
                 : (in_array($s, ['pending', 'processing'])      ? 'badge-pending'
                 : (in_array($s, ['failed', 'cancelled', 'payment_failed']) ? 'badge-failed'
                 : 'badge-default'));
            // 'payment_failed' is the literal value NomodController writes on a failed/
            // cancelled checkout — show it as "Failed" rather than the raw snake_case value.
            $label = $s === 'payment_failed' ? 'Failed' : ucfirst($s);
            return '<span class="badge '.$cls.'">'.e($label).'</span>';
        }],
        ['label' => '', 'html' => true, 'render' => fn($b) =>
            '<a href="'.route('manager.orders.activities.show', $b).'" class="orders-btn orders-btn-ghost orders-btn-sm"><i class="fas fa-eye"></i> View</a>'
        ],
    ],
])
@endsection
