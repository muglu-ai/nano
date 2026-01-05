<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\EventContact;
use App\Models\Invoice;
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
use App\Mail\UserCredentialsMail;
use App\Mail\ExhibitorRegistrationMail;

class ExhibitorRegistrationController extends Controller
{
    /**
     * Show the multi-step registration form
     */
    public function showForm(Request $request)
    {
        // Get draft data from session (if exists)
        $sessionData = session('exhibitor_registration_draft', []);
        
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
        
        // Get event configuration for pricing
        $eventConfig = DB::table('event_configurations')->where('id', 1)->first();
        $shellSchemeRate = $eventConfig->shell_scheme_rate ?? 13000;
        $rawSpaceRate = $eventConfig->raw_space_rate ?? 12000;
        $gstRate = $eventConfig->gst_rate ?? 18;
        
        // Get dropdown data
        $sectors = [
            'Information Technology',
            'Electronics & Semiconductor',
            'Drones & Robotics',
            'EV, Energy, Climate, Water, Soil, GSDI',
            'Telecommunications',
            'Cybersecurity',
            'Artificial Intelligence',
            'Cloud Services',
            'E-Commerce',
            'Automation',
            'AVGC',
            'Aerospace, Defence & Space Tech',
            'Mobility Tech',
            'Infrastructure',
            'Biotech',
            'Agritech',
            'Medtech',
            'Fintech',
            'Healthtech',
            'Edutech',
            'Startup',
            'Unicorn / VCs',
            'Academia & University',
            'Tech Parks / Co-Working Spaces of India',
            'Banking / Insurance',
            'R&D and Central Govt.',
            'Others'
        ];
        
        $subSectors = config('constants.SUB_SECTORS', []);
        
        // Get countries
        $countries = Country::select('id', 'name', 'code')->orderBy('name')->get();
        
        // Get India's ID for default selection
        $india = Country::where('code', 'IN')->first();
        $indiaId = $india ? $india->id : null;
        
        // Get states for India by default
        $selectedCountryId = $draft->country_id ?? $indiaId;
        $states = $selectedCountryId ? State::where('country_id', $selectedCountryId)->select('id', 'name')->orderBy('name')->get() : collect();
        
        // Get booth size options from database (admin-configurable)
        $boothSizesConfig = json_decode($eventConfig->booth_sizes ?? '{}', true);
        $boothSizes = [
            'Raw' => $boothSizesConfig['Raw'] ?? ['36', '48', '54', '72', '108', '135'],
            'Shell' => $boothSizesConfig['Shell'] ?? ['9', '12', '15', '18', '27']
        ];
        
        return view('exhibitor-registration.form', compact(
            'draft',
            'sectors',
            'subSectors',
            'states',
            'countries',
            'shellSchemeRate',
            'rawSpaceRate',
            'gstRate',
            'boothSizes'
        ));
    }

    /**
     * Calculate price based on booth space, size, and per sqm rate
     */
    public function calculatePrice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booth_space' => 'required|in:Raw,Shell',
            'booth_size' => 'required|string',
            'gst_rate' => 'nullable|numeric|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid parameters'
            ], 422);
        }

        // Get event configuration
        $eventConfig = DB::table('event_configurations')->where('id', 1)->first();
        $gstRatePercent = $request->input('gst_rate') ?? ($eventConfig->gst_rate ?? 18);
        $gstRate = $gstRatePercent / 100;
        
        // Get processing charge rate (default 3% for Indian payments)
        $processingRatePercent = $eventConfig->ind_processing_charge ?? 3;
        $processingRate = $processingRatePercent / 100;
        
        // Get rate per sqm based on booth space type
        $ratePerSqm = 0;
        if ($request->input('booth_space') === 'Shell') {
            $ratePerSqm = $eventConfig->shell_scheme_rate ?? 13000;
        } else {
            $ratePerSqm = $eventConfig->raw_space_rate ?? 12000;
        }
        
        // Extract sqm from booth size (e.g., "36" from "36sqm" or "36")
        $boothSize = preg_replace('/[^0-9]/', '', $request->input('booth_size'));
        $sqm = (int) $boothSize;
        
        if ($sqm <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid booth size'
            ], 422);
        }
        
        // Calculate base price
        $basePrice = $sqm * $ratePerSqm;
        
        // Calculate GST
        $gstAmount = $basePrice * $gstRate;
        
        // Calculate processing charges on (base price + GST)
        $processingCharges = ($basePrice + $gstAmount) * $processingRate;
        
        // Calculate total
        $totalPrice = $basePrice + $gstAmount + $processingCharges;
        
        return response()->json([
            'success' => true,
            'price' => [
                'sqm' => $sqm,
                'rate_per_sqm' => $ratePerSqm,
                'base_price' => round($basePrice, 2),
                'gst_rate' => $gstRatePercent,
                'gst_amount' => round($gstAmount, 2),
                'processing_rate' => $processingRatePercent,
                'processing_charges' => round($processingCharges, 2),
                'total_price' => round($totalPrice, 2)
            ]
        ]);
    }

    /**
     * Get booth sizes based on booth space type
     */
    public function getBoothSizes(Request $request)
    {
        $boothSpace = $request->input('booth_space');
        
        // Get booth sizes from database (admin-configurable)
        $eventConfig = DB::table('event_configurations')->where('id', 1)->first();
        $boothSizesConfig = json_decode($eventConfig->booth_sizes ?? '{}', true);
        
        // Default values if not configured
        $defaultSizes = [
            'Raw' => ['36', '48', '54', '72', '108', '135'],
            'Shell' => ['9', '12', '15', '18', '27']
        ];
        
        $sizes = $boothSizesConfig[$boothSpace] ?? $defaultSizes[$boothSpace] ?? [];
        
        // Format for frontend
        $formattedSizes = array_map(function($size) {
            return [
                'value' => trim($size),
                'label' => trim($size) . ' sqm'
            ];
        }, $sizes);
        
        if (empty($formattedSizes)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid booth space type or no sizes configured'
            ], 422);
        }
        
        return response()->json([
            'success' => true,
            'booth_sizes' => $formattedSizes
        ]);
    }

    /**
     * Auto-save form data to session
     */
    public function autoSave(Request $request)
    {
        $formData = $request->except(['_token']);
        
        // Handle billing data
        $billingTelephoneNational = '';
        $billingTelephoneCountryCode = '91';
        
        if ($request->has('billing_telephone_national') && $request->input('billing_telephone_national')) {
            $billingTelephoneNational = preg_replace('/\s+/', '', trim($request->input('billing_telephone_national')));
            $billingTelephoneCountryCode = $request->input('billing_telephone_country_code') ?: '91';
        } elseif ($request->has('billing_telephone') && $request->input('billing_telephone')) {
            $billingTelephoneValue = preg_replace('/\s+/', '', trim($request->input('billing_telephone')));
            if (preg_match('/^\+?(\d{1,3})(\d+)$/', $billingTelephoneValue, $matches)) {
                $billingTelephoneCountryCode = $matches[1];
                $billingTelephoneNational = $matches[2];
            } else {
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
        
        // Handle contact mobile formatting
        $mobileNational = '';
        $mobileCountryCode = '91';
        
        if ($request->has('contact_mobile_national') && $request->input('contact_mobile_national')) {
            $mobileNational = preg_replace('/\s+/', '', trim($request->input('contact_mobile_national')));
            $mobileCountryCode = $request->input('contact_country_code') ?: '91';
        } elseif ($request->has('contact_mobile') && $request->input('contact_mobile')) {
            $mobileValue = preg_replace('/\s+/', '', trim($request->input('contact_mobile')));
            if (preg_match('/^\+?(\d{1,3})(\d+)$/', $mobileValue, $matches)) {
                $mobileCountryCode = $matches[1];
                $mobileNational = $matches[2];
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
        $exhibitorTelephoneCountryCode = '91';
        
        if ($request->has('exhibitor_telephone_national') && $request->input('exhibitor_telephone_national')) {
            $exhibitorTelephoneNational = preg_replace('/\s+/', '', trim($request->input('exhibitor_telephone_national')));
            $exhibitorTelephoneCountryCode = $request->input('exhibitor_telephone_country_code') ?: '91';
        } elseif ($request->has('exhibitor_telephone') && $request->input('exhibitor_telephone')) {
            $exhibitorTelephoneValue = preg_replace('/\s+/', '', trim($request->input('exhibitor_telephone')));
            if (preg_match('/^\+?(\d{1,3})(\d+)$/', $exhibitorTelephoneValue, $matches)) {
                $exhibitorTelephoneCountryCode = $matches[1];
                $exhibitorTelephoneNational = $matches[2];
            } else {
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
        
        // Store in session
        session(['exhibitor_registration_draft' => $formData]);
        
        // Calculate progress percentage
        $progress = $this->calculateProgressFromData($formData);
        
        return response()->json([
            'success' => true,
            'message' => 'Data stored in session',
            'progress' => $progress
        ]);
    }

    /**
     * Verify Google reCAPTCHA response
     */
    private function verifyRecaptcha($recaptchaResponse)
    {
        // If disabled via config, always pass
        if (!config('constants.RECAPTCHA_ENABLED', false)) {
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
     * Normalize website URL
     */
    private function normalizeWebsiteUrl($url)
    {
        if (empty($url)) {
            return $url;
        }
        
        $url = trim($url);
        
        if (!preg_match('/^https?:\/\//i', $url)) {
            $url = 'http://' . $url;
        }
        
        return $url;
    }

    /**
     * Calculate progress percentage
     */
    private function calculateProgressFromData($data)
    {
        $fields = [
            'booth_space', 'booth_size', 'sector', 'subsector', 'category',
            'billing_company_name', 'billing_address', 'billing_city', 'billing_state_id', 'billing_postal_code',
            'billing_telephone', 'billing_website',
            'exhibitor_name', 'exhibitor_address', 'exhibitor_city', 'exhibitor_state_id', 'exhibitor_postal_code',
            'exhibitor_telephone', 'exhibitor_website',
            'contact_title', 'contact_first_name', 'contact_last_name', 
            'contact_designation', 'contact_email', 'contact_mobile',
            'tan_status', 'gst_status', 'pan_no', 'sales_executive_name'
        ];
        
        $filled = 0;
        foreach ($fields as $field) {
            if (isset($data[$field]) && !empty($data[$field])) {
                $filled++;
            } elseif (isset($data['contact_data'][$field]) && !empty($data['contact_data'][$field])) {
                $filled++;
            } elseif (isset($data['billing_data'][$field]) && !empty($data['billing_data'][$field])) {
                $filled++;
            } elseif (isset($data['exhibitor_data'][$field]) && !empty($data['exhibitor_data'][$field])) {
                $filled++;
            }
        }
        
        return round(($filled / count($fields)) * 100);
    }

    /**
     * Fetch GST details
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
        
        // Rate limiting
        $ipAddress = $request->ip();
        $rateLimitKey = 'gst_api_rate_limit_' . $ipAddress;
        $rateLimitData = Cache::get($rateLimitKey, ['count' => 0, 'reset_at' => now()->addMinutes(10)]);
        
        if ($rateLimitData['count'] >= 5) {
            $resetTime = $rateLimitData['reset_at'];
            $minutesRemaining = max(1, (int) ceil(now()->diffInSeconds($resetTime) / 60));
            
            if ($minutesRemaining > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Rate limit exceeded. Please try again after {$minutesRemaining} minutes.",
                    'rate_limit_exceeded' => true,
                    'reset_in_minutes' => $minutesRemaining
                ], 429);
            } else {
                $rateLimitData = ['count' => 0, 'reset_at' => now()->addMinutes(10)];
            }
        }
        
        // Check database first
        $gstLookup = GstLookup::where('gst_number', $gstNumber)->first();
        
        if ($gstLookup) {
            $gstLookup->update(['last_verified_at' => now()]);
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
                ],
                'from_cache' => true,
            ]);
        }
        
        // Increment rate limit
        $rateLimitData['count']++;
        Cache::put($rateLimitKey, $rateLimitData, now()->addMinutes(10));
        
        // Fetch from API
        $gstLookup = GstLookup::fetchFromApi($gstNumber);

        if (!$gstLookup) {
            return response()->json([
                'success' => false,
                'message' => 'GST number not found or invalid.'
            ], 404);
        }

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
            ],
            'from_cache' => false,
        ]);
    }

    /**
     * Get state ID from state name
     */
    private function getStateIdFromName($stateName)
    {
        if (!$stateName) {
            return null;
        }

        $stateName = trim($stateName);
        $state = State::whereRaw('LOWER(name) = ?', [strtolower($stateName)])->first();
        
        if (!$state) {
            $state = State::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($stateName) . '%'])->first();
        }
        
        return $state ? $state->id : null;
    }

    /**
     * Submit form
     */
    public function submitForm(Request $request)
    {
        try {
            // Verify reCAPTCHA if enabled
            if (config('constants.RECAPTCHA_ENABLED', false)) {
                $recaptchaResponse = $request->input('g-recaptcha-response');
                if (!$this->verifyRecaptcha($recaptchaResponse)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'reCAPTCHA verification failed. Please try again.',
                        'errors' => ['recaptcha' => ['reCAPTCHA verification failed']]
                    ], 422);
                }
            }
            
            // Get session data
            $sessionData = session('exhibitor_registration_draft', []);
            $allData = array_merge($sessionData, $request->all());
            
            // Map billing fields to old field names for validation compatibility
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
                $allData['landline'] = $request->input('billing_telephone_national');
            } elseif ($request->has('billing_telephone')) {
                $allData['landline'] = $request->input('billing_telephone');
            }
            if ($request->has('billing_website')) {
                $allData['website'] = $request->input('billing_website');
            }
            
            // Map contact mobile for validation
            if ($request->has('contact_mobile_national') && !empty($request->input('contact_mobile_national'))) {
                $allData['contact_mobile'] = $request->input('contact_mobile_national');
            }
            
            // Validation rules
            $rules = [
                'booth_space' => 'required|in:Raw,Shell',
                'booth_size' => 'required|string',
                'sector' => 'required|string',
                'subsector' => 'required|string',
                'category' => 'required|in:Exhibitor,Sponsor',
                'billing_company_name' => 'required|string|max:255',
                'billing_address' => 'required|string',
                'billing_city' => 'required|string|max:255',
                'billing_state_id' => 'required|exists:states,id',
                'billing_postal_code' => 'required|string|max:20',
                'billing_telephone' => 'required|string',
                'billing_website' => 'required|url',
                'billing_email' => 'nullable|email|max:255',
                'exhibitor_name' => 'required|string|max:255',
                'exhibitor_address' => 'required|string',
                'exhibitor_city' => 'required|string|max:255',
                'exhibitor_state_id' => 'required|exists:states,id',
                'exhibitor_postal_code' => 'required|string|max:20',
                'exhibitor_telephone' => 'required|string',
                'exhibitor_website' => 'required|url',
                'contact_title' => 'required|in:Mr.,Mrs.,Ms.,Dr.,Prof.',
                'contact_first_name' => 'required|string|max:255',
                'contact_last_name' => 'required|string|max:255',
                'contact_designation' => 'required|string|max:255',
                'contact_email' => 'required|email|max:255',
                'contact_mobile' => 'required|string',
                'tan_status' => 'required|in:Registered,Unregistered',
                'gst_status' => 'required|in:Registered,Unregistered',
                'pan_no' => 'required|string|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
                'sales_executive_name' => 'required|string|max:255',
            ];
            
            // Conditional validations
            if (($allData['sector'] ?? '') === 'Others') {
                $rules['other_sector_name'] = 'required|string|max:255';
            }
            
            if (($allData['tan_status'] ?? '') === 'Registered') {
                $rules['tan_no'] = 'required|string|max:50';
            }
            
            if (($allData['gst_status'] ?? '') === 'Registered') {
                $rules['gst_no'] = 'required|string|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/';
            }
            
            $validator = Validator::make($allData, $rules);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please fix the validation errors below.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Calculate price
            $eventConfig = DB::table('event_configurations')->where('id', 1)->first();
            $gstRate = ($eventConfig->gst_rate ?? 18) / 100;
            
            // Get processing charge rate (default 3% for Indian payments)
            $processingRate = ($eventConfig->ind_processing_charge ?? 3) / 100;
            
            $ratePerSqm = 0;
            if ($allData['booth_space'] === 'Shell') {
                $ratePerSqm = $eventConfig->shell_scheme_rate ?? 13000;
            } else {
                $ratePerSqm = $eventConfig->raw_space_rate ?? 12000;
            }
            
            $boothSize = preg_replace('/[^0-9]/', '', $allData['booth_size']);
            $sqm = (int) $boothSize;
            $basePrice = $sqm * $ratePerSqm;
            $gstAmount = $basePrice * $gstRate;
            
            // Calculate processing charges on (base price + GST)
            $processingCharges = ($basePrice + $gstAmount) * $processingRate;
            
            $totalPrice = $basePrice + $gstAmount + $processingCharges;
            
            // Handle billing telephone for session storage
            $billingData = $allData['billing_data'] ?? [];
            $billingTelephone = '';
            if ($request->has('billing_telephone_national') && !empty($request->input('billing_telephone_national'))) {
                $billingCountryCode = $request->input('billing_telephone_country_code') ?: '91';
                $billingNational = preg_replace('/\s+/', '', trim($request->input('billing_telephone_national')));
                $billingTelephone = $billingCountryCode . '-' . $billingNational;
            } elseif (isset($billingData['telephone'])) {
                $billingTelephone = $billingData['telephone'];
            } elseif ($request->has('billing_telephone')) {
                $billingTelephone = $request->input('billing_telephone');
            }
            
            // Handle contact mobile for session storage
            $contactData = $allData['contact_data'] ?? [];
            $contactMobile = '';
            if ($request->has('contact_mobile_national') && !empty($request->input('contact_mobile_national'))) {
                $contactCountryCode = $request->input('contact_country_code') ?: '91';
                $contactNational = preg_replace('/\s+/', '', trim($request->input('contact_mobile_national')));
                $contactMobile = $contactCountryCode . '-' . $contactNational;
            } elseif (isset($contactData['mobile'])) {
                $contactMobile = $contactData['mobile'];
            } elseif ($request->has('contact_mobile')) {
                $contactMobile = $request->input('contact_mobile');
            }
            
            // Store validated and calculated data in session (no DB insert yet)
            $submittedData = [
                'all_data' => $allData,
                'billing_data' => $billingData,
                'exhibitor_data' => $allData['exhibitor_data'] ?? [],
                'contact_data' => $contactData,
                'billing_telephone' => $billingTelephone,
                'contact_mobile' => $contactMobile,
                'pricing' => [
                    'base_price' => $basePrice,
                    'gst_amount' => $gstAmount,
                    'processing_charges' => $processingCharges,
                    'processing_rate' => $eventConfig->ind_processing_charge ?? 3,
                    'gst_rate' => $eventConfig->gst_rate ?? 18,
                    'total_price' => $totalPrice,
                ],
                'submitted_at' => now()->toDateTimeString(),
            ];
            
            session(['exhibitor_registration_submitted' => $submittedData]);
            
            // Clear draft session
            session()->forget('exhibitor_registration_draft');
            
            return response()->json([
                'success' => true,
                'message' => 'Registration validated successfully!',
                'redirect_url' => route('exhibitor-registration.preview')
            ]);
            
        } catch (\Exception $e) {
            Log::error('Exhibitor Registration Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred. Please try again.'
            ], 500);
        }
    }

    /**
     * Show preview page (from session data, before DB insertion)
     */
    public function showPreview()
    {
        // Get submitted data from session
        $submittedData = session('exhibitor_registration_submitted');
        
        if (!$submittedData) {
            // No submitted data, redirect back to form
            return redirect()->route('exhibitor-registration.register')
                ->with('error', 'Please submit the form first.');
        }
        
        // Check if application already exists (user clicked proceed to payment)
        if (isset($submittedData['application_id'])) {
            $application = Application::with(['eventContact', 'invoice', 'state', 'country'])
                ->find($submittedData['application_id']);
            
            if ($application) {
                return view('exhibitor-registration.preview', compact('application'));
            }
        }
        
        // Pass session data to view for preview
        return view('exhibitor-registration.preview', ['submittedData' => $submittedData]);
    }
    
    /**
     * Create application from session data (called when Proceed to Payment is clicked)
     */
    public function createApplicationFromSession(Request $request)
    {
        // Get submitted data from session
        $submittedData = session('exhibitor_registration_submitted');
        
        if (!$submittedData) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please submit the form again.'
            ], 400);
        }
        
        $allData = $submittedData['all_data'];
        $billingData = $submittedData['billing_data'];
        $contactData = $submittedData['contact_data'];
        $pricing = $submittedData['pricing'];
        
        // Get event_id (default to 1 if not set)
        $eventId = $allData['event_id'] ?? 1;
        
        // Start database transaction
        DB::beginTransaction();
        
        try {
            // Check if contact email already exists in users table
            $email = $allData['contact_email'];
            $existingUser = \App\Models\User::where('email', $email)->first();
            
            // If user exists, return error immediately
            if ($existingUser) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'This email is already registered with us. Please use a different email address or contact support if you need to update your registration.',
                    'errors' => [
                        'contact_email' => ['This email address is already registered with us. Please use a different email or contact support.']
                    ]
                ], 422);
            }
            
            // Create user account (since email doesn't exist)
            $user = null;
            
            // Check if an application already exists for this email and event
            // IMPORTANT: Only check "submitted" applications for email uniqueness
            // "in-progress" applications can be updated/continued
            $existingSubmittedApplication = null;
            $existingInProgressApplication = null;
            
            // Check by email addresses (only submitted status)
            $existingSubmittedApplication = Application::where('application_type', 'exhibitor-registration')
                ->where('event_id', $eventId)
                ->where('status', 'submitted') // Only check submitted applications
                ->where(function($query) use ($email, $billingData) {
                    // Check by company email
                    $query->where('company_email', $email);
                    
                    // Also check billing email if different from contact email
                    $billingEmail = $billingData['email'] ?? null;
                    if (!empty($billingEmail) && $billingEmail !== $email) {
                        $query->orWhere('company_email', $billingEmail);
                    }
                })
                ->first();
            
            // If found submitted application, reject (email already used)
            if ($existingSubmittedApplication) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'You have already registered for this event with this email address. Each email can only register once per event.',
                    'errors' => [
                        'email' => ['An application already exists for this email address and event. Please use a different email or contact support if you need to update your registration.']
                    ]
                ], 422);
            }
            
            // Create new user account (email doesn't exist, so safe to create)
            $password = Str::random(12);
            $user = \App\Models\User::create([
                'name' => $allData['contact_first_name'] . ' ' . $allData['contact_last_name'],
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'exhibitor',
            ]);
            
            // Send credentials email
            $contactName = $allData['contact_first_name'] . ' ' . $allData['contact_last_name'];
            $setupProfileUrl = config('app.url');
            Mail::to($email)->send(new UserCredentialsMail($contactName, $setupProfileUrl, $email, $password));
            
            // No existing in-progress application since user doesn't exist yet
            $application = null;
            
            // Use contact email as company email (mandatory field)
            $companyEmail = $allData['contact_email'] ?? $email;
            
            // Contact name for billing details
            $contactName = trim(($contactData['first_name'] ?? $allData['contact_first_name'] ?? '') . ' ' . ($contactData['last_name'] ?? $allData['contact_last_name'] ?? ''));
            if (empty($contactName)) {
                $contactName = $billingData['company_name'] ?? $allData['billing_company_name'] ?? '';
            }
            
            // Generate unique application_id if creating new application
            if (!$application) {
                $applicationId = $this->generateApplicationId();
            } else {
                $applicationId = $application->application_id;
            }
            
            // Create or update application using billing data
            if (!$application) {
                $application = Application::create([
                    'application_id' => $applicationId,
                    'user_id' => $user->id,
                    'event_id' => $eventId,
                    'company_name' => $billingData['company_name'] ?? $allData['billing_company_name'] ?? '',
                    'company_email' => $companyEmail,
                    'address' => $billingData['address'] ?? $allData['billing_address'] ?? '',
                    'city_id' => $billingData['city'] ?? $allData['billing_city'] ?? '',
                    'state_id' => $billingData['state_id'] ?? $allData['billing_state_id'] ?? null,
                    'postal_code' => $billingData['postal_code'] ?? $allData['billing_postal_code'] ?? '',
                    'country_id' => $billingData['country_id'] ?? $allData['billing_country_id'] ?? Country::where('code', 'IN')->first()->id,
                    'landline' => $submittedData['billing_telephone'] ?? '',
                    'website' => $this->normalizeWebsiteUrl($billingData['website'] ?? $allData['billing_website'] ?? ''),
                    'stall_category' => $allData['booth_space'],
                    'interested_sqm' => $allData['booth_size'],
                    'sector_id' => $allData['sector'],
                    'subSector' => $allData['subsector'],
                    'type_of_business' => $allData['other_sector_name'] ?? null,
                    'gst_compliance' => ($allData['gst_status'] ?? '') === 'Registered' ? 1 : 0,
                    'gst_no' => $allData['gst_no'] ?? null,
                    'pan_no' => $allData['pan_no'],
                    'tan_no' => $allData['tan_no'] ?? null,
                    'promocode' => $allData['promocode'] ?? null,
                    'status' => 'submitted',
                    'application_type' => 'exhibitor-registration',
                ]);
            } else {
                // Update existing in-progress application
                $application->update([
                    'application_id' => $applicationId,
                    'user_id' => $user->id,
                    'event_id' => $eventId,
                    'company_name' => $billingData['company_name'] ?? $allData['billing_company_name'] ?? '',
                    'company_email' => $companyEmail,
                    'address' => $billingData['address'] ?? $allData['billing_address'] ?? '',
                    'city_id' => $billingData['city'] ?? $allData['billing_city'] ?? '',
                    'state_id' => $billingData['state_id'] ?? $allData['billing_state_id'] ?? null,
                    'postal_code' => $billingData['postal_code'] ?? $allData['billing_postal_code'] ?? '',
                    'country_id' => $billingData['country_id'] ?? $allData['billing_country_id'] ?? Country::where('code', 'IN')->first()->id,
                    'landline' => $submittedData['billing_telephone'] ?? '',
                    'website' => $this->normalizeWebsiteUrl($billingData['website'] ?? $allData['billing_website'] ?? ''),
                    'stall_category' => $allData['booth_space'],
                    'interested_sqm' => $allData['booth_size'],
                    'sector_id' => $allData['sector'],
                    'subSector' => $allData['subsector'],
                    'type_of_business' => $allData['other_sector_name'] ?? null,
                    'gst_compliance' => ($allData['gst_status'] ?? '') === 'Registered' ? 1 : 0,
                    'gst_no' => $allData['gst_no'] ?? null,
                    'pan_no' => $allData['pan_no'],
                    'tan_no' => $allData['tan_no'] ?? null,
                    'promocode' => $allData['promocode'] ?? null,
                    'status' => 'submitted',
                ]);
            }
            
            // Create or update event contact with correct field names
            $contactMobile = $submittedData['contact_mobile'] ?? '';
            $contact = EventContact::where('application_id', $application->id)->first();
            if (!$contact) {
                $contact = new EventContact();
                $contact->application_id = $application->id;
            }
            $contact->salutation = $contactData['title'] ?? $allData['contact_title'] ?? null;
            $contact->first_name = $contactData['first_name'] ?? $allData['contact_first_name'];
            $contact->last_name = $contactData['last_name'] ?? $allData['contact_last_name'];
            $contact->designation = $contactData['designation'] ?? $allData['contact_designation'];
            $contact->job_title = $contactData['designation'] ?? $allData['contact_designation']; // job_title same as designation
            $contact->email = $contactData['email'] ?? $allData['contact_email'];
            $contact->contact_number = $contactMobile; // Use contact_number instead of mobile
            $contact->save();
            
            // Create or update billing detail
            $billingDetail = \App\Models\BillingDetail::where('application_id', $application->id)->first();
            if (!$billingDetail) {
                $billingDetail = new \App\Models\BillingDetail();
                $billingDetail->application_id = $application->id;
            }
            
            // Use billing data from form
            if ($billingData && !empty($billingData)) {
                $billingDetail->billing_company = $billingData['company_name'] ?? $allData['billing_company_name'] ?? '';
                $billingDetail->contact_name = $contactName;
                $billingDetail->email = $billingData['email'] ?? $allData['billing_email'] ?? $email;
                $billingDetail->phone = $submittedData['billing_telephone'] ?? '';
                $billingDetail->address = $billingData['address'] ?? $allData['billing_address'] ?? '';
                $billingDetail->city_id = !empty($billingData['city']) ? trim($billingData['city']) : ($allData['billing_city'] ?? null);
                $billingDetail->state_id = $billingData['state_id'] ?? $allData['billing_state_id'] ?? null;
                $billingDetail->country_id = $billingData['country_id'] ?? $allData['billing_country_id'] ?? Country::where('code', 'IN')->first()->id;
                $billingDetail->postal_code = $billingData['postal_code'] ?? $allData['billing_postal_code'] ?? '';
                $billingDetail->gst_id = $allData['gst_no'] ?? null;
                $billingDetail->same_as_basic = '0'; // Different from exhibitor
            } else {
                // Fallback: Use application data if billing data not available
                $billingDetail->billing_company = $application->company_name ?? '';
                $billingDetail->contact_name = $contactName;
                $billingDetail->email = $email;
                $billingDetail->phone = $application->landline ?? '';
                $billingDetail->address = $application->address ?? '';
                $billingDetail->city_id = $application->city_id ?? null;
                $billingDetail->state_id = $application->state_id ?? null;
                $billingDetail->country_id = $application->country_id ?? null;
                $billingDetail->postal_code = $application->postal_code ?? '';
                $billingDetail->gst_id = $application->gst_no ?? null;
                $billingDetail->same_as_basic = '0';
            }
            $billingDetail->save();
            
            // Create or update invoice
            $invoice = Invoice::where('application_id', $application->id)->first();
            if (!$invoice) {
                $invoice = new Invoice();
                $invoice->application_id = $application->id;
            }
            // Use application_id (TIN number) for both application_no and invoice_no for consistency
            $invoice->application_no = $application->application_id;
            $invoice->invoice_no = $application->application_id;
            $invoice->type = 'Exhibitor Registration';
            $invoice->amount = $pricing['total_price'];
            $invoice->price = $pricing['base_price'];
            $invoice->gst = $pricing['gst_amount'];
            $invoice->gst_amount = $pricing['gst_amount'];
            $invoice->processing_chargesRate = $pricing['processing_rate'];
            $invoice->processing_charges = $pricing['processing_charges'];
            $invoice->total_final_price = $pricing['total_price'];
            $invoice->currency = 'INR';
            $invoice->status = 'pending';
            $invoice->payment_status = 'unpaid';
            $invoice->payment_due_date = null;
            $invoice->save();
            
            DB::commit();
            
            // Update session with application_id for security
            $submittedData['application_id'] = $application->id;
            session(['exhibitor_registration_submitted' => $submittedData]);
            session(['exhibitor_registration_application_id' => $application->application_id]);
            
            // Send confirmation email with contact information
            Mail::to($email)->send(new ExhibitorRegistrationMail($application, $invoice, $contact));
            
            return response()->json([
                'success' => true,
                'message' => 'Application created successfully!',
                'application_id' => $application->application_id,
                'redirect_url' => route('exhibitor-registration.payment', $application->application_id)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exhibitor Registration Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the application. Please try again.'
            ], 500);
        }
    }

    /**
     * Show payment page (uses application_id/TIN from URL, not database ID)
     */
    public function showPayment($applicationId)
    {
        // Find application by application_id (TIN number), not database ID
        $application = Application::with(['invoice', 'eventContact', 'state', 'country'])
            ->where('application_id', $applicationId)
            ->where('application_type', 'exhibitor-registration')
            ->firstOrFail();
        
        // Security: Verify ownership using session
        $sessionApplicationId = session('exhibitor_registration_application_id');
        
        // Check if the application_id (TIN number) in session matches the application
        if (!$sessionApplicationId || $sessionApplicationId !== $application->application_id) {
            // Unauthorized access attempt - redirect back to form
            return redirect()->route('exhibitor-registration.register')
                ->with('error', 'Unauthorized access. Please submit the form again.');
        }
        
        if (!$application->invoice) {
            return redirect()->route('exhibitor-registration.preview')
                ->with('error', 'Invoice not found.');
        }
        
        return view('exhibitor-registration.payment', compact('application'));
    }
    
    /**
     * Generate unique application_id using APPLICATION_ID_PREFIX
     * Format: TIN-BTS-2026-EXH-XXXXXX (6-digit random number)
     */
    private function generateApplicationId()
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
     * Process payment (redirect to payment gateway)
     * Uses application_id (TIN) from URL, not database ID
     */
    public function processPayment(Request $request, $applicationId)
    {
        // Find application by application_id (TIN number), not database ID
        $application = Application::with(['invoice'])
            ->where('application_id', $applicationId)
            ->where('application_type', 'exhibitor-registration')
            ->firstOrFail();
        
        // Security: Verify ownership using session
        $sessionApplicationId = session('exhibitor_registration_application_id');
        
        // Check if the application_id (TIN number) in session matches the application
        if (!$sessionApplicationId || $sessionApplicationId !== $application->application_id) {
            // Unauthorized access attempt - redirect back to form
            return redirect()->route('exhibitor-registration.register')
                ->with('error', 'Unauthorized access. Please submit the form again.');
        }
        
        $invoice = Invoice::where('application_id', $application->id)->firstOrFail();
        
        if ($invoice->payment_status === 'paid') {
            return redirect()->route('exhibitor-registration.confirmation', $application->application_id)
                ->with('info', 'Payment already processed');
        }
        
        // Redirect to payment gateway based on payment method
        $paymentMethod = $request->input('payment_method', 'CCAvenue');
        
        if ($paymentMethod === 'Bank Transfer') {
            // For bank transfer, show instructions or redirect to a page
            return redirect()->route('exhibitor-registration.confirmation', $application->application_id)
                ->with('info', 'Please contact us for bank transfer instructions.');
        } elseif ($paymentMethod === 'PayPal' || $invoice->currency === 'USD') {
            // PayPal
            return redirect()->route('paypal.form', ['id' => $invoice->invoice_no]);
        } else {
            // CCAvenue (default for INR)
            return redirect()->route('payment.ccavenue', ['id' => $invoice->invoice_no]);
        }
    }
    
    /**
     * Show confirmation page (after payment success)
     * Uses application_id (TIN) from URL, not database ID
     */
    public function showConfirmation($applicationId)
    {
        // Find application by application_id (TIN number), not database ID
        $application = Application::where('application_id', $applicationId)
            ->where('application_type', 'exhibitor-registration')
            ->firstOrFail();
        
        // Security: For confirmation page, we allow access after payment
        // Session may have expired, but payment was successful, so allow access
        // Log for security monitoring
        $sessionApplicationId = session('exhibitor_registration_application_id');
        if (!$sessionApplicationId || $sessionApplicationId !== $applicationId) {
            \Log::info('Exhibitor registration confirmation access', [
                'application_id' => $applicationId,
                'session_app_id' => $sessionApplicationId,
                'ip' => request()->ip(),
                'payment_status' => $application->invoices()->where('payment_status', 'paid')->exists() ? 'paid' : 'unpaid'
            ]);
        }

        $invoice = Invoice::where('application_id', $application->id)->firstOrFail();
        $contact = EventContact::where('application_id', $application->id)->first();
        
        return view('exhibitor-registration.confirmation', compact('application', 'invoice', 'contact'));
    }
}

