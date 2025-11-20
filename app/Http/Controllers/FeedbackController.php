<?php

namespace App\Http\Controllers;

use App\Models\ExhibitorFeedback;
use App\Models\Application;
use App\Services\CaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    protected $captchaService;

    public function __construct(CaptchaService $captchaService)
    {
        $this->captchaService = $captchaService;
    }

    /**
     * Show the feedback form (public access)
     */
    public function show()
    {
        $user = Auth::user();
        $companyName = null;

        // If user is logged in, check if they've already submitted feedback
        if ($user) {
            $existingFeedback = ExhibitorFeedback::where('user_id', $user->id)->first();
            
            if ($existingFeedback) {
                return redirect()->route('feedback.thankyou')
                    ->with('info', 'You have already submitted your feedback. Thank you!');
            }

            // Get user's company name from application if available
            $application = Application::where('user_id', $user->id)->first();
            $companyName = $application ? $application->company_name : null;
        }

        // Generate CAPTCHA (use session one if available from validation error, otherwise generate new)
        $captchaSvg = session('captchaSvg') ?? $this->captchaService->generate();
        
        // Clear the session CAPTCHA after using it
        session()->forget('captchaSvg');

        return view('feedback.form', [
            'user' => $user,
            'companyName' => $companyName,
            'captchaSvg' => $captchaSvg,
        ]);
    }

    /**
     * Store the feedback (public access)
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // If user is logged in, check if they've already submitted feedback
        if ($user) {
            $existingFeedback = ExhibitorFeedback::where('user_id', $user->id)->first();
            
            if ($existingFeedback) {
                return redirect()->route('feedback.thankyou')
                    ->with('info', 'You have already submitted your feedback. Thank you!');
            }
        }

        // Validate the request
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'event_rating' => 'required|integer|min:1|max:5',
            'portal_rating' => 'required|integer|min:1|max:5',
            'overall_experience_rating' => 'nullable|integer|min:1|max:5',
            'event_organization_rating' => 'nullable|integer|min:1|max:5',
            'venue_rating' => 'nullable|integer|min:1|max:5',
            'networking_opportunities_rating' => 'nullable|integer|min:1|max:5',
            'what_liked_most' => 'nullable|string|max:2000',
            'what_could_be_improved' => 'nullable|string|max:2000',
            'additional_comments' => 'nullable|string|max:2000',
            'would_recommend' => 'nullable|in:yes,no,maybe',
            'captcha' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$this->captchaService->validate($value)) {
                        $fail('The CAPTCHA is incorrect. Please try again.');
                    }
                },
            ],
        ]);

        // If validation fails, regenerate CAPTCHA and redirect back
        if ($validator->fails()) {
            $captchaSvg = $this->captchaService->generate();
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('captchaSvg', $captchaSvg);
        }

        $validated = $validator->validated();

        try {
            // Create feedback
            $feedback = ExhibitorFeedback::create([
                'user_id' => $user ? $user->id : null, // Optional - can be null for public submissions
                'name' => $validated['name'],
                'email' => $validated['email'],
                'company_name' => $validated['company_name'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'event_rating' => $validated['event_rating'],
                'portal_rating' => $validated['portal_rating'],
                'overall_experience_rating' => $validated['overall_experience_rating'] ?? null,
                'event_organization_rating' => $validated['event_organization_rating'] ?? null,
                'venue_rating' => $validated['venue_rating'] ?? null,
                'networking_opportunities_rating' => $validated['networking_opportunities_rating'] ?? null,
                'what_liked_most' => $validated['what_liked_most'] ?? null,
                'what_could_be_improved' => $validated['what_could_be_improved'] ?? null,
                'additional_comments' => $validated['additional_comments'] ?? null,
                'would_recommend' => $validated['would_recommend'] ?? null,
            ]);

            Log::info('Feedback submitted', [
                'user_id' => $user ? $user->id : null,
                'feedback_id' => $feedback->id,
                'email' => $feedback->email,
                'event_rating' => $feedback->event_rating,
                'portal_rating' => $feedback->portal_rating,
            ]);

            return redirect()->route('feedback.thankyou')
                ->with('success', 'Thank you for your valuable feedback!');

        } catch (\Exception $e) {
            Log::error('Error submitting feedback', [
                'user_id' => $user ? $user->id : null,
                'email' => $request->input('email'),
                'error' => $e->getMessage(),
            ]);

            // Regenerate CAPTCHA for retry
            $captchaSvg = $this->captchaService->generate();

            return back()
                ->withInput()
                ->with('error', 'There was an error submitting your feedback. Please try again.')
                ->with('captchaSvg', $captchaSvg);
        }
    }

    /**
     * Show thank you page (public access)
     */
    public function thankyou()
    {
        return view('feedback.thankyou');
    }

    /**
     * Reload CAPTCHA (AJAX endpoint)
     */
    public function reloadCaptcha()
    {
        $captchaSvg = $this->captchaService->generate();
        return response()->json(['captcha' => $captchaSvg]);
    }
}

