<?php

namespace App\Http\Controllers\Delegate;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Ticket\TicketUpgradeRequest;
use App\Models\Ticket\TicketOrder;
use App\Models\Ticket\TicketPayment;
use App\Models\Payment;
use App\Models\Invoice;
use App\Services\TicketUpgradeService;
use App\Services\CcAvenueService;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;
use PaypalServerSdkLib\Authentication\ClientCredentialsAuthCredentialsBuilder;
use PaypalServerSdkLib\Environment;
use PaypalServerSdkLib\Models\Builders\OrderRequestBuilder;
use PaypalServerSdkLib\Models\CheckoutPaymentIntent;
use PaypalServerSdkLib\Models\Builders\PurchaseUnitRequestBuilder;
use PaypalServerSdkLib\Models\Builders\AmountWithBreakdownBuilder;

class DelegateUpgradePaymentController extends Controller
{
    protected $upgradeService;
    protected $ccAvenueService;

    public function __construct(TicketUpgradeService $upgradeService, CcAvenueService $ccAvenueService)
    {
        $this->upgradeService = $upgradeService;
        $this->ccAvenueService = $ccAvenueService;
    }

    /**
     * Initiate payment for upgrade
     */
    public function initiatePayment($requestId)
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        $upgradeRequest = TicketUpgradeRequest::where('contact_id', $contact->id)
            ->with(['registration.event', 'upgradeOrder'])
            ->findOrFail($requestId);

        if (!$upgradeRequest->canBeProcessed()) {
            return redirect()->route('delegate.upgrades.index')
                ->with('error', 'Upgrade request is not available for processing.');
        }

        // Create order if not exists
        if (!$upgradeRequest->upgradeOrder) {
            $order = $this->upgradeService->createUpgradeOrder($requestId);
        } else {
            $order = $upgradeRequest->upgradeOrder;
        }

        // Check if order is already paid
        if ($order->status === 'paid') {
            return redirect()->route('delegate.upgrades.receipt', $requestId)
                ->with('success', 'Upgrade payment has already been completed.');
        }

        $event = $upgradeRequest->registration->event;
        $registration = $upgradeRequest->registration;

        // Determine currency and payment gateway
        $isInternational = ($registration->nationality === 'International' || 
                           $registration->nationality === 'international');
        $currency = $isInternational ? 'USD' : 'INR';
        // For international: Use PayPal (can be changed to Nafianal later)
        // For national: Use CCAvenue
        $paymentGateway = $isInternational ? 'PayPal' : 'CCAvenue';

        // Prepare payment data
        $billingName = $contact->name ?? '';
        $billingEmail = $contact->email ?? '';
        $billingPhone = $contact->phone ?? $registration->company_phone;
        $amount = $upgradeRequest->total_amount;

        // For CCAvenue
        if ($paymentGateway === 'CCAvenue') {
            $paymentData = [
                'order_id' => $order->order_no . '_' . time(),
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => $currency,
                'redirect_url' => route('delegate.upgrades.payment.success', $requestId),
                'cancel_url' => route('delegate.upgrades.payment.failure', $requestId),
                'billing_name' => $billingName,
                'billing_address' => $registration->company_name,
                'billing_city' => $registration->company_city ?? '',
                'billing_state' => $registration->company_state ?? '',
                'billing_zip' => '',
                'billing_country' => $registration->company_country,
                'billing_tel' => $billingPhone,
                'billing_email' => $billingEmail,
            ];

            $result = $this->ccAvenueService->initiateTransaction($paymentData);

            if ($result['success']) {
                return redirect($result['redirect_url']);
            } else {
                return redirect()->route('delegate.upgrades.receipt', $requestId)
                    ->with('error', 'Failed to initiate payment. Please try again.');
            }
        }

        // For PayPal (international payments)
        if ($paymentGateway === 'PayPal') {
            try {
                // Initialize PayPal client
                $paypalMode = config('constants.PAYPAL_MODE', 'sandbox');
                $clientId = $paypalMode === 'live' 
                    ? config('constants.PAYPAL_LIVE_CLIENT_ID')
                    : config('constants.PAYPAL_SANDBOX_CLIENT_ID');
                $clientSecret = $paypalMode === 'live'
                    ? config('constants.PAYPAL_LIVE_SECRET')
                    : config('constants.PAYPAL_SANDBOX_SECRET');
                
                $environment = $paypalMode === 'live' ? Environment::PRODUCTION : Environment::SANDBOX;
                
                $paypalClient = PaypalServerSdkClientBuilder::init()
                    ->environment($environment)
                    ->clientCredentials(
                        ClientCredentialsAuthCredentialsBuilder::init()
                            ->clientId($clientId)
                            ->clientSecret($clientSecret)
                            ->build()
                    )
                    ->build();

                // Build purchase unit
                $purchaseUnit = PurchaseUnitRequestBuilder::init(
                    AmountWithBreakdownBuilder::init($currency, $amount)->build()
                )
                    ->description('Ticket Upgrade for ' . ($registration->company_name ?? 'Event'))
                    ->invoiceId($order->order_no . '_' . time())
                    ->build();

                // Build order request
                $orderRequest = OrderRequestBuilder::init()
                    ->intent(CheckoutPaymentIntent::CAPTURE)
                    ->purchaseUnits([$purchaseUnit])
                    ->applicationContext(
                        \PaypalServerSdkLib\Models\Builders\ApplicationContextBuilder::init()
                            ->returnUrl(route('delegate.upgrades.payment.success', $requestId) . '?gateway=paypal')
                            ->cancelUrl(route('delegate.upgrades.payment.failure', $requestId))
                            ->build()
                    )
                    ->build();

                // Create PayPal order
                $apiResponse = $paypalClient->ordersController()->ordersCreate($orderRequest);
                
                if ($apiResponse->getStatusCode() !== 201) {
                    throw new \Exception('Failed to create PayPal order');
                }

                $paypalOrderId = $apiResponse->getResult()->getId();

                // Store PayPal order ID in session
                session(['upgrade_paypal_order_id' => $paypalOrderId, 'upgrade_request_id' => $requestId]);

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
                    throw new \Exception('Failed to get PayPal approval URL');
                }
            } catch (\Exception $e) {
                Log::error('PayPal upgrade payment initiation failed', [
                    'request_id' => $requestId,
                    'error' => $e->getMessage(),
                ]);
                return redirect()->route('delegate.upgrades.receipt', $requestId)
                    ->with('error', 'Failed to initiate PayPal payment. Please try again.');
            }
        }

        return redirect()->route('delegate.upgrades.receipt', $requestId)
            ->with('error', 'Payment gateway not configured.');
    }

    /**
     * Handle successful payment callback
     */
    public function paymentSuccess($requestId, Request $request)
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        $upgradeRequest = TicketUpgradeRequest::where('contact_id', $contact->id)
            ->with(['registration.event', 'upgradeOrder'])
            ->findOrFail($requestId);

        $order = $upgradeRequest->upgradeOrder;
        if (!$order) {
            return redirect()->route('delegate.upgrades.index')
                ->with('error', 'Order not found.');
        }

        // Handle CCAvenue encrypted response
        $encResponse = $request->input('encResp');
        $paymentData = [];

        if ($encResponse) {
            try {
                $credentials = $this->ccAvenueService->getCredentials();
                $decryptedResponse = $this->ccAvenueService->decrypt($encResponse, $credentials['working_key']);
                parse_str($decryptedResponse, $paymentData);

                Log::info('Upgrade CCAvenue callback decrypted', [
                    'request_id' => $requestId,
                    'order_status' => $paymentData['order_status'] ?? null,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to decrypt CCAvenue response for upgrade', [
                    'request_id' => $requestId,
                    'error' => $e->getMessage(),
                ]);
                return redirect()->route('delegate.upgrades.index')
                    ->with('error', 'Payment response could not be processed. Please contact support.');
            }
        } else {
            // For PayPal or other gateways
            if ($request->has('gateway') && $request->gateway === 'paypal') {
                // Handle PayPal callback
                $paypalOrderId = session('upgrade_paypal_order_id');
                if (!$paypalOrderId) {
                    return redirect()->route('delegate.upgrades.index')
                        ->with('error', 'PayPal order not found. Please try again.');
                }

                // Capture PayPal payment
                try {
                    $paypalMode = config('constants.PAYPAL_MODE', 'sandbox');
                    $clientId = $paypalMode === 'live' 
                        ? config('constants.PAYPAL_LIVE_CLIENT_ID')
                        : config('constants.PAYPAL_SANDBOX_CLIENT_ID');
                    $clientSecret = $paypalMode === 'live'
                        ? config('constants.PAYPAL_LIVE_SECRET')
                        : config('constants.PAYPAL_SANDBOX_SECRET');
                    
                    $environment = $paypalMode === 'live' ? Environment::PRODUCTION : Environment::SANDBOX;
                    
                    $paypalClient = PaypalServerSdkClientBuilder::init()
                        ->environment($environment)
                        ->clientCredentials(
                            ClientCredentialsAuthCredentialsBuilder::init()
                                ->clientId($clientId)
                                ->clientSecret($clientSecret)
                                ->build()
                        )
                        ->build();

                    // Capture the order
                    $captureResponse = $paypalClient->ordersController()->ordersCapture($paypalOrderId);
                    
                    if ($captureResponse->getStatusCode() === 201) {
                        $captureResult = $captureResponse->getResult();
                        $paymentData = [
                            'status' => 'COMPLETED',
                            'transaction_id' => $captureResult->getId(),
                            'gateway' => 'paypal',
                            'method' => 'paypal',
                            'amount' => $captureResult->getPurchaseUnits()[0]->getPayments()->getCaptures()[0]->getAmount()->getValue(),
                            'currency' => $captureResult->getPurchaseUnits()[0]->getPayments()->getCaptures()[0]->getAmount()->getCurrencyCode(),
                        ];
                    } else {
                        throw new \Exception('PayPal capture failed');
                    }
                } catch (\Exception $e) {
                    Log::error('PayPal capture failed for upgrade', [
                        'request_id' => $requestId,
                        'error' => $e->getMessage(),
                    ]);
                    return redirect()->route('delegate.upgrades.index')
                        ->with('error', 'Payment capture failed. Please contact support.');
                }
            } else {
                // For other gateways, use request data directly
                $paymentData = $request->all();
            }
        }

        $orderStatus = $paymentData['order_status'] ?? ($paymentData['status'] ?? null);
        $isSuccess = ($orderStatus === 'Success' || $orderStatus === 'success' || $paymentData['status'] === 'COMPLETED');

        if (!$isSuccess) {
            // Payment failed
            $upgradeRequest->markAsFailed();
            return redirect()->route('delegate.upgrades.index')
                ->with('error', 'Payment failed. Please try again.');
        }

        // Process payment success
        try {
            DB::beginTransaction();

            // Create ticket payment record
            TicketPayment::create([
                'order_ids_json' => [$order->id],
                'method' => $paymentData['payment_mode'] ?? ($paymentData['method'] ?? 'ccavenue'),
                'amount' => $upgradeRequest->total_amount,
                'status' => 'completed',
                'gateway_txn_id' => $paymentData['tracking_id'] ?? ($paymentData['transaction_id'] ?? null),
                'gateway_name' => $paymentData['gateway'] ?? 'ccavenue',
                'paid_at' => isset($paymentData['trans_date']) 
                    ? \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $paymentData['trans_date'])->format('Y-m-d H:i:s')
                    : now(),
                'pg_response_json' => $paymentData,
            ]);

            // Update order
            $order->update(['status' => 'paid']);

            // Process upgrade (update master tables) - this updates tickets, assignments, and creates upgrade record
            $this->upgradeService->processPaymentSuccess($requestId, $paymentData);

            // Generate final receipt
            $this->upgradeService->generateUpgradeReceipt($requestId, 'final');

            DB::commit();

            return redirect()->route('delegate.upgrades.receipt', $requestId)
                ->with('success', 'Upgrade payment successful! Your ticket has been upgraded.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Upgrade payment processing failed', [
                'request_id' => $requestId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('delegate.upgrades.index')
                ->with('error', 'Payment was successful but upgrade processing failed. Please contact support.');
        }
    }

    /**
     * Handle failed payment
     */
    public function paymentFailure($requestId)
    {
        $account = Auth::guard('delegate')->user();
        $contact = $account->contact;

        $upgradeRequest = TicketUpgradeRequest::where('contact_id', $contact->id)
            ->findOrFail($requestId);

        // Mark as failed
        $upgradeRequest->markAsFailed();

        return redirect()->route('delegate.upgrades.index')
            ->with('error', 'Payment failed. You can try again or cancel the upgrade request.');
    }

    /**
     * Handle payment webhook
     */
    public function webhook(Request $request)
    {
        // Similar to existing webhook handling
        // Process payment webhook from gateway
        Log::info('Upgrade payment webhook received', ['data' => $request->all()]);

        // Extract request ID from order number or other identifier
        // Process payment and update upgrade request

        return response()->json(['status' => 'received']);
    }
}
