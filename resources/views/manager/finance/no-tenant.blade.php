@extends('layouts.manager')
@section('title', 'Finance')
@section('page-title', 'Finance')

@section('content')
<div style="background:#1a1a1a; border:1px solid rgba(255,215,0,0.15); border-radius:12px; padding:48px 32px; text-align:center; max-width:540px; margin:40px auto;">
    <i class="fas fa-circle-info" style="font-size:48px; color:#FFD700; margin-bottom:16px;"></i>
    <h2 style="color:#fff; font-size:22px; margin-bottom:10px;">Not a tenant subdomain</h2>
    <p style="color:#aaa; font-size:14px; line-height:1.6;">
        Finance is only available when you log in to a partner subdomain
        (e.g. <code style="background:#222; padding:2px 6px; border-radius:4px;">yourbrand.gotrips.ai/manager</code>),
        not the main GoTrips site.
    </p>
</div>
@endsection
