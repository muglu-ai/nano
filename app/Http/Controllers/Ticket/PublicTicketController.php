<?php

namespace App\Http\Controllers\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Events;
use App\Models\Ticket\TicketType;
use App\Models\Ticket\TicketCategory;
use App\Models\Ticket\TicketEventConfig;
use App\Models\Ticket\EventDay;
use App\Models\Ticket\TicketRegistrationCategory;
use App\Models\Ticket\TicketRegistrationTracking;
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
        
        // Get selected ticket type from query parameter (can be slug or ID for backward compatibility)
        $selectedTicketParam = request()->query('ticket');
        $selectedNationality = request()->query('nationality'); // Get nationality from URL
        
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
        
        // Get selected ticket type if provided (try slug first, then ID for backward compatibility)
        $selectedTicketType = null;
        if ($selectedTicketParam) {
            // Try to find by slug first
            $selectedTicketType = $ticketTypes->firstWhere('slug', $selectedTicketParam);
            // If not found by slug, try ID (for backward compatibility)
            if (!$selectedTicketType && is_numeric($selectedTicketParam)) {
                $selectedTicketType = $ticketTypes->find($selectedTicketParam);
            }
        }
        
        // Determine if fields should be disabled (when passed via URL)
        $isTicketTypeLocked = !empty($selectedTicketParam) && $selectedTicketType !== null;
        $isNationalityLocked = !empty($selectedNationality) && in_array($selectedNationality, ['national', 'international']);
        
        // Get sectors and organization types from config
        $sectors = config('constants.sectors', []);
        $organizationTypes = config('constants.organization_types', []);
        
        // Track registration started
        $trackingToken = session('ticket_registration_tracking_token');
        $tracking = null;
        
        if (!$trackingToken) {
            // Create new tracking record
            $trackingToken = TicketRegistrationTracking::generateTrackingToken();
            session(['ticket_registration_tracking_token' => $trackingToken]);
            
            $tracking = TicketRegistrationTracking::create([
                'event_id' => $event->id,
                'tracking_token' => $trackingToken,
                'session_id' => session()->getId(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'status' => 'started',
                'started_at' => now(),
            ]);
        } else {
            // Update existing tracking
            $tracking = TicketRegistrationTracking::where('tracking_token', $trackingToken)
                ->where('event_id', $event->id)
                ->first();
            
            if ($tracking && $tracking->status === 'abandoned') {
                // User returned after abandonment
                $tracking->updateStatus('started');
            }
        }

        // If user is coming back from preview (edit flow), load session data into old() helper
        // The old() helper reads from flashed session data, so we need to flash it
        $registrationData = session('ticket_registration_data');
        if ($registrationData && $registrationData['event_id'] == $event->id) {
            // Normalize nationality back to form values (form uses 'national'/'international', but session stores 'Indian'/'International')
            if (isset($registrationData['nationality'])) {
                if ($registrationData['nationality'] === 'Indian') {
                    $registrationData['nationality'] = 'national';
                } elseif ($registrationData['nationality'] === 'International') {
                    $registrationData['nationality'] = 'international';
                }
            }
            
            // Flash the session data so old() helper can access it
            // This preserves all form values when user clicks "Edit Registration"
            request()->session()->flashInput($registrationData);
        }
        
        return view('tickets.public.register', compact(
            'event', 
            'config', 
            'ticketTypes', 
            'registrationCategories', 
            'eventDays', 
            'selectedTicketType',
            'selectedNationality',
            'isTicketTypeLocked',
            'isNationalityLocked',
            'sectors',
            'organizationTypes'
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
            'registration_category_id' => 'nullable|exists:ticket_registration_categories,id',
            'ticket_type_id' => [
                'required',
                function ($attribute, $value, $fail) use ($event) {
                    // Check if ticket type exists by slug or ID, and belongs to this event
                    $ticketType = TicketType::where('event_id', $event->id)
                        ->where(function($query) use ($value) {
                            $query->where('slug', $value)
                                  ->orWhere('id', $value);
                        })
                        ->where('is_active', true)
                        ->first();
                    
                    if (!$ticketType) {
                        $fail('The selected ticket type is invalid.');
                    }
                },
            ],
            'delegate_count' => 'required|integer|min:1|max:100',
            'nationality' => 'required|in:national,international,Indian,International',
            'organisation_name' => 'required|string|max:255',
            'industry_sector' => 'required|string|max:255',
            'organisation_type' => 'required|string|max:255',
            'company_country' => 'required|string|max:255',
            'company_state' => 'nullable|string|max:255',
            'company_city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
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
        $delegateCount = (int) $validated['delegate_count']; // Cast to integer for comparison
        $delegates = $request->input('delegates', []);
        
        // Log for debugging
        Log::info('Ticket Registration - Delegate Validation', [
            'delegate_count' => $delegateCount,
            'delegate_count_type' => gettype($delegateCount),
            'delegates_received' => count($delegates),
            'delegates_data' => $delegates,
            'all_request_data' => $request->except(['_token', 'g-recaptcha-response']),
        ]);
        
        // Filter out empty delegate entries (in case form has empty fields)
        $delegates = array_filter($delegates, function($delegate) {
            return !empty($delegate['first_name']) || !empty($delegate['last_name']) || !empty($delegate['email']);
        });
        
        // Re-index array to ensure sequential keys (0, 1, 2, ...)
        $delegates = array_values($delegates);
        
        // Log after filtering
        Log::info('Ticket Registration - After Filtering', [
            'delegates_count_after_filter' => count($delegates),
            'delegates_after_filter' => $delegates,
        ]);
        
        // Always validate delegates (even for count = 1)
        $delegatesCount = count($delegates);
        if ($delegatesCount !== $delegateCount) {
            Log::warning('Ticket Registration - Delegate Count Mismatch', [
                'expected_count' => $delegateCount,
                'expected_count_type' => gettype($delegateCount),
                'received_count' => $delegatesCount,
                'received_count_type' => gettype($delegatesCount),
                'delegates_data' => $delegates,
            ]);
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['delegate_count' => 'Please provide information for all ' . $delegateCount . ' delegate(s).']);
        }
        
        Log::info('Ticket Registration - Delegate Validation Passed', [
            'delegate_count' => $delegateCount,
            'delegates_count' => $delegatesCount,
        ]);
        
        // Validate all delegate emails are unique within the registration
        $emails = array_column($delegates, 'email');
        if (count($emails) !== count(array_unique($emails))) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['delegates' => 'Each delegate must have a unique email address.']);
        }
        
        // Collect all emails to check (organization, contact, and all delegates)
        $allEmailsToCheck = [];
        
        // Add organization email if provided
        if (!empty($validated['email'])) {
            $allEmailsToCheck[] = $validated['email'];
        }
        
        // Add contact email if GST is required
        if (!empty($validated['gst_required']) && $validated['gst_required'] == '1' && !empty($validated['contact_email'])) {
            $allEmailsToCheck[] = $validated['contact_email'];
        }
        
        // Add all delegate emails
        foreach ($delegates as $delegate) {
            if (!empty($delegate['email'])) {
                $allEmailsToCheck[] = $delegate['email'];
            }
        }
        
        // Check if any email already exists in ticket_delegates table for this event
        // Same email cannot be used for multiple ticket registrations/categories
        foreach ($allEmailsToCheck as $email) {
            $existingDelegate = \App\Models\Ticket\TicketDelegate::where('email', $email)
                ->whereHas('registration', function($query) use ($event) {
                    $query->where('event_id', $event->id);
                })
                ->exists();
            
            if ($existingDelegate) {
                // Determine which field to show error for
                $errorField = 'delegates';
                $errorMessage = "The email address '{$email}' has already been used for ticket registration. Each email can only be used once per event.";
                
                if ($email === ($validated['email'] ?? null)) {
                    $errorField = 'email';
                } elseif ($email === ($validated['contact_email'] ?? null)) {
                    $errorField = 'contact_email';
                }
                
                return redirect()->back()
                    ->withInput()
                    ->withErrors([$errorField => $errorMessage]);
            }
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

        // Verify ticket type belongs to this event (can be slug or ID)
        $ticketType = TicketType::where('event_id', $event->id)
            ->where(function($query) use ($validated) {
                $query->where('slug', $validated['ticket_type_id'])
                      ->orWhere('id', $validated['ticket_type_id']);
            })
            ->where('is_active', true)
            ->firstOrFail();
        
        // Store ticket type ID (not slug) in validated data for consistency
        $validated['ticket_type_id'] = $ticketType->id;
        
        // Normalize nationality value (convert 'national'/'international' to 'Indian'/'International')
        if (isset($validated['nationality'])) {
            if ($validated['nationality'] === 'national') {
                $validated['nationality'] = 'Indian';
            } elseif ($validated['nationality'] === 'international') {
                $validated['nationality'] = 'International';
            }
        }
        
        // Auto-set registration category if not provided
        // Try to get from ticket rules first, otherwise use default (first active category)
        if (empty($validated['registration_category_id'])) {
            // Try to find registration category from ticket rules
            $ticketRule = \App\Models\Ticket\TicketCategoryTicketRule::where('ticket_type_id', $ticketType->id)
                ->whereHas('registrationCategory', function($q) use ($event) {
                    $q->where('event_id', $event->id)->where('is_active', true);
                })
                ->with('registrationCategory')
                ->first();
            
            if ($ticketRule && $ticketRule->registrationCategory) {
                $validated['registration_category_id'] = $ticketRule->registrationCategory->id;
            } else {
                // Use default: first active registration category for this event
                $defaultCategory = TicketRegistrationCategory::where('event_id', $event->id)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->first();
                
                if ($defaultCategory) {
                    $validated['registration_category_id'] = $defaultCategory->id;
                }
            }
        }
        
        // Auto-set registration category if not provided
        // Try to get from ticket rules first, otherwise use default (first active category)
        if (empty($validated['registration_category_id'])) {
            // Try to find registration category from ticket rules
            $ticketRule = \App\Models\Ticket\TicketCategoryTicketRule::where('ticket_type_id', $ticketType->id)
                ->whereHas('registrationCategory', function($q) use ($event) {
                    $q->where('event_id', $event->id)->where('is_active', true);
                })
                ->with('registrationCategory')
                ->first();
            
            if ($ticketRule && $ticketRule->registrationCategory) {
                $validated['registration_category_id'] = $ticketRule->registrationCategory->id;
            } else {
                // Use default: first active registration category for this event
                $defaultCategory = TicketRegistrationCategory::where('event_id', $event->id)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->first();
                
                if ($defaultCategory) {
                    $validated['registration_category_id'] = $defaultCategory->id;
                }
            }
        }

        // Format phone numbers: Remove spaces and add dash after country code (e.g., +91-8619276031)
        if (isset($validated['phone'])) {
            $validated['phone'] = $this->formatPhoneNumber($validated['phone']);
        }
        if (isset($validated['contact_phone'])) {
            $validated['contact_phone'] = $this->formatPhoneNumber($validated['contact_phone']);
        }
        
        // Format delegate phone numbers
        foreach ($delegates as &$delegate) {
            if (isset($delegate['phone'])) {
                $delegate['phone'] = $this->formatPhoneNumber($delegate['phone']);
            }
        }
        
        // Store form data in session for preview (including delegates)
        $registrationData = array_merge($validated, [
            'event_id' => $event->id,
            'event_slug' => $event->slug ?? $event->id,
            'delegates' => $delegates, // Store delegates array
        ]);
        
        session(['ticket_registration_data' => $registrationData]);

        // Track registration in progress
        $trackingToken = session('ticket_registration_tracking_token');
        if ($trackingToken) {
            $tracking = TicketRegistrationTracking::where('tracking_token', $trackingToken)
                ->where('event_id', $event->id)
                ->first();
            
            if ($tracking) {
                $ticketType = TicketType::find($validated['ticket_type_id']);
                $tracking->updateStatus('in_progress', [
                    'registration_data' => $registrationData,
                    'ticket_type_id' => $validated['ticket_type_id'],
                    'ticket_type_slug' => $ticketType->slug ?? null,
                    'nationality' => $validated['nationality'],
                    'delegate_count' => $validated['delegate_count'],
                    'company_country' => $validated['company_country'] ?? null,
                ]);
            }
        }

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

        // Determine nationality for pricing
        $nationality = $registrationData['nationality'] ?? 'Indian';
        $isInternational = ($nationality === 'International' || $nationality === 'international');
        $nationalityForPrice = $isInternational ? 'international' : 'national';
        
        // Calculate pricing
        $quantity = $registrationData['delegate_count'];
        $unitPrice = $ticketType->getCurrentPrice($nationalityForPrice);
        $subtotal = $unitPrice * $quantity;
        
        // Get GST rate (default 18%)
        $gstRate = config('constants.GST_RATE', 18);
        
        // Get processing charge rate (3% for India, 9% for International)
        $country = $registrationData['company_country'] ?? $registrationData['country'] ?? '';
        $isIndian = strtolower($country) === 'india' || $nationality === 'Indian';
        $processingChargeRate = $isIndian 
            ? config('constants.IND_PROCESSING_CHARGE', 3) 
            : config('constants.INT_PROCESSING_CHARGE', 9);
        
        // Calculate GST on subtotal
        $gstAmount = ($subtotal * $gstRate) / 100;
        
        // Calculate processing charge on (subtotal + GST)
        $processingChargeAmount = (($subtotal + $gstAmount) * $processingChargeRate) / 100;
        
        // Total
        $total = $subtotal + $gstAmount + $processingChargeAmount;
        
        // Determine currency
        $currency = $isInternational ? 'USD' : 'INR';

        // Track preview viewed - update with latest registration data and calculated total
        $trackingToken = session('ticket_registration_tracking_token');
        if ($trackingToken) {
            $tracking = TicketRegistrationTracking::where('tracking_token', $trackingToken)
                ->where('event_id', $event->id)
                ->first();
            
            if ($tracking) {
                // Store ALL registration data including all form fields and delegates in JSON format
                $tracking->updateStatus('preview_viewed', [
                    'registration_data' => $registrationData, // Complete form data with all fields in JSON
                    'calculated_total' => $total,
                ]);
            }
        }

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
        $ipAddress = $request->ip();
        
        try {
            // First, check if GST exists in GstLookup table (cache)
            $gst = GstLookup::where('gst_number', $gstin)->first();
            
            if ($gst) {
                // Update last verified timestamp
                $gst->update(['last_verified_at' => now()]);
                
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
            
            // If not in cache, check IP-based rate limiting (3 hits per IP)
            $ipHits = cache()->get("gst_validation_ip_{$ipAddress}", 0);
            
            if ($ipHits >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'GST validation limit exceeded (3 attempts per IP). Please fill the details manually.',
                    'limit_exceeded' => true,
                    'allow_manual' => true
                ], 429);
            }
            
            // Increment IP hit counter
            cache()->put("gst_validation_ip_{$ipAddress}", $ipHits + 1, now()->addHours(24));
            
            // Fetch from API (this will also save to GstLookup)
            $gst = GstLookup::fetchFromApi($gstin);
            
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
                'message' => 'GST number not found or invalid. Please fill the details manually.',
                'allow_manual' => true
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('GST Validation Error', [
                'gstin' => $gstin,
                'ip' => $ipAddress,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error validating GST. Please fill the details manually.',
                'allow_manual' => true
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

