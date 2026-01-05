<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Models\Ticket\TicketType;
use App\Models\Ticket\TicketCategory;
use App\Models\Ticket\TicketEventConfig;
use App\Models\Ticket\EventDay;
use App\Models\Ticket\TicketRegistrationCategory;
use App\Models\GstLookup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PublicTicketController extends Controller
{
    /**
     * Show ticket discovery page
     */
    public function discover($eventSlug)
    {
        $event = Events::where('slug', $eventSlug)->orWhere('id', $eventSlug)->firstOrFail();
        
        // Check if ticket system is active
        $config = TicketEventConfig::where('event_id', $event->id)->first();
        if (!$config || !$config->is_active) {
            abort(404, 'Ticket registration is not available for this event.');
        }
        
        // Load ticket types with relationships
        $ticketTypes = TicketType::where('event_id', $event->id)
            ->where('is_active', true)
            ->with(['category', 'subcategory', 'eventDays', 'inventory'])
            ->orderBy('sort_order')
            ->get();
        
        // Group by category
        $categories = TicketCategory::where('event_id', $event->id)
            ->with(['ticketTypes' => function($query) {
                $query->where('is_active', true)
                      ->with(['subcategory', 'eventDays', 'inventory'])
                      ->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();
        
        // Get event days for entitlements
        $eventDays = EventDay::where('event_id', $event->id)
            ->orderBy('sort_order')
            ->orderBy('date')
            ->get();
        
        return view('tickets.public.discover', compact('event', 'ticketTypes', 'categories', 'eventDays', 'config'));
    }

    /**
     * Show registration form
     */
    public function register($eventSlug)
    {
        $event = Events::where('slug', $eventSlug)->orWhere('id', $eventSlug)->firstOrFail();
        
        // Check if ticket system is active
        $config = TicketEventConfig::where('event_id', $event->id)->first();
        if (!$config || !$config->is_active) {
            abort(404, 'Ticket registration is not available for this event.');
        }
        
        // Get selected ticket type from query parameter
        $selectedTicketTypeId = request()->query('ticket');
        
        // Load ticket types
        $ticketTypes = TicketType::where('event_id', $event->id)
            ->where('is_active', true)
            ->with(['category', 'subcategory', 'eventDays', 'inventory'])
            ->orderBy('sort_order')
            ->get();
        
        // Load registration categories
        $registrationCategories = TicketRegistrationCategory::where('event_id', $event->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
        
        // Load event days
        $eventDays = EventDay::where('event_id', $event->id)
            ->orderBy('sort_order')
            ->orderBy('date')
            ->get();
        
        // Get selected ticket type if provided
        $selectedTicketType = $selectedTicketTypeId 
            ? $ticketTypes->find($selectedTicketTypeId) 
            : null;
        
        return view('tickets.public.register', compact(
            'event', 
            'config', 
            'ticketTypes', 
            'registrationCategories', 
            'eventDays', 
            'selectedTicketType'
        ));
    }

    /**
     * Store registration form data and redirect to preview
     */
    public function store(Request $request, $eventSlug)
    {
        $event = Events::where('slug', $eventSlug)->orWhere('id', $eventSlug)->firstOrFail();
        
        // Verify reCAPTCHA if enabled
        if (config('constants.RECAPTCHA_ENABLED', false)) {
            $recaptchaResponse = $request->input('g-recaptcha-response');
            if (!$this->verifyRecaptcha($recaptchaResponse)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['recaptcha' => 'reCAPTCHA verification failed. Please try again.']);
            }
        }
        
        // Validate the request
        $validated = $request->validate([
            'registration_category_id' => 'required|exists:ticket_registration_categories,id',
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'delegate_count' => 'required|integer|min:1|max:100',
            'nationality' => 'required|in:Indian,International',
            'organisation_name' => 'required|string|max:255',
            'industry_sector' => 'required|string|max:255',
            'organisation_type' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'gst_required' => 'required|in:0,1',
            'gstin' => 'nullable|string|max:15|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
            'gst_legal_name' => 'nullable|string|max:255',
            'gst_address' => 'nullable|string',
            'gst_state' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'delegates' => 'required|array|min:1',
            'delegates.*.first_name' => 'required|string|max:255',
            'delegates.*.last_name' => 'required|string|max:255',
            'delegates.*.email' => 'required|email|max:255',
            'delegates.*.phone' => 'nullable|string|max:20',
            'delegates.*.salutation' => 'nullable|string|max:10',
            'delegates.*.job_title' => 'nullable|string|max:255',
        ], [
            'registration_category_id.required' => 'Please select a registration category.',
            'ticket_type_id.required' => 'Please select a ticket type.',
            'industry_sector.required' => 'Please select an industry sector.',
            'organisation_type.required' => 'Please select an organisation type.',
            'gst_required.required' => 'Please specify if GST is required.',
            'gstin.regex' => 'Invalid GSTIN format. Please enter a valid 15-digit GSTIN.',
            'delegates.required' => 'Please provide delegate information.',
            'delegates.min' => 'At least one delegate is required.',
            'delegates.*.first_name.required' => 'First name is required for all delegates.',
            'delegates.*.last_name.required' => 'Last name is required for all delegates.',
            'delegates.*.email.required' => 'Email is required for all delegates.',
            'delegates.*.email.email' => 'Please enter a valid email address for all delegates.',
        ]);

        // Validate delegate count matches delegates array
        $delegateCount = $validated['delegate_count'];
        $delegates = $request->input('delegates', []);
        
        // Log for debugging
        Log::info('Ticket Registration - Delegate Validation', [
            'delegate_count' => $delegateCount,
            'delegates_received' => count($delegates),
            'delegates_data' => $delegates,
            'all_request_data' => $request->except(['_token', 'g-recaptcha-response']),
        ]);
        
        // Filter out empty delegate entries (in case form has empty fields)
        $delegates = array_filter($delegates, function($delegate) {
            return !empty($delegate['first_name']) || !empty($delegate['last_name']) || !empty($delegate['email']);
        });
        
        // Log after filtering
        Log::info('Ticket Registration - After Filtering', [
            'delegates_count_after_filter' => count($delegates),
            'delegates_after_filter' => $delegates,
        ]);
        
        // Always validate delegates (even for count = 1)
        if (count($delegates) !== $delegateCount) {
            Log::warning('Ticket Registration - Delegate Count Mismatch', [
                'expected_count' => $delegateCount,
                'received_count' => count($delegates),
                'delegates_data' => $delegates,
            ]);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['delegate_count' => 'Please provide information for all ' . $delegateCount . ' delegate(s).']);
        }
        
        // Validate all delegate emails are unique
        $emails = array_column($delegates, 'email');
        if (count($emails) !== count(array_unique($emails))) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['delegates' => 'Each delegate must have a unique email address.']);
        }

        // If GST is required, validate GST fields and primary contact
        if ($validated['gst_required'] == '1') {
            $request->validate([
                'gstin' => 'required|string|max:15|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
                'gst_legal_name' => 'required|string|max:255',
                'gst_address' => 'required|string',
                'gst_state' => 'required|string|max:255',
                'contact_name' => 'required|string|max:255',
                'contact_email' => 'required|email|max:255',
                'contact_phone' => 'required|string|max:20',
            ], [
                'gstin.required' => 'GSTIN is required when GST is applicable.',
                'gst_legal_name.required' => 'GST legal name is required.',
                'gst_address.required' => 'GST address is required.',
                'gst_state.required' => 'GST state is required.',
                'contact_name.required' => 'Primary contact name is required for GST invoice.',
                'contact_email.required' => 'Primary contact email is required for GST invoice.',
                'contact_phone.required' => 'Primary contact phone is required for GST invoice.',
            ]);
        }

        // Verify ticket type belongs to this event
        $ticketType = TicketType::where('id', $validated['ticket_type_id'])
            ->where('event_id', $event->id)
            ->where('is_active', true)
            ->firstOrFail();

        // Store form data in session for preview (including delegates)
        $registrationData = array_merge($validated, [
            'event_id' => $event->id,
            'event_slug' => $event->slug ?? $event->id,
            'delegates' => $delegates, // Store delegates array
        ]);
        
        session(['ticket_registration_data' => $registrationData]);

        // Redirect to preview page
        return redirect()->route('tickets.preview', $event->slug ?? $event->id);
    }

    /**
     * Show preview page with price calculation
     */
    public function preview($eventSlug)
    {
        $event = Events::where('slug', $eventSlug)->orWhere('id', $eventSlug)->firstOrFail();
        
        // Get registration data from session
        $registrationData = session('ticket_registration_data');
        
        if (!$registrationData || $registrationData['event_id'] != $event->id) {
            return redirect()->route('tickets.register', $event->slug ?? $event->id)
                ->with('error', 'Please complete the registration form first.');
        }

        // Load ticket type
        $ticketType = TicketType::where('id', $registrationData['ticket_type_id'])
            ->where('event_id', $event->id)
            ->with(['category', 'subcategory', 'eventDays'])
            ->firstOrFail();

        // Calculate pricing
        $quantity = $registrationData['delegate_count'];
        $unitPrice = $ticketType->getCurrentPrice();
        $subtotal = $unitPrice * $quantity;
        
        // Get GST rate (default 18%)
        $gstRate = config('constants.GST_RATE', 18);
        
        // Get processing charge rate (3% for India, 9% for International)
        $isIndian = strtolower($registrationData['country']) === 'india' || $registrationData['nationality'] === 'Indian';
        $processingChargeRate = $isIndian 
            ? config('constants.IND_PROCESSING_CHARGE', 3) 
            : config('constants.INT_PROCESSING_CHARGE', 9);
        
        // Calculate GST on subtotal
        $gstAmount = ($subtotal * $gstRate) / 100;
        
        // Calculate processing charge on (subtotal + GST)
        $processingChargeAmount = (($subtotal + $gstAmount) * $processingChargeRate) / 100;
        
        // Total
        $total = $subtotal + $gstAmount + $processingChargeAmount;

        // Load registration category
        $registrationCategory = TicketRegistrationCategory::find($registrationData['registration_category_id']);

        return view('tickets.public.preview', compact(
            'event',
            'registrationData',
            'ticketType',
            'registrationCategory',
            'quantity',
            'unitPrice',
            'subtotal',
            'gstRate',
            'gstAmount',
            'processingChargeRate',
            'processingChargeAmount',
            'total'
        ));
    }

    /**
     * Validate GST number via API
     */
    public function validateGst(Request $request)
    {
        $request->validate([
            'gstin' => 'required|string|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
        ]);

        $gstin = strtoupper($request->gstin);
        
        try {
            $gst = GstLookup::findOrFetch($gstin);
            
            if ($gst) {
                return response()->json([
                    'success' => true,
                    'gst' => [
                        'company_name' => $gst->company_name,
                        'billing_address' => $gst->billing_address,
                        'state_name' => $gst->state_name,
                        'state_code' => $gst->state_code,
                        'pincode' => $gst->pincode,
                        'city' => $gst->city,
                        'pan' => $gst->pan,
                    ]
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'GST number not found or invalid.'
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error validating GST: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify Google reCAPTCHA Enterprise v3 response
     */
    private function verifyRecaptcha($recaptchaResponse)
    {
        // If disabled via config, always pass
        if (!config('constants.RECAPTCHA_ENABLED', false)) {
            return true;
        }

        $siteKey = config('services.recaptcha.site_key');
        $projectId = config('services.recaptcha.project_id');
        $apiKey = config('services.recaptcha.api_key');
        $expectedAction = 'submit';

        if (empty($siteKey) || empty($projectId) || empty($apiKey) || empty($recaptchaResponse)) {
            Log::warning('reCAPTCHA config or token missing', [
                'siteKey' => !empty($siteKey),
                'projectId' => !empty($projectId),
                'hasToken' => !empty($recaptchaResponse),
            ]);
            return false;
        }

        $url = sprintf(
            'https://recaptchaenterprise.googleapis.com/v1/projects/%s/assessments?key=%s',
            $projectId,
            $apiKey
        );

        try {
            $response = Http::post($url, [
                'event' => [
                    'token' => $recaptchaResponse,
                    'expectedAction' => $expectedAction,
                    'siteKey' => $siteKey,
                ],
            ]);

            $result = $response->json();

            if (!$response->successful()) {
                Log::warning('reCAPTCHA Enterprise API error', [
                    'status' => $response->status(),
                    'response' => $result,
                ]);
                return false;
            }

            $tokenProps = $result['tokenProperties'] ?? null;

            if (
                !$tokenProps ||
                ($tokenProps['valid'] ?? false) !== true ||
                ($tokenProps['action'] ?? null) !== $expectedAction
            ) {
                Log::warning('reCAPTCHA Enterprise token invalid', [
                    'tokenProperties' => $tokenProps,
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('reCAPTCHA verification error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Continue registration with token
     */
    public function continueRegistration($eventSlug, $token)
    {
        // TODO: Implement magic link continuation
        return redirect()->back()->with('error', 'Feature not yet implemented');
    }
}

