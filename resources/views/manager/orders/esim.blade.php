@extends('layouts.manager')

@section('title', 'eSIM Orders')
@section('page-title', 'eSIM Orders')

@section('content')
@php
    $statusOptions = [''=>'All statuses', 'paid'=>'Paid', 'pending'=>'Pending', 'failed'=>'Failed'];
    $statusSelect = '<select name="status">';
    foreach ($statusOptions as $val => $lbl) {
        $sel = request('status') === $val ? ' selected' : '';
        $statusSelect .= '<option value="'.e($val).'"'.$sel.'>'.e($lbl).'</option>';
    }
    $statusSelect .= '</select>';
@endphp

@include('manager.orders._search-bar', [
    'placeholder' => 'Search by customer name, email, or order ref...',
    'extra' => $statusSelect,
])

@include('manager.orders._table', [
    'rows'  => $orders,
    'empty' => 'No eSIM orders yet.',
    'columns' => [
        ['label' => 'Order Ref', 'render' => fn($o) => $o->order_reference ?: ('#'.$o->id)],
        ['label' => 'Customer',  'render' => fn($o) => ($o->customer_name ?: '—').' · '.($o->customer_email ?: '—')],
        ['label' => 'Country',   'render' => fn($o) => $o->country_name ?: '—'],
        ['label' => 'Bundle',    'render' => fn($o) => ($o->bundle_name ?: '—').' · '.($o->validity_days ? $o->validity_days.'d' : '')],
        ['label' => 'Amount',    'render' => fn($o) => number_format((float) $o->selling_price, 2).' '.($o->currency ?: 'AED')],
        ['label' => 'Payment',   'html' => true, 'render' => function($o) {
            $s = strtolower($o->payment_status ?? 'pending');
            $cls = $s === 'paid' ? 'badge-paid'
                 : ($s === 'pending' ? 'badge-pending'
                 : ($s === 'failed' ? 'badge-failed' : 'badge-default'));
            return '<span class="badge '.$cls.'">'.e(ucfirst($s)).'</span>';
        }],
        ['label' => 'Date',      'render' => fn($o) => $o->created_at?->format('d M Y') ?: '—'],
        ['label' => '', 'html' => true, 'render' => fn($o) =>
            '<a href="'.route('manager.orders.esim.show', $o).'" class="orders-btn orders-btn-ghost orders-btn-sm"><i class="fas fa-eye"></i> View</a>'
        ],
    ],
])
@endsection
