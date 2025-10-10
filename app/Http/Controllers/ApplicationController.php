<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\BillingDetail;
use App\Models\Country;
use App\Models\EventContact;
use App\Models\Events;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Sector;
use App\Models\State;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Models\EventParticipation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminApplicationSubmitted;
use App\Mail\UserApplicationSubmitted;
use App\Models\SecondaryEventContact;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\ExhibitorPaymentConfirmation;
use Illuminate\Support\Facades\Hash;


class ApplicationController extends Controller
{
    //

    private $countries = [
        1 => 'India',
        2 => 'United States',
        3 => 'Germany'
    ];

    private $states = [
        1 => 'Maharashtra',
        2 => 'California',
        3 => 'Bavaria'
    ];



    private $typesofbusiness = [
        1 => 'Manufacturer',
        2 => 'Distributor',
        3 => 'Retailer',
        4 => 'Wholesaler',
        5 => 'Importer',
        6 => 'Exporter',
        7 => 'Agent',
        8 => 'Service Provider',
        9 => 'Others'
    ];


    //construct function to check whether user is logged in or not
    public function __construct()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    }



    public function saveLogoLink(Request $request)
    {
        // Validate the request
        $request->validate([
            'logo_link' => 'required|url',
        ]);

        // Get the authenticated user
        $user = auth()->user();

        // Find the application for the user
        $application = Application::where('user_id', $user->id)->first();

        if (!$application) {
            return redirect()->back()->with('error', 'Application not found.');
        }

        // Update the logo link if it is not already set
        if (empty($application->logo_link)) {
            $application->logo_link = $request->input('logo_link');
            $application->save();
            return redirect()->back()->with('success', 'Logo link updated successfully.');
        }

        return redirect()->back()->with('info', 'Logo link is already set.');
    }


    // view onboarding email with the application id
    public function OnboardingEmail($applicationId)
    {
        // Find the application
        $application = Application::where('application_id', $applicationId)->first();
        if (!$application) {
            return redirect()->back()->withErrors(['error' => 'Application not found.']);
        }

        // Extract required data
        $companyName = $application->company_name;

        // Get registered email from users table
        $registeredEmail = $application->user->email ?? null;

        // Get event contact info
        $eventContact = $application->eventContact;
        $eventContactName = $eventContact ? $eventContact->first_name . ' ' . $eventContact->last_name : null;
        $eventContactEmail = $eventContact->email ?? null;

        // Determine final recipient email
        $email = $registeredEmail ?? $eventContactEmail;

        // Ensure email is present
        if (!$email) {
            return redirect()->back()->withErrors(['error' => 'No valid email found to send onboarding message.']);
        }

        $url = config('APP_URL');
        $c_name = $companyName;


        $email = "manish.sharma@interlinks.in";

        //render view from markdown('emails.exhibitor.payment_confirmation') 
        // and pass the variables to it
        // Render the view
        // $view = view('emails.exhibitor.payment_confirmation', compact('c_name', 'email', 'url'));

        // Convert the view to HTML
        //$htmlContent = $view->render();

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Onboarding email view rendered successfully.',
        //     'html' => $htmlContent,
        // ]);

        //return view('emails.exhibitor.payment_confirmation', compact('c_name', 'email', 'url'));

        $data = [
            'name' => $c_name,
            'email' => $email,
            'url' => $url
        ];

        // Send the mail
        Mail::to($email)->send(new ExhibitorPaymentConfirmation($data));

        return response()->json([
            'success' => true,
            'message' => 'Onboarding email sent successfully.',
        ]);
    }





    //call the ProductCategory model

    //generate application id as from the constants file
    public function generateApplicationId()
    {
        //call the construct function
        $this->__construct();

        $applicationId = config('constants.APPLICATION_ID_PREFIX') . substr(uniqid(), -4);
        //make sure that it doesn't match with any existing application id
        if (Application::where('application_id', $applicationId)->exists()) {
            return $this->generateApplicationId();
        }
        return $applicationId;
    }




    public function showForm(Request $request)
    {
        $this->__construct();

        //        return response()->json(['success' => true]);
        $role = 'exhibitor';
        if (!in_array($role, ['exhibitor', 'sponsor'])) {
            abort(404);
        }

        $productCategories = ProductCategory::select('id', 'name')->get();

        $countries = Country::select('id', 'name', 'code')->get();
        $states = State::select('id', 'name')->get();

        //put a condtion to check if user has already filled the information then auto populate the form

        //if user has already filled the information then auto populate the form
        $application = Application::where('user_id', auth()->id())->latest()->first();

        if ($application) {
            $eventContact = EventContact::where('application_id', $application->id)->first();
            $billing = BillingDetail::where('application_id', $application->id)->first();
            return view('applications.create', [
                'role' => $role,
                'countries' => $countries,
                'states' => $states,
                'business' => $this->typesofbusiness,
                'productCategories' => $productCategories,
                'application' => $application,
                'eventContact' => $eventContact,
                'billing' => $billing,
            ]);
        }



        if ($application) {
            $application->submission_status = 'in progress';
        }

        return view('applications.create', [
            'role' => $role,
            'countries' => $countries,
            'states' => $states,
            'business' => $this->typesofbusiness,
            'productCategories' => $productCategories,
            'application' => $application,

        ]);
    }
    public function showForm2($slug, Request $request)
    {
        $this->__construct();

        // Fetch only necessary columns and cache if possible
        $eventExists = Events::where('slug', $slug)->first(['event_name', 'event_year']);
        if (!$eventExists) {
            return redirect()->back()->withErrors(['error' => 'Event does not exist.']);
        }

        $role = 'exhibitor';
        if (!in_array($role, ['exhibitor', 'sponsor'])) {
            abort(404);
        }

        // Use caching if data doesn't change frequently
        $productCategories = Cache::remember('product_categories', 60, function () {
            return ProductCategory::select('id', 'name')->get();
        });

        $countries = Cache::remember('countries', 60, function () {
            return Country::select('id', 'name', 'code')->get();
        });

        $states = Cache::remember('states', 60, function () {
            return State::select('id', 'name')->get();
        });

        // Fetch only latest application for the user
        $application = Application::where('user_id', auth()->id())
            ->latest()
            ->select('*') // Select only necessary fields
            ->first();

        $eventContact = $billing = null;
        if ($application) {
            // Use eager loading to minimize queries
            $application->load(['eventContact', 'billingDetail']);
            $eventContact = $application->eventContact;
            $billing = $application->billing;
            $eventContact = EventContact::where('application_id', $application->id)->first();
            $billing = BillingDetail::where('application_id', $application->id)->first();
        }

        $business = $this->typesofbusiness;



        return view('sponsor.page', compact(
            'role',
            'countries',
            'states',
            'business',
            'productCategories',
            'application',
            'eventContact',
            'billing'
        ));
    }


    public function showForm3($slug, Request $request)
    {
        $this->__construct();

        $eventExists = Events::where('slug', $slug)->first(['event_name', 'event_year']);
        if (!$eventExists) {
            return redirect()->back()->withErrors(['error' => 'Event does not exist.']);
        }

        //        return response()->json(['success' => true]);
        $role = 'exhibitor';
        if (!in_array($role, ['exhibitor', 'sponsor'])) {
            abort(404);
        }

        $productCategories = ProductCategory::select('id', 'name')->get();

        $countries = Country::select('id', 'name', 'code')->get();
        $states = State::select('id', 'name')->get();

        //put a condition to check if user has already filled the information then auto populate the form

        //if user has already filled the information then auto populate the form
        $application = Application::where('user_id', auth()->id())->latest()->first();
        //        $application = Application::where('id',10)->first();
        if ($application) {
            $eventContact = EventContact::where('application_id', $application->id)->first();
            $billing = BillingDetail::where('application_id', $application->id)->first();
            return view('sponsor.page', [
                'role' => $role,
                'countries' => $countries,
                'states' => $states,
                'business' => $this->typesofbusiness,
                'productCategories' => $productCategories,
                'application' => $application,
                'eventContact' => $eventContact,
                'billing' => $billing,
            ]);
        }



        if ($application) {
            $application->submission_status = 'in progress';
        }

        return view('sponsor.page', [
            'role' => $role,
            'countries' => $countries,
            'states' => $states,
            'business' => $this->typesofbusiness,
            'productCategories' => $productCategories,
            'application' => $application,

        ]);
    }

    /* public function submitForm(Request $request )
    {
        $role = 'exhibitor';
        if (!in_array($role, ['exhibitor', 'sponsor'])) {
            abort(404);
        }

        $productCategories = ProductCategory::all();
        $productCategoryNames = $productCategories->pluck('id')->toArray();





        #dd($request->all());

        $validated = $request->validate([
            'billing_country' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Validate that the billing country exists in the Country model
                    if (!Country::where('id', $value)->exists()) {
                        $fail('The selected country for billing is invalid.');
                    }
                },
            ],
            'gst_compliance' => 'required|boolean',
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'country' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Validate that the country exists in the Country model
                    if (!Country::where('id', $value)->exists()) {
                        $fail('The selected country is invalid.');
                    }
                },
            ],
            'state' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    // Validate that the state exists in the State model for the selected country
                    $country = Country::find($request->input('country'));
                    if (!$country || !State::where('country_id', $country->id)->where('id', $value)->exists()) {
                        $fail('The selected state is invalid for the selected country.');
                    }
                },
            ],
            'company_no' => 'nullable|string|max:15',
            'company_email' => 'required|email|max:255',
            'website' => 'nullable|url',
            #'main_product_category' => '1',
            'gst_no' => 'nullable|string',
            'pan_no' => 'nullable|string',
            'tan_no' => 'nullable|string',
            'event_contact_salutation' => 'required|string|max:10',
            'event_contact_first_name' => 'required|string|max:255',
            'event_contact_last_name' => 'required|string|max:255',
            'event_contact_email' => 'required|email|max:255',
            'event_contact_phone' => 'required|string|max:15',
            #'type_of_business' => 'required|array' . implode(',', array_keys($this->typesofbusiness)),
            'billing_company' => 'required|string|max:255',
            'billing_contact_name' => 'required|string|max:255',
            'billing_email' => 'required|email|max:255',
            'billing_phone' => 'required|string|max:15',
            'billing_address' => 'required|string|max:500',
            'billing_postal_code' => 'required|string|max:10',
            'billing_city' => 'required|string|max:255',
            'billing_state' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    // Validate that the state exists in the State model for the selected country
                    $country = Country::find($request->input('country'));
                    if (!$country || !State::where('country_id', $country->id)->where('id', $value)->exists()) {
                        $fail('The selected state is invalid for the selected country.');
                    }
                },
            ],
            'gst_certificate' => 'required|file|mimes:pdf|max:2048',

        ]);


        $gst_certificate = $request->file('gst_certificate')->store('gst_certificates');

        //if country_code other than 1 then payment_currency is USD else INR
        if($request->country != 1){
            $payment_currency = 'USD';
        }else{
            $payment_currency = 'INR';
        }

        #dd($gst_certificate);

        // Save Application
        $application = Application::create([
            'user_id' => auth()->id(),
            'headquarters_country_id' => $request->country,
            'billing_country_id' => $request->billing_country,
            'gst_compliance' => $request->gst_compliance,
            'company_name' => $request->company_name,
            'address' => $request->address,
            'landline' => $request->company_no,
            'postal_code' => $request->postal_code,
            'city_id' => $request->city,
            'country_id' => $request->country,
            'state_id' => $request->state,
            'main_product_category' => 'Category1',
            'gst_no' => $request->gst_no,
            'pan_no' => $request->pan_no,
            'tan_no' => $request->tan_no,
            'certificate' => $gst_certificate,
            'company_email' => $request->company_email,
            'website' => $request->website,
            'type_of_business'=> implode(',', $request->type_of_business),
            'payment_currency' => $payment_currency,
            'status' => 'initiated',
        ]);

        //DISPLAY the generated query fromabove code
        #dd($application);

        // Save Event Contact
        $eventContact = EventContact::create([
            'application_id' => $application->id,
            'salutation' => $request->event_contact_salutation,
            'first_name' => $request->event_contact_first_name,
            'last_name' => $request->event_contact_last_name,
            'email' => $request->event_contact_email,
            'contact_number' => $request->event_contact_phone,
        ]);

        #dd($eventContact);



        // Save Billing Details
        $billing = BillingDetail::create([
            'application_id' => $application->id,
            'billing_company' => $request->billing_company,
            'contact_name' => $request->billing_contact_name,
            'email' => $request->billing_email,
            'phone' => $request->billing_phone,
            'address' => $request->billing_address,
            'postal_code' => $request->billing_postal_code,
            'city_id' => $request->billing_city,
            'state_id' => $request->billing_state,
            'country_id' => $request->billing_country,
            'same_as_basic' => '0',
        ]);

        #dd($billing);

        return redirect()->route("dashboard.{$role}")->with('success', 'Application submitted successfully!');
    } */

    public function submitForm(Request $request)
    {



        //        dd($request->all());
        $this->__construct();
        $role = 'exhibitor';

        if (!in_array($role, ['exhibitor', 'sponsor'])) {
            abort(404);
        }



        //dd($request->all());
        // Validation
        $validated = $request->validate([
            'billing_country' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Country::where('id', $value)->exists()) {
                        $fail('The selected country for billing is invalid.');
                    }
                },
            ],
            'gst_compliance' => [
                'required_if:billing_country,101,351',
                'boolean'
            ],
            'gst_no' => 'nullable|string|required_if:gst_compliance,1',
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'postal_code' => 'required|string|max:10',
            'city' => 'required|string|max:255',
            'contactNoCode' => 'required|string|max:5',
            'contactPhone_code' => 'required|string|max:5',
            'billing_phoneCode' => 'required|string|max:5',

            //            'country' => [
            //                'required',
            //                function ($attribute, $value, $fail) {
            //                    if (!Country::where('id', $value)->exists()) {
            //                        $fail('The selected country is invalid.');
            //                    }
            //                },
            //            ],
            'state' => 'required|string|max:255',
            //            'state' => [
            //                'required',
            ////                function ($attribute, $value, $fail) use ($request) {
            ////                    $country = Country::find($request->input('country'));
            ////                    if (!$country || !State::where('country_id', $country->id)->where('id', $value)->exists()) {
            ////                        $fail('The selected state is invalid for the selected country.');
            ////                    }
            ////                },
            //            ],
            'company_no' => 'nullable|string|max:15',
            'company_email' => 'required|email|max:255',
            //if website is without http or https then add https to it
            'website' => 'nullable|string|max:255',
            'pan_no' => 'nullable|string',
            'tan_no' => 'nullable|string',
            'event_contact_salutation' => 'required|string|max:10',
            'event_contact_first_name' => 'required|string|max:255',
            'event_contact_last_name' => 'required|string|max:255',
            'event_contact_designation' => 'required|string|max:255',
            'event_contact_email' => 'required|email|max:255',
            'event_contact_phone' => 'required|string|max:15',
            'billing_company' => 'required|string|max:255',
            'billing_contact_name' => 'required|string|max:255',
            'billing_email' => 'required|email|max:255',
            'billing_phone' => 'required|string|max:15',
            'billing_address' => 'required|string|max:500',
            'billing_postal_code' => 'required|string|max:10',
            'billing_city' => 'required|string|max:255',
            'event_id' => 'required|exists:events,id',
            'type_of_business' => 'required|array',
            'assoc_mem' => 'nullable|string',
            'country' => 'required|exists:countries,id',
            'sec_event_contact_salutation' => 'nullable|string|max:10',
            'sec_event_contact_first_name' => 'nullable|string|max:255',
            'sec_event_contact_last_name' => 'nullable|string|max:255',
            'sec_event_contact_designation' => 'nullable|string|max:255',
            'sec_event_contact_email' => 'nullable|email|max:255',
            'sec_event_contact_phone' => 'nullable|string|max:15',




            'billing_state' => 'required|string|max:255',

            //            'billing_state' => [
            //                'required',
            //                function ($attribute, $value, $fail) use ($request) {
            //                    $country = Country::find($request->input('country'));
            //                    if (!$country || !State::where('country_id', $country->id)->where('id', $value)->exists()) {
            //                        $fail('The selected state is invalid for the selected country.');
            //                    }
            //                },
            //            ],
            'gst_certificate' => 'nullable|file|mimes:pdf|max:2048',
        ]);




        $payment_currency = $request->country != 1 ? 'EUR' : 'INR';

        // Check if the application already exists
        $application = Application::firstOrNew([
            'user_id' => auth()->id(),
            'event_id' => $request->event_id,
        ]);

        if ($request->hasFile('gst_certificate')) {
            // Store the new file and update the application record
            $gst_certificate = $request->file('gst_certificate')->store('gst_certificates', 'public');
            $application->certificate = $gst_certificate;
        }
        if ($request->billing_country === 101 || $request->billing_country === 351) {
            $payment_currency = 'INR';
        } else {
            $payment_currency = 'EUR';
        }
        $companyPhone = $request->contactNoCode . '-' . $request->company_no;
        $contactPhone = $request->contactPhone_code . '-' . $request->event_contact_phone;
        $billingPhone = $request->billing_phoneCode . '-' . $request->billing_phone;

        //if website is without http or https then add https to it
        if ($request->website && !preg_match('/^https?:\/\//', $request->website)) {
            $request->website = 'https://' . $request->website;
        }

        $application->fill([
            'headquarters_country_id' => $request->headquarters_country,
            'billing_country_id' => $request->billing_country,
            'gst_compliance' => $request->gst_compliance,
            'company_name' => $request->company_name,
            'address' => $request->address,
            'landline' => $companyPhone,
            'postal_code' => $request->postal_code,
            'city_id' => $request->city,
            'country_id' => $request->country,
            'state_id' => $request->state,
            'main_product_category' => $request->main_product_category,
            'gst_no' => $request->gst_no,
            'pan_no' => $request->pan_no,
            'tan_no' => $request->tan_no,
            //            'certificate' => $gst_certificate,
            'company_email' => $request->company_email,
            'website' => $request->website,
            'type_of_business' => implode(',', $request->type_of_business),
            'payment_currency' => $payment_currency,
            'status' => 'initiated',
            'application_id' => $application->exists ? $application->application_id : $this->generateApplicationId(),
            'country_name' => $request->country,
            'assoc_mem' => $request->assoc_mem,
        ]);

        // Save the application
        $application->save();



        // Update or create an EventContact
        $eventContact = EventContact::updateOrCreate(
            // Matching criteria to find the record
            [
                'application_id' => $application->id,
                'email' => $request->event_contact_email,
            ],
            // Fields to update or insert
            [
                'salutation' => $request->event_contact_salutation,
                'first_name' => $request->event_contact_first_name,
                'last_name' => $request->event_contact_last_name,
                'designation' => $request->event_contact_designation,
                'job_title' => $request->event_contact_designation,
                'contact_number' => $contactPhone,
            ]
        );

        // Update or create a SecondaryEventContact
        if ($request->sec_event_contact_email && $request->sec_event_contact_phone && $request->sec_event_contact_first_name) {
            $sec_contact_no = $request->sec_contactPhone_code . '-' . $request->sec_event_contact_phone;
            $secEventContact = SecondaryEventContact::updateOrCreate(
                [
                    'application_id' => $application->id,
                    'email' => $request->sec_event_contact_email,
                ],
                [
                    'salutation' => $request->sec_event_contact_salutation,
                    'first_name' => $request->sec_event_contact_first_name,
                    'last_name' => $request->sec_event_contact_last_name,
                    'designation' => $request->sec_event_contact_designation,
                    'job_title' => $request->sec_event_contact_designation,
                    'contact_number' => $sec_contact_no,
                ]
            );
        }



        // Update or create billing details
        $billing = BillingDetail::updateOrCreate(
            ['application_id' => $application->id],
            [
                'billing_company' => $request->billing_company,
                'contact_name' => $request->billing_contact_name,
                'email' => $request->billing_email,
                'phone' => $billingPhone,
                'address' => $request->billing_address,
                'postal_code' => $request->billing_postal_code,
                'city_id' => $request->billing_city,
                'state_id' => $request->billing_state,
                'country_id' => $request->billing_country,
                'gst_id' => $request->gst_no,
                'same_as_basic' => '0',
            ]
        );

        //redirect to apply page with name application.show
        return redirect()->route('application.show')->with('success', 'Application saved successfully!');

        return redirect()->route("dashboard.{$role}")->with('success', 'Application saved successfully!');
    }


    public function review()
    {
        $this->__construct();
        $application = Application::where('user_id', auth()->id())->latest()->first();
        $eventContact = EventContact::where('application_id', $application->id)->first();
        $billing = BillingDetail::where('application_id', $application->id)->first();
//        dd($application, $eventContact, $billing);
        return view('applications.review', [
            'application' => $application,
            'eventContact' => $eventContact,
            'billing' => $billing,
        ]);
    }
    //second step of form submission

    public function apply()
    {
        return redirect()->route('event.list');
        $this->__construct();
        //if already filled the form then show the form with the filled data
        $application = Application::where('user_id', auth()->id())->latest()->first();
        //return to event participation page with route name event.list'
        if (!$application) {
            return redirect()->route('event.list');
        }


        //        $participation_type =  ['Onsite' => 'active', 'Hybrid' => 'active', 'Online' => 'disabled']; // Replace with dynamic data if needed
        $participation_type =  ['Onsite' => 'active']; // Replace with dynamic data if needed
        $productGroups = [
            'Semiconductor Design (EDA, IP, etc)',
            'Semiconductor Equipment',
            'Semiconductor Materials',
            'Flat panel display',
            'Fab facilities and semiconductor-related services',
            'Government / Association / Research',
            'Semiconductor Front-end manufacturing (IDM, foundry, etc)',
            'Semiconductor Back-end manufacturing (assembly, packaging, testing)',
        ];
        $sectors = Sector::select('id', 'name')->get();
        $stall_type = ['Shell Scheme', 'Bare Space']; // Replace with dynamic data if needed
        if ($application) {
            return view('applications.apply-page', compact('application', 'productGroups', 'sectors', 'stall_type', 'participation_type'));
        }



        return view('applications.apply-page', compact('participation_type', 'productGroups', 'sectors', 'stall_type'));
    }
    public function apply_spon()
    {
        return redirect()->route('event.list');
        $this->__construct();
        //if already filled the form then show the form with the filled data
        $application = Application::where('user_id', auth()->id())->latest()->first();
        //return to event participation page with route name event.list'
        if (!$application) {
            return redirect()->route('event.list');
        }


        //        $participation_type =  ['Onsite' => 'active', 'Hybrid' => 'active', 'Online' => 'disabled']; // Replace with dynamic data if needed
        $participation_type =  ['Onsite' => 'active']; // Replace with dynamic data if needed
        $productGroups = [
            'Semiconductor Design (EDA, IP, etc)',
            'Semiconductor Equipment',
            'Semiconductor Materials',
            'Flat panel display',
            'Fab facilities and semiconductor-related services',
            'Government / Association / Research',
            'Semiconductor Front-end manufacturing (IDM, foundry, etc)',
            'Semiconductor Back-end manufacturing (assembly, packaging, testing)',
        ];
        $sectors = Sector::select('id', 'name')->get();
        $stall_type = ['Shell Scheme', 'Bare Space']; // Replace with dynamic data if needed
        if ($application) {
            return view('sponsor.apply-page', compact('application', 'productGroups', 'sectors', 'stall_type', 'participation_type'));
        }

        return view('sponsor.apply-page', compact('participation_type', 'productGroups', 'sectors', 'stall_type'));
    }
    public function apply_new()
    {
        return redirect()->route('event.list');
        $this->__construct();
        //if already filled the form then show the form with the filled data
        $application = Application::where('user_id', auth()->id())->latest()->first();
        $participation_type =  ['Onsite' => 'active', 'Hybrid' => 'active', 'Online' => 'disabled']; // Replace with dynamic data if needed
        $productGroups = ['Group A', 'Group B', 'Group C']; // Replace with dynamic data if needed
        $sectors = Sector::select('id', 'name')->get();
        $stall_type = ['Shell Scheme', 'Bare Space']; // Replace with dynamic data if needed
        if ($application) {
            return view('sponsor.apply-page', compact('application', 'productGroups', 'sectors', 'stall_type', 'participation_type'));
        }



        return view('sponsor.apply-page', compact('participation_type', 'productGroups', 'sectors', 'stall_type'));
    }

    public function apply_store(Request $request)
    {
        $this->__construct();
        //dd($request->all());

        $validatedData = $request->validate([
            'participation_type' => 'required|in:Onsite,Hybrid,Online',
            'region' => 'required|in:India,International',
            'previous_participation' => 'required|boolean',
            'stall_category' => 'required|string',
            'sponsorship_apply' => 'required|boolean',
            'interested_sqm' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->sponsorship_apply != 1 && $value < 1) {
                        $fail('The interested sqm must be at least 1 unless sponsorship is applied.');
                    }
                },
            ],
            'product_groups' => 'required|array',
            'sectors' => 'required|array|exists:sectors,id',
            'terms_accepted' => 'accepted',
            'semi_member' => 'required|boolean',
            'semi_member_id' => 'nullable|string',
            'pref_location' => 'required|string',

        ]);

        //if stall_category bare space then interested_sqm should be greater than 18
        if ($validatedData['stall_category'] === 'Bare Space' && $validatedData['interested_sqm'] < 18) {
            return redirect()->back()->withErrors(['interested_sqm' => 'Interested sqm should be greater than 18 for Bare Space stall category.']);
        }

        //dd($validatedData);


        $application = Application::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'participation_type' => $request['participation_type'],
                'region' => $validatedData['region'],
                'participated_previous' => $validatedData['previous_participation'],
                'stall_category' => $validatedData['stall_category'],
                'interested_sqm' => $validatedData['interested_sqm'],
                'product_groups' => $validatedData['product_groups'],
                'sponsor_only' => $validatedData['sponsorship_apply'],
                'cancellation_terms' => 1,
                'sector_id' => $validatedData['sectors'],
                'semi_member' => $validatedData['semi_member'],
                'semi_memberID' => $validatedData['semi_member_id'],
                'payment_currency' => $validatedData['region'] === 'India' ? 'INR' : 'EUR',
                'pref_location' => $validatedData['pref_location'],
            ]
        );

        // Update sectors relationship
        $application->sectors()->sync($validatedData['sectors']);
        //redirect to route named terms
        return redirect()->route('terms')->with('success', 'Application saved successfully!');

        return response()->json(['message' => 'Data updated', 'application' => $application]);
    }

    //terms and conditions with I acknowledge that I have read the above terms and condition carefully.* checkbox
    public function terms()
    {
        return redirect()->route('event.list');
        $this->__construct();
        $application = Application::where('user_id', auth()->id())->latest()->first();
        return view('applications.terms_new', compact('application'));
    }

    //terms and conditions with I acknowledge that I have read the above terms and condition carefully.* checkbox
    public function terms_store(Request $request)
    {
        return redirect()->route('event.list');
        $this->__construct();
        $validatedData = $request->validate([
            'terms_accepted' => 'accepted',
        ]);

        $application = Application::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'terms_accepted' => 1,
            ]
        );

        //redirect to route named preview
        return redirect()->route('application.preview')->with('success', 'Application saved successfully!');

        return response()->json(['message' => 'Data updated', 'application' => $application]);
    }

    //preview of everything filled by the user.
    public function preview()
    {
        return redirect()->route('event.list');
        $this->__construct();
        //if user is not logged in then redirect to login page

        $application = Application::where('user_id', auth()->id())->latest()->first();


        $eventContact = EventContact::where('application_id', $application->id)->first();
        $billing = BillingDetail::where('application_id', $application->id)->first();

        $invoice = Invoice::where('application_id', $application->id)->first() ?? null;
        //$id = $invoice->id;
        //        dd($invoice, $id);
        $payments = array();
        if ($invoice) {
            $in_id = $invoice->id;
            //            dd($in_id);
            //$payments = Payment::where('application_id', $application->application_id)->get();
            $payments = Payment::where('invoice_id', $in_id)->get();
        }

        //        dd($payments);


        return view('applications.preview_new', [
            'application' => $application,
            'eventContact' => $eventContact,
            'billing' => $billing,
            'invoice' => $invoice,
            'payments' => $payments,
        ]);
    }

    //final
    public function final(Request $request)
    {
        //redirect to event.list
        return redirect()->route('event.list');
        $this->__construct();
        $application = Application::where('user_id', auth()->id())->latest()->first();

        $application->submission_status = 'submitted';
        $application->submission_date = now();
        $application->save();

        // Get the admin email (replace with your actual admin email)
        //semiconindia@semi.org
        $emails = ['test.interlinks@gmail.com'];

        // Send email to admin and organisers using BCC
        $adminEmails = config('constants.admin_emails.to');


        Mail::to($adminEmails)->bcc($emails)->send(new AdminApplicationSubmitted($application));

        $userEmails = [
            $application->eventContact->email,
            // $application->billingDetail->email,
            // auth()->user()->email
        ];

        foreach ($userEmails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($email)->send(new UserApplicationSubmitted($application));
                } catch (\Exception $e) {
                    // Handle the error (log it, notify admin, etc.)
                    \Log::error("Failed to send email to {$email}: " . $e->getMessage());
                }
            } else {
                // Handle invalid email (log it, notify admin, etc.)
                \Log::error("Invalid email address: {$email}");
            }
        }





        // dd($application);
        //if invoice details are found for application->id in invoice table then pass the details in compact
        //else pass null



        //redirect to application.final with $application
        return redirect()->route('application.preview')->with('success', 'Application submitted successfully! ')->with(compact('application'));

        #return redirect()->route('dashboard.exhibitor')->with('success', 'Application submitted successfully!');
    }
    public function final_admin(Request $request)
    {
        $this->__construct();
        //add a validation of application_id in request
        $validated = $request->validate([
            'application_id' => 'required|exists:applications,application_id',
        ]);



        $application = Application::where('application_id', $request->application_id)->first();

        $application->submission_status = 'submitted';
        $application->submission_date = $application->updated_at ?? now();
        $application->save();

        // Get the admin email (replace with your actual admin email)
        //semiconindia@semi.org
        $emails = ['test.interlinks@gmail.com'];
        $adminEmails = config('constants.admin_emails.to');

        // Send email to admin and organisers using BCC
        Mail::to($adminEmails)->bcc($emails)->send(new AdminApplicationSubmitted($application));

        $userEmails = [
            $application->eventContact->email,
            // $application->billingDetail->email,
            // auth()->user()->email
        ];

        foreach ($userEmails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($email)->send(new UserApplicationSubmitted($application));
                } catch (\Exception $e) {
                    // Handle the error (log it, notify admin, etc.)
                    \Log::error("Failed to send email to {$email}: " . $e->getMessage());
                }
            } else {
                // Handle invalid email (log it, notify admin, etc.)
                \Log::error("Invalid email address: {$email}");
            }
        }





        // dd($application);
        //if invoice details are found for application->id in invoice table then pass the details in compact
        //else pass null



        //redirect to application.final with $application
        return redirect()->route('application.preview')->with('success', 'Application submitted successfully! ')->with(compact('application'));

        #return redirect()->route('dashboard.exhibitor')->with('success', 'Application submitted successfully!');
    }
    //invoice display by post
    public function invoice(Request $request, $applicationId)
    {
        $this->__construct();
        //get application id from request
        #$applicationId = $request->input('application_id');
        //get application details from application model
        #$application = Application::find($applicationId);
        $application = Application::where('application_id', $applicationId)->first();
        $productCategories = ProductCategory::select('id', 'name')->get();
        //verify the user_id and application_id from the application model
        if ($application->user_id != auth()->id()) {
            return redirect()->route('dashboard.exhibitor')->withErrors(['error' => 'Unauthorized access.']);
        }
        $application->main_product_category = $productCategories->where('id', $application->main_product_category)->first()->name;
        //get invoice details from invoice model
        $applicationId = $application->id;
        $invoice = Invoice::where('application_id', $applicationId)->first();
        //dd($invoice);
        return view('applications.invoice_info', compact('application', 'invoice'));
    }


    //application info from application id
    public function applicationInfo()
    {
        $this->__construct();
        //from the auth user take the application id and get the details of the application

        $applicationId = auth()->user()->applications->first()->id;

        $productCategories = ProductCategory::select('id', 'name')->get();

        //get application details from application model
        $application = Application::find($applicationId);
        //get invoice details from invoice model
        $invoice = Invoice::where('application_id', $applicationId)->first();
        //billing details from billing detail model
        $billingDetails = BillingDetail::where('application_id', $applicationId)->first();
        //event contact details from event contact model
        $eventContact = EventContact::where('application_id', $applicationId)->first();

        return view('applications.application_info', compact('application', 'invoice', 'billingDetails', 'eventContact', 'productCategories'));
    }

    //make a ajax call to get the interested sqm based on the stall category
    public function getSQMOptions(Request $request)
    {
        $stallType = $request->input('stall_type');
        $startValue = ($stallType === "Bare") ? 18 : 9; // Set start value based on selection
        $options = [];

        for ($i = $startValue; $i <= 900; $i += 9) {
            $options[] = ['value' => $i, 'text' => $i . ' sqm'];
        }

        return response()->json($options);
    }

    //make ajax call to get the country code based on the country id
    public function getCountryCode(Request $request)
    {
        $countryId = (int) $request->input('country_id');
        //input type is int so check if it is integer or not
        if (!is_int($countryId)) {
            return response()->json(['error' => 'Invalid country ID.']);
        }
        //validate the country id
        if (!Country::where('id', $countryId)->exists()) {
            return response()->json(['error' => 'Invalid country ID.']);
        }
        $country = Country::find($countryId);
        return response()->json([
            'code' => $country->code,
            'id' => $country->id
        ]);
    }



    //export pdf of the application
    public function exportPDF(Request $request)
    {

        //redirect to event.list
        return redirect()->route('event.list');
        $this->__construct();
        //get the application id from the auth user 
        //get auth user id
        $userId = auth()->id();

        // $userId = 105;

        $application = Application::where('user_id', $userId)->first();

        // validate the application id from request 

        $productCategories = ProductCategory::select('id', 'name')->get();

        //sector details from sector model
        $sectors = Sector::select('id', 'name')->get();


        //export this view into pdf
        $pdf = PDF::loadView('export.application_export', compact('application', 'productCategories', 'sectors'));
        //download the pdf with name $application->company_name.pdf

        $timestamp = now()->format('Ymd_His');
        return $pdf->download(
            str_replace(' ', '_', $application->company_name)
            . '_-' . config('constants.SHORT_NAME')
            . '_' . $application->event->event_year
            . '_' . $timestamp
            . '.pdf'
        );
        //dd($invoice);
        return view('export.application_export', compact('application', 'productCategories', 'sectors'));
    }
    public function exportPDF_admin(Request $request)
    {

        $this->__construct();
        //get the application id from the auth user 
        //get auth user id
        // get the application id from the request

        $validated = $request->validate([
            'application_id' => 'required|exists:applications,application_id',
        ]);


        // get the application id from the request and store it in $userId
        $userId = $request->application_id;
        // $userId = 105;

        $application = Application::where('application_id', $userId)->first();

        // validate the application id from request 

        $productCategories = ProductCategory::select('id', 'name')->get();

        //sector details from sector model
        $sectors = Sector::select('id', 'name')->get();



        //return view from export.application_export with compact application, productCategories, sectors
        // dd($application, $productCategories, $sectors);
        //echo view('export.application_export', compact('application', 'productCategories', 'sectors'));
//exit;

        //let the images direct path visible in the pdf
         $pdf = PDF::setOptions(['isRemoteEnabled' => true])->loadView('export.application_export', compact('application', 'productCategories', 'sectors'));

        //export this view into pdf
//        $pdf = PDF::loadView('export.application_export', compact('application', 'productCategories', 'sectors'));
        //download the pdf with name $application->company_name.pdf

        $timestamp = now()->format('Ymd_His');


        //view the pdf in browser
        //return $pdf->stream(str_replace(' ', '_', $application->company_name) . '_-' . config('constants.SHORT_NAME') . '_' . $application->event->event_year . '_' . $timestamp . '.pdf');
        //

        return $pdf->download(str_replace(' ', '_', $application->company_name) . '_-' . config('constants.SHORT_NAME') . '_' . $application->event->event_year . '_' . $timestamp . '.pdf');

        //dd($invoice);
        return view('export.application_export', compact('application', 'productCategories', 'sectors'));
    }

    /**
     * Show the form for creating a new application (Admin)
     */
    public function create()
    {
        // Check if user is admin
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect('/login')->with('error', 'Unauthorized access');
        }

        // Get necessary data for the form
        $countries = \App\Models\Country::all();
        $states = \App\Models\State::all();
        $cities = array();
        $sectors = \App\Models\Sector::all();
        $tickets = \App\Models\Ticket::where('nationality', 'Indian')
            ->select('id', 'ticket_type')
            ->distinct('ticket_type')
            ->get();
       // dd($tickets);

        return view('admin.application.create', compact('countries', 'states', 'cities', 'sectors', 'tickets'));
    }

    /**
     * Store a newly created application (Admin)
     */
    public function store(Request $request)
    {

        // dd($request->all());
        // Check if user is admin
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect('/login')->with('error', 'Unauthorized access');
        }

        //user validation with email unique
        $request->validate([
            'company_email' => 'required|email|max:255|unique:users,email',
            'company_name' => 'required|string|max:255',
        ]);

        // password generate
        $password = substr(md5(uniqid()), 0, 10);

        // Import the Hash facade at the top of your file with: use Illuminate\Support\Facades\Hash;

        // password hash
        $passwordHash = Hash::make($password);

        // make user first with email and name
        // $user = \App\Models\User::create([
        //     'name' => $request->company_name,
        //     'email' => $request->company_email,
        //     'password' => $passwordHash, // Default password, user should change
        //     'simplePass' => $password,
        //     'role' => 'exhibitor',
        //     'email_verified_at' => now(),
        // ]);

        // dd($user);

        //generate application id
        


        // Get valid ticket IDs from database
        $validTicketIds = \App\Models\Ticket::pluck('id')->toArray();
        $ticketIdsRule = 'required|in:' . implode(',', $validTicketIds);

        // Validate the request
        $request->validate([            
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'application_type' => 'required|in:exhibitor,sponsor',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:20',
            'city_id' => 'nullable|string|max:255',
            'state_id' => 'nullable|exists:states,id',
            'country_id' => 'nullable|exists:countries,id',
            'comments' => 'nullable|string',
            // 'stall_category' => 'required|in:Shell Scheme,Bare Space, Startup Booth',
            // 'booth_size' => 'required|integer|min:1|max:36',
            // 'payment_currency' => 'required|in:EUR,INR',
            'ticket_ids' => 'required|array|min:1',
            'ticket_ids.*' => $ticketIdsRule,
            'ticket_counts' => 'required|array|min:1',
            'ticket_counts.*' => 'required|integer|min:1',
        ]);

        try {
            // Create user first
            $user = \App\Models\User::create([
                'name' => $request->company_name,
                'email' => $request->company_email,
                'password' => $passwordHash, // Default password, user should change
                'simplePass' => $password,
                'role' => 'exhibitor',
                'email_verified_at' => now(),
            ]);

            // Generate unique application ID
            $applicationId = $this->generateApplicationId();

            // Create the application
            $application = Application::create([
                'user_id' => $user->id,
                'company_name' => $request->company_name,
                'company_email' => $request->company_email,
                'status' => 'approved',
                'application_id' => $applicationId,
                'submission_status' => 'approved',
                'approved_by' => auth()->user()->name,
                'submission_date' => now(),
                'RegSource' => 'Admin',
                'approved_date' => now(),
            ]);

            // Create ticket allocations if provided
            if ($request->ticket_ids && $request->ticket_counts) {
                $ticketAllocation = [];
                foreach ($request->ticket_ids as $index => $ticketId) {
                    if (isset($request->ticket_counts[$index])) {
                        $ticketAllocation[$ticketId] = (int)$request->ticket_counts[$index];
                    }
                }
                
                if (!empty($ticketAllocation)) {
                    \App\Models\ExhibitionParticipant::create([
                        'application_id' => $application->id,
                        'ticketAllocation' => json_encode($ticketAllocation),
                        'stall_manning_count' => 0,
                        'complimentary_delegate_count' => 0,
                    ]);
                }
            }

            return redirect()->route('application.lists')
                ->with('success', 'Application and user account created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create application: ' . $e->getMessage());
        }
    }
}
