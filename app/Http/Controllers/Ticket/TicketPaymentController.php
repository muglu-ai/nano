<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Models\Ticket\TicketContact;
use App\Models\Ticket\TicketRegistration;
use App\Models\Ticket\TicketOrder;
use App\Models\Ticket\TicketOrderItem;
use App\Models\Ticket\TicketType;
use App\Models\Ticket\TicketRegistrationCategory;
use App\Models\Ticket\TicketDelegate;
use App\Models\Ticket\TicketPayment;
use App\Models\Ticket\TicketRegistrationTracking;
use App\Models\Payment;
use App\Models\Invoice;
use App\Services\CcAvenueService;
use App\Mail\TicketRegistrationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class TicketPaymentController extends Controller
{
    protected $ccAvenueService;

    public function __construct(CcAvenueService $ccAvenueService)
    {
        $this->ccAvenueService = $ccAvenueService;
    }

    /**
     * Initiate payment - Create order and show payment page
     */
    public function initiate(Request $request, $eventSlug)
    {
        $event = Events::where('slug', $eventSlug)->orWhere('id', $eventSlug)->firstOrFail();
        
        // Check if order number is provided in URL (for direct access)
        $orderNo = $request->query('order');
        if ($orderNo) {
            // If order number is provided, redirect to initiateByTin
            return redirect()->route('tickets.payment.by-tin', [
                'eventSlug' => $event->slug ?? $event->id,
                'tin' => $orderNo
            ]);
        }
        
        // Get registration data from session
        $registrationData = session('ticket_registration_data');

        // Log for debugging when initiating payment
        Log::info('Ticket Payment - Initiate called', [
            'event_id' => $event->id,
            'event_slug' => $event->slug,
            'has_registration_data' => $registrationData !== null,
            'registration_event_id' => $registrationData['event_id'] ?? null,
            'registration_data_keys' => $registrationData ? array_keys($registrationData) : [],
        ]);
        
        if (!$registrationData || $registrationData['event_id'] != $event->id) {
            Log::warning('Ticket Payment - Missing or mismatched registration data', [
                'event_id' => $event->id,
                'registration_event_id' => $registrationData['event_id'] ?? null,
            ]);
            return redirect()->route('tickets.register', $event->slug ?? $event->id)
                ->with('error', 'Please complete the registration form first.');
        }

        // Track payment initiated - update with latest registration data
        $trackingToken = session('ticket_registration_tracking_token');
        if ($trackingToken) {
            $tracking = TicketRegistrationTracking::where('tracking_token', $trackingToken)
                ->where('event_id', $event->id)
                ->first();
            
            if ($tracking && $registrationData) {
                // Store all registration data before payment initiation
                $tracking->updateStatus('payment_initiated', [
                    'registration_data' => $registrationData, // Store all form data including delegates
                ]);
            }
        }

        // Clear session data immediately when proceeding to payment
        // This prevents user from going back to edit the same registration
        session()->forget('ticket_registration_data');

        try {
            DB::beginTransaction();

            // Load ticket type
            $ticketType = TicketType::where('id', $registrationData['ticket_type_id'])
                ->where('event_id', $event->id)
                ->with(['category', 'subcategory'])
                ->firstOrFail();

            // Determine nationality for pricing
            $nationality = $registrationData['nationality'] ?? 'Indian';
            $isInternational = ($nationality === 'International' || $nationality === 'international');
            $nationalityForPrice = $isInternational ? 'international' : 'national';
            
            // Calculate pricing
            $quantity = $registrationData['delegate_count'];
            $unitPrice = $ticketType->getCurrentPrice($nationalityForPrice);
            $subtotal = $unitPrice * $quantity;
            
            $gstRate = config('constants.GST_RATE', 18);
            $country = $registrationData['company_country'] ?? $registrationData['country'] ?? '';
            $isIndian = strtolower($country) === 'india' || $nationality === 'Indian';
            $processingChargeRate = $isIndian 
                ? config('constants.IND_PROCESSING_CHARGE', 3) 
                : config('constants.INT_PROCESSING_CHARGE', 9);
            
            $gstAmount = ($subtotal * $gstRate) / 100;
            $processingChargeAmount = (($subtotal + $gstAmount) * $processingChargeRate) / 100;
            $total = $subtotal + $gstAmount + $processingChargeAmount;
            
            // Determine currency
            $currency = $isInternational ? 'USD' : 'INR';

            // Create or get contact (use first delegate email if contact email not provided)
            $contactEmail = $registrationData['contact_email'] ?? ($registrationData['delegates'][0]['email'] ?? null);
            $contactName = $registrationData['contact_name'] ?? ($registrationData['delegates'][0]['first_name'] . ' ' . ($registrationData['delegates'][0]['last_name'] ?? ''));
            $contactPhone = $this->formatPhoneNumber($registrationData['contact_phone'] ?? ($registrationData['delegates'][0]['phone'] ?? null));
            
            if ($contactEmail) {
                $contact = TicketContact::firstOrCreate(
                    ['email' => $contactEmail],
                    [
                        'name' => $contactName,
                        'phone' => $contactPhone,
                    ]
                );
            } else {
                // Fallback: use first delegate
                $firstDelegate = $registrationData['delegates'][0] ?? null;
                if ($firstDelegate) {
                    $contact = TicketContact::firstOrCreate(
                        ['email' => $firstDelegate['email']],
                        [
                            'name' => $firstDelegate['first_name'] . ' ' . ($firstDelegate['last_name'] ?? ''),
                            'phone' => $this->formatPhoneNumber($firstDelegate['phone'] ?? null),
                        ]
                    );
                } else {
                    throw new \Exception('Unable to create contact: No contact email or delegate email provided.');
                }
            }

            // Create registration
            $registration = TicketRegistration::create([
                'event_id' => $event->id,
                'contact_id' => $contact->id,
                'company_name' => $registrationData['organisation_name'],
                'company_country' => $registrationData['company_country'] ?? $registrationData['country'] ?? null,
                'company_state' => $registrationData['company_state'] ?? $registrationData['state'] ?? null,
                'company_city' => $registrationData['company_city'] ?? $registrationData['city'] ?? null,
                'company_phone' => $this->formatPhoneNumber($registrationData['phone']),
                'industry_sector' => $registrationData['industry_sector'],
                'organisation_type' => $registrationData['organisation_type'],
                'registration_category_id' => $registrationData['registration_category_id'],
                'gst_required' => $registrationData['gst_required'] == '1',
                'gstin' => $registrationData['gstin'] ?? null,
                'gst_legal_name' => $registrationData['gst_legal_name'] ?? null,
                'gst_address' => $registrationData['gst_address'] ?? null,
                'gst_state' => $registrationData['gst_state'] ?? null,
                'nationality' => $registrationData['nationality'],
            ]);

            // Generate unique order number using TIN pattern
            $orderNo = $this->generateUniqueOrderNumber();

            // Create order
            $order = TicketOrder::create([
                'registration_id' => $registration->id,
                'order_no' => $orderNo,
                'subtotal' => $subtotal,
                'gst_total' => $gstAmount,
                'processing_charge_total' => $processingChargeAmount,
                'discount_amount' => 0,
                'total' => $total,
                'status' => 'pending',
            ]);

            // Update tracking with order information and complete registration data
            $trackingToken = session('ticket_registration_tracking_token');
            if ($trackingToken) {
                $tracking = TicketRegistrationTracking::where('tracking_token', $trackingToken)
                    ->where('event_id', $event->id)
                    ->first();
                
                if ($tracking && $registrationData) {
                    // Store ALL registration data including all form fields and delegates
                    $tracking->update([
                        'registration_id' => $registration->id,
                        'order_id' => $order->id,
                        'order_no' => $orderNo,
                        'registration_data' => $registrationData, // Complete form data in JSON
                        'calculated_total' => $total,
                        'final_total' => $total,
                    ]);
                }
            }

            // Create order item
            TicketOrderItem::create([
                'order_id' => $order->id,
                'ticket_type_id' => $ticketType->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
                'gst_rate' => $gstRate,
                'gst_amount' => $gstAmount,
                'processing_charge_rate' => $processingChargeRate,
                'processing_charge_amount' => $processingChargeAmount,
                'total' => $total,
                'pricing_type' => $ticketType->isEarlyBirdActive() ? 'early_bird' : 'regular',
            ]);

            // Create invoice for the order (for payment tracking and mapping)
            $invoice = Invoice::create([
                'invoice_no'         => $order->order_no,
                'type'               => 'ticket_registration',
                'registration_id'    => $registration->id, // link to ticket registration for traceability
                'currency'           => $currency,
                'amount'             => $total, // base amount required by DB
                'price'              => $subtotal,
                'gst'                => $gstAmount,
                'processing_charges' => $processingChargeAmount,
                'total_final_price'  => $total,
                'amount_paid'        => 0,
                'pending_amount'     => $total,
                'payment_status'     => 'unpaid', // Initially unpaid
            ]);

            // Create delegates (always required now)
            $delegates = $registrationData['delegates'] ?? [];
            if (count($delegates) > 0) {
                foreach ($delegates as $delegateData) {
                    TicketDelegate::create([
                        'registration_id' => $registration->id,
                        'salutation' => $delegateData['salutation'] ?? null,
                        'first_name' => $delegateData['first_name'],
                        'last_name' => $delegateData['last_name'],
                        'email' => $delegateData['email'],
                        'phone' => $this->formatPhoneNumber($delegateData['phone'] ?? null),
                        'job_title' => $delegateData['job_title'] ?? null,
                    ]);
                }
            } else {
                // Fallback: This should not happen as validation requires delegates
                // But if it does, create from contact info (only if GST is required)
                if ($registrationData['gst_required'] == '1' && isset($registrationData['contact_name'])) {
                    TicketDelegate::create([
                        'registration_id' => $registration->id,
                        'first_name' => $registrationData['contact_name'],
                        'last_name' => '',
                        'email' => $registrationData['contact_email'] ?? $contact->email,
                        'phone' => $registrationData['contact_phone'] ?? $contact->phone,
                    ]);
                }
            }

            DB::commit();
            
            // Session already cleared at the start of payment initiation
            // No need to clear again here

            // Load registration category for display (may be null)
            $registrationCategory = null;
            if ($registration->registration_category_id) {
                $registrationCategory = TicketRegistrationCategory::find($registration->registration_category_id);
            }
            
            // Reload order with relationships
            $order->load(['registration.contact', 'items.ticketType', 'registration.delegates', 'registration.registrationCategory']);

            // Send registration confirmation email with payment link
            try {
                $contactEmail = $order->registration->contact->email ?? null;
                if ($contactEmail) {
                    Mail::to($contactEmail)->send(new TicketRegistrationMail($order, $event));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send ticket registration email', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                ]);
                // Don't fail the transaction if email fails
            }

            // Redirect to payment page with order number in URL for easy sharing and refresh
            return redirect()->route('tickets.payment.by-tin', [
                'eventSlug' => $event->slug ?? $event->id,
                'tin' => $order->order_no
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Ticket payment initiation error: ' . $e->getMessage(), [
                'event' => $event->id,
                'registration_data' => $registrationData,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('tickets.preview', $event->slug ?? $event->id)
                ->with('error', 'An error occurred while processing your payment. Please try again.');
        }
    }

    /**
     * Show payment page (if payment fails or user cancels)
     */
    public function show($token)
    {
        $order = TicketOrder::where('secure_token', $token)
            ->with(['registration.event', 'items.ticketType'])
            ->firstOrFail();
        
        return view('tickets.payment.show', compact('order'));
    }

    /**
     * Payment callback from gateway
     */
    public function callback(Request $request, $token)
    {
        $order = TicketOrder::where('secure_token', $token)
            ->with(['registration.event', 'registration.contact'])
            ->firstOrFail();
        
        $event = $order->registration->event;
        
        // Handle CCAvenue response
        $encResponse = $request->input('encResp');
        
        if ($encResponse) {
            try {
                $credentials = $this->ccAvenueService->getCredentials();
                $decryptedResponse = $this->ccAvenueService->decrypt($encResponse, $credentials['working_key']);
                parse_str($decryptedResponse, $responseArray);

                $orderStatus = $responseArray['order_status'] ?? null;
                $orderIdFromGateway = $responseArray['order_id'] ?? null;
                $transDate = isset($responseArray['trans_date'])
                    ? Carbon::createFromFormat('d/m/Y H:i:s', $responseArray['trans_date'])->format('Y-m-d H:i:s')
                    : now();

                // Check for invoice
                $invoice = Invoice::where('invoice_no', $order->order_no)
                    ->where('type', 'ticket_registration')
                    ->first();

                // Update payment gateway response table
                DB::table('payment_gateway_response')
                    ->where('order_id', $orderIdFromGateway)
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

                // Create or update ticket payment record
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
                // Invoice should always exist as it's created during order creation
                if (!$invoice) {
                    // Fallback: Create invoice if it doesn't exist (shouldn't happen, but safety check)
                    $invoice = Invoice::where('invoice_no', $order->order_no)
                        ->where('type', 'ticket_registration')
                        ->first();
                    
                    if (!$invoice) {
                        $invoice = Invoice::create([
                            'invoice_no'         => $order->order_no,
                            'type'               => 'ticket_registration',
                            'registration_id'    => $order->registration_id,
                            'currency'           => $currency ?? 'INR',
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
                    'invoice_id' => $invoice->id, // Invoice should always exist
                    'payment_method' => $responseArray['payment_mode'] ?? 'CCAvenue',
                    'amount' => $responseArray['mer_amount'] ?? $order->total,
                    'amount_paid' => $isSuccess ? ($responseArray['mer_amount'] ?? $order->total) : 0,
                    'amount_received' => $isSuccess ? ($responseArray['mer_amount'] ?? $order->total) : 0,
                    'transaction_id' => $responseArray['tracking_id'] ?? $order->order_no,
                    'pg_result' => $orderStatus,
                    'track_id' => $responseArray['tracking_id'] ?? null,
                    'pg_response_json' => json_encode($responseArray),
                    'payment_date' => $isSuccess ? $transDate : null,
                    'currency' => $order->registration->nationality === 'International' ? 'USD' : 'INR',
                    'status' => $paymentTableStatus,
                    'order_id' => $order->order_no, // Store TIN/order_no in order_id field
                ]);

                // Update order status and invoice
                if ($isSuccess) {
                    $order->update(['status' => 'paid']);

                    // Update invoice - mark as paid
                    $paidAmount = $responseArray['mer_amount'] ?? $order->total;
                    $invoice->update([
                        'amount_paid' => $paidAmount,
                        'pending_amount' => max(0, ($invoice->total_final_price ?? $paidAmount) - $paidAmount),
                        'payment_status' => 'paid', // Mark invoice as paid
                    ]);

                    // Track payment completed
                    $tracking = TicketRegistrationTracking::where('order_id', $order->id)->first();
                    if ($tracking) {
                        $tracking->updateStatus('payment_completed', [
                            'final_total' => $paidAmount,
                        ]);
                    }

                    // Send payment acknowledgement email (payment successful)
                    try {
                        $contactEmail = $order->registration->contact->email ?? null;
                        if ($contactEmail) {
                            $adminEmails = config('constants.ADMIN_EMAILS', []);
                            $mail = Mail::to($contactEmail);
                            if (!empty($adminEmails)) {
                                $mail->bcc($adminEmails);
                            }
                            // Pass true to indicate payment is successful
                            $mail->send(new TicketRegistrationMail($order, $event, true));
                        }
                    } catch (\Exception $e) {
                        Log::error('Failed to send ticket payment acknowledgement email', [
                            'order_id' => $order->id,
                            'error' => $e->getMessage()
                        ]);
                    }

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
                    // Payment failed - ensure invoice remains unpaid
                    // Invoice should already be 'unpaid', but ensure it stays that way
                    if ($invoice && $invoice->payment_status !== 'unpaid') {
                        $invoice->update([
                            'payment_status' => 'unpaid', // Ensure invoice remains unpaid on failure
                        ]);
                    }

                    // Track payment failed
                    $tracking = TicketRegistrationTracking::where('order_id', $order->id)->first();
                    if ($tracking) {
                        $tracking->updateStatus('payment_failed', [
                            'dropoff_stage' => 'payment',
                            'dropoff_reason' => $responseArray['failure_message'] ?? 'Payment failed',
                        ]);
                    }

                    // Payment failed
                    $failureMessage = $responseArray['failure_message'] ?? 'Payment failed. Please try again.';
                    return redirect()->route('tickets.payment.by-tin', [
                        'eventSlug' => $event->slug ?? $event->id,
                        'tin' => $order->order_no
                    ])->with('error', $failureMessage);
                }
            } catch (\Exception $e) {
                Log::error('Payment callback error: ' . $e->getMessage(), [
                    'order_id' => $order->id,
                    'token' => $order->secure_token,
                    'trace' => $e->getTraceAsString()
                ]);
                
                return redirect()->route('tickets.payment.by-tin', [
                    'eventSlug' => $event->slug ?? $event->id,
                    'tin' => $order->order_no
                ])->with('error', 'Error processing payment response. Please contact support.');
            }
        }

        return redirect()->route('tickets.payment.by-tin', [
            'eventSlug' => $event->slug ?? $event->id,
            'tin' => $order->order_no
        ])->with('error', 'Invalid payment response.');
    }

    /**
     * Show confirmation page
     */
    public function confirmation($eventSlug, $token)
    {
        $event = Events::where('slug', $eventSlug)->orWhere('id', $eventSlug)->firstOrFail();
        $order = TicketOrder::with(['registration.contact', 'items.ticketType', 'registration.registrationCategory', 'registration.delegates'])
            ->where('secure_token', $token)
            ->whereHas('registration', function($q) use ($event) {
                $q->where('event_id', $event->id);
            })
            ->firstOrFail();

        return view('tickets.public.confirmation', compact('event', 'order'));
    }

    /**
     * Process payment - Initiate payment gateway and redirect
     */
    public function process(Request $request, $token)
    {
        $order = TicketOrder::where('secure_token', $token)
            ->with(['registration.event', 'registration.contact', 'items.ticketType'])
            ->firstOrFail();
        
        // Only allow processing if order is pending
        if ($order->status !== 'pending') {
            return redirect()->route('tickets.payment', $order->secure_token)
                ->with('error', 'This order has already been processed.');
        }

        try {
            $event = $order->registration->event;
            $registration = $order->registration;
            
            // Determine currency based on nationality
            $isInternational = ($registration->nationality === 'International' || $registration->nationality === 'international');
            $currency = $isInternational ? 'USD' : 'INR';
            
            // Prepare payment gateway data
            $billingName = $registration->contact->name ?? '';
            $billingEmail = $registration->contact->email ?? '';
            $billingPhone = $registration->contact->phone ?? $registration->company_phone;
            
            $amount = $order->total;
            // If international, amount is already in USD (stored in order), no conversion needed
            // The order total is already in the correct currency
            
            $paymentData = [
                'order_id' => $order->order_no . '_' . time(),
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => $currency,
                'redirect_url' => route('tickets.payment.callback', $order->secure_token),
                'cancel_url' => route('tickets.payment', $order->secure_token),
                'billing_name' => $billingName,
                'billing_address' => $registration->company_name,
                'billing_city' => $registration->company_city ?? '',
                'billing_state' => $registration->company_state ?? '',
                'billing_zip' => '',
                'billing_country' => $registration->company_country,
                'billing_tel' => $billingPhone,
                'billing_email' => $billingEmail,
            ];

            // Initiate payment gateway
            $result = $this->ccAvenueService->initiateTransaction($paymentData);

            if ($result['success']) {
                // Store payment gateway order ID in session for callback
                session(['payment_order_id' => $paymentData['order_id'], 'ticket_order_id' => $order->id]);
                
                Log::info('Ticket Payment - Showing payment form', [
                    'order_id' => $order->id,
                    'payment_order_id' => $paymentData['order_id'],
                ]);
                
                // Return view with form that auto-submits to CCAvenue (same as PaymentGatewayController)
                return view('pgway.ccavenue', [
                    'encryptedData' => $result['encrypted_data'],
                    'access_code' => $result['access_code']
                ]);
            } else {
                $errorMessage = $result['error'] ?? $result['message'] ?? 'Unknown error';
                Log::error('Ticket Payment - Gateway initiation failed', [
                    'order_id' => $order->id,
                    'order_no' => $order->order_no,
                    'error' => $errorMessage,
                    'result' => $result,
                    'payment_data' => $paymentData,
                ]);
                
                return redirect()->route('tickets.payment', $order->secure_token)
                    ->with('error', 'Failed to initiate payment: ' . $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('Ticket payment process error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'token' => $order->secure_token,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('tickets.payment', $order->secure_token)
                ->with('error', 'An error occurred while processing your payment. Please try again.');
        }
    }

    /**
     * Payment webhook
     */
    public function webhook(Request $request)
    {
        // TODO: Implement webhook handling for payment gateway callbacks
        return response()->json(['status' => 'ok']);
    }

    /**
     * Initiate payment by TIN (order number) - for direct access via email link
     */
    public function initiateByTin($eventSlug, $tin)
    {
        $event = Events::where('slug', $eventSlug)->orWhere('id', $eventSlug)->firstOrFail();
        
        // Find order by TIN (order_no)
        $order = TicketOrder::where('order_no', $tin)
            ->whereHas('registration', function($q) use ($event) {
                $q->where('event_id', $event->id);
            })
            ->with(['registration.contact', 'items.ticketType', 'registration.delegates', 'registration.registrationCategory'])
            ->firstOrFail();
        
        // Only allow access if order is pending
        if ($order->status !== 'pending') {
            return redirect()->route('tickets.payment', $order->id)
                ->with('error', 'This order has already been processed.');
        }
        
        // Load related data
        $ticketType = $order->items->first()->ticketType ?? null;
        $registrationCategory = $order->registration->registrationCategory;
        
        // Show payment page
        return view('tickets.public.payment', compact('event', 'order', 'ticketType', 'registrationCategory'));
    }

    /**
     * Generate unique order number using TICKET_ORDER_PREFIX
     * Format: TIN-BTS-2026-TKT-XXXXXX (6-digit random number)
     * Ensures no duplicates by checking database
     */
    private function generateUniqueOrderNumber()
    {
        $prefix = config('constants.TICKET_ORDER_PREFIX');
        $maxAttempts = 100; // Prevent infinite loop
        $attempts = 0;
        
        while ($attempts < $maxAttempts) {
            // Generate 6-digit random number
            $randomNumber = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $orderNo = $prefix . $randomNumber;
            $attempts++;
            
            // Check if it already exists in ticket_orders table
            $exists = TicketOrder::where('order_no', $orderNo)->exists();
            
            if (!$exists) {
                return $orderNo;
            }
        }
        
        // If we've tried too many times, use timestamp-based fallback
        $timestamp = substr(time(), -6); // Last 6 digits of timestamp
        $orderNo = $prefix . $timestamp;
        if (!TicketOrder::where('order_no', $orderNo)->exists()) {
            return $orderNo;
        }
        
        // Last resort: use microtime
        $microtime = substr(str_replace('.', '', microtime(true)), -6);
        return $prefix . $microtime;
    }
    
    /**
     * Format phone number: Remove spaces and add dash after country code
     * Example: +91 8619276031 -> +91-8619276031
     * Example: +918619276031 -> +91-8619276031
     */
    private function formatPhoneNumber($phone)
    {
        if (empty($phone)) {
            return $phone;
        }
        
        // Remove all spaces
        $phone = str_replace(' ', '', trim($phone));
        
        // If phone starts with +, add dash after country code (2-3 digits)
        if (preg_match('/^(\+\d{1,3})(\d+)$/', $phone, $matches)) {
            return $matches[1] . '-' . $matches[2];
        }
        
        return $phone;
    }
}

