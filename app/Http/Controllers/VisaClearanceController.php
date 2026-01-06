<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Events;
use App\Models\Country;
use App\Models\VisaClearanceRequest;

class VisaClearanceController extends Controller
{
    /**
     * Show the Visa Clearance Registration form (public).
     */
    public function showForm(Request $request, $eventSlug = null)
    {
        $event = null;

        if ($eventSlug) {
            $event = Events::where('slug', $eventSlug)
                ->orWhere('id', $eventSlug)
                ->first();
        }

        // Get all countries for nationality and country dropdowns
        $countries = Country::orderBy('name')->get(['id', 'name', 'code']);

        return view('visa.clearance-form', compact('event', 'countries'));
    }

    /**
     * Handle Visa Clearance form submission.
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'event_id'                => 'nullable|exists:events,id',
            'event_year'              => 'nullable|string|max:10',
            'organisation_name'       => 'required|string|max:255',
            'designation'             => 'required|string|max:255',
            'passport_name'           => 'required|string|max:255',
            'father_husband_name'     => 'required|string|max:255',
            'dob'                     => 'required|date',
            'place_of_birth'          => 'required|string|max:255',
            'nationality'             => 'required|string|max:100',
            'passport_number'         => 'required|string|max:100',
            'passport_issue_date'     => 'required|date',
            'passport_issue_place'    => 'required|string|max:255',
            'passport_expiry_date'    => 'required|date|after:today',
            'entry_date_india'        => 'required|date',
            'exit_date_india'         => 'required|date|after_or_equal:entry_date_india',
            'phone_country_code'      => 'nullable|string|max:10',
            'phone_number'            => 'required|string|max:20',
            'email'                   => 'required|email|max:255',
            'address_line1'           => 'required|string|max:255',
            'address_line2'           => 'nullable|string|max:255',
            'city'                    => 'required|string|max:100',
            'state'                   => 'required|string|max:100',
            'country'                 => 'required|string|max:100',
            'postal_code'             => 'required|string|max:20',
        ]);

        try {
            // Create visa clearance request
            $visaRequest = VisaClearanceRequest::create([
                'event_id' => $validated['event_id'] ?? null,
                'event_year' => $validated['event_year'] ?? date('Y'),
                'organisation_name' => $validated['organisation_name'],
                'designation' => $validated['designation'],
                'passport_name' => $validated['passport_name'],
                'father_husband_name' => $validated['father_husband_name'],
                'dob' => $validated['dob'],
                'place_of_birth' => $validated['place_of_birth'],
                'nationality' => $validated['nationality'],
                'passport_number' => $validated['passport_number'],
                'passport_issue_date' => $validated['passport_issue_date'],
                'passport_issue_place' => $validated['passport_issue_place'],
                'passport_expiry_date' => $validated['passport_expiry_date'],
                'entry_date_india' => $validated['entry_date_india'],
                'exit_date_india' => $validated['exit_date_india'],
                'phone_country_code' => $validated['phone_country_code'] ?? null,
                'phone_number' => $validated['phone_number'],
                'email' => $validated['email'],
                'address_line1' => $validated['address_line1'],
                'address_line2' => $validated['address_line2'] ?? null,
                'city' => $validated['city'],
                'state' => $validated['state'],
                'country' => $validated['country'],
                'postal_code' => $validated['postal_code'],
                'source_url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'pending',
            ]);

            Log::info('Visa Clearance form submitted successfully', [
                'visa_request_id' => $visaRequest->id,
                'event_id' => $visaRequest->event_id,
                'email' => $visaRequest->email,
            ]);

            // TODO: Send email to admin and user for confirmation
            // Mail::to($visaRequest->email)->send(new VisaClearanceUserConfirmationMail($visaRequest));
            // Mail::to(config('constants.admin_emails.to'))->send(new VisaClearanceAdminNotificationMail($visaRequest));

            return redirect()->route('visa.clearance.thankyou')
                ->with('success', 'Your visa clearance registration has been submitted successfully. We will process your request and get back to you soon.');

        } catch (\Exception $e) {
            Log::error('Visa Clearance submission error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token']),
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while submitting your visa clearance request. Please try again.']);
        }
    }

    /**
     * Show thank you page
     */
    public function thankyou()
    {
        return view('visa.clearance-thankyou');
    }
}


