<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceMail;
use App\Models\BillingDetail;
use App\Models\EventContact;
use App\Models\SecondaryEventContact;
use App\Models\ProductCategory;
// use App\Models\Sector;
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
use App\Models\ExhibitorInfo;
use App\Mail\ExhibitorDirectoryReminder;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Sector;
use Illuminate\Support\Facades\Http;




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
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
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
    public function index(Request $request, $status = null)
    {
        //check user is logged in or not
        if (!auth()->check()) {
            return redirect('/login');
        }
        
        // Get application type from request (default to 'exhibitor' for backward compatibility)
        $applicationType = $request->input('type', 'exhibitor');
        $paymentStatus = $request->input('payment_status'); // For filtering paid/unpaid
        $filter = $request->input('filter'); // For Startup Zone filters: approved, approval-pending, paid, approved-not-paid
        
        $slug = 'Application List';
        if ($applicationType === 'startup-zone') {
            $slug = 'Startup Zone - Application List';
        }

        // Build base query
        $query = Application::with('eventContact');
        
        // Filter by application type
        // For 'exhibitor' type, show all non-startup-zone applications (including null and 'exhibitor')
        if ($applicationType === 'startup-zone') {
            $query->where('application_type', 'startup-zone');
        } else {
            // For exhibitor or other types, exclude startup-zone
            $query->where(function($q) {
                $q->where('application_type', '!=', 'startup-zone')
                  ->orWhereNull('application_type')
                  ->orWhere('application_type', 'exhibitor');
            });
        }

        // Handle Startup Zone specific filters
        if ($applicationType === 'startup-zone' && $filter) {
            if ($filter === 'approved') {
                $query->where('submission_status', 'approved');
                $slug = 'Approved - Startup Zone Application List';
            } elseif ($filter === 'approval-pending') {
                $query->where('submission_status', 'submitted');
                $slug = 'Approval Pending - Startup Zone Application List';
            } elseif ($filter === 'paid') {
                $query->whereHas('invoices', function($q) {
                    $q->where('payment_status', 'paid');
                });
                $slug = 'Paid - Startup Zone Application List';
            } elseif ($filter === 'approved-not-paid') {
                $query->where('submission_status', 'approved')
                    ->where(function($q) {
                        $q->whereDoesntHave('invoices')
                          ->orWhereHas('invoices', function($invoiceQuery) {
                              $invoiceQuery->where('payment_status', '!=', 'paid');
                          });
                    });
                $slug = 'Approved but Not Paid - Startup Zone Application List';
            }
        } elseif ($status) {
            // Handle regular status filters
            if ($status == 'in-progress') {
                $status = 'in progress';
            }
            $slug = $status . ' - Application List';
            if ($applicationType === 'startup-zone') {
                $slug = $status . ' - Startup Zone Application List';
            }
            
            $query->where('submission_status', $status);
            
            // Filter by payment status if provided (for paid/unpaid)
            if ($paymentStatus) {
                if ($paymentStatus === 'paid') {
                    $query->whereHas('invoices', function($q) {
                        $q->where('payment_status', 'paid');
                    });
                } elseif ($paymentStatus === 'unpaid') {
                    $query->where(function($q) {
                        $q->whereDoesntHave('invoices')
                          ->orWhereHas('invoices', function($invoiceQuery) {
                              $invoiceQuery->where('payment_status', '!=', 'paid');
                          });
                    });
                }
            }
        }

        // Handle approved status separately for revenue calculation
        if ($status == 'approved') {
            $query = Application::with('eventContact', 'invoice');
            
            // Filter by application type
            if ($applicationType === 'startup-zone') {
                $query->where('application_type', 'startup-zone');
            } else {
                $query->where(function($q) {
                    $q->where('application_type', '!=', 'startup-zone')
                      ->orWhereNull('application_type')
                      ->orWhere('application_type', 'exhibitor');
                });
            }
            
            $query->where('submission_status', 'approved');
            
            $applications = $query->orderBy('submission_date', 'desc')->get();
            
            //total revenue from all approved applications from price field in invoice table
            $invoiceType = $applicationType === 'startup-zone' ? 'Startup Zone Registration' : 'Stall Booking';
            $totalRevenue = Invoice::where('type', $invoiceType)
                            ->whereIn('payment_status', ['paid'])
                            ->sum('total_final_price');

            return view('dashboard.approved_list', compact('applications', 'slug', 'totalRevenue'));
        }

        // Get applications for all other cases
        $applications = $query->orderBy('submission_date', 'desc')->get();

        return view('dashboard.list', compact('applications', 'slug'));
    }

    /**
     * Approve startup zone application
     */
    public function approveStartupZone(Request $request, $id)
    {
        //check user is logged in or not
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
            return redirect('/login');
        }

        $application = Application::find($id);
        
        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        // Check if it's a startup zone or exhibitor registration application
        if (!in_array($application->application_type, ['startup-zone', 'exhibitor-registration'])) {
            return response()->json(['message' => 'This method is only for startup zone and exhibitor registration applications'], 400);
        }

        // Check if already approved
        if ($application->submission_status === 'approved') {
            return response()->json(['message' => 'Application already approved', 'application_id' => $application->id, 'company_name' => $application->company_name]);
        }

        // Use direct DB update to ensure the change persists
        try {
            $updateResult = \DB::table('applications')
                ->where('id', $id)
                ->update([
                    'submission_status' => 'approved',
                    'approved_date' => now(),
                    'approved_by' => auth()->id(),
                    'updated_at' => now()
                ]);
            
            // Refresh the model to get updated values
            $application = Application::find($id);
            
            \Log::info('Application Approved via DB Update', [
                'application_id' => $application->application_id,
                'application_type' => $application->application_type,
                'company_name' => $application->company_name,
                'approved_by' => auth()->id(),
                'update_result' => $updateResult,
                'final_submission_status' => $application->submission_status,
                'approved_date' => $application->approved_date
            ]);
            
            // Verify the update actually happened
            if ($application->submission_status !== 'approved') {
                \Log::error('CRITICAL: Application status still not approved after DB update', [
                    'application_id' => $application->application_id,
                    'id' => $id,
                    'current_status' => $application->submission_status,
                    'update_result' => $updateResult
                ]);
                
                return response()->json([
                    'message' => 'Failed to update application status',
                    'error' => 'Database update did not persist',
                    'debug' => [
                        'id' => $id,
                        'current_status' => $application->submission_status
                    ]
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Exception during application approval', [
                'application_id' => $application->application_id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Error updating application status',
                'error' => $e->getMessage()
            ], 500);
        }

        // Send approval email to user with payment link
        try {
            $invoice = \App\Models\Invoice::where('application_id', $application->id)->first();
            $contact = \App\Models\EventContact::where('application_id', $application->id)->first();
            
            if ($invoice && $contact) {
                // Reload application with relationships for email
                $application->load(['country', 'state', 'eventContact']);
                
                // Get user email
                $userEmail = $contact->email ?? $application->company_email;

                // Get BCC emails from config
                $bccEmails = config('constants.admin_emails.bcc', []);
                
                if ($userEmail) {
                    // Send appropriate email based on application type
                    if ($application->application_type === 'exhibitor-registration') {
                        $mail = Mail::to($userEmail);
                        if (!empty($bccEmails)) {
                            $mail->bcc($bccEmails);
                        }
                        $mail->send(new \App\Mail\ExhibitorRegistrationMail($application, $invoice, $contact, 'approval'));
                    } else {
                        $mail = Mail::to($userEmail);
                        if (!empty($bccEmails)) {
                            $mail->bcc($bccEmails);
                        }
                        $mail->send(new \App\Mail\StartupZoneMail($application, 'approval', $invoice, $contact));
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send approval email to user', [
                'application_id' => $application->application_id,
                'application_type' => $application->application_type,
                'email' => $userEmail ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            // Don't fail the approval if email fails
        }

        return response()->json([
            'message' => 'Application approved successfully',
            'application_id' => $application->id,
            'company_name' => $application->company_name
        ]);
    }

    public function applicationUpdate(Request $request, $id)
    {

        // dd($request->all());
        // Validate the incoming request data
        $request->validate([
            'company_name' => 'required|string|max:255',
            'website' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            // 'main_product_category' => 'required|exists:product_categories,id',
            // 'type_of_business' => 'nullable|string|max:255',
            'sectors' => 'nullable|array',
            'sectors.*' => 'exists:sectors,id',
            'stall_category' => 'nullable|string|max:255',
            'interested_sqm' => 'nullable|integer',
            'allocated_sqm' => 'nullable|string|max:255',
            // 'semi_member' => 'nullable|string',
            // 'semi_memberID' => 'nullable|string|max:100',
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
            'billing_email' => 'nullable|string',
            'billing_phone' => 'nullable|string|max:20',
            'billing_address' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:255',
            'billing_state' => 'nullable|string|max:255',
            'billing_country' => 'nullable|string|max:255',
        ]);

        // Find the application
        $application = Application::findOrFail($id);

        // log in json file whatever the old data is and who has updated the data
        // In case of version control, use a proper audit log or database mechanism instead of raw file writes.
        // For demonstration, we'll append logs to a versioned log file with timestamps and user info.
        $userName = auth()->user()->name ?? 'system';
        $time = now()->toDateTimeString();

        $oldData = $application->toArray();
        $oldData['updated_by'] = $userName;
        $oldData['updated_at'] = $time;

        // Create a versioned history file per application
        $oldDataLogFile = storage_path('logs/application_versions_' . $application->id . '.jsonl');
        file_put_contents($oldDataLogFile, json_encode($oldData) . PHP_EOL, FILE_APPEND);

        $requestData = $request->all();
        $requestData['updated_by'] = $userName;
        $requestData['updated_at'] = $time;
        $requestDataLogFile = storage_path('logs/application_request_versions_' . $application->id . '.jsonl');
        file_put_contents($requestDataLogFile, json_encode($requestData) . PHP_EOL, FILE_APPEND);



        // Update the basic fields
        $application->company_name = $request->company_name ?? $application->company_name;
        $application->website = $request->website ?? $application->website ?? '';
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

        if ($application->eventContact->email != $request->event_contact_email) {
            $user = User::find($application->user_id);
            $user->email = $request->event_contact_email;
            $user->save();
            // echo "User email updated successfully";
            // exit;
        }

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
            $billingDetails->billing_company = $request->billing_company ?? $billingDetails->billing_company ?? '';
            $billingDetails->contact_name = $request->contact_name ?? $billingDetails->contact_name ?? '';
            $billingDetails->email = $request->billing_email ?? $billingDetails->email ?? '';
            $billingDetails->phone = $request->billing_phone ?? $billingDetails->phone ?? '';
            $billingDetails->address = $request->billing_address ?? $billingDetails->address ?? '';
            $billingDetails->city_id = $request->billing_city ?? $billingDetails->city_id ?? '';
            $billingDetails->state_id = $request->billing_state ?? $billingDetails->state_id ?? '';
            $billingDetails->country_id = $request->billing_country ?? $billingDetails->country_id ?? '';
            $billingDetails->save();
        }
        //  else {
        //     // Optionally, handle the case where billing details are missing
        //     Log::error('Billing details not found for application ID: ' . $application->id);
        //     return redirect()->back()->withErrors(['error' => 'Billing details not found for this application.']);
        // }

        // Save the application
        $application->save();

        // incase of change in contactperson email change update the user email to new email
        

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
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
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
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
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
        $recipients[] = ''; // Add default email
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
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
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
            $recipients[] = ORGANIZER_EMAIL; // Add default email

            $html = "<p>Dear {$application->company_name},</p>
            <p>We are pleased to inform you that your application at the ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' has been approved.</p>
            <p>Thank you for your interest in participating in ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . '. We look forward to your presence at the event.</p>
            <p>Best regards,</p>
            <p>' . config('constants.EVENT_NAME') . ' Team</p>";
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
                            <img src="https://www.mmactiv.in/images/semicon_logo.png" alt="' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . '">
                            <span class="header-text">' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . '</span>
                        </div>
                        <div class="content">
                            <p>{$contactName},</p>
                            <p><strong>Company Name:</strong> <span style="color: #007bff;">{$application->billingDetail->billing_company}</span></p>
                            <p>Your application has been approved successfully.</p>
                        </div>
                        <div class="footer">
                            <p>Best Regards,</p>
                            <p><strong>' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . '</strong></p>
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
                        ->subject(config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' Application Approved')
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
        $recipients[] = ORGANIZER_EMAIL; // Add default email
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
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
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
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
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
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
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

        //get application details from application model with user relationship
        $application = Application::with('user')->where('application_id', $applicationId)->first();
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

        // echo "Sending email credentials to the applicants";
        // exit;
        //get the application id from the request
        //select all the applcaitiosn where RegSource = 'Admin'
        $applications = Application::all();
        //dd($applications);
        // dd($applications);
        //send the email to the applicant
        foreach ($applications as $application) {
            $name = $application->user->name;
            //todo: change the url to the new url
            $setupProfileUrl = config('constants.APP_URL');
            $username = $application->user->email;
            $password = $application->user->simplePass;

            //if usernme talvinder.singh@zop.dev skip that email 
            // if ($username == 'talvinder.singh@zop.dev') {
            //     continue;
            // }
            // echo $name . " - " . $username . " - " . $password . "<br>";
            // echo view('emails.credentials', ['setupProfileUrl' => $setupProfileUrl, 'email' => $username, 'name' => $name, 'password' => $password])->render();
            // exit;
            // exit;
            // echo $username;
            // echo $name;
            // echo $setupProfileUrl;
            // echo $password;
            // exit;
            // echo view('emails.credentials', ['name' => $name, 'setupProfileUrl' => $setupProfileUrl, 'username' => $username, 'password' => $password])->render();
            // exit;
            try {
                //render the email to view it
                // echo view('emails.credentials', ['setupProfileUrl' => $setupProfileUrl, 'email' => $username, 'name' => $name, 'password' => $password])->render();
                // exit;
                Mail::to($username)->bcc('test.interlinks@gmail.com')->send(new UserCredentialsMail($name, $setupProfileUrl, $username, $password));
            } catch (\Exception $e) {
                echo "Error sending email to " . $username . ": " . $e->getMessage() . "<br>";
                exit;
            }
            // echo "Email sent to " . $username . "<br>";
            // exit;
        }
        echo "All emails sent successfully";
        exit;
    }


    
    public function sendAllData()
    {
        // $middlewareResponse = $this->adminMiddleware();
        // if ($middlewareResponse) {
        //     return $middlewareResponse;
        // }
        $exhibitorInfo = ExhibitorInfo::where('submission_status', 1)
            ->where(function($query) {
                $query->whereNull('api_status')->orWhere('api_status', 0);
            })
            // ->limit(1)
            ->get();

            // dd($exhibitorInfo);
        foreach ($exhibitorInfo as $exhibitor) {
             // Build payload for external API
        $companyName = $exhibitor->company_name ?? '';
        $about = $exhibitor->description ?? '';
        $website = $exhibitor->website ?? '';
        $fasciaName = $exhibitor->fascia_name ?? '';
        $contactName = $exhibitor->contact_person ?? '';

        // derive country code and mobile from phone using format "+CC-NUMBER"
        $countryCode = '';
        $mobile = '';
        if (!empty($exhibitor->phone) && strpos($exhibitor->phone, '+') === 0) {
            $parts = explode('-', $exhibitor->phone, 2);
            if (count($parts) === 2) {
                $countryCode = preg_replace('/[^\d]/', '', $parts[0]);
                $mobile = preg_replace('/[^\d]/', '', $parts[1]);
            }
        }

        // contact mobile (display) fallback to telPhone in same parsing style
        $contactMobile = '';
        if (!empty($exhibitor->telPhone) && strpos($exhibitor->telPhone, '+') === 0) {
            $tparts = explode('-', $exhibitor->telPhone, 2);
            if (count($tparts) === 2) {
                $contactCountryCode = preg_replace('/[^\d+]/', '', $tparts[0]);
                $contactNumber = preg_replace('/[^\d]/', '', $tparts[1]);
                $contactMobile = trim($contactCountryCode . ' ' . $contactNumber);
            }
        }
        if ($contactMobile === '' && $countryCode !== '' && $mobile !== '') {
            // build display from main phone if no telPhone provided
            $contactMobile = '+' . $countryCode . ' ' . $mobile;
        }

        // remove space from the contactMobile
        $contactMobile = str_replace(' ', '', $contactMobile);

        // photo: send only the file name (API builds path automatically)
        $photo = '';
        if (!empty($exhibitor->logo)) {
            $photo = basename($exhibitor->logo);
        }

        // optional custom variables
        $var1 = $exhibitor->sector ?? '';
        $var2 = $exhibitor->category ?? 'Startup';

        //BizExpress Advisors Pvt Ltd 
        //there is nbsp between the word handle it correctly 
        // the like [NB] like this should be removed
        $companyName = str_replace(["\u{00A0}", '&nbsp;'], ' ', $companyName);
        $companyName = trim($companyName);

        $about = str_replace(["\r\n", "\n", "\r"], ' ', $about);
        $about = trim($about);

        $payload = [
            'api_key' => 'scan626246ff10216s477754768osk',
            'event_id' => '118150',
            'company_name' => $companyName,
            'about' => $about,
            'email' => $exhibitor->email,
            'country_code' => $countryCode,
            'mobile' => $mobile,
            'website' => $website,
            'contact_mobile' => $contactMobile,
            'contact_email' => $exhibitor->email ?? '',
            'contact_name' => $contactName,
            'photo' => $photo,
            'fascia_name' => $fasciaName,
            'var_1' => $var1,
            'var_2' => $var2,
        ];

        // dd($payload);
            $apiResult = $this->sendExhibitorData($payload);

            $successFlag = false;
        if (isset($apiResult['response']) && is_array($apiResult['response']) && isset($apiResult['response']['status'])) {
            $successFlag = (string)$apiResult['response']['status'] === '1';
        } else if (!empty($apiResult['success'])) {
            $successFlag = true;
        }

        $exhibitor->api_status = $successFlag ? 1 : 0;

        $message = '';
        if (isset($apiResult['response']) && is_array($apiResult['response'])) {
            $message = json_encode($apiResult['response']);
        } else if (isset($apiResult['raw_response'])) {
            $message = (string)$apiResult['raw_response'];
        } else if (isset($apiResult['error'])) {
            $message = (string)$apiResult['error'];
        }

        // Safely append API message
        $existingMessage = (string)($exhibitor->api_message ?? '');
        $exhibitor->api_message = trim($existingMessage . ' ' . $message);
        $exhibitor->save();
        echo "sent data to chkdin for " . $exhibitor->company_name . " with api_status " . $exhibitor->api_status . " and api_message " . $exhibitor->api_message . "<br>";
        }
    }


    private function sendExhibitorData(array $data): array
    {
        $url = 'https://studio.chkdin.com/api/v1/push_exhibitor';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
            'User-Agent: PHP-Exhibitor-API-Client/1.0'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'error' => 'cURL Error: ' . $error,
                'http_code' => $httpCode
            ];
        }

        $responseData = json_decode($response, true);

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'response' => $responseData ?: $response,
            'raw_response' => $response
        ];
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
            $password = (!empty($user->simplePass)) ? $user->simplePass : 'Password not available';

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

    /**
     * Display booth management page with all applications
     */
    public function boothManagement(Request $request)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
            return redirect('/login');
        }

        $slug = 'Booth Management';

        // Query to get applications with booth numbers or approved applications
        $query = Application::with(['user'])
            ->where(function($q) {
                $q->whereNotNull('stallNumber')
                  ->orWhere('submission_status', 'approved');
            });

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('stallNumber', 'like', "%{$search}%")
                  ->orWhere('application_id', 'like', "%{$search}%");
            });
        }

        // Filter by zone
        if ($request->has('zone') && !empty($request->zone)) {
            $query->where('zone', $request->zone);
        }

        $query->orderBy('company_name', 'asc');

        $perPage = $request->get('per_page', 25);
        $applications = $query->paginate($perPage);
        $applications->appends($request->query());

        // Get unique zones for filter
        $zones = Application::whereNotNull('zone')
            ->distinct()
            ->pluck('zone')
            ->filter()
            ->sort();

        return view('admin.booth_management', compact('applications', 'slug', 'zones'));
    }

    /**
     * Export exhibitors who have not filled the exhibitor directory
     * Includes company info and primary contact details
     */
    public function exportMissingExhibitorDirectory(Request $request)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
            return redirect('/login');
        }

        $rows = DB::table('applications as a')
            ->leftJoin('exhibitors_info as ei', 'ei.application_id', '=', 'a.id')
            ->leftJoin('event_contacts as ec', 'ec.application_id', '=', 'a.id')
            ->select(
                'a.application_id',
                'a.company_name',
                'a.company_email',
                'a.stallNumber',
                'a.zone',
                'a.hallNo',
                DB::raw("TRIM(CONCAT(COALESCE(ec.first_name,''), ' ', COALESCE(ec.last_name,''))) as contact_person"),
                'ec.job_title',
                'ec.email as contact_email',
                'ec.contact_number as contact_number'
            )
            ->where('a.submission_status', 'approved')
            ->where(function ($q) {
                $q->whereNull('ei.id')->orWhere('ei.submission_status', 0);
            })
            ->orderBy('a.company_name', 'asc')
            ->get();

        $filename = 'missing_exhibitor_directory_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new class($rows) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $rows;
            public function __construct($rows) { $this->rows = $rows; }
            public function array(): array {
                $data = [];
                foreach ($this->rows as $r) {
                    $data[] = [
                        $r->application_id ?? 'N/A',
                        $r->company_name ?? 'N/A',
                        $r->company_email ?? 'N/A',
                        $r->stallNumber ?? 'N/A',
                        $r->zone ?? 'N/A',
                        $r->hallNo ?? 'N/A',
                        $r->contact_person ?: 'N/A',
                        $r->job_title ?? 'N/A',
                        $r->contact_email ?? 'N/A',
                        $r->contact_number ?? 'N/A',
                    ];
                }
                return $data;
            }
            public function headings(): array {
                return [
                    'Application ID',
                    'Company Name',
                    'Company Email',
                    'Booth Number',
                    'Zone',
                    'Hall No',
                    'Contact Person',
                    'Job Title',
                    'Contact Email',
                    'Contact Number',
                ];
            }
        }, $filename);
    }

    /**
     * Export all exhibitors with booth fields for bulk update template
     */
    public function exportBoothTemplate(Request $request)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
            return redirect('/login');
        }

        $apps = Application::where('submission_status', 'approved')
            ->select(['id', 'application_id', 'company_name', 'tag', 'stallNumber', 'zone', 'hallNo', 'sector_id', 'stall_category', 'allocated_sqm'])
            ->orderBy('company_name', 'asc')
            ->get();

        // Collect all sector ids used in sector_id (json or scalar)
        $allSectorIds = [];
        foreach ($apps as $app) {
            if (!empty($app->sector_id)) {
                if (is_string($app->sector_id)) {
                    $decoded = json_decode($app->sector_id, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $allSectorIds = array_merge($allSectorIds, $decoded);
                    } elseif (is_numeric($app->sector_id)) {
                        $allSectorIds[] = (int)$app->sector_id;
                    }
                } elseif (is_numeric($app->sector_id)) {
                    $allSectorIds[] = (int)$app->sector_id;
                }
            }
        }
        $allSectorIds = array_values(array_unique(array_filter($allSectorIds, fn($v) => !is_null($v))));
        $sectorMap = [];
        if (!empty($allSectorIds)) {
            $sectorMap = Sector::whereIn('id', $allSectorIds)->pluck('name', 'id')->toArray();
        }

        $rows = [];
        foreach ($apps as $app) {
            // Build sector display from sector_id
            $sectorNames = [];
            if (!empty($app->sector_id)) {
                if (is_string($app->sector_id)) {
                    $decoded = json_decode($app->sector_id, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        foreach ($decoded as $sid) {
                            if (isset($sectorMap[$sid])) {
                                $sectorNames[] = $sectorMap[$sid];
                            }
                        }
                    } elseif (is_numeric($app->sector_id)) {
                        $sid = (int)$app->sector_id;
                        if (isset($sectorMap[$sid])) {
                            $sectorNames[] = $sectorMap[$sid];
                        }
                    }
                } elseif (is_numeric($app->sector_id)) {
                    $sid = (int)$app->sector_id;
                    if (isset($sectorMap[$sid])) {
                        $sectorNames[] = $sectorMap[$sid];
                    }
                }
            }
            $sectorDisplay = !empty($sectorNames) ? implode(', ', $sectorNames) : 'N/A';

            $rows[] = [
                $app->application_id,
                $app->company_name,
                $app->tag ?? 'N/A',
                $app->stallNumber,
                $app->zone,
                $app->hallNo,
                $sectorDisplay,
                $app->stall_category,
                $app->allocated_sqm,
            ];
        }

        $filename = 'booth_bulk_sample_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new class($rows) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $rows;
            public function __construct($rows) { $this->rows = $rows; }
            public function array(): array { return $this->rows; }
            public function headings(): array {
                return [
                    'Application ID',
                    'Company Name',
                    'Association Name',
                    'Booth Number',
                    'Zone',
                    'Hall No',
                    'Sector',
                    'Stall Category',
                    'Allocated SQM',
                ];
            }
        }, $filename);
    }

    /**
     * Import booth updates from uploaded Excel
     */
    public function importBoothUpdates(Request $request)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
            return redirect('/login');
        }

        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv,xls',
        ]);

        $sheets = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
            public function array(array $array)
            {
                return $array;
            }
        }, $request->file('file'));
        if (empty($sheets) || empty($sheets[0])) {
            return redirect()->back()->with('error', 'The uploaded file is empty or invalid.');
        }
        $rows = $sheets[0];
        if (count($rows) < 2) {
            return redirect()->back()->with('error', 'No data rows found in the uploaded file.');
        }

        // Map headers
        $header = array_map(fn($h) => strtolower(trim((string)$h)), $rows[0]);
        $findIdx = function(string $name) use ($header) {
            $name = strtolower($name);
            foreach ($header as $i => $h) {
                if ($h === $name) return $i;
            }
            return -1;
        };
        $idxAppId = $findIdx('application id');
        $idxBooth = $findIdx('booth number');
        $idxZone = $findIdx('zone');
        $idxHall = $findIdx('hall no');

        if ($idxAppId === -1) {
            return redirect()->back()->with('error', 'Missing "Application ID" column in the uploaded file.');
        }

        $updated = 0;
        $skipped = 0;
        $errors = [];

        // Process each row
        for ($i = 1; $i < count($rows); $i++) {
            $r = $rows[$i];
            // Normalize row length to header length
            $r = array_pad($r, count($header), null);
            $applicationId = trim((string)($r[$idxAppId] ?? ''));
            if ($applicationId === '') { $skipped++; continue; }

            $application = Application::where('application_id', $applicationId)->first();
            if (!$application) {
                $errors[] = "Row ".($i+1).": Application ID {$applicationId} not found";
                $skipped++;
                continue;
            }

            $booth = $idxBooth !== -1 ? trim((string)($r[$idxBooth] ?? '')) : null;
            $zone = $idxZone !== -1 ? trim((string)($r[$idxZone] ?? '')) : null;
            $hall = $idxHall !== -1 ? trim((string)($r[$idxHall] ?? '')) : null;

            $changed = false;
            $before = [
                'stallNumber' => $application->stallNumber,
                'zone' => $application->zone,
                'hallNo' => $application->hallNo,
            ];
            if ($booth !== null && $booth !== '') { $application->stallNumber = $booth; $changed = true; }
            if ($zone !== null && $zone !== '') { $application->zone = $zone; $changed = true; }
            if ($hall !== null && $hall !== '') { $application->hallNo = $hall; $changed = true; }

            if ($changed) {
                try {
                    $application->save();
                    $after = [
                        'stallNumber' => $application->stallNumber,
                        'zone' => $application->zone,
                        'hallNo' => $application->hallNo,
                    ];
                    $this->logBoothUpdate($application, $before, $after, 'import', $i + 1);
                    $updated++;
                } catch (\Exception $e) {
                    $errors[] = "Row ".($i+1).": Failed to update {$applicationId} - " . $e->getMessage();
                    $skipped++;
                }
            } else {
                $skipped++;
            }
        }

        $message = "Booth updates processed. Updated: {$updated}, Skipped: {$skipped}.";
        if (!empty($errors)) {
            $message .= " Errors: " . count($errors);
            return redirect()->back()->with('success', $message)->with('import_errors', $errors);
        }
        return redirect()->back()->with('success', $message);
    }

    /**
     * Export Fascia Details for exhibitors (Booth Management)
     * Headers: Exhibitor Name, Fascia Name, Booth Number, Hall Number, Contact Person and Details
     * Fascia falls back to application.fascia_name if exhibitor_info.fascia_name is missing
     */
    public function exportFasciaDetails(Request $request)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
            return redirect('/login');
        }

        $rows = DB::table('applications as a')
            ->leftJoin('exhibitors_info as ei', 'ei.application_id', '=', 'a.id')
            ->leftJoin('event_contacts as ec', 'ec.application_id', '=', 'a.id')
            ->select(
                'a.company_name as exhibitor_name',
                DB::raw('COALESCE(ei.fascia_name, a.fascia_name) as fascia_name'),
                'a.stallNumber as booth_number',
                'a.hallNo as hall_number',
                'ec.first_name',
                'ec.last_name',
                'ec.job_title',
                'ec.email',
                'ec.contact_number'
            )
            //where fascia name is not null
            ->where('ei.fascia_name', '!=', null)
            
            ->orderBy('a.company_name', 'asc')
            ->get();

        $arrayRows = [];
        foreach ($rows as $r) {
            // Exclude rows where fascia_name is null
            if (is_null($r->fascia_name)) {
                continue;
            }
            $contactName = trim(((string)$r->first_name) . ' ' . ((string)$r->last_name));
            $parts = array_filter([
                $contactName !== '' ? $contactName : null,
                $r->job_title ?: null,
                $r->email ?: null,
                $r->contact_number ?: null,
            ]);
            $contactDisplay = implode(' | ', $parts);

            $arrayRows[] = [
                $r->exhibitor_name ?? 'N/A',
                $r->fascia_name ?? 'N/A',
                $r->booth_number ?? 'N/A',
                $r->hall_number ?? 'N/A',
                $contactDisplay ?: 'N/A',
            ];
        }

        $filename = 'fascia_details_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new class($arrayRows) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            protected $rows;
            public function __construct($rows) { $this->rows = $rows; }
            public function array(): array { return $this->rows; }
            public function headings(): array {
                return [
                    'Exhibitor Name',
                    'Fascia Name',
                    'Booth Number',
                    'Hall Number',
                    'Contact Person and Details',
                ];
            }
        }, $filename);
    }

    /**
     * Append a booth update log (before vs after) to a JSONL file
     */
    private function logBoothUpdate(Application $application, array $before, array $after, string $source, ?int $rowNumber = null): void
    {
        try {
            $changedKeys = [];
            foreach (['stallNumber', 'zone', 'hallNo'] as $key) {
                if (($before[$key] ?? null) !== ($after[$key] ?? null)) {
                    $changedKeys[] = $key;
                }
            }
            if (empty($changedKeys)) {
                return;
            }
            $entry = [
                'timestamp' => now()->toDateTimeString(),
                'logged_by' => auth()->id(),
                'ip' => request()->ip(),
                'source' => $source, // single | bulk | import
                'row_number' => $rowNumber,
                'application' => [
                    'id' => $application->id,
                    'application_id' => $application->application_id,
                    'company_name' => $application->company_name,
                ],
                'changed_fields' => $changedKeys,
                'before' => array_intersect_key($before, array_flip($changedKeys)),
                'after' => array_intersect_key($after, array_flip($changedKeys)),
            ];
            $logFile = storage_path('logs/booth_updates.jsonl');
            file_put_contents($logFile, json_encode($entry, JSON_UNESCAPED_SLASHES) . PHP_EOL, FILE_APPEND);
        } catch (\Throwable $e) {
            // Do not interrupt main flow on logging failure
        }
    }
    /**
     * Update a single booth number
     */
    public function updateBooth(Request $request, $id)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'stallNumber' => 'nullable|string|max:255',
            'zone' => 'nullable|string|max:255',
            'hallNo' => 'nullable|string|max:255',
        ]);

        $application = Application::findOrFail($id);
        $before = [
            'stallNumber' => $application->stallNumber,
            'zone' => $application->zone,
            'hallNo' => $application->hallNo,
        ];

        if ($request->has('stallNumber')) {
            $application->stallNumber = $request->stallNumber;
        }

        if ($request->has('zone')) {
            $application->zone = $request->zone;
        }

        if ($request->has('hallNo')) {
            $application->hallNo = $request->hallNo;
        }

        $application->save();
        $after = [
            'stallNumber' => $application->stallNumber,
            'zone' => $application->zone,
            'hallNo' => $application->hallNo,
        ];
        $this->logBoothUpdate($application, $before, $after, 'single');

        return response()->json([
            'success' => true,
            'message' => 'Booth details updated successfully',
            'application' => [
                'id' => $application->id,
                'company_name' => $application->company_name,
                'stallNumber' => $application->stallNumber,
                'zone' => $application->zone,
                'hallNo' => $application->hallNo,
            ]
        ]);
    }

    /**
     * Bulk update booth numbers
     */
    public function bulkUpdateBooths(Request $request)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'updates' => 'required|array',
            'updates.*.id' => 'required|exists:applications,id',
            'updates.*.stallNumber' => 'nullable|string|max:255',
            'updates.*.zone' => 'nullable|string|max:255',
            'updates.*.hallNo' => 'nullable|string|max:255',
        ]);

        $updatedCount = 0;
        $errors = [];

        foreach ($request->updates as $update) {
            try {
                $application = Application::find($update['id']);
                
                $before = [
                    'stallNumber' => $application->stallNumber,
                    'zone' => $application->zone,
                    'hallNo' => $application->hallNo,
                ];
                $changed = false;

                if (isset($update['stallNumber'])) {
                    $application->stallNumber = $update['stallNumber'];
                    $changed = true;
                }

                if (isset($update['zone'])) {
                    $application->zone = $update['zone'];
                    $changed = true;
                }

                if (isset($update['hallNo'])) {
                    $application->hallNo = $update['hallNo'];
                    $changed = true;
                }

                if ($changed) {
                    $application->save();
                    $after = [
                        'stallNumber' => $application->stallNumber,
                        'zone' => $application->zone,
                        'hallNo' => $application->hallNo,
                    ];
                    $this->logBoothUpdate($application, $before, $after, 'bulk');
                    $updatedCount++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to update application ID {$update['id']}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully updated {$updatedCount} booth(s)",
            'updated_count' => $updatedCount,
            'errors' => $errors
        ]);
    }

    public function sendDirectoryReminder()
    {
        // Send reminder emails also when ExhibitorInfo record is not found or submission_status = 0

        try {
            // Get all approved applications with their users
            $applications = Application::where('submission_status', 'approved')
                ->with('user')
                ->get();

            // Get all ExhibitorInfo records for reference by application_id
            $exhibitorInfos = \App\Models\ExhibitorInfo::all()->keyBy('application_id');


            // dd($exhibitorInfos);

            $sentCount = 0;
            $skippedCount = 0;
            $errors = [];

            foreach ($applications as $application) {
                //dd($application);
                // Skip if user or email doesn't exist
                if (!$application->user || !$application->user->email) {
                    $skippedCount++;
                    continue;
                }

                // Check if exhibitorInfo is missing or submission_status = 0
                $exhibitorInfo = $exhibitorInfos->get($application->id);

                if (!$exhibitorInfo || (isset($exhibitorInfo->submission_status) && $exhibitorInfo->submission_status == 0)) {

                    // dd($application);
                    // Send reminder
                    $user = $application->user;
                    $loginEmail = $user->email;
                    $loginPassword = !empty($user->simplePass) ? $user->simplePass : 'Password not available. Please use Forgot Password.';

                    try {
                        $loginEmail = 'manishksharma9801@gmail.com';
                        Mail::to($loginEmail)
                        ->bcc('test.interlinks@gmail.com')
                            ->send(new \App\Mail\ExhibitorDirectoryReminder(
                                $loginEmail,
                                $loginPassword,
                                route('login'),
                                route('forgot.password')
                            ));
                        $sentCount++;
                    } catch (\Exception $mailException) {
                        Log::error('Failed sending directory reminder to ' . $loginEmail . ': ' . $mailException->getMessage());
                        $errors[] = "Failed sending to $loginEmail: " . $mailException->getMessage();
                    }
                    // dd('sent');
                } else {
                    // Already filled and submitted, skip
                    $skippedCount++;
                }
                echo "sent to " . $loginEmail . "<br>";
                exit;
            }

            return response()->json([
                'success' => true,
                'message' => "Directory reminder emails sent: $sentCount (skipped: $skippedCount)",
                'sent_count' => $sentCount,
                'skipped_count' => $skippedCount,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            Log::error('Error in sendDirectoryReminder: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to process directory reminders: ' . $e->getMessage()
            ], 500);
        }
    }

    //make a new route to test the email sending like emailtest.php
    public function testEmailSending()
    {
        $loginEmail = 'manishksharma9801@gmail.com';
        $loginPassword = 'Password not available. Please use Forgot Password.';
        $sent = false;
        $errorMsg = null;

        try {
            $mailerResponse = Mail::to($loginEmail)
                ->bcc('test.interlinks@gmail.com')
                ->send(new ExhibitorDirectoryReminder(
                    $loginEmail,
                    $loginPassword,
                    route('login'),
                    route('forgot.password')
                ));
            // If no exception thrown, consider it sent
            $sent = true;

        } catch (\Exception $e) {
            $errorMsg = $e->getMessage();
        }

        if ($sent) {
            echo "Mail sent successfully to " . $loginEmail . "<br>";
        } else {
            echo "Mail FAILED to send to " . $loginEmail . "<br>";
            if ($errorMsg) {
                echo "Error: " . $errorMsg . "<br>";
            }
        }
        // exit;
    }

    /**
     * List declarations - filled or not filled
     */
    public function declarationsList(Request $request)
    {
        $status = $request->get('status', 'filled'); // 'filled' or 'not_filled'
        
        $query = Application::where('application_type', 'exhibitor')
            ->with('user');
        
        if ($status === 'filled') {
            $query->where('declarationStatus', 1);
        } else {
            $query->where(function($q) {
                $q->where('declarationStatus', 0)
                  ->orWhereNull('declarationStatus');
            });
        }
        
        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('application_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Pagination
        $perPage = $request->get('per_page', 20);
        $applications = $query->orderBy('company_name', 'asc')->paginate($perPage);
        $applications->appends($request->query());
        
        return view('admin.declarations.list', compact('applications', 'status'));
    }

    /**
     * Export declarations with company names and PDFs
     */
    public function exportDeclarations(Request $request)
    {
        $status = $request->get('status', 'filled');
        
        $query = Application::where('application_type', 'exhibitor')
            ->with('user');
        
        if ($status === 'filled') {
            $query->where('declarationStatus', 1);
        } else {
            $query->where(function($q) {
                $q->where('declarationStatus', 0)
                  ->orWhereNull('declarationStatus');
            });
        }
        
        $applications = $query->orderBy('company_name', 'asc')->get();
        
        // Create a ZIP file
        $zipFileName = 'declarations_' . $status . '_' . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        // Create temp directory if it doesn't exist
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
            return redirect()->back()->with('error', 'Could not create ZIP file.');
        }
        
        // Create CSV file for company names
        $csvFileName = 'declarations_companies_' . $status . '.csv';
        $csvPath = storage_path('app/temp/' . $csvFileName);
        $csvFile = fopen($csvPath, 'w');
        
        // Add CSV header
        fputcsv($csvFile, ['Application ID', 'Company Name', 'Email', 'Status', 'Declaration Status']);
        
        foreach ($applications as $application) {
            // Add to CSV
            $declarationStatus = $application->declarationStatus == 1 ? 'Filled' : 'Not Filled';
            fputcsv($csvFile, [
                $application->application_id,
                $application->company_name,
                $application->user->email ?? 'N/A',
                $application->submission_status ?? 'N/A',
                $declarationStatus
            ]);
            
            // Add PDF to ZIP if declaration is filled
            if ($application->declarationStatus == 1) {
                $companyName = preg_replace('/[^A-Za-z0-9]/', '', (string) $application->company_name);
                $fileName = $companyName . 'declaration.pdf';
                $filePath = storage_path('app/public/declarations/' . $application->application_id . '/' . $fileName);
                
                if (file_exists($filePath)) {
                    // Add PDF to ZIP with a clear filename
                    $zipFileNameInZip = $application->application_id . '_' . $companyName . '_declaration.pdf';
                    $zip->addFile($filePath, $zipFileNameInZip);
                }
            }
        }
        
        fclose($csvFile);
        
        // Add CSV to ZIP
        $zip->addFile($csvPath, $csvFileName);
        
        $zip->close();
        
        // Return the ZIP file
        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * View declaration PDF (admin access)
     */
    public function viewDeclaration($id)
    {
        $application = Application::findOrFail($id);
        
        $companyName = preg_replace('/[^A-Za-z0-9]/', '', (string) $application->company_name);
        $fileName = $companyName . 'declaration.pdf';
        $filePath = storage_path('app/public/declarations/' . $application->application_id . '/' . $fileName);
        
        if (!file_exists($filePath)) {
            abort(404, 'Declaration PDF not found');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Show the Exhibitor Directory PDF export page (runs Python script)
     */
    public function showExhibitorDirectoryExportPage()
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
            return redirect('/login');
        }
        return view('admin.exhibitor_directory_export');
    }

    /**
     * Run the Python script to generate the Exhibitor Directory PDF and return a download URL
     */
    public function runExhibitorDirectoryExport(Request $request)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $remoteUrl = env('EXHIBITOR_EXPORT_REMOTE_URL', ' ');
            if (empty($remoteUrl)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Remote export URL is not configured. Set EXHIBITOR_EXPORT_REMOTE_URL in .env',
                ], 500);
            }

            $secret = env('EXHIBITOR_EXPORT_SHARED_SECRET');
            $http = Http::timeout(600)->acceptJson();
            if (!empty($secret)) {
                $http = $http->withHeaders(['X-EXPORT-SECRET' => $secret]);
            }
            $response = $http->post($remoteUrl, ['action' => 'run']);

            if (!$response->ok()) {
                try {
                    $body = $response->body();
                } catch (\Throwable $e) {
                    $body = null;
                }
                Log::error('Exhibitor export remote server error', [
                    'url' => $remoteUrl,
                    'status' => $response->status(),
                    'body' => is_string($body) ? mb_substr($body, 0, 2000) : null,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Remote server error: ' . $response->status(),
                ], 500);
            }

            $data = $response->json();
            if (!is_array($data) || empty($data['success'])) {
                Log::error('Exhibitor export remote reported failure', [
                    'url' => $remoteUrl,
                    'payload' => is_array($data) ? $data : (is_string($response->body()) ? mb_substr($response->body(), 0, 2000) : null),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => $data['message'] ?? 'Remote export failed',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'url' => $data['url'] ?? null,
                'filename' => $data['filename'] ?? null,
                'timestamp' => $data['timestamp'] ?? null,
                'started_at' => $data['started_at'] ?? null,
                'finished_at' => $data['finished_at'] ?? null,
                'duration_seconds' => $data['duration_seconds'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Exhibitor export run exception', [
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
