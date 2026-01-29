<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>PayPal Checkout</title>
    <script src="https://www.paypal.com/sdk/js?client-id={{ env('PAYPAL_CLIENT_ID') }}&currency=USD"></script>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h2 class="text-center">PayPal Checkout</h2>
                <p class="lead text-center">Secure and fast payment processing</p>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h4>Order Information</h4>
                        <p><strong>Order ID:</strong> 123456</p>
                        <p><strong>Billing Company:</strong> ABC Corp</p>
                        <p><strong>Billing Email:</strong> example@abc.com</p>
                        <p><strong>Billing Address:</strong> 123 Main St, Suite 100</p>
                        <p><strong>Billing Country:</strong> USA</p>
                        <p><strong>Zipcode:</strong> 12345</p>
                        <h5>Order Items:</h5>
                        <ul>
                            <li>Item 1</li>
                            <li>Item 2</li>
                            <li>Item 3</li>
                        </ul>
                    </div>
                    <div class="col-md-6 text-center">
                        <div id="paypal-button-container" class="mt-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return fetch('/paypal/create-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ amount: "100.00" })
                }).then(response => response.json())
                  .then(order => order.id);
            },
            onApprove: function(data, actions) {
                return fetch(`/paypal/capture-order/${data.orderID}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => response.json())
                  .then(details => {
                      alert('Transaction completed by ' + details.payer.name.given_name);
                  });
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>
