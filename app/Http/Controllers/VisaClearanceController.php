<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VisaClearanceController extends Controller
{
    /**
     * Show the Visa Clearance Registration form (public).
     */
    public function showForm(Request $request)
    {
        return view('visa.clearance-form');
    }

    /**
     * Handle Visa Clearance form submission.
     *
     * For now we just validate and show a simple thank you screen; 
     * later we can wire this to a dedicated model/table if needed.
     */
    public function submit(Request $request)
    {
        $validated = $request->validate([
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

        // TODO: Persist and/or email the data if required.
        Log::info('Visa Clearance form submitted', [
            'email' => $validated['email'] ?? null,
        ]);

        return view('visa.clearance-thankyou');
    }
}


