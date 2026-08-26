@extends('layouts.manager')

@section('title', 'eSIM Orders')
@section('page-title', 'eSIM Orders')

@section('content')
@php
    $statusOptions = [''=>'All statuses', 'paid'=>'Paid', 'unpaid'=>'Unpaid', 'failed'=>'Failed', 'cancelled'=>'Cancelled'];
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
            $s = strtolower($o->payment_status ?? 'unpaid');
            // 'cancelled' is grouped with 'failed' visually (both mean no charge
            // went through) but keeps its own label so managers can tell a
            // customer-cancelled checkout apart from a genuine decline.
            $cls = $s === 'paid' ? 'badge-paid'
                 : ($s === 'unpaid' ? 'badge-pending'
                 : (in_array($s, ['failed', 'cancelled']) ? 'badge-failed' : 'badge-default'));
            return '<span class="badge '.$cls.'">'.e(ucfirst($s)).'</span>';
        }],
        ['label' => 'eSIM', 'html' => true, 'render' => function($o) {
            // A paid order with no MontyeSIM ID means the customer paid and got
            // nothing — surface it here so it doesn't sit unnoticed.
            if ($o->payment_status === 'paid' && ! $o->monty_order_id) {
                return '<span class="badge badge-failed"><i class="fas fa-exclamation-triangle"></i> Not issued</span>';
            }
            if ($o->monty_order_id) {
                // A group order is only done when every eSIM came through.
                // Judging by the parent id alone showed "Issued" for an order
                // where 17 of 20 travellers got one, which is the case most in
                // need of attention.
                $qty = $o->unitCount();
                if ($qty > 1) {
                    $issued = $o->units()->whereNotNull('monty_order_id')->count();
                    if ($issued < $qty) {
                        return '<span class="badge badge-failed"><i class="fas fa-exclamation-triangle"></i> '
                            .$issued.' of '.$qty.'</span>';
                    }
                    return '<span class="badge badge-paid">Issued ×'.$qty.'</span>';
                }
                return '<span class="badge badge-paid">Issued</span>';
            }
            return '<span class="badge badge-default">—</span>';
        }],
        ['label' => 'QR sent', 'html' => true, 'render' => function($o) {
            // A provisioned eSIM the customer was never emailed is a silent
            // failure: the order looks complete from every other column.
            if (! $o->monty_order_id) {
                return '<span class="badge badge-default">—</span>';
            }
            return $o->qr_sent_at
                ? '<span class="badge badge-paid">Sent</span>'
                : '<span class="badge badge-pending">Not sent</span>';
        }],
        ['label' => 'Date',      'render' => fn($o) => $o->created_at?->format('d M Y') ?: '—'],
        ['label' => '', 'html' => true, 'render' => fn($o) =>
            '<a href="'.route('manager.orders.esim.show', $o).'" class="orders-btn orders-btn-ghost orders-btn-sm"><i class="fas fa-eye"></i> View</a>'
        ],
    ],
])
@endsection
