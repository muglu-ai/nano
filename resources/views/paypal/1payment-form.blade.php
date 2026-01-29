<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayPal Payment</title>
</head>
<body>
    <h2>Pay with PayPal</h2>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif
    @if(session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif

    <form action="{{ route('paypal.create') }}" method="POST">
        @csrf
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        <br><br>

        <label for="amount">Amount (USD):</label>
        <input type="number" name="amount" step="0.01" required>
        <br><br>

        <button type="submit">Pay with PayPal</button>
    </form>
</body>
</html>
