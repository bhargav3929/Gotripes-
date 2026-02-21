@extends('layouts.app')

@section('content')
    <h2>Payment Cancelled</h2>
    <p>You have cancelled the payment process. If this was a mistake, please try booking again.</p>
    <a href="{{ url('/') }}">Return to Home</a>
@endsection
