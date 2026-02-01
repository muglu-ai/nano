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
use App\Services\TicketPromoCodeService;
use App\Services\TicketGstCalculationService;
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
        
        // Load ticket types with relationships - exclude exhibitor-only ticket types
        // (by category flag OR by name/slug containing "exhibitor")
        $ticketTypes = TicketType::where('event_id', $event->id)
            ->where('is_active', true)
            ->with(['category' => function($query) {
                $query->select('id', 'name', 'is_exhibitor_only');
            }, 'subcategory', 'eventDays', 'inventory'])
            ->where(function($query) {
                $query->whereDoesntHave('category')
                      ->orWhereHas('category', function($q) {
                          $q->where('is_exhibitor_only', false)
                            ->orWhereNull('is_exhibitor_only');
                      });
            })
            ->where(function($query) {
                $query->whereRaw('LOWER(name) NOT LIKE ?', ['%exhibitor%'])
                      ->whereRaw('LOWER(COALESCE(slug, "")) NOT LIKE ?', ['%exhibitor%']);
            })
            ->orderBy('sort_order')
            ->get();
        
        // Group by category - exclude exhibitor-only categories and categories that only have exhibitor-type tickets
        $categories = TicketCategory::where('event_id', $event->id)
            ->where(function($query) {
                $query->where('is_exhibitor_only', false)
                      ->orWhereNull('is_exhibitor_only');
            })
            ->whereRaw('LOWER(COALESCE(name, "")) NOT LIKE ?', ['%exhibitor%'])
            ->with(['ticketTypes' => function($query) {
                $query->where('is_active', true)
                      ->whereRaw('LOWER(name) NOT LIKE ?', ['%exhibitor%'])
                      ->whereRaw('LOWER(COALESCE(slug, "")) NOT LIKE ?', ['%exhibitor%'])
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

        // If ticket and nationality are not in URL, check session data (for edit flow from preview)
        if (!$selectedTicketParam && !$selectedNationality) {
            $registrationData = session('ticket_registration_data');
            
            // If session data exists and has ticket type and nationality, use them
            if ($registrationData && isset($registrationData['event_id']) && $registrationData['event_id'] == $event->id) {
                // Get ticket type from session - could be slug or ID
                if (isset($registrationData['ticket_type_id'])) {
                    $ticketTypeIdOrSlug = $registrationData['ticket_type_id'];
                    
                    // Try to find ticket type to get slug if it's an ID
                    // Exclude exhibitor-only ticket types (by category or name/slug)
                    $tempTicketType = \App\Models\Ticket\TicketType::where('event_id', $event->id)
                        ->where(function($query) use ($ticketTypeIdOrSlug) {
                            $query->where('slug', $ticketTypeIdOrSlug)
                                  ->orWhere('id', $ticketTypeIdOrSlug);
                        })
                        ->where(function($query) {
                            $query->whereDoesntHave('category')
                                  ->orWhereHas('category', function($q) {
                                      $q->where('is_exhibitor_only', false)
                                        ->orWhereNull('is_exhibitor_only');
                                  });
                        })
                        ->whereRaw('LOWER(name) NOT LIKE ?', ['%exhibitor%'])
                        ->whereRaw('LOWER(COALESCE(slug, "")) NOT LIKE ?', ['%exhibitor%'])
                        ->first();
                    
                    if ($tempTicketType) {
                        $selectedTicketParam = $tempTicketType->slug;
                    } else {
                        // Exhibitor pass from session not allowed on public form - clear so user picks a valid type
                        $selectedTicketParam = null;
                    }
                }
                
                // Get nationality from session and normalize
                if (isset($registrationData['nationality'])) {
                    $nationality = $registrationData['nationality'];
                    // Normalize: 'Indian'/'indian' -> 'national', 'International'/'international' -> 'international'
                    if (in_array(strtolower($nationality), ['indian', 'national'])) {
                        $selectedNationality = 'national';
                    } elseif (in_array(strtolower($nationality), ['international'])) {
                        $selectedNationality = 'international';
                    } else {
                        $selectedNationality = $nationality;
                    }
                }
            }
            
            // If still no ticket and nationality, redirect to the ticket registration link
            if (!$selectedTicketParam && !$selectedNationality) {
                $redirectUrl = config('constants.TICKET_REGISTRATION_LINK');
                // Redirect to the ticket registration link
                return redirect()->to($redirectUrl);
            }
        }
        
        // Load ticket types - exclude exhibitor-only (by category flag or name/slug)
        $ticketTypes = TicketType::where('event_id', $event->id)
            ->where('is_active', true)
            ->with(['category' => function($query) {
                $query->select('id', 'name', 'is_exhibitor_only');
            }, 'subcategory', 'eventDays', 'inventory'])
            ->where(function($query) {
                $query->whereDoesntHave('category')
                      ->orWhereHas('category', function($q) {
                          $q->where('is_exhibitor_only', false)
                            ->orWhereNull('is_exhibitor_only');
                      });
            })
            ->where(function($query) {
                $query->whereRaw('LOWER(name) NOT LIKE ?', ['%exhibitor%'])
                      ->whereRaw('LOWER(COALESCE(slug, "")) NOT LIKE ?', ['%exhibitor%']);
            })
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
                    // Also ensure it's not an exhibitor-only ticket type
                    $ticketType = TicketType::where('event_id', $event->id)
                        ->where(function($query) use ($value) {
                            $query->where('slug', $value)
                                  ->orWhere('id', $value);
                        })
                        ->where('is_active', true)
                        ->whereHas('category', function($query) {
                            $query->where('is_exhibitor_only', false)
                                  ->orWhereNull('is_exhibitor_only');
                        })
                        ->first();
                    
                    if (!$ticketType) {
                        $fail('The selected ticket type is invalid or not available for public registration.');
                    }
                },
            ],
            'delegate_count' => 'required|integer|min:1|max:100',
            'nationality' => 'required|in:national,international,Indian,International',
            'registration_type' => 'required|in:Individual,Organisation',
            'organisation_name' => [
                function ($attribute, $value, $fail) use ($request) {
                    $registrationType = $request->input('registration_type');
                    if ($registrationType === 'Organisation' && empty($value)) {
                        $fail('The organisation name field is required when registration type is Organisation.');
                    }
                },
                'nullable',
                'string',
                'max:255',
            ],
            'industry_sector' => 'required|string|max:255',
            'organisation_type' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    $registrationType = $request->input('registration_type');
                    
                    // Individual registration types
                    $individualTypes = ['Incubator', 'Accelerator', 'Investors', 'Consulting', 'Service Enabler / Consulting', 'Students', 'Others'];
                    
                    // Organisation registration types (from config)
                    $organisationTypes = config('constants.organization_types', []);
                    
                    // Validate based on registration type
                    if ($registrationType === 'Individual') {
                        if (!in_array($value, $individualTypes)) {
                            $fail('Please select a valid organisation type for Individual registration.');
                        }
                    } else if ($registrationType === 'Organisation') {
                        if (!in_array($value, $organisationTypes)) {
                            $fail('Please select a valid organisation type for Organisation registration.');
                        }
                    }
                },
            ],
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
            'delegates.*.phone' => 'required|string|max:20',
            'delegates.*.salutation' => 'nullable|string|max:10',
            'delegates.*.job_title' => 'nullable|string|max:255',
            'delegates.*.linkedin_profile' => 'nullable|url|max:500',
        ], [
            'ticket_type_id.required' => 'Please select a ticket type.',
            'registration_type.required' => 'Please select a registration type.',
            'registration_type.in' => 'Registration type must be either Individual or Organisation.',
            'industry_sector.required' => 'Please select an industry sector.',
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

        // Verify ticket type belongs to this event and is not exhibitor-only
        $ticketType = TicketType::where('event_id', $event->id)
            ->where(function($query) use ($validated) {
                $query->where('slug', $validated['ticket_type_id'])
                      ->orWhere('id', $validated['ticket_type_id']);
            })
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereDoesntHave('category')
                      ->orWhereHas('category', function($q) {
                          $q->where('is_exhibitor_only', false)
                            ->orWhereNull('is_exhibitor_only');
                      });
            })
            ->whereRaw('LOWER(name) NOT LIKE ?', ['%exhibitor%'])
            ->whereRaw('LOWER(COALESCE(slug, "")) NOT LIKE ?', ['%exhibitor%'])
            ->firstOrFail();
        
        // Store ticket type ID (not slug) in validated data for consistency
        $validated['ticket_type_id'] = $ticketType->id;
        
        // Handle selected event day - only required if day selection is enabled
        $selectedEventDayId = $request->input('selected_event_day_id');
        if ($ticketType->enable_day_selection) {
            // Day selection is enabled - user must select a day
            if (empty($selectedEventDayId)) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['selected_event_day_id' => 'Please select which day you want to attend.']);
            }
            
            // Check if "All Days" option was selected
            if ($selectedEventDayId === 'all') {
                // User selected all days - store as 'all' to indicate full access
                $validated['selected_event_day_id'] = 'all';
                $validated['selected_all_days'] = true;
            } else {
                // Verify the selected day is valid for this ticket type
                $validDay = $ticketType->getAllAccessibleDays()->pluck('id')->contains($selectedEventDayId);
                if (!$validDay) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['selected_event_day_id' => 'The selected day is not valid for this ticket type.']);
                }
                
                $validated['selected_event_day_id'] = $selectedEventDayId;
                $validated['selected_all_days'] = false;
            }
        } else {
            // Day selection disabled - user gets all days automatically
            $validated['selected_event_day_id'] = null;
            $validated['selected_all_days'] = true;
        }
        
        // Normalize nationality value (convert 'national'/'international' to 'Indian'/'International')
        if (isset($validated['nationality'])) {
            if ($validated['nationality'] === 'national') {
                $validated['nationality'] = 'Indian';
            } elseif ($validated['nationality'] === 'international') {
                $validated['nationality'] = 'International';
            }
        }
        
        // Auto-set registration category if not provided
        // Priority: 1. Ticket rules, 2. Match by name, 3. First active category
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
                // Try to match registration category by ticket type name
                $matchedCategory = TicketRegistrationCategory::where('event_id', $event->id)
                    ->where('is_active', true)
                    ->where('name', $ticketType->name)
                    ->first();
                
                if ($matchedCategory) {
                    $validated['registration_category_id'] = $matchedCategory->id;
            } else {
                    // Fallback: first active registration category for this event
                $defaultCategory = TicketRegistrationCategory::where('event_id', $event->id)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->first();
                
                if ($defaultCategory) {
                    $validated['registration_category_id'] = $defaultCategory->id;
                }
                }
            }
        }

        // Handle Individual registration type - set organisation_name to null, but keep organisation_type (now required)
        if (isset($validated['registration_type']) && $validated['registration_type'] === 'Individual') {
            $validated['organisation_name'] = null;
            // organisation_type is now required for both Individual and Organisation, so keep it
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

        // Calculate pricing - check if per-day pricing applies
        $quantity = $registrationData['delegate_count'];
        $selectedEventDayId = $registrationData['selected_event_day_id'] ?? null;
        $selectedAllDays = $registrationData['selected_all_days'] ?? false;
        
        // Use per-day price if ticket has per-day pricing and a specific day is selected (not "all")
        if ($ticketType->hasPerDayPricing() && $selectedEventDayId && $selectedEventDayId !== 'all' && !$selectedAllDays) {
            // Single day selected - use per-day price
            $unitPrice = $ticketType->getPerDayPrice($nationalityForPrice) ?? $ticketType->getCurrentPrice($nationalityForPrice);
        } elseif ($ticketType->hasPerDayPricing() && ($selectedEventDayId === 'all' || $selectedAllDays)) {
            // "All Days" selected - use regular price (full package)
            $unitPrice = $ticketType->getCurrentPrice($nationalityForPrice);
        } else {
            $unitPrice = $ticketType->getCurrentPrice($nationalityForPrice);
        }
        $subtotal = round($unitPrice * $quantity);
        
        // Apply Group Discount FIRST (if delegate count > 3, apply 10% discount)
        $groupDiscountApplied = false;
        $groupDiscountRate = 0;
        $groupDiscountAmount = 0;
        $groupDiscountMinDelegates = config('constants.GROUP_DISCOUNT_MIN_DELEGATES', 4); // Minimum 4 delegates required for group discount
        
        if ($quantity >= $groupDiscountMinDelegates) {
            $groupDiscountApplied = true;
            $groupDiscountRate = config('constants.GROUP_DISCOUNT_RATE', 10); // Default 10%
            $groupDiscountAmount = round(($subtotal * $groupDiscountRate) / 100);
        }
        
        // Subtotal after group discount
        $subtotalAfterGroupDiscount = round($subtotal - $groupDiscountAmount);
        
        // Apply promocode discount SECOND (on amount after group discount)
        $discountAmount = 0;
        $promocodeData = session('ticket_promocode');
        $promocodeCode = null;
        $promocodeDiscountPercentage = null;
        
        if ($promocodeData && isset($promocodeData['promo_code_id'])) {
            $promoCode = \App\Models\Ticket\TicketPromoCode::find($promocodeData['promo_code_id']);
            if ($promoCode) {
                // Re-validate promocode
                $promoCodeService = new TicketPromoCodeService();
                $validationData = [
                    'ticket_type_id' => $ticketType->id,
                    'registration_category_id' => $registrationData['registration_category_id'] ?? null,
                    'selected_event_day_id' => ($selectedEventDayId === 'all' || $selectedAllDays) ? null : $selectedEventDayId,
                    'delegate_count' => $quantity,
                    'base_amount' => $subtotalAfterGroupDiscount, // Use amount after group discount
                ];
                
                $validationResult = $promoCodeService->validatePromoCode($promoCode->code, $event->id, $validationData);
                
                if ($validationResult['valid']) {
                    // Calculate discount on amount after group discount
                    $discountAmount = $promoCodeService->calculateDiscount($promoCode, $subtotalAfterGroupDiscount);
                    $promocodeCode = $promoCode->code;
                    $promocodeDiscountPercentage = $promoCode->type === 'percentage' ? $promoCode->value : null;
                } else {
                    // Invalid promocode, clear from session
                    session()->forget('ticket_promocode');
                }
            }
        }
        
        // Calculate subtotal after all discounts (GST will be calculated on this)
        $subtotalAfterDiscount = round($subtotalAfterGroupDiscount - $discountAmount);
        
        // Determine GST type and calculate GST on discounted amount
        $gstService = new TicketGstCalculationService();
        $gstType = $gstService->determineGstType($registrationData);
        $gstCalculation = $gstService->calculateGst($subtotalAfterDiscount, $gstType);
        
        // Extract GST values
        $cgstRate = $gstCalculation['cgst_rate'];
        $cgstAmount = $gstCalculation['cgst_amount'];
        $sgstRate = $gstCalculation['sgst_rate'];
        $sgstAmount = $gstCalculation['sgst_amount'];
        $igstRate = $gstCalculation['igst_rate'];
        $igstAmount = $gstCalculation['igst_amount'];
        $gstAmount = $gstCalculation['total_gst']; // Total GST for backward compatibility
        
        // Get processing charge rate (3% for India/National, 9% for International)
        // Use nationality to determine processing charge rate
        $processingChargeRate = $isInternational 
            ? config('constants.INT_PROCESSING_CHARGE', 9)  // International: 9%
            : config('constants.IND_PROCESSING_CHARGE', 3); // National/Indian: 3%
        
        // Calculate processing charge on (discounted subtotal + GST)
        $processingChargeAmount = round((($subtotalAfterDiscount + $gstAmount) * $processingChargeRate) / 100);
        
        // Calculate final total: discounted subtotal + GST + processing charge
        $total = round($subtotalAfterDiscount + $gstAmount + $processingChargeAmount);
        
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
            'groupDiscountApplied',
            'groupDiscountRate',
            'groupDiscountAmount',
            'groupDiscountMinDelegates',
            'gstType',
            'cgstRate',
            'cgstAmount',
            'sgstRate',
            'sgstAmount',
            'igstRate',
            'igstAmount',
            'gstAmount', // Keep for backward compatibility
            'processingChargeRate',
            'processingChargeAmount',
            'discountAmount',
            'promocodeCode',
            'promocodeDiscountPercentage',
            'total',
            'currency',
            'isInternational'
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
                    'message' => 'GST validation limit exceeded. Please fill the details manually.',
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
     * Validate promocode
     */
    public function validatePromocode(Request $request, $eventSlug)
    {
        try {
            $event = Events::where('slug', $eventSlug)->orWhere('id', $eventSlug)->firstOrFail();
            
            // Validate request - return JSON on validation failure
            // Note: ticket_type_id can be either ID or slug
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'code' => 'required|string|max:100',
                'ticket_type_id' => [
                    'required',
                    function ($attribute, $value, $fail) use ($event) {
                        // Check if ticket type exists and is not exhibitor-only (by category or name/slug)
                        $ticketType = TicketType::where('event_id', $event->id)
                            ->where(function($query) use ($value) {
                                $query->where('slug', $value)
                                      ->orWhere('id', $value);
                            })
                            ->where('is_active', true)
                            ->where(function($query) {
                                $query->whereDoesntHave('category')
                                      ->orWhereHas('category', function($q) {
                                          $q->where('is_exhibitor_only', false)
                                            ->orWhereNull('is_exhibitor_only');
                                      });
                            })
                            ->whereRaw('LOWER(name) NOT LIKE ?', ['%exhibitor%'])
                            ->whereRaw('LOWER(COALESCE(slug, "")) NOT LIKE ?', ['%exhibitor%'])
                            ->first();
                        
                        if (!$ticketType) {
                            $fail('The selected ticket type is invalid or not available for public registration.');
                        }
                    },
                ],
                'registration_category_id' => 'nullable|exists:ticket_registration_categories,id',
                'selected_event_day_id' => 'nullable',
                'delegate_count' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'valid' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get registration data from session or request
            $registrationData = session('ticket_registration_data', []);
            
            // Get ticket type (exclude exhibitor-only by category or name/slug)
            $ticketType = TicketType::where('event_id', $event->id)
                ->where(function($query) use ($request) {
                    $query->where('slug', $request->ticket_type_id)
                          ->orWhere('id', $request->ticket_type_id);
                })
                ->where('is_active', true)
                ->where(function($query) {
                    $query->whereDoesntHave('category')
                          ->orWhereHas('category', function($q) {
                              $q->where('is_exhibitor_only', false)
                                ->orWhereNull('is_exhibitor_only');
                          });
                })
                ->whereRaw('LOWER(name) NOT LIKE ?', ['%exhibitor%'])
                ->whereRaw('LOWER(COALESCE(slug, "")) NOT LIKE ?', ['%exhibitor%'])
                ->first();
                
            if (!$ticketType) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Invalid ticket type.',
                ], 400);
            }

            // Determine nationality for pricing
            $nationality = $registrationData['nationality'] ?? $request->nationality ?? 'Indian';
            $isInternational = ($nationality === 'International' || $nationality === 'international');
            $nationalityForPrice = $isInternational ? 'international' : 'national';

            // Calculate base amount (subtotal before GST/processing charges)
            $quantity = $request->delegate_count;
            $selectedEventDayId = $request->selected_event_day_id;
            $selectedAllDays = ($selectedEventDayId === 'all' || $selectedEventDayId === null);
            
            // Use per-day price if applicable
            if ($ticketType->hasPerDayPricing() && $selectedEventDayId && !$selectedAllDays) {
                $unitPrice = $ticketType->getPerDayPrice($nationalityForPrice) ?? $ticketType->getCurrentPrice($nationalityForPrice);
            } else {
                $unitPrice = $ticketType->getCurrentPrice($nationalityForPrice);
            }
            
            $baseAmount = round($unitPrice * $quantity);

            // Prepare validation data
            $validationData = [
                'ticket_type_id' => $request->ticket_type_id,
                'registration_category_id' => $request->registration_category_id,
                'selected_event_day_id' => $selectedAllDays ? null : $selectedEventDayId,
                'delegate_count' => $quantity,
                'base_amount' => $baseAmount,
                'contact_id' => $registrationData['contact_id'] ?? null,
            ];

            // Validate promocode
            $promoCodeService = new TicketPromoCodeService();
            $result = $promoCodeService->validatePromoCode($request->code, $event->id, $validationData);

            if ($result['valid']) {
                // Calculate discount first
                $discountAmount = round($result['discount_amount']);
                $baseAmountAfterDiscount = round($baseAmount - $discountAmount);
                
                // Calculate GST on discounted amount using new service
                $gstService = new TicketGstCalculationService();
                $gstType = $gstService->determineGstType($registrationData);
                $gstCalculation = $gstService->calculateGst($baseAmountAfterDiscount, $gstType);
                $gstAmount = $gstCalculation['total_gst'];
                
                $processingChargeRate = $isInternational 
                    ? config('constants.INT_PROCESSING_CHARGE', 9)
                    : config('constants.IND_PROCESSING_CHARGE', 3);
                
                $processingChargeAmount = round((($baseAmountAfterDiscount + $gstAmount) * $processingChargeRate) / 100);
                $finalTotal = round($baseAmountAfterDiscount + $gstAmount + $processingChargeAmount);
                
                // Check if complimentary
                $isComplimentary = $finalTotal <= 0;

                // Store promocode in session
                session(['ticket_promocode' => [
                    'code' => $request->code,
                    'promo_code_id' => $result['promoCode']->id,
                    'discount_amount' => $discountAmount,
                    'discount_percentage' => $result['discount_percentage'],
                ]]);

                return response()->json([
                    'valid' => true,
                    'message' => $result['message'],
                    'discount_amount' => $discountAmount,
                    'discount_percentage' => $result['discount_percentage'],
                    'base_amount' => $baseAmount,
                    'gst_amount' => $gstAmount,
                    'cgst_amount' => $gstCalculation['cgst_amount'] ?? null,
                    'sgst_amount' => $gstCalculation['sgst_amount'] ?? null,
                    'igst_amount' => $gstCalculation['igst_amount'] ?? null,
                    'gst_type' => $gstType,
                    'processing_charge_amount' => $processingChargeAmount,
                    'final_total' => $finalTotal,
                    'is_complimentary' => $isComplimentary,
                ]);
            }

            // Clear promocode from session if invalid
            session()->forget('ticket_promocode');

            return response()->json([
                'valid' => false,
                'message' => $result['message'],
            ], 400);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'valid' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Event not found.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Promocode Validation Error', [
                'code' => $request->code ?? 'N/A',
                'event_slug' => $eventSlug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'valid' => false,
                'message' => 'An error occurred while validating the promocode. Please try again.',
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
     * Example: +91-8619276031 -> +91-8619276031 (already formatted, returns as-is)
     */
    private function formatPhoneNumber($phone)
    {
        if (empty($phone)) {
            return $phone;
        }
        
        // Remove all spaces
        $phone = str_replace(' ', '', trim($phone));
        
        // If already in format +CC-NUMBER, validate and return
        if (preg_match('/^(\+\d{1,3})-(\d+)$/', $phone, $matches)) {
            $countryCode = $matches[1];
            $number = $matches[2];
            
            // Validate phone number has at least 7 digits (minimum for most countries)
            if (strlen($number) < 7) {
                \Log::warning('Phone number too short', [
                    'phone' => $phone,
                    'number_length' => strlen($number),
                ]);
            }
            
            return $phone; // Already formatted correctly
        }
        
        // If phone starts with + but no dash, add dash after country code
        if (preg_match('/^(\+\d{1,3})(\d+)$/', $phone, $matches)) {
            $countryCode = $matches[1];
            $number = $matches[2];
            
            // Validate phone number has at least 7 digits
            if (strlen($number) < 7) {
                \Log::warning('Phone number too short', [
                    'phone' => $phone,
                    'number_length' => strlen($number),
                ]);
            }
            
            return $countryCode . '-' . $number;
        }
        
        return $phone;
    }
}

