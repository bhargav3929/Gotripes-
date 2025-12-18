@extends('layouts.app')

@section('content')
    <h2>Payment Successful</h2>
    <p>Thank you for your payment. Your booking/order ID is: {{ $booking->id }}</p>
    <p>Status: {{ $booking->payment_status }}</p>
    <!-- Add more booking/payment details here -->
@endsection
