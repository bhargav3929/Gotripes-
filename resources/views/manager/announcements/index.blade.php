@extends('layouts.manager')

@section('title', 'News Ticker')
@section('page-title', 'News Ticker')

@section('content')
<div class="wp-page-header">
    <h1 class="wp-page-title">News Ticker</h1>
    <a href="{{ route('manager.announcements.create') }}" class="wp-btn wp-btn-primary">
        <i class="fas fa-plus"></i> Add New Item
    </a>
</div>

<div class="wp-card">
    <div class="table-responsive">
        <table class="wp-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 100px;">Tag</th>
                    <th>Announcement Text</th>
                    <th style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($announcements as $index => $item)
                <tr>
                    <td style="color: var(--wp-text-muted);">{{ $announcements->firstItem() + $index }}</td>
                    <td>
                        @if($item->tagType && $item->tagType !== 'none')
                            @switch($item->tagType)
                                @case('breaking')
                                    <span class="wp-badge wp-badge-red">Breaking</span>
                                    @break
                                @case('trending')
                                    <span class="wp-badge wp-badge-amber">Trending</span>
                                    @break
                                @case('exclusive')
                                    <span class="wp-badge wp-badge-green">Exclusive</span>
                                    @break
                                @case('alert')
                                    <span class="wp-badge wp-badge-blue">New</span>
                                    @break
                            @endswitch
                        @else
                            <span class="text-muted-wp" style="font-size: 12px;">No tag</span>
                        @endif
                    </td>
                    <td>{{ Str::limit($item->description, 100) }}</td>
                    <td>
                        <div style="display: flex; gap: 6px;">
                            <a href="{{ route('manager.announcements.edit', $item->id) }}" class="wp-btn wp-btn-secondary wp-btn-sm">
                                <i class="fas fa-pen"></i> Edit
                            </a>
                            <form action="{{ route('manager.announcements.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Remove this announcement?');">
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
                    <td colspan="4">
                        <div style="padding: 20px 0;">
                            <i class="fas fa-rss" style="font-size: 28px; color: var(--wp-border); margin-bottom: 8px; display: block;"></i>
                            No announcements yet.
                            <a href="{{ route('manager.announcements.create') }}" style="color: var(--wp-primary);">Create your first one.</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($announcements->hasPages())
        <div class="wp-pagination">
            {{ $announcements->links() }}
        </div>
    @endif
</div>
@endsection
