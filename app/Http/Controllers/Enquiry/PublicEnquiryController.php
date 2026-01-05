<?php

namespace App\Http\Controllers\Enquiry;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\EnquiryInterest;
use App\Models\Events;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class PublicEnquiryController extends Controller
{
    /**
     * Show the enquiry form
     */
    public function showForm(Request $request, $eventSlug = null)
    {
        // Get event if slug provided
        $event = null;
        if ($eventSlug) {
            $event = Events::where('slug', $eventSlug)->orWhere('id', $eventSlug)->first();
        }

        // Get pre-selected interest from query parameter
        $preSelectedInterest = $request->query('interest');
        $preSelectedInterests = [];
        
        if ($preSelectedInterest) {
            // Allow multiple interests separated by comma
            $interests = explode(',', $preSelectedInterest);
            $validInterests = EnquiryInterest::getInterestTypes();
            foreach ($interests as $interest) {
                $interest = trim(strtolower($interest));
                if (array_key_exists($interest, $validInterests)) {
                    $preSelectedInterests[] = $interest;
                }
            }
        }

        // Get sectors list (same as exhibitor registration)
        $sectors = config('constants.sectors', []);
        
        // Get all countries from database
        $countries = Country::orderBy('name')->get(['id', 'name', 'code']);
        
        // Get India country for default state loading
        $indiaCountry = Country::where('code', 'IN')->first();
        $defaultStateId = null;
        if ($indiaCountry) {
            // Get a default state for India (first state alphabetically)
            $defaultState = \App\Models\State::where('country_id', $indiaCountry->id)->orderBy('name')->first();
            if ($defaultState) {
                $defaultStateId = $defaultState->name;
            }
        }

        return view('enquiry.form', compact('event', 'preSelectedInterests', 'sectors', 'countries', 'indiaCountry', 'defaultStateId'));
    }

    /**
     * Submit the enquiry form
     */
    public function submit(Request $request)
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
            'sector' => 'required|string|max:200',
            'title' => 'nullable|string|max:10',
            'name' => 'required|string|max:255',
            'organisation' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_country_code' => 'nullable|string|max:5',
            'phone_number' => 'required|string|max:15|regex:/^[0-9]+$/',
            'state' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'comments' => 'required|string|max:1000',
            'referral_source' => 'required|string|max:100',
            'interests' => 'required|array|min:1',
            'interests.*' => 'in:delegate,startup_pod,speaking,exhibition,sponsoring,b2b,visitor,poster,other',
            'interest_other' => 'required_if:interests.*,other|nullable|string|max:255',
            'event_id' => 'nullable|exists:events,id',
            'event_year' => 'nullable|string|max:10',
        ], [
            'sector.required' => 'Please select a sector.',
            'name.required' => 'Name is required.',
            'organisation.required' => 'Organisation is required.',
            'designation.required' => 'Designation is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'phone_number.required' => 'Contact number is required.',
            'phone_number.regex' => 'Contact number must contain only numbers.',
            'phone_number.max' => 'Contact number cannot exceed 15 digits.',
            'state.required' => 'State is required.',
            'city.required' => 'City is required.',
            'country.required' => 'Country is required.',
            'comments.required' => 'Comment is required.',
            'comments.max' => 'Comment cannot exceed 1000 characters.',
            'referral_source.required' => 'Please select how you heard about this event.',
            'interests.required' => 'Please select at least one interest.',
            'interests.min' => 'Please select at least one interest.',
            'interests.*.in' => 'Invalid interest type selected.',
            'interest_other.required_if' => 'Please specify the other interest.',
        ]);

        try {
            // Get event if provided
            $event = null;
            if (!empty($validated['event_id'])) {
                $event = Events::find($validated['event_id']);
            }

            // Prepare full name
            $fullName = $validated['name'];
            if (!empty($validated['title'])) {
                $fullName = $validated['title'] . ' ' . $fullName;
            }

            // Prepare phone full
            $phoneFull = $validated['phone_number'];
            if (!empty($validated['phone_country_code'])) {
                $phoneFull = '+' . $validated['phone_country_code'] . ' ' . $phoneFull;
            }

            // Create enquiry
            $enquiry = Enquiry::create([
                'event_id' => $event ? $event->id : null,
                'event_year' => $validated['event_year'] ?? ($event ? $event->event_year : date('Y')),
                'title' => $validated['title'] ?? null,
                'first_name' => null, // Can be parsed from name if needed
                'last_name' => null,
                'full_name' => $fullName,
                'organisation' => $validated['organisation'],
                'designation' => $validated['designation'],
                'sector' => $validated['sector'] ?? null,
                'email' => $validated['email'],
                'phone_country_code' => $validated['phone_country_code'] ?? null,
                'phone_number' => $validated['phone_number'],
                'phone_full' => $phoneFull,
                'city' => $validated['city'],
                'state' => $validated['state'] ?? null,
                'country' => $validated['country'],
                'postal_code' => null,
                'address' => null,
                'comments' => $validated['comments'],
                'referral_source' => $validated['referral_source'] ?? null,
                'source_url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'new',
            ]);

            // Create interest records
            $interests = $request->input('interests', []);
            foreach ($interests as $interestType) {
                EnquiryInterest::create([
                    'enquiry_id' => $enquiry->id,
                    'interest_type' => $interestType,
                    'interest_other_detail' => ($interestType === 'other') ? ($request->input('interest_other') ?? null) : null,
                ]);
            }

            // Send emails (non-blocking - don't wait for it to complete)
            try {
                $this->sendEmails($enquiry);
            } catch (\Exception $e) {
                // Log but don't fail the submission
                Log::error('Email sending failed but enquiry was saved', [
                    'enquiry_id' => $enquiry->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Always redirect to thank you page after successful submission
            return redirect()->route('enquiry.thankyou')
                ->with('success', 'Thank you for your enquiry! We will get back to you soon.');

        } catch (\Exception $e) {
            Log::error('Enquiry submission error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while submitting your enquiry. Please try again.']);
        }
    }

    /**
     * Show thank you page
     */
    public function thankyou()
    {
        return view('enquiry.thankyou');
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

    /**
     * Send emails to user and admin
     */
    private function sendEmails(Enquiry $enquiry)
    {
        try {
            // Load relationships
            $enquiry->load(['interests', 'event']);

            // Send email to user
            try {
                Mail::to($enquiry->email)->send(new \App\Mail\EnquiryUserConfirmationMail($enquiry));
            } catch (\Exception $e) {
                Log::error('Failed to send user confirmation email', [
                    'enquiry_id' => $enquiry->id,
                    'email' => $enquiry->email,
                    'error' => $e->getMessage(),
                ]);
            }

            // Send email to admin
            $adminEmails = config('constants.admin_emails.to', []);
            $bccEmails = config('constants.admin_emails.bcc', []);

            if (!empty($adminEmails)) {
                $mail = Mail::to($adminEmails);
                if (!empty($bccEmails)) {
                    $mail->bcc($bccEmails);
                }
                $mail->send(new \App\Mail\EnquiryAdminNotificationMail($enquiry));
            } elseif (!empty($bccEmails)) {
                Mail::to([])->bcc($bccEmails)->send(new \App\Mail\EnquiryAdminNotificationMail($enquiry));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send enquiry emails', [
                'enquiry_id' => $enquiry->id,
                'error' => $e->getMessage(),
            ]);
            // Don't fail the transaction if email fails
        }
    }
}
