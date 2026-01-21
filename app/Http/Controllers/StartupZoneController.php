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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Mail\UserCredentialsMail;
use App\Mail\ExhibitorRegistrationMail;
use App\Mail\StartupZoneMail;

class StartupZoneController extends Controller
{
    /**
     * Show the multi-step registration form
     */
    public function showForm(Request $request)
    {
        // Get association from URL parameter
        $associationParam = $request->query('association');

        // Passing (tv) parameter in the URL will show the TV screen in the form
        
        // Check for TV parameter (case-insensitive for parameter name)
        // Check all possible case variations: tv, TV, Tv, tV
        $hasTV = false;
        
        // Method 1: Check using request->has() for common case variations
        if ($request->has('tv') || $request->has('TV') || $request->has('Tv') || $request->has('tV')) {
            $hasTV = true;
            session(['startup_zone_has_tv' => true]);
        } else {
            // Method 2: Check all query parameters case-insensitively
            $allQueryParams = $request->query();
            foreach ($allQueryParams as $key => $value) {
                if (strtolower($key) === 'tv') {
                    $hasTV = true;
                    session(['startup_zone_has_tv' => true]);
                    break;
                }
            }
        }
        
        // If TV parameter is NOT present in URL, clear the session flag
        // This ensures normal behavior when ?tv is removed
        if (!$hasTV) {
            session()->forget('startup_zone_has_tv');
        }

        // Log the hasTV value
        Log::info('hasTV value: ' . ($hasTV ? 'true' : 'false'));

        
        // end tv parameter check
        
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
        // $sectors = DB::table('sectors')->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $sectors = config('constants.sectors', []);
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
            'associationLogo',
            'hasTV'
        ));
    }

    /**
     * Verify Google reCAPTCHA response
     */
    private function verifyRecaptcha($recaptchaResponse)
    {
        // If disabled via config, always pass
        if (!config('constants.RECAPTCHA_ENABLED')) {
            return true;
        }

        $siteKey   = config('services.recaptcha.site_key');
        $projectId = config('services.recaptcha.project_id');
        $apiKey    = config('services.recaptcha.api_key');
        $expectedAction = 'submit';

        if (empty($siteKey) || empty($projectId) || empty($apiKey) || empty($recaptchaResponse)) {
            Log::warning('reCAPTCHA config or token missing', [
                'siteKey' => !empty($siteKey),
                'projectId' => $projectId,
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
                    'token'          => $recaptchaResponse,
                    'expectedAction' => $expectedAction,
                    'siteKey'        => $siteKey,
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

            // Optional: you can also check riskAnalysis.score if you want a threshold
            return true;
        } catch (\Exception $e) {
            Log::error('reCAPTCHA Enterprise verification error', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Normalize website URL - add https:// if protocol is missing
     */
    private function normalizeWebsiteUrl($url)
    {
        if (empty($url)) {
            return $url;
        }
        
        $url = trim($url);
        
        // If URL doesn't start with http:// or https://, add https://
        if (!preg_match('/^https?:\/\//i', $url)) {
            $url = 'https://' . $url;
        }
        
        return $url;
    }

    /**
     * Store form data in session (lightweight, no database writes)
     * NOTE: This method does NOT check for duplicate applications.
     * Duplicate checking only happens in restoreDraftToApplication() on final submission.
     */
    public function autoSave(Request $request)
    {
        // Store all form data in session - no database writes until submit
        // No duplicate checking here - this is just session storage for draft data
        $formData = $request->except(['_token', 'certificate']);
        
        // Handle billing data
        $billingTelephoneNational = '';
        $billingTelephoneCountryCode = '91'; // Default to India
        
        if ($request->has('billing_telephone_national') && $request->input('billing_telephone_national')) {
            // Extract only digits from telephone national number (remove alphabets and special chars)
            $billingTelephoneNational = preg_replace('/[^0-9]/', '', $request->input('billing_telephone_national'));
            // Extract only digits from country code
            $billingTelephoneCountryCode = preg_replace('/[^0-9]/', '', $request->input('billing_telephone_country_code') ?: '91');
        } elseif ($request->has('billing_telephone') && $request->input('billing_telephone')) {
            // Extract only digits from telephone value (remove alphabets and special chars)
            $billingTelephoneValue = preg_replace('/[^0-9]/', '', trim($request->input('billing_telephone')));
            if (preg_match('/^(\d{1,3})(\d+)$/', $billingTelephoneValue, $matches)) {
                $billingTelephoneCountryCode = $matches[1];
                $billingTelephoneNational = $matches[2];
            } else {
                // If format doesn't match, use all extracted digits as national number
                $billingTelephoneNational = $billingTelephoneValue;
            }
        }
        
        $billingData = [
            'company_name' => $request->input('billing_company_name'),
            'address' => $request->input('billing_address'),
            'country_id' => $request->input('billing_country_id'),
            'state_id' => $request->input('billing_state_id'),
            'city' => $request->input('billing_city'),
            'postal_code' => $request->input('billing_postal_code'),
            'telephone' => $billingTelephoneNational ? ($billingTelephoneCountryCode . '-' . $billingTelephoneNational) : '',
            'website' => $this->normalizeWebsiteUrl($request->input('billing_website') ?? ''),
            'email' => $request->input('billing_email'),
        ];
        
        if (!empty($billingData)) {
            $formData['billing_data'] = $billingData;
        }
        
        // Handle file upload separately (if provided)
        if ($request->hasFile('certificate')) {
            $file = $request->file('certificate');
            
            // Get company name for filename
            $companyName = $request->input('billing_company_name') 
                        ?? $request->input('exhibitor_name') 
                        ?? $request->input('company_name')
                        ?? 'company';
            
            // Sanitize company name for filename (handle special characters and Unicode)
            // Convert to lowercase for consistency
            $companyName = mb_strtolower($companyName, 'UTF-8');
            
            // Remove accents and convert to ASCII equivalents (handles é, ñ, ü, etc.)
            $companyName = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $companyName);
            
            // Replace spaces and special characters with underscores
            $companyName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $companyName);
            
            // Remove multiple consecutive underscores
            $companyName = preg_replace('/_+/', '_', $companyName);
            
            // Remove leading/trailing underscores
            $companyName = trim($companyName, '_');
            
            // Limit length to prevent overly long filenames
            $companyName = substr($companyName, 0, 50);
            
            // If empty after sanitization, use default
            if (empty($companyName)) {
                $companyName = 'company';
            }
            
            // Generate filename: CompanyName_YYYYMMDD_HHMMSS.pdf
            $timestamp = now()->format('Ymd_His');
            $extension = $file->getClientOriginalExtension();
            $filename = $companyName . '_' . $timestamp . '.' . $extension;
            
            $path = $file->storeAs('startup-zone/certificates', $filename, 'public');
            $formData['certificate_path'] = $path;
        }
        
        // Build contact data from individual fields
        // Format mobile as country_code-national_number (e.g., 91-9801217815)
        $mobileNational = '';
        $mobileCountryCode = '91'; // Default to India
        
        if ($request->has('contact_mobile_national') && $request->input('contact_mobile_national')) {
            // Extract only digits from national number (remove spaces and non-digits)
            $mobileNational = preg_replace('/[^0-9]/', '', trim($request->input('contact_mobile_national')));
            // Extract only digits from country code (remove + and non-digits)
            $mobileCountryCode = preg_replace('/[^0-9]/', '', $request->input('contact_country_code') ?: '91');
        } elseif ($request->has('contact_mobile') && $request->input('contact_mobile')) {
            // If mobile is provided directly, extract and format
            $mobileValue = preg_replace('/[^0-9]/', '', trim($request->input('contact_mobile')));
            // Try to extract country code if present (first 1-4 digits as country code, rest as national)
            if (strlen($mobileValue) > 10) {
                // Assume last 10 digits are national number for Indian numbers
                $mobileNational = substr($mobileValue, -10);
                $mobileCountryCode = substr($mobileValue, 0, -10) ?: '91';
            } else {
                $mobileNational = $mobileValue;
            }
        }
        
        $contactData = [
            'title' => $request->input('contact_title'),
            'first_name' => $request->input('contact_first_name'),
            'last_name' => $request->input('contact_last_name'),
            'designation' => $request->input('contact_designation'),
            'email' => $request->input('contact_email'),
            'mobile' => $mobileNational ? ($mobileCountryCode . '-' . $mobileNational) : '', // Format as country_code-national_number
            'country_code' => $mobileCountryCode,
        ];
        
        if (!empty($contactData)) {
            $formData['contact_data'] = $contactData;
        }
        
        // Handle exhibitor data
        $exhibitorTelephoneNational = '';
        $exhibitorTelephoneCountryCode = '91'; // Default to India
        
        if ($request->has('exhibitor_telephone_national') && $request->input('exhibitor_telephone_national')) {
            // Extract only digits from telephone national number (remove alphabets and special chars)
            $exhibitorTelephoneNational = preg_replace('/[^0-9]/', '', $request->input('exhibitor_telephone_national'));
            // Extract only digits from country code
            $exhibitorTelephoneCountryCode = preg_replace('/[^0-9]/', '', $request->input('exhibitor_telephone_country_code') ?: '91');
        } elseif ($request->has('exhibitor_telephone') && $request->input('exhibitor_telephone')) {
            // Extract only digits from telephone value (remove alphabets and special chars)
            $exhibitorTelephoneValue = preg_replace('/[^0-9]/', '', trim($request->input('exhibitor_telephone')));
            if (preg_match('/^(\d{1,3})(\d+)$/', $exhibitorTelephoneValue, $matches)) {
                $exhibitorTelephoneCountryCode = $matches[1];
                $exhibitorTelephoneNational = $matches[2];
            } else {
                // If format doesn't match, use all extracted digits as national number
                $exhibitorTelephoneNational = $exhibitorTelephoneValue;
            }
        }
        
        $exhibitorData = [
            'name' => $request->input('exhibitor_name'),
            'address' => $request->input('exhibitor_address'),
            'country_id' => $request->input('exhibitor_country_id'),
            'state_id' => $request->input('exhibitor_state_id'),
            'city' => $request->input('exhibitor_city'),
            'postal_code' => $request->input('exhibitor_postal_code'),
            'telephone' => $exhibitorTelephoneNational ? ($exhibitorTelephoneCountryCode . '-' . $exhibitorTelephoneNational) : '',
            'website' => $this->normalizeWebsiteUrl($request->input('exhibitor_website') ?? ''),
            'email' => $request->input('exhibitor_email'),
        ];
        
        if (!empty($exhibitorData)) {
            $formData['exhibitor_data'] = $exhibitorData;
        }
        
        // Handle landline: format as country_code-national_number (e.g., 91-9801217815)
        if ($request->has('landline_national') && $request->input('landline_national')) {
            // Extract only digits from landline national number (remove alphabets and special chars)
            $landlineNational = preg_replace('/[^0-9]/', '', $request->input('landline_national'));
            // Extract only digits from country code (remove + and non-digits)
            $landlineCountryCode = preg_replace('/[^0-9]/', '', $request->input('landline_country_code') ?: '91');
            $formData['landline'] = $landlineCountryCode . '-' . $landlineNational;
        } elseif ($request->has('landline') && $request->input('landline')) {
            // If landline is provided directly, extract and format
            $landlineValue = preg_replace('/[^0-9]/', '', trim($request->input('landline')));
            if (strlen($landlineValue) > 10) {
                // Assume last 10 digits are national number
                $landlineNational = substr($landlineValue, -10);
                $landlineCountryCode = substr($landlineValue, 0, -10) ?: '91';
                $formData['landline'] = $landlineCountryCode . '-' . $landlineNational;
            } elseif (!empty($landlineValue)) {
                $formData['landline'] = '91-' . $landlineValue; // Default to India
            }
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
            // reCAPTCHA temporarily disabled
            
            // FIRST: Save latest form data to session before processing
            // This ensures we always use the latest values from the form
            $this->saveFormDataToSession($request);
            
            $fieldConfigs = FormFieldConfiguration::currentVersion()
                ->active()
                ->byFormType('startup-zone')
                ->get()
                ->keyBy('field_name');

            // Build validation rules for all fields
            $rules = $this->buildValidationRules($fieldConfigs, 'all');
            
            // Get fresh session data (just saved above) and merge with request data
            // Request data takes precedence over session data
            $sessionData = session('startup_zone_draft', []);
            $allData = array_merge($sessionData, $request->all());
            
            // Map new billing field names to old field names for validation compatibility
            // Billing fields -> map to old names for validation
            if ($request->has('billing_postal_code')) {
                $allData['postal_code'] = $request->input('billing_postal_code');
            }
            if ($request->has('billing_email')) {
                $allData['company_email'] = $request->input('billing_email');
            }
            if ($request->has('billing_company_name')) {
                $allData['company_name'] = $request->input('billing_company_name');
            }
            if ($request->has('billing_address')) {
                $allData['address'] = $request->input('billing_address');
            }
            if ($request->has('billing_country_id')) {
                $allData['country_id'] = $request->input('billing_country_id');
            }
            if ($request->has('billing_state_id')) {
                $allData['state_id'] = $request->input('billing_state_id');
            }
            if ($request->has('billing_city')) {
                $allData['city_id'] = $request->input('billing_city');
            }
            if ($request->has('billing_telephone_national') && !empty($request->input('billing_telephone_national'))) {
                // Extract only digits from national number and country code
                $landlineNational = preg_replace('/[^0-9]/', '', $request->input('billing_telephone_national'));
                $landlineCountryCode = preg_replace('/[^0-9]/', '', $request->input('billing_telephone_country_code') ?: '91');
                $allData['landline'] = $landlineCountryCode . '-' . $landlineNational;
            } elseif ($request->has('billing_telephone') && !empty($request->input('billing_telephone'))) {
                // Extract only digits (remove alphabets and special chars)
                $landlineValue = preg_replace('/[^0-9]/', '', $request->input('billing_telephone'));
                if (strlen($landlineValue) > 10) {
                    $landlineNational = substr($landlineValue, -10);
                    $landlineCountryCode = substr($landlineValue, 0, -10) ?: '91';
                    $allData['landline'] = $landlineCountryCode . '-' . $landlineNational;
                } else {
                    $allData['landline'] = '91-' . $landlineValue;
                }
            }
            if ($request->has('billing_website')) {
                $allData['website'] = $request->input('billing_website');
            }
            
            // For intl-tel-input fields, validate the national number instead
            // Map contact_mobile_national to contact_mobile for validation
            if ($request->has('contact_mobile_national') && !empty($request->input('contact_mobile_national'))) {
                $allData['contact_mobile'] = $request->input('contact_mobile_national');
            }
            if ($request->has('landline_national') && !empty($request->input('landline_national'))) {
                // Extract only digits from national number and country code
                $landlineNational = preg_replace('/[^0-9]/', '', $request->input('landline_national'));
                $landlineCountryCode = preg_replace('/[^0-9]/', '', $request->input('landline_country_code') ?: '91');
                $allData['landline'] = $landlineCountryCode . '-' . $landlineNational;
            }
            
            // CRITICAL: Check if contact email already exists in users table - BLOCK SUBMISSION
            $contactEmail = $request->input('contact_email');
            if (!empty($contactEmail) && $this->checkEmailExists(trim($contactEmail))) {
                // Email already exists - return error immediately and STOP processing
                return response()->json([
                    'success' => false,
                    'message' => 'This email is already registered. Please use a different email address.',
                    'errors' => [
                        'contact_email' => ['Email already exists']
                    ]
                ], 422);
            }
            
            // SERVER-SIDE GST VALIDATION: When GST is provided, enforce PAN and Country
            $gstNo = $request->input('gst_no');
            $gstCompliance = $request->input('gst_compliance');
            
            if ($gstCompliance == '1' && !empty($gstNo) && strlen($gstNo) >= 12) {
                // Extract PAN from GST number (characters 3-12, 0-indexed: positions 2-11)
                $extractedPan = strtoupper(substr($gstNo, 2, 10));
                
                // Auto-set PAN from GST (server-side enforcement)
                $allData['pan_no'] = $extractedPan;
                
                // Ensure country is India (ID 101) when GST is provided
                $india = \App\Models\Country::where('name', 'India')->first();
                if ($india) {
                    $allData['country_id'] = $india->id;
                    $allData['billing_country_id'] = $india->id;
                }
                
                // Log for audit
                \Log::info('GST Validation - Server-side enforcement', [
                    'gst_no' => $gstNo,
                    'extracted_pan' => $extractedPan,
                    'submitted_pan' => $request->input('pan_no'),
                    'country_set_to' => $india ? $india->id : 'not found'
                ]);
            }
            
            // Custom validation messages
            $customMessages = [
                'certificate.max' => 'The certificate field must not be greater than 2mb.',
            ];
            
            $validator = Validator::make($allData, $rules, $customMessages);

            if ($validator->fails()) {
                // Log validation errors for debugging
                \Log::info('Startup Zone Form Validation Failed', [
                    'errors' => $validator->errors()->toArray(),
                    'data_keys' => array_keys($allData),
                    'rules' => $rules
                ]);
                
                // Map validation errors back to frontend field names
                $errors = $validator->errors();
                $mappedErrors = [];
                
                foreach ($errors->messages() as $field => $messages) {
                    // Map old field names back to new field names for frontend
                    if ($field === 'postal_code') {
                        $mappedErrors['billing_postal_code'] = $messages;
                    } elseif ($field === 'company_email') {
                        $mappedErrors['billing_email'] = $messages;
                    } elseif ($field === 'company_name') {
                        $mappedErrors['billing_company_name'] = $messages;
                    } elseif ($field === 'address') {
                        $mappedErrors['billing_address'] = $messages;
                    } elseif ($field === 'country_id') {
                        $mappedErrors['billing_country_id'] = $messages;
                    } elseif ($field === 'state_id') {
                        $mappedErrors['billing_state_id'] = $messages;
                    } elseif ($field === 'city_id') {
                        $mappedErrors['billing_city'] = $messages;
                    } elseif ($field === 'landline') {
                        $mappedErrors['billing_telephone'] = $messages;
                    } elseif ($field === 'website') {
                        $mappedErrors['billing_website'] = $messages;
                    } else {
                        $mappedErrors[$field] = $messages;
                    }
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Please fix the validation errors below.',
                    'errors' => $mappedErrors
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
                
                // Get company name for filename
                $companyName = $request->input('billing_company_name') 
                            ?? $request->input('exhibitor_name') 
                            ?? $request->input('company_name')
                            ?? $draft->company_name
                            ?? 'company';
                
                // Sanitize company name for filename (handle special characters and Unicode)
                // Convert to lowercase for consistency
                $companyName = mb_strtolower($companyName, 'UTF-8');
                
                // Remove accents and convert to ASCII equivalents
                $companyName = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $companyName);
                
                // Replace spaces and special characters with underscores
                $companyName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $companyName);
                
                // Remove multiple consecutive underscores
                $companyName = preg_replace('/_+/', '_', $companyName);
                
                // Remove leading/trailing underscores
                $companyName = trim($companyName, '_');
                
                // Limit length to prevent overly long filenames
                $companyName = substr($companyName, 0, 50);
                
                // If empty after sanitization, use default
                if (empty($companyName)) {
                    $companyName = 'company';
                }
                
                // Generate filename: CompanyName_YYYYMMDD_HHMMSS.pdf
                $timestamp = now()->format('Ymd_His');
                $extension = $file->getClientOriginalExtension();
                $filename = $companyName . '_' . $timestamp . '.' . $extension;
                
                $path = $file->storeAs('startup-zone/certificates', $filename, 'public');
                $draft->certificate_path = $path;
            } elseif (isset($sessionData['certificate_path'])) {
                $draft->certificate_path = $sessionData['certificate_path'];
            }

            // Handle landline: format as country_code-national_number (e.g., 91-9801217815)
            $landlineData = [];
            if ($request->has('landline_national') && $request->input('landline_national')) {
                // Extract only digits from landline national number (remove alphabets and special chars)
                $landlineNational = preg_replace('/[^0-9]/', '', $request->input('landline_national'));
                // Extract only digits from country code
                $landlineCountryCode = preg_replace('/[^0-9]/', '', $request->input('landline_country_code') ?: '91');
                $landlineData['landline'] = $landlineCountryCode . '-' . $landlineNational;
            } elseif ($request->has('landline') && $request->input('landline')) {
                // Extract only digits from landline value (remove alphabets and special chars)
                $landlineValue = preg_replace('/[^0-9]/', '', trim($request->input('landline')));
                // If already in format country_code-national_number, keep it; otherwise format it
                if (!preg_match('/^\d{1,3}-\d+$/', $landlineValue)) {
                    // Try to extract country code if present (only digits)
                    if (preg_match('/^(\d{1,3})(\d+)$/', $landlineValue, $matches)) {
                        $landlineData['landline'] = $matches[1] . '-' . $matches[2];
                    } else {
                        $landlineData['landline'] = '91-' . $landlineValue; // Default to India
                    }
                } else {
                    $landlineData['landline'] = $landlineValue;
                }
            }
            
            // Update draft with all form fields
            $formFields = array_merge($sessionData, $landlineData, $request->only([
                'stall_category', 'interested_sqm',
                'how_old_startup', 'gst_compliance', 'gst_no', 'pan_no',
                'sector_id', 'subSector', 'type_of_business',
                'promocode', 'assoc_mem', 'RegSource', 'payment_mode'
            ]));
            
            // Map billing_ prefixed fields to draft columns
            if ($request->has('billing_company_name')) {
                $formFields['company_name'] = $request->input('billing_company_name');
            }
            if ($request->has('billing_address')) {
                $formFields['address'] = $request->input('billing_address');
            }
            if ($request->has('billing_city')) {
                $formFields['city_id'] = $request->input('billing_city');
            }
            if ($request->has('billing_state_id')) {
                $formFields['state_id'] = $request->input('billing_state_id');
            }
            if ($request->has('billing_postal_code')) {
                $formFields['postal_code'] = $request->input('billing_postal_code');
            }
            if ($request->has('billing_country_id')) {
                $formFields['country_id'] = $request->input('billing_country_id');
            }
            if ($request->has('billing_website')) {
                $formFields['website'] = $this->normalizeWebsiteUrl($request->input('billing_website'));
            }
            if ($request->has('billing_email')) {
                $formFields['company_email'] = $request->input('billing_email');
            }
            
            // Landline is already handled above in $landlineData
            if (isset($landlineData['landline'])) {
                $formFields['landline'] = $landlineData['landline'];
            }
            
            // Normalize website URL - add https:// if missing (if not already set from billing_website above)
            if (isset($formFields['website']) && !empty($formFields['website']) && !$request->has('billing_website')) {
                $formFields['website'] = $this->normalizeWebsiteUrl($formFields['website']);
            }
            
            // Validate foreign key relationships before saving
            if (isset($formFields['state_id']) && $formFields['state_id']) {
                $stateExists = State::where('id', $formFields['state_id'])->exists();
                if (!$stateExists) {
                    \Log::warning('Invalid state_id provided', [
                        'state_id' => $formFields['state_id'],
                        'session_id' => $sessionId
                    ]);
                    // Set to null if state doesn't exist to avoid foreign key violation
                    $formFields['state_id'] = null;
                }
            }
            
            if (isset($formFields['country_id']) && $formFields['country_id']) {
                $countryExists = Country::where('id', $formFields['country_id'])->exists();
                if (!$countryExists) {
                    \Log::warning('Invalid country_id provided', [
                        'country_id' => $formFields['country_id'],
                        'session_id' => $sessionId
                    ]);
                    // Set to null if country doesn't exist to avoid foreign key violation
                    $formFields['country_id'] = null;
                }
            }
            
            // Determine currency from payment_mode if not explicitly set
            if (!isset($formFields['currency']) && isset($formFields['payment_mode'])) {
                $formFields['currency'] = $formFields['payment_mode'] === 'PayPal' ? 'USD' : 'INR';
            }
            
            $draft->fill($formFields);

            // Store billing_data and exhibitor_data from session (these are not in fillable)
            if (isset($sessionData['billing_data'])) {
                $draft->billing_data = $sessionData['billing_data'];
            }
            if (isset($sessionData['exhibitor_data'])) {
                $draft->exhibitor_data = $sessionData['exhibitor_data'];
            }
            
            // Calculate and store pricing data
            $pricing = $this->calculatePricing($draft);
            $draft->pricing_data = $pricing;
            
            // Ensure currency is stored
            if (!isset($draft->currency)) {
                $draft->currency = $pricing['currency'];
            }

            // Store contact data as JSON (from session or request)
            if (isset($sessionData['contact_data'])) {
                $draft->contact_data = $sessionData['contact_data'];
            } else {
                // Format mobile as country_code-national_number (e.g., 91-9801217815)
                $mobileNational = '';
                $mobileCountryCode = '91'; // Default to India
                
                if ($request->has('contact_mobile_national') && $request->input('contact_mobile_national')) {
                    // Extract only digits from national number (remove spaces and non-digits)
                    $mobileNational = preg_replace('/[^0-9]/', '', trim($request->input('contact_mobile_national')));
                    // Extract only digits from country code (remove + and non-digits)
                    $mobileCountryCode = preg_replace('/[^0-9]/', '', $request->input('contact_country_code') ?: '91');
                } elseif ($request->has('contact_mobile') && $request->input('contact_mobile')) {
                    // If mobile is provided directly, extract and format
                    $mobileValue = preg_replace('/[^0-9]/', '', trim($request->input('contact_mobile')));
                    // Try to extract country code if present (first 1-4 digits as country code, rest as national)
                    if (strlen($mobileValue) > 10) {
                        // Assume last 10 digits are national number for Indian numbers
                        $mobileNational = substr($mobileValue, -10);
                        $mobileCountryCode = substr($mobileValue, 0, -10) ?: '91';
                    } else {
                        $mobileNational = $mobileValue;
                    }
                }
                
                $contactData = [
                    'title' => $request->input('contact_title'),
                    'first_name' => $request->input('contact_first_name'),
                    'last_name' => $request->input('contact_last_name'),
                    'designation' => $request->input('contact_designation'),
                    'email' => $request->input('contact_email'),
                    'mobile' => $mobileNational ? ($mobileCountryCode . '-' . $mobileNational) : '', // Format as country_code-national_number
                    'country_code' => $mobileCountryCode,
                ];
                $draft->contact_data = $contactData;
            }

            $draft->progress_percentage = $this->calculateProgress($draft);
            $draft->save();
            
            // SAFEGUARD: Verify no application was created (should never happen in submitForm)
            // This method should ONLY save to draft, never create applications
            $sessionId = session()->getId();
            
            \Log::info('submitForm: Draft saved, NOT creating application', [
                'draft_id' => $draft->id,
                'session_id' => $sessionId,
                'message' => 'Data saved to startup_zone_drafts only. Application will be created when user clicks Proceed to Payment.'
            ]);
            
            \Log::info('submitForm: Draft saved, NOT creating application', [
                'draft_id' => $draft->id,
                'session_id' => $sessionId,
                'message' => 'Data saved to startup_zone_drafts only. Application will be created when user clicks Proceed to Payment.'
            ]);

            // Return success with redirect to preview page
            // User will click "Proceed to Payment" on preview page to create application
            return response()->json([
                'success' => true,
                'message' => 'Form submitted successfully! Please review your details.',
                'progress' => $draft->progress_percentage,
                'redirect' => route('startup-zone.preview')
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
            $applicationId = $request->query('application_id');
            
            // Security: Verify ownership using session
            $sessionApplicationId = session('startup_zone_application_id');
            if ($sessionApplicationId && $sessionApplicationId !== $applicationId) {
                // Unauthorized access attempt
                abort(403, 'Unauthorized access to this application');
            }
            
            $application = Application::where('application_id', $applicationId)
                ->where('application_type', 'startup-zone')
                ->firstOrFail();
            
            $invoice = Invoice::where('application_id', $application->id)->firstOrFail();
            $contact = EventContact::where('application_id', $application->id)->first();
            $billingDetail = \App\Models\BillingDetail::where('application_id', $application->id)->first();
            
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
        
        // Check if TV parameter is set (from session)
        $hasTV = session('startup_zone_has_tv', false);
        
        return view('startup-zone.preview', compact('application', 'invoice', 'contact', 'billingDetail', 'hasTV'));
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
        
        // Check if TV parameter is set (from session)
        $hasTV = session('startup_zone_has_tv', false);
        
        // Get pricing from draft pricing_data (preferred) or calculate
        $pricing = $draft->pricing_data ?? $this->calculatePricing($draft);
        
        // If pricing not in draft, store it
        if (!$draft->pricing_data) {
            $draft->pricing_data = $pricing;
            $draft->currency = $pricing['currency'];
            $draft->save();
        }
        
        // Get currency from draft
        $currency = $draft->currency ?? $pricing['currency'] ?? 'INR';

        return view('startup-zone.preview', compact('draft', 'pricing', 'hasTV', 'currency'));
    }

    /**
     * Restore draft to application (final submission)
     * Now accepts form data directly via POST for validation and processing
     */
    public function restoreDraftToApplication(Request $request)
    {
        try {
            // reCAPTCHA temporarily disabled for final submission
            
            // FIRST: Always save latest form data to session (if provided)
            // This ensures we always use the latest values from the form
            if ($request->hasAny([
                'billing_company_name', 'billing_email', 'billing_address',
                'exhibitor_name', 'exhibitor_email', 'exhibitor_address',
                'contact_email', 'contact_first_name', 'stall_category'
            ])) {
                $this->saveFormDataToSession($request);
            }
            
        $sessionId = session()->getId();
        $draft = StartupZoneDraft::bySession($sessionId)->active()->firstOrFail();

            // Get field configurations for validation
        $fieldConfigs = FormFieldConfiguration::currentVersion()
            ->active()
            ->byFormType('startup-zone')
            ->get()
            ->keyBy('field_name');

            // Build validation rules
            $rules = $this->buildValidationRules($fieldConfigs, 'all');
            
            // Get fresh session data and draft data
            // Since preview page sends empty FormData, we need to extract from session/draft
            $sessionData = session('startup_zone_draft', []);
            
            // Extract billing_data, exhibitor_data, contact_data from session
            $billingData = $sessionData['billing_data'] ?? null;
            $billingData = is_string($billingData) ? json_decode($billingData, true) : $billingData;
            
            $exhibitorData = $sessionData['exhibitor_data'] ?? null;
            $exhibitorData = is_string($exhibitorData) ? json_decode($exhibitorData, true) : $exhibitorData;
            
            $contactData = $sessionData['contact_data'] ?? null;
            $contactData = is_string($contactData) ? json_decode($contactData, true) : $contactData;
            
            // Also check draft for data (fallback)
            $draftBillingData = $draft->billing_data ?? null;
            $draftBillingData = is_string($draftBillingData) ? json_decode($draftBillingData, true) : $draftBillingData;
            
            $draftExhibitorData = $draft->exhibitor_data ?? null;
            $draftExhibitorData = is_string($draftExhibitorData) ? json_decode($draftExhibitorData, true) : $draftExhibitorData;
            
            $draftContactData = $draft->contact_data ?? null;
            $draftContactData = is_string($draftContactData) ? json_decode($draftContactData, true) : $draftContactData;
            
            // Use session data first, then draft data as fallback
            $billingData = $billingData ?? $draftBillingData;
            $exhibitorData = $exhibitorData ?? $draftExhibitorData;
            $contactData = $contactData ?? $draftContactData;
            
            // Build allData for validation - map to old field names
            $allData = array_merge($sessionData, $request->all());
            
            // Map billing data to old field names (for validation)
            if ($billingData) {
                if (isset($billingData['postal_code'])) {
                    $allData['postal_code'] = $billingData['postal_code'];
                }
                if (isset($billingData['email'])) {
                    $allData['company_email'] = $billingData['email'];
                }
                if (isset($billingData['company_name'])) {
                    $allData['company_name'] = $billingData['company_name'];
                }
                if (isset($billingData['address'])) {
                    $allData['address'] = $billingData['address'];
                }
                if (isset($billingData['country_id'])) {
                    $allData['country_id'] = $billingData['country_id'];
                }
                if (isset($billingData['state_id'])) {
                    $allData['state_id'] = $billingData['state_id'];
                }
                if (isset($billingData['city'])) {
                    $allData['city_id'] = $billingData['city'];
                }
                if (isset($billingData['telephone'])) {
                    // Keep telephone in format country_code-national_number
                    $telephoneValue = $billingData['telephone'];
                    // If already in format "91-9801217815", keep it
                    if (preg_match('/^(\d+)-(\d+)$/', $telephoneValue, $matches)) {
                        $allData['landline'] = $matches[1] . '-' . $matches[2]; // Keep country code with national number
                    } else {
                        // Extract only digits and add default country code
                        $digits = preg_replace('/[^0-9]/', '', $telephoneValue);
                        if (strlen($digits) > 10) {
                            $allData['landline'] = substr($digits, 0, -10) . '-' . substr($digits, -10);
                        } else {
                            $allData['landline'] = '91-' . $digits;
                        }
                    }
                }
                if (isset($billingData['website'])) {
                    $allData['website'] = $billingData['website'];
                }
            }
            
            // Map request data if provided (takes precedence)
            if ($request->has('billing_postal_code')) {
                $allData['postal_code'] = $request->input('billing_postal_code');
            }
            if ($request->has('billing_email')) {
                $allData['company_email'] = $request->input('billing_email');
            }
            if ($request->has('billing_company_name')) {
                $allData['company_name'] = $request->input('billing_company_name');
            }
            if ($request->has('billing_address')) {
                $allData['address'] = $request->input('billing_address');
            }
            if ($request->has('billing_country_id')) {
                $allData['country_id'] = $request->input('billing_country_id');
            }
            if ($request->has('billing_state_id')) {
                $allData['state_id'] = $request->input('billing_state_id');
            }
            if ($request->has('billing_city')) {
                $allData['city_id'] = $request->input('billing_city');
            }
            if ($request->has('billing_telephone_national') && !empty($request->input('billing_telephone_national'))) {
                // Extract only digits from national number and country code
                $landlineNational = preg_replace('/[^0-9]/', '', $request->input('billing_telephone_national'));
                $landlineCountryCode = preg_replace('/[^0-9]/', '', $request->input('billing_telephone_country_code') ?: '91');
                $allData['landline'] = $landlineCountryCode . '-' . $landlineNational;
            } elseif ($request->has('billing_telephone') && !empty($request->input('billing_telephone'))) {
                // Extract only digits (remove alphabets and special chars)
                $landlineValue = preg_replace('/[^0-9]/', '', $request->input('billing_telephone'));
                if (strlen($landlineValue) > 10) {
                    $landlineNational = substr($landlineValue, -10);
                    $landlineCountryCode = substr($landlineValue, 0, -10) ?: '91';
                    $allData['landline'] = $landlineCountryCode . '-' . $landlineNational;
                } else {
                    $allData['landline'] = '91-' . $landlineValue;
                }
            }
            if ($request->has('billing_website')) {
                $allData['website'] = $request->input('billing_website');
            }
            
            // Map contact data
            if ($contactData) {
                if (isset($contactData['email'])) {
                    $allData['contact_email'] = $contactData['email'];
                }
                if (isset($contactData['mobile']) && !empty($contactData['mobile'])) {
                    // Extract national number from various formats
                    // Stored format is typically "91-9806575432" (country_code-national_number)
                    $mobile = trim($contactData['mobile']);
                    
                    \Log::info('Extracting contact mobile', [
                        'original_mobile' => $mobile,
                        'mobile_type' => gettype($mobile)
                    ]);
                    
                    // Remove all spaces first
                    $mobile = preg_replace('/\s+/', '', $mobile);
                    
                    // Try to match format with hyphen: "91-9806575432" or "91-91-9806575432"
                    // Extract the 10 digits after the last hyphen (most reliable method)
                    if (preg_match('/-(\d{10})$/', $mobile, $matches)) {
                        $allData['contact_mobile'] = $matches[1];
                        \Log::info('Mobile extracted using hyphen pattern', ['extracted' => $allData['contact_mobile']]);
                    }
                    // Try to match format with optional + and country code at start: "+91-9806575432"
                    elseif (preg_match('/^\+?\d{1,3}-(\d{10})$/', $mobile, $matches)) {
                        $allData['contact_mobile'] = $matches[1];
                        \Log::info('Mobile extracted using + pattern', ['extracted' => $allData['contact_mobile']]);
                    }
                    // Try to match format like "+919801217815" or "919801217815" (no hyphen)
                    elseif (preg_match('/^\+?(\d{1,4})(\d{10})$/', $mobile, $matches)) {
                        $allData['contact_mobile'] = $matches[2];
                        \Log::info('Mobile extracted using no-hyphen pattern', ['extracted' => $allData['contact_mobile']]);
                    }
                    // Fallback: extract all digits and get last 10
                    else {
                        $digitsOnly = preg_replace('/[^0-9]/', '', $mobile);
                        if (strlen($digitsOnly) >= 10) {
                            // Get last 10 digits (handles cases like "91919806575432" -> "9806575432")
                            $allData['contact_mobile'] = substr($digitsOnly, -10);
                            \Log::info('Mobile extracted using fallback (last 10 digits)', [
                                'all_digits' => $digitsOnly,
                                'extracted' => $allData['contact_mobile']
                            ]);
                        } elseif (strlen($digitsOnly) > 0) {
                            $allData['contact_mobile'] = $digitsOnly;
                            \Log::warning('Mobile extracted but less than 10 digits', [
                                'extracted' => $allData['contact_mobile'],
                                'length' => strlen($allData['contact_mobile'])
                            ]);
                        } else {
                            \Log::error('Failed to extract mobile number', ['original' => $contactData['mobile']]);
                        }
                    }
                    
                    // Final validation: ensure we have exactly 10 digits
                    if (isset($allData['contact_mobile'])) {
                        // Ensure it's a string and contains only digits
                        $allData['contact_mobile'] = (string) $allData['contact_mobile'];
                        $allData['contact_mobile'] = preg_replace('/[^0-9]/', '', $allData['contact_mobile']);
                        
                        // If we have more than 10 digits, get last 10
                        if (strlen($allData['contact_mobile']) > 10) {
                            $allData['contact_mobile'] = substr($allData['contact_mobile'], -10);
                        }
                        
                        if (strlen($allData['contact_mobile']) !== 10) {
                            \Log::warning('Contact mobile extraction result is not 10 digits', [
                                'original' => $contactData['mobile'],
                                'extracted' => $allData['contact_mobile'],
                                'length' => strlen($allData['contact_mobile'])
                            ]);
                        } else {
                            \Log::info('Contact mobile successfully extracted (10 digits)', [
                                'extracted' => $allData['contact_mobile']
                            ]);
                        }
                    }
                } else {
                    \Log::warning('Contact mobile not found in contactData', [
                        'contactData_keys' => $contactData ? array_keys($contactData) : [],
                        'has_mobile_key' => isset($contactData['mobile'])
                    ]);
                }
            }
            
            // Map exhibitor data if needed
            if ($exhibitorData) {
                if (isset($exhibitorData['name']) && empty($allData['company_name'])) {
                    $allData['company_name'] = $exhibitorData['name'];
                }
            }
            
            // Map draft fields directly
            if (empty($allData['postal_code']) && !empty($draft->postal_code)) {
                $allData['postal_code'] = $draft->postal_code;
            }
            if (empty($allData['company_email']) && !empty($draft->company_email)) {
                $allData['company_email'] = $draft->company_email;
            }
            if (empty($allData['company_name']) && !empty($draft->company_name)) {
                $allData['company_name'] = $draft->company_name;
            }
            if (empty($allData['address']) && !empty($draft->address)) {
                $allData['address'] = $draft->address;
            }
            if (empty($allData['country_id']) && !empty($draft->country_id)) {
                $allData['country_id'] = $draft->country_id;
            }
            if (empty($allData['state_id']) && !empty($draft->state_id)) {
                $allData['state_id'] = $draft->state_id;
            }
            if (empty($allData['city_id']) && !empty($draft->city_id)) {
                $allData['city_id'] = $draft->city_id;
            }
            if (empty($allData['landline']) && !empty($draft->landline)) {
                // Keep landline in format country_code-national_number
                $landlineValue = $draft->landline;
                if (preg_match('/^(\d+)-(\d+)$/', $landlineValue, $matches)) {
                    $allData['landline'] = $matches[1] . '-' . $matches[2]; // Keep country code with national number
                } else {
                    // Extract only digits and add default country code
                    $digits = preg_replace('/[^0-9]/', '', $landlineValue);
                    if (strlen($digits) > 10) {
                        $allData['landline'] = substr($digits, 0, -10) . '-' . substr($digits, -10);
                    } else {
                        $allData['landline'] = '91-' . $digits;
                    }
                }
            }
            if (empty($allData['website']) && !empty($draft->website)) {
                $allData['website'] = $draft->website;
            }
            
            // Certificate is already handled above (validation rule removed if exists in draft)
            
            // For intl-tel-input fields, validate the national number
            // Request data takes precedence over session/draft data
            if ($request->has('contact_mobile_national') && !empty($request->input('contact_mobile_national'))) {
                $allData['contact_mobile'] = preg_replace('/[^0-9]/', '', (string) $request->input('contact_mobile_national'));
                // Ensure exactly 10 digits
                if (strlen($allData['contact_mobile']) > 10) {
                    $allData['contact_mobile'] = substr($allData['contact_mobile'], -10);
                }
                \Log::info('Contact mobile set from request', ['value' => $allData['contact_mobile']]);
            }
            // Note: contact_mobile is already extracted from contactData above (lines 1080-1150)
            // Final check: ensure contact_mobile is set and is exactly 10 digits
            if (empty($allData['contact_mobile'])) {
                \Log::warning('Contact mobile is empty after all extraction attempts', [
                    'contactData' => $contactData,
                    'request_has_contact_mobile_national' => $request->has('contact_mobile_national'),
                    'draft_contact_data' => $draft->contact_data ?? null
                ]);
            } elseif (strlen($allData['contact_mobile']) !== 10) {
                \Log::error('Contact mobile is not 10 digits before validation', [
                    'value' => $allData['contact_mobile'],
                    'length' => strlen($allData['contact_mobile'])
                ]);
            }
            if ($request->has('landline_national') && !empty($request->input('landline_national'))) {
                // Extract only digits from national number and country code
                $landlineNational = preg_replace('/[^0-9]/', '', $request->input('landline_national'));
                $landlineCountryCode = preg_replace('/[^0-9]/', '', $request->input('landline_country_code') ?: '91');
                $allData['landline'] = $landlineCountryCode . '-' . $landlineNational;
            }
            
            // CRITICAL: Check if contact email already exists in users table - BLOCK SUBMISSION
            $contactEmail = $request->input('contact_email');
            if (empty($contactEmail)) {
                // Use already-extracted contactData from above
                $contactEmail = $contactData['email'] ?? null;
            }
            // Also check allData in case it was set there
            if (empty($contactEmail) && !empty($allData['contact_email'])) {
                $contactEmail = $allData['contact_email'];
            }
            
            if (!empty($contactEmail) && $this->checkEmailExists(trim($contactEmail))) {
                // Email already exists - return error immediately and STOP processing
                return response()->json([
                    'success' => false,
                    'message' => 'This email is already registered. Please use a different email address.',
                    'errors' => [
                        'contact_email' => ['Email already exists']
                    ]
                ], 422);
            }
            
            // Custom validation messages
            $customMessages = [
                'certificate.max' => 'The certificate field must not be greater than 2mb.',
            ];
            
            // Modify certificate validation rule if certificate already exists in draft
            // When restoring from draft, the certificate is already uploaded, so make it optional
            if (!empty($draft->certificate_path) || isset($sessionData['certificate_path'])) {
                // Certificate already exists - remove the certificate validation rule entirely
                // since we don't need to validate an already-uploaded file
                unset($rules['certificate']);
            }
            
            // Log data for debugging
            \Log::info('restoreDraftToApplication: Data prepared for validation', [
                'draft_id' => $draft->id,
                'has_postal_code' => !empty($allData['postal_code']),
                'has_company_email' => !empty($allData['company_email']),
                'has_company_name' => !empty($allData['company_name']),
                'has_contact_mobile' => !empty($allData['contact_mobile']),
                'contact_mobile_value' => $allData['contact_mobile'] ?? null,
                'contact_mobile_length' => strlen($allData['contact_mobile'] ?? ''),
                'contact_mobile_raw' => $contactData['mobile'] ?? null,
                'contact_data_full' => $contactData ?? null,
                'has_certificate' => !empty($draft->certificate_path) || isset($sessionData['certificate_path']),
                'certificate_path' => $draft->certificate_path ?? $sessionData['certificate_path'] ?? null,
                'billing_data_keys' => $billingData ? array_keys($billingData) : [],
                'allData_keys' => array_keys($allData)
            ]);
            
            // Validate using request data (latest values)
            $validator = Validator::make($allData, $rules, $customMessages);
            
            if ($validator->fails()) {
                \Log::warning('restoreDraftToApplication: Validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'allData' => $allData
                ]);
                // Map validation errors back to frontend field names
                $errors = $validator->errors();
                $mappedErrors = [];
                
                foreach ($errors->messages() as $field => $messages) {
                    // Map old field names back to new field names for frontend
                    if ($field === 'postal_code') {
                        $mappedErrors['billing_postal_code'] = $messages;
                    } elseif ($field === 'company_email') {
                        $mappedErrors['billing_email'] = $messages;
                    } elseif ($field === 'company_name') {
                        $mappedErrors['billing_company_name'] = $messages;
                    } elseif ($field === 'address') {
                        $mappedErrors['billing_address'] = $messages;
                    } elseif ($field === 'country_id') {
                        $mappedErrors['billing_country_id'] = $messages;
                    } elseif ($field === 'state_id') {
                        $mappedErrors['billing_state_id'] = $messages;
                    } elseif ($field === 'city_id') {
                        $mappedErrors['billing_city'] = $messages;
                    } elseif ($field === 'landline') {
                        $mappedErrors['billing_telephone'] = $messages;
                    } elseif ($field === 'website') {
                        $mappedErrors['billing_website'] = $messages;
                    } else {
                        $mappedErrors[$field] = $messages;
                    }
                }
                
            return response()->json([
                'success' => false,
                    'message' => 'Please fix the validation errors below.',
                    'errors' => $mappedErrors
            ], 422);
        }

        DB::beginTransaction();
            
            // Get fresh session data (just saved above) - it has the latest values
            $sessionData = session('startup_zone_draft', []);
            
            // Update draft with latest session data (for database storage)
            if (isset($sessionData['billing_data'])) {
                $draft->billing_data = $sessionData['billing_data'];
            }
            if (isset($sessionData['exhibitor_data'])) {
                $draft->exhibitor_data = $sessionData['exhibitor_data'];
            }
            if (isset($sessionData['contact_data'])) {
                $draft->contact_data = $sessionData['contact_data'];
            }
            
            // Update draft with latest session data
            $draft->save();
            
            DB::commit();
            
            // NOW create application from draft (user clicked "Proceed to Payment")
            try {
                $application = $this->createApplicationFromDraft($draft);
                
                // Redirect to payment page
                return response()->json([
                    'success' => true,
                    'application_id' => $application->application_id,
                    'message' => 'Application created successfully! Redirecting to payment...',
                    'redirect' => route('startup-zone.payment', $application->application_id),
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to create application from draft in restoreDraftToApplication', [
                    'draft_id' => $draft->id,
                    'error' => $e->getMessage(),
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create application: ' . $e->getMessage(),
                    'error' => $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Startup Zone restore draft error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create application from draft - called when user clicks "Proceed to Payment"
     * This method creates users, applications, invoices, etc. from draft data
     */
    private function createApplicationFromDraft($draft)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        $caller = $backtrace[1]['function'] ?? 'unknown';
        
        \Log::info('createApplicationFromDraft called - CREATING APPLICATION NOW', [
            'draft_id' => $draft->id,
            'session_id' => session()->getId(),
            'called_from' => $caller,
            'file' => $backtrace[1]['file'] ?? 'unknown',
            'line' => $backtrace[1]['line'] ?? 'unknown',
            'message' => 'This should ONLY be called when user clicks Proceed to Payment'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Get fresh session data (latest values)
            $sessionData = session('startup_zone_draft', []);
            
            // ALWAYS use session data first (latest values), then fallback to draft
            // Get contact email - prioritize contact person email, fallback to billing email or exhibitor email
            $contactData = $sessionData['contact_data'] ?? $draft->contact_data ?? null;
            $contactData = is_string($contactData) ? json_decode($contactData, true) : $contactData;
            
            $billingDataSession = $sessionData['billing_data'] ?? null;
            $billingDataDraft = $draft->billing_data ?? null;
            $billingDataDraft = is_string($billingDataDraft) ? json_decode($billingDataDraft, true) : $billingDataDraft;
            $billingData = $billingDataSession ?? $billingDataDraft;
            
            $exhibitorDataSession = $sessionData['exhibitor_data'] ?? null;
            $exhibitorDataDraft = $draft->exhibitor_data ?? null;
            $exhibitorDataDraft = is_string($exhibitorDataDraft) ? json_decode($exhibitorDataDraft, true) : $exhibitorDataDraft;
            $exhibitorData = $exhibitorDataSession ?? $exhibitorDataDraft;
            
            // Get contact email with proper priority
            $contactPersonEmail = $contactData['email'] ?? null;
            $billingEmail = $billingData['email'] ?? null;
            $exhibitorEmail = $exhibitorData['email'] ?? null;
            $contactEmail = $contactPersonEmail ?: $billingEmail ?: $exhibitorEmail;
            
            // Validate email is not empty
            if (empty($contactEmail)) {
                throw new \Exception('Contact email or billing email is required');
            }
            
            // Get event_id from draft (default to 1 if not set)
            $eventId = $draft->event_id ?? 1;
            
            // Check if user exists with this email (email must be unique in users table)
            $user = \App\Models\User::where('email', $contactEmail)->first();
            
            // Check if an application already exists for this email and event
            // IMPORTANT: Only check "submitted" applications for email uniqueness
            // "in-progress" applications can be updated/continued
            $existingSubmittedApplication = null;
            $existingInProgressApplication = null;
            
            // First, check by user_id if user exists (most reliable)
            if ($user) {
                // Check for submitted application
                $existingSubmittedApplication = Application::where('application_type', 'startup-zone')
                    ->where('event_id', $eventId)
                    ->where('user_id', $user->id)
                    ->where('submission_status', 'submitted')
                    ->first();
                
                // Check for in-progress application (can be updated)
                if (!$existingSubmittedApplication) {
                    $existingInProgressApplication = Application::where('application_type', 'startup-zone')
                        ->where('event_id', $eventId)
                        ->where('user_id', $user->id)
                        ->where('submission_status', 'in progress')
                    ->first();
                }
            }
            
            // If not found by user_id, check by email addresses (only submitted status)
            if (!$existingSubmittedApplication) {
                $existingSubmittedApplication = Application::where('application_type', 'startup-zone')
                    ->where('event_id', $eventId)
                    ->where('submission_status', 'submitted') // Only check submitted applications
                    ->where(function($query) use ($contactEmail, $billingData, $exhibitorData) {
                        // Check by user's email
                        $query->whereHas('user', function($userQuery) use ($contactEmail) {
                            $userQuery->where('email', $contactEmail);
                        })
                        // Check by company email (contact email)
                        ->orWhere('company_email', $contactEmail);
                        
                        // Also check billing email or exhibitor email if different from contact email
                        $billingEmail = $billingData['email'] ?? null;
                        $exhibitorEmail = $exhibitorData['email'] ?? null;
                        
                        if (!empty($billingEmail) && $billingEmail !== $contactEmail) {
                            $query->orWhere('company_email', $billingEmail);
                        }
                        if (!empty($exhibitorEmail) && $exhibitorEmail !== $contactEmail) {
                            $query->orWhere('company_email', $exhibitorEmail);
                        }
                    })
                    ->first();
            }
            
            // If found submitted application, reject (email already used)
            // if ($existingSubmittedApplication) {
            //     DB::rollBack();
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'You have already registered for this event with this email address. Each email can only register once per event.',
            //         'errors' => [
            //             'email' => ['An application already exists for this email address and event. Please use a different email or contact support if you need to update your registration.']
            //         ]
            //     ], 422);
            // }
            
            // If found in-progress application, we'll update it instead of creating new
            // This allows users to continue their registration
            
            // Use latest data from session/draft for contact name
            $contactName = trim(($contactData['first_name'] ?? '') . ' ' . ($contactData['last_name'] ?? ''));
            if (empty($contactName)) {
                $contactName = $exhibitorData['name'] ?? $billingData['company_name'] ?? $draft->company_name ?? '';
            }
            $passwordGenerated = false;
            $password = null;
            
            if (!$user) {
                // Generate random password
                $password = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
                $passwordHash = Hash::make($password);
                
                // Create user with the contact email (ensures uniqueness)
                \Log::info('Creating NEW User from draft', [
                    'draft_id' => $draft->id,
                    'email' => $contactEmail,
                    'name' => $contactName
                ]);
                
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
                    
                    \Log::info('User created successfully', [
                        'user_id' => $user->id,
                        'email' => $contactEmail
                    ]);
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
            
            // If we have an existing in-progress application, update it instead of creating new
            if ($existingInProgressApplication) {
                $application = $existingInProgressApplication;
                $applicationId = $application->application_id; // Keep existing application_id
                \Log::info('Updating existing in-progress application', [
                    'application_id' => $applicationId,
                    'user_id' => $user->id,
                    'email' => $contactEmail
                ]);
            } else {
                // Generate application_id using TIN_NO_PREFIX with 6-digit number (before creating application)
                $applicationId = $this->generateApplicationIdWithTinPrefix();
                
                \Log::info('Creating NEW Application from draft', [
                    'draft_id' => $draft->id,
                    'application_id' => $applicationId,
                    'contact_email' => $contactEmail
                ]);
                
                // Create new application
                $application = new Application();
            }
            
            // Use the latest data we already extracted above (from session first, then draft)
            // $exhibitorData and $billingData are already set above with proper priority
            
            // Get company name from exhibitor_data only (for application table)
            $companyName = null;
            if ($exhibitorData && is_array($exhibitorData) && !empty($exhibitorData['name'])) {
                $companyName = trim($exhibitorData['name']);
            } elseif (!empty($draft->company_name)) {
                $companyName = trim($draft->company_name);
            }
            
            // Final validation: company_name cannot be null
            if (empty($companyName)) {
                DB::rollBack();
                \Log::error('Startup Zone: company_name is null - exhibitor_data required', [
                    'draft_id' => $draft->id,
                    'exhibitor_data' => $exhibitorData,
                    'draft_company_name' => $draft->company_name,
                ]);
                throw new \Exception('Company name is required. Please fill in the Exhibitor Information section.');
            }
            
            // Get company email from exhibitor_data only (for application table)
            $companyEmail = null;
            if ($exhibitorData && !empty($exhibitorData['email'])) {
                $companyEmail = $exhibitorData['email'];
            } elseif (!empty($draft->company_email)) {
                $companyEmail = $draft->company_email;
            } else {
                $companyEmail = $contactEmail; // Final fallback to contact email
            }
            
            // Get address from exhibitor_data only (for application table)
            $address = $exhibitorData['address'] ?? $draft->address ?? '';
            
            // Get city from exhibitor_data only (for application table)
            $city = null;
            if ($exhibitorData && !empty($exhibitorData['city'])) {
                $city = trim($exhibitorData['city']);
            } elseif (!empty($draft->city_id)) {
                // If draft has city_id, use it (could be string or ID, but we'll store as string)
                $city = is_numeric($draft->city_id) ? null : $draft->city_id;
            }
            
            // Get state_id from exhibitor_data only (for application table)
            $stateId = $exhibitorData['state_id'] ?? $draft->state_id ?? null;
            
            // Get postal_code from exhibitor_data only (for application table)
            $postalCode = $exhibitorData['postal_code'] ?? $draft->postal_code ?? '';
            
            // Get country_id from exhibitor_data only (for application table)
            $countryId = $exhibitorData['country_id'] ?? $draft->country_id ?? null;
            
            // Get landline from exhibitor_data only (for application table)
            // Keep format as country_code-national_number (e.g., 91-9801217815)
            $landlineRaw = $exhibitorData['telephone'] ?? $draft->landline ?? '';
            if (preg_match('/^(\d+)-(\d+)$/', $landlineRaw, $matches)) {
                // Already in correct format, keep it
                $landline = $matches[1] . '-' . $matches[2];
            } else {
                // Extract only digits and add default country code
                $digits = preg_replace('/[^0-9]/', '', $landlineRaw);
                if (!empty($digits)) {
                    if (strlen($digits) > 10) {
                        $landline = substr($digits, 0, -10) . '-' . substr($digits, -10);
                    } else {
                        $landline = '91-' . $digits;
                    }
                } else {
                    $landline = '';
                }
            }
            
            // Get website from exhibitor_data only (for application table)
            $website = $exhibitorData['website'] ?? $draft->website ?? '';
            $website = $this->normalizeWebsiteUrl($website);
            
            // Check if this is a complimentary registration
            $isComplimentary = false;
            if ($draft->promocode) {
                $association = AssociationPricingRule::where('promocode', $draft->promocode)
                    ->active()
                    ->valid()
                    ->first();
                if ($association && $association->is_complimentary) {
                    $isComplimentary = true;
                }
            }
            
            // Set application status based on whether it's complimentary
            $applicationStatus = $isComplimentary ? 'approved' : 'initiated';
            $submissionStatus = $isComplimentary ? 'approved' : 'in progress';
            
            // Get currency for payment_currency field
            $draftCurrency = $draft->currency ?? ($draft->payment_mode === 'PayPal' ? 'USD' : 'INR');
            $paymentCurrency = $draftCurrency === 'USD' ? 'USD' : 'INR';
            
            $application->fill([
                'application_id' => $applicationId,
                'stall_category' => $draft->stall_category ?? 'Startup Booth',
                'interested_sqm' => $draft->interested_sqm ?? 'Booth / POD',
                'company_name' => $companyName,
                'certificate' => $draft->certificate_path,
                'how_old_startup' => $draft->how_old_startup,
                'companyYears' => $draft->how_old_startup, // Also save to companyYears field
                'address' => $address,
                'city_id' => $city, // Store city name as string
                'state_id' => $stateId,
                'postal_code' => $postalCode,
                'country_id' => $countryId,
                'landline' => $landline,
                'website' => $website,
                'company_email' => $companyEmail,
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
                'status' => $applicationStatus,
                'submission_status' => $submissionStatus,
                'event_id' => $draft->event_id ?? 1,
                'user_id' => $user->id,
                'payment_currency' => $paymentCurrency,
                'terms_accepted' => 1,
                'userActive' => $isComplimentary ? true : false, // Activate user for complimentary registrations
            ]);
            
            // Set approval details if complimentary
            if ($isComplimentary) {
                $application->approved_date = now();
                $application->approved_by = 'System (Complimentary)';
            }
            
            $application->save();

            // If updating existing application, also update related records
            if ($existingInProgressApplication) {
                // Update EventContact if exists
                $existingContact = EventContact::where('application_id', $application->id)->first();
                if ($existingContact && $contactData && !empty($contactData)) {
                    $existingContact->update([
                        'salutation' => $contactData['title'] ?? null,
                        'first_name' => $contactData['first_name'] ?? null,
                        'last_name' => $contactData['last_name'] ?? null,
                        'job_title' => $contactData['designation'] ?? null,
                        'email' => $contactEmail,
                        'contact_number' => $contactData['mobile'] ?? null,
                    ]);
                }
                
                // Update BillingDetail if exists
                $existingBillingDetail = \App\Models\BillingDetail::where('application_id', $application->id)->first();
                if ($existingBillingDetail) {
                    // We'll update billing detail below, so skip here
                }
            }

            // Create or update event contact - use latest contact_data from session/draft
            // (Already handled above if updating in-progress application, but create if new)
            if ($contactData && !empty($contactData)) {
                $contact = EventContact::where('application_id', $application->id)->first();
                if (!$contact) {
                $contact = new EventContact();
                $contact->application_id = $application->id;
                }
                $contact->salutation = $contactData['title'] ?? null;
                $contact->first_name = $contactData['first_name'] ?? null;
                $contact->last_name = $contactData['last_name'] ?? null;
                $contact->job_title = $contactData['designation'] ?? null;
                // Ensure contact email matches the user email (use contact person email if available, otherwise company email)
                $contact->email = $contactEmail; // This matches the user email
                $contact->contact_number = $contactData['mobile'] ?? null;
                $contact->save();
            }

            // Create or update billing detail - use latest billing_data from session/draft
            $billingDetail = \App\Models\BillingDetail::where('application_id', $application->id)->first();
            if (!$billingDetail) {
            $billingDetail = new \App\Models\BillingDetail();
            $billingDetail->application_id = $application->id;
            }
            
            // Use the latest billingData, exhibitorData, and contactName we already extracted above
            // These variables are already set with proper priority (session first, then draft)
            
            // Store billing_data ONLY in billing_details table (no fallbacks to exhibitor_data)
            if ($billingData && !empty($billingData)) {
                // Use billing data from form
                $billingDetail->billing_company = $billingData['company_name'] ?? '';
                $billingDetail->contact_name = $contactName;
                $billingDetail->email = $billingData['email'] ?? $contactEmail;
                $billingDetail->phone = $billingData['telephone'] ?? '';
                $billingDetail->address = $billingData['address'] ?? '';
                
                // Store city name as string (exact value from form)
                $billingDetail->city_id = !empty($billingData['city']) ? trim($billingData['city']) : null;
                
                $billingDetail->state_id = $billingData['state_id'] ?? null;
                $billingDetail->country_id = $billingData['country_id'] ?? null;
                $billingDetail->postal_code = $billingData['postal_code'] ?? '';
                $billingDetail->gst_id = $draft->gst_no ?? null;
                $billingDetail->same_as_basic = '0'; // Different from exhibitor
            } else {
                // Fallback: Use contact details only if billing data not available
                $billingDetail->billing_company = $contactName;
                $billingDetail->contact_name = $contactName;
                $billingDetail->email = $contactEmail;
                $billingDetail->phone = $draft->contact_data['mobile'] ?? '';
                $billingDetail->address = '';
                $billingDetail->city_id = null;
                $billingDetail->state_id = null;
                $billingDetail->country_id = null;
                $billingDetail->postal_code = '';
                $billingDetail->gst_id = $draft->gst_no ?? null;
                $billingDetail->same_as_basic = '1';
            }
            
            $billingDetail->save();

            // Create or update invoice (if updating in-progress application, invoice might exist)
            $invoice = Invoice::where('application_id', $application->id)->first();
            if (!$invoice) {
            $invoice = new Invoice();
            $invoice->application_id = $application->id;
            }
            
            // Get pricing from draft pricing_data (preferred) or calculate
            $pricing = $draft->pricing_data ?? $this->calculatePricing($draft);
            
            // Get currency from draft
            $currency = $draft->currency ?? $pricing['currency'] ?? ($draft->payment_mode === 'PayPal' ? 'USD' : 'INR');
            
            // Map currency to payment_currency (USD -> USD, INR -> INR, PayPal -> USD)
            $paymentCurrency = $currency === 'USD' ? 'USD' : 'INR';
            
            $invoice->application_no = $application->application_id;
            $invoice->invoice_no = $application->application_id;
            $invoice->type = 'Startup Zone Registration';
            
            // If complimentary, set all amounts to 0 and mark as paid
            if ($isComplimentary) {
                $invoice->amount = 0;
                $invoice->price = 0;
                $invoice->gst = 0;
                $invoice->processing_charges = 0;
                $invoice->processing_chargesRate = 0;
                $invoice->total_final_price = 0;
                $invoice->currency = $currency;
                $invoice->payment_status = 'paid';
                $invoice->payment_due_date = now(); // Set to current date for complimentary
                $invoice->pending_amount = 0;
                $invoice->amount_paid = 0; // No payment needed, but status is 'paid'
            } else {
                $invoice->amount = $pricing['total']; // Required field - total amount
                $invoice->price = $pricing['base_price'];
                // Store IGST, CGST, SGST breakdown
                $invoice->igst_rate = $pricing['igst_rate'] ?? null;
                $invoice->igst_amount = $pricing['igst_amount'] ?? null;
                $invoice->cgst_rate = $pricing['cgst_rate'] ?? null;
                $invoice->cgst_amount = $pricing['cgst_amount'] ?? null;
                $invoice->sgst_rate = $pricing['sgst_rate'] ?? null;
                $invoice->sgst_amount = $pricing['sgst_amount'] ?? null;
                $invoice->processing_charges = $pricing['processing_charges'];
                $invoice->processing_chargesRate = $pricing['processing_rate'];
                $invoice->total_final_price = $pricing['total'];
                $invoice->currency = $currency; // Use currency from draft
                $invoice->payment_status = 'unpaid';
                $invoice->payment_due_date = now()->addDays(5); // Required field - payment due date
                $invoice->pending_amount = $pricing['total']; // Set pending amount to total initially
                $invoice->amount_paid = 0; // No amount paid initially
            }
            
            $invoice->save();
            
            \Log::info('Invoice saved to database', [
                'invoice_id' => $invoice->id,
                'application_id' => $application->application_id,
                'draft_id' => $draft->id,
                'total_amount' => $invoice->total_final_price
            ]);
            
            // Exhibitor data is now stored in applications table, so no need to create exhibitors_info

            // Update association registration count
            if ($draft->promocode) {
                $association = AssociationPricingRule::where('promocode', $draft->promocode)->first();
                if ($association) {
                    $association->increment('current_registrations');
                }
            }

            // Get contact for email
            $contact = EventContact::where('application_id', $application->id)->first();
            $billingDetail = \App\Models\BillingDetail::where('application_id', $application->id)->first();
            
            // For complimentary registrations: Send confirmation email immediately
            // For regular registrations: Send admin notification email when user confirms details (after preview)
            if ($isComplimentary) {
                // Send confirmation email to user for complimentary registration
                // Use 'approval' type since it's already approved and shows confirmation
                try {
                    Mail::to($contactEmail)->send(new StartupZoneMail($application, 'approval', $invoice, $contact));
                    
                    \Log::info('Complimentary Registration Confirmation Email Sent', [
                        'application_id' => $application->application_id,
                        'email' => $contactEmail
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to send complimentary registration email', [
                        'application_id' => $application->application_id,
                        'error' => $e->getMessage()
                    ]);
                }
            } else {
                // For startup zone: Send admin notification email when user confirms details (after preview)
                // Admin needs to approve before user can make payment
                
                // NOTE: No user email is sent on submission - user will receive email only after admin approval
            }

            // Mark draft as converted to application (keep for analytics)
            $draft->update([
                'converted_to_application_id' => $application->id,
                'converted_at' => now(),
                'is_abandoned' => false, // Keep it active for analytics
            ]);
            
            // Store application_id in session for security validation on payment page
            session(['startup_zone_application_id' => $application->application_id]);
            
            // Keep session data for preview page, but will be cleared when user reaches payment page
            // Don't clear draft yet - needed for preview page

            DB::commit();
            
            return $application;

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Startup Zone: Error creating application from draft', [
                'draft_id' => $draft->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
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
        
        // Load billing detail (billing_data is stored in billing_details table)
        $billingDetail = \App\Models\BillingDetail::where('application_id', $application->id)->first();
        
        // Security: Verify ownership using session
        // Check if this application_id matches the one stored in session (from form submission)
        $sessionApplicationId = session('startup_zone_application_id');
        if ($sessionApplicationId && $sessionApplicationId !== $applicationId) {
            // If session has a different application_id, this is unauthorized access attempt
            \Log::warning('Unauthorized startup zone payment access attempt', [
                'requested_application_id' => $applicationId,
                'session_application_id' => $sessionApplicationId,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
            abort(403, 'Unauthorized access to this application');
        }
        
        // If no session, log for security monitoring (may be from approval email link)
        if (!$sessionApplicationId) {
            \Log::info('Startup zone payment access without session validation', [
                'application_id' => $applicationId,
                'ip' => request()->ip(),
                'referer' => request()->header('referer')
            ]);
        }

        // Update submission_status to 'submitted' when user reaches payment page
        $statusJustChanged = false;
        if ($application->submission_status === 'in progress') {
            $application->submission_status = 'submitted';
            $application->save();
            $statusJustChanged = true;
            \Log::info('Application status updated to submitted', [
                'application_id' => $applicationId,
                'user_id' => $application->user_id
            ]);
        }

        // Send admin notification email when application is first submitted
        if ($statusJustChanged) {
            try {
                // Reload application with relationships for email
                $application->load(['country', 'state', 'eventContact']);
                $contact = EventContact::where('application_id', $application->id)->first();
                
                // Get admin emails from config
                $adminEmails = config('constants.admin_emails.to', []);
                $bccEmails = config('constants.admin_emails.bcc', []);
                
                \Log::info('Attempting to send admin notification email for startup zone', [
                    'application_id' => $application->application_id,
                    'admin_emails' => $adminEmails,
                    'bcc_emails' => $bccEmails,
                ]);
                
                if (!empty($adminEmails)) {
                    // If adminEmails NOT empty, always send to adminEmails (with BCC if present)
                    $mail = Mail::to($adminEmails);
                    if (!empty($bccEmails)) {
                        $mail->bcc($bccEmails);
                    }
                    $mail->send(new StartupZoneMail($application, 'admin_notification', null, $contact));
                    
                    \Log::info('Admin notification email sent successfully', [
                        'application_id' => $application->application_id,
                        'to' => $adminEmails,
                        'bcc' => $bccEmails,
                    ]);
                } elseif (!empty($bccEmails)) {
                    // If adminEmails is empty but bccEmails is not, send to bccEmails using only() to set BCC as main recipients
                    $mail = Mail::to([])->bcc($bccEmails);
                    $mail->send(new StartupZoneMail($application, 'admin_notification', null, $contact));
                    
                    \Log::info('Admin notification email sent successfully (BCC only)', [
                        'application_id' => $application->application_id,
                        'bcc' => $bccEmails,
                    ]);
                } else {
                    \Log::warning('No admin emails configured for startup zone notification', [
                        'application_id' => $application->application_id,
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send admin notification email for startup zone', [
                    'application_id' => $application->application_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                // Don't fail the transaction if email fails
            }
        }

        // Check if application is approved - payment only allowed after approval
        if ($application->submission_status !== 'approved') {
            $invoice = Invoice::where('application_id', $application->id)->first();
            $billingDetail = \App\Models\BillingDetail::where('application_id', $application->id)->first();
            $eventContact = EventContact::where('application_id', $application->id)->first();
            
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
            
            return view('startup-zone.payment', compact('application', 'invoice', 'billingDetail', 'eventContact'))
                ->with('approval_pending', true);
        }

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

        $billingDetail = \App\Models\BillingDetail::where('application_id', $application->id)->first();
        $eventContact = EventContact::where('application_id', $application->id)->first();
        
        // Clear session data once user reaches payment page (final insert is done)
        // This prevents editing fields after reaching payment page
        session()->forget('startup_zone_draft');
        session()->forget('startup_zone_application_id');
        session()->forget('payment_application_id');
        session()->forget('payment_application_type');
        session()->forget('invoice_no');
        
        return view('startup-zone.payment', compact('application', 'invoice', 'billingDetail', 'eventContact'));
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request, $applicationId)
    {
        $application = Application::where('application_id', $applicationId)
            ->where('application_type', 'startup-zone')
            ->firstOrFail();
        
        // Security: Verify ownership using session
        $sessionApplicationId = session('startup_zone_application_id');
        if ($sessionApplicationId && $sessionApplicationId !== $applicationId) {
            abort(403, 'Unauthorized access to this application');
        }

        // Check if application is approved - payment only allowed after approval
        if ($application->submission_status !== 'approved') {
            return redirect()->route('startup-zone.payment', $applicationId)
                ->with('error', 'Your profile is not approved yet for payment. Please wait for admin approval.');
        }

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
        
        // Security: For confirmation page, we allow access after payment
        // Session may have expired, but payment was successful, so allow access
        // Log for security monitoring
        $sessionApplicationId = session('startup_zone_application_id');
        if (!$sessionApplicationId || $sessionApplicationId !== $applicationId) {
            \Log::info('Startup zone confirmation access', [
                'application_id' => $applicationId,
                'session_app_id' => $sessionApplicationId,
                'ip' => request()->ip(),
                'payment_status' => $application->invoices()->where('payment_status', 'paid')->exists() ? 'paid' : 'unpaid'
            ]);
        }

        $invoice = Invoice::where('application_id', $application->id)->firstOrFail();
        $contact = EventContact::where('application_id', $application->id)->first();
        
        // Send credentials email after payment if config allows it
        // Only send if payment was just completed (check session flag to avoid duplicate sends)
        $paymentJustCompleted = session('payment_success', false);
        
        if ($invoice->payment_status === 'paid' && $application->user_id && $paymentJustCompleted && config('constants.SEND_CREDENTIALS_AFTER_PAYMENT', false)) {
            $user = \App\Models\User::find($application->user_id);
            if ($user && $user->simplePass) {
                try {
                    $contactEmail = $contact && $contact->email ? $contact->email : $application->company_email;
                    $contactName = $contact ? trim(($contact->salutation ?? '') . ' ' . ($contact->first_name ?? '') . ' ' . ($contact->last_name ?? '')) : $application->company_name;
                    
                    if ($contactEmail) {
                        $setupProfileUrl = config('app.url');
                        // Mail::to($contactEmail)->send(new UserCredentialsMail(
                        //     $contactName,
                        //     $setupProfileUrl,
                        //     $contactEmail,
                        //     $user->simplePass
                        // ));
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send credentials email after payment', [
                        'email' => $contactEmail ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                    // Don't fail if email fails
                }
            }
        }
        
        // Send thank you email after payment confirmation
        // Note: Payment thank you email is sent from PayPalController and PaymentGatewayController
        // This is a fallback in case payment was completed elsewhere
        if ($invoice->payment_status === 'paid' && $paymentJustCompleted) {
            try {
                $contactEmail = $contact && $contact->email ? $contact->email : $application->company_email;
                if ($contactEmail) {
                    $application->load(['country', 'state', 'eventContact']);
                    
                    // Get payment details from session if available
                    $paymentResponse = session('payment_response', []);
                    $paymentDetails = [
                        'transaction_id' => $paymentResponse['tracking_id'] ?? $paymentResponse['transaction_id'] ?? null,
                        'payment_method' => $paymentResponse['payment_mode'] ?? 'Payment Gateway',
                        'amount' => $invoice->total_final_price,
                        'currency' => $invoice->currency,
                    ];
                    
                    Mail::to($contactEmail)->send(new \App\Mail\StartupZoneMail($application, 'payment_thank_you', $invoice, $contact, $paymentDetails));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send payment thank you email', [
                    'email' => $contactEmail ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
                // Don't fail if email fails
            }
            
        }
        
        // Clean up session data (drafts are kept in database for analytics)
        session()->forget('startup_zone_draft');
        session()->forget('payment_application_id');
        session()->forget('payment_application_type');
        session()->forget('invoice_no');
        
        // Note: Drafts are NOT deleted - they are kept in database for analytics purposes

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

        $billingDetail = \App\Models\BillingDetail::where('application_id', $application->id)->first();
        
        return view('startup-zone.confirmation', compact('application', 'invoice', 'contact', 'billingDetail'));
    }

    /**
     * Helper: Save form data to session (extracted from autoSave for reuse)
     * This ensures latest form values are always saved before processing
     */
    private function saveFormDataToSession(Request $request)
    {
        $formData = $request->except(['_token', 'certificate']);
        
        // Get existing billing_data from session to preserve values not in request
        $sessionData = session('startup_zone_draft', []);
        $existingBillingData = $sessionData['billing_data'] ?? null;
        $existingBillingData = is_string($existingBillingData) ? json_decode($existingBillingData, true) : $existingBillingData;
        
        // Handle billing data
        $billingTelephoneNational = '';
        $billingTelephoneCountryCode = '91'; // Default to India
        
        if ($request->has('billing_telephone_national') && $request->input('billing_telephone_national')) {
            // Extract only digits from telephone national number (remove alphabets and special chars)
            $billingTelephoneNational = preg_replace('/[^0-9]/', '', $request->input('billing_telephone_national'));
            // Extract only digits from country code
            $billingTelephoneCountryCode = preg_replace('/[^0-9]/', '', $request->input('billing_telephone_country_code') ?: '91');
        } elseif ($request->has('billing_telephone') && $request->input('billing_telephone')) {
            // Extract only digits from telephone value (remove alphabets and special chars)
            $billingTelephoneValue = preg_replace('/[^0-9]/', '', trim($request->input('billing_telephone')));
            if (preg_match('/^(\d{1,3})(\d+)$/', $billingTelephoneValue, $matches)) {
                $billingTelephoneCountryCode = $matches[1];
                $billingTelephoneNational = $matches[2];
            } else {
                // If format doesn't match, use all extracted digits as national number
                $billingTelephoneNational = $billingTelephoneValue;
            }
        }
        
        // Build billing_data - use request values if provided, otherwise preserve existing values
        $billingData = [
            'company_name' => $request->input('billing_company_name') ?? $existingBillingData['company_name'] ?? null,
            'address' => $request->input('billing_address') ?? $existingBillingData['address'] ?? null,
            'country_id' => $request->input('billing_country_id') ?? $existingBillingData['country_id'] ?? null,
            'state_id' => $request->input('billing_state_id') ?? $existingBillingData['state_id'] ?? null,
            'city' => $request->input('billing_city') ?? $existingBillingData['city'] ?? null,
            'postal_code' => $request->input('billing_postal_code') ?? $existingBillingData['postal_code'] ?? null,
            'telephone' => $billingTelephoneNational ? ($billingTelephoneCountryCode . '-' . $billingTelephoneNational) : ($existingBillingData['telephone'] ?? ''),
            'website' => $request->has('billing_website') ? $this->normalizeWebsiteUrl($request->input('billing_website') ?? '') : ($existingBillingData['website'] ?? ''),
            'email' => $request->input('billing_email') ?? $existingBillingData['email'] ?? null,
        ];
        
        // Remove null values to keep the array clean
        $billingData = array_filter($billingData, function($value) {
            return $value !== null;
        });
        
        if (!empty($billingData)) {
            $formData['billing_data'] = $billingData;
        }
        
        // Handle file upload separately (if provided)
        if ($request->hasFile('certificate')) {
            $file = $request->file('certificate');
            
            // Get company name for filename
            $companyName = $request->input('billing_company_name') 
                        ?? $request->input('exhibitor_name') 
                        ?? $request->input('company_name')
                        ?? 'company';
            
            // Sanitize company name for filename (handle special characters and Unicode)
            // Convert to lowercase for consistency
            $companyName = mb_strtolower($companyName, 'UTF-8');
            
            // Remove accents and convert to ASCII equivalents (handles é, ñ, ü, etc.)
            $companyName = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $companyName);
            
            // Replace spaces and special characters with underscores
            $companyName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $companyName);
            
            // Remove multiple consecutive underscores
            $companyName = preg_replace('/_+/', '_', $companyName);
            
            // Remove leading/trailing underscores
            $companyName = trim($companyName, '_');
            
            // Limit length to prevent overly long filenames
            $companyName = substr($companyName, 0, 50);
            
            // If empty after sanitization, use default
            if (empty($companyName)) {
                $companyName = 'company';
            }
            
            // Generate filename: CompanyName_YYYYMMDD_HHMMSS.pdf
            $timestamp = now()->format('Ymd_His');
            $extension = $file->getClientOriginalExtension();
            $filename = $companyName . '_' . $timestamp . '.' . $extension;
            
            $path = $file->storeAs('startup-zone/certificates', $filename, 'public');
            $formData['certificate_path'] = $path;
        }
        
        // Build contact data from individual fields
        $mobileNational = '';
        $mobileCountryCode = '91'; // Default to India
        
        if ($request->has('contact_mobile_national') && $request->input('contact_mobile_national')) {
            // Extract only digits from national number (remove spaces and non-digits)
            $mobileNational = preg_replace('/[^0-9]/', '', trim($request->input('contact_mobile_national')));
            // Extract only digits from country code (remove + and non-digits)
            $mobileCountryCode = preg_replace('/[^0-9]/', '', $request->input('contact_country_code') ?: '91');
        } elseif ($request->has('contact_mobile') && $request->input('contact_mobile')) {
            // If mobile is provided directly, extract and format
            $mobileValue = preg_replace('/[^0-9]/', '', trim($request->input('contact_mobile')));
            // Try to extract country code if present (first 1-4 digits as country code, rest as national)
            if (strlen($mobileValue) > 10) {
                // Assume last 10 digits are national number for Indian numbers
                $mobileNational = substr($mobileValue, -10);
                $mobileCountryCode = substr($mobileValue, 0, -10) ?: '91';
            } else {
                $mobileNational = $mobileValue;
            }
        }
        
        $contactData = [
            'title' => $request->input('contact_title'),
            'first_name' => $request->input('contact_first_name'),
            'last_name' => $request->input('contact_last_name'),
            'designation' => $request->input('contact_designation'),
            'email' => $request->input('contact_email'),
            'mobile' => $mobileNational ? ($mobileCountryCode . '-' . $mobileNational) : '',
            'country_code' => $mobileCountryCode,
        ];
        
        if (!empty($contactData)) {
            $formData['contact_data'] = $contactData;
        }
        
        // Handle exhibitor data
        $exhibitorTelephoneNational = '';
        $exhibitorTelephoneCountryCode = '91'; // Default to India
        
        if ($request->has('exhibitor_telephone_national') && $request->input('exhibitor_telephone_national')) {
            // Extract only digits from telephone national number (remove alphabets and special chars)
            $exhibitorTelephoneNational = preg_replace('/[^0-9]/', '', $request->input('exhibitor_telephone_national'));
            // Extract only digits from country code
            $exhibitorTelephoneCountryCode = preg_replace('/[^0-9]/', '', $request->input('exhibitor_telephone_country_code') ?: '91');
        } elseif ($request->has('exhibitor_telephone') && $request->input('exhibitor_telephone')) {
            // Extract only digits from telephone value (remove alphabets and special chars)
            $exhibitorTelephoneValue = preg_replace('/[^0-9]/', '', trim($request->input('exhibitor_telephone')));
            if (preg_match('/^(\d{1,3})(\d+)$/', $exhibitorTelephoneValue, $matches)) {
                $exhibitorTelephoneCountryCode = $matches[1];
                $exhibitorTelephoneNational = $matches[2];
            } else {
                // If format doesn't match, use all extracted digits as national number
                $exhibitorTelephoneNational = $exhibitorTelephoneValue;
            }
        }
        
        $exhibitorData = [
            'name' => $request->input('exhibitor_name'),
            'address' => $request->input('exhibitor_address'),
            'country_id' => $request->input('exhibitor_country_id'),
            'state_id' => $request->input('exhibitor_state_id'),
            'city' => $request->input('exhibitor_city'),
            'postal_code' => $request->input('exhibitor_postal_code'),
            'telephone' => $exhibitorTelephoneNational ? ($exhibitorTelephoneCountryCode . '-' . $exhibitorTelephoneNational) : '',
            'website' => $this->normalizeWebsiteUrl($request->input('exhibitor_website') ?? ''),
            'email' => $request->input('exhibitor_email'),
        ];
        
        if (!empty($exhibitorData)) {
            $formData['exhibitor_data'] = $exhibitorData;
        }
        
        // Handle landline: format as country_code-national_number (e.g., 91-9801217815)
        if ($request->has('landline_national') && $request->input('landline_national')) {
            // Extract only digits from landline national number (remove alphabets and special chars)
            $landlineNational = preg_replace('/[^0-9]/', '', $request->input('landline_national'));
            // Extract only digits from country code (remove + and non-digits)
            $landlineCountryCode = preg_replace('/[^0-9]/', '', $request->input('landline_country_code') ?: '91');
            $formData['landline'] = $landlineCountryCode . '-' . $landlineNational;
        } elseif ($request->has('landline') && $request->input('landline')) {
            // If landline is provided directly, extract and format
            $landlineValue = preg_replace('/[^0-9]/', '', trim($request->input('landline')));
            if (strlen($landlineValue) > 10) {
                // Assume last 10 digits are national number
                $landlineNational = substr($landlineValue, -10);
                $landlineCountryCode = substr($landlineValue, 0, -10) ?: '91';
                $formData['landline'] = $landlineCountryCode . '-' . $landlineNational;
            } elseif (!empty($landlineValue)) {
                $formData['landline'] = '91-' . $landlineValue; // Default to India
            }
        }
        
        // Store in session - merge with existing session data to preserve other fields
        $existingSessionData = session('startup_zone_draft', []);
        $mergedData = array_merge($existingSessionData, $formData);
        session(['startup_zone_draft' => $mergedData]);
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

        // Normalize website URL before validation (if present in allData)
        // Note: $allData is passed as parameter to this method, check if it exists
        if (isset($allData) && isset($allData['website']) && !empty($allData['website'])) {
            $allData['website'] = $this->normalizeWebsiteUrl($allData['website']);
        }

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
            $rules['certificate'] = 'required|file|mimes:pdf|max:2048';
        }
        if (in_array('website', $fieldsToValidate)) {
            // Ensure url validation is present (website already normalized above)
            if (isset($rules['website'])) {
                if (strpos($rules['website'], 'url') === false) {
                    $rules['website'] .= '|url';
                }
            } else {
                $rules['website'] = 'required|url';
            }
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
     * Format: TIN-BTS-2026-EXH-XXXXXX (6-digit random number)
     */
    private function generateApplicationIdWithTinPrefix()
    {
        $prefix = config('constants.APPLICATION_ID_PREFIX');
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
     * Generate unique PIN number using PIN_NO_PREFIX
     * Format: PRN-BTS-2026-EXHP-XXXXXX (6-digit random number)
     */
    private function generatePinNo()
    {
        $prefix = config('constants.PIN_NO_PREFIX');
        $maxAttempts = 100; // Prevent infinite loop
        $attempts = 0;
        
        while ($attempts < $maxAttempts) {
            // Generate 6-digit random number
            $randomNumber = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
            $pinNo = $prefix . $randomNumber;
            $attempts++;
            
            // Check if it already exists in invoices table
            if (!\App\Models\Invoice::where('pin_no', $pinNo)->exists()) {
                return $pinNo;
            }
        }
        
        // If we've tried too many times, use timestamp-based fallback
        $timestamp = substr(time(), -6); // Last 6 digits of timestamp
        $pinNo = $prefix . $timestamp;
        if (!\App\Models\Invoice::where('pin_no', $pinNo)->exists()) {
            return $pinNo;
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
                } elseif ($field === 'postal_code') {
                    // Check billing_postal_code first, then fallback to postal_code
                    $value = isset($draft->billing_data) && isset($draft->billing_data['postal_code']) 
                        ? $draft->billing_data['postal_code'] 
                        : ($draft->postal_code ?? null);
                } elseif ($field === 'company_email') {
                    // Check billing_email first, then fallback to company_email
                    $value = isset($draft->billing_data) && isset($draft->billing_data['email']) 
                        ? $draft->billing_data['email'] 
                        : ($draft->company_email ?? null);
                } elseif ($field === 'company_name') {
                    // Check billing_company_name first, then fallback to company_name
                    $value = isset($draft->billing_data) && isset($draft->billing_data['company_name']) 
                        ? $draft->billing_data['company_name'] 
                        : ($draft->company_name ?? null);
                } elseif ($field === 'address') {
                    // Check billing_address first, then fallback to address
                    $value = isset($draft->billing_data) && isset($draft->billing_data['address']) 
                        ? $draft->billing_data['address'] 
                        : ($draft->address ?? null);
                } elseif ($field === 'country_id') {
                    // Check billing_country_id first, then fallback to country_id
                    $value = isset($draft->billing_data) && isset($draft->billing_data['country_id']) 
                        ? $draft->billing_data['country_id'] 
                        : ($draft->country_id ?? null);
                } elseif ($field === 'state_id') {
                    // Check billing_state_id first, then fallback to state_id
                    $value = isset($draft->billing_data) && isset($draft->billing_data['state_id']) 
                        ? $draft->billing_data['state_id'] 
                        : ($draft->state_id ?? null);
                } elseif ($field === 'city_id') {
                    // Check billing_city first, then fallback to city_id
                    $value = isset($draft->billing_data) && isset($draft->billing_data['city']) 
                        ? $draft->billing_data['city'] 
                        : ($draft->city_id ?? null);
                } elseif ($field === 'landline') {
                    // Check billing_telephone first, then fallback to landline
                    $value = isset($draft->billing_data) && isset($draft->billing_data['telephone']) 
                        ? $draft->billing_data['telephone'] 
                        : ($draft->landline ?? null);
                } elseif ($field === 'website') {
                    // Check billing_website first, then fallback to website
                    $value = isset($draft->billing_data) && isset($draft->billing_data['website']) 
                        ? $draft->billing_data['website'] 
                        : ($draft->website ?? null);
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
     * Helper: Round amount to whole number
     * .8 and above → round up to 1, .4 and below → round down to 0
     */
    private function roundAmount($amount)
    {
        $decimal = $amount - floor($amount);
        if ($decimal >= 0.8) {
            return ceil($amount); // Round up if .8 or higher (e.g., 52000.8 → 52001)
        } elseif ($decimal <= 0.4) {
            return floor($amount); // Round down if .4 or lower (e.g., 52000.4 → 52000)
        } else {
            // For .5 to .7, round up (standard rounding behavior)
            return round($amount, 0);
        }
    }

    /**
     * Helper: Calculate pricing for startup zone with early bird and regular pricing
     * 
     * Pricing logic:
     * 1. Check if early bird pricing is active (based on cutoff date)
     * 2. Apply early bird price if active, otherwise use regular price
     * 3. Apply association pricing (if promocode exists and no TV)
     * 4. TV pricing takes precedence over association pricing
     */
    private function calculatePricing($draft)
    {
        // Get currency from draft (preferred) or derive from payment_mode
        $currency = $draft->currency ?? ($draft->payment_mode === 'PayPal' ? 'USD' : 'INR');
        
        // Check if TV parameter is set (from session or draft)
        $hasTV = session('startup_zone_has_tv', false);
        
        // Get event config for pricing configuration
        $eventConfig = DB::table('event_configurations')->where('id', 1)->first();
        
        // Early bird cutoff date - can be configured in event_configurations or use default
        // Default: March 31, 2026 (can be changed via event_configurations table)
        $earlyBirdCutoffDate = $eventConfig->startup_zone_early_bird_cutoff_date ?? '2026-03-31';
        $isEarlyBird = now()->lte(Carbon::parse($earlyBirdCutoffDate)->endOfDay());
        
        // Get configured prices from database (with fallback defaults)
        // INR Prices
        $regularPriceWithoutTVINR = $eventConfig->startup_zone_regular_price_inr ?? 52000.00;
        $regularPriceWithTVINR = $eventConfig->startup_zone_regular_price_with_tv_inr ?? 60000.00;
        $earlyBirdPriceWithoutTVINR = $eventConfig->startup_zone_early_bird_price_inr ?? 30000.00;
        $earlyBirdPriceWithTVINR = $eventConfig->startup_zone_early_bird_price_with_tv_inr ?? 37500.00;
        
        // USD Prices (if configured, otherwise will be calculated from INR)
        $regularPriceWithoutTVUSD = $eventConfig->startup_zone_regular_price_usd ?? null;
        $regularPriceWithTVUSD = $eventConfig->startup_zone_regular_price_with_tv_usd ?? null;
        $earlyBirdPriceWithoutTVUSD = $eventConfig->startup_zone_early_bird_price_usd ?? null;
        $earlyBirdPriceWithTVUSD = $eventConfig->startup_zone_early_bird_price_with_tv_usd ?? null;
        
        // Determine base price based on early bird status, TV, and currency
        if ($currency === 'USD') {
            // Use USD prices if configured, otherwise calculate from INR
            if ($hasTV) {
                if ($isEarlyBird) {
                    $basePrice = $earlyBirdPriceWithTVUSD ?? ($earlyBirdPriceWithTVINR / ($eventConfig->usd_exchange_rate ?? 83));
                } else {
                    $basePrice = $regularPriceWithTVUSD ?? ($regularPriceWithTVINR / ($eventConfig->usd_exchange_rate ?? 83));
                }
            } else {
                if ($isEarlyBird) {
                    $basePrice = $earlyBirdPriceWithoutTVUSD ?? ($earlyBirdPriceWithoutTVINR / ($eventConfig->usd_exchange_rate ?? 83));
                } else {
                    $basePrice = $regularPriceWithoutTVUSD ?? ($regularPriceWithoutTVINR / ($eventConfig->usd_exchange_rate ?? 83));
                }
            }
            // Store INR equivalent for association pricing calculation
            $basePriceINR = $basePrice * ($eventConfig->usd_exchange_rate ?? 83);
        } else {
            // INR pricing
            if ($hasTV) {
                $basePriceINR = $isEarlyBird ? $earlyBirdPriceWithTVINR : $regularPriceWithTVINR;
            } else {
                $basePriceINR = $isEarlyBird ? $earlyBirdPriceWithoutTVINR : $regularPriceWithoutTVINR;
            }
            $basePrice = $basePriceINR;
        }
        
        // Get association pricing if promocode exists
        // Note: TV pricing takes precedence over association pricing
        // Association pricing applies to regular price, not early bird
        if ($draft->promocode && !$hasTV) {
            $association = AssociationPricingRule::where('promocode', $draft->promocode)
                ->active()
                ->valid()
                ->first();

            if ($association) {
                // Association pricing overrides both early bird and regular pricing
                $associationPriceINR = $association->getEffectivePrice();
                $basePriceINR = $associationPriceINR;
                
                // Convert to USD if needed
                if ($currency === 'USD') {
                    $basePrice = $basePriceINR / ($eventConfig->usd_exchange_rate ?? 83);
                } else {
                    $basePrice = $basePriceINR;
                }
            }
        }

        // Get processing rate based on currency
        if ($currency === 'USD') {
            $processingRate = ($eventConfig->usd_processing_charge ?? 9.5) / 100; // 9.5% for USD
        } else {
            $processingRate = ($eventConfig->ind_processing_charge ?? 3) / 100; // 3% for INR
        }

        // Get GST rates
        $igstRatePercent = $eventConfig->igst_rate ?? 18;
        $cgstRatePercent = $eventConfig->cgst_rate ?? 9;
        $sgstRatePercent = $eventConfig->sgst_rate ?? 9;
        
        // Determine GST type (IGST vs CGST+SGST) based on GST validation and state matching
        $organizerStateCode = substr(config('constants.GSTIN'), 0, 2); // e.g., '29' for Karnataka
        $gstNo = $draft->gst_no ?? null;
        $validatedGstStateCode = $gstNo && strlen($gstNo) >= 2 ? substr($gstNo, 0, 2) : null;
        $isSameState = $validatedGstStateCode && $validatedGstStateCode === $organizerStateCode;
        
        // Calculate IGST, CGST, SGST amounts - only store applicable GST
        if ($isSameState) {
            // Same state - apply CGST + SGST only
            $igstRate = null;
            $igstAmount = null;
            $cgstRate = $cgstRatePercent;
            $sgstRate = $sgstRatePercent;
            $cgstAmount = $basePrice * ($cgstRatePercent / 100);
            $sgstAmount = $basePrice * ($sgstRatePercent / 100);
            $totalGst = $cgstAmount + $sgstAmount;
        } else {
            // Different state or GST not provided - apply IGST only
            $igstRate = $igstRatePercent;
            $igstAmount = $basePrice * ($igstRatePercent / 100);
            $cgstRate = null;
            $sgstRate = null;
            $cgstAmount = null;
            $sgstAmount = null;
            $totalGst = $igstAmount;
        }
        
        $processingCharges = ($basePrice + $totalGst) * $processingRate;
        $total = $basePrice + $totalGst + $processingCharges;

        return [
            'base_price' => $this->roundAmount($basePrice),
            'igst_rate' => $igstRate,
            'igst_amount' => $igstAmount ? $this->roundAmount($igstAmount) : null,
            'cgst_rate' => $cgstRate,
            'cgst_amount' => $cgstAmount ? $this->roundAmount($cgstAmount) : null,
            'sgst_rate' => $sgstRate,
            'sgst_amount' => $sgstAmount ? $this->roundAmount($sgstAmount) : null,
            'is_same_state' => $isSameState,
            'processing_charges' => $this->roundAmount($processingCharges),
            'processing_rate' => $processingRate * 100,
            'total' => $this->roundAmount($total),
            'currency' => $currency,
            'has_tv' => $hasTV,
            'base_price_inr' => $basePriceINR
        ];
    }

    /**
     * Get city ID from city name
     */
    private function getCityIdFromName($cityName, $stateId = null)
    {
        if (empty($cityName)) {
            return null;
        }
        
        // If it's already numeric, return as is
        if (is_numeric($cityName)) {
            return $cityName;
        }
        
        // Try to find city by name
        $query = \App\Models\City::where('name', 'like', $cityName);
        if ($stateId) {
            $query->where('state_id', $stateId);
        }
        $city = $query->first();
        
        return $city ? $city->id : null;
    }

    private function checkEmailExists($email)
    {
        $user = \App\Models\User::where('email', $email)->first();
        return $user !== null;
    }

    /**
     * Check if email already exists in users table (AJAX endpoint)
     */
    public function checkEmail(Request $request)
    {
        $email = $request->input('email');
        
        if (empty($email)) {
            return response()->json([
                'exists' => false,
                'message' => ''
            ]);
        }
        
        $exists = $this->checkEmailExists(trim($email));
        
        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Email already exists' : ''
        ]);
    }
}


