<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMail;
use App\Models\BillingDetail;
use App\Models\EventContact;
use App\Models\SecondaryEventContact;
use App\Models\ProductCategory;
use App\Models\Sector;
use App\Models\Sponsorship;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Application;
use App\Http\Middleware\Auth;
use App\Helpers\ExhibitorPriceCalculator;
use App\Models\Invoice;
use App\Models\Country;
use App\Models\State;
use App\Http\Controllers\MailController;
use App\Models\DeletedBillingDetail;
use App\Models\DeletedEventContact;
use App\Models\DeletedApplication;
use App\Models\DeletedSecondaryEventContact;
//log
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use App\Mail\Onboarding;
use App\Mail\UserCredentialsMail;




class AdminController extends Controller
{


    //call the middleware to check if user is logged in
    // public function __construct()
    // {
    //     if (auth()->check() && auth()->user()->role !== 'admin') {
    //         return redirect('/login');
    //     }

    // }

    public function __construct()
    {
        $this->middleware(['admin']);
    }

    // make a route to display all the users in a table with pagination and search and sort
    public function usersList(Request $request)
    {
        $query = User::query();
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('applications', function($appQuery) use ($search) {
                      $appQuery->where('company_name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Sorting
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortField, ['name', 'email', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } elseif ($sortField === 'company') {
            // For company sorting, we need to join with applications table
            $query->leftJoin('applications', 'users.id', '=', 'applications.user_id')
                  ->orderBy('applications.company_name', $sortDirection)
                  ->select('users.*'); // Ensure we only select user columns
        }
        
        // Pagination
        $perPage = $request->get('per_page', 10);
        $users = $query->paginate($perPage);
        $users->appends($request->query());
        
        // Add company name from applications table
        foreach ($users as $user) {
            // Get the most recent application or the first one if multiple exist
            $application = $user->applications()->latest()->first();
            $user->company = $application ? $application->company_name : 'N/A';
        }
        
        return view('admin.users-direct', compact('users'));
    }


    public function getUsers(Request $request)
    {
        // Check if the user is logged in and has an admin role
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $sortField = $request->input('sort', 'name'); // Default sort by 'name'
        $sortDirection = $request->input('direction', 'asc'); // Default sort 'asc'
        $perPage = $request->input('per_page', 10); // Default 10 items per page

        $users = User::orderBy($sortField, $sortDirection)->paginate($perPage);

        // if the user has application then pass the company name as in users.company 
        $users->each(function ($user) {
            // Get the most recent application or the first one if multiple exist
            $application = $user->applications()->latest()->first();
            $user->company = $application ? $application->company_name : 'N/A';
        });

        return response()->json($users);
    }

    //bring back to submission list if only rejected
    public function submission_back(Request $request)
    {
        // Validate the request
        $request->validate([
            'application_id' => 'required|exists:applications,id',
        ]);

        // Find the application by id
        $application = Application::find($request->input('application_id'));

        // If application not found or not rejected, redirect to dashboard.admin
        if (!$application || $application->submission_status !== 'rejected') {
            //redirect back from where the request came from
            return redirect()->back();
        }

        // Change the submission_status to submitted
        $application->submission_status = 'submitted';
        $application->rejection_reason = null;
        $application->rejected_date = null;
        $application->save();

        // Redirect to the application list with success message
        return redirect()->back()->with('success', 'Application has been moved back to submission list successfully.');
        return redirect()->route('dashboard.admin')->with('success', 'Application has been moved back to submission list successfully.');
    }


    //return view at admin.test
    public function test()
    {
        return view('admin.test');
    }

    //fetch all application list
    public function index($status = null)
    {
        //check user is logged in or not
        if (!auth()->check()) {
            return redirect('/login');
        }
        $slug = 'Application List';

        if ($status) {
            if ($status == 'in-progress') {
                $status = 'in progress';
            }
            $slug = $status . ' - Application List ';
            //            $applications = Application::with('eventContact')->where('submission_status', $status)->whereDoesntHave('sponsorships')->get();
            // $applications = Application::with('eventContact')->where('submission_status', $status)->where('application_type', 'exhibitor')->get();
            $applications = Application::with('eventContact')
                ->where('submission_status', $status)
                ->where('application_type', 'exhibitor')
                ->orderBy('submission_date', 'desc') // or 'asc' for ascending order
                ->get();

            //can i get the query log for this query from $appliications
            //Log::info('Applications Query Log', DB::getQueryLog());
        } else {
            $applications = Application::with('eventContact')
                ->orderBy('submission_date', 'desc') // or 'asc' for ascending order
                ->get();
            // $applications = Application::with('eventContact')->get();
            // $applications = Application::with('eventContact')->whereDoesntHave('sponsorships')->get();
        }

        if ($status == 'approved') {
            //    $applications = Application::with('eventContact', 'invoice')->where('submission_status', 'approved')->whereDoesntHave('sponsorships')->get();
            // $applications = Application::with('eventContact', 'invoice')->where('submission_status', 'approved')->where('application_type', 'exhibitor')->get();
            // $applications = Application::with('eventContact', 'invoice')->where('submission_status', 'approved')->get();
            $applications = Application::with('eventContact', 'invoice')
                ->where('submission_status', 'approved')
                ->orderBy('submission_date', 'desc') // or 'asc' for ascending order
                ->get();
            // dd($applications);
            //total revenue from all approved applications from price field in invoice table
            $totalRevenue = Invoice::where('type', 'Stall Booking')
                            ->whereIn('payment_status', ['paid'])
                            ->sum('total_final_price');

            return view('dashboard.approved_list', compact('applications', 'slug', 'totalRevenue'));
        }


        return view('dashboard.list', compact('applications', 'slug'));
    }

    public function applicationUpdate(Request $request, $id)
    {

        // dd($request->all());
        // Validate the incoming request data
        $request->validate([
            'company_name' => 'required|string|max:255',
            'website' => 'nullable|url',
            'address' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'main_product_category' => 'required|exists:product_categories,id',
            'type_of_business' => 'required|string|max:255',
            'sectors' => 'nullable|array',
            'sectors.*' => 'exists:sectors,id',
            'stall_category' => 'nullable|string|max:255',
            'interested_sqm' => 'nullable|integer',
            'allocated_sqm' => 'nullable|integer',
            'semi_member' => 'nullable|string',
            'semi_memberID' => 'nullable|string|max:100',
            'event_contact_name' => 'nullable|string|max:255',
            'event_contact_design' => 'nullable|string|max:255',
            'event_contact_email' => 'nullable|email',
            'event_contact_mobile' => 'nullable|string|max:20',
            'secondary_contact_name' => 'nullable|string|max:255',
            'secondary_contact_design' => 'nullable|string|max:255',
            'secondary_contact_email' => 'nullable|email',
            'secondary_contact_mobile' => 'nullable|string|max:20',
            'gst_compliance' => 'nullable|string',
            'gst_no' => 'nullable|string|max:20',
            'pan_no' => 'nullable|string|max:20',
            'billing_company' => 'nullable|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'billing_email' => 'nullable|email',
            'billing_phone' => 'nullable|string|max:20',
            'billing_address' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:255',
            'billing_state' => 'nullable|exists:states,id',
            'billing_country' => 'nullable|exists:countries,id',
        ]);

        // Find the application
        $application = Application::findOrFail($id);

        // Update the basic fields
        $application->company_name = $request->company_name ?? $application->company_name;
        $application->website = $request->website ?? $application->website;
        $application->address = $request->address ?? $application->address;
        $application->postal_code = $request->postal_code ?? $application->postal_code;
        $application->main_product_category = $request->main_product_category ?? $application->main_product_category;
        $application->type_of_business = $request->type_of_business ?? $application->type_of_business;
        $application->sector_id = json_encode($request->sectors); // Save as JSON

        // Exhibition Info
        $application->stall_category = $request->stall_category ?? $application->stall_category;
        $application->interested_sqm = $request->interested_sqm ?? $application->interested_sqm;
        $application->allocated_sqm = $request->allocated_sqm ?? $application->allocated_sqm;
        $application->semi_member = $request->semi_member == 'Yes' ? 1 : 0;
        $application->semi_memberID = $request->semi_memberID ?? $application->semi_memberID;

        // Update Event Contact Person
        if ($request->has('event_contact_name')) {
            $eventContact = $application->eventContact;
            // Split the name by comma to separate name and job title
            // $nameParts = explode(',', $request->event_contact_name);

            // Handle cases where the name might have a space-separated first and last name
            // $fullName = trim($nameParts[0]);
            $nameArray = explode(' ', $request->event_contact_name);
            $eventContact->first_name = array_shift($nameArray); // First name is the first part
            $eventContact->last_name = implode(' ', $nameArray); // Remaining parts as last name

            // Assign first name and last name
            // $eventContact->first_name = isset($nameArray[0]) ? $nameArray[0] : $application->eventContact->first_name;
            // $eventContact->last_name = isset($nameArray[1]) ? $nameArray[1] : $application->eventContact->last_name;

            // Assign job title from the second part after the comma
            $eventContact->job_title = $request->event_contact_design ?? $application->eventContact->job_title;


            $eventContact->email = $request->event_contact_email ?? $application->eventContact->email;
            $eventContact->contact_number = $request->event_contact_mobile ?? $application->eventContact->contact_number;
            $eventContact->save();
        }

        // Update Secondary Event Contact
        if ($request->has('secondary_contact_name')) {
            $secondaryContact = $application->secondaryEventContact;
            // $nameParts = explode(' ', $request->secondary_contact_name);

            // Handle cases where the name might have a space-separated first and last name
            // $fullName = trim($nameParts[0]);
            $nameArray = explode(' ', $request->secondary_contact_name);
            $secondaryContact->first_name = array_shift($nameArray); // First name is the first part
            $secondaryContact->last_name = implode(' ', $nameArray); // Remaining parts as last name

            // Assign first name and last name
            // $secondaryContact->first_name = isset($nameArray[0]) ? $nameArray[0] : $application->secondaryEventContact->first_name;
            // $secondaryContact->last_name = isset($nameArray[1]) ? $nameArray[1] : $application->secondaryEventContact->last_name;

            // Assign job title from the second part after the comma
            $secondaryContact->job_title = $request->secondary_contact_design ?? $application->secondaryEventContact->job_title;
            $secondaryContact->email = $request->secondary_contact_email ?? $application->secondaryEventContact->email;
            $secondaryContact->contact_number = $request->secondary_contact_mobile ?? $application->secondaryEventContact->contact_number;
            $secondaryContact->save();
        }

        // Update Company Details
        $application->gst_compliance = $request->gst_compliance == 'Yes' ? 1 : 0;
        $application->gst_no = $request->gst_no ?? $application->gst_no;
        $application->pan_no = $request->pan_no ?? $application->pan_no;

        // Update Billing Details
        $billingDetails = $application->billingDetail;
        if ($billingDetails) {
            $billingDetails->billing_company = $request->billing_company ?? $billingDetails->billing_company;
            $billingDetails->contact_name = $request->contact_name ?? $billingDetails->contact_name;
            $billingDetails->email = $request->billing_email ?? $billingDetails->email;
            $billingDetails->phone = $request->billing_phone ?? $billingDetails->phone;
            $billingDetails->address = $request->billing_address ?? $billingDetails->address;
            $billingDetails->city_id = $request->billing_city ?? $billingDetails->city_id;
            $billingDetails->state_id = $request->billing_state ?? $billingDetails->state_id;
            $billingDetails->country_id = $request->billing_country ?? $billingDetails->country_id;
            $billingDetails->save();
        } else {
            // Optionally, handle the case where billing details are missing
            Log::error('Billing details not found for application ID: ' . $application->id);
            return redirect()->back()->withErrors(['error' => 'Billing details not found for this application.']);
        }

        // Save the application
        $application->save();

        // Redirect with success message
        return redirect()->back()->with('success', 'Application information updated successfully!');
    }


    //users list
    public function users()
    {
        //check user is logged in or not
        // if (!auth()->check()) {
        //     return redirect('/login');
        // }
        $slug = 'Users List';
        $users = User::all();
        return view('dashboard.users', compact('users', 'slug'));
    }

    //Approving the application with application id and updating application_status to approved
    //calculating the total amount of application and updating the total_amount field in event_contact table
    public function approve_old(Request $request)
    {
        //check user is logged in or not
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect('/login');
        }

        //log the request
        Log::info('Approve Application Request', $request->all());
        //validate the id from request from application model exist or not
        $request->validate([
            'id' => 'required|exists:applications,id',
            'isPavilion' => 'required|boolean',
            'allocateSqm' => 'required|string',
            'stallNumber' => 'required|string',
        ]);

        $allocateSqm = intval($request->allocateSqm);

        //log validated data
        Log::info('Approve Application Request Validated', $request->all());


        $id = $request->input('id');


        $application = Application::find($id);
        $nos = 1;

        //$price = ExhibitorPriceCalculator::calculatePrice($application->allocated_sqm, $application->stall_category, $nos, 0);

        $application->submission_status = 'approved';
        $application->approved_date = now();
        //is_pavilion from request isPavilion
        $application->is_pavilion = $request->isPavilion;
        //allocated_sqm from request allocated_sqm
        $application->allocated_sqm = $allocateSqm;
        $application->stallNumber = $request->stallNumber;
        $application->save();
        $eventContact = EventContact::find($application->event_contact_id);
        //calculatePrice
        $price = ExhibitorPriceCalculator::calculatePrice($allocateSqm, $application->stall_category, $nos, 0);        //create new invoice for the application
        $amount = $price['final_total_price'];
        $processingCharges = $price['processing_charges'];
        $gst = $price['gst'];
        $discount = $price['discount'];
        $actual_price = $price['actual_price'];



        //if application_id with same application_id and type is Stall Booking exist then update the invoice else create new invoice
        $invoice = Invoice::where('application_id', $application->id)
            ->where('type', 'Stall Booking')
            ->first();

        if ($invoice) {
            // Update existing invoice
            $invoice->amount = $amount;
            $invoice->pending_amount = 0;
            $invoice->price = $actual_price;
            $invoice->processing_charges = $processingCharges;
            $invoice->gst = $gst;
            $invoice->total_final_price = $amount;
            $invoice->currency = 'INR';
            $invoice->payment_status = 'unpaid';
            $invoice->payment_due_date = now()->addDays(5);
            $invoice->discount_per = 0;
        } else {
            // Create new invoice
            $invoice = new Invoice();
            $invoice->application_id = $application->id;
            $invoice->type = 'Stall Booking';
            $invoice->amount = $amount;
            $invoice->pending_amount = 0;
            $invoice->price = $actual_price;
            $invoice->processing_charges = $processingCharges;
            $invoice->gst = $gst;
            $invoice->total_final_price = $amount;
            $invoice->currency = 'INR';
            $invoice->payment_status = 'unpaid';
            $invoice->payment_due_date = now()->addDays(5);
            $invoice->discount_per = 0;
            $invoice->application_no = $application->application_id;
            do {
                $randomNumber = mt_rand(10000, 99999);
                $invoiceNo = 'SEC-INV' . $randomNumber;
            } while (Invoice::where('invoice_no', $invoiceNo)->exists());

            $invoice->invoice_no = $invoiceNo;
        }

        $invoice->save();
        $to = $application->eventContact->email;
        $application_id = $application->application_id;

        //send email to applicant with approval
        //send a post request to send email with email_type as submission and to as applicant email
        $recipients = is_array($to) ? $to : [$to];
        $recipients[] = 'manish.sharma@interlinks.in'; // Add default email
        foreach ($recipients as $recipient) {
            Mail::to($recipient)->queue(new InvoiceMail($application_id));
        }

        //return success message with approved application id
        //return json response with success message
        return response()->json(['message' => 'Application Approved and Invoice Generated', 'application_id' => $application->id, 'company_name' => $application->company_name]);
        //send email to applicant with approval

        //send email after approval to billing person with payment link

        //return redirect back with success message with applicant id is approved and invoice is generated with
        return redirect()->back()->with('success', 'Application Approved and Invoice Generated');
    }
    public function approve_v2(Request $request)
    {
        //check user is logged in or not
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect('/login');
        }
        //log the request
        Log::info('Approve Application Request', $request->all());
        //validate the id from request from application model exist or not
        $request->validate([
            'id' => 'required|exists:applications,id',
            'isPavilion' => 'required|boolean',
            'allocateSqm' => 'required|string',
            'stallNumber' => 'required|string',
        ]);

        $allocateSqm = intval($request->allocateSqm);
        $id = $request->input('id');
        $application = Application::find($id);
        $nos = 1;
        $region = $application->region;
        $membershipType = $application->membership_verified == 1 ? 'SEMI' : 'Non-SEMI';
        $boothType = $application->pref_location;
        $stallType = $application->stall_category;

        //define the early bird date and regular date
        $earlyBirdDate = '2025-03-31';
        $regularDate = '2025-04-01';
        $earlyBird = now()->lte(Carbon::parse($earlyBirdDate));

        //if early bird then store value in $earlybird 'Early Bird' : 'Regular';
        $earlyBird = $earlyBird ? 'Early Bird' : 'Regular';
        $currencyType = $application->payment_currency;
        $stallSize = $allocateSqm;

        //dd($membershipType, $boothType, $stallType, $earlyBird, $currencyType);

        //$price = ExhibitorPriceCalculator::calculatePrice($stallSize, $membershipType, $boothType, $stallType, $earlyBird, $currencyType  );        //create new invoice for the application
        $application->submission_status = 'approved';
        $application->approved_date = now();
        //is_pavilion from request isPavilion
        $application->is_pavilion = $request->isPavilion;
        //allocated_sqm from request allocated_sqm
        $application->allocated_sqm = $allocateSqm;
        $application->stallNumber = $request->stallNumber;
        $application->save();
        $eventContact = EventContact::find($application->event_contact_id);
        //calculatePrice
        $price = ExhibitorPriceCalculator::calculatePrice($stallSize, $membershipType, $boothType, $stallType, $earlyBird, $currencyType);
        //create new invoice for the application
        $amount = $price['final_total_price'];
        $processingCharges = $price['processing_charges'];
        $gst = $price['gst'];
        $discount = $price['discount'];
        $actual_price = $price['actual_price'];



        //if application_id with same application_id and type is Stall Booking exist then update the invoice else create new invoice
        $invoice = Invoice::where('application_id', $application->id)
            ->where('type', 'Stall Booking')
            ->first();

        if ($invoice) {
            // Update existing invoice
            $invoice->amount = $amount;
            $invoice->pending_amount = 0;
            $invoice->price = $actual_price;
            $invoice->processing_charges = $processingCharges;
            $invoice->gst = $gst;
            $invoice->total_final_price = $amount;
            $invoice->currency = $currencyType;
            $invoice->payment_status = 'unpaid';
            $invoice->payment_due_date = now()->addDays(5);
            $invoice->discount_per = 0;
        } else {
            // Create new invoice
            $invoice = new Invoice();
            $invoice->application_id = $application->id;
            $invoice->type = 'Stall Booking';
            $invoice->amount = $amount;
            $invoice->pending_amount = 0;
            $invoice->price = $actual_price;
            $invoice->processing_charges = $processingCharges;
            $invoice->gst = $gst;
            $invoice->total_final_price = $amount;
            $invoice->currency = $currencyType;
            $invoice->payment_status = 'unpaid';
            $invoice->payment_due_date = now()->addDays(5);
            $invoice->discount_per = 0;
            $invoice->application_no = $application->application_id;
            do {
                $randomNumber = mt_rand(10000, 99999);
                $invoiceNo = 'SEC-INV-' . $randomNumber;
            } while (Invoice::where('invoice_no', $invoiceNo)->exists());

            $invoice->invoice_no = $invoiceNo;
        }

        $invoice->save();
        $to = $application->billingDetail->email;
        $application_id = $application->application_id;

        //send email to applicant with approval
        //send a post request to send email with email_type as submission and to as applicant email
        $recipients = is_array($to) ? $to : [$to];
        $recipients[] = 'manish.sharma@interlinks.in'; // Add default email
        $recipients[] = 'semiconindia@semi.org'; // Add default email
        Mail::to($recipients[0])->bcc(array_slice($recipients, 1))->send(new InvoiceMail($application_id));

        //return success message with approved application id
        //return json response with success message
        return response()->json(['message' => 'Application Approved and Invoice Generated', 'application_id' => $application->id, 'company_name' => $application->company_name]);
        //send email to applicant with approval

        //send email after approval to billing person with payment link

        //return redirect back with success message with applicant id is approved and invoice is generated with
        return redirect()->back()->with('success', 'Application Approved and Invoice Generated');
    }

    public function approve(Request $request)
    {
        //check user is logged in or not
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect('/login');
        }
        //log the request
        //Log::info('Approve Application Request', $request->all());
        //validate the id from request from application model exist or not
        $request->validate([
            'id' => 'required|exists:applications,id',
            'isPavilion' => 'required|boolean',
            'allocateSqm' => 'required|string',
            'stallNumber' => 'required|string',
            'boothType' => 'required|string',
            'booth_cat' => 'required|string',
        ]);

        $allocateSqm = intval($request->allocateSqm);
        $id = $request->input('id');
        $application = Application::find($id);

        // check if the application is already approved
        if ($application->submission_status == 'approved') {
            return response()->json(['message' => 'Application Already Approved', 'application_id' => $application->id, 'company_name' => $application->company_name]);
        }
        $nos = 1;
        $region = $application->region;
        $membershipType = $application->membership_verified == 1 ? 'SEMI' : 'Non-SEMI';
        $boothType = $request->boothType;
        $stallType = $application->stall_category;
        $booth_cat = $request->booth_cat;



        //define the early bird date and regular date
        //define the early bird date and regular date
        $earlyBirdDate = '2025-03-31';
        $regularDate = '2025-04-01';
        $submissionDate = Carbon::parse($application->submission_date);
        $earlyBird = $submissionDate->lte(Carbon::parse($earlyBirdDate));

        //if early bird then store value in $earlybird 'Early Bird' : 'Regular';
        $earlyBird = $earlyBird ? 'Early Bird' : 'Regular';

        //pass earlyBird as boolean
        $earlyBirdBool = $earlyBird === 'Early Bird';




        $currencyType = $application->billingDetail->country->name != 'India' ? 'EUR' : 'INR';


        // $currencyType = $application->payment_currency;
        $stallSize = $allocateSqm;

        //dd($membershipType, $boothType, $stallType, $earlyBird, $currencyType);

        //$price = ExhibitorPriceCalculator::calculatePrice($stallSize, $membershipType, $boothType, $stallType, $earlyBird, $currencyType  );        //create new invoice for the application


        //calculatePrice
        Log::info('Logging details', [
            'company_name' => $application->company_name,
            'stallSize' => $stallSize,
            'membershipType' => $membershipType,
            'boothType' => $boothType,
            'stallType' => $stallType,
            'earlyBird' => $earlyBirdBool,
            'currencyType' => $currencyType,
        ]);


        $eventContact = EventContact::where('application_id', $application->id)->first();
        $contactName = $eventContact->first_name . ' ' . $eventContact->last_name;
        if ($allocateSqm == 0) {
            $application->submission_status = 'approved';
            $application->approved_date = now();
            $application->save();
            //send email to applicant with approval
            $to = $application->eventContact->email;
            $recipients = is_array($to) ? $to : [$to];
            $recipients[] = 'test.interlinks@gmail.com'; // Add default email
            $recipients[] = 'semiconindia@semi.org'; // Add default email

            $html = "<p>Dear {$application->company_name},</p>
            <p>We are pleased to inform you that your application at the SEMICON India 2025 has been approved.</p>
            <p>Thank you for your interest in participating in SEMICON India 2025. We look forward to your presence at the event.</p>
            <p>Best regards,</p>
            <p>SEMICON India Team</p>";
            $html = <<<HTML
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Application Submitted Successfully</title>
                        <meta charset="UTF-8">
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                background-color: #f4f4f4;
                                margin: 0;
                                padding: 20px;
                            }
                            .email-container {
                                max-width: 600px;
                                margin: 0 auto;
                                background: #ffffff;
                                padding: 20px;
                                border-radius: 8px;
                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                            }
                            .header {
                                text-align: center;
                                border-bottom: 2px solid #007bff;
                                padding-bottom: 10px;
                                margin-bottom: 20px;
                            }
                            .header img {
                                max-width: 150px;
                                display: block;
                                margin: 0 auto 0px;
                            }
                            .header-text {
                                font-size: 15px;
                                font-weight: normal;
                                color: rgb(15, 15, 15);
                                display: block;
                            }
                            .content {
                                font-size: 16px;
                                color: #333;
                                line-height: 1.6;
                            }
                            .footer {
                                text-align: center;
                                margin-top: 20px;
                                font-size: 14px;
                                color: #666;
                            }
                            a {
                                color: #007bff;
                                text-decoration: none;
                            }
                        </style>
                    </head>
                    <body>
                    <div class="email-container">
                        <div class="header">
                            <img src="https://www.mmactiv.in/images/semicon_logo.png" alt="SEMICON India 2025">
                            <span class="header-text">SEMICON India 2025</span>
                        </div>
                        <div class="content">
                            <p>{$contactName},</p>
                            <p><strong>Company Name:</strong> <span style="color: #007bff;">{$application->billingDetail->billing_company}</span></p>
                            <p>Your application has been approved successfully.</p>
                        </div>
                        <div class="footer">
                            <p>Best Regards,</p>
                            <p><strong>SEMICON India 2025</strong></p>
                            <p><a href="https://www.semiconindia.org/">https://www.semiconindia.org/</a></p>
                        </div>
                    </div>
                    </body>
                    </html>
                    HTML;
            try {
                Mail::send([], [], function ($message) use ($recipients, $html) {
                    $message->to($recipients[0])
                        ->bcc(array_slice($recipients, 1))
                        ->subject('SEMICON India 2025 Application Approved')
                        ->html($html);
                });
            } catch (\Exception $e) {
                Log::error('Error sending invoice email', [
                    'error' => $e->getMessage(),
                    'application_id' => $application->application_id
                ]);
            }
            //return success message with approved application id
            //return json response with success message
            return response()->json(['message' => 'Application Approved', 'application_id' => $application->id, 'company_name' => $application->company_name]);
        }
        $application->submission_status = 'approved';
        $application->approved_date = now();
        //is_pavilion from request isPavilion
        $application->is_pavilion = $request->isPavilion;
        //allocated_sqm from request allocated_sqm
        $application->allocated_sqm = $allocateSqm;
        $application->stallNumber = $request->stallNumber;
        $application->pref_location = $request->boothType;
        $application->stall_category = $booth_cat;
        $application->save();

        $stallType = $application->stall_category;
        $boothType = $application->pref_location;
        $membershipType = $application->membership_verified == 1 ? 'SEMI' : 'Non-SEMI';
        $stallSize = $allocateSqm;

        $price = ExhibitorPriceCalculator::calculatePrice($stallSize, $membershipType, $boothType, $stallType, $earlyBirdBool, $currencyType);
        //create new invoice for the application
        $amount = $price['final_total_price'];
        $processingCharges = $price['processing_charges'];
        $gst = $price['gst'];
        $discount = $price['discount'];
        $actual_price = $price['actual_price'];
        //if application_id with same application_id and type is Stall Booking exist then update the invoice else create new invoice
        $invoice = Invoice::where('application_id', $application->id)
            ->where('type', 'Stall Booking')
            ->first();
        if ($invoice) {
            // Update existing invoice
            $invoice->amount = $amount;
            $invoice->pending_amount = 0;
            $invoice->price = $actual_price;
            $invoice->processing_charges = $processingCharges;
            $invoice->gst = $gst;
            $invoice->total_final_price = $amount;
            $invoice->currency = $currencyType;
            $invoice->payment_status = 'unpaid';
            $invoice->payment_due_date = now()->addDays(5);
            $invoice->discount_per = 0;
        } else {
            // Create new invoice
            $invoice = new Invoice();
            $invoice->application_id = $application->id;
            $invoice->type = 'Stall Booking';
            $invoice->amount = $amount;
            $invoice->pending_amount = 0;
            $invoice->price = $actual_price;
            $invoice->processing_charges = $processingCharges;
            $invoice->gst = $gst;
            $invoice->total_final_price = $amount;
            $invoice->currency = $currencyType;
            $invoice->payment_status = 'unpaid';
            $invoice->payment_due_date = now()->addDays(5);
            $invoice->discount_per = 0;
            $invoice->application_no = $application->application_id;
            do {
                $randomNumber = mt_rand(10000, 99999);
                $invoiceNo = 'SEC-INV-' . $randomNumber;
            } while (Invoice::where('invoice_no', $invoiceNo)->exists());

            $invoice->invoice_no = $invoiceNo;
        }

        $invoice->save();
        $to = $application->eventContact->email;
        $application_id = $application->application_id;

        //send email to applicant with approval
        //send a post request to send email with email_type as submission and to as applicant email
        $recipients = is_array($to) ? $to : [$to];
        $recipients[] = 'test.interlinks@gmail.com'; // Add default email
        $recipients[] = 'semiconindia@semi.org'; // Add default email
        try {
            Mail::to($recipients[0])->bcc(array_slice($recipients, 1))->send(new InvoiceMail($application_id));
        } catch (\Exception $e) {
            Log::error('Error sending invoice email', ['error' => $e->getMessage(), 'application_id' => $application_id]);
        }

        //return success message with approved application id
        //return json response with success message
        return response()->json(['message' => 'Application Approved and Invoice Generated for', 'application_id' => $application->id, 'company_name' => $application->company_name]);
        //send email to applicant with approval


        //send email after approval to billing person with payment link

        //return redirect back with success message with applicant id is approved and invoice is generated with
        return redirect()->back()->with('success', 'Application Approved and Invoice Generated');
    }

    public function sponsorship_approve(Request $request)
    {
        //check user is logged in or not
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect('/login');
        }

        //log the request
        Log::info('Approve Sponsor Application Request Validated', $request->all());
        //validate the id from request from application model exist or not
        $request->validate([
            'id' => 'required|exists:applications,id',
            'sponsorship_id' => 'required|exists:sponsorships,id',
        ]);

        //log validated data
        Log::info('Approve Sponsor Application Request Validated', $request->all());



        $id = $request->input('id');


        $application = Application::find($id);
        $nos = 1;

        $price = ExhibitorPriceCalculator::calculatePrice($application->interested_sqm, $application->stall_category, $nos, 0);

        $application->submission_status = 'approved';
        $application->approved_date = now();
        //is_pavilion from request isPavilion
        $application->is_pavilion = $request->isPavilion;
        //allocated_sqm from request allocated_sqm
        $application->allocated_sqm = $request->allocateSqm;
        $application->save();
        $eventContact = EventContact::find($application->event_contact_id);
        //calculatePrice
        $price = ExhibitorPriceCalculator::calculatePrice($application->interested_sqm, $application->stall_category, $nos, 0);        //create new invoice for the application
        $amount = $price['final_total_price'];
        $processingCharges = $price['processing_charges'];
        $gst = $price['gst'];
        $discount = $price['discount'];
        $actual_price = $price['actual_price'];


        //if application_id with same application_id and type is Stall Booking exist then update the invoice else create new invoice


        $invoice = Invoice::where('application_id', $application->id)
            ->where('type', 'Stall Booking')
            ->first();

        if ($invoice) {
            // Update existing invoice
            $invoice->amount = $amount;
            $invoice->pending_amount = 0;
            $invoice->price = $actual_price;
            $invoice->processing_charges = $processingCharges;
            $invoice->gst = $gst;
            $invoice->total_final_price = $amount;
            $invoice->currency = 'INR';
            $invoice->payment_status = 'unpaid';
            $invoice->payment_due_date = now()->addDays(5);
            $invoice->discount_per = 0;
        } else {
            // Create new invoice
            $invoice = new Invoice();
            $invoice->application_id = $application->id;
            $invoice->type = 'Stall Booking';
            $invoice->amount = $amount;
            $invoice->pending_amount = 0;
            $invoice->price = $actual_price;
            $invoice->processing_charges = $processingCharges;
            $invoice->gst = $gst;
            $invoice->total_final_price = $amount;
            $invoice->currency = 'INR';
            $invoice->payment_status = 'unpaid';
            $invoice->payment_due_date = now()->addDays(5);
            $invoice->discount_per = 0;
            $invoice->application_no = $application->application_id;
            do {
                $randomNumber = mt_rand(10000, 99999);
                $invoiceNo = 'SEC-INV' . $randomNumber;
            } while (Invoice::where('invoice_no', $invoiceNo)->exists());

            $invoice->invoice_no = $invoiceNo;
        }

        $invoice->save();

        //return success message with approved application id
        //return json response with success message
        return response()->json(['message' => 'Application Approved and Invoice Generated', 'application_id' => $application->id, 'company_name' => $application->company_name]);
        //send email to applicant with approval

        //send email after approval to billing person with payment link

        //return redirect back with success message with applicant id is approved and invoice is generated with
        return redirect()->back()->with('success', 'Application Approved and Invoice Generated');
    }

    //Reject the application with application id and updating application_status to rejected
    public function reject(Request $request)
    {
        //check user is logged in or not
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect('/login');
        }


        Log::info('Reject Application Request', $request->all());

        $id = $request->input('id');

        $application = Application::find($id);
        $application->submission_status = 'rejected';
        $application->rejection_reason = $request->reason;
        $application->rejected_date = now();
        $application->save();

        //return success message with rejected application id
        return response()->json(['message' => 'Application Rejected', 'application_id' => $application->id, 'company_name' => $application->company_name]);
    }

    public function sponsorship_reject(Request $request)
    {
        //check user is logged in or not
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect('/login');
        }

        $request->validate([
            'id' => 'required|exists:applications,id',
            'sponsorship_id' => 'required|exists:sponsorships,id',
            'reason' => 'string|nullable',
        ]);


        Log::info('Reject Application Request', $request->all());

        $id = $request->input('id');

        $application = Application::find($id);

        $sponsorship = Sponsorship::find($request->sponsorship_id);
        //check if the application is already approved
        if ($sponsorship->status == 'approved') {
            return response()->json(['message' => 'Application Already Approved', 'application_id' => $application->id, 'company_name' => $application->company_name]);
        }
        $sponsorship->status = 'rejected';
        $sponsorship->approval_date = now();
        $sponsorship->save();

        // $application->submission_status = 'rejected';
        $application->rejection_reason = $request->reason;
        $application->rejected_date = now();
        $application->save();

        //return success message with rejected application id
        return response()->json(['message' => 'Application Rejected', 'application_id' => $application->id, 'company_name' => $application->company_name]);
    }


    //sponsor application list
    public function sponsorApplicationList($status = null)
    {
        //check user is logged in or not
        if (!auth()->check()) {
            return redirect('/login');
        }
        $slug = 'Sponsor Application List';
        //check status and query the application with status
        if ($status) {
            if ($status == 'in-progress') {
                $status = 'initiated';
            }
            $slug = $status . ' - Sponsor Application List ';

            $applications = Application::with('eventContact', 'sponsorship')->whereHas('sponsorship', function ($query) use ($status) {
                $query->where('status', $status);
            })->get();
        } else {
            $applications = Application::with('eventContact', 'sponsorship')->whereHas('sponsorship')->get();
        }

        //dd($applications , Application::first()->sponsorship()->count());
        //$applications = Application::with('eventContact')->whereHas('sponsorships')->get();
        return view('dashboard.sponsorship-list', compact('applications', 'slug'));
    }

    //application info by id
    public function applicationView(Request $request)
    {
        //check if the user is logged in or not
        if (!auth()->check()) {
            return redirect('/login');
        }
        $this->__construct();
        //from the auth user take the application id and get the details of the application
        //get the application id from the request
        $applicationId = $request->application_id;
        // dd($applicationId);
        $productCategories = ProductCategory::select('id', 'name')->get();

        //get application details from application model
        $application = Application::where('application_id', $applicationId)->first();
        //if not application return to route dashboard.admin
        if (!$application) {
            return redirect()->route('dashboard.admin');
        }
        $app_id = $application->id;
        //get invoice details from invoice model
        $invoice = Invoice::where('application_id', $applicationId)->first();
        //billing details from billing detail model
        $billingDetails = BillingDetail::where('application_id', $app_id)->first();

        // dd($billingDetails);
        //event contact details from event contact model
        $eventContact = EventContact::where('application_id', $app_id)->first();
        $sectors = Sector::all();

        $countries = Country::all();
        $states = State::all();


        return view('admin.application_preview', compact('application', 'invoice', 'billingDetails', 'eventContact', 'productCategories', 'sectors', 'countries', 'states'));
    }



    //verify the membership of the user with application id and semi_member to 1
    public function verifyMembership(Request $request)
    {
        //check if the user is logged in or not
        if (!auth()->check()) {
            return redirect('/login');
        }
        Log::info('Verify Membership Request', $request->all());

        //validate the request
        $request->validate([
            'application_id' => 'required|exists:applications,application_id',
        ]);

        //get the application id from the request
        $id = $request->input('application_id');

        //get the application details from application model
        $application = Application::where('application_id', $id)->first();
        //set the semi_member to 1
        $application->semi_member = 1;
        $application->membership_verified = 1;
        $application->save();

        //return success message with verified application id
        return response()->json(['message' => 'Membership Verified', 'application_id' => $application->id, 'company_name' => $application->company_name]);
    }

    //unverify the membership of the user with application id and semi_member to 0
    public function unverifyMembership(Request $request)
    {
        //check if the user is logged in or not
        if (!auth()->check()) {
            return redirect('/login');
        }

        //validate the request
        $request->validate([
            'application_id' => 'required|exists:applications,application_id',
        ]);

        //get the application id from the request
        $id = $request->input('application_id');

        //get the application details from application model
        $application = Application::where('application_id', $id)->first();
        //set the semi_member to 0
        $application->membership_verified = 0;
        $application->save();

        //return success message with unverified application id
        return response()->json(['message' => 'Membership Unverified', 'application_id' => $application->id, 'company_name' => $application->company_name]);
    }


    //copy to delete table 
    public function copy(Request $request)
    {
        // Validate the id from the request to ensure it exists in the applications table
        $request->validate([
            'id' => 'required|exists:applications,application_id',
        ]);

        DB::beginTransaction(); // Start a transaction to ensure data integrity



        try {
            $application = Application::where('application_id', $request->id)->firstOrFail();
            // Step 1: Copy and Delete SecondaryEventContact
            $secondaryContacts = SecondaryEventContact::where('application_id', $application->id)->get();
            foreach ($secondaryContacts as $contact) {
                DeletedSecondaryEventContact::create($contact->toArray());
                $contact->delete();
            }

            // Step 2: Copy and Delete EventContact
            $eventContacts = EventContact::where('application_id', $application->id)->get();
            foreach ($eventContacts as $contact) {
                DeletedEventContact::create($contact->toArray());
                $contact->delete();
            }

            // Step 3: Copy and Delete BillingDetail
            $billingDetails = BillingDetail::where('application_id', $application->id)->get();
            foreach ($billingDetails as $billing) {
                DeletedBillingDetail::create($billing->toArray());
                $billing->delete();
            }

            // Step 4: Copy and Delete Application

            DeletedApplication::create($application->toArray());
            $application->delete();

            DB::commit(); // Commit the transaction if all operations succeed

            return response()->json(['message' => 'Application and related data copied and deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction if any error occurs
            Log::error('Error in copy and delete process: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to complete copy and delete process'], 500);
        }
    }

    //send onboarding email to the applicant
    public function sendOnboardingEmail(Request $request)
    {


        //check user is logged in or not
        // if (!auth()->check() || auth()->user()->role !== 'admin') {
        //     return redirect('/login');
        // }
        $applications = Application::where('submission_status', 'approved')
            ->whereHas('invoices', function ($invoiceQuery) {
                $invoiceQuery->where('type', 'Stall Booking')
                    ->whereIn('payment_status', ['paid', 'partial'])
                    ->whereHas('payments', function ($paymentQuery) {
                        $paymentQuery->where('status', 'successful');
                    });
            })
            ->get();
        if ($applications) {
            // loop through the application and get the event contact email, registered user email and billing email
            $emails = [];
            $i = 0;
            foreach ($applications as $application) {
                $i++;
                echo "Processing Application: {$i} - {$application->company_name}<br>";
                // //send onboarding email to the applicant
                // Mail::to($emails)->queue(new OnboardingMail($application));
                $eventContactEmail = $application->eventContact->email;
                $registeredUserEmail = $application->user->email;
                $billingEmail = $application->billingDetail->email;

                $emails = array_unique(array_merge($emails, [$registeredUserEmail]));
                $onboardingEmail = new Onboarding($registeredUserEmail, $application->company_name);

                $company_name = Application::where('id', $application->id)->first()->company_name;



                $exhibitor = $application->company_name; // Assuming you have an Exhibitor model related to the application


                $contact_email = EventContact::where('application_id', $application->id)->first()->email;
                // echo $contact_email;

                // Mail::to($registeredUserEmail)
                //     ->cc([$contact_email])
                //     ->queue($onboardingEmail);

                //render each email to view it 
                echo view('mail.onboarding', ['email' => $registeredUserEmail, 'exhibitor' => $application->company_name])->render();

                echo "<br>";
                echo "<br>";
                // exit;




            }
            exit;
            dd($emails);
        }
    }

    //make a function to send email credentials to the applicant where RegSource = 'Admin' 
    // make this function to send UserCredentialsMail to the applicant
    public function sendUserCredentialsEmail(Request $request)
    {
        //get the application id from the request
        //select all the applcaitiosn where RegSource = 'Admin'
        $applications = Application::where('RegSource', 'Admin')->get();
        //send the email to the applicant
        foreach ($applications as $application) {
            $name = $application->user->name;
            $setupProfileUrl = config('app.url');
            $username = $application->user->email;
            $password = $application->user->simplePass;
            Mail::to($username)->bcc('test.interlinks@gmail.com')->queue(new UserCredentialsMail($name, $setupProfileUrl, $username, $password));
        }
    }

    // Send credentials email to a single user
    public function sendCredentials(Request $request, $userId)
    {
        try {
            // echo $userId;
            // exit;
            $user = User::findOrFail($userId);

            // dd($user);
            
            if (!$user->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'User credentials are not available.'
                ], 400);
            }

            $name = $user->name;
            $setupProfileUrl = config('app.url');
            $username = $user->email;
            $password = $user->simplePass;

            // Send the email
            Mail::to($username)->bcc('test.interlinks@gmail.com')->send(new UserCredentialsMail($name, $setupProfileUrl, $username, $password));

            return response()->json([
                'success' => true,
                'message' => 'Credentials sent successfully to ' . $user->email
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending credentials: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send credentials: ' . $e->getMessage()
            ], 500);
        }
    }
}
