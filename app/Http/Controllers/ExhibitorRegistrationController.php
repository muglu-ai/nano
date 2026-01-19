<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\EventContact;
use App\Models\Invoice;
use App\Models\State;
use App\Models\Country;
use App\Models\GstLookup;
use App\Models\StartupZoneDraft;
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
use App\Mail\ExhibitorRegistrationAdminMail;

class ExhibitorRegistrationController extends Controller
{
    /**
     * Show the multi-step registration form
     */
    public function showForm(Request $request)
    {

        // Handle currency parameter from URL (ind = INR, int = USD)
        // Currency parameter is REQUIRED - if missing or invalid, redirect with error
        $currencyParam = $request->query('currency');
        $selectedCurrency = null;
        $isCurrencyReadOnly = false;
        
        // Check if currency parameter is provided
        if (!$currencyParam) {
            // No currency parameter - redirect with error message
            return redirect()->to('https://bengalurutechsummit.com/exhibition.php#exhibition-tariff')
                ->with('error', 'Invalid URL. Please select a currency option.');
        }
        
        // Validate currency parameter value
        if ($currencyParam === 'ind') {
            $selectedCurrency = 'INR';
            $isCurrencyReadOnly = true;
        } elseif ($currencyParam === 'int') {
            $selectedCurrency = 'USD';
            $isCurrencyReadOnly = true;
        } else {
            // Invalid currency parameter value - redirect with error message
            return redirect()->to('https://bengalurutechsummit.com/exhibition.php#exhibition-tariff')
                ->with('error', 'Invalid URL. Please select a valid currency option.');
        }

        // Get draft data from database (if exists)
        $sessionId = session()->getId();
        $draft = StartupZoneDraft::where('session_id', $sessionId)
            ->where('application_type', 'exhibitor-registration')
            ->active()
            ->first();
        
        // If no draft exists, create a new draft record
        if (!$draft) {
            $draft = new StartupZoneDraft();
            $draft->session_id = $sessionId;
            $draft->uuid = Str::uuid();
            $draft->application_type = 'exhibitor-registration';
            $draft->expires_at = now()->addDays(30);
            $draft->progress_percentage = 0;
            $draft->contact_data = [];
            $draft->billing_data = [];
            $draft->exhibitor_data = [];
            $draft->save();
        }

        // If currency is set from URL, override draft currency
        if ($selectedCurrency) {
            $draft->currency = $selectedCurrency;
            $draft->save();
        }
        
        // Ensure progress_percentage exists
        if (!isset($draft->progress_percentage) || $draft->progress_percentage === null) {
            $draft->progress_percentage = 0;
            $draft->save();
        }
        
        // Set default country to India if not set
        $countryId = $draft->country_id ?? null;
        if (!$countryId) {
            $india = Country::where('code', 'IN')->first();
            if ($india) {
                $countryId = $india->id;
            }
        }
        
        // Get event configuration for pricing
        $eventConfig = DB::table('event_configurations')->where('id', 1)->first();
        $shellSchemeRate = $eventConfig->shell_scheme_rate ?? 14000;
        $rawSpaceRate = $eventConfig->raw_space_rate ?? 13000;
        $shellSchemeRateUSD = $eventConfig->shell_scheme_rate_usd ?? 175;
        $rawSpaceRateUSD = $eventConfig->raw_space_rate_usd ?? 160;
        $gstRate = $eventConfig->gst_rate ?? 18;
        
        // Get dropdown data
     /*   $sectors = [
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
        ];*/
        $sectors = config('constants.sectors', []);
        
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
            'boothSizes',
            'selectedCurrency',
            'isCurrencyReadOnly'
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
            'currency' => 'required|in:INR,USD',
            'gst_rate' => 'nullable|numeric|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid parameters'
            ], 422);
        }

        // Get currency (default to INR)
        $currency = $request->input('currency', 'INR');
        
        // Get event configuration
        $eventConfig = DB::table('event_configurations')->where('id', 1)->first();
        $gstRatePercent = $request->input('gst_rate') ?? ($eventConfig->gst_rate ?? 18);
        $gstRate = $gstRatePercent / 100;
        
        // Get processing charge rate based on currency
        // For INR: use ind_processing_charge, for USD: use int_processing_charge
        if ($currency === 'USD') {
            $processingRatePercent = $eventConfig->int_processing_charge ?? 9; // Default 9% for USD
        } else {
            $processingRatePercent = $eventConfig->ind_processing_charge ?? 3; // Default 3% for INR
        }
        $processingRate = $processingRatePercent / 100;
        
        // Get rate per sqm based on booth space type and currency
        $ratePerSqm = 0;
        if ($request->input('booth_space') === 'Shell') {
            if ($currency === 'USD') {
                $ratePerSqm = $eventConfig->shell_scheme_rate_usd ?? 175;
            } else {
                $ratePerSqm = $eventConfig->shell_scheme_rate ?? 13000;
            }
        } else {
            if ($currency === 'USD') {
                $ratePerSqm = $eventConfig->raw_space_rate_usd ?? 160;
            } else {
                $ratePerSqm = $eventConfig->raw_space_rate ?? 12000;
            }
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
                'total_price' => round($totalPrice, 2),
                'currency' => $currency
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
            'company_name' => trim($request->input('billing_company_name') ?? ''),
            'address' => trim($request->input('billing_address') ?? ''),
            'country_id' => $request->input('billing_country_id'),
            'state_id' => $request->input('billing_state_id'),
            'city' => trim($request->input('billing_city') ?? ''),
            'postal_code' => trim($request->input('billing_postal_code') ?? ''),
            'telephone' => $billingTelephoneNational ? ($billingTelephoneCountryCode . '-' . $billingTelephoneNational) : '',
            'website' => $this->normalizeWebsiteUrl($request->input('billing_website') ?? ''),
            'email' => trim($request->input('billing_email') ?? ''),
            'gst_status' => $request->input('gst_status'),
            'gst_no' => trim($request->input('gst_no') ?? ''),
            'pan_no' => trim($request->input('pan_no') ?? ''),
            'tan_no' => trim($request->input('tan_no') ?? ''),
            'tan_status' => $request->input('tan_status')
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
            'first_name' => trim($request->input('contact_first_name') ?? ''),
            'last_name' => trim($request->input('contact_last_name') ?? ''),
            'designation' => trim($request->input('contact_designation') ?? ''),
            'email' => trim($request->input('contact_email') ?? ''),
            'mobile' => $mobileNational ? ($mobileCountryCode . '-' . $mobileNational) : '',
            'country_code' => $mobileCountryCode,
        ];
        
        // Check email in autoSave (non-blocking, just for early feedback)
        $contactEmail = $request->input('contact_email');
        $emailExists = false;
        if (!empty($contactEmail)) {
            $emailExists = $this->checkEmailExists(trim($contactEmail));
        }
        
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
            'name' => trim($request->input('exhibitor_name') ?? ''),
            'address' => trim($request->input('exhibitor_address') ?? ''),
            'country_id' => $request->input('exhibitor_country_id'),
            'state_id' => $request->input('exhibitor_state_id'),
            'city' => trim($request->input('exhibitor_city') ?? ''),
            'postal_code' => trim($request->input('exhibitor_postal_code') ?? ''),
            'telephone' => $exhibitorTelephoneNational ? ($exhibitorTelephoneCountryCode . '-' . $exhibitorTelephoneNational) : '',
            'website' => $this->normalizeWebsiteUrl($request->input('exhibitor_website') ?? ''),
            'email' => trim($request->input('exhibitor_email') ?? ''),
        ];
        
        // Add additional exhibitor-specific fields
        if ($request->has('sales_executive_name')) {
            $exhibitorData['sales_executive_name'] = trim($request->input('sales_executive_name'));
        }
        
        // Trim other fields
        if ($request->has('sector')) {
            $formData['sector'] = trim($request->input('sector'));
        }
        if ($request->has('subsector')) {
            $formData['subsector'] = trim($request->input('subsector'));
        }
        if ($request->has('other_sector_name')) {
            $formData['other_sector_name'] = trim($request->input('other_sector_name'));
        }
        if ($request->has('category')) {
            $exhibitorData['category'] = $request->input('category');
        }
        // if ($request->has('tan_status')) {
        //     $exhibitorData['tan_status'] = $request->input('tan_status');
        // }
        // if ($request->has('tan_no')) {
        //     $exhibitorData['tan_no'] = $request->input('tan_no');
        // }
        
        if (!empty($exhibitorData)) {
            $formData['exhibitor_data'] = $exhibitorData;
        }
        
        // Save to draft table (same as Startup Zone)
        $sessionId = session()->getId();
        $draft = StartupZoneDraft::where('session_id', $sessionId)
            ->where('application_type', 'exhibitor-registration')
            ->first();
        
        if (!$draft) {
            $draft = new StartupZoneDraft();
            $draft->session_id = $sessionId;
            $draft->uuid = Str::uuid();
            $draft->application_type = 'exhibitor-registration';
            $draft->event_id = 1; // Default event ID
            $draft->expires_at = now()->addDays(30);
        }
        
        // Update draft with form data
        $draft->contact_data = $formData['contact_data'] ?? [];
        $draft->billing_data = $formData['billing_data'] ?? [];
        
        // Merge exhibitor_data to preserve existing data
        $existingExhibitorData = $draft->exhibitor_data ?? [];
        $newExhibitorData = $formData['exhibitor_data'] ?? [];
        $draft->exhibitor_data = array_merge($existingExhibitorData, $newExhibitorData);
        
        // Store individual fields for easier access
        if (isset($formData['booth_space'])) {
            $draft->stall_category = $formData['booth_space'];
        }
        if (isset($formData['booth_size'])) {
            $draft->interested_sqm = $formData['booth_size'];
        }
        if (isset($billingData['company_name'])) {
            $draft->company_name = $billingData['company_name'];
        }
        if (isset($billingData['email'])) {
            $draft->company_email = $billingData['email'];
        }
        if (isset($billingData['address'])) {
            $draft->address = $billingData['address'];
        }
        if (isset($billingData['city'])) {
            $draft->city_id = $billingData['city'];
        }
        if (isset($billingData['state_id'])) {
            $draft->state_id = $billingData['state_id'];
        }
        if (isset($billingData['postal_code'])) {
            $draft->postal_code = $billingData['postal_code'];
        }
        if (isset($billingData['country_id'])) {
            $draft->country_id = $billingData['country_id'];
        }
        if (isset($billingData['telephone'])) {
            $draft->landline = $billingData['telephone'];
        }
        if (isset($billingData['website'])) {
            $draft->website = $billingData['website'];
        }
        if (isset($formData['sector'])) {
            $draft->sector_id = $formData['sector'];
        }
        if (isset($formData['subsector'])) {
            $draft->subSector = $formData['subsector'];
        }
        if (isset($formData['other_sector_name'])) {
            $draft->type_of_business = $formData['other_sector_name'];
        }
        if (isset($formData['gst_status'])) {
            $draft->gst_compliance = ($formData['gst_status'] === 'Registered');
        }
        if (isset($formData['gst_no'])) {
            $draft->gst_no = $formData['gst_no'];
        }
        if (isset($formData['pan_no'])) {
            $draft->pan_no = $formData['pan_no'];
        }
        if (isset($formData['promocode'])) {
            $draft->promocode = $formData['promocode'];
        }
        if (isset($formData['currency'])) {
            $draft->currency = $formData['currency'];
        }
        
        // Calculate progress percentage
        $progress = $this->calculateProgressFromData($formData);
        $draft->progress_percentage = $progress;
        
        $draft->save();
        
        // Also store in session for backward compatibility
        session(['exhibitor_registration_draft' => $formData]);
        
        // Return response with email validation warning (non-blocking for autoSave)
        $response = [
            'success' => true,
            'message' => 'Data saved successfully',
            'progress' => $progress
        ];
        
        // Add email warning if email exists (non-blocking in autoSave)
        if ($emailExists) {
            $response['email_warning'] = true;
            $response['email_message'] = 'This email is already registered. Please use a different email address.';
        }
        
        return response()->json($response);
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
            
            // List of text fields that should be trimmed and validated for leading spaces
            $textFields = [
                'booth_size', 'sector', 'subsector', 'other_sector_name',
                'billing_company_name', 'billing_address', 'billing_city', 'billing_postal_code',
                'billing_telephone', 'billing_website', 'billing_email',
                'exhibitor_name', 'exhibitor_address', 'exhibitor_city', 'exhibitor_postal_code',
                'exhibitor_telephone', 'exhibitor_website',
                'contact_first_name', 'contact_last_name', 'contact_designation',
                'contact_email', 'contact_mobile',
                'pan_no', 'tan_no', 'gst_no', 'sales_executive_name'
            ];
            
            // Trim all string inputs after validation (validation will check for leading spaces on original input)
            foreach ($textFields as $field) {
                if (isset($allData[$field]) && is_string($allData[$field])) {
                    $allData[$field] = trim($allData[$field]);
                }
                if ($request->has($field) && is_string($request->input($field))) {
                    $allData[$field] = trim($request->input($field));
                }
            }
            
            // Map billing fields to old field names for validation compatibility
            if ($request->has('billing_postal_code')) {
                $allData['postal_code'] = $allData['billing_postal_code'] ?? trim($request->input('billing_postal_code'));
            }
            if ($request->has('billing_email')) {
                $allData['company_email'] = $allData['billing_email'] ?? trim($request->input('billing_email'));
            }
            if ($request->has('billing_company_name')) {
                $allData['company_name'] = $allData['billing_company_name'] ?? trim($request->input('billing_company_name'));
            }
            if ($request->has('billing_address')) {
                $allData['address'] = $allData['billing_address'] ?? trim($request->input('billing_address'));
            }
            if ($request->has('billing_country_id')) {
                $allData['country_id'] = $request->input('billing_country_id');
            }
            if ($request->has('billing_state_id')) {
                $allData['state_id'] = $request->input('billing_state_id');
            }
            if ($request->has('billing_city')) {
                $allData['city_id'] = $allData['billing_city'] ?? trim($request->input('billing_city'));
            }
            if ($request->has('billing_telephone_national') && !empty($request->input('billing_telephone_national'))) {
                $allData['landline'] = preg_replace('/\s+/', '', trim($request->input('billing_telephone_national')));
            } elseif ($request->has('billing_telephone')) {
                $allData['landline'] = preg_replace('/\s+/', '', trim($request->input('billing_telephone')));
            }
            if ($request->has('billing_website')) {
                $allData['website'] = $allData['billing_website'] ?? trim($request->input('billing_website'));
            }
            
            // Map contact mobile for validation
            if ($request->has('contact_mobile_national') && !empty($request->input('contact_mobile_national'))) {
                $allData['contact_mobile'] = preg_replace('/\s+/', '', trim($request->input('contact_mobile_national')));
            } elseif (isset($allData['contact_mobile'])) {
                $allData['contact_mobile'] = preg_replace('/\s+/', '', trim($allData['contact_mobile']));
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
            
            // Validation rules - All text fields must not start with space
            // Note: We validate the original request input (before trimming) to catch leading spaces
            $rules = [
                'booth_space' => 'required|in:Raw,Shell',
                'booth_size' => ['required', 'string', 'regex:/^\S/'],
                'sector' => ['required', 'string', 'regex:/^\S/'],
                'subsector' => ['required', 'string', 'regex:/^\S/'],
                'category' => 'required|in:Exhibitor,Sponsor',
                'billing_company_name' => ['required', 'string', 'max:255', 'regex:/^\S/'],
                'billing_address' => ['required', 'string', 'regex:/^\S/'],
                'billing_city' => ['required', 'string', 'max:255', 'regex:/^\S/'],
                'billing_state_id' => 'required|exists:states,id',
                'billing_country_id' => 'required|exists:countries,id',
                'billing_postal_code' => ['required', 'string','alpha_num', 'min:4', 'max:10'],
                'billing_telephone' => 'required|string',
                'billing_website' => 'required|url',
                'billing_email' => 'nullable|email|max:255',
                'exhibitor_name' => ['required', 'string', 'max:255', 'regex:/^\S/'],
                'exhibitor_address' => ['required', 'string', 'regex:/^\S/'],
                'exhibitor_city' => ['required', 'string', 'max:255', 'regex:/^\S/'],
                'exhibitor_state_id' => 'required|exists:states,id',
                'exhibitor_country_id' => 'required|exists:countries,id',
                'exhibitor_postal_code' => ['required', 'string', 'alpha_num', 'min:4', 'max:10'],
                'exhibitor_telephone' => 'required|string',
                'exhibitor_website' => 'required|url',
                'contact_title' => 'required|in:Mr.,Mrs.,Ms.,Dr.,Prof.',
                'contact_first_name' => ['required', 'string', 'max:255', 'regex:/^\S/'],
                'contact_last_name' => ['required', 'string', 'max:255', 'regex:/^\S/'],
                'contact_designation' => ['required', 'string', 'max:255', 'regex:/^\S/'],
                'contact_email' => ['required', 'email', 'max:255', 'regex:/^\S/'],
                'contact_mobile' => ['required', function ($attribute, $value, $fail) use ($request) {

                    $code = $request->contact_country_code;
            
                    //  India: 10 digits only
                    if ($code === '+91') {
                        if (!preg_match('/^[0-9]{9}$/', $value)) {
                            $fail('Invalid Mobile Number');
                        }
                    }
            
                    //  Other countries: alphanumeric 8-15
                    else {
                        if (!preg_match('/^[A-Za-z0-9]{8,15}$/', $value)) {
                            $fail('Invalid Mobile Number');
                        }
                    }
                }],
                'tan_status' => 'required|in:Registered,Unregistered',
                'gst_status' => 'required|in:Registered,Unregistered',
                'pan_no' => 'required|string|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
                'sales_executive_name' => ['required', 'string', 'max:255', 'regex:/^\S/'],
            ];
            
            // Conditional validations
            if (($request->input('sector') ?? '') === 'Others') {
                $rules['other_sector_name'] = ['required', 'string', 'max:255', 'regex:/^\S/'];
            }
            
            if (($request->input('tan_status') ?? '') === 'Registered') {
                $rules['tan_no'] = ['required', 'string', 'max:50', 'regex:/^\S/'];
            }
            
            if (($request->input('gst_status') ?? '') === 'Registered') {
                $rules['gst_no'] = 'required|string|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/';
            }
            
            // Custom validation messages
            $messages = [
                '*.regex' => 'The :attribute field cannot start with a space.',
                'billing_country_id.required' => 'The billing country field is required.',
                'billing_country_id.exists' => 'The selected billing country is invalid.',
                'exhibitor_country_id.required' => 'The exhibitor country field is required.',
                'exhibitor_country_id.exists' => 'The selected exhibitor country is invalid.',
            ];
            
            // Validate using original request data (before trimming) to catch leading spaces
            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please fix the validation errors below.',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get currency from form
            $currency = $request->input('currency', 'INR');
            
            // Calculate price
            $eventConfig = DB::table('event_configurations')->where('id', 1)->first();
            $gstRate = ($eventConfig->gst_rate ?? 18) / 100;
            
            // Get processing charge rate based on currency
            if ($currency === 'USD') {
                $processingRate = ($eventConfig->int_processing_charge ?? 9.5) / 100; // Default 9.5% for USD
            } else {
                $processingRate = ($eventConfig->ind_processing_charge ?? 3) / 100; // Default 3% for INR
            }
            
            // Get rate per sqm based on booth space type and currency
            $ratePerSqm = 0;
            if ($allData['booth_space'] === 'Shell') {
                if ($currency === 'USD') {
                    $ratePerSqm = $eventConfig->shell_scheme_rate_usd ?? 175;
                } else {
                    $ratePerSqm = $eventConfig->shell_scheme_rate ?? 13000;
                }
            } else {
                if ($currency === 'USD') {
                    $ratePerSqm = $eventConfig->raw_space_rate_usd ?? 160;
                } else {
                    $ratePerSqm = $eventConfig->raw_space_rate ?? 12000;
                }
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
            
            // Save to draft table (NOT creating application yet)
            $sessionId = session()->getId();
            $draft = StartupZoneDraft::where('session_id', $sessionId)
                ->where('application_type', 'exhibitor-registration')
                ->first();
            
            if (!$draft) {
                $draft = new StartupZoneDraft();
                $draft->session_id = $sessionId;
                $draft->uuid = Str::uuid();
                $draft->application_type = 'exhibitor-registration';
                $draft->event_id = 1; // Default event ID
                $draft->expires_at = now()->addDays(30);
            }
            
            // Update draft with all form data
            $draft->contact_data = $contactData;
            $draft->billing_data = $billingData;
            $draft->exhibitor_data = $allData['exhibitor_data'] ?? [];
            
            // Store individual fields
            $draft->stall_category = $allData['booth_space'] ?? null;
            $draft->interested_sqm = $allData['booth_size'] ?? null;
            $draft->company_name = $billingData['company_name'] ?? null;
            $draft->company_email = $billingData['email'] ?? $allData['billing_email'] ?? null;
            $draft->address = $billingData['address'] ?? null;
            $draft->city_id = $billingData['city'] ?? null;
            $draft->state_id = $billingData['state_id'] ?? null;
            $draft->postal_code = $billingData['postal_code'] ?? null;
            $draft->country_id = $billingData['country_id'] ?? null;
            $draft->landline = $billingTelephone;
            $draft->website = $billingData['website'] ?? null;
            $draft->sector_id = $allData['sector'] ?? null;
            $draft->subSector = $allData['subsector'] ?? null;
            $draft->type_of_business = $allData['other_sector_name'] ?? null;
            $draft->gst_compliance = ($allData['gst_status'] ?? '') === 'Registered';
            $draft->gst_no = $allData['gst_no'] ?? null;
            $draft->pan_no = $allData['pan_no'] ?? null;
            $draft->promocode = $allData['promocode'] ?? null;
            
            // Store additional exhibitor-specific data in JSON fields
            $exhibitorData = $allData['exhibitor_data'] ?? [];
            // $exhibitorData['tan_status'] = $allData['tan_status'] ?? null;
            // $exhibitorData['tan_no'] = $allData['tan_no'] ?? null;
            $exhibitorData['sales_executive_name'] = !empty($allData['sales_executive_name']) ? trim($allData['sales_executive_name']) : null;
            $exhibitorData['category'] = $allData['category'] ?? null;
            $draft->exhibitor_data = $exhibitorData;
            
            // Store pricing in pricing_data column
            $pricingData = [
                'base_price' => $basePrice,
                'gst_amount' => $gstAmount,
                'processing_charges' => $processingCharges,
                'processing_rate' => $currency === 'USD' ? ($eventConfig->int_processing_charge ?? 9.5) : ($eventConfig->ind_processing_charge ?? 3),
                'gst_rate' => $eventConfig->gst_rate ?? 18,
                'total_price' => $totalPrice,
                'sqm' => $sqm,
                'rate_per_sqm' => $ratePerSqm,
                'currency' => $currency,
            ];
            
            // Store pricing data and currency in draft
            $draft->pricing_data = $pricingData;
            $draft->currency = $currency;
            
            // Calculate progress
            $progress = $this->calculateProgressFromData($allData);
            $draft->progress_percentage = $progress;
            
            $draft->save();
            
            // Store pricing in session for preview page (backward compatibility)
            session(['exhibitor_registration_pricing' => $pricingData]);
            
            // Also store in session for backward compatibility
            session(['exhibitor_registration_draft' => $allData]);
            
            \Log::info('Exhibitor Registration: Draft saved, NOT creating application', [
                'draft_id' => $draft->id,
                'session_id' => $sessionId,
                'message' => 'Data saved to startup_zone_drafts only. Application will be created when user clicks Proceed to Payment.'
            ]);
            
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
     * Show preview page (from draft table, before application creation)
     */
    public function showPreview()
    {
        // Check if application_id is provided (after draft restoration)
        if (request()->has('application_id')) {
            $applicationId = request()->query('application_id');
            $application = Application::where('application_id', $applicationId)
                ->where('application_type', 'exhibitor-registration')
                ->with(['eventContact', 'invoice', 'state', 'country'])
                ->first();
            
            if ($application) {
                return view('exhibitor-registration.preview', compact('application'));
            }
        }
        
        // Get draft from database
        $sessionId = session()->getId();
        $draft = StartupZoneDraft::where('session_id', $sessionId)
            ->where('application_type', 'exhibitor-registration')
            ->active()
            ->first();
        
        if (!$draft) {
            return redirect()->route('exhibitor-registration.register')
                ->with('error', 'No draft found. Please submit the form again.');
        }
        
        // Get currency from draft
        $currency = $draft->currency ?? 'INR';
        
        // Get pricing from draft pricing_data column (preferred) or session (fallback)
        $pricing = $draft->pricing_data ?? session('exhibitor_registration_pricing', []);
        
        // If pricing not in draft or session, calculate it
        if (empty($pricing)) {
            $eventConfig = DB::table('event_configurations')->where('id', 1)->first();
            $gstRate = ($eventConfig->gst_rate ?? 18) / 100;
            
            // Get processing rate based on currency
            if ($currency === 'USD') {
                $processingRate = ($eventConfig->usd_processing_charge ?? 0) / 100;
            } else {
                $processingRate = ($eventConfig->ind_processing_charge ?? 3) / 100;
            }
            
            // Get rate per sqm based on currency
            $ratePerSqm = 0;
            if ($draft->stall_category === 'Shell') {
                if ($currency === 'USD') {
                    $ratePerSqm = $eventConfig->shell_scheme_rate_usd ?? 175;
                } else {
                    $ratePerSqm = $eventConfig->shell_scheme_rate ?? 13000;
                }
            } else {
                if ($currency === 'USD') {
                    $ratePerSqm = $eventConfig->raw_space_rate_usd ?? 160;
                } else {
                    $ratePerSqm = $eventConfig->raw_space_rate ?? 12000;
                }
            }
            
            $boothSize = preg_replace('/[^0-9]/', '', $draft->interested_sqm ?? '0');
            $sqm = (int) $boothSize;
            $basePrice = $sqm * $ratePerSqm;
            $gstAmount = $basePrice * $gstRate;
            $processingCharges = ($basePrice + $gstAmount) * $processingRate;
            $totalPrice = $basePrice + $gstAmount + $processingCharges;
            
            $pricing = [
                'base_price' => $basePrice,
                'gst_amount' => $gstAmount,
                'processing_charges' => $processingCharges,
                'processing_rate' => $currency === 'USD' ? ($eventConfig->int_processing_charge ?? 9.5) : ($eventConfig->ind_processing_charge ?? 3),
                'gst_rate' => $eventConfig->gst_rate ?? 18,
                'total_price' => $totalPrice,
                'sqm' => $sqm,
                'rate_per_sqm' => $ratePerSqm,
                'currency' => $currency,
            ];
        }
        
        // Extract data from draft
        $billingData = $draft->billing_data ?? [];
        $exhibitorData = $draft->exhibitor_data ?? [];
        $contactData = $draft->contact_data ?? [];
        
        // Log for debugging
        \Log::info('Exhibitor Registration Preview: Draft data extracted', [
            'draft_id' => $draft->id,
            'has_billing_data' => !empty($billingData),
            'has_exhibitor_data' => !empty($exhibitorData),
            'has_contact_data' => !empty($contactData),
            'billing_data_keys' => array_keys($billingData),
            'exhibitor_data_keys' => array_keys($exhibitorData),
            'contact_data_keys' => array_keys($contactData),
        ]);
        
        return view('exhibitor-registration.preview', compact(
            'draft',
            'billingData',
            'exhibitorData',
            'contactData',
            'pricing',
            'currency'
        ));

       
    }

       
    
    /**
     * Create application from draft (called when Proceed to Payment is clicked)
     */
    public function createApplicationFromSession(Request $request)
    {
        // Get draft from database
        $sessionId = session()->getId();
        $draft = StartupZoneDraft::where('session_id', $sessionId)
            ->where('application_type', 'exhibitor-registration')
            ->active()
            ->first();
        
        if (!$draft) {
            \Log::error('Exhibitor Registration: Draft not found', [
                'session_id' => $sessionId
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Draft not found. Please submit the form again.'
            ], 400);
        }
        
        \Log::info('Exhibitor Registration: Creating application from draft', [
            'draft_id' => $draft->id,
            'session_id' => $sessionId
        ]);
        
        // Extract data from draft
        $billingData = $draft->billing_data ?? [];
        $exhibitorData = $draft->exhibitor_data ?? [];
        $contactData = $draft->contact_data ?? [];
        
        \Log::info('Exhibitor Registration: Draft data extracted', [
            'has_billing_data' => !empty($billingData),
            'has_exhibitor_data' => !empty($exhibitorData),
            'has_contact_data' => !empty($contactData),
            'contact_email' => $contactData['email'] ?? 'not_set',
        ]);
        
        // Get currency from draft
        $currency = $draft->currency ?? 'INR';
        
        // Get pricing from draft pricing_data column (preferred) or session (fallback)
        $pricing = $draft->pricing_data ?? session('exhibitor_registration_pricing', []);
        if (empty($pricing)) {
            $eventConfig = DB::table('event_configurations')->where('id', 1)->first();
            $gstRate = ($eventConfig->gst_rate ?? 18) / 100;
            
            // Get processing rate based on currency
            if ($currency === 'USD') {
                $processingRate = ($eventConfig->usd_processing_charge ?? 0) / 100;
            } else {
                $processingRate = ($eventConfig->ind_processing_charge ?? 3) / 100;
            }
            
            // Get rate per sqm based on currency
            $ratePerSqm = 0;
            if ($draft->stall_category === 'Shell') {
                if ($currency === 'USD') {
                    $ratePerSqm = $eventConfig->shell_scheme_rate_usd ?? 175;
                } else {
                    $ratePerSqm = $eventConfig->shell_scheme_rate ?? 13000;
                }
            } else {
                if ($currency === 'USD') {
                    $ratePerSqm = $eventConfig->raw_space_rate_usd ?? 160;
                } else {
                    $ratePerSqm = $eventConfig->raw_space_rate ?? 12000;
                }
            }
            
            $boothSize = preg_replace('/[^0-9]/', '', $draft->interested_sqm ?? '0');
            $sqm = (int) $boothSize;
            $basePrice = $sqm * $ratePerSqm;
            $gstAmount = $basePrice * $gstRate;
            $processingCharges = ($basePrice + $gstAmount) * $processingRate;
            $totalPrice = $basePrice + $gstAmount + $processingCharges;
            
            $pricing = [
                'base_price' => $basePrice,
                'gst_amount' => $gstAmount,
                'processing_charges' => $processingCharges,
                'processing_rate' => $currency === 'USD' ? ($eventConfig->int_processing_charge ?? 9.5) : ($eventConfig->ind_processing_charge ?? 3),
                'gst_rate' => $eventConfig->gst_rate ?? 18,
                'total_price' => $totalPrice,
                'sqm' => $sqm,
                'rate_per_sqm' => $ratePerSqm,
                'currency' => $currency,
            ];
        }
        
        // Get currency from draft
        $currency = $draft->currency ?? 'INR';
        
        // Build allData from draft for compatibility
        $allData = [
            'booth_space' => $draft->stall_category,
            'booth_size' => $draft->interested_sqm,
            'sector' => $draft->sector_id,
            'subsector' => $draft->subSector,
            'other_sector_name' => $draft->type_of_business,
            'gst_status' => $draft->gst_compliance ? 'Registered' : 'Unregistered',
            'gst_no' => $draft->gst_no,
            'pan_no' => $draft->pan_no,
            'promocode' => $draft->promocode,
            'event_id' => $draft->event_id ?? 1,
            'tan_status' => $billingData['tan_status'] ?? 'Unregistered',
            'tan_no' => $billingData['tan_no'] ?? null,
            'sales_executive_name' => $exhibitorData['sales_executive_name'] ?? '',
            'category' => $exhibitorData['category'] ?? 'Exhibitor',
            'contact_email' => $contactData['email'] ?? '',
            'contact_first_name' => $contactData['first_name'] ?? '',
            'contact_last_name' => $contactData['last_name'] ?? '',
            'contact_title' => $contactData['title'] ?? '',
            'contact_designation' => $contactData['designation'] ?? '',
            // Map billing fields for compatibility
            'billing_company_name' => $billingData['company_name'] ?? $draft->company_name ?? '',
            'billing_address' => $billingData['address'] ?? $draft->address ?? '',
            'billing_city' => $billingData['city'] ?? $draft->city_id ?? '',
            'billing_state_id' => $billingData['state_id'] ?? $draft->state_id ?? null,
            'billing_postal_code' => $billingData['postal_code'] ?? $draft->postal_code ?? '',
            'billing_country_id' => $billingData['country_id'] ?? $draft->country_id ?? null,
            'billing_website' => $billingData['website'] ?? $draft->website ?? '',
            'billing_email' => $billingData['email'] ?? $draft->company_email ?? '',
        ];
        
        // Extract landline from draft/billingData - Keep with country code
        if (isset($billingData['telephone']) && !empty($billingData['telephone'])) {
            $landlineValue = $billingData['telephone'];
            // Keep format "country_code-national_number"
            if (preg_match('/^(\d+)-(\d+)$/', $landlineValue, $matches)) {
                $allData['landline'] = $matches[1] . '-' . $matches[2]; // Keep country code with national number
            } else {
                // If no hyphen, assume it's already formatted or needs default country code
                $allData['landline'] = $landlineValue;
            }
        } elseif (!empty($draft->landline)) {
            $landlineValue = $draft->landline;
            if (preg_match('/^(\d+)-(\d+)$/', $landlineValue, $matches)) {
                $allData['landline'] = $matches[1] . '-' . $matches[2]; // Keep country code with national number
            } else {
                $allData['landline'] = $landlineValue;
            }
        }
        
        // Extract contact mobile from contactData - Store with country code
        if (isset($contactData['mobile']) && !empty($contactData['mobile'])) {
            $mobile = trim($contactData['mobile']);
            // Remove all spaces first
            $mobile = preg_replace('/\s+/', '', $mobile);
            
            // Try to match format with hyphen: "91-9806575432" or "91-08896541230"
            if (preg_match('/^(\d{1,4})-(\d+)$/', $mobile, $matches)) {
                $nationalNumber = ltrim($matches[2], '0'); // Remove leading zeros
                if (strlen($nationalNumber) == 10) {
                    $allData['contact_mobile'] = $matches[1] . '-' . $nationalNumber;
                }
            }
            // Try to match format with optional + and country code at start: "+91-9806575432"
            elseif (preg_match('/^\+?(\d{1,3})-(\d+)$/', $mobile, $matches)) {
                $nationalNumber = ltrim($matches[2], '0'); // Remove leading zeros
                if (strlen($nationalNumber) == 10) {
                    $allData['contact_mobile'] = $matches[1] . '-' . $nationalNumber;
                }
            }
            // Try to match format like "+919801217815" or "919801217815" (no hyphen)
            elseif (preg_match('/^\+?(\d{1,4})(\d{10,})$/', $mobile, $matches)) {
                $nationalNumber = ltrim($matches[2], '0'); // Remove leading zeros
                if (strlen($nationalNumber) == 10) {
                    $allData['contact_mobile'] = $matches[1] . '-' . $nationalNumber;
                }
            }
            // Fallback: extract all digits and format with default country code (91 for India)
            else {
                $digitsOnly = preg_replace('/[^0-9]/', '', $mobile);
                if (strlen($digitsOnly) >= 10) {
                    $nationalNumber = substr($digitsOnly, -10);
                    $nationalNumber = ltrim($nationalNumber, '0'); // Remove leading zeros
                    // If there are extra digits, they might be country code
                    if (strlen($digitsOnly) > 10) {
                        $countryCode = substr($digitsOnly, 0, strlen($digitsOnly) - 10);
                        $allData['contact_mobile'] = $countryCode . '-' . $nationalNumber;
                    } else {
                        $allData['contact_mobile'] = '91-' . $nationalNumber; // Default to India
                    }
                } else {
                    $digitsOnly = ltrim($digitsOnly, '0'); // Remove leading zeros
                    $allData['contact_mobile'] = '91-' . $digitsOnly; // Default to India
                }
            }
        }
        
        // Get event_id (default to 1 if not set)
        $eventId = $allData['event_id'] ?? 1;
        
        \Log::info('Exhibitor Registration: allData built from draft', [
            'has_contact_mobile' => isset($allData['contact_mobile']),
            'contact_mobile' => $allData['contact_mobile'] ?? 'not_set',
            'has_landline' => isset($allData['landline']),
            'landline' => $allData['landline'] ?? 'not_set',
            'company_name' => $allData['billing_company_name'] ?? 'not_set',
        ]);
        
        // Start database transaction
        DB::beginTransaction();
        
        try {
            // CRITICAL: Check if contact email already exists in users table - BLOCK SUBMISSION
            $email = $allData['contact_email'];
            if (!empty($email) && $this->checkEmailExists(trim($email))) {
                // Email already exists - return error immediately and STOP processing
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'This email is already registered. Please use a different email address.',
                    'errors' => [
                        'contact_email' => ['Email already exists']
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
            
            \Log::info('Exhibitor Registration: Creating user', [
                'email' => $email,
                'name' => ($allData['contact_first_name'] ?? '') . ' ' . ($allData['contact_last_name'] ?? '')
            ]);
            
            $user = \App\Models\User::create([
                'name' => trim(($allData['contact_first_name'] ?? '') . ' ' . ($allData['contact_last_name'] ?? '')),
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'exhibitor',
            ]);
            
            \Log::info('Exhibitor Registration: User created', ['user_id' => $user->id]);
            
            // Send credentials email
            $contactName = trim(($allData['contact_first_name'] ?? '') . ' ' . ($allData['contact_last_name'] ?? ''));
            $setupProfileUrl = config('app.url');
            try {
                Mail::to($email)->send(new UserCredentialsMail($contactName, $setupProfileUrl, $email, $password));
                \Log::info('Exhibitor Registration: Credentials email sent', ['email' => $email]);
            } catch (\Exception $mailError) {
                \Log::warning('Exhibitor Registration: Failed to send credentials email', [
                    'email' => $email,
                    'error' => $mailError->getMessage()
                ]);
                // Don't fail if email fails
            }
            
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
                \Log::info('Exhibitor Registration: Creating application', [
                    'application_id' => $applicationId,
                    'user_id' => $user->id,
                    'company_name' => $billingData['company_name'] ?? $allData['billing_company_name'] ?? 'not_set',
                ]);
                
                // Store exhibitor_data in applications table
                $exhibitorName = $exhibitorData['name'] ?? $draft->company_name ?? '';
                $exhibitorEmail = $exhibitorData['email'] ?? $companyEmail;
                $exhibitorAddress = $exhibitorData['address'] ?? $draft->address ?? '';
                $exhibitorCity = $exhibitorData['city'] ?? $draft->city_id ?? '';
                $exhibitorStateId = $exhibitorData['state_id'] ?? $draft->state_id ?? null;
                $exhibitorPostalCode = $exhibitorData['postal_code'] ?? $draft->postal_code ?? '';
                $exhibitorCountryId = $exhibitorData['country_id'] ?? $draft->country_id ?? Country::where('code', 'IN')->first()->id;
                $exhibitorLandlineRaw = $exhibitorData['telephone'] ?? $draft->landline ?? '';
                // Keep format with country code "country_code-national_number"
                if (preg_match('/^(\d+)-(\d+)$/', $exhibitorLandlineRaw, $matches)) {
                    $exhibitorLandline = $matches[1] . '-' . $matches[2];
                } else {
                    $exhibitorLandline = $exhibitorLandlineRaw;
                }
                $exhibitorWebsite = $this->normalizeWebsiteUrl($exhibitorData['website'] ?? $draft->website ?? '');
                
                $application = Application::create([
                    'application_id' => $applicationId,
                    'user_id' => $user->id,
                    'event_id' => $eventId,
                    'company_name' => $exhibitorName,
                    'company_email' => $exhibitorEmail,
                    'address' => $exhibitorAddress,
                    'city_id' => $exhibitorCity,
                    'state_id' => $exhibitorStateId,
                    'postal_code' => $exhibitorPostalCode,
                    'country_id' => $exhibitorCountryId,
                    'landline' => $exhibitorLandline,
                    'website' => $exhibitorWebsite,
                    'stall_category' => $allData['booth_space'] ?? $draft->stall_category ?? '',
                    'interested_sqm' => $allData['booth_size'] ?? $draft->interested_sqm ?? '',
                    'sector_id' => $allData['sector'] ?? $draft->sector_id ?? '',
                    'subSector' => $allData['subsector'] ?? $draft->subSector ?? '',
                    'type_of_business' => $allData['other_sector_name'] ?? $draft->type_of_business ?? null,
                    'gst_compliance' => ($allData['gst_status'] ?? ($draft->gst_compliance ? 'Registered' : 'Unregistered')) === 'Registered' ? 1 : 0,
                    'gst_no' => $allData['gst_no'] ?? $draft->gst_no ?? null,
                    'pan_no' => $allData['pan_no'] ?? $draft->pan_no ?? '',
                    'tan_compliance' => ($billingData['tan_status'] ?? $allData['tan_status'] ?? 'Unregistered') === 'Registered' ? 1 : 0,
                    'tan_no' => $billingData['tan_no'] ?? $allData['tan_no'] ?? null,
                    'promocode' => $allData['promocode'] ?? $draft->promocode ?? null,
                    'salesPerson' => $allData['sales_executive_name'] ?? '',
                    'exhibitorType' => $allData['category'] ?? 'Exhibitor',
                    'payment_currency' => $currency,
                    'status' => 'initiated',
                    'submission_status' => 'submitted',
                    'submission_date' => now(),
                    'application_type' => 'exhibitor-registration',
                ]);
                
                \Log::info('Exhibitor Registration: Application created', [
                    'application_id' => $application->application_id,
                    'application_db_id' => $application->id
                ]);
            } else {
                // Update existing in-progress application with exhibitor_data
                $exhibitorName = $exhibitorData['name'] ?? $application->company_name ?? '';
                $exhibitorEmail = $exhibitorData['email'] ?? $companyEmail;
                $exhibitorAddress = $exhibitorData['address'] ?? $application->address ?? '';
                $exhibitorCity = $exhibitorData['city'] ?? $application->city_id ?? '';
                $exhibitorStateId = $exhibitorData['state_id'] ?? $application->state_id ?? null;
                $exhibitorPostalCode = $exhibitorData['postal_code'] ?? $application->postal_code ?? '';
                $exhibitorCountryId = $exhibitorData['country_id'] ?? $application->country_id ?? Country::where('code', 'IN')->first()->id;
                $exhibitorLandlineRaw = $exhibitorData['telephone'] ?? $application->landline ?? '';
                // Keep format with country code "country_code-national_number"
                if (preg_match('/^(\d+)-(\d+)$/', $exhibitorLandlineRaw, $matches)) {
                    $exhibitorLandline = $matches[1] . '-' . $matches[2];
                } else {
                    $exhibitorLandline = $exhibitorLandlineRaw;
                }
                $exhibitorWebsite = $this->normalizeWebsiteUrl($exhibitorData['website'] ?? $application->website ?? '');
                
                $application->update([
                    'application_id' => $applicationId,
                    'user_id' => $user->id,
                    'event_id' => $eventId,
                    'company_name' => $exhibitorName,
                    'company_email' => $exhibitorEmail,
                    'address' => $exhibitorAddress,
                    'city_id' => $exhibitorCity,
                    'state_id' => $exhibitorStateId,
                    'postal_code' => $exhibitorPostalCode,
                    'country_id' => $exhibitorCountryId,
                    'landline' => $exhibitorLandline,
                    'website' => $exhibitorWebsite,
                    'stall_category' => $allData['booth_space'],
                    'interested_sqm' => $allData['booth_size'],
                    'sector_id' => $allData['sector'],
                    'subSector' => $allData['subsector'],
                    'type_of_business' => $allData['other_sector_name'] ?? null,
                    'gst_compliance' => ($allData['gst_status'] ?? '') === 'Registered' ? 1 : 0,
                    'gst_no' => $allData['gst_no'] ?? null,
                    'pan_no' => $allData['pan_no'],
                    'tan_compliance' => ($billingData['tan_status'] ?? $allData['tan_status'] ?? 'Unregistered') === 'Registered' ? 1 : 0,
                    'tan_no' => $billingData['tan_no'] ?? $allData['tan_no'] ?? null,
                    'promocode' => $allData['promocode'] ?? null,
                    'salesPerson' => $allData['sales_executive_name'] ?? '',
                    'exhibitorType' => $allData['category'] ?? 'Exhibitor',
                    'payment_currency' => $currency,
                    'status' => 'initiated',
                    'submission_status' => 'submitted',
                    'submission_date' => now(),
                ]);
            }
            
            // Create or update event contact with correct field names
            $contactMobile = $allData['contact_mobile'] ?? '';
            
            \Log::info('Exhibitor Registration: Creating event contact', [
                'application_id' => $application->id,
                'contact_mobile' => $contactMobile,
                'contact_email' => $contactData['email'] ?? $allData['contact_email'] ?? 'not_set',
            ]);
            
            $contact = EventContact::where('application_id', $application->id)->first();
            if (!$contact) {
                $contact = new EventContact();
                $contact->application_id = $application->id;
            }
            $contact->salutation = $contactData['title'] ?? $allData['contact_title'] ?? null;
            $contact->first_name = $contactData['first_name'] ?? $allData['contact_first_name'] ?? '';
            $contact->last_name = $contactData['last_name'] ?? $allData['contact_last_name'] ?? '';
            $contact->designation = $contactData['designation'] ?? $allData['contact_designation'] ?? '';
            $contact->job_title = $contactData['designation'] ?? $allData['contact_designation'] ?? ''; // job_title same as designation
            $contact->email = $contactData['email'] ?? $allData['contact_email'] ?? $email;
            $contact->contact_number = $contactMobile; // Use contact_number instead of mobile
            $contact->save();
            
            \Log::info('Exhibitor Registration: Event contact saved', ['contact_id' => $contact->id]);
            
            // Create or update billing detail
            $billingDetail = \App\Models\BillingDetail::where('application_id', $application->id)->first();
            if (!$billingDetail) {
                $billingDetail = new \App\Models\BillingDetail();
                $billingDetail->application_id = $application->id;
            }
            
            // Store billing_data ONLY in billing_details table (no fallbacks to exhibitor_data or application)
            if ($billingData && !empty($billingData)) {
                $billingDetail->billing_company = $billingData['company_name'] ?? $allData['billing_company_name'] ?? '';
                $billingDetail->contact_name = $contactName;
                $billingDetail->email = $billingData['email'] ?? $allData['billing_email'] ?? $email;
                $billingDetail->phone = $billingData['telephone'] ?? '';
                $billingDetail->address = $billingData['address'] ?? $allData['billing_address'] ?? '';
                $billingDetail->city_id = !empty($billingData['city']) ? trim($billingData['city']) : ($allData['billing_city'] ?? null);
                $billingDetail->state_id = $billingData['state_id'] ?? $allData['billing_state_id'] ?? null;
                $billingDetail->country_id = $billingData['country_id'] ?? $allData['billing_country_id'] ?? Country::where('code', 'IN')->first()->id;
                $billingDetail->postal_code = $billingData['postal_code'] ?? $allData['billing_postal_code'] ?? '';
                $billingDetail->gst_id = $allData['gst_no'] ?? null;
                $billingDetail->same_as_basic = '0'; // Different from exhibitor
            } else {
                // Fallback: Use contact details only if billing data not available
                $billingDetail->billing_company = $contactName;
                $billingDetail->contact_name = $contactName;
                $billingDetail->email = $email;
                $billingDetail->phone = $draft->contact_data['mobile'] ?? '';
                $billingDetail->address = '';
                $billingDetail->city_id = null;
                $billingDetail->state_id = null;
                $billingDetail->country_id = Country::where('code', 'IN')->first()->id;
                $billingDetail->postal_code = '';
                $billingDetail->gst_id = $allData['gst_no'] ?? null;
                $billingDetail->same_as_basic = '1';
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
            $invoice->gst = $pricing['gst_amount']; // Only 'gst' column exists, not 'gst_amount'
            $invoice->processing_chargesRate = $pricing['processing_rate'];
            $invoice->processing_charges = $pricing['processing_charges'];
            $invoice->total_final_price = $pricing['total_price'];
            $invoice->currency = $currency; // Use currency from draft
            $invoice->payment_status = 'unpaid';
            $invoice->pending_amount = $pricing['total_price']; // Set pending amount to total initially
            $invoice->payment_due_date = null;
            $invoice->save();
            
            // Exhibitor data is now stored in applications table, so no need to create exhibitors_info
            
            DB::commit();
            
            // Mark draft as converted (use database ID, not application_id string)
            $draft->converted_to_application_id = $application->id;
            $draft->converted_at = now();
            $draft->save();
            
            // Update session with application_id for security
            session(['exhibitor_registration_application_id' => $application->application_id]);
            
            // Send confirmation email with contact information
            try {
                Mail::to($email)->send(new ExhibitorRegistrationMail($application, $invoice, $contact));
                \Log::info('Exhibitor Registration: Confirmation email sent', ['email' => $email]);
            } catch (\Exception $mailError) {
                \Log::warning('Exhibitor Registration: Failed to send confirmation email', [
                    'email' => $email,
                    'error' => $mailError->getMessage()
                ]);
                // Don't fail if email fails
            }
            
            // Send admin notification email (separate mail class for Exhibitor Registration)
            try {
                // Reload application with relationships for email
                $application->load(['country', 'state', 'eventContact']);
                $contact = EventContact::where('application_id', $application->id)->first();
                
                // Get admin emails from config
                $adminEmails = config('constants.admin_emails.to', []);
                $bccEmails = config('constants.admin_emails.bcc', []);
                
                \Log::info('Attempting to send admin notification email for exhibitor registration', [
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
                    $mail->send(new ExhibitorRegistrationAdminMail($application, $contact));
                    
                    \Log::info('Admin notification email sent successfully', [
                        'application_id' => $application->application_id,
                        'to' => $adminEmails,
                        'bcc' => $bccEmails,
                    ]);
                } elseif (!empty($bccEmails)) {
                    // If adminEmails is empty but bccEmails is not, send to bccEmails using bcc() as main recipients
                    Mail::to([])->bcc($bccEmails)->send(new ExhibitorRegistrationAdminMail($application, $contact));
                    
                    \Log::info('Admin notification email sent successfully (BCC only)', [
                        'application_id' => $application->application_id,
                        'bcc' => $bccEmails,
                    ]);
                } else {
                    \Log::warning('No admin emails configured for exhibitor registration notification', [
                        'application_id' => $application->application_id,
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send admin notification email for exhibitor registration', [
                    'application_id' => $application->application_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                // Don't fail if email fails
            }
            
            \Log::info('Exhibitor Registration: Application creation successful', [
                'application_id' => $application->application_id,
                'user_id' => $user->id,
                'invoice_id' => $invoice->id ?? null
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Application created successfully!',
                'application_id' => $application->application_id,
                'redirect_url' => route('exhibitor-registration.payment', $application->application_id)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Exhibitor Registration Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'draft_id' => $draft->id ?? null,
                'session_id' => $sessionId,
                'email' => $email ?? null,
                'allData_keys' => array_keys($allData ?? []),
                'billingData_keys' => array_keys($billingData ?? []),
                'contactData_keys' => array_keys($contactData ?? []),
                'exhibitorData_keys' => array_keys($exhibitorData ?? []),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the application. Please try again.',
                'error_details' => config('app.debug') ? $e->getMessage() : null
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
        
        // Load exhibitor info if exists
        $exhibitorInfo = \App\Models\ExhibitorInfo::where('application_id', $application->id)->first();
        
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
        
        // Check if application is approved - payment only allowed after approval
        if ($application->submission_status !== 'approved') {
            $invoice = Invoice::where('application_id', $application->id)->first();
            $billingDetail = \App\Models\BillingDetail::where('application_id', $application->id)->first();
            $eventContact = EventContact::where('application_id', $application->id)->first();
            return view('exhibitor-registration.payment', compact('application', 'invoice', 'billingDetail', 'eventContact'))
                ->with('approval_pending', true);
        }
        
        // Load billing detail (billing_data is stored in billing_details table)
        $billingDetail = \App\Models\BillingDetail::where('application_id', $application->id)->first();
        
        return view('exhibitor-registration.payment', compact('application', 'billingDetail'));
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

    /**
     * Check if email already exists in users table
     */
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

