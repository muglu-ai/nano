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
use App\Mail\InviteMail;
use App\Models\ExhibitionParticipant;
use App\Helpers\TicketAllocationHelper;
use App\Models\ComplimentaryDelegate;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use App\Models\AttendeeLog;
use App\Models\Attendee;
use App\Models\Ticket;
use App\Models\Invoice;
use App\Models\AssociationPricingRule;


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

        // Filter by specific exhibitor when id is provided
        $filterExhibitionParticipantId = $request->get('exhibition_participant_id') ?? $request->get('exhibitorparticipant_id');
        if (!empty($filterExhibitionParticipantId)) {
            $complimentaryQuery->where('exhibition_participant_id', (int) $filterExhibitionParticipantId);
        }

        // Report mode: show grouped data by exhibitor with allocated/used and registrants
        if ($request->boolean('report')) {
            // Get unique exhibition participant IDs from current query
            $epIds = (clone $complimentaryQuery)->select('exhibition_participant_id')->distinct()->pluck('exhibition_participant_id');
            
            $reportRows = collect();
            if ($epIds->count() > 0) {
                // Load EPs with related application once
                $eps = ExhibitionParticipant::with('application')->whereIn('id', $epIds)->get()->keyBy('id');
                
                foreach ($epIds as $epId) {
                    $ep = $eps->get($epId);
                    if (!$ep) {
                        continue;
                    }
                    // Allocated passes from ticketAllocation JSON (complimentary allocation)
                    $allocated = 0;
                    if (!empty($ep->ticketAllocation)) {
                        $alloc = json_decode($ep->ticketAllocation, true);
                        if (is_array($alloc)) {
                            $allocated = array_sum($alloc);
                        }
                    }
                    // Used passes = registered complimentary delegates for this EP
                    $used = ComplimentaryDelegate::where('exhibition_participant_id', $epId)
                        ->whereNotNull('first_name')
                        ->whereRaw("TRIM(first_name) != ''")
                        ->count();
                    // Registrations list
                    $registrations = ComplimentaryDelegate::where('exhibition_participant_id', $epId)
                        ->whereNotNull('first_name')
                        ->whereRaw("TRIM(first_name) != ''")
                        ->orderBy('first_name', 'asc')
                        ->get(['first_name', 'middle_name', 'last_name', 'email', 'mobile', 'organisation_name', 'unique_id', 'ticketType']);
                    
                    $reportRows->push((object)[
                        'company_name' => $ep->application->company_name ?? ($registrations->first()->organisation_name ?? 'N/A'),
                        'exhibition_participant_id' => $epId,
                        'allocated_passes' => $allocated,
                        'used_passes' => $used,
                        'registrations' => $registrations,
                    ]);
                }
            }
            
            $slug = "Complimentary Passes Report";
            return view('admin.stall-manning.complimentary-report', compact('reportRows', 'slug'));
        }

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


        // unreachable
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
        ini_set('memory_limit', '512M');
        set_time_limit(0);

        // Keep log as per instruction, do not remove
        $this->logExportingPassesToFile($request);

        $data = collect();

        // Getting all entries for ComplimentaryDelegate
        $delegates = ComplimentaryDelegate::with(['exhibitionParticipant.application'])
            ->select(
                'id',
                'exhibition_participant_id',
                'unique_id',
                'first_name',
                'email',
                'mobile',
                'job_title',
                'organisation_name',
                'created_at',
                'id_type',
                'id_no',
                'ticketType'
            )
            ->whereNotNull('first_name')
            ->where('first_name', '!=', '')
            ->get();

        foreach ($delegates as $row) {
            // Handle null relations gracefully
            $exhibitorName = '';
            if (
                $row->relationLoaded('exhibitionParticipant') &&
                $row->exhibitionParticipant &&
                $row->exhibitionParticipant->relationLoaded('application') &&
                $row->exhibitionParticipant->application
            ) {
                $exhibitorName = $row->exhibitionParticipant->application->company_name;
            }
            $data->push([
                'Type' => $row->ticketType,
                'ID' => $row->unique_id,
                'Name' => $row->first_name,
                'Email' => $row->email,
                'Mobile' => ltrim($row->mobile, '+'),
                'Job Title' => $row->job_title,
                'Organisation' => $row->organisation_name,
                'Exhibitor Name' => $exhibitorName,
            ]);
        }

        $filename = 'exhibitor_passes_' . date('Ymd_His') . '.xlsx';

        // Export via Excel. Headings and data are dynamically generated.
        return \Maatwebsite\Excel\Facades\Excel::download(
            new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
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
                        'Exhibitor Name',
                    ];
                }
            },
            $filename
        );
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


    /**
     * Sync and find applications that are paid/complimentary but don't have passes allocated
     */
    public function syncPassesAllocation(Request $request)
    {
        try {
            // Find applications that are:
            // 1. Paid (invoice payment_status = 'paid' OR partial with 40%+ paid)
            // 2. Complimentary (has promocode with is_complimentary = true)
            // 3. Don't have ExhibitionParticipant OR have ExhibitionParticipant but no passes allocated
            
            $paidApplications = Application::whereHas('invoice', function ($query) {
                $query->where(function ($q) {
                    $q->where('payment_status', 'paid')
                      ->orWhere(function ($partial) {
                          $partial->where('payment_status', 'partial')
                                  ->whereRaw('(amount_paid >= (amount * 0.4) OR amount_paid >= (total_final_price * 0.4))');
                      });
                });
            })
            ->where(function ($query) {
                $query->where('allocated_sqm', '>', 0)
                    ->orWhere('allocated_sqm', '=', 'Startup Booth')
                    ->orWhere('allocated_sqm', '=', 'Booth / POD')
                    ->orWhere('application_type', '=', 'exhibitor')
                    ->orWhere('application_type', '=', 'pavilion')
                    ->orWhere('application_type', '=', 'sponsor')
                    ->orWhere('application_type', '=', 'sponsor+exhibitor');
            })
            ->where(function ($query) {
                $query->whereDoesntHave('exhibitionParticipant')
                      ->orWhereHas('exhibitionParticipant', function ($ep) {
                          $ep->where(function ($q) {
                              $q->where(function ($count) {
                                  // Check if ticketAllocation is empty or null
                                  $count->where(function($q) {
                                      $q->whereNull('ticketAllocation')
                                        ->orWhere('ticketAllocation', '=', '')
                                        ->orWhereRaw("JSON_LENGTH(ticketAllocation) = 0");
                                  });
                              })
                              ->where(function ($ticket) {
                                  $ticket->whereNull('ticketAllocation')
                                         ->orWhere('ticketAllocation', '=', '')
                                         ->orWhereRaw("TRIM(ticketAllocation) = ''");
                              });
                          });
                      });
            })
            ->whereIn('submission_status', ['approved', 'submitted'])
            ->with(['invoice', 'exhibitionParticipant'])
            ->get();

            // Find complimentary applications
            $complimentaryApplications = Application::whereNotNull('promocode')
                ->where(function ($query) {
                    $query->where('allocated_sqm', '>', 0)
                        ->orWhere('allocated_sqm', '=', 'Startup Booth')
                        ->orWhere('allocated_sqm', '=', 'Booth / POD')
                        ->orWhere('application_type', '=', 'exhibitor')
                        ->orWhere('application_type', '=', 'pavilion')
                        ->orWhere('application_type', '=', 'sponsor')
                        ->orWhere('application_type', '=', 'sponsor+exhibitor');
                })
                ->where(function ($query) {
                    $query->whereDoesntHave('exhibitionParticipant')
                          ->orWhereHas('exhibitionParticipant', function ($ep) {
                              $ep->where(function ($q) {
                                  $q->where(function ($count) {
                                      // Check if ticketAllocation is empty or null
                                      $count->where(function($q) {
                                          $q->whereNull('ticketAllocation')
                                            ->orWhere('ticketAllocation', '=', '')
                                            ->orWhereRaw("JSON_LENGTH(ticketAllocation) = 0");
                                      });
                                  })
                                  ->where(function ($ticket) {
                                      $ticket->whereNull('ticketAllocation')
                                             ->orWhere('ticketAllocation', '=', '')
                                             ->orWhereRaw("TRIM(ticketAllocation) = ''");
                                  });
                              });
                          });
                })
                ->whereIn('submission_status', ['approved', 'submitted'])
                ->with(['invoice', 'exhibitionParticipant'])
                ->get()
                ->filter(function ($app) {
                    // Check if promocode is for a complimentary association
                    if ($app->promocode) {
                        $association = AssociationPricingRule::where('promocode', $app->promocode)
                            ->where('is_complimentary', true)
                            ->active()
                            ->valid()
                            ->first();
                        return $association !== null;
                    }
                    return false;
                });

            // Merge and deduplicate by application id
            $allApplications = $paidApplications->merge($complimentaryApplications)
                ->unique('id')
                ->values();

            // Mark each application with its status
            foreach ($allApplications as $app) {
                $app->is_paid = $app->invoice && in_array($app->invoice->payment_status, ['paid', 'partial']);
                $app->is_complimentary = false;
                
                if ($app->promocode) {
                    $association = AssociationPricingRule::where('promocode', $app->promocode)
                        ->where('is_complimentary', true)
                        ->active()
                        ->valid()
                        ->first();
                    $app->is_complimentary = $association !== null;
                }
                
                $app->needs_allocation = !$app->exhibitionParticipant || 
                    ($app->exhibitionParticipant && 
                     (empty($app->exhibitionParticipant->ticketAllocation) && 
                      empty($app->exhibitionParticipant->ticketAllocation)));
            }

            return response()->json([
                'success' => true,
                'count' => $allApplications->count(),
                'applications' => $allApplications->map(function ($app) {
                    return [
                        'id' => $app->id,
                        'application_id' => $app->application_id,
                        'company_name' => $app->company_name,
                        'is_paid' => $app->is_paid ?? false,
                        'is_complimentary' => $app->is_complimentary ?? false,
                        'allocated_sqm' => $app->allocated_sqm,
                        'stall_category' => $app->stall_category,
                        'has_exhibition_participant' => $app->exhibitionParticipant !== null,
                        'needs_allocation' => $app->needs_allocation ?? true,
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Error syncing passes allocation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while syncing: ' . $e->getMessage()
            ], 500);
        }
    }

    public function passesAllocation(Request $request)
    {
        // Get search query and sorting parameters
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 15);
        $sortField = $request->get('sort', 'company_name');
        $sortOrder = $request->get('order', 'asc');
        
        // Check if sync was requested
        $syncRequested = $request->has('sync') && $request->get('sync') === '1';

        try {

            // Query for approved applications with passes allocation
            // Include applications with exhibitionParticipant that have passes allocated
            // OR applications without exhibitionParticipant (so admin can add/update passes)
            $query = Application::with(['exhibitionParticipant', 'user', 'billingDetail'])
                // ->where('submission_status', 'approved')
                ->where(function ($query) {
                    $query->where('allocated_sqm', '>', 0)
                        ->orWhere('allocated_sqm', '=', 'Startup Booth')
                        ->orWhere('allocated_sqm', '=', 'Booth / POD')
                        ->orWhere('application_type', '=', 'exhibitor')
                        ->orWhere('application_type', '=', 'pavilion')
                        ->orWhere('application_type', '=', 'sponsor')
                        //sponsor+exhibitor
                        ->orWhere('application_type', '=', 'sponsor+exhibitor')
                        ;
                })
                ->where(function ($query) {
                    // Applications that have exhibitionParticipant with passes allocated
                    $query->whereHas('exhibitionParticipant', function ($ep) {
                        $ep->where(function ($inner) {
                            $inner->where(function ($count) {
                                // Check if ticketAllocation has data
                                $count->whereNotNull('ticketAllocation')
                                    ->where('ticketAllocation', '!=', '')
                                    ->whereRaw("JSON_LENGTH(ticketAllocation) > 0");
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

                    // dd($query->toSql());
                    // dd($query->get());
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
                case 'ticketAllocation':
                    // Sort by whether ticketAllocation exists and has data
                    $query->orderByRaw("CASE WHEN ticketAllocation IS NULL OR ticketAllocation = '' OR JSON_LENGTH(ticketAllocation) = 0 THEN 1 ELSE 0 END");
                    break;
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
                    // Calculate counts from ticketAllocation JSON using helper
                    try {
                        $countsData = TicketAllocationHelper::getCountsFromAllocation($app->id);
                        $totalTicketAllocations += $countsData['total_allocated'] ?? 0;
                        $totalStallManning += $countsData['stall_manning_count'] ?? 0;
                        $totalComplimentaryDelegates += $countsData['complimentary_delegate_count'] ?? 0;
                    } catch (\Exception $e) {
                        // Handle case where calculation might fail
                        Log::warning('Error calculating counts for application ' . $app->id . ': ' . $e->getMessage());
                    }
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

            // Get all available ticket types for the modal (using new TicketType model)
            $availableTickets = \App\Models\Ticket\TicketType::where('is_active', true)
                ->with(['category', 'subcategory', 'event'])
                ->orderBy('name')
                ->get()
                ->map(function($ticketType) {
                    return (object) [
                        'id' => $ticketType->id,
                        'ticket_type' => $ticketType->name,
                        'slug' => $ticketType->slug,
                        'category' => $ticketType->category->name ?? 'N/A',
                        'subcategory' => $ticketType->subcategory->name ?? null,
                        'regular_price' => $ticketType->regular_price ?? 0,
                        'early_bird_price' => $ticketType->early_bird_price ?? null,
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
                'ticket_allocations' => 'required|array',
                'ticket_allocations.*' => 'integer|min:0',
            ]);

            $application = Application::with(['exhibitionParticipant'])->findOrFail($request->application_id);

            // Process ticket allocations - filter out zero counts
            $ticketAllocations = [];
            if ($request->has('ticket_allocations')) {
                foreach ($request->ticket_allocations as $ticketTypeId => $count) {
                    if ($count > 0) {
                        $ticketAllocations[$ticketTypeId] = $count;
                    }
                }
            }

            // Use TicketAllocationHelper to allocate
            $wasRecentlyCreated = !ExhibitionParticipant::where('application_id', $application->id)->exists();
            
            $exhibitionParticipant = TicketAllocationHelper::allocate(
                $application->id,
                $ticketAllocations
            );

            // Reload the relationship to ensure it's fresh
            $application->load('exhibitionParticipant');

            // Get counts from allocation (calculated from JSON)
            $countsData = TicketAllocationHelper::getCountsFromAllocation($application->id);
            $totalTicketAllocations = $countsData['total_allocated'] ?? 0;
            // Get counts from ticketAllocation JSON (calculated by helper)
            $countsData = TicketAllocationHelper::getCountsFromAllocation($application->id);
            $stallManningCount = $countsData['stall_manning_count'] ?? 0;
            $complimentaryCount = $countsData['complimentary_delegate_count'] ?? 0;
            $totalPasses = $totalTicketAllocations;

            // Log the update or creation
            $action = $wasRecentlyCreated ? 'created' : 'updated';
            \Log::info('Passes allocation ' . $action, [
                'application_id' => $application->id,
                'company_name' => $application->company_name,
                'exhibition_participant_id' => $exhibitionParticipant->id,
                'action' => $action,
                'ticket_allocations' => $ticketAllocations,
                'total_ticket_allocations' => $totalTicketAllocations,
                'stall_manning_count' => $stallManningCount,
                'complimentary_delegate_count' => $complimentaryCount,
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
                    'ticket_allocations' => $ticketAllocations,
                    'total_ticket_allocations' => $totalTicketAllocations,
                    'stall_manning_count' => $stallManningCount,
                    'complimentary_delegate_count' => $complimentaryCount,
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

            // Use TicketAllocationHelper to auto-allocate based on booth area
            try {
                $exhibitionParticipant = TicketAllocationHelper::autoAllocateAfterPayment(
                    $application->id,
                    $stallSize,
                    $application->event_id ?? null,
                    $application->application_type ?? null
                );

                // Get counts from allocation (calculated from JSON)
                $countsData = TicketAllocationHelper::getCountsFromAllocation($application->id);
                $stallManningCount = $countsData['stall_manning_count'] ?? 0;
                $complimentaryCount = $countsData['complimentary_delegate_count'] ?? 0;
                $totalAllocated = $countsData['total_allocated'] ?? 0;

                // Log the auto-allocation
                \Log::info('Passes auto-allocated based on stall size', [
                    'application_id' => $application->id,
                    'company_name' => $application->company_name,
                    'stall_size' => $stallSize,
                    'stall_manning_count' => $stallManningCount,
                    'complimentary_delegate_count' => $complimentaryCount,
                    'total_allocated' => $totalAllocated,
                    'allocated_by' => auth()->id(),
                    'allocated_at' => now()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Passes auto-allocated successfully for ' . $application->company_name . ' based on ' . $stallSize . ' sqm stall size',
                    'data' => [
                        'stall_size' => $stallSize,
                        'stall_manning_count' => $stallManningCount,
                        'complimentary_delegate_count' => $complimentaryCount,
                        'total_allocated' => $totalAllocated,
                        'total_passes' => $totalAllocated
                    ]
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to auto-allocate using helper', [
                    'application_id' => $application->id,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
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

    /**
     * Resend invite emails to complimentary delegates who haven't registered yet
     * (first_name is null but token is not null)
     */
    public function resendInviteEmails(Request $request)
    {
        try {
            // Find all complimentary delegates who have been invited but haven't registered
            $pendingInvites = DB::table('complimentary_delegates')
                ->whereNull('first_name')
                ->whereNotNull('token')
                ->where('token', '!=', '')
                ->get();

            if ($pendingInvites->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No pending invites found to resend.',
                    'count' => 0
                ]);
            }

            $sentCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($pendingInvites as $delegate) {
                try {
                    // Get the company name from the application
                    $companyName = Application::whereHas('exhibitionParticipant', function ($query) use ($delegate) {
                        $query->where('id', $delegate->exhibition_participant_id);
                    })->value('company_name') ?? '';

                    // Determine delegate type based on ticketType or default to 'delegate'
                    $delegateType = $delegate->ticketType ?? 'delegate';

                    //render the email view
                    //can we render the email view

                    
                    // Render the email view with the required variables to preview or generate the HTML (optional - for logging/debug/testing)
                    // $emailView = view('emails.invitee', [
                    //     'companyName' => $companyName,
                    //     'delegateType' => $delegateType,
                    //     'token' => $delegate->token,
                    //     'email' => $delegate->email,
                    // ])->render();

                    // echo $emailView;

                    // exit;

                    // Optionally, you could log or inspect $emailView here for debugging
                    // Log::info('Rendered invite email view', ['email' => $delegate->email, 'view' => $emailView]);


                    // Send the invite email
                    Mail::to($delegate->email)
                    ->bcc('test.interlinks@gmail.com')
                    ->send(new InviteMail($companyName, $delegateType, $delegate->token));
                    
                    $sentCount++;

                    // Log the resend
                    Log::info('Resent invite email to complimentary delegate', [
                        'email' => $delegate->email,
                        'exhibition_participant_id' => $delegate->exhibition_participant_id,
                        'token' => $delegate->token,
                        'sent_by' => auth()->id()
                    ]);

                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = [
                        'email' => $delegate->email,
                        'error' => $e->getMessage()
                    ];

                    Log::error('Failed to resend invite email', [
                        'email' => $delegate->email,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Invite emails processed. Sent: {$sentCount}, Failed: {$failedCount}",
                'sent_count' => $sentCount,
                'failed_count' => $failedCount,
                'total_pending' => $pendingInvites->count(),
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Error in resendInviteEmails', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while resending invite emails: ' . $e->getMessage()
            ], 500);
        }
    }


}
