<form method="post" action="/payment/ccavenue">
    @csrf
    <input type="hidden" name="amount" value="100">
    <button type="submit">Pay with CCAvenue</button>
</form>

<form method="post" action="/payment/paypal">
    @csrf
    <input type="hidden" name="amount" value="10">
    <button type="submit">Pay with PayPal</button>
</form>
