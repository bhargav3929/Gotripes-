{{--
    Tenant developer theme. Include as the LAST thing in <head> so the
    tenant's CSS wins over the platform's:
        @include('partials.tenant-theme', ['surface' => 'storefront'])   or 'dashboard'
    Renders nothing unless the super admin has the theme switched on and a
    version is live. Theme JS is skipped while a platform admin is viewing.
--}}
@php
    $gtTheme = \App\Services\TenantTheme\TenantTheme::for(current_company());
    $gtSurface = ($surface ?? 'storefront') === 'dashboard' ? 'dashboard' : 'storefront';
@endphp
@if($gtTheme)
    {{-- Developer theme: version #{{ $gtTheme->deployment->id }}{{ $gtTheme->deployment->shortSha() ? ' (' . $gtTheme->deployment->shortSha() . ')' : '' }} --}}
    @if($gtFonts = $gtTheme->googleFontsUrl())
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="{{ $gtFonts }}">
    @endif
    @if(($gtVars = $gtTheme->cssVariables() . $gtTheme->fontRules()) !== '')
    <style id="gt-tenant-theme-vars">{!! $gtVars !!}</style>
    @endif
    <script>
        (function () {
            var d = document.documentElement;
            d.setAttribute('data-gt-theme', @json($gtTheme->company->slug));
            d.setAttribute('data-gt-surface', @json($gtSurface));
            d.setAttribute('data-gt-page', @json(request()->route()?->getName() ?? '/' . ltrim(request()->path(), '/')));
            window.GoTripsTheme = Object.freeze({
                tenant: @json($gtTheme->company->slug),
                surface: @json($gtSurface),
                page: d.getAttribute('data-gt-page'),
                asset: function (path) { return @json(rtrim($gtTheme->url(''), '/')) + '/' + String(path).replace(/^\/+/, ''); }
            });
        })();
    </script>
    @if($gtTheme->wants($gtSurface, 'css'))
    <link rel="stylesheet" id="gt-tenant-theme-css" href="{{ $gtTheme->url($gtTheme->fileFor($gtSurface, 'css')) }}">
    @endif
    @if($gtTheme->wants($gtSurface, 'js') && \App\Services\TenantTheme\TenantTheme::scriptsAllowedForViewer())
    <script id="gt-tenant-theme-js" src="{{ $gtTheme->url($gtTheme->fileFor($gtSurface, 'js')) }}" defer></script>
    @endif
@endif
