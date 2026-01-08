<?php

namespace App\Http\Controllers;

use App\Models\ElevateRegistration;
use App\Models\ElevateAttendee;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

        return view('elevate-registration.form', compact('countries', 'states', 'salutations', 'indiaCountry'));
    }

    /**
     * Submit the registration form
     */
    public function submit(Request $request)
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
            DB::beginTransaction();

            // Create registration
            $registration = ElevateRegistration::create([
                'company_name' => $validated['company_name'],
                'address' => $validated['address'],
                'country' => $validated['country'],
                'state' => $validated['state'],
                'city' => $validated['city'],
                'postal_code' => $validated['postal_code'],
                'attendance' => $validated['attendance'],
                'attendance_reason' => $validated['attendance_reason'] ?? null,
            ]);

            // Create attendees if attending
            if ($validated['attendance'] === 'yes' && !empty($validated['attendees'])) {
                foreach ($validated['attendees'] as $attendeeData) {
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

            DB::commit();

            return redirect()->route('elevate-registration.thankyou')
                ->with('success', 'Thank you for your registration! Your information has been submitted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Elevate registration submission error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token']),
            ]);

            return redirect()->back()
                ->withInput()
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
