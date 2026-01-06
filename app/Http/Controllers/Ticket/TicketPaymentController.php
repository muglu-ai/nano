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
use App\Services\CcAvenueService;
use App\Mail\TicketRegistrationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        try {
            DB::beginTransaction();

            // Load ticket type
            $ticketType = TicketType::where('id', $registrationData['ticket_type_id'])
                ->where('event_id', $event->id)
                ->with(['category', 'subcategory'])
                ->firstOrFail();

            // Calculate pricing
            $quantity = $registrationData['delegate_count'];
            $unitPrice = $ticketType->getCurrentPrice();
            $subtotal = $unitPrice * $quantity;
            
            $gstRate = config('constants.GST_RATE', 18);
            $isIndian = strtolower($registrationData['country']) === 'india' || $registrationData['nationality'] === 'Indian';
            $processingChargeRate = $isIndian 
                ? config('constants.IND_PROCESSING_CHARGE', 3) 
                : config('constants.INT_PROCESSING_CHARGE', 9);
            
            $gstAmount = ($subtotal * $gstRate) / 100;
            $processingChargeAmount = (($subtotal + $gstAmount) * $processingChargeRate) / 100;
            $total = $subtotal + $gstAmount + $processingChargeAmount;

            // Create or get contact (use first delegate email if contact email not provided)
            $contactEmail = $registrationData['contact_email'] ?? ($registrationData['delegates'][0]['email'] ?? null);
            $contactName = $registrationData['contact_name'] ?? ($registrationData['delegates'][0]['first_name'] . ' ' . ($registrationData['delegates'][0]['last_name'] ?? ''));
            $contactPhone = $registrationData['contact_phone'] ?? ($registrationData['delegates'][0]['phone'] ?? null);
            
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
                            'phone' => $firstDelegate['phone'] ?? null,
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
                'company_country' => $registrationData['country'],
                'company_state' => $registrationData['state'] ?? null,
                'company_city' => $registrationData['city'] ?? null,
                'company_phone' => $registrationData['phone'],
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
                        'phone' => $delegateData['phone'] ?? null,
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
            
            // Clear session data after order creation
            session()->forget('ticket_registration_data');

            // Load registration category for display
            $registrationCategory = TicketRegistrationCategory::find($registration->registration_category_id);
            
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

            // Show payment page with all order details
            return view('tickets.public.payment', compact('event', 'order', 'ticketType', 'registrationCategory', 'registrationData'));

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
    public function show($orderId)
    {
        $order = TicketOrder::with(['registration.event', 'items.ticketType'])->findOrFail($orderId);
        
        return view('tickets.payment.show', compact('order'));
    }

    /**
     * Payment callback from gateway
     */
    public function callback(Request $request, $orderId)
    {
        $order = TicketOrder::with(['registration.event'])->findOrFail($orderId);
        
        // Handle CCAvenue response
        $encResponse = $request->input('encResp');
        
        if ($encResponse) {
            try {
                $credentials = $this->ccAvenueService->getCredentials();
                $decryptedResponse = $this->ccAvenueService->decrypt($encResponse, $credentials['working_key']);
                parse_str($decryptedResponse, $responseArray);

                // Update payment status based on response
                if (isset($responseArray['order_status']) && $responseArray['order_status'] === 'Success') {
                    // Payment successful
                    $order->update(['status' => 'paid']);
                    
                    // TODO: Create payment record, send receipt email, etc.
                    
                    return redirect()->route('tickets.confirmation', [
                        'eventSlug' => $order->registration->event->slug ?? $order->registration->event->id,
                        'orderId' => $order->id
                    ])->with('success', 'Payment successful!');
                } else {
                    // Payment failed
                    $failureMessage = $responseArray['failure_message'] ?? 'Payment failed. Please try again.';
                    return redirect()->route('tickets.payment', $order->id)
                        ->with('error', $failureMessage);
                }
            } catch (\Exception $e) {
                Log::error('Payment callback error: ' . $e->getMessage(), [
                    'order_id' => $order->id,
                    'trace' => $e->getTraceAsString()
                ]);
                
                return redirect()->route('tickets.payment', $order->id)
                    ->with('error', 'Error processing payment response. Please contact support.');
            }
        }

        return redirect()->route('tickets.payment', $order->id)
            ->with('error', 'Invalid payment response.');
    }

    /**
     * Show confirmation page
     */
    public function confirmation($eventSlug, $orderId)
    {
        $event = Events::where('slug', $eventSlug)->orWhere('id', $eventSlug)->firstOrFail();
        $order = TicketOrder::with(['registration.contact', 'items.ticketType', 'registration.registrationCategory'])
            ->where('id', $orderId)
            ->whereHas('registration', function($q) use ($event) {
                $q->where('event_id', $event->id);
            })
            ->firstOrFail();

        return view('tickets.public.confirmation', compact('event', 'order'));
    }

    /**
     * Process payment - Initiate payment gateway and redirect
     */
    public function process(Request $request, $orderId)
    {
        $order = TicketOrder::with(['registration.event', 'registration.contact', 'items.ticketType'])->findOrFail($orderId);
        
        // Only allow processing if order is pending
        if ($order->status !== 'pending') {
            return redirect()->route('tickets.payment', $order->id)
                ->with('error', 'This order has already been processed.');
        }

        try {
            $event = $order->registration->event;
            $registration = $order->registration;
            
            // Prepare payment gateway data
            $billingName = $registration->contact->name ?? '';
            $billingEmail = $registration->contact->email ?? '';
            $billingPhone = $registration->contact->phone ?? $registration->company_phone;
            
            $paymentData = [
                'order_id' => $order->order_no . '_' . time(),
                'amount' => number_format($order->total, 2, '.', ''),
                'currency' => 'INR',
                'redirect_url' => route('tickets.payment.callback', $order->id),
                'cancel_url' => route('tickets.payment', $order->id),
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
                
                Log::info('Ticket Payment - Redirecting to gateway', [
                    'order_id' => $order->id,
                    'payment_order_id' => $paymentData['order_id'],
                ]);
                
                // Redirect to payment gateway
                return redirect($result['payment_url']);
            } else {
                $errorMessage = $result['error'] ?? $result['message'] ?? 'Unknown error';
                Log::error('Ticket Payment - Gateway initiation failed', [
                    'order_id' => $order->id,
                    'error' => $errorMessage,
                ]);
                
                return redirect()->route('tickets.payment', $order->id)
                    ->with('error', 'Failed to initiate payment: ' . $errorMessage);
            }

        } catch (\Exception $e) {
            Log::error('Ticket payment process error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('tickets.payment', $order->id)
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
}

