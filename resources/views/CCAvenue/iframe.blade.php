@if(isset($payment_url, $encrypted_data, $access_code))
<!DOCTYPE html>
<html>
<head>
    <title>Redirecting to CCAvenue Payment...</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>
<body>
    <p style="text-align:center;margin-top:50px;font-family:sans-serif;">
        Redirecting to secure payment page, please wait...
    </p>
    <form id="ccavenuePaymentForm" method="post" action="{{ $payment_url }}">
        <input type="hidden" name="encRequest" value="{{ $encrypted_data }}">
        <input type="hidden" name="access_code" value="{{ $access_code }}">
    </form>
    <script>
        document.getElementById('ccavenuePaymentForm').submit();
    </script>
</body>
</html>
@else
    <div style="color:red;text-align:center;margin-top:50px;">
        Payment redirection parameters missing. Please start your payment from the payment form.
    </div>
@endif
