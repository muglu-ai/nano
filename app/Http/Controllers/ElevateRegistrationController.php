<?php

namespace App\Http\Controllers;

use App\Models\ElevateRegistration;
use App\Models\ElevateAttendee;
use App\Models\ElevateRegistrationSession;
use App\Models\Country;
use App\Mail\ElevateRegistrationConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class ElevateRegistrationController extends Controller
{
    /**
     * Show the registration form
     */
    public function showForm()
    {
        // Get all countries from database
        $countries = Country::orderBy('name')->get(['id', 'name', 'code']);
        
        // Get India country for default state loading
        $indiaCountry = Country::where('code', 'IN')->first();
        $states = [];
        if ($indiaCountry) {
            $states = \App\Models\State::where('country_id', $indiaCountry->id)
                ->orderBy('name')
                ->get(['id', 'name']);
        }

        // Salutation options
        $salutations = ['Mr', 'Ms', 'Mrs', 'Dr', 'Prof', 'Other'];

        // Try to load existing session data
        $sessionId = session()->getId();
        $session = ElevateRegistrationSession::bySession($sessionId)
            ->active()
            ->first();
        
        $formData = $session ? $session->form_data : null;

        return view('elevate-registration.form', compact('countries', 'states', 'salutations', 'indiaCountry', 'formData'));
    }

    /**
     * Save form data to session table and show preview
     */
    public function saveAndPreview(Request $request)
    {
        // Verify reCAPTCHA
        $recaptchaResponse = $request->input('g-recaptcha-response');
        if (!$this->verifyRecaptcha($recaptchaResponse)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['recaptcha' => 'reCAPTCHA verification failed. Please try again.']);
        }

        // Validate the request
        $validated = $request->validate([
            // Company Information
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'country' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            
            // Elevate Application Fields
            'elevate_application_call_names' => 'required|array|min:1|max:2',
            'elevate_application_call_names.*' => 'in:ELEVATE 2025,ELEVATE Unnati 2025,ELEVATE MINORITIES 2025',
            'elevate_2025_id' => 'required|string|max:50',
            
            // Attendance
            'attendance' => 'required|in:yes,no',
            'attendance_reason' => 'required_if:attendance,no|nullable|string|max:1000',
            
            // Attendees/Contacts (required for both yes and no)
            'attendees' => 'required|array|min:1',
            'attendees.*.salutation' => 'required_with:attendees|string|max:10',
            'attendees.*.first_name' => 'required_with:attendees|string|max:255',
            'attendees.*.last_name' => 'required_with:attendees|string|max:255',
            'attendees.*.job_title' => 'required_with:attendees|string|max:255',
            'attendees.*.email' => 'required_with:attendees|email|max:255',
            'attendees.*.phone_number' => 'required_with:attendees|string|max:20',
            'attendees.*.phone_country_code' => 'required_with:attendees|string|max:5',
        ], [
            'company_name.required' => 'Company name is required.',
            'address.required' => 'Address is required.',
            'city.required' => 'City is required.',
            'postal_code.required' => 'Postal code is required.',
            'elevate_application_call_names.required' => 'Please select at least one Elevate Application Call Name.',
            'elevate_application_call_names.min' => 'Please select at least one Elevate Application Call Name.',
            'elevate_application_call_names.max' => 'Maximum 2 Elevate Application Call Names can be selected.',
            'elevate_application_call_names.*.in' => 'Invalid Elevate Application Call Name selected.',
            'elevate_2025_id.required' => 'ELEVATE 2025 ID is required.',
            'attendance.required' => 'Please indicate if you will be attending.',
            'attendance_reason.required_if' => 'Please provide a reason if you are not attending.',
            'attendees.required' => 'At least one attendee/contact is required.',
            'attendees.min' => 'At least one attendee/contact is required.',
            'attendees.*.salutation.required_with' => 'Salutation is required for all attendees.',
            'attendees.*.first_name.required_with' => 'First name is required for all attendees.',
            'attendees.*.last_name.required_with' => 'Last name is required for all attendees.',
            'attendees.*.job_title.required_with' => 'Designation is required for all attendees/contacts.',
            'attendees.*.email.required_with' => 'Email is required for all attendees.',
            'attendees.*.email.email' => 'Please enter a valid email address.',
            'attendees.*.phone_number.required_with' => 'Phone number is required for all attendees.',
        ]);

        // Validate duplicate emails within the same submission and check existing emails
        if (!empty($validated['attendees'])) {
            $emails = [];
            $emailIndexMap = []; // Map email to attendee index for error reporting
            
            // Collect all emails with their indices
            foreach ($validated['attendees'] as $index => $attendee) {
                $email = strtolower(trim($attendee['email'] ?? ''));
                if (!empty($email)) {
                    $emails[] = $email;
                    if (!isset($emailIndexMap[$email])) {
                        $emailIndexMap[$email] = [];
                    }
                    $emailIndexMap[$email][] = $index;
                }
            }
            
            // Check for duplicate emails within the same submission
            $uniqueEmails = array_unique($emails);
            if (count($emails) !== count($uniqueEmails)) {
                $duplicateEmails = array_diff_assoc($emails, $uniqueEmails);
                $errors = [];
                foreach ($duplicateEmails as $dupEmail) {
                    foreach ($emailIndexMap[$dupEmail] as $index) {
                        $errors["attendees.{$index}.email"] = 'This email address is already used for another attendee/contact. Please use a different email address.';
                    }
                }
                return redirect()->back()
                    ->withInput()
                    ->withErrors($errors);
            }
            
            // Check if any email already exists in database
            if (!empty($emails)) {
                $existingEmails = ElevateAttendee::whereIn('email', $emails)
                    ->pluck('email')
                    ->map(function($email) {
                        return strtolower($email);
                    })
                    ->toArray();
                
                if (!empty($existingEmails)) {
                    $errors = [];
                    foreach ($validated['attendees'] as $index => $attendee) {
                        $email = strtolower(trim($attendee['email'] ?? ''));
                        if (in_array($email, $existingEmails)) {
                            $errors["attendees.{$index}.email"] = 'This email address is already registered. Please use a different email address.';
                        }
                    }
                    return redirect()->back()
                        ->withInput()
                        ->withErrors($errors);
                }
            }
        }

        try {
            $sessionId = session()->getId();
            
            // Prepare form data for storage
            $formData = [
                'company_name' => $validated['company_name'],
                'address' => $validated['address'],
                'country' => $validated['country'],
                'state' => $validated['state'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'elevate_application_call_names' => $validated['elevate_application_call_names'],
                'elevate_2025_id' => $validated['elevate_2025_id'],
                'attendance' => $validated['attendance'],
                'attendance_reason' => $validated['attendance_reason'] ?? null,
                'attendees' => $validated['attendees'] ?? [],
            ];

            // Calculate progress percentage (assuming all fields filled = 100%)
            $progressPercentage = 100;

            // Save or update session
            $session = ElevateRegistrationSession::updateOrCreate(
                ['session_id' => $sessionId],
                [
                    'form_data' => $formData,
                    'progress_percentage' => $progressPercentage,
                    'expires_at' => now()->addDays(7), // Expire after 7 days
                ]
            );

            // Redirect to preview page
            return redirect()->route('elevate-registration.preview')
                ->with('session_id', $sessionId);

        } catch (\Exception $e) {
            Log::error('Elevate registration session save error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token']),
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while saving your registration. Please try again.']);
        }
    }

    /**
     * Show preview page
     */
    public function preview(Request $request)
    {
        $sessionId = $request->get('session_id') ?? session()->getId();
        
        $session = ElevateRegistrationSession::bySession($sessionId)
            ->active()
            ->first();

        if (!$session) {
            return redirect()->route('elevate-registration.form')
                ->with('error', 'Session expired. Please fill the form again.');
        }

        $formData = $session->form_data;

        return view('elevate-registration.preview', compact('session', 'formData'));
    }

    /**
     * Final submission - Copy from session to final tables
     */
    public function submit(Request $request)
    {
        // Verify reCAPTCHA
        $recaptchaResponse = $request->input('g-recaptcha-response');
        if (!$this->verifyRecaptcha($recaptchaResponse)) {
            return redirect()->back()
                ->withErrors(['recaptcha' => 'reCAPTCHA verification failed. Please try again.']);
        }

        $sessionId = $request->get('session_id') ?? session()->getId();
        
        $session = ElevateRegistrationSession::bySession($sessionId)
            ->active()
            ->first();

        if (!$session) {
            return redirect()->route('elevate-registration.form')
                ->with('error', 'Session expired. Please fill the form again.');
        }

        $formData = $session->form_data;

        try {
            DB::beginTransaction();

            // Create registration from session data
            $registration = ElevateRegistration::create([
                'company_name' => $formData['company_name'],
                'address' => $formData['address'],
                'country' => $formData['country'],
                'state' => $formData['state'],
                'city' => $formData['city'],
                'postal_code' => $formData['postal_code'],
                'elevate_application_call_names' => $formData['elevate_application_call_names'],
                'elevate_2025_id' => $formData['elevate_2025_id'],
                'attendance' => $formData['attendance'],
                'attendance_reason' => $formData['attendance_reason'] ?? null,
            ]);

            // Create attendees/contacts (required for both yes and no)
            if (!empty($formData['attendees'])) {
                foreach ($formData['attendees'] as $attendeeData) {
                    ElevateAttendee::create([
                        'registration_id' => $registration->id,
                        'salutation' => $attendeeData['salutation'],
                        'first_name' => $attendeeData['first_name'],
                        'last_name' => $attendeeData['last_name'],
                        'job_title' => $attendeeData['job_title'] ?? null,
                        'email' => $attendeeData['email'],
                        'phone_number' => $attendeeData['phone_number'],
                    ]);
                }
            }

            // Mark session as converted
            $session->update([
                'converted_at' => now(),
                'converted_to_registration_id' => $registration->id,
            ]);

            DB::commit();

            // Reload registration with attendees relationship
            $registration->load('attendees');

            // Send confirmation email to all attendees/contacts
            if ($registration->attendees->count() > 0) {
                try {
                    foreach ($registration->attendees as $attendee) {
                        $mail = Mail::to($attendee->email);
                        // Add BCC to test.interlinks@gmail.com
                        $mail->bcc('test.interlinks@gmail.com');
                        $mail->send(new ElevateRegistrationConfirmationMail($registration));
                    }
                } catch (\Exception $e) {
                    // Log but don't fail the submission
                    Log::error('Elevate registration email sending failed', [
                        'registration_id' => $registration->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Store registration ID in session for thank you page
            session(['last_registration_id' => $registration->id]);
            
            return redirect()->route('elevate-registration.thankyou')
                ->with('success', 'Thank you for your registration! Your information has been submitted successfully. A confirmation email has been sent to your registered email address.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Elevate registration final submission error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_id' => $sessionId,
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while submitting your registration. Please try again.']);
        }
    }

    /**
     * Show thank you page
     */
    public function thankyou()
    {
        $registration = null;
        $formData = null;
        
        // Try to get registration from session
        $registrationId = session('last_registration_id');
        if ($registrationId) {
            $registration = ElevateRegistration::with('attendees')->find($registrationId);
        }
        
        // If no registration found, try to get from converted session
        if (!$registration) {
            $sessionId = session()->getId();
            $session = ElevateRegistrationSession::bySession($sessionId)
                ->whereNotNull('converted_at')
                ->orderBy('updated_at', 'desc')
                ->first();
            
            if ($session) {
                // Try to get the registration that was just created
                $registration = ElevateRegistration::with('attendees')
                    ->where('company_name', $session->form_data['company_name'] ?? '')
                    ->where('elevate_2025_id', $session->form_data['elevate_2025_id'] ?? '')
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                // If no registration found, use session data
                if (!$registration) {
                    $formData = $session->form_data;
                }
            }
        }
        
        return view('elevate-registration.thankyou', compact('registration', 'formData'));
    }

    /**
     * Get states by country (AJAX)
     */
    public function getStates(Request $request)
    {
        $countryId = $request->input('country_id');
        
        if (!$countryId) {
            return response()->json(['states' => []]);
        }

        $states = \App\Models\State::where('country_id', $countryId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json(['states' => $states]);
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
}
