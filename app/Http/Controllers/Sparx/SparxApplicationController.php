<?php

namespace App\Http\Controllers\Sparx;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sparx;
use App\Models\Events;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class SparxApplicationController extends Controller
{
    /**
     * Show the application form (new or pre-filled by event)
     */
    public function create(Request $request, $uuid = null)
    {
        $event = null;
        $application = null;

        // If UUID provided → load existing draft/submitted application
        if ($uuid) {
            $application = Sparx::where('uuid', $uuid)->first();
            if (!$application) {
                abort(404, 'Application not found');
            }
            // Optional: check ownership if you add auth later
            // if (auth()->check() && $application->user_id !== auth()->id()) {
            //     abort(403);
            // }
        }

        // Load event if slug or ID passed (you can adapt parameter name)
        if (!$application && $request->has('event')) {
            $event = Events::where('slug', $request->event)
                ->orWhere('id', $request->event)
                ->first();
        }

        $sectors = ['Medicine', 'Electronics', 'Agriculture', 'Healthcare', 'Manufacturing', 'Environment/Energy', 'Others'];

        // You can load countries, states, etc. like in enquiry
        // $countries = Country::orderBy('name')->get();

        return view('sparx.form', compact(
            'event',
            'application',           // for editing/pre-filling
            'sectors'
            // 'countries', etc.
        ));
    }

    /**
     * Store / submit new application
     */
    public function store(Request $request)
    {
        // 1. reCAPTCHA verification (copy from enquiry)
        $recaptchaResponse = $request->input('g-recaptcha-response');
        if (!$this->verifyRecaptcha($recaptchaResponse)) {
            return back()->withInput()->withErrors(['recaptcha' => 'reCAPTCHA failed.']);
        }

        // 2. Inline validation (adapted from your form fields)
        $validated = $request->validate([
            'name'                  => 'required|string|max:120',
            'designation'           => 'required|string|max:100',
            'organization'          => 'required|string|max:150',
            'email'                 => 'required|email|max:150',
            'phone_country_code'    => 'nullable|string|max:5',
            'phone_number'          => 'required|string|max:20',
            'address'               => 'nullable|string',
            'city'                  => 'nullable|string|max:100',
            'state'                 => 'nullable|string|max:100',
            'country'               => 'required|string|max:100',
            'postal_code'           => 'nullable|string|max:10',
            'startup_idea_name'     => 'required|string|max:120',
            'website'               => 'nullable|url|max:255',
            'sector'                => 'nullable|string|max:80',
            'idea_description'      => 'required|string',
            'products'              => 'required|string',
            'key_successes'         => 'required|string',
            'potential_market_size' => 'required|string|max:120',
            'company_size_employees'=> 'required|integer|min:0',
            'is_registered'         => 'required|boolean',
            'registration_date'     => 'nullable|date|before_or_equal:today',
            'consent_given'         => 'required|boolean',
            'event_id'              => 'nullable|exists:events,id',
            'event_year'            => 'nullable|string|max:10',
        ], [
            // custom messages like in enquiry
            'name.required' => 'Name is required.',
            'email.required' => 'A valid email is required.',
            'startup_idea_name.required' => 'Startup / Idea name is required.',
            // ... add more as needed
        ]);

        try {
            $application = Sparx::create([
                'event_id'      => $validated['event_id'] ?? null,
                'event_year'    => $validated['event_year'] ?? now()->year,
                'status'        => 'submitted',
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->userAgent(),
                // map all other fields from $validated
                'name'                  => $validated['name'],
                'designation'           => $validated['designation'],
                'organization'          => $validated['organization'],
                'email'                 => $validated['email'],
                'phone_country_code'    => $validated['phone_country_code'],
                'phone_number'          => $validated['phone_number'],
                'phone_full'            => $validated['phone_country_code'] 
                                            ? '+' . $validated['phone_country_code'] . ' ' . $validated['phone_number'] 
                                            : $validated['phone_number'],
                'address'               => $validated['address'],
                'city'                  => $validated['city'],
                'state'                 => $validated['state'],
                'country'               => $validated['country'],
                'postal_code'           => $validated['postal_code'],
                'startup_idea_name'     => $validated['startup_idea_name'],
                'website'               => $validated['website'],
                'sector'                => $validated['sector'],
                'idea_description'      => $validated['idea_description'],
                'products'              => $validated['products'],
                'key_successes'         => $validated['key_successes'],
                'potential_market_size' => $validated['potential_market_size'],
                'company_size_employees'=> $validated['company_size_employees'],
                'is_registered'         => $validated['is_registered'],
                'registration_date'     => $validated['registration_date'] ?? null,
                'consent_given'         => $validated['consent_given'] ?? true,
            ]);

            // Send emails (copy pattern from enquiry)
            $this->sendApplicationEmails($application);

            return redirect()->route('sparx.thank-you')
                ->with('success', 'Application submitted successfully.')
                ->with('reference_number', $application->formatted_registration_id);

        } catch (\Exception $e) {
            Log::error('Sparx application submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->withErrors(['error' => 'Submission failed. Please try again.']);
        }
    }

    /**
     * Thank you page
     */
    public function thankYou()
    {
        $event = (object) [
            'event_name' => config('constants.EVENT_NAME', 'NanoSparX Program'),
            'event_year' => config('constants.EVENT_YEAR', date('Y')),
        ];
        return view('sparx.thankyou', compact('event'));
    }

    // Copy & adapt your verifyRecaptcha method here
    private function verifyRecaptcha($response)
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
            Log::error('reCAPTCHA Enterprise verification error', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    // New method for emails
    private function sendApplicationEmails(Sparx $application)
    {
        // Similar pattern: user confirmation + admin notification
        // Use new Mailable classes: SparxUserConfirmationMail, SparxAdminNotificationMail
        try {
            Mail::to($application->email)->send(new \App\Mail\SparxUserConfirmationMail($application));
        } catch (\Exception $e) {
            Log::error('User email failed', ['id' => $application->id, 'error' => $e->getMessage()]);
        }

        // Admin notification (same as enquiry)
        $adminEmails = config('constants.admin_emails.to', []);
        if (!empty($adminEmails)) {
            Mail::to($adminEmails)->send(new \App\Mail\SparxAdminNotificationMail($application));
        }
    }
}