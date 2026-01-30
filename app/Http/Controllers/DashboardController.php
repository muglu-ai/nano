<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ExhibitionParticipant;
use App\Models\ExhibitorInfo;
use App\Models\Invoice;
use App\Models\StallManning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\CoExhibitor;
use App\Models\Payment;
use App\Models\Ticket;
use App\Helpers\TicketAllocationHelper;
use App\Helpers\EventAnalyticsHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    //
    //construct function to check if user is logged in
    public function __construct()
    {
        if (auth()->check() && !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
            return redirect('/login');
        }
    }

    // make a function check if the user is exhibitor and least once application is approved and payment is successful
    private function isExhibitorWithApprovedApplication()
    {
        $user = auth()->user();
        if ($user && $user->role == 'exhibitor') {
            // change this to cheeck just for approved application 
            $application = Application::where('user_id', $user->id)
                ->where('submission_status', 'approved')
                ->where(function ($query) {
                    $query->where('allocated_sqm', '>', 0)
                          ->orWhere('allocated_sqm', '=', 'Startup Booth')
                        ->orWhere('allocated_sqm', '=', 'Booth / POD')
                    ;
                })
                ->first();

            //verified if the application has invoices with successful payments
            // $application = Application::where('user_id', $user->id)
            //     ->where('submission_status', 'approved')
            //     ->whereHas('invoices.payments', function ($query) {
            //         $query->where('status', 'successful');
            //     })
            //     ->first();

            return !is_null($application);
        }
        return false;
    }

    public function updateFasciaName(Request $request)
    {

        //call the isExhibitorWithApprovedApplication function to check if the user is exhibitor and atleast once application is approved and payment is successful
        if (!$this->isExhibitorWithApprovedApplication()) {
            return redirect()->route('user.dashboard')
                ->with('error', 'You must have an approved application with successful payment to update the fascia name.');
        }

        // 1. Get the authenticated user's application
        $application = Application::where('user_id', Auth::id())->firstOrFail();

        // 2. CRITICAL: Check if the fascia name has already been submitted.
        // If it is not empty, prevent the update and redirect with an error.
        if (!empty($application->fascia_name)) {
            return redirect()->route('user.dashboard')
                ->with('error', 'Fascia name has already been submitted and cannot be changed.');
        }

        // 3. Validate the incoming request data.
        $validated = $request->validate([
            'fascia_name' => 'required|string|max:255',
        ]);

        // 4. Update the application with the new fascia name.
        $application->update([
            'fascia_name' => $validated['fascia_name'],
        ]);

        // 5. Redirect back to the dashboard with a success message.
        return redirect()->route('user.dashboard')
            ->with('success', 'Fascia name has been saved successfully!');
    }


    public function exhibitorDashboard()
    {
        //fetch user type and send to that dashboard

        $user = auth()->user();
        //if not user is logged in then redirect to login page
        if (!auth()->check()) {
            return redirect('/login');
        }
        if ($user->role == 'exhibitor') {
            $application = Application::where('user_id', auth()->user()->id)
                ->where('submission_status', 'approved')
                // ->where(function ($query) {
                //     $query->where('allocated_sqm', '>', 0)
                //         ->orWhere('allocated_sqm', '=', 'Startup Booth')
                //         ->orWhere('allocated_sqm', '=', 'Booth / POD')
                //     ;
                // })

                // ->whereHas('invoices.payments', function ($query) {
                //     $query->where('status', 'successful');
                // })
                ->first();

            //if application is null redirect to event list  name event.list
            if (!$application) {
                return redirect()->route('event.list');
            }
            //get the no of exhibitors and delegate from the exhibitionParticipation table who's id is application id with same user id
            //get the application id from the application table where user id is same as the logged in user id
            $applicationId = Application::where('user_id', auth()->id())->value('id');
            
            // Handle case when applicationId is null
            if (!$applicationId) {
                return redirect()->route('event.list')->with('error', 'No application found. Please submit an application first.');
            }
            
            //get the application
            $application = Application::where('user_id', auth()->id())->first();
            
            //get the exhibitor and delegate count from the exhibitionParticipation table where application id is same as the application id
            $exhibitionParticipant = ExhibitionParticipant::where('application_id', $applicationId)->first();
            
            // Get ticket allocation details using helper
            $ticketDetails = collect();
            $ticketSummary = [];
            
            if ($exhibitionParticipant) {
                try {
                    // Get allocation details using helper
                    $allocationDetails = TicketAllocationHelper::getAllocation($applicationId);
                    
                    // Convert to ticketDetails format
                    $ticketDetails = collect($allocationDetails)->map(function ($data, $ticketTypeId) {
                        return [
                            'id' => $ticketTypeId,
                            'name' => $data['name'] ?? 'Unknown',
                            'count' => $data['count'] ?? 0,
                            'slug' => $data['slug'] ?? Str::slug($data['name'] ?? 'unknown', '-'),
                        ];
                    });

                    // Get usage stats for each ticket type
                    $usageStats = TicketAllocationHelper::getInvitationUsageStats($applicationId);
                    
                    foreach ($ticketDetails as $ticket) {
                        $ticketTypeId = $ticket['id'];
                        $stats = $usageStats[$ticketTypeId] ?? [];
                        
                        $ticketSummary[] = [
                            'id' => $ticketTypeId,
                            'name' => $ticket['name'],
                            'count' => $ticket['count'],
                            'usedCount' => $stats['used'] ?? 0,
                            'pendingCount' => $stats['pending'] ?? 0,
                            'acceptedCount' => $stats['accepted'] ?? 0,
                            'cancelledCount' => $stats['cancelled'] ?? 0,
                            'remainingCount' => $stats['available'] ?? $ticket['count'],
                            'slug' => $ticket['slug'],
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error('Error getting ticket allocation details in dashboard: ' . $e->getMessage());
                    $ticketDetails = collect();
                    $ticketSummary = [];
                }
            }



            // dd($ticketSummary);

            //

            // whatever their in $ticketDetails ticketType and count pass it to view so that the card can be shown in dashboard dynamically
            // also generate the slug of it

            $directoryFilled = ExhibitorInfo::where('application_id', $applicationId)
                                ->where('submission_status', 1)
                                ->exists();

//             dd($ticketDetails)   ;
// dd($directoryFilled);


            return view('dashboard.index', compact('exhibitionParticipant', 'application', 'ticketDetails', 'directoryFilled', 'ticketSummary'));
            return view('dashboard.index');
        } elseif ($user->role == 'admin') {
            $analytics = app('analytics');
            $submittedApplications = $analytics['applicationsByStatus']['submitted'] ?? 0;
            $approvedApplications = $analytics['applicationsByStatus']['approved'] ?? 0;
            $rejectedApplications = $analytics['applicationsByStatus']['rejected'] ?? 0;
            $inProgressApplications = $analytics['applicationsByStatus']['in progress'] ?? 0;
            $totalApplications = $submittedApplications + $approvedApplications + $rejectedApplications + $inProgressApplications;

            return view('dashboard.admin', compact('analytics'));
        }


        return view('exhibitor.dashboard');
    }

    public function exhibitorDashboard_new()
    {
        //fetch user type and send to that dashboard

        $user = auth()->user();
        //if not user is logged in then redirect to login page
        if (!auth()->check()) {
            return redirect('/login');
        }

        //dd($user->role);
        if ($user->role == 'exhibitor') {
            $application = Application::where('user_id', auth()->user()->id)
                ->where('submission_status', 'approved')
                ->where(function ($query) {
                    $query->where('allocated_sqm', '>', 0)
                        ->orWhere('allocated_sqm', '=', 'Startup Booth')
                        ->orWhere('allocated_sqm', '=', 'Booth / POD');
                })

                // ->whereHas('invoices.payments', function ($query) {
                //     $query->where('status', 'successful');
                // })
                ->first();

            //if application is null redirect to event list  name event.list
            if (!$application) {
                return redirect()->route('event.list');
            }
            //get the no of exhibitors and delegate from the exhibitionParticipation table who's id is application id with same user id
            //get the application id from the application table where user id is same as the logged in user id
            $applicationId = Application::where('user_id', auth()->id())->value('id');
            
            // Handle case when applicationId is null
            if (!$applicationId) {
                return redirect()->route('event.list')->with('error', 'No application found. Please submit an application first.');
            }
            
            //get the application
            $application = Application::where('user_id', auth()->id())->first();
            
            //get the exhibitor and delegate count from the exhibitionParticipation table where application id is same as the application id
            $exhibitionParticipant = ExhibitionParticipant::where('application_id', $applicationId)->first();
            
            // Handle case when exhibitionParticipant is null for directory check
            $directoryFilled = false;
            if ($applicationId) {
                $directoryFilled = ExhibitorInfo::where('application_id', $applicationId)
                                    ->where('submission_status', 1)
                                    ->exists();
            }

        //    dd($directoryFilled);


            return view('dashboard.index', compact('exhibitionParticipant', 'application', 'directoryFilled'));
            return view('dashboard.index');
        } elseif ($user->role == 'admin' || $user->role == 'super-admin') {
            try {
                $analytics = app('analytics');
                $submittedApplications = $analytics['applicationsByStatus']['submitted'] ?? 0;
                $approvedApplications = $analytics['applicationsByStatus']['approved'] ?? 0;
                $rejectedApplications = $analytics['applicationsByStatus']['rejected'] ?? 0;
                $inProgressApplications = $analytics['applicationsByStatus']['in progress'] ?? 0;
                $totalApplications = $submittedApplications + $approvedApplications + $rejectedApplications + $inProgressApplications;
            } catch (\Exception $e) {
                Log::error('Error loading analytics: ' . $e->getMessage());
                // Set default values if analytics fails
                $analytics = [];
                $submittedApplications = 0;
                $approvedApplications = 0;
                $rejectedApplications = 0;
                $inProgressApplications = 0;
                $totalApplications = 0;
            }

            // Fetch applications grouped by billing country, excluding applications in sponsorships
            try {
                $applicationsByCountry = DB::table('applications as a')
                    ->join('countries as c', 'a.billing_country_id', '=', 'c.id') // Use billing_country_id
                    ->leftJoin('sponsorships as s', 'a.id', '=', 's.application_id') // Check if application exists in sponsorships
                    ->select(
                        'c.name as country_name',
                        DB::raw('COUNT(a.id) as total_companies'),
                        DB::raw('SUM(CAST(a.interested_sqm AS UNSIGNED)) as total_sqm')
                    )
                    ->where('a.submission_status', 'submitted')
                    ->whereNull('s.application_id') // Exclude applications present in sponsorships
                    ->groupBy('c.id')
                    ->having('total_sqm', '>', 0)
                    ->orderByDesc('total_companies')
                    ->get();
            } catch (\Exception $e) {
                Log::error('Error fetching applications by country: ' . $e->getMessage());
                $applicationsByCountry = collect([]);
            }

//            dd($applicationsByCountry);
            // Count total unique countries with submitted applications (excluding sponsorships)
            try {
                $totalCountries = DB::table('applications as a')
                    ->leftJoin('sponsorships as s', 'a.id', '=', 's.application_id') // Ensure exclusion
                    ->where('a.submission_status', 'submitted')
                    ->whereNull('s.application_id') // Exclude applications in sponsorships
                    ->distinct()
                    ->count('a.billing_country_id');
            } catch (\Exception $e) {
                Log::error('Error counting total countries: ' . $e->getMessage());
                $totalCountries = 0;
            }

            // Get India vs. International count and total sqm (excluding sponsorships)
            try {
                $indiaInternationalStats = DB::table('applications as a')
                    ->join('countries as c', 'a.billing_country_id', '=', 'c.id') // Use billing_country_id
                    ->leftJoin('sponsorships as s', 'a.id', '=', 's.application_id') // Exclude sponsored applications
                    ->selectRaw("
                        COUNT(DISTINCT CASE WHEN c.name = 'India' THEN a.id END) AS india_count,
                        SUM(CASE WHEN c.name = 'India' THEN CAST(a.interested_sqm AS UNSIGNED) ELSE 0 END) AS india_sqm,
                        COUNT(DISTINCT CASE WHEN c.name != 'India' THEN a.id END) AS international_count,
                        SUM(CASE WHEN c.name != 'India' THEN CAST(a.interested_sqm AS UNSIGNED) ELSE 0 END) AS international_sqm
                    ")
                    ->where('a.submission_status', 'submitted')
                    ->whereNull('s.application_id') // Exclude applications in sponsorships
                    ->whereRaw("CAST(a.interested_sqm AS UNSIGNED) > 0 AND a.interested_sqm IS NOT NULL AND a.interested_sqm != ''") // Exclude zero and empty sqm values
                    ->first();
            } catch (\Exception $e) {
                Log::error('Error fetching India/International stats: ' . $e->getMessage());
                $indiaInternationalStats = (object)[
                    'india_count' => 0,
                    'india_sqm' => 0,
                    'international_count' => 0,
                    'international_sqm' => 0
                ];
            }

            try {
                $approvedApplicationsByCountry = DB::table('applications as a')
                    ->join('countries as c', 'a.billing_country_id', '=', 'c.id') // Use billing_country_id
                    ->leftJoin('sponsorships as s', 'a.id', '=', 's.application_id') // Exclude applications in sponsorships
                    ->select(
                        'c.name as country_name',
                        DB::raw('COUNT(a.id) as total_companies'),
                        DB::raw('SUM(CAST(a.allocated_sqm AS UNSIGNED)) as total_sqm')
                    )
                    ->where('a.submission_status', 'approved') // Only approved applications
                    ->whereNull('s.application_id') // Exclude applications in sponsorships
                    ->groupBy('c.id')
                    ->having('total_sqm', '>', 0)
                    ->orderByDesc('total_companies')
                    ->get();
            } catch (\Exception $e) {
                Log::error('Error fetching approved applications by country: ' . $e->getMessage());
                $approvedApplicationsByCountry = collect([]);
            }

            // Count total unique countries with approved applications (excluding sponsorships)
            try {
                $totalApprovedCountries = DB::table('applications as a')
                    ->leftJoin('sponsorships as s', 'a.id', '=', 's.application_id') // Ensure exclusion
                    ->where('a.submission_status', 'approved') // Only approved applications
                    ->whereNull('s.application_id') // Exclude applications in sponsorships
                    ->distinct()
                    ->count('a.billing_country_id');
            } catch (\Exception $e) {
                Log::error('Error counting approved countries: ' . $e->getMessage());
                $totalApprovedCountries = 0;
            }

            // Get India vs. International count and total sqm (excluding sponsorships)
            try {
                $approvedIndiaInternationalStats = DB::table('applications as a')
                    ->join('countries as c', 'a.billing_country_id', '=', 'c.id') // Use billing_country_id
                    ->leftJoin('sponsorships as s', 'a.id', '=', 's.application_id') // Exclude sponsored applications
                    ->selectRaw("
                        COUNT(DISTINCT CASE WHEN c.name = 'India' THEN a.id END) AS india_count,
                        SUM(CASE WHEN c.name = 'India' THEN CAST(a.allocated_sqm AS UNSIGNED) ELSE 0 END) AS india_sqm,
                        COUNT(DISTINCT CASE WHEN c.name != 'India' THEN a.id END) AS international_count,
                        SUM(CASE WHEN c.name != 'India' THEN CAST(a.allocated_sqm AS UNSIGNED) ELSE 0 END) AS international_sqm
                    ")
                    ->where('a.submission_status', 'approved') // Only approved applications
                    ->whereNull('s.application_id') // Exclude applications in sponsorships
                    ->whereRaw("a.allocated_sqm IS NOT NULL") // Exclude null and zero sqm values
                    ->first();
            } catch (\Exception $e) {
                Log::error('Error fetching approved India/International stats: ' . $e->getMessage());
                $approvedIndiaInternationalStats = (object)[
                    'india_count' => 0,
                    'india_sqm' => 0,
                    'international_count' => 0,
                    'international_sqm' => 0
                ];
            }

            // give me sql query for the above query



//            dd($approvedIndiaInternationalStats);

            //count the CoExhibitors where status pending
            try {
                $coExhibitorCount = CoExhibitor::where('status', 'pending')->count();
                $approvedCoexhibitorCount = CoExhibitor::where('status', 'approved')->count();
            } catch (\Exception $e) {
                Log::error('Error counting CoExhibitors: ' . $e->getMessage());
                $coExhibitorCount = 0;
                $approvedCoexhibitorCount = 0;
            }

            try {
                return view('dashboard.admin_new', compact(
                    'analytics',
                    'applicationsByCountry',
                    'totalCountries',
                    'indiaInternationalStats',
                    'approvedApplicationsByCountry',
                    'totalApprovedCountries',
                    'approvedIndiaInternationalStats',
                    'coExhibitorCount',
                    'approvedCoexhibitorCount'
                ));
            } catch (\Exception $e) {
                Log::error('Error rendering admin dashboard view: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                return response()->view('errors.500', ['message' => 'Error loading dashboard: ' . $e->getMessage()], 500);
            }
        }


        return view('exhibitor.dashboard');
    }

    //applicant details
    public function applicantDetails()
    {
        $this->__construct();

        return view('admin.application-view');
    }


    //invoice details for admin from Invoice model
    public function invoiceDetails()
    {
        $this->__construct();
        $slug = 'Invoices';
        $invoices = Invoice::with(['application', 'payments', 'billingDetails'])->get();


        return view('dashboard.invoice-list', compact('invoices', 'slug'));
    }


    // get the participant details for user to get the printable view
    public function participantDetails()
    {
        $this->__construct();
        $slug = 'Participant Details';
        $application = Application::where('user_id', auth()->id())->first();
        
        // Handle case when application is null
        if (!$application) {
            return redirect()->route('event.list')->with('error', 'No application found. Please submit an application first.');
        }
        
        // dd($application);
        $contactPerson = '';
        if ($application->eventContact) {
            $contactPerson = trim(
                ($application->eventContact->salutation ?? '') . ' ' .
                ($application->eventContact->first_name ?? '') . ' ' .
                ($application->eventContact->last_name ?? '')
            );
        }
        
        $address = $application->address ?? '';
        if ($application->city_id) {
            $address .= ' ' . $application->city_id;
        }
        if ($application->state && $application->state->name) {
            $address .= ', ' . $application->state->name;
        }
        if ($application->postal_code) {
            $address .= '- ' . $application->postal_code;
        }
        if ($application->country && $application->country->name) {
            $address .= ' ' . $application->country->name;
        }
        
        $data = [
            'application_id' => $application->application_id ?? '',
            'contact_person' => $contactPerson,
            'company_name' => $application->company_name ?? '',
            'address' => trim($address),
            'booth_no' => $application->stallNumber ?? '',

        ];

        //dd($data);

        return view('dashboard.participant-details', compact('data', 'slug'));
    }

    public function eventAnalytics()
    {
        $this->__construct();
        
        $analytics = EventAnalyticsHelper::getEventAnalytics();
       
        
        return view('dashboard.admin_new1', compact('analytics'));
    }

    public function registrationCategoryDetails($category)
    {
        $this->__construct();
        
        // Get delegates for the specific category
        $delegates = DB::table('ticket_registration_categories as trc')
            ->join('ticket_registrations as tr', 'trc.id', '=', 'tr.registration_category_id')
            ->leftJoin('ticket_delegates as td', 'tr.id', '=', 'td.registration_id')
            ->leftJoin('ticket_orders as to', 'tr.id', '=', 'to.registration_id')
            ->leftJoin('invoices as inv', 'tr.id', '=', 'inv.registration_id')
            ->leftJoin('payments as p', function($join) {
                $join->on('inv.id', '=', 'p.invoice_id')
                     ->where('p.status', '=', 'successful');
            })
            ->where('trc.name', $category)
            ->where('trc.is_active', 1)
            ->select(
                'tr.id as registration_id',
                'tr.created_at as registration_date',
                'tr.industry_sector as sector',
                'tr.organisation_type as organisation_type',
                'to.order_no as tin_number',
                'tr.company_name as company_name',
                'tr.registration_type as registration_type',
                DB::raw('COUNT(DISTINCT td.id) as no_of_delegates'),
                DB::raw('GROUP_CONCAT(DISTINCT CONCAT(COALESCE(td.salutation, ""), " ", td.first_name, " ", td.last_name) ORDER BY td.id SEPARATOR ", ") as delegate_names'),
                'trc.name as registration_category',
                DB::raw('COALESCE(MAX(p.payment_method), "Not Specified") as mode_of_payment'),
                DB::raw('CASE WHEN COUNT(p.id) > 0 THEN "Paid" ELSE "Not Paid" END as payment_status'),
                DB::raw('CASE WHEN MAX(inv.total_final_price) IS NOT NULL THEN CONCAT("Rs. ", FORMAT(MAX(inv.total_final_price), 2)) ELSE "N/A" END as amount'),
                DB::raw('CASE WHEN MAX(inv.id) IS NOT NULL THEN CONCAT("INV-", MAX(inv.id)) ELSE "Payment Pending" END as invoice'),
                'tr.gstin as gst_number'
            )
            ->groupBy(
                'tr.id', 'tr.created_at', 'tr.industry_sector', 'tr.organisation_type', 
                'to.order_no', 'tr.company_name', 'tr.registration_type', 
                'trc.name', 'tr.gstin'
            )
            ->orderBy('tr.created_at', 'desc')
            ->get();
        
        return view('dashboard.registration_category_details', compact('delegates', 'category'));
    }

    public function delegateDetails($registrationId)
    {
        $this->__construct();
        
        // Get detailed registration and delegate information, including tax fields from ticket_orders
        $registration = DB::table('ticket_registrations as tr')
            ->join('ticket_registration_categories as trc', 'tr.registration_category_id', '=', 'trc.id')
            ->leftJoin('ticket_orders as to', 'tr.id', '=', 'to.registration_id')
            ->leftJoin('invoices as inv', 'tr.id', '=', 'inv.registration_id')
            ->leftJoin('payments as p', function($join) {
                $join->on('inv.id', '=', 'p.invoice_id')
                     ->where('p.status', '=', 'successful');
            })
            ->where('tr.id', $registrationId)
            ->select(
                'tr.*',
                'trc.name as registration_category',
                'to.order_no as tin_number',
                DB::raw('(SELECT payment_method FROM payments WHERE invoice_id = inv.id AND status = "successful" LIMIT 1) as payment_method'),
                DB::raw('(SELECT CASE WHEN COUNT(*) > 0 THEN "Paid" ELSE "Not Paid" END FROM payments WHERE invoice_id = inv.id AND status = "successful") as payment_status'),
                DB::raw('(SELECT total_final_price FROM invoices WHERE registration_id = tr.id LIMIT 1) as total_amount'),
                DB::raw('(SELECT id FROM invoices WHERE registration_id = tr.id LIMIT 1) as invoice_id'),
                // Correct tax fields from ticket_orders
                'to.igst_total',
                'to.igst_rate',
                'to.cgst_total',
                'to.cgst_rate',
                'to.sgst_total',
                'to.sgst_rate'
            )
            ->first();
            
        if (!$registration) {
            return redirect()->back()->with('error', 'Registration not found.');
        }
        
        // Get all delegates for this registration, including their registration category
        $delegates = DB::table('ticket_delegates as td')
            ->join('ticket_registrations as tr', 'td.registration_id', '=', 'tr.id')
            ->join('ticket_registration_categories as trc', 'tr.registration_category_id', '=', 'trc.id')
            ->where('td.registration_id', $registrationId)
            ->select('td.*', 'trc.name as registration_category')
            ->get();
        
        return view('dashboard.delegate_details', compact('registration', 'delegates'));
    }
}
