@extends('layouts.manager')

@section('title', $guide['title'] . ' - Help Guide')
@section('page-title', 'Help Guide')

@section('content')
<div class="help-shell">
    <nav class="help-crumbs" aria-label="Breadcrumb">
        <a href="{{ route('manager.help.index') }}"><i class="fas fa-arrow-left"></i> All guides</a>
        <span class="help-crumb-sep">/</span>
        <span class="help-crumb-current">{{ $guide['title'] }}</span>
    </nav>

    <article class="wp-card help-article">
        <div class="help-article-body help-prose">{!! $html !!}</div>
    </article>

    <nav class="help-pager" aria-label="Guide navigation">
        @if($previous)
            <a class="help-pager-link help-pager-prev" href="{{ route('manager.help.show', $previous['slug']) }}">
                <span class="help-pager-label"><i class="fas fa-chevron-left"></i> Previous</span>
                <span class="help-pager-title">{{ $previous['title'] }}</span>
            </a>
        @else
            <span></span>
        @endif
        @if($next)
            <a class="help-pager-link help-pager-next" href="{{ route('manager.help.show', $next['slug']) }}">
                <span class="help-pager-label">Next <i class="fas fa-chevron-right"></i></span>
                <span class="help-pager-title">{{ $next['title'] }}</span>
            </a>
        @endif
    </nav>
</div>

<style>
.help-shell{max-width:800px}
.help-crumbs{display:flex;align-items:center;gap:10px;font-size:12px;margin-bottom:18px;color:var(--wp-text-muted);min-width:0}
.help-crumbs a{color:var(--wp-primary);text-decoration:none;white-space:nowrap}
.help-crumbs a:hover{color:var(--wp-primary-hover)}
.help-crumbs a i{margin-right:4px}
.help-crumb-sep{opacity:.5}
.help-crumb-current{color:#ccc;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.help-article{border-color:var(--wp-border-light)}
.help-article-body{padding:40px 48px 48px;max-width:760px}
.help-pager{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:20px}
.help-pager-link{display:flex;flex-direction:column;gap:4px;background:var(--wp-white);border:1px solid var(--wp-border-light);border-radius:8px;padding:14px 18px;text-decoration:none;color:inherit;min-width:0;transition:border-color .18s ease,background .18s ease}
.help-pager-link:hover{border-color:var(--wp-primary);background:#323232;color:inherit}
.help-pager-next{text-align:right;align-items:flex-end}
.help-pager-label{font-size:11px;text-transform:uppercase;letter-spacing:.6px;color:var(--wp-text-muted)}
.help-pager-title{font-weight:600;color:#fff;font-size:13px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%}
@media(max-width:640px){.help-article-body{padding:24px 18px 28px}.help-pager{grid-template-columns:1fr}.help-pager-next{text-align:left;align-items:flex-start}}
</style>
@include('manager.help._prose-styles')
@endsection
