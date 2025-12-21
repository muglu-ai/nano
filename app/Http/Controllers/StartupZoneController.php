<?php

namespace App\Http\Controllers;

use App\Models\StartupZoneDraft;
use App\Models\AssociationPricingRule;
use App\Models\FormFieldConfiguration;
use App\Models\Application;
use App\Models\EventContact;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\State;
use App\Models\Country;
use App\Models\GstLookup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Mail\UserCredentialsMail;
use App\Mail\ExhibitorRegistrationMail;

class StartupZoneController extends Controller
{
    /**
     * Show the multi-step registration form
     */
    public function showForm(Request $request)
    {
        // Get association from URL parameter
        $associationParam = $request->query('association');
        
        // Get draft data from session (if exists)
        $sessionData = session('startup_zone_draft', []);
        
        // Create a simple object-like structure for the view
        // Ensure contact_data is properly structured if it exists
        if (isset($sessionData['contact_data']) && is_array($sessionData['contact_data'])) {
            // Keep contact_data as array for easy access in view
        }
        
        $draft = (object) $sessionData;
        
        // Ensure progress_percentage exists
        if (!isset($draft->progress_percentage)) {
            $draft->progress_percentage = 0;
        }
        
        // Set default country to India if not set
        if (!isset($draft->country_id)) {
            $india = Country::where('code', 'IN')->first();
            if ($india) {
                $draft->country_id = $india->id;
            }
        }
        
        // Get association pricing rules
        $associations = AssociationPricingRule::active()->valid()->get();
        
        // Get form field configurations (current version)
        $fieldConfigs = FormFieldConfiguration::currentVersion()
            ->active()
            ->byFormType('startup-zone')
            ->ordered()
            ->get()
            ->keyBy('field_name');
        
        // Get dropdown data
        $sectors = DB::table('sectors')->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        
        // Get SUB_SECTORS from config file instead of database
        $subSectorsConfig = config('constants.SUB_SECTORS', []);
        $subSectors = collect($subSectorsConfig)->map(function ($name, $index) {
            return (object) [
                'id' => $index + 1,
                'name' => $name,
                'is_active' => true,
                'sort_order' => $index + 1
            ];
        });
        
        $orgTypes = DB::table('organization_types')->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        
        // Get countries using same pattern as ApplicationController
        $countries = Country::select('id', 'name', 'code')->orderBy('name')->get();
        
        // Get India's ID for default selection
        $india = Country::where('code', 'IN')->first();
        $indiaId = $india ? $india->id : null;
        
        // Get states for India by default (or selected country from draft)
        $selectedCountryId = $draft->country_id ?? $indiaId;
        $states = $selectedCountryId ? State::where('country_id', $selectedCountryId)->select('id', 'name')->orderBy('name')->get() : collect();
        
        // Get association logo if association param is provided
        $associationLogo = null;
        if ($associationParam) {
            $association = AssociationPricingRule::where('association_name', $associationParam)
                ->orWhere('promocode', $associationParam)
                ->active()
                ->first();
            if ($association && $association->logo_path) {
                $associationLogo = asset('storage/' . $association->logo_path);
            }
        }
        
        // Share associationLogo with all views using view()->share() or pass to layout
        view()->share('associationLogo', $associationLogo);
        
        return view('startup-zone.form', compact(
            'draft',
            'associations',
            'fieldConfigs',
            'sectors',
            'subSectors',
            'orgTypes',
            'states',
            'countries',
            'associationParam',
            'associationLogo'
        ));
    }

    /**
     * Store form data in session (lightweight, no database writes)
     */
    public function autoSave(Request $request)
    {
        // Store all form data in session - no database writes until submit
        $formData = $request->except(['_token', 'certificate']);
        
        // Handle landline: use national number if available, otherwise use full number
        if ($request->has('landline_national') && $request->input('landline_national')) {
            $formData['landline'] = $request->input('landline_national');
            $formData['landline_country_code'] = $request->input('landline_country_code');
        }
        
        // Handle file upload separately (if provided)
        if ($request->hasFile('certificate')) {
            $file = $request->file('certificate');
            $path = $file->store('startup-zone/certificates', 'public');
            $formData['certificate_path'] = $path;
        }
        
        // Build contact data from individual fields
        // Use contact_mobile_national for the actual mobile number (without country code)
        $contactData = [
            'title' => $request->input('contact_title'),
            'first_name' => $request->input('contact_first_name'),
            'last_name' => $request->input('contact_last_name'),
            'designation' => $request->input('contact_designation'),
            'email' => $request->input('contact_email'),
            'mobile' => $request->input('contact_mobile_national') ?: $request->input('contact_mobile'), // Use national number if available
            'country_code' => $request->input('contact_country_code'),
        ];
        
        if (!empty($contactData)) {
            $formData['contact_data'] = $contactData;
        }
        
        // Store in session
        session(['startup_zone_draft' => $formData]);
        
        // Calculate progress percentage (for UI feedback only)
        $progress = $this->calculateProgressFromData($formData);
        
        return response()->json([
            'success' => true,
            'message' => 'Data stored in session',
            'progress' => $progress
        ]);
    }

    /**
     * Validate promocode
     */
    public function validatePromocode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'promocode' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid promocode format'
            ], 422);
        }

        $promocode = $request->input('promocode');
        
        $association = AssociationPricingRule::where('promocode', $promocode)
            ->active()
            ->valid()
            ->first();

        if (!$association) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired promocode'
            ], 404);
        }

        // Check registration limit
        if ($association->isRegistrationFull()) {
            return response()->json([
                'success' => false,
                'message' => 'Registration limit reached for this promocode'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'association' => [
                'name' => $association->association_name,
                'display_name' => $association->display_name,
                'logo_path' => $association->logo_path ? asset('storage/' . $association->logo_path) : null,
                'price' => $association->getEffectivePrice(),
                'is_complimentary' => $association->is_complimentary,
                'description' => $association->description,
            ]
        ]);
    }

    /**
     * Fetch GST details from API or database
     */
    public function fetchGstDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gst_no' => 'required|string|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid GST number format'
            ], 422);
        }

        $gstNumber = strtoupper($request->input('gst_no'));
        
        // Rate limiting: 5 requests per IP per 10 minutes
        $ipAddress = $request->ip();
        $rateLimitKey = 'gst_api_rate_limit_' . $ipAddress;
        $rateLimitData = Cache::get($rateLimitKey, ['count' => 0, 'reset_at' => now()->addMinutes(10)]);
        
        // Check if rate limit exceeded
        if ($rateLimitData['count'] >= 5) {
            $resetTime = $rateLimitData['reset_at'];
            $minutesRemaining = max(1, (int) ceil(now()->diffInSeconds($resetTime) / 60)); // Round up to whole minutes
            
            if ($minutesRemaining > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Rate limit exceeded. Please try again after {$minutesRemaining} minutes.",
                    'rate_limit_exceeded' => true,
                    'reset_in_minutes' => $minutesRemaining
                ], 429);
            } else {
                // Reset counter if time has passed
                $rateLimitData = ['count' => 0, 'reset_at' => now()->addMinutes(10)];
            }
        }
        
        // Check database first (doesn't count towards rate limit)
        $gstLookup = GstLookup::where('gst_number', $gstNumber)->first();
        
        if ($gstLookup) {
            // Update last verified timestamp (from cache, no API call)
            $gstLookup->update(['last_verified_at' => now()]);
            
            // Return cached data
            $stateId = $this->getStateIdFromName($gstLookup->state_name);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'company_name' => $gstLookup->company_name,
                    'billing_address' => $gstLookup->billing_address,
                    'state_id' => $stateId,
                    'state_name' => $gstLookup->state_name,
                    'pincode' => $gstLookup->pincode,
                    'pan' => $gstLookup->pan,
                    'city' => $gstLookup->city,
                    'trade_name' => $gstLookup->trade_name,
                    'status' => $gstLookup->status,
                ],
                'from_cache' => true,
                'rate_limit_remaining' => null // Don't show for cached responses
            ]);
        }
        
        // Increment rate limit counter before API call
        $rateLimitData['count']++;
        Cache::put($rateLimitKey, $rateLimitData, now()->addMinutes(10));
        
        // Fetch from API
        $gstLookup = GstLookup::fetchFromApi($gstNumber);

        if (!$gstLookup) {
            return response()->json([
                'success' => false,
                'message' => 'GST number not found or invalid. Please verify the GST number and try again, or fill the details manually.'
            ], 404);
        }

        // Get state ID from state name
        $stateId = $this->getStateIdFromName($gstLookup->state_name);

            // Only show remaining requests on the last API call (when 1 request remaining)
            $rateLimitRemaining = 5 - $rateLimitData['count'];
            $showRemaining = $rateLimitRemaining === 1;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'company_name' => $gstLookup->company_name,
                    'billing_address' => $gstLookup->billing_address,
                    'state_id' => $stateId,
                    'state_name' => $gstLookup->state_name,
                    'pincode' => $gstLookup->pincode,
                    'pan' => $gstLookup->pan,
                    'city' => $gstLookup->city,
                    'trade_name' => $gstLookup->trade_name,
                    'status' => $gstLookup->status,
                ],
                'from_cache' => false,
                'rate_limit_remaining' => $showRemaining ? $rateLimitRemaining : null
            ]);
    }

    /**
     * Helper: Get state ID from state name
     */
    private function getStateIdFromName($stateName)
    {
        if (!$stateName) {
            return null;
        }

        // Clean state name (remove extra spaces)
        $stateName = trim($stateName);
        
        // Try exact match first (case insensitive)
        $state = State::whereRaw('LOWER(name) = ?', [strtolower($stateName)])->first();
        
        // If not found, try partial match (case insensitive)
        if (!$state) {
            $state = State::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($stateName) . '%'])->first();
        }
        
        // If still not found, try reverse partial match
        if (!$state) {
            $states = State::all();
            foreach ($states as $s) {
                if (stripos($stateName, $s->name) !== false || stripos($s->name, $stateName) !== false) {
                    $state = $s;
                    break;
                }
            }
        }
        
        return $state ? $state->id : null;
    }

    /**
     * Submit complete form (all fields in one page)
     * Now saves to database from session data
     */
    public function submitForm(Request $request)
    {
        try {
            $fieldConfigs = FormFieldConfiguration::currentVersion()
                ->active()
                ->byFormType('startup-zone')
                ->get()
                ->keyBy('field_name');

            // Build validation rules for all fields
            $rules = $this->buildValidationRules($fieldConfigs, 'all');
            
            // Merge session data with request data for validation
            $sessionData = session('startup_zone_draft', []);
            $allData = array_merge($sessionData, $request->all());
            
            // For intl-tel-input fields, validate the national number instead
            // Map contact_mobile_national to contact_mobile for validation
            if ($request->has('contact_mobile_national') && !empty($request->input('contact_mobile_national'))) {
                $allData['contact_mobile'] = $request->input('contact_mobile_national');
            }
            if ($request->has('landline_national') && !empty($request->input('landline_national'))) {
                $allData['landline'] = $request->input('landline_national');
            }
            
            $validator = Validator::make($allData, $rules);

            if ($validator->fails()) {
                // Log validation errors for debugging
                \Log::info('Startup Zone Form Validation Failed', [
                    'errors' => $validator->errors()->toArray(),
                    'data_keys' => array_keys($allData),
                    'rules' => $rules
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Please fix the validation errors below.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Now save to database from session + request data
            $sessionId = session()->getId();
            
            // Get or create draft in database
            $draft = StartupZoneDraft::bySession($sessionId)->first();

            if (!$draft) {
                $draft = new StartupZoneDraft();
                $draft->session_id = $sessionId;
                $draft->uuid = Str::uuid();
                $draft->expires_at = now()->addDays(30);
            }

            // Handle file upload (from request or session)
            if ($request->hasFile('certificate')) {
                $file = $request->file('certificate');
                $path = $file->store('startup-zone/certificates', 'public');
                $draft->certificate_path = $path;
            } elseif (isset($sessionData['certificate_path'])) {
                $draft->certificate_path = $sessionData['certificate_path'];
            }

            // Handle landline: use national number if available
            $landlineData = [];
            if ($request->has('landline_national') && $request->input('landline_national')) {
                $landlineData['landline'] = $request->input('landline_national');
            } elseif ($request->has('landline')) {
                $landlineData['landline'] = $request->input('landline');
            }
            
            // Update draft with all form fields from session + request
            $draft->fill(array_merge($sessionData, $landlineData, $request->only([
                'stall_category', 'interested_sqm', 'company_name',
                'how_old_startup', 'address', 'city_id', 'state_id',
                'postal_code', 'country_id', 'website',
                'company_email', 'gst_compliance', 'gst_no', 'pan_no',
                'sector_id', 'subSector', 'type_of_business',
                'promocode', 'assoc_mem', 'RegSource', 'payment_mode'
            ])));

            // Store contact data as JSON (from session or request)
            if (isset($sessionData['contact_data'])) {
                $draft->contact_data = $sessionData['contact_data'];
            } else {
                $contactData = [
                    'title' => $request->input('contact_title'),
                    'first_name' => $request->input('contact_first_name'),
                    'last_name' => $request->input('contact_last_name'),
                    'designation' => $request->input('contact_designation'),
                    'email' => $request->input('contact_email'),
                    'mobile' => $request->input('contact_mobile_national') ?: $request->input('contact_mobile'), // Use national number if available
                    'country_code' => $request->input('contact_country_code'),
                ];
                $draft->contact_data = $contactData;
            }

            $draft->progress_percentage = $this->calculateProgress($draft);
            $draft->save();

            return response()->json([
                'success' => true,
                'message' => 'Form saved successfully',
                'progress' => $draft->progress_percentage
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Startup Zone Form Submission Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return JSON error response instead of HTML
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving the form. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Show preview page
     */
    public function showPreview(Request $request)
    {
        $associationLogo = null;
        
        // Check if application_id is provided (after draft restoration)
        if ($request->has('application_id')) {
            $application = Application::where('application_id', $request->application_id)
                ->where('application_type', 'startup-zone')
                ->firstOrFail();
            
            $invoice = Invoice::where('application_id', $application->id)->firstOrFail();
            $contact = EventContact::where('application_id', $application->id)->first();
            
            // Get association logo if promocode exists
            if ($application->promocode) {
                $association = AssociationPricingRule::where('promocode', $application->promocode)
                    ->active()
                    ->first();
                if ($association && $association->logo_path) {
                    $associationLogo = asset('storage/' . $association->logo_path);
                }
            }
            view()->share('associationLogo', $associationLogo);
            
            return view('startup-zone.preview', compact('application', 'invoice', 'contact'));
        }
        
        // Otherwise, show draft preview from database (after submitForm saves it)
        $sessionId = session()->getId();
        $draft = StartupZoneDraft::bySession($sessionId)->active()->firstOrFail();

        // Get association logo if promocode exists in draft
        if ($draft->promocode) {
            $association = AssociationPricingRule::where('promocode', $draft->promocode)
                ->active()
                ->first();
            if ($association && $association->logo_path) {
                $associationLogo = asset('storage/' . $association->logo_path);
            }
        }
        view()->share('associationLogo', $associationLogo);

        // Calculate pricing
        $pricing = $this->calculatePricing($draft);

        return view('startup-zone.preview', compact('draft', 'pricing'));
    }

    /**
     * Restore draft to application (final submission)
     */
    public function restoreDraftToApplication(Request $request)
    {
        $sessionId = session()->getId();
        $draft = StartupZoneDraft::bySession($sessionId)->active()->firstOrFail();

        // Validate all required fields
        $fieldConfigs = FormFieldConfiguration::currentVersion()
            ->active()
            ->byFormType('startup-zone')
            ->get()
            ->keyBy('field_name');

        $validationResult = $this->validateDraft($draft, $fieldConfigs);
        
        if (!$validationResult['valid']) {
            return response()->json([
                'success' => false,
                'errors' => $validationResult['errors']
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Get contact email - prioritize contact person email, fallback to company email
            $contactPersonEmail = $draft->contact_data['email'] ?? null;
            $contactEmail = $contactPersonEmail ?: $draft->company_email;
            
            // Validate email is not empty
            if (empty($contactEmail)) {
                throw new \Exception('Contact email or company email is required');
            }
            
            $contactName = trim(($draft->contact_data['first_name'] ?? '') . ' ' . ($draft->contact_data['last_name'] ?? ''));
            if (empty($contactName)) {
                $contactName = $draft->company_name;
            }
            
            // Check if user exists with this email (email must be unique in users table)
            $user = \App\Models\User::where('email', $contactEmail)->first();
            $passwordGenerated = false;
            $password = null;
            
            if (!$user) {
                // Generate random password
                $password = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                $passwordHash = Hash::make($password);
                
                // Create user with the contact email (ensures uniqueness)
                try {
                    $user = \App\Models\User::create([
                        'name' => $contactName,
                        'email' => $contactEmail, // This email will be unique (enforced by DB constraint)
                        'password' => $passwordHash,
                        'simplePass' => $password,
                        'role' => 'exhibitor',
                        'email_verified_at' => now(),
                    ]);
                    $passwordGenerated = true;
                } catch (\Illuminate\Database\QueryException $e) {
                    // If email already exists (race condition), fetch the existing user
                    if ($e->getCode() == 23000) { // Integrity constraint violation
                        $user = \App\Models\User::where('email', $contactEmail)->first();
                        if (!$user) {
                            throw new \Exception('Failed to create user: Email already exists');
                        }
                    } else {
                        throw $e;
                    }
                }
            }
            
            // Generate application_id using TIN_NO_PREFIX with 6-digit number (before creating application)
            $applicationId = $this->generateApplicationIdWithTinPrefix();
            
            // Create application
            $application = new Application();
            $application->fill([
                'application_id' => $applicationId,
                'stall_category' => $draft->stall_category ?? 'Startup Booth',
                'interested_sqm' => $draft->interested_sqm ?? 'Booth / POD',
                'company_name' => $draft->company_name,
                'certificate' => $draft->certificate_path,
                'how_old_startup' => $draft->how_old_startup,
                'address' => $draft->address,
                'city_id' => $draft->city_id,
                'state_id' => $draft->state_id,
                'postal_code' => $draft->postal_code,
                'country_id' => $draft->country_id,
                'landline' => $draft->landline,
                'website' => $draft->website,
                'company_email' => $draft->company_email,
                'gst_compliance' => $draft->gst_compliance,
                'gst_no' => $draft->gst_no,
                'pan_no' => $draft->pan_no,
                'sector_id' => $draft->sector_id,
                'subSector' => $draft->subSector,
                'type_of_business' => $draft->type_of_business,
                'promocode' => $draft->promocode,
                'assoc_mem' => $draft->assoc_mem,
                'RegSource' => $draft->RegSource,
                'application_type' => 'startup-zone',
                'participant_type' => 'Startup',
                'status' => 'initiated',
                'submission_status' => 'in progress',
                'event_id' => $draft->event_id ?? 1,
                'user_id' => $user->id,
                'terms_accepted' => 1,
            ]);
            $application->save();

            // Create event contact - use the same email that was used for user creation
            if ($draft->contact_data) {
                $contact = new EventContact();
                $contact->application_id = $application->id;
                $contact->salutation = $draft->contact_data['title'] ?? null;
                $contact->first_name = $draft->contact_data['first_name'] ?? null;
                $contact->last_name = $draft->contact_data['last_name'] ?? null;
                $contact->job_title = $draft->contact_data['designation'] ?? null;
                // Ensure contact email matches the user email (use contact person email if available, otherwise company email)
                $contact->email = $contactEmail; // This matches the user email
                $contact->contact_number = $draft->contact_data['mobile'] ?? null;
                $contact->save();
            }

            // Create invoice
            $pricing = $this->calculatePricing($draft);
            $invoice = new Invoice();
            $invoice->application_id = $application->id;
            $invoice->application_no = $application->application_id;
            $invoice->invoice_no = $application->application_id . '-' . date('YmdHis');
            $invoice->type = 'Startup Zone Registration';
            $invoice->amount = $pricing['total']; // Required field - total amount
            $invoice->price = $pricing['base_price'];
            $invoice->gst = $pricing['gst'];
            $invoice->processing_charges = $pricing['processing_charges'];
            $invoice->processing_chargesRate = $pricing['processing_rate'];
            $invoice->total_final_price = $pricing['total'];
            $invoice->currency = $draft->payment_mode === 'PayPal' ? 'USD' : 'INR';
            $invoice->payment_status = 'unpaid';
            $invoice->payment_due_date = now()->addDays(5); // Required field - payment due date
            $invoice->pending_amount = $pricing['total']; // Set pending amount to total initially
            $invoice->amount_paid = 0; // No amount paid initially
            $invoice->save();

            // Update association registration count
            if ($draft->promocode) {
                $association = AssociationPricingRule::where('promocode', $draft->promocode)->first();
                if ($association) {
                    $association->increment('current_registrations');
                }
            }

            // Get contact for email
            $contact = EventContact::where('application_id', $application->id)->first();
            
            // Send credentials email if user was just created
            if ($passwordGenerated) {
                try {
                    $setupProfileUrl = config('app.url');
                    Mail::to($contactEmail)->send(new UserCredentialsMail(
                        $contactName,
                        $setupProfileUrl,
                        $contactEmail,
                        $password
                    ));
                } catch (\Exception $e) {
                    \Log::error('Failed to send credentials email', [
                        'email' => $contactEmail,
                        'error' => $e->getMessage()
                    ]);
                    // Don't fail the transaction if email fails
                }
            }

            // Send exhibitor registration confirmation email with payment link
            try {
                // Reload application with relationships for email
                $application->load(['country', 'state', 'eventContact']);
                $emailTo = $contact && $contact->email ? $contact->email : $contactEmail;
                Mail::to($emailTo)->send(new ExhibitorRegistrationMail($application, $invoice, $contact));
            } catch (\Exception $e) {
                \Log::error('Failed to send exhibitor registration email', [
                    'email' => $emailTo ?? $contactEmail,
                    'error' => $e->getMessage()
                ]);
                // Don't fail the transaction if email fails
            }

            // Delete draft
            $draft->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'application_id' => $application->application_id,
                'invoice_id' => $invoice->id,
                'message' => $passwordGenerated ? 'Login credentials sent to your email.' : 'Registration successful!',
                'redirect' => route('startup-zone.preview') . '?application_id=' . $application->application_id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show payment page
     */
    public function showPayment($applicationId)
    {
        $application = Application::where('application_id', $applicationId)
            ->where('application_type', 'startup-zone')
            ->firstOrFail();

        $invoice = Invoice::where('application_id', $application->id)->firstOrFail();

        // Get association logo if promocode exists
        $associationLogo = null;
        if ($application->promocode) {
            $association = AssociationPricingRule::where('promocode', $application->promocode)
                ->active()
                ->first();
            if ($association && $association->logo_path) {
                $associationLogo = asset('storage/' . $association->logo_path);
            }
        }
        view()->share('associationLogo', $associationLogo);

        return view('startup-zone.payment', compact('application', 'invoice'));
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request, $applicationId)
    {
        $application = Application::where('application_id', $applicationId)
            ->where('application_type', 'startup-zone')
            ->firstOrFail();

        $invoice = Invoice::where('application_id', $application->id)->firstOrFail();

        if ($invoice->payment_status === 'paid') {
            return redirect()->route('startup-zone.confirmation', $applicationId)
                ->with('info', 'Payment already processed');
        }

        // Redirect to payment gateway based on payment mode
        $paymentMethod = $request->input('payment_method', $invoice->currency === 'INR' ? 'CCAvenue' : 'PayPal');
        
        if ($paymentMethod === 'Bank Transfer') {
            // For bank transfer, show instructions or redirect to a page
            return redirect()->route('startup-zone.confirmation', $applicationId)
                ->with('info', 'Please contact us for bank transfer instructions.');
        } elseif ($paymentMethod === 'PayPal' || $invoice->currency === 'USD') {
            // PayPal
            return redirect()->route('paypal.form', ['id' => $invoice->invoice_no]);
        } else {
            // CCAvenue (default for INR)
            // Use application_id (TIN) for order_id format matching
            // The route will generate order_id as: {application_id}_{timestamp}
            return redirect()->route('payment.ccavenue', ['id' => $invoice->invoice_no]);
        }
    }

    /**
     * Show confirmation page (after payment success)
     */
    public function showConfirmation($applicationId)
    {
        $application = Application::where('application_id', $applicationId)
            ->where('application_type', 'startup-zone')
            ->firstOrFail();

        $invoice = Invoice::where('application_id', $application->id)->firstOrFail();
        $contact = EventContact::where('application_id', $application->id)->first();

        // Get association logo if promocode exists
        $associationLogo = null;
        if ($application->promocode) {
            $association = AssociationPricingRule::where('promocode', $application->promocode)
                ->active()
                ->first();
            if ($association && $association->logo_path) {
                $associationLogo = asset('storage/' . $association->logo_path);
            }
        }
        view()->share('associationLogo', $associationLogo);

        return view('startup-zone.confirmation', compact('application', 'invoice', 'contact'));
    }

    /**
     * Helper: Calculate form progress percentage
     */
    private function calculateProgress($draft)
    {
        $totalFields = 20; // Total number of fields
        $filledFields = 0;

        $fields = [
            'company_name', 'address', 'city_id', 'state_id', 'postal_code',
            'country_id', 'landline', 'website', 'company_email',
            'gst_compliance', 'pan_no', 'sector_id', 'subSector',
            'contact_data', 'payment_mode'
        ];

        foreach ($fields as $field) {
            if ($field === 'contact_data') {
                if ($draft->contact_data && !empty($draft->contact_data)) {
                    $filledFields += 1;
                }
            } elseif (isset($draft->$field) && $draft->$field) {
                $filledFields += 1;
            }
        }

        return round(($filledFields / $totalFields) * 100);
    }

    /**
     * Helper: Calculate progress from array data (for session-based storage)
     */
    private function calculateProgressFromData($data)
    {
        $totalFields = 20;
        $filledFields = 0;

        $fields = [
            'company_name', 'address', 'city_id', 'state_id', 'postal_code',
            'country_id', 'landline', 'website', 'company_email',
            'gst_compliance', 'pan_no', 'sector_id', 'subSector',
            'contact_data', 'payment_mode'
        ];

        foreach ($fields as $field) {
            if ($field === 'contact_data') {
                if (isset($data['contact_data']) && !empty($data['contact_data'])) {
                    $filledFields += 1;
                }
            } elseif (isset($data[$field]) && !empty($data[$field])) {
                $filledFields += 1;
            }
        }

        return round(($filledFields / $totalFields) * 100);
    }

    /**
     * Helper: Build validation rules from field configurations
     */
    private function buildValidationRules($fieldConfigs, $step)
    {
        $rules = [];

        $allFields = [
            'company_name', 'address', 'city_id', 'state_id', 'postal_code',
            'country_id', 'landline', 'website', 'company_email',
            'gst_compliance', 'gst_no', 'pan_no', 'sector_id', 'subSector',
            'certificate', 'how_old_startup', 'promocode', 'stall_category',
            'interested_sqm', 'type_of_business',
            'contact_title', 'contact_first_name', 'contact_last_name',
            'contact_designation', 'contact_email', 'contact_mobile', 'contact_country_code',
            'payment_mode'
        ];

        $stepFields = [
            'step1' => [
                'company_name', 'address', 'city_id', 'state_id', 'postal_code',
                'country_id', 'landline', 'website', 'company_email',
                'gst_compliance', 'gst_no', 'pan_no', 'sector_id', 'subSector',
                'certificate', 'how_old_startup', 'promocode', 'stall_category', 'interested_sqm'
            ],
            'step2' => [
                'contact_title', 'contact_first_name', 'contact_last_name',
                'contact_designation', 'contact_email', 'contact_mobile', 'contact_country_code'
            ],
            'step3' => ['payment_mode'],
            'all' => $allFields
        ];

        $fieldsToValidate = $stepFields[$step] ?? $allFields;

        foreach ($fieldsToValidate as $field) {
            $config = $fieldConfigs->get($field);
            if ($config && $config->is_required) {
                $rules[$field] = 'required';
                
                // Add custom validation rules
                if ($config->validation_rules) {
                    $rules[$field] .= '|' . implode('|', $config->validation_rules);
                }
            } else {
                $rules[$field] = 'nullable';
            }
        }

        // Add specific validations
        if (in_array('gst_no', $fieldsToValidate)) {
            $rules['gst_no'] = 'required_if:gst_compliance,1|nullable|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/';
        }
        if (in_array('pan_no', $fieldsToValidate)) {
            $rules['pan_no'] = 'required|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/';
        }
        if (in_array('contact_email', $fieldsToValidate)) {
            $rules['contact_email'] = 'required|email';
        }
        if (in_array('contact_mobile', $fieldsToValidate)) {
            // Validate contact_mobile (will be mapped from contact_mobile_national if present)
            $rules['contact_mobile'] = 'required|regex:/^[0-9]{10}$/';
        }
        if (in_array('certificate', $fieldsToValidate)) {
            $rules['certificate'] = 'nullable|file|mimes:pdf|max:2048';
        }
        if (in_array('website', $fieldsToValidate)) {
            $rules['website'] = 'required|url';
        }
        if (in_array('company_email', $fieldsToValidate)) {
            $rules['company_email'] = 'required|email';
        }
        if (in_array('postal_code', $fieldsToValidate)) {
            $rules['postal_code'] = 'required|regex:/^[0-9]{6}$/';
        }

        return $rules;
    }

    /**
     * Generate unique application_id using TIN_NO_PREFIX
     * Format: BTS-2026-EXH-XXXXXX (6-digit random number)
     */
    private function generateApplicationIdWithTinPrefix()
    {
        $prefix = config('constants.TIN_NO_PREFIX');
        $maxAttempts = 100; // Prevent infinite loop
        $attempts = 0;
        
        while ($attempts < $maxAttempts) {
            // Generate 6-digit random number
            $randomNumber = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $applicationId = $prefix . $randomNumber;
            $attempts++;
            
            // Check if it already exists
            $exists = Application::where('application_id', $applicationId)->exists();
            
            if (!$exists) {
                return $applicationId;
            }
        }
        
        // If we've tried too many times, use timestamp-based fallback
        $timestamp = substr(time(), -6); // Last 6 digits of timestamp
        $applicationId = $prefix . $timestamp;
        if (!Application::where('application_id', $applicationId)->exists()) {
            return $applicationId;
        }
        
        // Last resort: use microtime
        $microtime = substr(str_replace('.', '', microtime(true)), -6);
        return $prefix . $microtime;
    }

    /**
     * Helper: Validate draft data
     */
    private function validateDraft($draft, $fieldConfigs)
    {
        $errors = [];
        $valid = true;

        foreach ($fieldConfigs as $config) {
            if ($config->is_required) {
                $field = $config->field_name;
                $value = null;

                if ($field === 'contact_data') {
                    $value = $draft->contact_data;
                } else {
                    $value = $draft->$field ?? null;
                }

                if (empty($value)) {
                    $errors[$field] = "The {$config->field_label} field is required.";
                    $valid = false;
                }
            }
        }

        return ['valid' => $valid, 'errors' => $errors];
    }

    /**
     * Helper: Calculate pricing based on association
     */
    private function calculatePricing($draft)
    {
        $basePrice = 52000.00; // Default price
        $processingRate = 0.03; // 3% for Indian payments
        $currency = 'INR';

        // Get association pricing if promocode exists
        if ($draft->promocode) {
            $association = AssociationPricingRule::where('promocode', $draft->promocode)
                ->active()
                ->valid()
                ->first();

            if ($association) {
                $basePrice = $association->getEffectivePrice();
            }
        }

        // Determine currency and processing rate based on payment mode
        if ($draft->payment_mode === 'PayPal') {
            $currency = 'USD';
            $processingRate = 0.095; // 9.5% for PayPal
            // Convert INR to USD (approximate rate, should use actual exchange rate)
            $basePrice = $basePrice / 83; // Example: 1 USD = 83 INR
        }                                                                                                                                       

        $gst = $basePrice * 0.18; // 18% GST
        $processingCharges = ($basePrice + $gst) * $processingRate;
        $total = $basePrice + $gst + $processingCharges;

        return [
            'base_price' => round($basePrice, 2),
            'gst' => round($gst, 2),
            'processing_charges' => round($processingCharges, 2),
            'processing_rate' => $processingRate * 100,
            'total' => round($total, 2),
            'currency' => $currency
        ];
    }
}
