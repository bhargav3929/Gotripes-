@extends('layouts.manager')

@section('title', 'e-Visa ' . ($application->order_id ?: '#'.$application->id))
@section('page-title', 'e-Visa ' . ($application->order_id ?: '#'.$application->id))

@section('content')
@php
    // Paid, but the provider refused the submission — the recoverable failure.
    $needsResubmit = $application->status === 'submit_failed';

    // Paid and finalized on our side but the provider never moved past the
    // payment gate: usually means finalize-checkout has not run yet.
    $stalled = $application->is_paid
        && $application->status !== 'submitted'
        && !$needsResubmit;

    $providerError = is_array($application->last_response ?? null)
        ? ($application->last_response['error']['message']
            ?? $application->last_response['message']
            ?? null)
        : null;
@endphp

<div class="orders-toolbar">
    <a href="{{ route('manager.orders.evisa') }}" class="orders-btn orders-btn-ghost">
        <i class="fas fa-arrow-left"></i> Back to e-Visa applications
    </a>

    @if($needsResubmit)
        <form method="POST" action="{{ route('manager.orders.evisa.retry', $application->id) }}" class="d-inline"
              onsubmit="return confirm('Re-submit {{ $application->order_id }} to the visa provider?');">
            @csrf
            <button type="submit" class="orders-btn">
                <i class="fas fa-rotate-right"></i> Re-submit to provider
            </button>
        </form>
    @endif
</div>

@if($needsResubmit)
    <div class="wp-notice wp-notice-error">
        <span>
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>This customer has paid but their visa is not being processed.</strong>
            The provider rejected the submission, so no one is working on this application.
            Use <strong>Re-submit to provider</strong> above, or refund the customer.
            @if($providerError)
                <br><em>Provider said: {{ $providerError }}</em>
            @endif
        </span>
    </div>
@elseif($stalled)
    <div class="wp-notice wp-notice-warning">
        <span>
            <i class="fas fa-clock me-2"></i>
            Payment was recorded but the application has not been confirmed with the provider yet.
            The scheduled reconciliation (<code>evisa:reconcile</code>) retries this every ten minutes.
        </span>
    </div>
@endif

<div class="orders-detail-grid">
    <div class="orders-card orders-detail-card">
        <h3>Applicant</h3>
        <ul class="orders-detail-list">
            <li><span class="label">Name</span>        <span class="value">{{ trim(($application->first_name ?? '').' '.($application->last_name ?? '')) ?: '—' }}</span></li>
            <li><span class="label">Email</span>       <span class="value">{{ $application->email ?: '—' }}</span></li>
            <li><span class="label">Phone</span>       <span class="value">{{ $application->phone ?: '—' }}</span></li>
            <li><span class="label">Nationality</span> <span class="value">{{ $application->nationality ?: '—' }}</span></li>
            <li><span class="label">Passport</span>    <span class="value">{{ $application->passport_number ?: '—' }}</span></li>
        </ul>
    </div>

    <div class="orders-card orders-detail-card">
        <h3>Trip</h3>
        <ul class="orders-detail-list">
            <li><span class="label">Destination</span> <span class="value">{{ $application->destination_code ?: '—' }}</span></li>
            <li><span class="label">Origin</span>      <span class="value">{{ $application->origination_code ?: '—' }}</span></li>
            <li><span class="label">Arrival</span>     <span class="value">{{ optional($application->arrival_date)->format('d M Y') ?: '—' }}</span></li>
            <li><span class="label">Departure</span>   <span class="value">{{ optional($application->departure_date)->format('d M Y') ?: '—' }}</span></li>
        </ul>
    </div>

    <div class="orders-card orders-detail-card">
        <h3>Payment</h3>
        <ul class="orders-detail-list">
            <li><span class="label">Amount charged</span> <span class="value">{{ $application->amount ? number_format((float) $application->amount, 2).' '.($application->currency ?: 'USD') : '—' }}</span></li>
            <li><span class="label">Paid</span>           <span class="value">{{ $application->is_paid ? 'Yes' : 'No' }}</span></li>
            <li><span class="label">Status</span>         <span class="value">{{ ucfirst(str_replace('_', ' ', $application->status ?: 'draft')) }}</span></li>
            <li><span class="label">Checkout session</span> <span class="value">{{ $application->checkout_session_id ?: '—' }}</span></li>
        </ul>
    </div>

    <div class="orders-card orders-detail-card">
        <h3>Provider (Fluxir)</h3>
        <ul class="orders-detail-list">
            <li><span class="label">State</span>           <span class="value">{{ $application->state ?: '—' }}</span></li>
            <li><span class="label">Application ID</span>  <span class="value">{{ $application->fluxir_service_application_id ?: '—' }}</span></li>
            <li><span class="label">Trip ID</span>         <span class="value">{{ $application->fluxir_trip_id ?: '—' }}</span></li>
            <li><span class="label">Person ID</span>       <span class="value">{{ $application->fluxir_person_id ?: '—' }}</span></li>
            <li><span class="label">Documents</span>       <span class="value">{{ is_array($application->attachments) ? count($application->attachments).' uploaded' : '—' }}</span></li>
        </ul>
    </div>

    <div class="orders-card orders-detail-card">
        <h3>Notifications</h3>
        <ul class="orders-detail-list">
            <li><span class="label">Team notified</span>     <span class="value">{{ $application->notified_at?->format('d M Y H:i') ?: 'Not sent' }}</span></li>
            <li><span class="label">Customer emailed</span>  <span class="value">{{ $application->customer_notified_at?->format('d M Y H:i') ?: 'Not sent' }}</span></li>
            <li><span class="label">Created</span>           <span class="value">{{ $application->created_at?->format('d M Y H:i') ?: '—' }}</span></li>
        </ul>
    </div>

    @if(is_array($application->attachments) && count($application->attachments))
        <div class="orders-card orders-detail-card">
            <h3>Uploaded documents</h3>
            <ul class="orders-detail-list">
                @foreach($application->attachments as $nameId => $attachmentId)
                    <li><span class="label">{{ $nameId }}</span> <span class="value">{{ $attachmentId ?: 'uploaded' }}</span></li>
                @endforeach
            </ul>
            <p style="font-size:12px;opacity:.7;margin-top:8px;">
                Files are stored by the visa provider, not on this server.
            </p>
        </div>
    @endif
</div>
@endsection
