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
        // Validate the request
        $validated = $request->validate([
            // Company Information
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'country' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            
            // Elevate Application Fields
            'elevate_application_call_names' => 'required|array|min:1|max:2',
            'elevate_application_call_names.*' => 'in:ELEVATE 2025,ELEVATE Unnati 2025,ELEVATE MINORITIES 2025',
            'elevate_2025_id' => 'required|string|max:50',
            
            // Attendance
            'attendance' => 'required|in:yes,no',
            'attendance_reason' => 'required_if:attendance,no|nullable|string|max:1000',
            
            // Attendees (if attending)
            'attendees' => 'required_if:attendance,yes|array|min:1',
            'attendees.*.salutation' => 'required_with:attendees|string|max:10',
            'attendees.*.first_name' => 'required_with:attendees|string|max:255',
            'attendees.*.last_name' => 'required_with:attendees|string|max:255',
            'attendees.*.job_title' => 'nullable|string|max:255',
            'attendees.*.email' => 'required_with:attendees|email|max:255',
            'attendees.*.phone_number' => 'required_with:attendees|string|max:20',
        ], [
            'company_name.required' => 'Company name is required.',
            'address.required' => 'Address is required.',
            'country.required' => 'Country is required.',
            'state.required' => 'State is required.',
            'city.required' => 'City is required.',
            'postal_code.required' => 'Postal code is required.',
            'elevate_application_call_names.required' => 'Please select at least one Elevate Application Call Name.',
            'elevate_application_call_names.min' => 'Please select at least one Elevate Application Call Name.',
            'elevate_application_call_names.max' => 'Maximum 2 Elevate Application Call Names can be selected.',
            'elevate_application_call_names.*.in' => 'Invalid Elevate Application Call Name selected.',
            'elevate_2025_id.required' => 'ELEVATE 2025 ID is required.',
            'attendance.required' => 'Please indicate if you will be attending.',
            'attendance_reason.required_if' => 'Please provide a reason if you are not attending.',
            'attendees.required_if' => 'At least one attendee is required if you are attending.',
            'attendees.min' => 'At least one attendee is required.',
            'attendees.*.salutation.required_with' => 'Salutation is required for all attendees.',
            'attendees.*.first_name.required_with' => 'First name is required for all attendees.',
            'attendees.*.last_name.required_with' => 'Last name is required for all attendees.',
            'attendees.*.email.required_with' => 'Email is required for all attendees.',
            'attendees.*.email.email' => 'Please enter a valid email address.',
            'attendees.*.phone_number.required_with' => 'Phone number is required for all attendees.',
        ]);

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

            // Create attendees if attending
            if ($formData['attendance'] === 'yes' && !empty($formData['attendees'])) {
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

            // Send confirmation email to all attendees if attending
            if ($registration->attendance === 'yes' && $registration->attendees->count() > 0) {
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
        return view('elevate-registration.thankyou');
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
}
