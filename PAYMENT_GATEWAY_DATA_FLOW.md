# Payment Gateway Data Flow Documentation

This document explains what data is sent to CCAvenue and PayPal, and how values are updated after payment.

---

## 1. CCAvenue Payment Flow

### 1.1 Data Sent to CCAvenue (Request)

When initiating a payment, the following data is sent to CCAvenue:

#### For Invoice Payments (Registration Payments):
```php
$orderData = [
    'order_id' => 'TIN-BTS-2026-EXH-123456_1704067200',  // TIN + timestamp
    'amount' => '50000.00',                               // Invoice total
    'currency' => 'INR',                                  // Currency
    'redirect_url' => 'https://domain.com/registration/payment/callback/ccavenue',
    'cancel_url' => 'https://domain.com/registration/payment/callback/ccavenue',
    'billing_name' => 'John Doe',                         // Contact name
    'billing_address' => '123 Main Street',              // Address
    'billing_city' => 'Bangalore',                        // City
    'billing_state' => 'Karnataka',                      // State
    'billing_zip' => '560001',                           // Postal code
    'billing_country' => 'India',                         // Country
    'billing_tel' => '9876543210',                       // Phone (without country code)
    'billing_email' => 'john@example.com',               // Email
];
```

#### For Ticket Payments:
```php
$paymentData = [
    'order_id' => 'TKT-BEN-2026-000005_1704067200',      // Order number + timestamp
    'amount' => '5000.00',                                // Order total
    'currency' => 'INR',                                  // Currency
    'redirect_url' => 'https://domain.com/tickets/bts-2026/payment/callback/ccavenue',
    'cancel_url' => 'https://domain.com/tickets/bts-2026/payment/callback/ccavenue',
    'billing_name' => 'Jane Smith',                       // Contact name
    'billing_address' => 'Company Name',                  // Company name
    'billing_city' => 'Mumbai',                          // City
    'billing_state' => 'Maharashtra',                     // State
    'billing_zip' => '',                                 // Empty for tickets
    'billing_country' => 'India',                        // Country
    'billing_tel' => '9876543210',                       // Phone
    'billing_email' => 'jane@example.com',              // Email
];
```

#### API Request Structure:
The data is encrypted and sent as:
```php
$apiRequest = [
    'enc_request' => 'encrypted_data_string',            // AES encrypted request
    'access_code' => 'AVJS71ME17AS68SJSA',              // CCAvenue access code
    'command' => 'initiateTransaction',                  // API command
    'request_type' => 'JSON',                           // Request format
    'response_type' => 'JSON',                           // Response format
    'version' => '1.1',                                 // API version
];
```

### 1.2 Data Received from CCAvenue (Response)

After payment, CCAvenue redirects back with encrypted response. When decrypted, it contains:

```php
$responseArray = [
    'order_id' => 'TIN-BTS-2026-EXH-123456_1704067200',  // Original order ID
    'tracking_id' => '123456789012',                     // CCAvenue transaction ID
    'bank_ref_no' => '987654321098',                     // Bank reference number
    'order_status' => 'Success',                         // Payment status
    'failure_message' => '',                             // Error message (if failed)
    'payment_mode' => 'Credit Card',                     // Payment method used
    'card_name' => 'VISA',                               // Card type
    'status_code' => '0',                                // Status code
    'status_message' => 'Success',                       // Status message
    'currency' => 'INR',                                 // Currency
    'amount' => '50000.00',                              // Original amount
    'mer_amount' => '50000.00',                         // Merchant amount
    'trans_date' => '01/01/2024 12:30:45',              // Transaction date
    'auth_desc' => 'Y',                                  // Authorization description
];
```

### 1.3 Post-Payment Updates (CCAvenue)

After successful payment, the following updates are made:

#### 1. Update `payment_gateway_response` table:
```php
DB::table('payment_gateway_response')
    ->where('order_id', $orderId)
    ->update([
        'amount' => $responseArray['mer_amount'],         // Amount paid
        'transaction_id' => $responseArray['tracking_id'], // CCAvenue transaction ID
        'payment_method' => $responseArray['payment_mode'], // Payment method
        'trans_date' => $transDate,                      // Transaction date
        'reference_id' => $responseArray['bank_ref_no'], // Bank reference
        'response_json' => json_encode($responseArray),  // Full response
        'status' => 'Success',                           // Status
        'updated_at' => now(),
    ]);
```

#### 2. For Invoice Payments - Update `invoices` table:
```php
$invoice->update([
    'payment_status' => 'paid',                          // Mark as paid
    'amount_paid' => $responseArray['mer_amount'],       // Amount paid
    'pending_amount' => 0,                               // Clear pending
    'updated_at' => now(),
]);
```

#### 3. For Ticket Payments - Update `ticket_orders` table:
```php
$order->update([
    'status' => 'paid',                                   // Mark as paid
]);
```

#### 4. Create record in `ticket_payments` table (for tickets):
```php
TicketPayment::create([
    'order_ids_json' => [$order->id],                    // Order IDs array
    'method' => strtolower($responseArray['payment_mode']), // Payment method
    'amount' => $responseArray['mer_amount'],            // Amount
    'status' => 'completed',                             // Status
    'gateway_txn_id' => $responseArray['tracking_id'],   // Transaction ID
    'gateway_name' => 'ccavenue',                        // Gateway name
    'paid_at' => $transDate,                             // Payment date
    'pg_request_json' => [],                             // Request data
    'pg_response_json' => $responseArray,                // Full response
    'pg_webhook_json' => [],                             // Webhook data
]);
```

#### 5. Create record in `payments` table (for all payments):
```php
Payment::create([
    'invoice_id' => $invoice->id ?? null,                // Invoice ID (null for tickets)
    'payment_method' => $responseArray['payment_mode'],   // Payment method
    'amount' => $responseArray['mer_amount'],            // Amount
    'amount_paid' => $responseArray['mer_amount'],      // Amount paid
    'amount_received' => $responseArray['mer_amount'],   // Amount received
    'transaction_id' => $responseArray['tracking_id'],    // Transaction ID
    'pg_result' => $responseArray['order_status'],       // Payment result
    'track_id' => $responseArray['tracking_id'],         // Tracking ID
    'pg_response_json' => json_encode($responseArray),   // Full response
    'payment_date' => $transDate,                        // Payment date
    'currency' => 'INR',                                 // Currency
    'status' => 'successful',                           // Status
    'order_id' => $orderId,                              // Order ID
    'user_id' => $application->user_id ?? null,          // User ID
]);
```

---

## 2. PayPal Payment Flow

### 2.1 Data Sent to PayPal (Request)

When initiating a PayPal payment, the following data is sent:

#### For Invoice Payments:
```php
$orderRequest = [
    'checkoutPaymentIntent' => 'CAPTURE',                // Payment intent
    'purchaseUnits' => [
        [
            'referenceId' => 'INV-2026-001',             // Invoice number
            'amount' => [
                'currencyCode' => 'USD',                 // Currency (converted from INR)
                'value' => '602.41',                     // Amount in USD
            ],
        ],
    ],
    'applicationContext' => [
        'returnUrl' => 'https://domain.com/registration/payment/callback/paypal',
        'cancelUrl' => 'https://domain.com/registration/payment/callback/paypal',
    ],
];
```

#### For Ticket Payments:
```php
$orderRequest = [
    'checkoutPaymentIntent' => 'CAPTURE',                // Payment intent
    'purchaseUnits' => [
        [
            'referenceId' => 'TKT-BEN-2026-000005',      // Order number
            'amount' => [
                'currencyCode' => 'USD',                 // Currency (converted from INR)
                'value' => '60.24',                      // Amount in USD
            ],
        ],
    ],
    'applicationContext' => [
        'returnUrl' => 'https://domain.com/tickets/bts-2026/payment/callback/paypal',
        'cancelUrl' => 'https://domain.com/tickets/bts-2026/payment/callback/paypal',
    ],
];
```

**Note:** Amount is converted from INR to USD using exchange rate from config:
```php
$usdRate = config('constants.USD_RATE', 83);  // Default: 83
$amountUSD = $amountINR / $usdRate;
```

### 2.2 Data Received from PayPal (Response)

After payment, PayPal redirects back with order ID. We then capture the order and receive:

```php
$captureResult = [
    'id' => '5O190127TN364715T',                        // PayPal order ID
    'status' => 'COMPLETED',                             // Order status
    'purchaseUnits' => [
        [
            'referenceId' => 'TKT-BEN-2026-000005',     // Our order reference
            'payments' => [
                'captures' => [
                    [
                        'id' => '1JU08902MN917445L',     // Capture ID
                        'status' => 'COMPLETED',         // Capture status
                        'amount' => [
                            'currencyCode' => 'USD',     // Currency
                            'value' => '60.24',          // Amount
                        ],
                        'createTime' => '2024-01-01T12:30:45Z',
                    ],
                ],
            ],
        ],
    ],
];
```

### 2.3 Post-Payment Updates (PayPal)

After successful payment, the following updates are made:

#### 1. Update `payment_gateway_response` table:
```php
DB::table('payment_gateway_response')
    ->where('payment_id', $paypalOrderId)
    ->update([
        'status' => 'Success',                            // Status
        'response_json' => json_encode($captureResult),  // Full response
        'updated_at' => now(),
    ]);
```

#### 2. For Invoice Payments - Update `invoices` table:
```php
$invoice->update([
    'payment_status' => 'paid',                           // Mark as paid
    'amount_paid' => $amount * $usdRate,                 // Convert back to INR
    'pending_amount' => 0,                               // Clear pending
    'updated_at' => now(),
]);
```

#### 3. For Ticket Payments - Update `ticket_orders` table:
```php
$order->update([
    'status' => 'paid',                                  // Mark as paid
]);
```

#### 4. Create record in `ticket_payments` table (for tickets):
```php
TicketPayment::create([
    'order_ids_json' => [$order->id],                    // Order IDs array
    'method' => 'card',                                  // Payment method
    'amount' => $amount * $usdRate,                      // Convert back to INR
    'status' => 'completed',                             // Status
    'gateway_txn_id' => $paypalOrderId,                  // PayPal order ID
    'gateway_name' => 'paypal',                          // Gateway name
    'paid_at' => now(),                                  // Payment date
    'pg_request_json' => [],                            // Request data
    'pg_response_json' => (array) $captureResult,       // Full response
    'pg_webhook_json' => [],                             // Webhook data
]);
```

#### 5. Create record in `payments` table (for all payments):
```php
Payment::create([
    'invoice_id' => $invoice->id ?? null,                // Invoice ID (null for tickets)
    'payment_method' => 'PayPal',                        // Payment method
    'amount' => $amount * $usdRate,                      // Convert back to INR
    'amount_paid' => $amount * $usdRate,                 // Amount paid
    'amount_received' => $amount * $usdRate,            // Amount received
    'transaction_id' => $paypalOrderId,                 // PayPal order ID
    'pg_result' => $status,                              // Payment result
    'track_id' => $paypalOrderId,                        // Tracking ID
    'pg_response_json' => json_encode($captureResult),  // Full response
    'payment_date' => now(),                            // Payment date
    'currency' => 'USD',                                 // Currency
    'status' => 'successful',                           // Status
    'order_id' => $orderId,                              // Order ID
    'user_id' => $application->user_id ?? null,         // User ID
]);
```

---

## 3. Summary of Database Updates

### Tables Updated After Payment:

1. **`payment_gateway_response`**
   - Stores initial payment request and gateway response
   - Updated with transaction details after payment

2. **`invoices`** (for invoice payments)
   - `payment_status` → 'paid'
   - `amount_paid` → Payment amount
   - `pending_amount` → 0

3. **`ticket_orders`** (for ticket payments)
   - `status` → 'paid'

4. **`ticket_payments`** (for ticket payments)
   - New record created with payment details

5. **`payments`** (for all payments)
   - New record created for unified payment tracking

### Email Notifications:

After successful payment:
- **Ticket Payments**: `TicketRegistrationMail` sent to contact email
- **Invoice Payments**: Payment confirmation email sent

---

## 4. Key Points

1. **Order ID Format:**
   - Invoice: `{TIN}_{timestamp}` (e.g., `TIN-BTS-2026-EXH-123456_1704067200`)
   - Ticket: `{order_no}_{timestamp}` (e.g., `TKT-BEN-2026-000005_1704067200`)

2. **Currency Conversion:**
   - CCAvenue: Always INR
   - PayPal: Converted to USD using exchange rate (default: 83)

3. **Payment Storage:**
   - All payments stored in `payments` table for unified tracking
   - Ticket payments also stored in `ticket_payments` table
   - Gateway responses stored in `payment_gateway_response` table

4. **Transaction IDs:**
   - CCAvenue: `tracking_id` from response
   - PayPal: PayPal order ID (`id` from response)

5. **Status Values:**
   - CCAvenue: `order_status` = 'Success' or 'Failure'
   - PayPal: `status` = 'COMPLETED' or other status

