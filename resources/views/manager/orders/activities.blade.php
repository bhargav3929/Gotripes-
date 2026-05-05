@extends('layouts.manager')

@section('title', 'Activity Bookings')
@section('page-title', 'Activity Bookings')

@section('content')
@include('manager.orders._search-bar', [
    'placeholder' => 'Search by name, email, or phone...',
    'extra' => '
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
                 : (in_array($s, ['failed', 'cancelled'])        ? 'badge-failed'
                 : 'badge-default'));
            return '<span class="badge '.$cls.'">'.e(ucfirst($s)).'</span>';
        }],
        ['label' => '', 'html' => true, 'render' => fn($b) =>
            '<a href="'.route('manager.orders.activities.show', $b).'" class="orders-btn orders-btn-ghost orders-btn-sm"><i class="fas fa-eye"></i> View</a>'
        ],
    ],
])
@endsection
