@extends('layouts.manager')

@section('title', 'Help Guide')
@section('page-title', 'Help Guide')

@section('content')
<div class="help-shell">
    <header class="help-hero">
        <div class="help-hero-eyebrow"><i class="fas fa-book"></i> Product help guide</div>
        @if($introHtml)
            <div class="help-prose help-intro">{!! $introHtml !!}</div>
        @else
            <h1>Everything the site sells, explained for the people who support it</h1>
            <p>One guide per product: what it is, who uses it, how the customer goes through it, where it lives in the manager portal, and the questions that come up most.</p>
        @endif
    </header>

    @if($guides->isEmpty())
        <div class="wp-card help-empty">
            <i class="fas fa-book-open"></i>
            <strong>The guide has not been published yet</strong>
            <span>Guides are markdown files in <code>docs/help</code>. As soon as the first one is committed it appears here.</span>
        </div>
    @else
        <ol class="help-list">
            @foreach($guides as $guide)
                <li>
                    <a class="help-card" href="{{ route('manager.help.show', $guide['slug']) }}">
                        <span class="help-card-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="help-card-body">
                            <span class="help-card-title">{{ $guide['title'] }}</span>
                            @if($guide['summary'])
                                <span class="help-card-summary">{{ $guide['summary'] }}</span>
                            @endif
                        </span>
                        <i class="fas fa-arrow-right help-card-arrow"></i>
                    </a>
                </li>
            @endforeach
        </ol>
    @endif
</div>

<style>
.help-shell{max-width:840px}
.help-hero{padding:8px 0 32px;border-bottom:1px solid var(--wp-border-light);margin-bottom:32px}
.help-hero-eyebrow{font-size:11px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;color:var(--wp-primary);margin-bottom:14px}
.help-hero-eyebrow i{margin-right:6px}
.help-hero h1{font-size:30px;line-height:1.15;font-weight:700;letter-spacing:-.02em;color:#fff;margin:0 0 12px;max-width:640px}
.help-hero p{color:#b5b5b5;font-size:14px;line-height:1.7;max-width:620px;margin:0}
/* One column for the whole intro so its headings, rules and paragraphs
   share an edge instead of the prose wrapping far short of the rules. */
.help-intro{max-width:680px}
.help-intro h1{font-size:30px;line-height:1.15;letter-spacing:-.02em;margin-bottom:12px}
.help-intro>*:last-child{margin-bottom:0}
.help-list{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:12px}
.help-card{display:grid;grid-template-columns:44px minmax(0,1fr) 20px;gap:16px;align-items:center;background:var(--wp-white);border:1px solid var(--wp-border-light);border-radius:8px;padding:18px 20px;text-decoration:none;color:inherit;transition:border-color .18s ease,transform .18s ease,background .18s ease}
.help-card:hover{border-color:var(--wp-primary);background:#323232;transform:translateX(4px);color:inherit}
.help-card-index{font-family:Menlo,Consolas,monospace;font-size:13px;color:var(--wp-primary);opacity:.85}
.help-card-body{display:flex;flex-direction:column;gap:4px;min-width:0}
.help-card-title{font-size:16px;font-weight:700;color:#fff;letter-spacing:-.01em;line-height:1.3}
.help-card-summary{font-size:13px;color:#a8a8a8;line-height:1.6}
.help-card-arrow{color:var(--wp-text-muted);font-size:13px;transition:color .18s ease}
.help-card:hover .help-card-arrow{color:var(--wp-primary)}
.help-empty{padding:48px 24px;text-align:center;display:flex;flex-direction:column;gap:8px;align-items:center;color:#aaa;border-style:dashed}
.help-empty i{font-size:26px;color:var(--wp-primary);opacity:.7;margin-bottom:6px}
.help-empty strong{color:#eee;font-size:15px}
.help-empty span{font-size:12px;max-width:380px}
.help-empty code{color:var(--wp-primary);background:rgba(255,215,0,.08);padding:1px 6px;border-radius:4px}
@media(max-width:640px){.help-hero h1,.help-intro h1{font-size:24px}.help-card{grid-template-columns:32px minmax(0,1fr);padding:16px}.help-card-arrow{display:none}}
</style>
@include('manager.help._prose-styles')
@endsection
