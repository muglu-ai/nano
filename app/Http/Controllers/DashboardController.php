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
            
            // get the ticketAllocation value from the exhibitionParticipant table and get the ticket details from the tickets table with id and count from the ticketAllocation value
            // Handle case when $exhibitionParticipant is null to avoid error when accessing property
            try {
                if ($exhibitionParticipant) {
                    $ticketAllocation = $exhibitionParticipant->ticketAllocation;

                    if ($ticketAllocation) {
                        $ticketIds = json_decode($ticketAllocation, true);
                        if (!is_array($ticketIds)) {
                            throw new \Exception('Invalid ticket allocation format.');
                        }
                        $tickets = Ticket::whereIn('id', array_keys($ticketIds))->get();
                    } else {
                        $tickets = collect();
                        $ticketIds = [];
                    }

                    //make as json with ticket name and count
                    $ticketDetails = $tickets->map(function ($ticket) use ($ticketIds) {
                        return [
                            'name' => $ticket->ticket_type,
                            'count' => isset($ticketIds[$ticket->id]) ? $ticketIds[$ticket->id] : 0,
                            'slug' => Str::slug($ticket->ticket_type, '-'),
                        ];
                    });
                } else {
                    // If no participant, set empty collection for ticketDetails
                    $ticketDetails = collect();
                }
            } catch (\Exception $e) {
                $ticketDetails = collect();
                // Optionally log the error:
                Log::error($e->getMessage());
            }

            // Used count of each category
            // Handle case when $exhibitionParticipant is null
            if (!$exhibitionParticipant) {
                // If no exhibition participant exists, set default values
                $stallManningCount = 0;
                $ticketSummary = [];
            } else {
                // Get the Stall Manning Count from stall_manning table using same id
                $stallManningCount = StallManning::where('exhibition_participant_id', $exhibitionParticipant->id)->count();
                $ticketSummary = [];
                
                foreach ($ticketDetails as $ticket) {
                    $usedCount = DB::table('complimentary_delegates')
                        ->where('exhibition_participant_id', $exhibitionParticipant->id)
                        ->where('ticketType', $ticket['name'])
                        ->count();

                    $remainingCount = $ticket['count'] - $usedCount;

                    $ticketSummary[] = [
                        'name' => $ticket['name'],
                        'count' => $ticket['count'],
                        'usedCount' => $usedCount,
                        'remainingCount' => $remainingCount,
                        'slug' => $ticket['slug'],
                    ];
                }

                //merge the stall manning count to ticket summmary with ticket type as Exhibitor
                $stallManningCountValue = $exhibitionParticipant->stall_manning_count ?? 0;
                $ticketSummary[] = [
                    'name' => 'Exhibitor',
                    'count' => $stallManningCountValue,
                    'usedCount' => $stallManningCount,
                    'remainingCount' => max(0, $stallManningCountValue - $stallManningCount),
                    'slug' => 'stall_manning',
                ];
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
        } elseif ($user->role == 'admin') {
            $analytics = app('analytics');
            $submittedApplications = $analytics['applicationsByStatus']['submitted'] ?? 0;
            $approvedApplications = $analytics['applicationsByStatus']['approved'] ?? 0;
            $rejectedApplications = $analytics['applicationsByStatus']['rejected'] ?? 0;
            $inProgressApplications = $analytics['applicationsByStatus']['in progress'] ?? 0;
            $totalApplications = $submittedApplications + $approvedApplications + $rejectedApplications + $inProgressApplications;

            // Fetch applications grouped by billing country, excluding applications in sponsorships
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

//            dd($applicationsByCountry);
            // Count total unique countries with submitted applications (excluding sponsorships)
            $totalCountries = DB::table('applications as a')
                ->leftJoin('sponsorships as s', 'a.id', '=', 's.application_id') // Ensure exclusion
                ->where('a.submission_status', 'submitted')
                ->whereNull('s.application_id') // Exclude applications in sponsorships
                ->distinct()
                ->count('a.billing_country_id');

            // Get India vs. International count and total sqm (excluding sponsorships)
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

            // Count total unique countries with approved applications (excluding sponsorships)
            $totalApprovedCountries = DB::table('applications as a')
                ->leftJoin('sponsorships as s', 'a.id', '=', 's.application_id') // Ensure exclusion
                ->where('a.submission_status', 'approved') // Only approved applications
                ->whereNull('s.application_id') // Exclude applications in sponsorships
                ->distinct()
                ->count('a.billing_country_id');

            // Get India vs. International count and total sqm (excluding sponsorships)
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

            // give me sql query for the above query



//            dd($approvedIndiaInternationalStats);

            //count the CoExhibitors where status pending
            $coExhibitorCount = CoExhibitor::where('status', 'pending')->count();
            $approvedCoexhibitorCount = CoExhibitor::where('status', 'approved')->count();


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
}
