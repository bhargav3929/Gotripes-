@extends('layouts.client')

@section('title', 'Visa Applications')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="page-title"><i class="fas fa-passport me-2" style="color: var(--gold);"></i>Visa Applications</h1>
</div>

<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name or email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="fas fa-search"></i></button>
                <a href="{{ route('client.visa') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-redo"></i></a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Applicant</th>
                    <th>Email</th>
                    <th>Visa Type</th>
                    <th>Travel Date</th>
                    <th>Status</th>
                    <th>Applied On</th>
                </tr>
            </thead>
            <tbody>
                @forelse($visas as $visa)
                <tr>
                    <td>#{{ $visa->id }}</td>
                    <td>
                        <strong>{{ $visa->UAEV_first_name }} {{ $visa->UAEV_last_name }}</strong>
                    </td>
                    <td>{{ $visa->UAEV_email }}</td>
                    <td>{{ $visa->UAEV_visaDuration ?? 'Standard' }}</td>
                    <td>{{ $visa->UAEV_arrival_date ? \Carbon\Carbon::parse($visa->UAEV_arrival_date)->format('M d, Y') : '-' }}</td>
                    <td>
                        <span class="badge bg-{{ $visa->UAEV_status == 1 ? 'warning' : ($visa->UAEV_status == 2 ? 'success' : 'danger') }}">
                            {{ $visa->UAEV_status == 1 ? 'Pending' : ($visa->UAEV_status == 2 ? 'Approved' : 'Rejected') }}
                        </span>
                    </td>
                    <td>{{ $visa->UAEV_created_date ? \Carbon\Carbon::parse($visa->UAEV_created_date)->format('M d, Y') : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">No visa applications found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($visas->hasPages())
    <div class="card-footer d-flex justify-content-between align-items-center py-2">
        <small class="text-muted">Showing {{ $visas->firstItem() }}-{{ $visas->lastItem() }} of {{ $visas->total() }}</small>
        <div class="d-flex gap-2">
            @if($visas->onFirstPage())
                <span class="btn btn-sm btn-outline-secondary disabled"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $visas->previousPageUrl() }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-chevron-left"></i></a>
            @endif
            <span class="btn btn-sm btn-dark disabled">{{ $visas->currentPage() }} / {{ $visas->lastPage() }}</span>
            @if($visas->hasMorePages())
                <a href="{{ $visas->nextPageUrl() }}" class="btn btn-sm btn-primary"><i class="fas fa-chevron-right"></i></a>
            @else
                <span class="btn btn-sm btn-outline-secondary disabled"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
