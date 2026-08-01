@extends('layouts.manager')

@section('title', 'e-Visa Applications')
@section('page-title', 'e-Visa Applications')

@section('content')
@php
    $statusOptions = [
        ''                 => 'All statuses',
        'submitted'        => 'Submitted',
        'submit_failed'    => 'Submission failed',
        'awaiting_payment' => 'Awaiting payment',
        'paid'             => 'Paid',
        'cancelled'        => 'Cancelled',
        'failed'           => 'Failed',
        'draft'            => 'Draft',
    ];
    $statusSelect = '<select name="status">';
    foreach ($statusOptions as $val => $lbl) {
        $sel = request('status') === $val ? ' selected' : '';
        $statusSelect .= '<option value="'.e($val).'"'.$sel.'>'.e($lbl).'</option>';
    }
    $statusSelect .= '</select>';

    // Paid but not with the provider: the customer's money is taken and nobody
    // is processing their visa. Surface the count so it can't sit unnoticed.
    $stranded = $applications->getCollection()
        ->where('status', 'submit_failed')
        ->count();
@endphp

@if($stranded > 0)
    <div class="wp-notice wp-notice-error">
        <span>
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>{{ $stranded }} paid {{ $stranded === 1 ? 'application is' : 'applications are' }} not with the visa provider.</strong>
            The {{ $stranded === 1 ? 'customer has' : 'customers have' }} paid but nothing is processing their visa.
            Open each one and use <strong>Re-submit to provider</strong>.
        </span>
    </div>
@endif

@include('manager.orders._search-bar', [
    'placeholder' => 'Search by name, email, or order reference...',
    'extra' => $statusSelect,
])

@include('manager.orders._table', [
    'rows'  => $applications,
    'empty' => 'No e-Visa applications yet.',
    'columns' => [
        ['label' => 'Reference', 'render' => fn($a) => $a->order_id ?: ('#'.$a->id)],
        ['label' => 'Applicant', 'render' => fn($a) => trim(($a->first_name ?? '').' '.($a->last_name ?? '')) ?: '—'],
        ['label' => 'Email',     'render' => fn($a) => $a->email ?: '—'],
        ['label' => 'Route',     'render' => fn($a) => ($a->nationality ?: '—').' → '.($a->destination_code ?: '—')],
        ['label' => 'Amount',    'render' => fn($a) => $a->amount ? number_format((float) $a->amount, 2).' '.($a->currency ?: 'USD') : '—'],
        ['label' => 'Status', 'html' => true, 'render' => function($a) {
            $s = strtolower($a->status ?? 'draft');
            $cls = match ($s) {
                'submitted'                     => 'badge-paid',
                'submit_failed', 'failed'       => 'badge-failed',
                'awaiting_payment', 'paid'      => 'badge-pending',
                default                         => 'badge-default',
            };
            $label = $s === 'submit_failed' ? 'Submission failed' : ucfirst(str_replace('_', ' ', $s));
            return '<span class="badge '.$cls.'">'.e($label).'</span>';
        }],
        ['label' => 'Provider state', 'render' => fn($a) => $a->state ?: '—'],
        ['label' => 'Paid', 'html' => true, 'render' => fn($a) =>
            $a->is_paid
                ? '<span class="badge badge-paid">Yes</span>'
                : '<span class="badge badge-default">No</span>'
        ],
        ['label' => 'Date',  'render' => fn($a) => $a->created_at?->format('d M Y') ?: '—'],
        ['label' => '', 'html' => true, 'render' => fn($a) =>
            '<a href="'.route('manager.orders.evisa.show', $a).'" class="orders-btn orders-btn-ghost orders-btn-sm"><i class="fas fa-eye"></i> View</a>'
        ],
    ],
])
@endsection
