<form method="post" action="{{ $ccavenue_url ?? config('services.ccavenue.url') }}" name="redirect">
    <input type="hidden" name="encRequest" value="{{ $encRequest }}">
    <input type="hidden" name="access_code" value="{{ $accessCode }}">
    <noscript>
        <input type="submit" value="Click here if you are not redirected"/>
    </noscript>
</form>
<script>document.redirect.submit();</script>
