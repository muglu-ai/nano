<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\BillingDetail;
use App\Models\Ticket\TicketOrder;
use App\Models\Ticket\TicketPayment;
use App\Models\Ticket\TicketRegistrationTracking;
use App\Models\Events;
use App\Services\CcAvenueService;
use App\Mail\TicketRegistrationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\CheckoutPaymentIntent;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;
use PaypalServerSdkLib\Models\Builders\OrderApplicationContextBuilder;

class RegistrationPaymentController extends Controller
{
    private $ccAvenueService;
    private $paypalClient;

    public function __construct()
    {
        $this->ccAvenueService = new CcAvenueService();
        
        // Initialize PayPal client with mode-specific credentials
        $paypalMode = strtolower(config('constants.PAYPAL_MODE', 'live'));
        $isSandbox = ($paypalMode === 'sandbox');
        
        // Get credentials based on mode
        if ($isSandbox) {
            $clientId = config('constants.PAYPAL_SANDBOX_CLIENT_ID');
            $clientSecret = config('constants.PAYPAL_SANDBOX_SECRET');
            $environment = 'Sandbox';
            
            // If sandbox credentials are not set or empty, fall back to live credentials
            // Note: Live credentials won't work in sandbox environment, but this prevents errors
            if (empty($clientId) || empty($clientSecret) || trim($clientId) === '' || trim($clientSecret) === '') {
                Log::warning('PayPal sandbox credentials not set, falling back to live credentials', [
                    'mode' => $paypalMode,
                    'sandbox_id_empty' => empty($clientId),
                    'sandbox_secret_empty' => empty($clientSecret)
                ]);
                $clientId = config('constants.PAYPAL_LIVE_CLIENT_ID');
                $clientSecret = config('constants.PAYPAL_LIVE_SECRET');
                // Keep environment as Sandbox - user should get proper sandbox credentials
            }
        } else {
            $clientId = config('constants.PAYPAL_LIVE_CLIENT_ID');
            $clientSecret = config('constants.PAYPAL_LIVE_SECRET');
            $environment = 'Production';
        }
        
        // Trim whitespace and check again
        $clientId = trim($clientId ?? '');
        $clientSecret = trim($clientSecret ?? '');
        
        // Fallback to legacy credentials if mode-specific ones are still not set
        if (empty($clientId) || empty($clientSecret)) {
            $legacyId = config('constants.PAYPAL_CLIENT_ID');
            $legacySecret = config('constants.PAYPAL_SECRET');
            if (!empty($legacyId) && !empty($legacySecret)) {
                $clientId = trim($legacyId);
                $clientSecret = trim($legacySecret);
                Log::info('Using legacy PayPal credentials');
            }
        }
        
        // Validate credentials exist
        if (empty($clientId) || empty($clientSecret)) {
            $errorDetails = [
                'mode' => $paypalMode,
                'is_sandbox' => $isSandbox,
                'sandbox_id' => !empty(config('constants.PAYPAL_SANDBOX_CLIENT_ID')) ? 'set' : 'empty',
                'sandbox_secret' => !empty(config('constants.PAYPAL_SANDBOX_SECRET')) ? 'set' : 'empty',
                'live_id' => !empty(config('constants.PAYPAL_LIVE_CLIENT_ID')) ? 'set' : 'empty',
                'live_secret' => !empty(config('constants.PAYPAL_LIVE_SECRET')) ? 'set' : 'empty',
            ];
            Log::error('PayPal credentials not configured', $errorDetails);
            throw new \Exception('PayPal credentials not configured. Please set PAYPAL_SANDBOX_CLIENT_ID/SECRET for sandbox mode or PAYPAL_LIVE_CLIENT_ID/SECRET for live mode in config/constants.php. Current mode: ' . $paypalMode);
        }
        
        $this->paypalClient = PaypalServerSdkClientBuilder::init()
            ->clientCredentialsAuthCredentials(
                ClientCredentialsAuthCredentialsBuilder::init($clientId, $clientSecret)
                )
            ->environment($environment)
            ->build();

        // Debug logging for PayPal client initialization
        Log::info('PayPal Client Initialized', [
            'mode' => $paypalMode,
            'environment' => $environment,
            'client_id_length' => strlen($clientId ?? ''),
            'client_secret_length' => strlen($clientSecret ?? ''),
            'client_initialized' => $this->paypalClient ? 'yes' : 'no',
            'client_class' => $this->paypalClient ? get_class($this->paypalClient) : 'null'
        ]);
    }

    /**
     * Show order lookup form
     */
    public function showLookup()
    {
        return view('payment.registration-lookup');
    }

    /**
     * Handle order lookup by TIN or email (either one is sufficient)
     */
    public function lookupOrder(Request $request)
    {
        $request->validate([
            'tin_no' => 'nullable|string',
            'email' => 'nullable|email',
        ], [
            'email.email' => 'Please enter a valid email address.',
        ]);

        $tinNo = trim($request->tin_no ?? '');
        $email = trim($request->email ?? '');

        // At least one field must be provided
        if (empty($tinNo) && empty($email)) {
            return back()
                ->withInput()
                ->with('error', 'Please provide either TIN Number or Email Address.');
        }

        $application = null;
        $invoice = null;

        // Search by TIN if provided
        if (!empty($tinNo)) {
            $application = Application::where('application_id', $tinNo)->first();
            
            if ($application) {
                // Find invoice for this application
                $invoice = Invoice::where('application_id', $application->id)
                    ->where('payment_status', '!=', 'paid')
                    ->orderBy('created_at', 'desc')
                    ->first();
            }
        }

        // If not found by TIN, or if only email provided, search by email
        if (!$application || !$invoice) {
            $applicationsByEmail = collect();

            // Search in EventContact
            $eventContacts = \App\Models\EventContact::where('email', 'like', '%' . $email . '%')
                ->when(!empty($email), function($q) use ($email) {
                    $q->whereRaw('LOWER(email) = ?', [strtolower($email)]);
                })
                ->get();

            foreach ($eventContacts as $contact) {
                if ($contact->application_id) {
                    $app = Application::find($contact->application_id);
                    if ($app) {
                        $applicationsByEmail->push($app);
                    }
                }
            }

            // Search in Application company_email
            if (!empty($email)) {
                $appsByCompanyEmail = Application::whereRaw('LOWER(company_email) = ?', [strtolower($email)])->get();
                $applicationsByEmail = $applicationsByEmail->merge($appsByCompanyEmail);
            }

            // Search in BillingDetail
            if (!empty($email)) {
                $billingDetails = BillingDetail::whereRaw('LOWER(email) = ?', [strtolower($email)])->get();
                foreach ($billingDetails as $billing) {
                    if ($billing->application_id) {
                        $app = Application::find($billing->application_id);
                        if ($app) {
                            $applicationsByEmail->push($app);
                        }
                    }
                }
            }

            // Remove duplicates
            $applicationsByEmail = $applicationsByEmail->unique('id');

            // If we have applications by email, find the one with unpaid invoice
            if ($applicationsByEmail->isNotEmpty()) {
                foreach ($applicationsByEmail as $app) {
                    $inv = Invoice::where('application_id', $app->id)
                        ->where('payment_status', '!=', 'paid')
                        ->orderBy('created_at', 'desc')
                        ->first();
                    
                    if ($inv) {
                        $application = $app;
                        $invoice = $inv;
                        break;
                    }
                }
            }
        }

        // If both TIN and email provided, verify they match
        if (!empty($tinNo) && !empty($email) && $application) {
            $emailMatches = false;
            
            // Check EventContact email
            $eventContact = \App\Models\EventContact::where('application_id', $application->id)->first();
            if ($eventContact && strtolower($eventContact->email) === strtolower($email)) {
                $emailMatches = true;
            }

            // Check company email
            if (!$emailMatches && $application->company_email && strtolower($application->company_email) === strtolower($email)) {
                $emailMatches = true;
            }

            // Check BillingDetail email
            if (!$emailMatches) {
                $billingDetail = BillingDetail::where('application_id', $application->id)->first();
                if ($billingDetail && strtolower($billingDetail->email) === strtolower($email)) {
                    $emailMatches = true;
                }
            }

            if (!$emailMatches) {
                return back()
                    ->withInput()
                    ->with('error', 'TIN Number and Email do not match. Please verify your details.');
            }
        }

        if (!$application) {
            return back()
                ->withInput()
                ->with('error', 'No registration found with the provided information.');
        }

        if (!$invoice) {
            return back()
                ->withInput()
                ->with('error', 'No pending payment found for this registration.');
        }

        // Store in session for payment processing
        session([
            'payment_tin' => $application->application_id,
            'payment_email' => $email ?: ($eventContact->email ?? $application->company_email ?? ''),
            'payment_invoice_id' => $invoice->id,
            'payment_application_id' => $application->id,
        ]);

        return redirect()->route('registration.payment.select', $invoice->invoice_no);
    }

    /**
     * Show payment gateway selection page
     */
    public function showPaymentSelection($invoiceNo)
    {
        $invoice = Invoice::where('invoice_no', $invoiceNo)->firstOrFail();

        // Verify session matches
        if (session('payment_invoice_id') != $invoice->id) {
            return redirect()->route('registration.payment.lookup')
                ->with('error', 'Session expired. Please lookup your order again.');
        }

        $application = Application::find($invoice->application_id);
        
        // Get billing details
        $billingDetail = $this->getBillingDetails($invoice, $application);

        return view('payment.registration-payment-select', compact('invoice', 'application', 'billingDetail'));
    }

    /**
     * Process payment - redirect to selected gateway
     */
    public function processPayment(Request $request, $invoiceNo)
    {
        $request->validate([
            'payment_method' => 'required|in:CCAvenue,PayPal',
        ]);

        $invoice = Invoice::where('invoice_no', $invoiceNo)->firstOrFail();

        // Verify session
        if (session('payment_invoice_id') != $invoice->id) {
            return redirect()->route('registration.payment.lookup')
                ->with('error', 'Session expired. Please lookup your order again.');
        }

        $application = Application::find($invoice->application_id);
        $billingDetail = $this->getBillingDetails($invoice, $application);

        $paymentMethod = $request->payment_method;

        if ($paymentMethod === 'CCAvenue') {
            return $this->processCcAvenuePayment($invoice, $application, $billingDetail);
        } elseif ($paymentMethod === 'PayPal') {
            return $this->processPayPalPayment($invoice, $application, $billingDetail);
        }

        return back()->with('error', 'Invalid payment method selected.');
    }

    /**
     * Process CCAvenue payment
     */
    private function processCcAvenuePayment($invoice, $application, $billingDetail)
    {
        try {
            // Generate order_id with TIN prefix
            $tinPrefix = $application && $application->application_id
                ? $application->application_id
                : $invoice->invoice_no;
            $orderIdWithTimestamp = $tinPrefix . '_' . time();

            $orderData = [
                'order_id' => $orderIdWithTimestamp,
                'amount' => number_format($invoice->total_final_price, 2, '.', ''),
                'currency' => $invoice->currency ?? 'INR',
                'redirect_url' => route('registration.payment.callback', ['gateway' => 'ccavenue']),
                'cancel_url' => route('registration.payment.callback', ['gateway' => 'ccavenue']),
                'billing_name' => $billingDetail->contact_name ?? '',
                'billing_address' => $billingDetail->address ?? '',
                'billing_city' => $billingDetail->city_name ?? ($billingDetail->city_id ?? ''),
                'billing_state' => $billingDetail->state->name ?? '',
                'billing_zip' => $billingDetail->postal_code ?? '',
                'billing_country' => $billingDetail->country->name ?? '',
                'billing_tel' => preg_replace('/^.*-/', '', $billingDetail->phone ?? ''),
                'billing_email' => $billingDetail->email ?? '',
            ];

            // Initiate transaction
            $result = $this->ccAvenueService->initiateTransaction($orderData);

            if ($result['success']) {
                // Store payment gateway response
                DB::table('payment_gateway_response')->insert([
                    'merchant_data' => json_encode($orderData),
                    'order_id' => $orderData['order_id'],
                    'amount' => $orderData['amount'],
                    'status' => 'Pending',
                    'gateway' => 'CCAvenue',
                    'currency' => $orderData['currency'],
                    'email' => $orderData['billing_email'],
                    'user_id' => $application ? $application->user_id : null,
                    'created_at' => now(),
                ]);

                // Store in session
                session([
                    'payment_order_id' => $orderData['order_id'],
                    'payment_invoice_no' => $invoice->invoice_no,
                ]);

                // Redirect to payment URL
                return redirect($result['payment_url']);
            } else {
                return back()->with('error', 'Failed to initiate payment: ' . ($result['error'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('CCAvenue Payment Initiation Error', [
                'error' => $e->getMessage(),
                'invoice_no' => $invoice->invoice_no,
            ]);
            return back()->with('error', 'An error occurred while initiating payment. Please try again.');
        }
    }

    /**
     * Process PayPal payment
     */
    private function processPayPalPayment($invoice, $application, $billingDetail)
    {
        // Debug logging at start of PayPal payment processing
        Log::info('PayPal Payment Processing Started', [
            'invoice_id' => $invoice->id ?? null,
            'invoice_no' => $invoice->invoice_no ?? null,
            'application_id' => $application->id ?? null,
            'billing_detail_keys' => is_array($billingDetail) ? array_keys($billingDetail) : 'not_array',
            'invoice_total' => $invoice->total_final_price ?? null,
            'invoice_currency' => $invoice->currency ?? null,
            'paypal_mode' => config('constants.PAYPAL_MODE'),
            'client_initialized' => $this->paypalClient ? 'yes' : 'no'
        ]);

        try {
            // Convert to USD if needed
            $amount = $invoice->total_final_price;
            $currency = $invoice->currency ?? 'INR';

            if ($currency === 'INR') {
                // Get exchange rate from config or calculate
                $usdRate = $invoice->usd_rate ?? config('constants.USD_RATE', 83);
                $amount = $amount / $usdRate;
                $currency = 'USD';
            }

            // Generate order_id
            $tinPrefix = $application && $application->application_id
                ? $application->application_id
                : $invoice->invoice_no;
            $orderIdWithTimestamp = $tinPrefix . '_' . time();

            // Create PayPal order
            $orderRequest = OrderRequestBuilder::init()
                ->checkoutPaymentIntent(CheckoutPaymentIntent::CAPTURE)
                ->purchaseUnits([
                    PurchaseUnitRequestBuilder::init()
                        ->referenceId($invoice->invoice_no)
                        ->amount(
                            AmountWithBreakdownBuilder::init()
                                ->currencyCode($currency)
                                ->value(number_format($amount, 2, '.', ''))
                        )
                        ->build()
                ])
                ->applicationContext(
                    OrderApplicationContextBuilder::init()
                        ->returnUrl(route('registration.payment.callback', ['gateway' => 'paypal']))
                        ->cancelUrl(route('registration.payment.select', $invoice->invoice_no))
                        ->build()
                )
                ->build();

            // Debug logging before PayPal API call
            Log::info('CCAvenue PayPal Order Creation Debug', [
                'order_id' => $orderIdWithTimestamp,
                'amount' => $amount,
                'currency' => $currency,
                'paypal_mode' => config('constants.PAYPAL_MODE'),
                'paypal_client_initialized' => $this->paypalClient ? 'yes' : 'no',
                'order_request_type' => get_class($orderRequest),
                'return_url' => route('registration.payment.callback', ['gateway' => 'paypal']),
                'cancel_url' => route('registration.payment.select', $invoice->invoice_no)
            ]);

            try {
                $apiResponse = $this->paypalClient->getOrdersController()->createOrder($orderRequest);
                $paypalOrderId = $apiResponse->getResult()->getId();

                Log::info('CCAvenue PayPal Order Creation Success', [
                    'order_id' => $orderIdWithTimestamp,
                    'paypal_order_id' => $paypalOrderId
                ]);
            } catch (\PaypalServerSdkLib\Exceptions\ApiException $e) {
                Log::error('PayPal API Exception during order creation', [
                    'error' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'order_id' => $orderIdWithTimestamp,
                ]);

                // Check if it's the OAuth token error
                if (strpos($e->getMessage(), 'OAuthToken') !== false || strpos($e->getMessage(), 'addExpiryTime') !== false) {
                    return redirect()->route('registration.payment.select', $invoice->invoice_no)
                        ->with('error', 'Payment gateway authentication failed. Please try again in a few moments.');
                }

                throw $e;
            } catch (\Exception $e) {
                Log::error('Unexpected PayPal error during order creation', [
                    'error' => $e->getMessage(),
                    'order_id' => $orderIdWithTimestamp,
                ]);

                return redirect()->route('registration.payment.select', $invoice->invoice_no)
                    ->with('error', 'Payment processing failed. Please try again or contact support.');
            }

            // Store payment gateway response
            DB::table('payment_gateway_response')->insert([
                'merchant_data' => json_encode([
                    'order_id' => $orderIdWithTimestamp,
                    'paypal_order_id' => $paypalOrderId,
                    'amount' => $amount,
                    'currency' => $currency,
                ]),
                'order_id' => $orderIdWithTimestamp,
                'payment_id' => $paypalOrderId,
                'amount' => $amount,
                'status' => 'Pending',
                'gateway' => 'PayPal',
                'currency' => $currency,
                'email' => $billingDetail->email ?? '',
                'user_id' => $application ? $application->user_id : null,
                'created_at' => now(),
            ]);

            // Store in session
            session([
                'payment_order_id' => $orderIdWithTimestamp,
                'payment_paypal_order_id' => $paypalOrderId,
                'payment_invoice_no' => $invoice->invoice_no,
            ]);

            // Get approval URL from response
            $approvalUrl = null;
            foreach ($apiResponse->getResult()->getLinks() as $link) {
                if ($link->getRel() === 'approve') {
                    $approvalUrl = $link->getHref();
                    break;
                }
            }

            if ($approvalUrl) {
                return redirect($approvalUrl);
            } else {
                return back()->with('error', 'Failed to get PayPal approval URL.');
            }
        } catch (\Exception $e) {
            Log::error('PayPal Payment Initiation Error', [
                'error' => $e->getMessage(),
                'invoice_no' => $invoice->invoice_no,
            ]);
            return back()->with('error', 'An error occurred while initiating PayPal payment. Please try again.');
        }
    }

    /**
     * Handle payment callback from gateway
     */
    public function handleCallback(Request $request, $gateway)
    {
        if ($gateway === 'ccavenue') {
            return $this->handleCcAvenueCallback($request);
        } elseif ($gateway === 'paypal') {
            return $this->handlePayPalCallback($request);
        }

        return redirect()->route('registration.payment.lookup')
            ->with('error', 'Invalid payment gateway.');
    }

    /**
     * Handle CCAvenue callback
     */
    private function handleCcAvenueCallback(Request $request)
    {
        $encResponse = $request->input('encResp');

        if (empty($encResponse)) {
            return redirect()->route('registration.payment.lookup')
                ->with('error', 'Payment response incomplete. Please try again.');
        }

        try {
            $credentials = $this->ccAvenueService->getCredentials();
            $decryptedResponse = $this->ccAvenueService->decrypt($encResponse, $credentials['working_key']);
            parse_str($decryptedResponse, $responseArray);

            $orderId = $responseArray['order_id'] ?? null;
            $orderStatus = $responseArray['order_status'] ?? null;

            if (!$orderId) {
                return redirect()->route('registration.payment.lookup')
                    ->with('error', 'Invalid payment response.');
            }

            // Extract invoice number from order_id
            $invoiceNo = explode('_', $orderId)[0];
            $invoice = Invoice::where('invoice_no', $invoiceNo)->first();

            if (!$invoice) {
                return redirect()->route('registration.payment.lookup')
                    ->with('error', 'Invoice not found.');
            }

            // Update payment gateway response
            $transDate = isset($responseArray['trans_date'])
                ? Carbon::createFromFormat('d/m/Y H:i:s', $responseArray['trans_date'])->format('Y-m-d H:i:s')
                : now();

            DB::table('payment_gateway_response')
                ->where('order_id', $orderId)
                ->update([
                    'amount' => $responseArray['mer_amount'] ?? $invoice->total_final_price,
                    'transaction_id' => $responseArray['tracking_id'] ?? null,
                    'payment_method' => $responseArray['payment_mode'] ?? null,
                    'trans_date' => $transDate,
                    'reference_id' => $responseArray['bank_ref_no'] ?? null,
                    'response_json' => json_encode($responseArray),
                    'status' => $orderStatus === 'Success' ? 'Success' : 'Failed',
                    'updated_at' => now(),
                ]);

            if ($orderStatus === 'Success') {
                // Update invoice
                $invoice->update([
                    'payment_status' => 'paid',
                    'amount_paid' => $responseArray['mer_amount'] ?? $invoice->total_final_price,
                    'pending_amount' => 0,
                    'updated_at' => now(),
                ]);

                // Create payment record
                $application = Application::find($invoice->application_id);
                Payment::create([
                    'invoice_id' => $invoice->id,
                    'payment_method' => $responseArray['payment_mode'] ?? 'CCAvenue',
                    'amount' => $responseArray['mer_amount'] ?? $invoice->total_final_price,
                    'amount_paid' => $responseArray['mer_amount'] ?? $invoice->total_final_price,
                    'amount_received' => $responseArray['mer_amount'] ?? $invoice->total_final_price,
                    'transaction_id' => $responseArray['tracking_id'] ?? null,
                    'pg_result' => $orderStatus,
                    'track_id' => $responseArray['tracking_id'] ?? null,
                    'pg_response_json' => json_encode($responseArray),
                    'payment_date' => $transDate,
                    'currency' => $invoice->currency ?? 'INR',
                    'status' => 'successful',
                    'order_id' => $orderId,
                    'user_id' => $application ? $application->user_id : null,
                ]);

                // Store invoice_no in session for success page
                session(['invoice_no' => $invoice->invoice_no]);
                
                // Clear other payment session data
                session()->forget(['payment_tin', 'payment_email', 'payment_invoice_id', 'payment_application_id', 'payment_order_id', 'payment_paypal_order_id']);

                return redirect()->route('registration.payment.success')
                    ->with('success', 'Payment successful!');
            } else {
                return redirect()->route('registration.payment.select', $invoice->invoice_no)
                    ->with('error', 'Payment failed. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('CCAvenue Callback Error', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);
            return redirect()->route('registration.payment.lookup')
                ->with('error', 'An error occurred while processing payment. Please contact support.');
        }
    }

    /**
     * Handle PayPal callback
     */
    private function handlePayPalCallback(Request $request)
    {
        $paypalOrderId = $request->input('token') ?? session('payment_paypal_order_id');

        if (!$paypalOrderId) {
            return redirect()->route('registration.payment.lookup')
                ->with('error', 'Payment response incomplete.');
        }

        try {
            // Capture the order
            $captureResponse = $this->paypalClient->getOrdersController()->captureOrder($paypalOrderId);
            $captureResult = $captureResponse->getResult();

            $status = $captureResult->getStatus();
            $orderId = session('payment_order_id');
            $invoiceNo = session('payment_invoice_no');

            if (!$invoiceNo) {
                return redirect()->route('registration.payment.lookup')
                    ->with('error', 'Session expired. Please lookup your order again.');
            }

            $invoice = Invoice::where('invoice_no', $invoiceNo)->firstOrFail();

            // Update payment gateway response
            DB::table('payment_gateway_response')
                ->where('payment_id', $paypalOrderId)
                ->update([
                    'status' => $status === 'COMPLETED' ? 'Success' : 'Failed',
                    'response_json' => json_encode($captureResult),
                    'updated_at' => now(),
                ]);

            if ($status === 'COMPLETED') {
                // Get amount from capture
                $amount = 0;
                if ($captureResult->getPurchaseUnits() && count($captureResult->getPurchaseUnits()) > 0) {
                    $purchaseUnit = $captureResult->getPurchaseUnits()[0];
                    if ($purchaseUnit->getPayments() && $purchaseUnit->getPayments()->getCaptures()) {
                        $capture = $purchaseUnit->getPayments()->getCaptures()[0];
                        $amount = $capture->getAmount()->getValue();
                    }
                }

                // Update invoice
                $invoice->update([
                    'payment_status' => 'paid',
                    'amount_paid' => $amount,
                    'pending_amount' => 0,
                    'updated_at' => now(),
                ]);

                // Create payment record
                $application = Application::find($invoice->application_id);
                Payment::create([
                    'invoice_id' => $invoice->id,
                    'payment_method' => 'PayPal',
                    'amount' => $amount,
                    'amount_paid' => $amount,
                    'amount_received' => $amount,
                    'transaction_id' => $paypalOrderId,
                    'pg_result' => $status,
                    'track_id' => $paypalOrderId,
                    'pg_response_json' => json_encode($captureResult),
                    'payment_date' => now(),
                    'currency' => $invoice->currency ?? 'USD',
                    'status' => 'successful',
                    'order_id' => $orderId,
                    'user_id' => $application ? $application->user_id : null,
                ]);

                // Store invoice_no in session for success page
                session(['invoice_no' => $invoice->invoice_no]);
                
                // Clear other payment session data
                session()->forget(['payment_tin', 'payment_email', 'payment_invoice_id', 'payment_application_id', 'payment_order_id', 'payment_paypal_order_id']);

                return redirect()->route('registration.payment.success')
                    ->with('success', 'Payment successful!');
            } else {
                return redirect()->route('registration.payment.select', $invoice->invoice_no)
                    ->with('error', 'Payment was not completed. Please try again.');
            }
        } catch (\Exception $e) {
            Log::error('PayPal Callback Error', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);
            return redirect()->route('registration.payment.lookup')
                ->with('error', 'An error occurred while processing payment. Please contact support.');
        }
    }

    /**
     * Show payment success page
     */
    public function showSuccess(Request $request)
    {
        $invoiceNo = $request->session()->get('invoice_no') ?? $request->get('invoice_no');
        
        if (!$invoiceNo) {
            return redirect()->route('registration.payment.lookup')
                ->with('info', 'Please lookup your order to view payment status.');
        }

        $invoice = Invoice::where('invoice_no', $invoiceNo)->first();
        
        if (!$invoice) {
            return redirect()->route('registration.payment.lookup')
                ->with('error', 'Invoice not found.');
        }

        return view('payment.registration-success', compact('invoice'));
    }

    /**
     * Get billing details for invoice
     */
    private function getBillingDetails($invoice, $application)
    {
        // Try to get from BillingDetail
        $billingDetail = BillingDetail::where('application_id', $invoice->application_id)->first();

        if ($billingDetail) {
            return $billingDetail;
        }

        // Try to get from EventContact (for startup zone)
        if ($application) {
            $eventContact = \App\Models\EventContact::where('application_id', $application->id)->first();
            if ($eventContact) {
                $contactName = trim(($eventContact->salutation ?? '') . ' ' . ($eventContact->first_name ?? '') . ' ' . ($eventContact->last_name ?? ''));
                $phone = preg_replace('/^.*-/', '', $eventContact->contact_number ?? $application->landline ?? '');

                $cityName = '';
                if ($application->city_id) {
                    $city = DB::table('cities')->where('id', $application->city_id)->first();
                    $cityName = $city->name ?? '';
                }

                $stateName = '';
                if ($application->state_id) {
                    $state = \App\Models\State::find($application->state_id);
                    $stateName = $state->name ?? '';
                }

                $countryName = '';
                if ($application->country_id) {
                    $country = \App\Models\Country::find($application->country_id);
                    $countryName = $country->name ?? '';
                }

                return (object) [
                    'contact_name' => $contactName,
                    'email' => $eventContact->email ?? $application->company_email ?? '',
                    'phone' => $phone,
                    'address' => $application->address ?? '',
                    'postal_code' => $application->postal_code ?? '',
                    'city_name' => $cityName,
                    'state' => (object) ['name' => $stateName],
                    'country' => (object) ['name' => $countryName],
                ];
            }
        }

        // Return empty object if nothing found
        return (object) [
            'contact_name' => '',
            'email' => '',
            'phone' => '',
            'address' => '',
            'postal_code' => '',
            'city_name' => '',
            'state' => (object) ['name' => ''],
            'country' => (object) ['name' => ''],
        ];
    }

    /**
     * Show ticket lookup form
     * If tin or tin_no is provided in query string, automatically fetch and display order details
     */
    public function showTicketLookup($eventSlug, Request $request)
    {
        $event = Events::where('slug', $eventSlug)->orWhere('id', $eventSlug)->firstOrFail();
        
        // Get TIN from query parameter (tin or tin_no) - query() is most reliable for query string params
        $tin = $request->query('tin') ?? $request->query('tin_no');
        
        // Also check session and all() as fallback
        if (!$tin) {
            $tin = $request->input('tin') ?? $request->input('tin_no') ?? session('tin');
        }
        
        // Debug logging
        \Log::info('Ticket Lookup Request', [
            'tin' => $tin,
            'eventSlug' => $eventSlug,
            'all_query' => $request->query(),
            'all_input' => $request->all()
        ]);
        
        // If TIN is provided, automatically fetch and display order details
        if ($tin) {
            $tinNo = trim($tin);
            
            // Find ticket order by order_no (TIN) - first try with event constraint
            $order = TicketOrder::where('order_no', $tinNo)
                ->whereHas('registration', function($q) use ($event) {
                    $q->where('event_id', $event->id);
                })
                ->with(['registration.contact', 'registration.event', 'registration.delegates', 'items.ticketType', 'items.selectedDay', 'registration.registrationCategory'])
                ->first();
            
            // If not found with event constraint, try without (in case event doesn't match)
            if (!$order) {
                $order = TicketOrder::where('order_no', $tinNo)
                    ->with(['registration.contact', 'registration.event', 'registration.delegates', 'items.ticketType', 'items.selectedDay', 'registration.registrationCategory'])
                    ->first();
                
                // If order found but event doesn't match, still show it but with a warning
                if ($order && $order->registration && $order->registration->event_id != $event->id) {
                    \Log::warning('Ticket Lookup - Order found but event mismatch', [
                        'order_event_id' => $order->registration->event_id,
                        'requested_event_id' => $event->id,
                        'tin' => $tinNo
                    ]);
                }
            }
            
            if ($order) {
                // Get email from registration contact
                $email = $order->registration->contact->email ?? $order->registration->company_email ?? 'N/A';
                
                // Display order details directly
                return view('payment.ticket-order-details', compact('event', 'order', 'email'));
            } else {
                // Order not found, show lookup form with error and pre-fill TIN
                \Log::warning('Ticket Lookup - Order not found', ['tin' => $tinNo, 'eventSlug' => $eventSlug]);
                return view('payment.ticket-lookup', compact('event', 'tin'))
                    ->with('error', 'No ticket order found with the provided Order Number: ' . $tinNo);
            }
        }
        
        // No TIN provided, show lookup form
        return view('payment.ticket-lookup', compact('event', 'tin'));
    }

    /**
     * Handle ticket order lookup by TIN (order number)
     */
    public function lookupTicketOrder(Request $request, $eventSlug)
    {
        $event = Events::where('slug', $eventSlug)->orWhere('id', $eventSlug)->firstOrFail();
        
        $request->validate([
            'tin_no' => 'required|string',
        ], [
            'tin_no.required' => 'Please enter your Order Number (TIN).',
        ]);

        $tinNo = trim($request->tin_no);

        // Find ticket order by order_no (TIN)
        $order = TicketOrder::where('order_no', $tinNo)
            ->whereHas('registration', function($q) use ($event) {
                $q->where('event_id', $event->id);
            })
            ->with(['registration.contact', 'registration.event', 'items.ticketType', 'registration.registrationCategory'])
            ->first();

        if (!$order) {
            return back()
                ->withInput()
                ->with('error', 'No ticket order found with the provided Order Number.');
        }

        // Get email from registration contact
        $email = $order->registration->contact->email ?? $order->registration->company_email ?? 'N/A';

        return view('payment.ticket-order-details', compact('event', 'order', 'email'));
    }

    /**
     * Process ticket payment - Auto-select gateway based on country
     * URL: tickets/{eventSlug}/payment/{orderNo}
     */
    public function processTicketPayment($eventSlug, $orderNo)
    {
        try {
            // Prevent "initiate" from being treated as order number
            if ($orderNo === 'initiate') {
                return redirect()->route('tickets.register', $eventSlug)
                    ->with('error', 'Invalid payment request. Please complete registration first.');
            }

            $event = Events::where('slug', $eventSlug)->orWhere('id', $eventSlug)->firstOrFail();
            
            // Find ticket order by order_no
            $order = TicketOrder::where('order_no', $orderNo)
                ->whereHas('registration', function($q) use ($event) {
                    $q->where('event_id', $event->id);
                })
                ->with(['registration.contact', 'registration.event', 'items.ticketType'])
                ->firstOrFail();

            // Ensure secure_token exists
            if (empty($order->secure_token)) {
                $order->secure_token = bin2hex(random_bytes(32));
                $order->save();
            }

            // Check if already paid
            if ($order->status === 'paid') {
                return redirect()->route('tickets.confirmation', [
                    'eventSlug' => $event->slug ?? $event->id,
                    'token' => $order->secure_token ?? $order->id
                ])->with('info', 'Payment already completed.');
            }

            // Create invoice only when user proceeds to payment (avoid premature entries)
            $invoice = Invoice::where('invoice_no', $order->order_no)
                ->where('type', 'ticket_registration')
                ->first();

            if (!$invoice) {
                // Determine currency from nationality, not event
                $isInternational = ($order->registration->nationality === 'International' || 
                                   $order->registration->nationality === 'international');
                $currency = $isInternational ? 'USD' : 'INR';
                
                $orderTotal = $order->total ?? 0;
                $invoice = Invoice::create([
                    'invoice_no'         => $order->order_no,
                    'type'               => 'ticket_registration',
                    'registration_id'    => $order->registration_id, // link to ticket registration for traceability
                    'currency'           => $currency, // Use nationality-based currency, not event currency
                    'amount'             => $orderTotal, // base amount required by DB
                    'price'              => $orderTotal,
                    'gst'                => $order->gst_total ?? 0,
                    'processing_charges' => $order->processing_charge_total ?? 0,
                    'total_final_price'  => $orderTotal,
                    'amount_paid'        => 0,
                    'pending_amount'     => $orderTotal,
                    'payment_status'     => 'unpaid',
                ]);
            }

            // Determine payment gateway based on nationality (not company_country)
            $registration = $order->registration;
            $isInternational = ($registration->nationality === 'International' || 
                               $registration->nationality === 'international');
            $isIndian = !$isInternational; // If not international, then Indian
            
            // Determine currency and gateway based on nationality
            $currency = $isInternational ? 'USD' : 'INR';
            $paymentGateway = $isInternational ? 'PayPal' : 'CCAvenue';
            
            // IMPORTANT: Enforce currency-gateway matching
            if ($currency === 'USD' && $paymentGateway !== 'PayPal') {
                $paymentGateway = 'PayPal'; // Force PayPal for USD
            }
            if ($currency === 'INR' && $paymentGateway !== 'CCAvenue') {
                $paymentGateway = 'CCAvenue'; // Force CCAvenue for INR
            }
            
            // Get billing details
            $billingName = $registration->contact->name ?? '';
            $billingEmail = $registration->contact->email ?? '';
            
            // Get phone number - prefer company_phone as contact->phone might be malformed
            // Check if contact->phone looks valid (at least 10 digits when cleaned)
            $contactPhone = $registration->contact->phone ?? '';
            $companyPhone = $registration->company_phone ?? '';
            
            // Clean both to check which one is valid
            $contactPhoneDigits = preg_replace('/[^0-9]/', '', $contactPhone);
            $companyPhoneDigits = preg_replace('/[^0-9]/', '', $companyPhone);
            
            // Use company_phone if contact_phone has less than 10 digits
            if (strlen($contactPhoneDigits) >= 10) {
                $billingPhone = $contactPhone;
            } elseif (strlen($companyPhoneDigits) >= 10) {
                $billingPhone = $companyPhone;
            } else {
                $billingPhone = $contactPhone ?: $companyPhone; // Fallback to whatever is available
            }
            
            Log::info('Ticket Payment - Phone source selection', [
                'contact_phone' => $contactPhone,
                'contact_phone_digits' => strlen($contactPhoneDigits),
                'company_phone' => $companyPhone,
                'company_phone_digits' => strlen($companyPhoneDigits),
                'selected_phone' => $billingPhone,
            ]);

            // Prepare payment data
            $orderIdWithTimestamp = $order->order_no . '_' . time();
            $amount = $order->total;
            
            // Log gateway selection for debugging (after $amount is defined)
            Log::info('Ticket Payment Gateway Selection', [
                'order_no' => $order->order_no,
                'nationality' => $registration->nationality,
                'is_international' => $isInternational,
                'currency' => $currency,
                'payment_gateway' => $paymentGateway,
                'amount' => $amount,
            ]);
            
            // IMPORTANT: Amount is already in the correct currency (USD for international, INR for national)
            // Do NOT convert - the order total is already stored in the correct currency
            // For international: amount is already in USD
            // For national: amount is already in INR

            // Create or reuse a pending payment entry (like PaymentGatewayController style validation)
            $existingPayment = Payment::where('invoice_id', $invoice->id)
                ->where('status', 'pending')
                ->latest()
                ->first();

            if (!$existingPayment) {
                $existingPayment = Payment::create([
                    'invoice_id'      => $invoice->id,
                    'payment_method'  => $paymentGateway,
                    'amount'          => $amount,
                    'amount_paid'     => 0,
                    'amount_received' => 0,
                    'transaction_id'  => $orderIdWithTimestamp, // Set transaction_id to order_id for pending payments
                    'status'          => 'pending',
                    'order_id'        => $orderIdWithTimestamp,
                    'currency'        => $currency,
                    'payment_date'    => now(), // Set payment_date for pending payments
                ]);
            } else {
                // reuse existing order_id to keep gateway correlation consistent
                $orderIdWithTimestamp = $existingPayment->order_id ?? $orderIdWithTimestamp;
            }

            // Store in session
            session([
                'ticket_order_id' => $order->id,
                'ticket_order_no' => $order->order_no,
                'ticket_payment_gateway' => $paymentGateway,
                'ticket_payment_order_id' => $orderIdWithTimestamp,
            ]);

            if ($paymentGateway === 'CCAvenue') {
                return $this->processTicketCcAvenue($order, $orderIdWithTimestamp, $amount, $currency, $billingName, $billingEmail, $billingPhone, $registration, $invoice);
            } else {
                return $this->processTicketPayPal($order, $orderIdWithTimestamp, $amount, $currency, $billingName, $billingEmail, $billingPhone, $registration, $invoice);
            }

        } catch (\Exception $e) {
            Log::error('Ticket Payment Process Error', [
                'error' => $e->getMessage(),
                'event_slug' => $eventSlug,
                'order_no' => $orderNo,
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'An error occurred while processing payment. Please try again.');
        }
    }

    /**
     * Process ticket payment via CCAvenue
     */
    private function processTicketCcAvenue($order, $orderId, $amount, $currency, $billingName, $billingEmail, $billingPhone, $registration, $invoice)
    {
        try {
            $event = $order->registration->event;
            $eventSlug = $event->slug ?? $event->id;
            
            // ============================================================
            // PRE-PAYMENT VALIDATION - Check all fields before sending to CCAvenue
            // ============================================================
            $validationErrors = [];
            
            // 1. Validate Amount
            if (empty($amount) || !is_numeric($amount) || $amount <= 0) {
                $validationErrors[] = 'Invalid payment amount: ' . ($amount ?? 'null');
            }
            
            // 2. Validate Order ID format
            if (empty($orderId)) {
                $validationErrors[] = 'Order ID is missing';
            }
            
            // 3. Validate Currency
            if (empty($currency) || !in_array(strtoupper($currency), ['INR', 'USD'])) {
                $validationErrors[] = 'Invalid currency: ' . ($currency ?? 'null');
            }
            
            // 4. Validate Billing Email (Required by CCAvenue)
            if (empty($billingEmail) || !filter_var($billingEmail, FILTER_VALIDATE_EMAIL)) {
                $validationErrors[] = 'Invalid or missing billing email: ' . ($billingEmail ?? 'null');
            }

            // 5. Validate Billing Name
            if (empty($billingName)) {
                $billingName = $registration->company_name ?? 'Customer';
                Log::warning('Ticket CCAvenue Payment - Missing billing name, using fallback', [
                    'fallback_name' => $billingName,
                ]);
            }
            
            // 6. Validate and Clean Billing Phone
            // CCAvenue expects phone number without country code
            $originalBillingPhone = $billingPhone;
            
            Log::info('Ticket CCAvenue Payment - Phone BEFORE extraction', [
                'billingPhone_variable' => $billingPhone,
                'contact_phone' => $registration->contact->phone ?? 'NULL',
                'company_phone' => $registration->company_phone ?? 'NULL',
            ]);
            
            if (!empty($billingPhone)) {
                $billingPhone = $this->extractPhoneNumber($billingPhone);
            } else {
                // Try to get phone from registration
                $billingPhone = $this->extractPhoneNumber($registration->company_phone ?? '');
            }
            
            Log::info('Ticket CCAvenue Payment - Phone AFTER extraction', [
                'original_phone' => $originalBillingPhone,
                'extracted_phone' => $billingPhone,
                'extracted_length' => strlen($billingPhone),
            ]);
            
            if (empty($billingPhone) || strlen($billingPhone) < 10) {
                Log::warning('Ticket CCAvenue Payment - Phone number may be invalid after extraction', [
                    'original' => $originalBillingPhone,
                    'company_phone' => $registration->company_phone ?? 'NULL',
                    'cleaned' => $billingPhone,
                    'length' => strlen($billingPhone),
                ]);
            }
            
            // 7. Check CCAvenue credentials
            $credentials = $this->ccAvenueService->getCredentials();
            if (empty($credentials['merchant_id']) || empty($credentials['access_code']) || empty($credentials['working_key'])) {
                $validationErrors[] = 'CCAvenue credentials are not properly configured';
                Log::error('Ticket CCAvenue Payment - Missing credentials', [
                    'has_merchant_id' => !empty($credentials['merchant_id']),
                    'has_access_code' => !empty($credentials['access_code']),
                    'has_working_key' => !empty($credentials['working_key']),
                ]);
            }
            
            // 8. Validate Event
            if (!$event || empty($eventSlug)) {
                $validationErrors[] = 'Event information is missing';
            }
            
            // If there are validation errors, log them and return with error message
            if (!empty($validationErrors)) {
                Log::error('Ticket CCAvenue Payment - Pre-validation failed', [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'errors' => $validationErrors,
                    'billing_email' => $billingEmail,
                    'billing_phone' => $billingPhone,
                    'amount' => $amount,
                    'currency' => $currency,
                ]);
                
                $errorMessage = 'Payment cannot be processed due to validation errors: ' . implode(', ', $validationErrors);
                
                // Redirect to lookup page with TIN
                $lookupUrl = route('tickets.payment.lookup', ['eventSlug' => $eventSlug]) . '?tin=' . urlencode($order->order_no);
                return redirect($lookupUrl)->with('error', $errorMessage)->with('tin', $order->order_no);
            }
            
            // ============================================================
            // ALL VALIDATIONS PASSED - Proceed with payment
            // ============================================================
            
            // Both redirect and cancel URL should point to the same callback route
            // CCAvenue requires these URLs to be registered in the merchant panel
            $callbackUrl = route('registration.ticket.payment.callback', ['eventSlug' => $eventSlug, 'gateway' => 'ccavenue']);
            
            // Sanitize and prepare payment data
            // Note: billing_tel is already cleaned by extractPhoneNumber(), no need to sanitize again
            $paymentData = [
                'order_id' => $orderId,
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => 'INR',
                'redirect_url' => $callbackUrl,
                'cancel_url' => $callbackUrl,
                'billing_name' => $this->sanitizeForCcAvenue($billingName, 50),
                'billing_address' => $this->sanitizeForCcAvenue($registration->company_name ?? '', 100),
                'billing_city' => $this->sanitizeForCcAvenue($registration->company_city ?? '', 50),
                'billing_state' => $this->sanitizeForCcAvenue($registration->company_state ?? '', 50),
                'billing_zip' => $this->sanitizeForCcAvenue($registration->postal_code ?? '', 10),
                'billing_country' => $this->sanitizeForCcAvenue($registration->company_country ?? 'India', 50),
                'billing_tel' => $billingPhone, // Already cleaned, just use directly
                'billing_email' => $billingEmail,
            ];

            // ============================================================
            // DETAILED PARAMETER LOGGING FOR DEBUGGING
            // ============================================================
            $parameterAnalysis = [];
            
            // Check each parameter for potential issues
            foreach ($paymentData as $key => $value) {
                $analysis = [
                    'value' => $value,
                    'length' => strlen($value ?? ''),
                    'is_empty' => empty($value),
                ];
                
                // Check for special characters that CCAvenue might reject
                if (is_string($value) && !empty($value)) {
                    $specialChars = preg_match('/[&<>"\'\\\\\/=+;:@#$%^*()[\]{}|`~]/', $value);
                    $analysis['has_special_chars'] = $specialChars ? true : false;
                    
                    // Check for non-ASCII characters
                    $nonAscii = preg_match('/[^\x20-\x7E]/', $value);
                    $analysis['has_non_ascii'] = $nonAscii ? true : false;
                    
                    // Check for leading/trailing spaces
                    $analysis['has_leading_trailing_spaces'] = ($value !== trim($value));
                }
                
                $parameterAnalysis[$key] = $analysis;
            }
            
            Log::info('Ticket CCAvenue Payment - DETAILED PARAMETER ANALYSIS', [
                'order_no' => $order->order_no,
                'parameter_analysis' => $parameterAnalysis,
            ]);
            
            // Log the full payment data
            Log::info('Ticket CCAvenue Payment - Full Payment Data', [
                'payment_data' => $paymentData,
            ]);
            
            // Log potential issues summary
            $potentialIssues = [];
            
            // Check redirect URL length and format
            if (strlen($paymentData['redirect_url']) > 255) {
                $potentialIssues[] = 'redirect_url is too long (' . strlen($paymentData['redirect_url']) . ' chars)';
            }
            if (!filter_var($paymentData['redirect_url'], FILTER_VALIDATE_URL)) {
                $potentialIssues[] = 'redirect_url is not a valid URL';
            }
            
            // Check if redirect URL is HTTPS
            if (strpos($paymentData['redirect_url'], 'https://') !== 0) {
                $potentialIssues[] = 'redirect_url is not HTTPS (CCAvenue may require HTTPS)';
            }
            
            // Check amount format
            if (!preg_match('/^\d+\.\d{2}$/', $paymentData['amount'])) {
                $potentialIssues[] = 'amount format may be invalid: ' . $paymentData['amount'];
            }
            
            // Check billing_tel
            if (!empty($paymentData['billing_tel']) && !preg_match('/^\d{10,15}$/', $paymentData['billing_tel'])) {
                $potentialIssues[] = 'billing_tel format may be invalid: ' . $paymentData['billing_tel'];
            }
            
            // Check billing_email
            if (!filter_var($paymentData['billing_email'], FILTER_VALIDATE_EMAIL)) {
                $potentialIssues[] = 'billing_email is invalid: ' . $paymentData['billing_email'];
            }
            
            // Check billing_zip
            if (!empty($paymentData['billing_zip']) && !preg_match('/^[a-zA-Z0-9\s-]{3,10}$/', $paymentData['billing_zip'])) {
                $potentialIssues[] = 'billing_zip format may be invalid: ' . $paymentData['billing_zip'];
            }
            
            // Check for empty required fields
            $requiredFields = ['order_id', 'amount', 'currency', 'redirect_url', 'billing_email'];
            foreach ($requiredFields as $field) {
                if (empty($paymentData[$field])) {
                    $potentialIssues[] = "Required field '$field' is empty";
                }
            }
            
            if (!empty($potentialIssues)) {
                Log::warning('Ticket CCAvenue Payment - POTENTIAL ISSUES DETECTED', [
                    'order_no' => $order->order_no,
                    'issues' => $potentialIssues,
                ]);
            }

            Log::info('Ticket CCAvenue Payment - Initiating transaction', [
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'amount' => $amount,
                'currency' => $currency,
                'callback_url' => $callbackUrl,
                'potential_issues_count' => count($potentialIssues),
            ]);

            $result = $this->ccAvenueService->initiateTransaction($paymentData);

            if ($result['success']) {
                // Store payment gateway response
                DB::table('payment_gateway_response')->insert([
                    'merchant_data' => json_encode($paymentData),
                    'order_id' => $orderId,
                    'amount' => $amount,
                    'status' => 'Pending',
                    'gateway' => 'CCAvenue',
                    'currency' => $currency,
                    'email' => $billingEmail,
                    'created_at' => now(),
                ]);

                Log::info('Ticket CCAvenue Payment - Success, showing payment form', [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                ]);

                // Return view with form that auto-submits to CCAvenue (same as PaymentGatewayController)
                return view('pgway.ccavenue', [
                    'encryptedData' => $result['encrypted_data'],
                    'access_code' => $result['access_code']
                ]);
            } else {
                $errorMessage = $result['error'] ?? $result['message'] ?? 'Unknown error';
                Log::error('Ticket CCAvenue Payment - Gateway initiation failed', [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'error' => $errorMessage,
                    'result' => $result,
                ]);
                return redirect()->back()->with('error', 'Failed to initiate payment: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            Log::error('Ticket CCAvenue Payment Error', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
                'order_no' => $order->order_no ?? null,
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'An error occurred while initiating payment: ' . $e->getMessage());
        }
    }

    /**
     * Process ticket payment via PayPal
     */
    private function processTicketPayPal($order, $orderId, $amount, $currency, $billingName, $billingEmail, $billingPhone, $registration, $invoice)
    {
        // Debug logging at start of ticket PayPal processing
        Log::info('Ticket PayPal Payment Processing Started', [
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'orderId_param' => $orderId,
            'amount' => $amount,
            'currency' => $currency,
            'billing_name' => $billingName,
            'billing_email' => $billingEmail,
            'billing_phone' => $billingPhone,
            'registration_id' => $registration->id,
            'invoice_id' => $invoice->id ?? null,
            'paypal_mode' => config('constants.PAYPAL_MODE'),
            'client_initialized' => $this->paypalClient ? 'yes' : 'no'
        ]);

        try {
            $event = $order->registration->event;
            $eventSlug = $event->slug ?? $event->id;
            
            // Build purchase unit - matching PayPalController pattern
            $purchaseUnit = PurchaseUnitRequestBuilder::init(
                AmountWithBreakdownBuilder::init($currency, $amount)->build()
            )
                ->description('Ticket Registration for ' . ($order->registration->company_name ?? 'Event'))
                ->invoiceId($orderId)  // PayPal invoice tracking
                ->build();
            
            // Build application context with return/cancel URLs for redirect after payment
            $returnUrl = route('registration.ticket.payment.callback', [
                'eventSlug' => $eventSlug,
                'gateway' => 'paypal'
            ]);
            $cancelUrl = route('tickets.payment.lookup', [
                'eventSlug' => $eventSlug
            ]) . '?tin=' . urlencode($order->order_no);
            
            Log::info('Ticket PayPal - Creating order with return URLs', [
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl,
                'paypal_order_id_placeholder' => $orderId
            ]);
            
            // Build order body with application context for return URLs
            $orderRequest = OrderRequestBuilder::init(
                CheckoutPaymentIntent::CAPTURE,
                [$purchaseUnit]
            )
                ->applicationContext(
                    OrderApplicationContextBuilder::init()
                        ->returnUrl($returnUrl)
                        ->cancelUrl($cancelUrl)
                        ->build()
                )
                ->build();

            $orderBody = [
                'body' => $orderRequest
            ];

            // Debug logging before PayPal API call
            Log::info('PayPal Order Creation Debug', [
                'order_id' => $orderId,
                'order_no' => $order->order_no,
                'amount' => $amount,
                'currency' => $currency,
                'paypal_mode' => config('constants.PAYPAL_MODE'),
                'paypal_client_id_set' => !empty(config('constants.PAYPAL_SANDBOX_CLIENT_ID')) || !empty(config('constants.PAYPAL_LIVE_CLIENT_ID')),
                'order_request_structure' => json_encode($orderRequest, JSON_PRETTY_PRINT),
                'order_body_keys' => array_keys($orderBody),
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl,
                'client_initialized' => $this->paypalClient ? 'yes' : 'no'
            ]);

            try {
                $apiResponse = $this->paypalClient->getOrdersController()->createOrder($orderBody);
                $paypalOrderId = $apiResponse->getResult()->getId();

                Log::info('PayPal Order Creation Success', [
                    'order_id' => $orderId,
                    'paypal_order_id' => $paypalOrderId,
                    'api_response_status' => 'success'
                ]);
            } catch (\PaypalServerSdkLib\Exceptions\ApiException $e) {
                Log::error('PayPal Order Creation API Exception', [
                    'order_id' => $orderId,
                    'order_no' => $order->order_no,
                    'error_message' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'paypal_mode' => config('constants.PAYPAL_MODE'),
                    'request_body_preview' => json_encode($orderBody, JSON_PRETTY_PRINT)
                ]);
                throw $e;
            } catch (\Exception $e) {
                Log::error('PayPal Order Creation Unexpected Error', [
                    'order_id' => $orderId,
                    'order_no' => $order->order_no,
                    'error_message' => $e->getMessage(),
                    'error_trace' => $e->getTraceAsString(),
                    'paypal_mode' => config('constants.PAYPAL_MODE')
                ]);
                throw $e;
            }
            $paypalOrderId = $apiResponse->getResult()->getId();

            // Store payment gateway response
            DB::table('payment_gateway_response')->insert([
                'merchant_data' => json_encode([
                    'order_id' => $orderId,
                    'paypal_order_id' => $paypalOrderId,
                    'amount' => $amount,
                    'currency' => $currency,
                ]),
                'order_id' => $orderId,
                'payment_id' => $paypalOrderId,
                'amount' => $amount,
                'status' => 'Pending',
                'gateway' => 'PayPal',
                'currency' => $currency,
                'email' => $billingEmail,
                'created_at' => now(),
            ]);

            // Update session
            session(['ticket_paypal_order_id' => $paypalOrderId]);

            // Get approval URL
            $approvalUrl = null;
            foreach ($apiResponse->getResult()->getLinks() as $link) {
                if ($link->getRel() === 'approve') {
                    $approvalUrl = $link->getHref();
                    break;
                }
            }

            if ($approvalUrl) {
                return redirect($approvalUrl);
            } else {
                return redirect()->back()->with('error', 'Failed to get PayPal approval URL.');
            }
        } catch (\Exception $e) {
            Log::error('Ticket PayPal Payment Error', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
            ]);
            return redirect()->back()->with('error', 'An error occurred while initiating PayPal payment.');
        }
    }

    /**
     * Handle ticket payment callback
     */
    public function handleTicketPaymentCallback(Request $request, $eventSlug, $gateway)
    {
        Log::info('Ticket Payment Callback', ['request' => $request->all(), 'eventSlug' => $eventSlug, 'gateway' => $gateway]);
        $event = Events::where('slug', $eventSlug)->orWhere('id', $eventSlug)->firstOrFail();

        if ($gateway === 'ccavenue') {
            $encResponse = $request->input('encResp');
            
            // Try to get order number from request parameter first (fallback for failed decryption)
            $orderNoFromRequest = $request->input('orderNo');
            if ($orderNoFromRequest) {
                // Extract TIN from orderNo (format: TIN-BTS-2026-TKT-666662_1768457434)
                $orderNoFromRequest = explode('_', $orderNoFromRequest)[0];
            }
            
            $responseArray = [];
            $orderNo = null;
            
            if (!empty($encResponse) && strlen($encResponse) > 10) {
                // Only attempt decryption if encResp looks valid (not just "a" or similar garbage)
                try {
            $credentials = $this->ccAvenueService->getCredentials();
            $decryptedResponse = $this->ccAvenueService->decrypt($encResponse, $credentials['working_key']);
            parse_str($decryptedResponse, $responseArray);

            $orderIdFromGateway = $responseArray['order_id'] ?? null;
            $orderNo = $orderIdFromGateway ? explode('_', $orderIdFromGateway)[0] : null;
                    
                    Log::info('Ticket CCAvenue Callback - Decrypted response', [
                        'order_id_from_gateway' => $orderIdFromGateway,
                        'order_no' => $orderNo,
                        'order_status' => $responseArray['order_status'] ?? null,
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Ticket CCAvenue Callback - Decryption failed', [
                        'error' => $e->getMessage(),
                        'encResp_length' => strlen($encResponse),
                    ]);
                }
            } else {
                Log::warning('Ticket CCAvenue Callback - Invalid/empty encResp', [
                    'encResp' => $encResponse,
                    'orderNo_from_request' => $orderNoFromRequest,
                ]);
            }
            
            // Fallback to order number from request if decryption failed
            if (!$orderNo && $orderNoFromRequest) {
                $orderNo = $orderNoFromRequest;
                Log::info('Ticket CCAvenue Callback - Using orderNo from request parameter', [
                    'order_no' => $orderNo,
                ]);
            }

            if (!$orderNo) {
                Log::error('Ticket CCAvenue Callback - Could not determine order number', [
                    'encResp' => $encResponse,
                    'orderNo_from_request' => $orderNoFromRequest,
                ]);
                return redirect()->route('tickets.payment.lookup', $eventSlug)
                    ->with('error', 'Order not found for payment callback. Please use your TIN to check payment status.');
            }

            $order = TicketOrder::with(['registration.contact', 'registration.event', 'items.ticketType'])
                ->where('order_no', $orderNo)
                ->whereHas('registration', function($q) use ($event) {
                    $q->where('event_id', $event->id);
                })
                ->first();
                
            if (!$order) {
                Log::error('Ticket CCAvenue Callback - Order not found in database', [
                    'order_no' => $orderNo,
                    'event_id' => $event->id,
                ]);
                return redirect()->route('tickets.payment.lookup', $eventSlug)
                    ->with('error', 'Order not found. Please use your TIN to check payment status.')
                    ->with('tin', $orderNo);
            }

            return $this->handleTicketCcAvenueCallback($request, $order, $event, $responseArray);
        } elseif ($gateway === 'paypal') {
            // PayPal redirects back with 'token' parameter which is the PayPal order ID
            $paypalOrderId = $request->input('token') ?? $request->input('PayerID');
            
            Log::info('Ticket PayPal Callback - Received', [
                'event_slug' => $eventSlug,
                'gateway' => $gateway,
                'token' => $request->input('token'),
                'payer_id' => $request->input('PayerID'),
                'all_params' => $request->all()
            ]);
            
            if (!$paypalOrderId) {
                Log::error('Ticket PayPal Callback - No token/PayerID received', [
                    'request_all' => $request->all()
                ]);
                return redirect()->route('tickets.payment.lookup', $eventSlug)
                    ->with('error', 'Payment response incomplete. No token received.');
            }
            
            $pgRow = DB::table('payment_gateway_response')->where('payment_id', $paypalOrderId)->first();
            
            if (!$pgRow) {
                Log::error('Ticket PayPal Callback - Payment gateway response not found', [
                    'paypal_order_id' => $paypalOrderId
                ]);
                return redirect()->route('tickets.payment.lookup', $eventSlug)
                    ->with('error', 'Payment record not found.');
            }
            
            $orderIdFromGateway = $pgRow->order_id ?? null;
            $orderNo = $orderIdFromGateway ? explode('_', $orderIdFromGateway)[0] : null;

            if (!$orderNo) {
                Log::error('Ticket PayPal Callback - Order number not found', [
                    'paypal_order_id' => $paypalOrderId,
                    'order_id_from_gateway' => $orderIdFromGateway
                ]);
                return redirect()->route('tickets.payment.lookup', $eventSlug)
                    ->with('error', 'Order not found for payment callback.');
            }

            $order = TicketOrder::with(['registration.contact', 'registration.event', 'items.ticketType'])
                ->where('order_no', $orderNo)
                ->whereHas('registration', function($q) use ($event) {
                    $q->where('event_id', $event->id);
                })
                ->firstOrFail();

            return $this->handleTicketPayPalCallback($request, $order, $event, $paypalOrderId);
        }

        return redirect()->route('tickets.payment.lookup', $eventSlug)
            ->with('error', 'Invalid payment gateway.');
    }

    /**
     * Handle ticket CCAvenue callback
     */
    private function handleTicketCcAvenueCallback($request, $order, $event, $responseArray = null)
    {
        try {
            // If responseArray is empty or null, try to decrypt from request
            if (empty($responseArray) || !isset($responseArray['order_status'])) {
                $encResponse = $request->input('encResp');
                
                // Check if we have a valid encrypted response to decrypt
                if (!empty($encResponse) && strlen($encResponse) > 10) {
                    try {
                $credentials = $this->ccAvenueService->getCredentials();
                $decryptedResponse = $this->ccAvenueService->decrypt($encResponse, $credentials['working_key']);
                parse_str($decryptedResponse, $responseArray);
                    } catch (\Exception $e) {
                        Log::warning('Ticket CCAvenue Callback - Decryption failed in handler', [
                            'error' => $e->getMessage(),
                            'order_no' => $order->order_no,
                        ]);
                    }
                }
                
                // If still empty, this means decryption failed or encResp was invalid
                // Treat as cancelled/failed payment
                if (empty($responseArray) || !isset($responseArray['order_status'])) {
                    Log::warning('Ticket CCAvenue Callback - No valid response, treating as cancelled', [
                        'order_no' => $order->order_no,
                        'encResp' => $encResponse ?? 'null',
                    ]);
                    
                    // Redirect to lookup with TIN pre-filled so user can retry
                    // Pass tin as query parameter, not route parameter
                    $lookupUrl = route('tickets.payment.lookup', ['eventSlug' => $event->slug ?? $event->id]) . '?tin=' . urlencode($order->order_no);
                    return redirect($lookupUrl)
                        ->with('error', 'Payment was cancelled or incomplete. You can try again by clicking Pay Now.')
                        ->with('tin', $order->order_no);
                }
            }

            $orderStatus = $responseArray['order_status'] ?? null;
            $orderId = $responseArray['order_id'] ?? null;
            $transDate = isset($responseArray['trans_date'])
                ? Carbon::createFromFormat('d/m/Y H:i:s', $responseArray['trans_date'])->format('Y-m-d H:i:s')
                : now();

            $invoice = Invoice::where('invoice_no', $order->order_no)
                ->where('type', 'ticket_registration')
                ->first();

            // Update payment gateway response (wrapped in try-catch as this is not critical)
            try {
                if ($orderId) {
            DB::table('payment_gateway_response')
                ->where('order_id', $orderId)
                ->update([
                    'amount' => $responseArray['mer_amount'] ?? $order->total,
                    'transaction_id' => $responseArray['tracking_id'] ?? null,
                    'payment_method' => $responseArray['payment_mode'] ?? null,
                    'trans_date' => $transDate,
                    'reference_id' => $responseArray['bank_ref_no'] ?? null,
                    'response_json' => json_encode($responseArray),
                    'status' => $orderStatus === 'Success' ? 'Success' : 'Failed',
                    'updated_at' => now(),
                ]);
                }
            } catch (\Exception $e) {
                Log::warning('Ticket CCAvenue Callback - Failed to update payment_gateway_response', [
                    'error' => $e->getMessage(),
                    'order_id' => $orderId,
                ]);
            }

            // Determine payment status
            $isSuccess = ($orderStatus === 'Success');
            $paymentStatus = $isSuccess ? 'completed' : 'failed';
            $paymentTableStatus = $isSuccess ? 'successful' : 'failed';

            // Try to create ticket payment record (for both success and failure)
            try {
                TicketPayment::create([
                    'order_ids_json' => [$order->id],
                    'method' => strtolower($responseArray['payment_mode'] ?? 'card'),
                    'amount' => $responseArray['mer_amount'] ?? $order->total,
                    'status' => $paymentStatus,
                    'gateway_txn_id' => $responseArray['tracking_id'] ?? null,
                    'gateway_name' => 'ccavenue',
                    'paid_at' => $isSuccess ? $transDate : null,
                    'pg_request_json' => [],
                    'pg_response_json' => $responseArray,
                    'pg_webhook_json' => [],
                ]);
            } catch (\Exception $e) {
                Log::warning('Ticket CCAvenue Callback - Failed to create TicketPayment record', [
                    'error' => $e->getMessage(),
                    'order_no' => $order->order_no,
                ]);
                // Continue processing - don't fail the whole callback for this
            }

            // Always create Payment record in payments table with TIN/order_no
            // Check if payment already exists (for retry scenarios)
                $payment = null;
                if ($invoice) {
                    $payment = Payment::where('invoice_id', $invoice->id)
                    ->where('order_id', $order->order_no) // Use TIN/order_no for matching
                    ->latest()
                    ->first();
            } else {
                // For tickets without invoice, check by order_id (TIN)
                $payment = Payment::where('order_id', $order->order_no)
                    ->where(function($query) {
                        $query->whereNull('invoice_id')
                              ->orWhere('invoice_id', 0);
                    })
                        ->latest()
                        ->first();
                }

                if (!$payment) {
                // Create new payment record
                // Invoice should exist as it's created during order creation or payment initiation
                if (!$invoice) {
                    // Fallback: Try to find or create invoice
                    $invoice = Invoice::where('invoice_no', $order->order_no)
                        ->where('type', 'ticket_registration')
                        ->first();
                    
                    if (!$invoice) {
                        // Create invoice if it doesn't exist (shouldn't happen, but safety check)
                        $invoice = Invoice::create([
                            'invoice_no'         => $order->order_no,
                            'type'               => 'ticket_registration',
                            'registration_id'    => $order->registration_id,
                            'currency'           => 'INR',
                            'amount'             => $order->total,
                            'price'              => $order->subtotal,
                            'gst'                => $order->gst_total,
                            'processing_charges' => $order->processing_charge_total,
                            'total_final_price'  => $order->total,
                            'amount_paid'        => 0,
                            'pending_amount'     => $order->total,
                            'payment_status'     => 'unpaid',
                        ]);
                    }
                }
                
                Payment::create([
                    'invoice_id' => $invoice->id, // Invoice should always exist now
                        'payment_method' => $responseArray['payment_mode'] ?? 'CCAvenue',
                        'amount' => $responseArray['mer_amount'] ?? $order->total,
                    'amount_paid' => $isSuccess ? ($responseArray['mer_amount'] ?? $order->total) : 0,
                    'amount_received' => $isSuccess ? ($responseArray['mer_amount'] ?? $order->total) : 0,
                    'transaction_id' => $responseArray['tracking_id'] ?? $order->order_no,
                        'pg_result' => $orderStatus,
                        'track_id' => $responseArray['tracking_id'] ?? null,
                        'pg_response_json' => json_encode($responseArray),
                    'payment_date' => $transDate ?? now(), // Always set payment_date, never null
                        'currency' => 'INR',
                    'status' => $paymentTableStatus,
                    'order_id' => $order->order_no, // Store TIN/order_no in order_id field
                    ]);
                } else {
                // Update existing payment record
                    $payment->update([
                        'payment_method' => $responseArray['payment_mode'] ?? 'CCAvenue',
                        'amount' => $responseArray['mer_amount'] ?? $order->total,
                    'amount_paid' => $isSuccess ? ($responseArray['mer_amount'] ?? $order->total) : 0,
                    'amount_received' => $isSuccess ? ($responseArray['mer_amount'] ?? $order->total) : 0,
                    'transaction_id' => $responseArray['tracking_id'] ?? $order->order_no,
                        'pg_result' => $orderStatus,
                        'track_id' => $responseArray['tracking_id'] ?? null,
                        'pg_response_json' => json_encode($responseArray),
                    'payment_date' => $transDate ?? now(), // Always set payment_date, never null
                        'currency' => 'INR',
                    'status' => $paymentTableStatus,
                    'order_id' => $order->order_no, // Ensure TIN/order_no is stored
                    ]);
                }

            if ($isSuccess) {
                // Update order status
                $order->update(['status' => 'paid']);

                // Update invoice status/amounts - mark as paid and generate PIN
                if ($invoice) {
                    $paidAmount = $responseArray['mer_amount'] ?? $order->total;
                    
                    // Generate PIN number if not already set
                    $pinNo = $invoice->pin_no;
                    if (empty($pinNo)) {
                        $pinNo = $this->generateTicketPinNo();
                    }
                    
                    $invoice->update([
                        'amount_paid' => $paidAmount,
                        'pending_amount' => max(0, ($invoice->total_final_price ?? $paidAmount) - $paidAmount),
                        'payment_status' => 'paid',
                        'pin_no' => $pinNo, // Save PIN number
                    ]);
                    
                    Log::info('Ticket CCAvenue Payment - PIN generated', [
                        'order_no' => $order->order_no,
                        'pin_no' => $pinNo,
                    ]);
                }

                // Track payment completed
                $tracking = TicketRegistrationTracking::where('order_id', $order->id)->first();
                if ($tracking) {
                    $tracking->updateStatus('payment_completed', [
                        'final_total' => $paidAmount ?? $order->total,
                    ]);
                }

                // Send payment acknowledgement email (payment successful)
                // Note: Email is sent to user only. Admin notifications should be handled separately if needed.
                try {
                    $contactEmail = $order->registration->contact->email ?? null;
                    if ($contactEmail) {
                        // Send email to user only (removed BCC to admin - admin notifications should be separate)
                        Mail::to($contactEmail)->send(new TicketRegistrationMail($order, $event, true));
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send ticket payment acknowledgement email', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                }

                // Clear session
                session()->forget(['ticket_order_id', 'ticket_order_no', 'ticket_payment_gateway', 'ticket_payment_order_id']);

                return redirect()->route('tickets.confirmation', [
                    'eventSlug' => $event->slug ?? $event->id,
                    'token' => $order->secure_token
                ])->with('success', 'Payment successful!')
                  ->with('payment_details', [
                      'gateway' => 'CCAvenue',
                      'transaction_id' => $responseArray['tracking_id'] ?? null,
                      'amount' => $responseArray['mer_amount'] ?? $order->total,
                  ]);
            } else {
                // Payment failed - order status remains 'pending'
                // Payment records already created above with 'failed' status
                // Ensure invoice remains unpaid (wrapped in try-catch)
                try {
                    if ($invoice && $invoice->payment_status !== 'unpaid') {
                        $invoice->update([
                            'payment_status' => 'unpaid',
                        ]);
            }
        } catch (\Exception $e) {
                    Log::warning('Ticket CCAvenue Callback - Failed to update invoice', [
                'error' => $e->getMessage(),
                    ]);
                }

                // Check if payment was cancelled, aborted, or invalid
                $isCancelled = in_array($orderStatus, ['Cancelled', 'Aborted', 'cancelled', 'aborted']);
                $isInvalid = ($orderStatus === 'Invalid' || $orderStatus === 'invalid');
                
                if ($isCancelled) {
                    $message = 'Payment was cancelled. You can try again by clicking Pay Now.';
                } elseif ($isInvalid) {
                    $message = 'Payment could not be processed due to invalid transaction parameters. Please try again.';
                    
                    // ============================================================
                    // DETAILED LOGGING FOR INVALID STATUS - DEBUG CCAVENUE ISSUES
                    // ============================================================
                    Log::error('CCAvenue INVALID Status - FULL DEBUG INFO', [
                        'order_no' => $order->order_no,
                'order_id' => $order->id,
                        'full_response' => $responseArray,
                        'status_message' => $responseArray['status_message'] ?? 'N/A',
                        'failure_message' => $responseArray['failure_message'] ?? 'N/A',
                        'response_code' => $responseArray['response_code'] ?? 'N/A',
                        'bank_ref_no' => $responseArray['bank_ref_no'] ?? 'N/A',
                        'order_id_from_response' => $responseArray['order_id'] ?? 'N/A',
                        'tracking_id' => $responseArray['tracking_id'] ?? 'N/A',
                        'merchant_param1' => $responseArray['merchant_param1'] ?? 'N/A',
                        'merchant_param2' => $responseArray['merchant_param2'] ?? 'N/A',
                        'vault' => $responseArray['vault'] ?? 'N/A',
                        'offer_type' => $responseArray['offer_type'] ?? 'N/A',
                        'offer_code' => $responseArray['offer_code'] ?? 'N/A',
                        'discount_value' => $responseArray['discount_value'] ?? 'N/A',
                        'mer_amount' => $responseArray['mer_amount'] ?? 'N/A',
                        'eci_value' => $responseArray['eci_value'] ?? 'N/A',
                        'retry' => $responseArray['retry'] ?? 'N/A',
                        'response_keys' => array_keys($responseArray),
                    ]);
                    
                    // Try to get the original request data from database
                    try {
                        $originalRequest = DB::table('payment_gateway_response')
                            ->where('order_id', 'like', $order->order_no . '%')
                            ->orderBy('created_at', 'desc')
                            ->first();
                        
                        if ($originalRequest) {
                            Log::error('CCAvenue INVALID - Original Request Data', [
                                'order_no' => $order->order_no,
                                'original_merchant_data' => $originalRequest->merchant_data,
                                'original_amount' => $originalRequest->amount,
                                'original_currency' => $originalRequest->currency,
                                'original_email' => $originalRequest->email,
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Could not retrieve original request data', ['error' => $e->getMessage()]);
                    }
                } else {
                    $message = 'Payment failed. Please try again.';
                }

                Log::info('Ticket CCAvenue Payment - Failed/Cancelled, redirecting to lookup', [
                    'order_no' => $order->order_no,
                    'order_status' => $orderStatus,
                    'message' => $message,
                ]);

                // Redirect to lookup page with TIN as query parameter
                $lookupUrl = route('tickets.payment.lookup', ['eventSlug' => $event->slug ?? $event->id]) . '?tin=' . urlencode($order->order_no);
                return redirect($lookupUrl)->with('error', $message)->with('tin', $order->order_no);
            }
        } catch (\Exception $e) {
            Log::error('Ticket CCAvenue Callback Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'order_id' => $order->id ?? null,
            ]);
            
            // Even on error, try to redirect to lookup with TIN
            $lookupUrl = route('tickets.payment.lookup', ['eventSlug' => $event->slug ?? $event->id]) . '?tin=' . urlencode($order->order_no ?? '');
            return redirect($lookupUrl)->with('error', 'An error occurred while processing payment. Please try again.');
        }
    }

    /**
     * Handle ticket PayPal callback
     */
    private function handleTicketPayPalCallback($request, $order, $event, $paypalOrderId = null)
    {
        if (!$paypalOrderId) {
            return redirect()->route('tickets.payment.lookup', [
                'eventSlug' => $event->slug ?? $event->id
            ])->with('error', 'Payment response incomplete.')->with('tin', $order->order_no);
        }

        try {
            // Capture the order - captureOrder expects an array with 'id' key
            $captureBody = ['id' => $paypalOrderId];

            try {
                $captureResponse = $this->paypalClient->getOrdersController()->captureOrder($captureBody);
                $captureResult = $captureResponse->getResult();
                $status = $captureResult->getStatus();
            } catch (\PaypalServerSdkLib\Exceptions\ApiException $e) {
                Log::error('PayPal API Exception during order capture', [
                    'error' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'paypal_order_id' => $paypalOrderId,
                    'order_no' => $order->order_no,
                ]);

                // Check if it's the OAuth token error
                if (strpos($e->getMessage(), 'OAuthToken') !== false || strpos($e->getMessage(), 'addExpiryTime') !== false) {
                    return redirect()->route('tickets.payment.lookup', [
                        'eventSlug' => $event->slug ?? $event->id
                    ])->with('error', 'Payment gateway authentication failed. Please try again in a few moments.')->with('tin', $order->order_no);
                }

                throw $e;
            } catch (\Exception $e) {
                Log::error('Unexpected PayPal error during order capture', [
                    'error' => $e->getMessage(),
                    'paypal_order_id' => $paypalOrderId,
                    'order_no' => $order->order_no,
                ]);

                return redirect()->route('tickets.payment.lookup', [
                    'eventSlug' => $event->slug ?? $event->id
                ])->with('error', 'Payment processing failed. Please try again or contact support.')->with('tin', $order->order_no);
            }

            $invoice = Invoice::where('invoice_no', $order->order_no)
                ->where('type', 'ticket_registration')
                ->first();

            // Update payment gateway response
            DB::table('payment_gateway_response')
                ->where('payment_id', $paypalOrderId)
                ->update([
                    'status' => $status === 'COMPLETED' ? 'Success' : 'Failed',
                    'response_json' => json_encode($captureResult),
                    'updated_at' => now(),
                ]);

                // Get amount from capture
                $amount = 0;
                if ($captureResult->getPurchaseUnits() && count($captureResult->getPurchaseUnits()) > 0) {
                    $purchaseUnit = $captureResult->getPurchaseUnits()[0];
                    if ($purchaseUnit->getPayments() && $purchaseUnit->getPayments()->getCaptures()) {
                        $capture = $purchaseUnit->getPayments()->getCaptures()[0];
                        $amount = $capture->getAmount()->getValue();
                    }
                }

            // Determine payment status
            $isSuccess = ($status === 'COMPLETED');
            $paymentStatus = $isSuccess ? 'completed' : 'failed';
            $paymentTableStatus = $isSuccess ? 'successful' : 'failed';
            $inrAmount = $amount * (config('constants.USD_RATE', 83));

            // Always create ticket payment record (for both success and failure)
                TicketPayment::create([
                    'order_ids_json' => [$order->id],
                    'method' => 'card',
                'amount' => $inrAmount,
                'status' => $paymentStatus,
                    'gateway_txn_id' => $paypalOrderId,
                    'gateway_name' => 'paypal',
                'paid_at' => $isSuccess ? now() : null,
                    'pg_request_json' => [],
                    'pg_response_json' => (array) $captureResult,
                    'pg_webhook_json' => [],
                ]);

            // Always create Payment record in payments table with TIN/order_no
            // Check if payment already exists (for retry scenarios)
                $payment = null;
                if ($invoice) {
                    $payment = Payment::where('invoice_id', $invoice->id)
                    ->where('order_id', $order->order_no) // Use TIN/order_no for matching
                    ->latest()
                    ->first();
            } else {
                // For tickets without invoice, check by order_id (TIN)
                $payment = Payment::where('order_id', $order->order_no)
                    ->where(function($query) {
                        $query->whereNull('invoice_id')
                              ->orWhere('invoice_id', 0);
                    })
                        ->latest()
                        ->first();
                }

                if (!$payment) {
                // Create new payment record
                // Invoice should exist as it's created during order creation or payment initiation
                if (!$invoice) {
                    // Fallback: Try to find or create invoice
                    $invoice = Invoice::where('invoice_no', $order->order_no)
                        ->where('type', 'ticket_registration')
                        ->first();
                    
                    if (!$invoice) {
                        // Create invoice if it doesn't exist (shouldn't happen, but safety check)
                        $invoice = Invoice::create([
                            'invoice_no'         => $order->order_no,
                            'type'               => 'ticket_registration',
                            'registration_id'    => $order->registration_id,
                            'currency'           => 'USD',
                            'amount'             => $inrAmount,
                            'price'              => $order->subtotal,
                            'gst'                => $order->gst_total,
                            'processing_charges' => $order->processing_charge_total,
                            'total_final_price'  => $inrAmount,
                            'amount_paid'        => 0,
                            'pending_amount'     => $inrAmount,
                            'payment_status'     => 'unpaid',
                        ]);
                    }
                }
                
                Payment::create([
                    'invoice_id' => $invoice->id, // Invoice should always exist now
                        'payment_method' => 'PayPal',
                        'amount' => $inrAmount,
                    'amount_paid' => $isSuccess ? $inrAmount : 0,
                    'amount_received' => $isSuccess ? $inrAmount : 0,
                        'transaction_id' => $paypalOrderId,
                        'pg_result' => $status,
                        'track_id' => $paypalOrderId,
                        'pg_response_json' => json_encode($captureResult),
                    'payment_date' => now(), // Always set payment_date, never null
                        'currency' => 'USD',
                    'status' => $paymentTableStatus,
                    'order_id' => $order->order_no, // Store TIN/order_no in order_id field
                    ]);
                } else {
                // Update existing payment record
                    $payment->update([
                        'payment_method' => 'PayPal',
                        'amount' => $inrAmount,
                    'amount_paid' => $isSuccess ? $inrAmount : 0,
                    'amount_received' => $isSuccess ? $inrAmount : 0,
                        'transaction_id' => $paypalOrderId,
                        'pg_result' => $status,
                        'track_id' => $paypalOrderId,
                        'pg_response_json' => json_encode($captureResult),
                    'payment_date' => now(), // Always set payment_date, never null
                        'currency' => 'USD',
                    'status' => $paymentTableStatus,
                    'order_id' => $order->order_no, // Ensure TIN/order_no is stored
                    ]);
                }

            if ($isSuccess) {
                // Update order status
                $order->update(['status' => 'paid']);

                // Update invoice - mark as paid and generate PIN
                if ($invoice) {
                    // Generate PIN number if not already set
                    $pinNo = $invoice->pin_no;
                    if (empty($pinNo)) {
                        $pinNo = $this->generateTicketPinNo();
                    }
                    
                    $invoice->update([
                        'amount_paid' => $inrAmount,
                        'pending_amount' => max(0, ($invoice->total_final_price ?? $inrAmount) - $inrAmount),
                        'payment_status' => 'paid',
                        'pin_no' => $pinNo, // Save PIN number
                    ]);
                    
                    Log::info('Ticket PayPal Payment - PIN generated', [
                        'order_no' => $order->order_no,
                        'pin_no' => $pinNo,
                    ]);
                }

                // Send payment acknowledgement email (payment successful)
                // Note: Email is sent to user only. Admin notifications should be handled separately if needed.
                try {
                    $contactEmail = $order->registration->contact->email ?? null;
                    if ($contactEmail) {
                        // Send email to user only (removed BCC to admin - admin notifications should be separate)
                        Mail::to($contactEmail)->send(new TicketRegistrationMail($order, $event, true));
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send ticket payment acknowledgement email', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                }

                // Clear session
                session()->forget(['ticket_order_id', 'ticket_order_no', 'ticket_payment_gateway', 'ticket_payment_order_id', 'ticket_paypal_order_id']);

                return redirect()->route('tickets.confirmation', [
                    'eventSlug' => $event->slug ?? $event->id,
                    'token' => $order->secure_token
                ])->with('success', 'Payment successful!')
                  ->with('payment_details', [
                      'gateway' => 'PayPal',
                      'transaction_id' => $paypalOrderId,
                      'amount' => $amount,
                      'currency' => 'USD',
                  ]);
            } else {
                // Payment failed - order status remains 'pending'
                // Payment records already created above with 'failed' status
                // Ensure invoice remains unpaid
                if ($invoice && $invoice->payment_status !== 'unpaid') {
                    $invoice->update([
                        'payment_status' => 'unpaid', // Ensure invoice remains unpaid on failure
                    ]);
                }

                // Check if payment was cancelled (PayPal returns different status)
                $isCancelled = (strtolower($status ?? '') === 'cancelled' || strtolower($status ?? '') === 'voided');
                $message = $isCancelled ? 'Payment was cancelled. You can try again by clicking Pay Now.' : 'Payment was not completed. Please try again.';

                return redirect()->route('tickets.payment.lookup', [
                    'eventSlug' => $event->slug ?? $event->id
                ])->with('error', $message)->with('tin', $order->order_no);
            }
        } catch (\Exception $e) {
            Log::error('Ticket PayPal Callback Error', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
            ]);
            return redirect()->route('tickets.payment.by-tin', [
                'eventSlug' => $event->slug ?? $event->id,
                'tin' => $order->order_no
            ])->with('error', 'An error occurred while processing payment.');
        }
    }

    /**
     * Sanitize string for CCAvenue - remove special characters and limit length
     * CCAvenue is strict about field values and may return "Invalid" if they contain special characters
     */
    private function sanitizeForCcAvenue($value, $maxLength = 50)
    {
        if (empty($value)) {
            return '';
        }
        
        // Convert to string
        $value = (string) $value;
        
        // Remove or replace problematic characters
        // CCAvenue doesn't like: & < > " ' \ / = + ; : @ # $ % ^ * ( ) [ ] { } | ` ~
        $value = preg_replace('/[&<>"\'\\\\\/=+;:@#$%^*()[\]{}|`~]/', '', $value);
        
        // Replace multiple spaces with single space
        $value = preg_replace('/\s+/', ' ', $value);
        
        // Trim whitespace
        $value = trim($value);
        
        // Limit length
        if (strlen($value) > $maxLength) {
            $value = substr($value, 0, $maxLength);
        }
        
        return $value;
    }

    /**
     * Extract phone number without country code
     * 
     * Handles formats like:
     * - +91-9801217815 (country code separated by dash)
     * - +91 9801217815 (country code separated by space)
     * - +919801217815 (no separator)
     * - 919801217815 (no plus sign)
     * - 09801217815 (with leading zero)
     * - 9801217815 (just the number)
     * 
     * Returns just the 10-digit phone number without country code
     */
    /**
     * Generate unique PIN number for ticket payment confirmation
     * Format: PRN-BTS-2026-TKT-XXXXXX (6-digit random number)
     */
    private function generateTicketPinNo()
    {
        // Use ticket-specific prefix: PRN-BTS-2026-TKT-
        $shortName = config('constants.SHORT_NAME', 'BTS');
        $eventYear = config('constants.EVENT_YEAR', date('Y'));
        $prefix = 'PRN-' . $shortName . '-' . $eventYear . '-TKT-';

        $maxAttempts = 100;
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            // Generate 6-digit random number
            $randomNumber = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $pinNo = $prefix . $randomNumber;
            $attempts++;

            // Check if it already exists in invoices table
            if (!Invoice::where('pin_no', $pinNo)->exists()) {
                return $pinNo;
            }
        }

        // Fallback: use timestamp-based
        $timestamp = substr(time(), -6);
        $pinNo = $prefix . $timestamp;
        if (!Invoice::where('pin_no', $pinNo)->exists()) {
            return $pinNo;
        }

        // Last resort: use microtime
        $microtime = substr(str_replace('.', '', microtime(true)), -6);
        return $prefix . $microtime;
    }

    private function extractPhoneNumber($phone)
    {
        if (empty($phone)) {
            return '';
        }
        
        $originalPhone = $phone;
        $phone = (string) $phone;
        $phone = trim($phone);
        
        // Method 1: Check if phone has a separator (dash, space) after country code
        // Format: +91-9801217815 or +91 9801217815
        // Accept 7+ digits after separator (some countries have shorter numbers)
        if (preg_match('/^[\+]?(\d{1,3})[\-\s](\d{7,})$/', $phone, $matches)) {
            // $matches[1] = country code (91)
            // $matches[2] = actual phone number (9801217815)
            $extractedNumber = $matches[2];
            
            Log::info('Phone extraction - Method 1 (separator found)', [
                'original' => $originalPhone,
                'country_code' => $matches[1],
                'phone_number' => $extractedNumber,
                'phone_length' => strlen($extractedNumber),
            ]);
            
            return $extractedNumber;
        }
        
        // Method 2: No separator - remove all non-numeric and process
        $numericOnly = preg_replace('/[^0-9]/', '', $phone);
        $totalDigits = strlen($numericOnly);
        
        Log::info('Phone extraction - Method 2 (numeric only)', [
            'original' => $originalPhone,
            'numeric_only' => $numericOnly,
            'total_digits' => $totalDigits,
        ]);
        
        // If exactly 10 digits, return as is (already a phone number without country code)
        if ($totalDigits === 10) {
            return $numericOnly;
        }
        
        // If 11 digits and starts with 0, remove the leading 0
        if ($totalDigits === 11 && substr($numericOnly, 0, 1) === '0') {
            return substr($numericOnly, 1);
        }
        
        // If 12 digits and starts with 91, remove the country code
        if ($totalDigits === 12 && substr($numericOnly, 0, 2) === '91') {
            return substr($numericOnly, 2);
        }
        
        // If more than 10 digits and starts with 91, remove the country code
        if ($totalDigits > 10 && substr($numericOnly, 0, 2) === '91') {
            return substr($numericOnly, 2);
        }
        
        // If more than 10 digits and starts with 0, remove the leading 0
        if ($totalDigits > 10 && substr($numericOnly, 0, 1) === '0') {
            return substr($numericOnly, 1);
        }
        
        // Fallback: Return last 10 digits if we have more than 10
        if ($totalDigits > 10) {
            $extracted = substr($numericOnly, -10);
            Log::warning('Phone extraction - Using last 10 digits as fallback', [
                'original' => $originalPhone,
                'extracted' => $extracted,
            ]);
            return $extracted;
        }
        
        // Return whatever we have (might be less than 10 digits)
        return $numericOnly;
    }
}
