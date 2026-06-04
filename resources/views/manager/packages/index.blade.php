@extends('layouts.manager')

@section('title', 'Tour Packages')
@section('page-title', 'Tour Packages')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Tour Packages</h1>
    <a href="{{ route('manager.packages.create') }}" class="wp-btn wp-btn-primary">
        <i class="fas fa-plus"></i> Add Tour Package
    </a>
</div>

{{-- Country Filter --}}
@if(isset($usedCountries) && $usedCountries->count() > 1)
<div class="wp-card" style="margin-bottom: 0; border-bottom: none; border-radius: 8px 8px 0 0;">
    <div style="padding: 12px 16px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <span style="color: var(--wp-text-muted); font-size: 13px; white-space: nowrap;">
            <i class="fas fa-filter" style="margin-right: 4px;"></i> Filter:
        </span>
        <a href="{{ route('manager.packages.index') }}"
           class="wp-btn {{ !request('country') ? 'wp-btn-primary' : 'wp-btn-secondary' }}"
           style="padding: 4px 12px; font-size: 12px;">
            All
        </a>
        @foreach($usedCountries as $c)
            <a href="{{ route('manager.packages.index', ['country' => $c]) }}"
               class="wp-btn {{ request('country') === $c ? 'wp-btn-primary' : 'wp-btn-secondary' }}"
               style="padding: 4px 12px; font-size: 12px;">
                {{ $c }}
            </a>
        @endforeach
    </div>
</div>
@endif

<div class="wp-card" @if(isset($usedCountries) && $usedCountries->count() > 1) style="border-radius: 0 0 8px 8px;" @endif>
    <div class="table-responsive">
        <table class="wp-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 90px;">Image</th>
                    <th>Title</th>
                    <th style="width: 160px;">Country</th>
                    <th style="width: 130px;">Duration</th>
                    <th style="width: 110px;">Price</th>
                    <th style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($packages as $index => $package)
                <tr>
                    <td style="color: var(--wp-text-muted);">{{ $packages->firstItem() + $index }}</td>
                    <td>
                        @if($package->image)
                            <img src="{{ str_starts_with($package->image, 'http') ? $package->image : asset($package->image) }}" alt="{{ $package->title }}"
                                 style="width: 70px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid var(--wp-border-light);"
                                 onerror="this.style.display='none';">
                        @else
                            <div style="width: 70px; height: 50px; background: #333; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-image" style="color: var(--wp-text-muted); font-size: 16px;"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <strong style="color: var(--wp-text);">{{ Str::limit($package->title, 50) }}</strong>
                        @if($package->description)
                            <br><span style="font-size: 11px; color: var(--wp-text-muted);">{{ Str::limit(strip_tags($package->description), 70) }}</span>
                        @endif
                    </td>
                    <td>
                        @if($package->country)
                            <span class="wp-badge wp-badge-amber">{{ $package->country }}</span>
                        @else
                            <span class="text-muted-wp" style="font-size: 12px;">—</span>
                        @endif
                    </td>
                    <td style="font-size: 13px; color: var(--wp-text-secondary);">
                        <i class="fas fa-clock" style="color: var(--wp-primary); margin-right: 4px;"></i>
                        {{ $package->duration }}
                    </td>
                    <td>
                        <strong style="color: var(--wp-primary);">${{ number_format($package->price, 2) }}</strong>
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px;">
                            <a href="{{ route('manager.packages.edit', $package->id) }}" class="wp-btn wp-btn-secondary wp-btn-sm">
                                <i class="fas fa-pen"></i> Edit
                            </a>
                            <form action="{{ route('manager.packages.destroy', $package->id) }}" method="POST" onsubmit="return confirm('Delete this tour package?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="wp-btn wp-btn-danger wp-btn-sm">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="7">
                        <div style="padding: 24px 0; text-align: center;">
                            <i class="fas fa-suitcase-rolling" style="font-size: 32px; color: var(--wp-border); margin-bottom: 8px; display: block;"></i>
                            No tour packages yet.
                            <a href="{{ route('manager.packages.create') }}" style="color: var(--wp-primary);">Add your first one.</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($packages->hasPages())
        <div class="wp-pagination">
            {{ $packages->links() }}
        </div>
    @endif
</div>
@endsection
