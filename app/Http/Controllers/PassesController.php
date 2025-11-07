<?php

namespace App\Http\Controllers;

use App\Models\ExhibitorInfo;
use App\Models\ExhibitorProduct;
use App\Models\ExhibitorPressRelease;
use App\Models\Application;
use App\Models\StallManning;
use App\Models\CoExhibitor;
use http\Env\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Sponsorship;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SponsorInvoiceMail;
use App\Models\ExhibitionParticipant;
use App\Models\ComplimentaryDelegate;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\Models\AttendeeLog;
use App\Models\Attendee;
use App\Models\Ticket;


class PassesController extends Controller
{


    public function CombinePasses(Request $request)
    {

        $slug = "Exhibitor Passes";
        // Get StallManning entries and add pass type
        $stallManningQuery = StallManning::select(
            'id',
            'unique_id',
            'exhibition_participant_id',
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'mobile',
            'organisation_name',
            'created_at',
            'updated_at',
            DB::raw("'Exhibitor' as pass_type")
        )
            ->with(['exhibitionParticipant.application', 'exhibitionParticipant.coExhibitor'])
            ->whereNotNull('first_name')
            ->where('first_name', '!=', '');
        $stallManningCount = $stallManningQuery->count();


        // Get ComplimentaryDelegate entries and add pass type
        $complimentaryQuery = ComplimentaryDelegate::select(
            'id',
            'unique_id',
            'exhibition_participant_id',
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'mobile',
            'organisation_name',
            'created_at',
            'updated_at',
            'ticketType as pass_type'

        )
            ->with(['exhibitionParticipant.application', 'exhibitionParticipant.coExhibitor'])
            ->whereNotNull('first_name')
            ->whereRaw("TRIM(first_name) != ''");


        $complimentaryCount = $complimentaryQuery->count();
        // Handle search functionality
        if ($request->has('search')) {
            $searchTerm = trim($request->search);
            $stallManningQuery->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                    ->orWhere('unique_id', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('mobile', 'like', "%{$searchTerm}%")
                    ->orWhere('organisation_name', 'like', "%{$searchTerm}%");
            });
            $complimentaryQuery->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                    ->orWhere('unique_id', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('mobile', 'like', "%{$searchTerm}%")
                    ->orWhere('organisation_name', 'like', "%{$searchTerm}%");
            });
        }

        // Merge the two queries using union
        $query = $stallManningQuery->union($complimentaryQuery);

        // $inauguralApplied = ComplimentaryDelegate::whereHas('exhibitionParticipant.application', function ($q) {
        //     $q->whereNotNull('first_name')->where('first_name', '!=', '');
        // })->count();
        $complimentaryCount = (clone $complimentaryQuery)->count();

        $inauguralApplied = $complimentaryCount;

        $totalCompanyCount = ExhibitionParticipant::has('stallManning')->count();

        //dd($stallManningQuery->count(), $complimentaryQuery->count());

        // Note: count() after union is not reliable, so you may need to use get()->count()
        $totalEntries = $complimentaryCount + $stallManningCount;

        // Get paginated results
        $stallManningList = DB::table(DB::raw("({$query->toSql()}) as sub"))
            ->mergeBindings($query->getQuery())
            ->paginate(50);

        // Get paginated results
        $stallManningList = $query->paginate(50);


        return view('admin.stall-manning.index', compact('stallManningList', 'totalCompanyCount', 'inauguralApplied', 'totalEntries', 'slug'));
    }
    //get all the exhibitor passes from the StallManning model for the admin
    public function StallManning(Request $request)
    {
        $slug = "Exhibitor Passes";

        // Get StallManning entries and add pass type
        $stallManningQuery = StallManning::select(
            'id',
            'unique_id',
            'exhibition_participant_id',
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'mobile',
            'organisation_name',
            'created_at',
            'updated_at',
            DB::raw("'Exhibitor' as pass_type")
        )
            ->with(['exhibitionParticipant.application', 'exhibitionParticipant.coExhibitor'])
            ->whereNotNull('first_name')
            ->where('first_name', '!=', '');

        // Handle search functionality
        if ($request->has('search')) {
            $searchTerm = trim($request->search);
            $stallManningQuery->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                    ->orWhere('unique_id', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('mobile', 'like', "%{$searchTerm}%")
                    ->orWhere('organisation_name', 'like', "%{$searchTerm}%");
            });
        }

        $stallManningCount = (clone $stallManningQuery)->count();
        $inauguralApplied = 0;
        $totalCompanyCount = ExhibitionParticipant::has('stallManning')->count();
        $totalEntries = $stallManningCount;

        // Get paginated results
        $stallManningList = $stallManningQuery->paginate(50);

        return view('admin.stall-manning.index', compact(
            'stallManningList',
            'totalCompanyCount',
            'inauguralApplied',
            'totalEntries',
            'slug'
        ));
    }

    public function Complimentary(Request $request)
    {

        $slug = "Complimentary Passes";
        // Get StallManning entries and add pass type
        // Only ComplimentaryDelegate entries
        $complimentaryQuery = ComplimentaryDelegate::select(
            'id',
            'unique_id',
            'exhibition_participant_id',
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'mobile',
            'organisation_name',
            'created_at',
            'updated_at',
            'ticketType as pass_type'
        )
            ->with(['exhibitionParticipant.application', 'exhibitionParticipant.coExhibitor'])
            ->whereNotNull('first_name')
            ->whereRaw("TRIM(first_name) != ''");


        if ($request->has('search')) {
            $searchTerm = trim($request->search);
            $complimentaryQuery->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                    ->orWhere('unique_id', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('mobile', 'like', "%{$searchTerm}%")
                    ->orWhere('organisation_name', 'like', "%{$searchTerm}%");
            });
        }

        $complimentaryCount = (clone $complimentaryQuery)->count();
        $inauguralApplied = $complimentaryCount;
        $totalCompanyCount = ExhibitionParticipant::has('stallManning')->count();
        $stallManningCount = 0;
        $totalEntries = $complimentaryCount;

        // Get paginated results
        $stallManningList = $complimentaryQuery->paginate(50);

        return view('admin.stall-manning.index', compact(
            'stallManningList',
            'totalCompanyCount',
            'inauguralApplied',
            'totalEntries',
            'slug'
        ));


        return view('admin.stall-manning.index', compact('stallManningList', 'totalCompanyCount', 'inauguralApplied', 'totalEntries', 'slug'));
    }
    public function Inaugural(Request $request)
    {
        $slug = "Inaugural Passes";

        // Base query for complimentary delegates
        $complimentaryBase = ComplimentaryDelegate::select(
            'id',
            'exhibition_participant_id',
            'unique_id',
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'mobile',
            'organisation_name',
            'created_at',
            'updated_at',
            DB::raw("'Inaugural' as pass_type")
        )
            ->with(['exhibitionParticipant.application', 'exhibitionParticipant.coExhibitor'])
            ->whereNotNull('first_name')
            ->whereRaw("TRIM(first_name) != ''");

        // Apply search if given
        if ($request->filled('search')) {
            $searchTerm = trim($request->search);
            $complimentaryBase->where(function ($q) use ($searchTerm) {
                $q->where('first_name', 'like', "%{$searchTerm}%")
                    ->orWhere('unique_id', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%")
                    ->orWhere('mobile', 'like', "%{$searchTerm}%")
                    ->orWhere('organisation_name', 'like', "%{$searchTerm}%");
            });
        }

        // Clone for counts before pagination
        $complimentaryCount = (clone $complimentaryBase)->count();

        // Get inaugural applied count
        // $inauguralApplied = ComplimentaryDelegate::whereNotNull('first_name')
        //     ->where('first_name', '!=', '')
        //     ->whereHas('exhibitionParticipant.application', function ($q) {
        //         $q->whereNotNull('first_name')->where('first_name', '!=', '');
        //     })
        //     ->count();
        $inauguralApplied = $complimentaryCount;

        // Company count (has stallManning)
        $totalCompanyCount = ExhibitionParticipant::has('complimentaryDelegates')->count();

        // Stall manning count (currently no query, so keep zero or implement if needed)
        $stallManningCount = 0;

        // Total entries
        $totalEntries = $complimentaryCount + $stallManningCount;

        // Paginate results (single paginate call)
        $stallManningList = $complimentaryBase->paginate(50);

        return view('admin.stall-manning.index', compact(
            'stallManningList',
            'totalCompanyCount',
            'inauguralApplied',
            'totalEntries',
            'slug'
        ));
    }


    public function exportPasses(Request $request)
    {
        $data = collect();

        // Stall Manning
        $stallManning = ComplimentaryDelegate::select('id', 'exhibition_participant_id', 'unique_id', 'first_name', 'email', 'mobile', 'job_title', 'organisation_name', 'created_at', 'id_type', 'id_no', 'ticketType')
            ->whereNotNull('first_name')
            ->where('first_name', '!=', '')
            ->get();

        foreach ($stallManning as $row) {
            $data->push([
                'Type' => $row->ticketType,
                'ID' => $row->unique_id,
                'Name' => $row->first_name,
                'Email' => $row->email,
                'Mobile' => ltrim($row->mobile, '+'),
                'Job Title' => $row->job_title,
                'Organisation' => $row->organisation_name,
                // 'ID Type' => $row->id_type ?? 'N/A',
                // 'ID Number' => $row->id_no ?? 'N/A',/
            ]);
        }

        // Complimentary Delegate
        // $complimentary = ComplimentaryDelegate::select('id', 'exhibition_participant_id', 'unique_id', 'first_name', 'email', 'mobile', 'job_title', 'organisation_name', 'created_at', 'id_type', 'id_no')
        //     ->whereNotNull('first_name')
        //     ->where('first_name', '!=', '')
        //     ->get();

        // foreach ($complimentary as $row) {
        //     $data->push([
        //         'Type' => 'Exhibitor Inaugural Passes',
        //         'ID' => $row->unique_id,
        //         'Name' => $row->first_name,
        //         'Email' => $row->email,
        //         'Mobile' => ltrim($row->mobile, '+'),
        //         'Job Title' => $row->job_title ?? 'N/A',
        //         'Organisation' => $row->organisation_name,
        //         // 'ID Type' => $row->id_type ?? 'N/A',
        //         // 'ID Number' => $row->id_no ?? 'N/A',

        //     ]);
        // }

        $filename = 'exhibitor_passes_' . date('Ymd_His') . '.xlsx';

        // Log export action to a file with user details and IP
        $this->logExportingPassesToFile($request);

        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {

            //log which user is downloading the file
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function collection()
            {
                return $this->data;
            }

            public function headings(): array
            {
                return [
                    'Type',
                    'ID',
                    'Name',
                    'Email',
                    'Mobile',
                    'Job Title',
                    'Organisation',
                    // 'ID Type',
                    // 'ID Number',
                ];
            }
        }, $filename);
    }

    private function logExportingPassesToFile(Request $request)
    {
        Log::info('Exhibitor passes export initiated', [
            'user_id' => auth()->id(),
            'date' => now(),
            'ip' => $request->ip(),
        ]);

        $logData = [
            'user_id' => auth()->id(),
            'date' => now()->toDateTimeString(),
            'ip' => $request->ip(),
            'action' => 'export_passes',
            'status' => 'success',
            'details' => 'Exhibitor passes exported successfully'
        ];

        $logFile = storage_path('app/exportLogs.json');
        if (!file_exists($logFile)) {
            file_put_contents($logFile, json_encode([$logData], JSON_PRETTY_PRINT));
        } else {
            $existing = json_decode(file_get_contents($logFile), true) ?? [];
            $existing[] = $logData;
            file_put_contents($logFile, json_encode($existing, JSON_PRETTY_PRINT));
        }
    }

    // 

    //delte any log entry for the user
    public function deleteVisitor($id)
    {
        $attendee = StallManning::where('unique_id', $id)->first();

        if (!$attendee) {
            $attendee = ComplimentaryDelegate::where('unique_id', $id)->first();
        }

        if (!$attendee) {
            // Handle not found, e.g. throw 404 or redirect with error
            abort(404, 'Attendee not found.');
        }
        // Copy to log table
        AttendeeLog::create([
            'attendee_id' => $attendee->id,
            'name' => $attendee->first_name,
            'email' => $attendee->email,
            'data' => json_encode($attendee->toArray()), // backup full row
            'deleted_at' => now(),
        ]);

        // Delete from main table
        $attendee->delete();

        return redirect()->back()->with('success', 'Visitor deleted & copied to log.');
    }

    public function deleteVisitor2($id)
    {
        $attendee = Attendee::where('unique_id', $id)->first();

        // if (!$attendee) {
        //     $attendee = ComplimentaryDelegate::where('unique_id', $id)->first();
        // }

        if (!$attendee) {
            // Handle not found, e.g. throw 404 or redirect with error
            abort(404, 'Attendee not found.');
        }
        // Copy to log table
        AttendeeLog::create([
            'attendee_id' => $attendee->id,
            'name' => $attendee->first_name,
            'email' => $attendee->email,
            'data' => json_encode($attendee->toArray()), // backup full row
            'deleted_at' => now(),
        ]);

        // Delete from main table
        $attendee->delete();

        return redirect()->back()->with('success', 'Visitor deleted & copied to log.');
    }


    public function passesAllocation(Request $request)
    {
        // Get search query and sorting parameters
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 15);
        $sortField = $request->get('sort', 'company_name');
        $sortOrder = $request->get('order', 'asc');

        try {

            // Query for approved applications with passes allocation
            // Include applications with exhibitionParticipant that have passes allocated
            // OR applications without exhibitionParticipant (so admin can add/update passes)
            $query = Application::with(['exhibitionParticipant', 'user', 'billingDetail'])
                ->where('submission_status', 'approved')
                ->where(function ($query) {
                    $query->where('allocated_sqm', '>', 0)
                        ->orWhere('allocated_sqm', '=', 'Startup Booth')
                        ->orWhere('allocated_sqm', '=', 'Booth / POD');
                })
                ->where(function ($query) {
                    // Applications that have exhibitionParticipant with passes allocated
                    $query->whereHas('exhibitionParticipant', function ($ep) {
                        $ep->where(function ($inner) {
                            $inner->where(function ($count) {
                                $count->where('stall_manning_count', '>', 0)
                                    ->orWhere('complimentary_delegate_count', '>', 0);
                            })
                            ->orWhere(function ($ticket) {
                                $ticket->whereNotNull('ticketAllocation')
                                    ->where('ticketAllocation', '!=', '')
                                    ->whereRaw("TRIM(ticketAllocation) != ''");
                            });
                        });
                    })
                    // OR applications that don't have exhibitionParticipant (so admin can add passes)
                    ->orWhereDoesntHave('exhibitionParticipant');
                });
                // ->limit(50);

            // Apply search filter
            if ($search) {
                $searchTerm = trim($search);
                if (!empty($searchTerm)) {
                    $query->where(function ($q) use ($searchTerm) {
                        $q->where('company_name', 'like', "%{$searchTerm}%")
                          ->orWhere('application_id', 'like', "%{$searchTerm}%")
                          ->orWhere('stall_category', 'like', "%{$searchTerm}%")
                          ->orWhere('company_email', 'like', "%{$searchTerm}%");
                    });
                }
            }

            // Validate sort order
            $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';

            // Apply sorting - simplified approach to avoid join issues
            switch ($sortField) {
                case 'company_name':
                    $query->orderBy('company_name', $sortOrder);
                    break;
                case 'stall_category':
                    $query->orderBy('stall_category', $sortOrder);
                    break;
                case 'stall_manning_count':
                case 'complimentary_delegate_count':
                case 'total_passes':
                    // For pass-related sorting, use a simpler approach
                    // We'll sort by company name as fallback for now
                    $query->orderBy('company_name', 'asc');
                    break;
                default:
                    $query->orderBy('company_name', 'asc');
            }

            // Get paginated results
            $applications = $query->paginate($perPage);
            
            // Calculate consumed passes for each application
            foreach ($applications as $app) {
                if ($app->exhibitionParticipant && $app->exhibitionParticipant->id) {
                    // Count consumed StallManning passes
                    $app->consumedStallManning = DB::table('stall_manning')
                        ->where('exhibition_participant_id', $app->exhibitionParticipant->id)
                        ->whereNotNull('first_name')
                        ->where('first_name', '!=', '')
                        ->count();
                    
                    // Count consumed ComplimentaryDelegate passes
                    $app->consumedComplimentary = DB::table('complimentary_delegates')
                        ->where('exhibition_participant_id', $app->exhibitionParticipant->id)
                        ->whereNotNull('first_name')
                        ->whereRaw("TRIM(first_name) != ''")
                        ->count();
                    
                    // Calculate consumed tickets by type
                    $consumedTicketsArray = [];
                    //select all the ticket types from the tickets table
                    $ticketTypes = Ticket::select('ticket_type')->distinct()->pluck('ticket_type');
                    foreach ($ticketTypes as $ticketType) {
                        $count = DB::table('complimentary_delegates')
                            ->where('exhibition_participant_id', $app->exhibitionParticipant->id)
                            ->where('ticketType', $ticketType)
                            ->whereNotNull('first_name')
                            ->whereRaw("TRIM(first_name) != ''")
                            ->count();
                        $consumedTicketsArray[$ticketType] = $count;
                    }
                    $app->consumedTickets = $consumedTicketsArray;
                } else {
                    // Set default values when there's no exhibitionParticipant
                    $app->consumedStallManning = 0;
                    $app->consumedComplimentary = 0;
                    $app->consumedTickets = [];
                }
            }



            // Calculate totals
            $applicationsData = $query->get();
            $totalTicketAllocations = 0;
            $totalStallManning = 0;
            $totalComplimentaryDelegates = 0;
            
            foreach ($applicationsData as $app) {
                if ($app->exhibitionParticipant && $app->exhibitionParticipant->id) {
                    // Calculate ticket allocations
                    try {
                        $tickets = $app->exhibitionParticipant->tickets();
                        if ($tickets) {
                            $totalTicketAllocations += collect($tickets)->sum('count');
                        }
                    } catch (\Exception $e) {
                        // Handle case where tickets() might fail
                        Log::warning('Error calculating tickets for application ' . $app->id . ': ' . $e->getMessage());
                    }
                    
                    // Sum stall manning count (handle null/empty)
                    $stallManningCount = $app->exhibitionParticipant->stall_manning_count ?? 0;
                    $totalStallManning += is_numeric($stallManningCount) ? (int)$stallManningCount : 0;
                    
                    // Sum complimentary delegate count (handle null/empty)
                    $complimentaryCount = $app->exhibitionParticipant->complimentary_delegate_count ?? 0;
                    $totalComplimentaryDelegates += is_numeric($complimentaryCount) ? (int)$complimentaryCount : 0;
                }
            }
            
            $totalStats = [
                'total_exhibitors' => $applicationsData->count(),
                'total_stall_manning' => $totalStallManning,
                'total_complimentary_delegates' => $totalComplimentaryDelegates,
                'total_ticket_allocations' => $totalTicketAllocations,
            ];
            /*
            To-DO
            handle the exhibitor passes allocation
            */ 

            // Get all available tickets for the modal (unique ticket types)
            $availableTickets = \App\Models\Ticket::where('status', 1)
                ->select('ticket_type')
                ->distinct()
                ->get()
                ->map(function($ticket) {
                    // Get the first ticket of each type for reference
                    $firstTicket = \App\Models\Ticket::where('status', 1)
                        ->where('ticket_type', $ticket->ticket_type)
                        ->first();
                    
                    return (object) [
                        'id' => $firstTicket->id,
                        'ticket_type' => $ticket->ticket_type,
                        'nationality' => $firstTicket->nationality,
                        'early_bird_price' => $firstTicket->early_bird_price,
                        'normal_price' => $firstTicket->normal_price,
                    ];
                });
            
            
           
            return view('passes.allocation', compact('applications', 'search', 'totalStats', 'availableTickets'));
        } catch (\Exception $e) {
            Log::error('Error in passes allocation view', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'sort_field' => $request->get('sort'),
                'sort_order' => $request->get('order'),
                'search' => $request->get('search')
            ]);

            return back()->with('error', 'An error occurred while loading passes allocation data. Error: ' . $e->getMessage());
        }
    }

    /**
     * Update passes allocation for a specific company
     */
    public function updatePassesAllocation(Request $request)
    {
        try {
            $request->validate([
                'application_id' => 'required|integer|exists:applications,id',
                'stall_manning_count' => 'required|integer|min:0',
                // complimentary_delegate_count will be derived from ticket_allocations
                'ticket_allocations' => 'sometimes|array',
                'ticket_allocations.*' => 'integer|min:0',
            ]);

            $application = Application::with(['exhibitionParticipant'])->findOrFail($request->application_id);

            // Process ticket allocations
            $ticketAllocations = [];
            if ($request->has('ticket_allocations')) {
                foreach ($request->ticket_allocations as $ticketId => $count) {
                    if ($count > 0) {
                        $ticketAllocations[$ticketId] = $count;
                    }
                }
            }

            // Derive complimentary delegate count from ticket allocations (sum of counts)
            $complimentaryCount = array_sum($ticketAllocations);

            // Check if exhibitionParticipant exists, if not create it
            // Use updateOrCreate to handle both create and update in one call
            $wasRecentlyCreated = !ExhibitionParticipant::where('application_id', $application->id)->exists();
            
            $exhibitionParticipant = ExhibitionParticipant::updateOrCreate(
                ['application_id' => $application->id],
                [
                    'stall_manning_count' => $request->stall_manning_count ?? 0,
                    'complimentary_delegate_count' => $complimentaryCount ?? 0,
                    'ticketAllocation' => !empty($ticketAllocations) ? json_encode($ticketAllocations) : null,
                ]
            );

            // Reload the relationship to ensure it's fresh
            $application->load('exhibitionParticipant');

            // Calculate total ticket allocations
            $totalTicketAllocations = array_sum($ticketAllocations);
            $totalPasses = $request->stall_manning_count + $complimentaryCount + $totalTicketAllocations;

            // Log the update or creation
            $action = $wasRecentlyCreated ? 'created' : 'updated';
            \Log::info('Passes allocation ' . $action, [
                'application_id' => $application->id,
                'company_name' => $application->company_name,
                'exhibition_participant_id' => $exhibitionParticipant->id,
                'action' => $action,
                'stall_manning_count' => $request->stall_manning_count,
                'complimentary_delegate_count' => $complimentaryCount,
                'ticket_allocations' => $ticketAllocations,
                'total_ticket_allocations' => $totalTicketAllocations,
                'total_passes' => $totalPasses,
                'updated_by' => auth()->id(),
                'updated_at' => now()
            ]);

            $message = $wasRecentlyCreated 
                ? 'Passes allocation created successfully for ' . $application->company_name
                : 'Passes allocation updated successfully for ' . $application->company_name;

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'exhibition_participant_id' => $exhibitionParticipant->id,
                    'stall_manning_count' => $request->stall_manning_count,
                    'complimentary_delegate_count' => $complimentaryCount,
                    'ticket_allocations' => $ticketAllocations,
                    'total_ticket_allocations' => $totalTicketAllocations,
                    'total_passes' => $totalPasses,
                    'was_created' => $wasRecentlyCreated
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating passes allocation', [
                'error' => $e->getMessage(),
                'application_id' => $request->application_id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating passes allocation. Please try again.'
            ], 500);
        }
    }

    /**
     * Auto-allocate passes based on stall size for a specific company
     */
    public function autoAllocatePasses(Request $request)
    {
        try {
            $request->validate([
                'application_id' => 'required|integer|exists:applications,id',
            ]);

            $application = Application::with(['exhibitionParticipant'])->findOrFail($request->application_id);
            $stallSize = $application->allocated_sqm ?? 0;

            if ($stallSize < 9) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stall size must be at least 9 sqm to allocate passes.'
                ], 400);
            }

            // Pass allocation rules based on stall size
            $passAllocation = [
                ['min' => 9, 'max' => 17, 'passes' => 5],
                ['min' => 18, 'max' => 26, 'passes' => 10],
                ['min' => 27, 'max' => 54, 'passes' => 20],
                ['min' => 55, 'max' => 100, 'passes' => 30],
                ['min' => 101, 'max' => 400, 'passes' => 40],
                ['min' => 401, 'max' => PHP_INT_MAX, 'passes' => 50],
            ];

            // Find the correct pass count based on stall size
            $allocatedPasses = 0;
            foreach ($passAllocation as $range) {
                if ($stallSize >= $range['min'] && $stallSize <= $range['max']) {
                    $allocatedPasses = $range['passes'];
                    break;
                }
            }

            // Calculate complimentaryDelegateCount based on stall size
            if ($stallSize >= 9 && $stallSize < 36) {
                $complimentaryDelegateCount = 2;
            } elseif ($stallSize < 101) {
                $complimentaryDelegateCount = 5;
            } elseif ($stallSize >= 101) {
                $complimentaryDelegateCount = 10;
            } else {
                $complimentaryDelegateCount = 0;
            }

            // Check if exhibitionParticipant exists, if not create it
            if (!$application->exhibitionParticipant) {
                $exhibitionParticipant = new ExhibitionParticipant();
                $exhibitionParticipant->application_id = $application->id;
                $exhibitionParticipant->stall_manning_count = $allocatedPasses;
                $exhibitionParticipant->complimentary_delegate_count = $complimentaryDelegateCount;
                $exhibitionParticipant->save();
            } else {
                // Update existing exhibitionParticipant
                $application->exhibitionParticipant->stall_manning_count = $allocatedPasses;
                $application->exhibitionParticipant->complimentary_delegate_count = $complimentaryDelegateCount;
                $application->exhibitionParticipant->save();
            }

            // Log the auto-allocation
            \Log::info('Passes auto-allocated based on stall size', [
                'application_id' => $application->id,
                'company_name' => $application->company_name,
                'stall_size' => $stallSize,
                'stall_manning_count' => $allocatedPasses,
                'complimentary_delegate_count' => $complimentaryDelegateCount,
                'allocated_by' => auth()->id(),
                'allocated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Passes auto-allocated successfully for ' . $application->company_name . ' based on ' . $stallSize . ' sqm stall size',
                'data' => [
                    'stall_size' => $stallSize,
                    'stall_manning_count' => $allocatedPasses,
                    'complimentary_delegate_count' => $complimentaryDelegateCount,
                    'total_passes' => $allocatedPasses + $complimentaryDelegateCount
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error auto-allocating passes', [
                'error' => $e->getMessage(),
                'application_id' => $request->application_id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while auto-allocating passes. Please try again.'
            ], 500);
        }
    }


}
