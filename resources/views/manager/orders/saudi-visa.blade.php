@extends('layouts.manager')

@section('title', 'Saudi Visa Applications')
@section('page-title', 'Saudi Visa Applications')

@section('content')
@include('manager.orders._search-bar', [
    'placeholder' => 'Search by name, email, or passport...',
])

@include('manager.orders._table', [
    'rows'  => $applications,
    'empty' => 'No Saudi Visa applications yet.',
    'columns' => [
        ['label' => '#',          'render' => fn($a) => '#'.$a->id],
        ['label' => 'Order ID',   'render' => fn($a) => $a->order_id],
        ['label' => 'Applicant',  'render' => fn($a) => trim(($a->first_name ?? '').' '.($a->last_name ?? '')) ?: $a->full_name ?: '—'],
        ['label' => 'Email',      'render' => fn($a) => $a->email ?: '—'],
        ['label' => 'Phone',      'render' => fn($a) => $a->phone ?: '—'],
        ['label' => 'Nationality','render' => fn($a) => $a->nationality ?: '—'],
        ['label' => 'Visa Type',  'render' => fn($a) => $a->visaType?->name ?: '—'],
        ['label' => 'Price',      'render' => fn($a) => 'AED '.number_format($a->price, 2)],
        ['label' => 'Payment',    'render' => fn($a) => ucfirst($a->payment_status)],
        ['label' => 'Status',     'render' => fn($a) => ucfirst($a->status)],
        ['label' => '', 'html' => true, 'render' => fn($a) =>
            '<a href="'.route('manager.orders.saudi-visa.show', $a).'" class="orders-btn orders-btn-ghost orders-btn-sm"><i class="fas fa-eye"></i> View</a>'
        ],
    ],
])
@endsection
