@extends('layouts.app')

@section('content')
    <h2>Payment Failed or Aborted</h2>
    <p>Your payment was not successful. Please try again or contact support.</p>
    <p>Booking/Order ID: {{ $booking->id }}</p>
@endsection
