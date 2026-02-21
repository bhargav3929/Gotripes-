@extends('layouts.app')

@section('content')
    <h2>Payment Status Unknown</h2>
    <p>The payment status could not be confirmed at this time. Please check back later.</p>
    <p>Booking/Order ID: {{ $booking->id }}</p>
@endsection
