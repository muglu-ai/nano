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

            $apiResponse = $this->paypalClient->getOrdersController()->ordersCreate($orderRequest);
            $paypalOrderId = $apiResponse->getResult()->getId();

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
            $captureResponse = $this->paypalClient->getOrdersController()->ordersCapture($paypalOrderId);
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
        
        // Get TIN from query parameter (tin or tin_no) or session
        $tin = $request->query('tin') ?? $request->query('tin_no');
        if (!$tin && session('tin')) {
            $tin = session('tin');
        }
        
        // If TIN is provided, automatically fetch and display order details
        if ($tin) {
            $tinNo = trim($tin);
            
            // Find ticket order by order_no (TIN)
            $order = TicketOrder::where('order_no', $tinNo)
                ->whereHas('registration', function($q) use ($event) {
                    $q->where('event_id', $event->id);
                })
                ->with(['registration.contact', 'registration.event', 'items.ticketType', 'registration.registrationCategory'])
                ->first();
            
            if ($order) {
                // Get email from registration contact
                $email = $order->registration->contact->email ?? $order->registration->company_email ?? 'N/A';
                
                // Display order details directly
                return view('payment.ticket-order-details', compact('event', 'order', 'email'));
            } else {
                // Order not found, show lookup form with error
                return view('payment.ticket-lookup', compact('event', 'tin'))
                    ->with('error', 'No ticket order found with the provided Order Number.');
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
            $billingPhone = $registration->contact->phone ?? $registration->company_phone ?? '';

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
            
            // Validate required fields
            if (empty($billingEmail)) {
                Log::error('Ticket CCAvenue Payment - Missing billing email', [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                ]);
                return redirect()->back()->with('error', 'Billing email is required for payment.');
            }

            if (empty($billingName)) {
                Log::warning('Ticket CCAvenue Payment - Missing billing name, using company name', [
                    'order_id' => $order->id,
                ]);
                $billingName = $registration->company_name ?? 'Customer';
            }
            
            $paymentData = [
                'order_id' => $orderId,
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => $currency,
                'redirect_url' => route('registration.ticket.payment.callback', ['eventSlug' => $eventSlug, 'gateway' => 'ccavenue']),
                'cancel_url' => route('tickets.payment.lookup', ['eventSlug' => $eventSlug, 'tin' => $order->order_no]),
                'billing_name' => $billingName,
                'billing_address' => $registration->company_name ?? '',
                'billing_city' => $registration->company_city ?? '',
                'billing_state' => $registration->company_state ?? '',
                'billing_zip' => $registration->postal_code ?? '',
                'billing_country' => $registration->company_country ?? 'India',
                'billing_tel' => $billingPhone,
                'billing_email' => $billingEmail,
            ];

            Log::info('Ticket CCAvenue Payment - Initiating transaction', [
                'order_id' => $order->id,
                'order_no' => $order->order_no,
                'amount' => $amount,
                'currency' => $currency,
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

            $apiResponse = $this->paypalClient->getOrdersController()->ordersCreate($orderBody);
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
            if (empty($encResponse)) {
                return redirect()->route('tickets.payment.lookup', $eventSlug)
                    ->with('error', 'Payment response incomplete.');
            }

            $credentials = $this->ccAvenueService->getCredentials();
            $decryptedResponse = $this->ccAvenueService->decrypt($encResponse, $credentials['working_key']);
            parse_str($decryptedResponse, $responseArray);

            $orderIdFromGateway = $responseArray['order_id'] ?? null;
            $orderNo = $orderIdFromGateway ? explode('_', $orderIdFromGateway)[0] : null;

            if (!$orderNo) {
                return redirect()->route('tickets.payment.lookup', $eventSlug)
                    ->with('error', 'Order not found for payment callback.');
            }

            $order = TicketOrder::with(['registration.contact', 'registration.event', 'items.ticketType'])
                ->where('order_no', $orderNo)
                ->whereHas('registration', function($q) use ($event) {
                    $q->where('event_id', $event->id);
                })
                ->firstOrFail();

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
            if (!$responseArray) {
                $encResponse = $request->input('encResp');
                if (empty($encResponse)) {
                    return redirect()->route('tickets.payment.lookup', [
                        'eventSlug' => $event->slug ?? $event->id
                    ])->with('error', 'Payment response incomplete.')->with('tin', $order->order_no);
                }
                $credentials = $this->ccAvenueService->getCredentials();
                $decryptedResponse = $this->ccAvenueService->decrypt($encResponse, $credentials['working_key']);
                parse_str($decryptedResponse, $responseArray);
            }

            $orderStatus = $responseArray['order_status'] ?? null;
            $orderId = $responseArray['order_id'] ?? null;
            $transDate = isset($responseArray['trans_date'])
                ? Carbon::createFromFormat('d/m/Y H:i:s', $responseArray['trans_date'])->format('Y-m-d H:i:s')
                : now();

            $invoice = Invoice::where('invoice_no', $order->order_no)
                ->where('type', 'ticket_registration')
                ->first();

            // Update payment gateway response
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

            // Determine payment status
            $isSuccess = ($orderStatus === 'Success');
            $paymentStatus = $isSuccess ? 'completed' : 'failed';
            $paymentTableStatus = $isSuccess ? 'successful' : 'failed';

            // Always create ticket payment record (for both success and failure)
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
                    'payment_date' => $isSuccess ? $transDate : null,
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
                    'payment_date' => $isSuccess ? $transDate : null,
                        'currency' => 'INR',
                    'status' => $paymentTableStatus,
                    'order_id' => $order->order_no, // Ensure TIN/order_no is stored
                    ]);
                }

            if ($isSuccess) {
                // Update order status
                $order->update(['status' => 'paid']);

                // Update invoice status/amounts - mark as paid
                if ($invoice) {
                    $paidAmount = $responseArray['mer_amount'] ?? $order->total;
                    $invoice->update([
                        'amount_paid' => $paidAmount,
                        'pending_amount' => max(0, ($invoice->total_final_price ?? $paidAmount) - $paidAmount),
                        'payment_status' => 'paid', // Mark invoice as paid
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
                // Ensure invoice remains unpaid
                if ($invoice && $invoice->payment_status !== 'unpaid') {
                    $invoice->update([
                        'payment_status' => 'unpaid', // Ensure invoice remains unpaid on failure
                    ]);
                }

                // Check if payment was cancelled
                $isCancelled = ($orderStatus === 'Cancelled' || $orderStatus === 'Aborted' || strtolower($orderStatus ?? '') === 'cancelled');
                $message = $isCancelled ? 'Payment was cancelled. You can try again by clicking Pay Now.' : 'Payment failed. Please try again.';

                return redirect()->route('tickets.payment.lookup', [
                    'eventSlug' => $event->slug ?? $event->id
                ])->with('error', $message)->with('tin', $order->order_no);
            }
        } catch (\Exception $e) {
            Log::error('Ticket CCAvenue Callback Error', [
                'error' => $e->getMessage(),
                'order_id' => $order->id,
            ]);
            return redirect()->route('tickets.payment.lookup', [
                'eventSlug' => $event->slug ?? $event->id
            ])->with('error', 'An error occurred while processing payment.')->with('tin', $order->order_no ?? null);
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
            // Capture the order - ordersCapture expects an array with 'id' key
            $captureBody = ['id' => $paypalOrderId];
            $captureResponse = $this->paypalClient->getOrdersController()->ordersCapture($captureBody);
            $captureResult = $captureResponse->getResult();
            $status = $captureResult->getStatus();

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
                    'payment_date' => $isSuccess ? now() : null,
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
                    'payment_date' => $isSuccess ? now() : null,
                        'currency' => 'USD',
                    'status' => $paymentTableStatus,
                    'order_id' => $order->order_no, // Ensure TIN/order_no is stored
                    ]);
                }

            if ($isSuccess) {
                // Update order status
                $order->update(['status' => 'paid']);

                // Update invoice - mark as paid
                if ($invoice) {
                    $invoice->update([
                        'amount_paid' => $inrAmount,
                        'pending_amount' => max(0, ($invoice->total_final_price ?? $inrAmount) - $inrAmount),
                        'payment_status' => 'paid', // Mark invoice as paid
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
}
