@extends('layouts.manager')

@section('title', 'Hajj & Umrah Packages')
@section('page-title', 'Hajj & Umrah Packages')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">Hajj &amp; Umrah Packages</h1>
    <a href="{{ route('manager.umrah-packages.create') }}" class="wp-btn wp-btn-primary">
        <i class="fas fa-plus"></i> Add Package
    </a>
</div>

<div class="wp-card">
    <div class="table-responsive">
        <table class="wp-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 90px;">Image</th>
                    <th>Title</th>
                    <th style="width: 130px;">Duration</th>
                    <th style="width: 130px;">Price</th>
                    <th style="width: 110px;">Tag</th>
                    <th style="width: 90px;">Featured</th>
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
                    <td style="font-size: 13px; color: var(--wp-text-secondary);">
                        <i class="fas fa-clock" style="color: var(--wp-primary); margin-right: 4px;"></i>
                        {{ $package->duration }}
                    </td>
                    <td>
                        <strong style="color: var(--wp-primary);">{{ $package->currency }} {{ number_format($package->price, 2) }}</strong>
                    </td>
                    <td>
                        @if($package->tag)
                            <span class="wp-badge wp-badge-amber">{{ $package->tag }}</span>
                        @else
                            <span class="text-muted-wp" style="font-size: 12px;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($package->isFeatured)
                            <i class="fas fa-star" style="color: var(--wp-primary);"></i>
                        @else
                            <span class="text-muted-wp" style="font-size: 12px;">—</span>
                        @endif
                    </td>
                    <td>
                        <div style="display: flex; gap: 6px;">
                            <a href="{{ route('manager.umrah-packages.edit', $package->id) }}" class="wp-btn wp-btn-secondary wp-btn-sm">
                                <i class="fas fa-pen"></i> Edit
                            </a>
                            <form action="{{ route('manager.umrah-packages.destroy', $package->id) }}" method="POST" onsubmit="return confirm('Delete this package?');">
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
                    <td colspan="8">
                        <div style="padding: 24px 0; text-align: center;">
                            <i class="fas fa-kaaba" style="font-size: 32px; color: var(--wp-border); margin-bottom: 8px; display: block;"></i>
                            No Hajj or Umrah packages yet.
                            <a href="{{ route('manager.umrah-packages.create') }}" style="color: var(--wp-primary);">Add your first one.</a>
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
