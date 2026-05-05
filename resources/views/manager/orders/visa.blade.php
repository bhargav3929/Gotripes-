@extends('layouts.manager')

@section('title', 'Visa Applications')
@section('page-title', 'Visa Applications')

@section('content')
@include('manager.orders._search-bar', [
    'placeholder' => 'Search by name or email...',
])

@include('manager.orders._table', [
    'rows'  => $applications,
    'empty' => 'No visa applications yet.',
    'columns' => [
        ['label' => '#',          'render' => fn($a) => '#'.$a->id],
        ['label' => 'Applicant',  'render' => fn($a) => trim(($a->UAEV_first_name ?? '').' '.($a->UAEV_last_name ?? '')) ?: '—'],
        ['label' => 'Email',      'render' => fn($a) => $a->UAEV_email ?: '—'],
        ['label' => 'Phone',      'render' => fn($a) => $a->UAEV_phone ?: '—'],
        ['label' => 'Nationality','render' => fn($a) => $a->UAEV_nationality ?: '—'],
        ['label' => 'Duration',   'render' => fn($a) => $a->UAEV_visaDuration ?: '—'],
        ['label' => 'Arrival',    'render' => fn($a) => optional($a->UAEV_arrival_date)?->format('d M Y') ?: '—'],
        ['label' => 'Price',      'render' => fn($a) => $a->UAEV_price ?: '—'],
        ['label' => '', 'html' => true, 'render' => fn($a) =>
            '<a href="'.route('manager.orders.visa.show', $a).'" class="orders-btn orders-btn-ghost orders-btn-sm"><i class="fas fa-eye"></i> View</a>'
        ],
    ],
])
@endsection
