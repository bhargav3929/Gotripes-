<form method="POST" action="{{ route('payment.initiate') }}">
    @csrf
    <label>Amount (AED)</label>
    <input type="text" name="amount" required>
    <button type="submit">Pay Now</button>
</form>
