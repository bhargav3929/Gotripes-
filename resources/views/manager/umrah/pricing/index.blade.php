@extends('layouts.manager')
@section('title', 'Umrah Pricing')
@section('content')
<div class="container-fluid">
    <h2 class="h3 mb-4 text-gray-800">Pricing Management</h2>
    
    <form action="{{ route('manager.umrah.pricing.update') }}" method="POST">
        @csrf
        <div class="card shadow mb-4">
            <div class="card-body overflow-auto">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Package</th>
                            <th>Currency</th>
                            <th>Base Price</th>
                            <th>Discount Price</th>
                            <th>Adult Price</th>
                            <th>Child Price</th>
                            <th>Infant Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($packages as $package)
                        <tr>
                            <td class="fw-bold">{{ $package->title }}</td>
                            <td>
                                <select name="prices[{{ $package->id }}][currency]" class="form-select form-select-sm">
                                    <option value="AED" {{ $package->currency == 'AED' ? 'selected' : '' }}>AED</option>
                                    <option value="USD" {{ $package->currency == 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="EUR" {{ $package->currency == 'EUR' ? 'selected' : '' }}>EUR</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" name="prices[{{ $package->id }}][price]" class="form-control form-control-sm" value="{{ $package->price }}" required></td>
                            <td><input type="number" step="0.01" name="prices[{{ $package->id }}][discount_price]" class="form-control form-control-sm" value="{{ $package->discount_price }}"></td>
                            <td><input type="number" step="0.01" name="prices[{{ $package->id }}][adult_price]" class="form-control form-control-sm" value="{{ $package->adult_price }}"></td>
                            <td><input type="number" step="0.01" name="prices[{{ $package->id }}][child_price]" class="form-control form-control-sm" value="{{ $package->child_price }}"></td>
                            <td><input type="number" step="0.01" name="prices[{{ $package->id }}][infant_price]" class="form-control form-control-sm" value="{{ $package->infant_price }}"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">Save Pricing</button>
            </div>
        </div>
    </form>
</div>
@endsection
