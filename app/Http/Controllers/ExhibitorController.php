<?php

namespace App\Http\Controllers;

use App\Mail\InviteMail;
use App\Models\Application;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Country;
use App\Models\ExhibitionParticipant;
use App\Models\ComplimentaryDelegate;
use App\Models\StallManning;
use App\Mail\ExhibitorMail;
use Illuminate\Validation\Rule;
use App\Http\Controllers\ApiRelayController;
use App\Mail\InauguralMail;
use App\Services\EmailService;
use App\Models\Ticket;
use NunoMaduro\Collision\Adapters\Phpunit\ConfigureIO;

class ExhibitorController extends Controller
{

    public function __construct()
    {
        if (auth()->check() && auth()->user()->role == 'exhibitor') {
            return redirect('/login');
        }
    }
    //Show List of Complimentary Exhibitors


    //check whether the has application and check count from exhibition_participants table
    public function checkCount()
    {
        if (auth()->user()->role === 'exhibitor') {
            $application = Application::where('user_id', auth()->user()->id)
                ->where('submission_status', 'approved')
                // ->whereHas('invoices.payments', function ($query) {
                //     $query->where('status', 'successful');
                // })
                ->first();

            if (!$application) {
                return redirect()->route('application.exhibitor');
            }

            $application_id = $application->id;
        } else {
            // Assuming you have a CoExhibitor model and it has a relation to Application
            $coExhibitor = \App\Models\CoExhibitor::where('user_id', auth()->user()->id)->first();

            if (!$coExhibitor || !$coExhibitor->application) {
                return redirect()->route('application.exhibitor');
            }

            $application = $coExhibitor->application;
            $application_id = $coExhibitor->id;
        }



        //$exhibitionParticipantCount = $application->exhibitionParticipant()->count();
        // Default: get exhibition participant from application relation
        $exhibitionParticipant = $application->exhibitionParticipant;

        // If user is co-exhibitor, find exhibition participant by coExhibitor_id
        if (auth()->user()->role === 'co-exhibitor') {
            $userID = auth()->user()->id;
            $coExhibitorId = \App\Models\CoExhibitor::where('user_id', $userID)->value('id');

            $exhibitionParticipant = \App\Models\ExhibitionParticipant::where('coExhibitor_id', $coExhibitorId)->first();
        }

        // The ticketAllocation field contains a JSON string like {"1": 5, "2": 3, "3": 0}, where keys are ticket IDs and values are counts.
        // To process it, decode the JSON, then for each ticketId, fetch the Ticket model and add the count to a result array with ticket type and count.

        $ticketAllocationResult = [];
        if ($exhibitionParticipant && !empty($exhibitionParticipant->ticketAllocation)) {
            $ticketAllocations = json_decode($exhibitionParticipant->ticketAllocation, true);

            if (is_array($ticketAllocations)) {
                foreach ($ticketAllocations as $ticketId => $count) {
                    // Fetch the Ticket model, handle missing ticket gracefully
                    $ticket = Ticket::find($ticketId);
                    $ticketType = $ticket ? $ticket->ticket_type : 'Unknown';

                    $ticketAllocationResult[] = [
                        'ticket_id' => $ticketId,
                        'ticket_type' => $ticketType,
                        'count' => $count,
                        'slug' => Str::slug($ticket->ticket_type, '-'),
                    ];
                }
            }
        }


        $count = [
            'stall_manning_count' => $exhibitionParticipant ? $exhibitionParticipant->stall_manning_count : 0,
            'complimentary_delegate_count' => $exhibitionParticipant ? $exhibitionParticipant->complimentary_delegate_count : 0,
            'application' => $application_id,
            'ticket_allocation' => $ticketAllocationResult,
            'exhibition_participant_id' => $exhibitionParticipant ? $exhibitionParticipant->id : null,
        ];

        return $count;
    }

    //user accepts the Co-Exhibitor Terms
    public function acceptTerms(Request $request)
    {

        ##Log::info('Accept Terms Request', $request->all());

        try {
            // Validate the request
            $validated = $request->validate([
                'accept_terms' => 'required|boolean',
            ]);

            // Check if the user has an application
            $application = Application::where('user_id', auth()->user()->id)
                ->where('submission_status', 'approved')
                // ->whereHas('invoices.payments', function ($query) {
                //     $query->where('status', 'successful');
                // })
                ->first();

            if (!$application) {
                return response()->json(['error' => 'No approved application found.'], 404);
            }

            //if already accepted then return error
            if ($application->coex_terms_accepted) {
                return response()->json(['error' => 'Terms already accepted.'], 400);
            }

            // Update the exhibition participant's terms acceptance
            $application->coex_terms_accepted = true;
            $application->save();

            return response()->json(['success' => true, 'message' => 'Terms accepted.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Accept Terms Error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    //get the count of filled complimentary and delegate count from the exhibition_participants table, complimentary_delegates table and complimentary_delegates with the exhibition_participant_id
    public function usedcount()
    {
        $this->__Construct();
        $count = $this->checkCount();
        $complimentaryDelegates = DB::table('complimentary_delegates')
            ->where('exhibition_participant_id', $count['exhibition_participant_id'])
            ->count();

        $stallManning = DB::table('stall_manning')
            ->where('exhibition_participant_id', $count['exhibition_participant_id'])
            ->count();

        return [
            'complimentary_delegates' => $complimentaryDelegates,
            'stall_manning' => $stallManning,
        ];
    }



    public function list(Request $request, $type)
    {
        $this->__Construct();
        $count = $this->checkCount();

        //get the user application id
        $application = Application::where('user_id', auth()->user()->id)
            ->where('submission_status', 'approved')
            ->first();

        if (!$application) {
            return redirect('/dashboard');
        }

        $sortField = $request->input('sort', 'first_name');
        $sortDirection = $request->input('direction', 'asc');
        $perPage = $request->input('per_page', 50);

        // Default data and slug
        $data = [];
        $slug = $type;
        $used = $this->usedcount();
        $ticketId = null;

        if ($type == 'inaugural_passes') {
            $ticketName = 'Inaugural Passes';
            $slug = 'inaugural_passes';
            $allocated = $count['complimentary_delegate_count'] ?? 0;
            $usedCount = $used['complimentary_delegates'] ?? 0;

            $ticketId = $slug;

            $data = DB::table('complimentary_delegates')
                ->where('exhibition_participant_id', $count['exhibition_participant_id'])
                ->orderBy($sortField, $sortDirection)
                ->paginate($perPage);
        } elseif ($type == 'stall_manning') {
            $slug = 'stall_manning';
            $ticketId = 11;
            $ticketName = 'Stall Manning';
            $allocated = $count['stall_manning_count'] ?? 0;
            $usedCount = $used['stall_manning'] ?? 0;
            $data = DB::table('stall_manning')
                ->where('exhibition_participant_id', $count['exhibition_participant_id'])
                ->orderBy($sortField, $sortDirection)
                ->paginate($perPage);
        } else {
            // Check for custom ticket type by slug
            $ticket = collect($count['ticket_allocation'] ?? [])->firstWhere('slug', $type);

            //dd($ticket);
            if ($ticket) {
                $slug = $ticket['slug'];
                $ticketName = $ticket['ticket_type'];
                $allocated = $ticket['count'];
                $ticketId = $ticket['ticket_id'];
                $usedCount = DB::table('complimentary_delegates')
                    ->where('exhibition_participant_id', $count['exhibition_participant_id'])
                    ->where('ticketType', $ticket['ticket_type'])
                    ->count();

                $data = DB::table('complimentary_delegates')
                    ->where('exhibition_participant_id', $count['exhibition_participant_id'])
                    ->where('ticketType', $ticket['ticket_type'])
                    ->orderBy($sortField, $sortDirection)
                    ->paginate($perPage);
                // If you have a table for custom tickets, fetch data here. Otherwise, show empty or message.
                // Example: $data = DB::table('custom_ticket_table')->where(...)->paginate($perPage);
                //                $data = [];
            } else {
                return response()->json(['error' => 'Invalid type'], 400);
            }
        }

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        //dd($data);


        $companyName = $application ? $application->company_name : null;

        return view('exhibitor.delegates_list', compact('data', 'slug', 'count', 'used', 'companyName', 'ticketName', 'allocated', 'usedCount', 'ticketId'));
    }
    public function list2(Request $request, $type)
    {
        $this->__Construct();
        $count = $this->checkCount();

        //get the user application id
        $application = Application::where('user_id', auth()->user()->id)
            ->where('submission_status', 'approved')
            // ->whereHas('invoices.payments', function ($query) {
            //     $query->where('status', 'successful');
            // })
            ->first();

        //if no application then redirect to /dashboard
        if (!$application) {
            return redirect('/dashboard');
        }


        $sortField = $request->input('sort', 'first_name'); // Default sort by 'name'
        $sortDirection = $request->input('direction', 'asc'); // Default sort 'asc'
        $perPage = $request->input('per_page', 10); // Default 10 items per page


        if ($type == 'complimentary') {
            $slug = 'Exhibitor Passes';
            $data = DB::table('complimentary_delegates')
                ->where('exhibition_participant_id', $this->checkCount()['exhibition_participant_id'])
                ->orderBy($sortField, $sortDirection)
                ->paginate($perPage);
        } elseif ($type == 'stall_manning') {
            $slug = 'Stall Manning';
            $data = DB::table('stall_manning')
                ->where('exhibition_participant_id', $this->checkCount()['exhibition_participant_id'])
                ->orderBy($sortField, $sortDirection)
                ->paginate($perPage);
        } else {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        // Check if it's an API request
        if ($request->wantsJson()) {
            return response()->json($data);
        }
        $count = $this->checkCount();
        $used = $this->usedcount();


        return view('exhibitor.delegates_list2', compact('data', 'slug', 'count', 'used'));
    }


    //invite delegates to the event
    public function invite2(Request $request)
    {
        $this->__Construct();


        $validatedData = $request->validate([
            'invite_type' => 'required|in:delegate,exhibitor',
            'email' => 'required|email|unique:complimentary_delegates|unique:stall_manning',
        ]);
        // check the count of complimentary_delegates or stall_manning table from exhibition_participants table
        //how many of registered delegates or exhibitors are there and it should not exceed the count of complimentary_delegate_count or stall_manning_count
        $count = $this->checkCount();


        //get the count of complimentary_delegates or stall_manning table from exhibition_participants table and
        //check how many has same exhibition_participant_id

        //if invite_type is delegate
        if ($request->invite_type == 'delegate') {
            $countComplimentaryDelegates = DB::table('complimentary_delegates')
                ->where('exhibition_participant_id', $count['exhibition_participant_id'])
                ->count();

            if ($countComplimentaryDelegates >= $count['complimentary_delegate_count']) {
                return redirect()->back()->with('error', 'You have reached the maximum limit of Exhibitor Passes');
            } else {
                // insert into complimentary_delegates table with email id and exhibition_participant_id also
                // generate a unique token through which the invitee can fill out the information
                $token = Str::random(32);
                DB::table('complimentary_delegates')->insert([
                    'email' => $request->email,
                    'exhibition_participant_id' => $count['exhibition_participant_id'],
                    'token' => $token,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // generate a unique token through which the invitee can fill out the information
                //Mail::to($request->email)->send(new InviteMail($token));
                return response()->json(['message' => 'Invitation sent successfully!']);
            }
        }
        if ($request->invite_type == 'exhibitor') {
            $countStallManning = DB::table('stall_manning')
                ->where('exhibition_participant_id', $count['application'])
                ->count();

            if ($countStallManning >= $count['stall_manning_count']) {
                return redirect()->back()->with('error', 'You have reached the maximum limit of stall manning');
            } else {
                // insert into stall_manning table with email id and exhibition_participant_id also
                // generate a unique token through which the invitee can fill out the information
                // insert into stall_manning table with email id and exhibition_participant_id also
                $token = Str::random(32);
                DB::table('stall_manning')->insert([
                    'email' => $request->email,
                    'exhibition_participant_id' => $count['application'],
                    'token' => $token,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);



                // generate a unique token through which the invitee can fill out the information
                //Mail::to($request->email)->send(new InviteMail($token));

                return response()->json(['message' => 'Invitation sent successfully!']);
            }
        }
    }

    public function invite(Request $request)
    {

        try {

            //if the invite_type and if it is other than delegate or exhibitor then get the ticket id from the ticket table
            $this->__Construct();
            $ticketId = $request->invite_type;

            if (!in_array($ticketId, ['delegate', 'exhibitor'])) {
                // Assume invite_type is a slug for a custom ticket
                $ticket = Ticket::find($ticketId);
                if (!$ticket) {
                    return response()->json(['error' => 'Invalid ticket type'], 400);
                }
                $ticketId = $ticket->ticket_type;
            }

            Log::info('Ticket Type: ' . $ticketId);


            // Validate request and return JSON error messages
            $validatedData = $request->validate([
                'invite_type' => 'required',
                'email' => [
                    'required',
                    // 'email',
                    'unique:complimentary_delegates,email',
                    'unique:stall_manning,email',
                    //                    'email',
                ],
            ]);



            // Fetch counts
            $count = $this->checkCount();
            $participantId = $count['exhibition_participant_id'];



            if ($request->invite_type == 'delegate') {
                $countComplimentaryDelegates = DB::table('complimentary_delegates')
                    ->where('exhibition_participant_id', $participantId)
                    ->count();

                if ($countComplimentaryDelegates >= $count['complimentary_delegate_count']) {
                    return response()->json(['error' => 'You have reached the maximum limit of Exhibitor Passes'], 422);
                }

                // Generate token and insert
                $token = Str::random(length: 32);
                DB::table('complimentary_delegates')->insert([
                    'email' => $request->email,
                    'exhibition_participant_id' => $participantId,
                    'token' => $token,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);



                // Find the exhibition_participant_id from the complimentary_delegates or stall_manning table
                $exhibitionParticipantId = $participantId;



                // Find the company name from the application table using exhibition_participant_id
                $companyName = Application::whereHas('exhibitionParticipant', function ($query) use ($exhibitionParticipantId) {
                    $query->where('id', $exhibitionParticipantId);
                })->value('company_name');
                //send an email to the invitee with the token and link as Route::get('/invited/{token}/', [ExhibitorController::class, 'invited'])->name('exhibition.invited');

                Mail::to($request->email)->send(new InviteMail($companyName, $request->invite_type, $token));


                return response()->json(['message' => 'Invitation sent successfully!']);
            }

            if ($request->invite_type == 'exhibitor') {
                Log::info("Invitation mail sent queue 6");
                $countStallManning = DB::table('stall_manning')
                    ->where('exhibition_participant_id', $participantId)
                    ->count();

                if ($countStallManning >= $count['stall_manning_count']) {
                    return response()->json(['error' => 'You have reached the maximum limit of stall manning'], 422);
                }

                // Generate token and insert
                $token = Str::random(32);
                DB::table('stall_manning')->insert([
                    'email' => $request->email,
                    'exhibition_participant_id' => $participantId,
                    'token' => $token,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Find the exhibition_participant_id from the complimentary_delegates or stall_manning table
                $exhibitionParticipantId = $participantId;


                // Find the company name from the application table using exhibition_participant_id
                $companyName = Application::whereHas('exhibitionParticipant', function ($query) use ($exhibitionParticipantId) {
                    $query->where('id', $exhibitionParticipantId);
                })->value('company_name');

                //send an email to the invitee with the token and link as Route::get('/invited/{token}/', [ExhibitorController::class, 'invited'])->name('exhibition.invited');
                Mail::to($request->email)->queue(new InviteMail($companyName, $request->invite_type, $token));


                return response()->json(['message' => 'Invitation sent successfully!']);
            }


            // handle for other than delegate and exhibitor ticket type
            if ($request->invite_type != 'delegate' && $request->invite_type != 'exhibitor') {

                $ticketAllocation  = $count['ticket_allocation'];


                $ticket = collect($ticketAllocation)->firstWhere('ticket_id', $request->invite_type);

                if ($ticket) {
                    $allocatedCount = $ticket['count'];
                    $usedCount = DB::table('complimentary_delegates')
                        ->where('exhibition_participant_id', $participantId)
                        ->where('ticketType', $ticketId ?? $request->invite_type)
                        ->count();

                    //

                    Log::info('Ticket Type ' . $request->invite_type . 'Used count: ' . $usedCount . ', Allocated count: ' . $allocatedCount);

                    if ($usedCount >= $allocatedCount) {
                        // Handle limit reached (e.g., return error)
                        return response()->json(['error' => 'You have reached the maximum limit for this ticket type'], 422);
                    }
                    // Proceed with invite logic
                } else {
                    return response()->json(['error' => 'Invalid ticket type'], 400);
                }
            
                $token = Str::random(length: 32);
                DB::table('complimentary_delegates')->insert([
                    'email' => $request->email,
                    'exhibition_participant_id' => $participantId,
                    'token' => $token,
                    'ticketType' => $ticketId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $companyName = Application::whereHas('exhibitionParticipant', function ($query) use ($participantId) {
                    $query->where('id', $participantId);
                })->value('company_name');


               

                Mail::to($request->email)->send(new InviteMail($companyName, $request->invite_type, $token));


                return response()->json(['message' => 'Invitation sent successfully!']);
            
            }

            return response()->json(['error' => 'Invalid request'], 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors in JSON format
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Log error and return JSON response
            Log::error('Invite error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    public function invited2($token = null)
    {
        $token = $token ?? request('token');
        //if token is not-found then redirect to /invited/not-found
        if ($token == 'not-found') {
            return redirect()->to('/invited/not-found');
        }
        $complimentaryDelegate = DB::table('complimentary_delegates')
            ->where('token', $token)
            ->first();
        //or stall_manning
        $stallManning = DB::table('stall_manning')
            ->where('token', $token)
            ->first();
        //if not found then set a flag to false
        $notFound = false;
        if (!$complimentaryDelegate && !$stallManning) {
            $notFound = true;
        }
        // Find the exhibition_participant_id from the complimentary_delegates or stall_manning table
        if (!$complimentaryDelegate && !$stallManning) {
            //return to invited/not-found
            // redirect('invited/not-found')
            return redirect()->to('/invited/not-found');
            return response()->json(['error' => 'Invalid token or participant not found'], 404);
        }
        $exhibitionParticipantId = $complimentaryDelegate ? $complimentaryDelegate->exhibition_participant_id : $stallManning->exhibition_participant_id;
        // Find the company name from the application table using exhibition_participant_id
        $companyName = Application::whereHas('exhibitionParticipant', function ($query) use ($exhibitionParticipantId) {
            $query->where('id', $exhibitionParticipantId);
        })->value('company_name');



        return view('exhibitor.invited', compact('notFound', 'companyName'));
    }

    public function invited($token = null)
    {
        $token = $token ?? request('token');

        // Prevent infinite redirection loop by checking explicitly for null or 'not-found'
        if (empty($token) || $token === 'not-found') {
            return response()->view('exhibitor.invited', ['notFound' => true], 404);
        }
        if ($token === 'success') {
            return response()->view('exhibitor.invited', ['notFound' => false, 'token' => 'success'], 400);
        }

        // Check if the token exists in either table
        $complimentaryDelegate = DB::table('complimentary_delegates')->where('token', $token)->first();
        $stallManning = DB::table('stall_manning')->where('token', $token)->first();

        // If no record is found, show 404 page instead of redirecting
        if (!$complimentaryDelegate && !$stallManning) {
            return response()->view('exhibitor.invited', ['notFound' => true], 404);
        }

        // Determine the exhibition_participant_id
        $exhibitionParticipantId = $complimentaryDelegate->exhibition_participant_id ?? $stallManning->exhibition_participant_id;

        // Find the company name from the application table
        $companyName = Application::whereHas('exhibitionParticipant', function ($query) use ($exhibitionParticipantId) {
            $query->where('id', $exhibitionParticipantId);
        })->value('company_name');

        $notFound = false;
        return view('exhibitor.invited', compact('companyName', 'notFound', 'token'));
    }
    public function invited_test($token = null)
    {
        $token = $token ?? request('token');

        // Prevent infinite redirection loop by checking explicitly for null or 'not-found'
        if (empty($token) || $token === 'not-found') {
            return response()->view('exhibitor.invited', ['notFound' => true], 404);
        }
        if ($token === 'success') {
            return response()->view('exhibitor.invited', ['notFound' => false, 'token' => 'success'], 400);
        }

        // Check if the token exists in either table
        $complimentaryDelegate = DB::table('complimentary_delegates')->where('token', $token)->first();
        $stallManning = DB::table('stall_manning')->where('token', $token)->first();

        // If no record is found, show 404 page instead of redirecting
        if (!$complimentaryDelegate) {
            return response()->view('exhibitor.invited', ['notFound' => true], 404);
        }

        // Get the email from the database record
        $inviteeEmail = $complimentaryDelegate->email;

        // Determine the exhibition_participant_id
        $exhibitionParticipantId = $complimentaryDelegate->exhibition_participant_id;

        // Find the company name from the application table
        $companyName = Application::whereHas('exhibitionParticipant', function ($query) use ($exhibitionParticipantId) {
            $query->where('id', $exhibitionParticipantId);
        })->value('company_name');

        $natureOfBusiness = config('constants.sectors');
        $natureOfBusiness = array_map(function ($sector) {
            return ['name' => $sector];
        }, $natureOfBusiness);



        // $maxAttendees = 5;
        $productCategories = config('constants.product_categories');
        $jobFunctions = config('constants.job_functions');
        $countries = Country::all();

        $notFound = false;
        return view('exhibitor.invited_new', compact('companyName', 'notFound', 'token', 'natureOfBusiness', 'productCategories', 'jobFunctions', 'countries', 'inviteeEmail'));
    }

    public function inviteeSubmitted(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'token' => [
                'required',
                function ($attribute, $value, $fail) {
                    $existsInComplimentary = DB::table('complimentary_delegates')->where('token', $value)->exists();
                    $existsInStallManning = DB::table('stall_manning')->where('token', $value)->exists();
                    if (!$existsInComplimentary && !$existsInStallManning) {
                        $fail('The selected token is invalid.');
                    }
                },
            ],
            'name' => 'required',
            'fullPhoneNumber' => 'required',
            'jobTitle' => 'required',
        ]);
        //remove the token from the table and insert the data into the table
        $complimentaryDelegate = DB::table('complimentary_delegates')
            ->where('token', $request->token)
            ->first();

        $stallManning = DB::table('stall_manning')
            ->where('token', $request->token)
            ->first();

        $uniqueId = "";
        // do {
        //         $randomNumber = rand(3200, 9999);
        //         $regId = 'EXAT' . $randomNumber;
        //     } while (DB::table('stall_manning')->where('pinNo', $regId)->exists());

        if ($complimentaryDelegate) {
            $uniqueId = $this->generateUniqueId();
            $pinNo = $this->generateCompPinNo();

            DB::table('complimentary_delegates')
                ->where('token', $request->token)
                ->update([
                    'unique_id' => $uniqueId,
                    'first_name' => $request->name,
                    'mobile' => $request->fullPhoneNumber,
                    'job_title' => $request->jobTitle,
                    'organisation_name' => $request->organisationName,

                    'token' => null,
                    'pinNo' => $pinNo,
                    'updated_at' => now(),
                ]);
        }
        if ($stallManning) {
            $email = $stallManning->email;
            $uniqueId = $this->generateStallManningUniqueId();
            $pinNo = $this->generateCompPinNo();
            DB::table('stall_manning')
                ->where('token', $request->token)
                ->update([
                    'unique_id' => $uniqueId,
                    'first_name' => $request->name,
                    'mobile' => $request->fullPhoneNumber,
                    'job_title' => $request->jobTitle,
                    'organisation_name' => $request->organisationName,
                    'token' => null,
                    'pinNo' => $pinNo,
                    'api_sent' => 0,
                    'updated_at' => now(),
                ]);
        }




        $email = $stallManning->email ?? $complimentaryDelegate->email;

        //send data to api
        //  $apiRelayController = new \App\Http\Controllers\ApiRelayController();
        //         $apiRelayController->sendDataToApi($uniqueId);


        //send an email to the invitee with the token and link as Route::get('/invited/{token}/', [ExhibitorController::class, 'invited'])->name('exhibition.invited');
        $data = [
            'name' => trim($request->name),
            'company_name' => $request->organisationName,
            'email' => $email,
            'mobile' => $request->fullPhoneNumber,
            // 'qr_code_path' => $attendee->qr_code_path,
            'unique_id' => $uniqueId,
            'ticket_type' => 'Exhibitor',
            'designation' => $request->jobTitle ?? '-',
            'registration_date' => $request->created_at,
            'registration_type' => 'Exhibitor',
        ];

        // Send the email
        if (!empty($email)) {
            Mail::to($email)
                ->bcc('test.interlinks@gmail.com')
                ->send(new ExhibitorMail($data));
        }
        //redirect to the invited with message of successful submission route('exhibition.invited', ['token' => $token]) with token as success
        return redirect()->route('exhibition.invited', ['token' => 'success']);
    }

    public function generateUniqueId()
    {
        do {
            $uniqueId = config('constants.COMPLIMENTARY_REG_ID_PREFIX') . '-' . Str::random(5);
            $existsInAttendees = DB::table('attendees')->where('unique_id', $uniqueId)->exists();
            $existsInComplimentary = DB::table('complimentary_delegates')->where('unique_id', $uniqueId)->exists();
            $existsInStallManning = DB::table('stall_manning')->where('unique_id', $uniqueId)->exists();
        } while ($existsInAttendees || $existsInComplimentary || $existsInStallManning);

        return strtoupper($uniqueId);
    }

    public function generateStallManningUniqueId()
    {
        do {
            $uniqueId = config('constants.COMPLIMENTARY_REG_ID_PREFIX') . '-' . Str::random(5);
            $existsInAttendees = DB::table('attendees')->where('unique_id', $uniqueId)->exists();
            $existsInComplimentary = DB::table('complimentary_delegates')->where('unique_id', $uniqueId)->exists();
            $existsInStallManning = DB::table('stall_manning')->where('unique_id', $uniqueId)->exists();
        } while ($existsInStallManning || $existsInAttendees || $existsInComplimentary);

        return strtoupper($uniqueId);
    }

    //GENERATE PIN NO FROM VALIDATING THE regId
    public function generateCompPinNo()
    {
        do {
            $pinNo = config('constants.CONFIRMATION_ID_PREFIX_EXH') . '-' . str::random(5); // Generate a random 6-digit number
            $existsInStallManning = DB::table('stall_manning')->where('pinNo', $pinNo)->exists();
        } while ($existsInStallManning);

        return strtoupper($pinNo);
    }

    //generate pinNo for complimentary delegates
    public function generateCompDelPinNo()
    {
        do {
            $pinNo = config('constants.DELEGATE_ID_PREFIX') . '-' . str::random(5); // Generate a random 6-digit number
            $existsInComplimentary = DB::table('complimentary_delegates')->where('pinNo', $pinNo)->exists();
        } while ($existsInComplimentary);

        return strtoupper($pinNo);
    }




    public function inauguralInviteeSubmitted(Request $request)
    {
        // dd($request->all());
        try {


            $validatedData = $request->validate([
                'token' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        $existsInComplimentary = DB::table('complimentary_delegates')->where('token', $value)->exists();
                        if (!$existsInComplimentary) {
                            $fail('The selected token is invalid.');
                        }
                    },
                ],
                'title' => 'required|string|max:25',
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:250',
                'last_name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    'string',
                    'max:255',
                ],
                'designation' => 'required|string|max:255',
                'company' => 'required|string|max:255',
                'address' => 'required|string',
                'country' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'postal_code' => 'required|string|max:25',
                'mobile' => 'required|string|max:25',
                'business_nature' => 'required|array',
                'products' => 'required|array',
                'id_card_number' => 'required|string|max:50',
                'id_card_type' => 'required|string|max:150',
                'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return all fields with autofilled values if validation fails
            $fields = [
                'token' => $request->input('token'),
                'title' => $request->input('title'),
                'first_name' => $request->input('first_name'),
                'middle_name' => $request->input('middle_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'designation' => $request->input('designation'),
                'company' => $request->input('company'),
                'address' => $request->input('address'),
                'country' => $request->input('country'),
                'state' => $request->input('state'),
                'city' => $request->input('city'),
                'postal_code' => $request->input('postal_code'),
                'mobile' => $request->input('mobile'),
                'business_nature' => $request->input('business_nature'),
                'products' => $request->input('products'),
                'id_card_number' => $request->input('id_card_number'),
                'id_card_type' => $request->input('id_card_type'),
                // profile_picture cannot be autofilled, so just pass null or old file if needed
            ];
            return back()
                ->withErrors($e->validator)
                ->withInput($fields);
        }


        // dd($request->all());
        //remove the token from the table and insert the data into the table
        $complimentaryDelegate = DB::table('complimentary_delegates')
            ->where('token', $request->token)
            ->first();

        // $stallManning = DB::table('stall_manning')
        //     ->where('token', $request->token)
        //     ->first();
        $uniqueId = $this->generateUniqueId();
        if ($complimentaryDelegate) {
            DB::table('complimentary_delegates')
                ->where('token', $request->token)
                ->update([
                    'title' => $request->title,
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                    // 'email' => $request->email,
                    'job_title' => $request->designation,
                    'organisation_name' => $request->company,
                    'address' => $request->address,
                    'country' => $request->country,
                    'state' => $request->state,
                    'city' => $request->city,
                    'postal_code' => $request->postal_code,
                    'mobile' => $request->mobile,
                    'buisness_nature' => $request->business_nature,
                    'products' => $request->products,
                    'id_no' => $request->id_card_number,
                    'id_type' => $request->id_card_type,
                    'profile_pic' => $request->hasFile('profile_picture')
                        ? $request->file('profile_picture')->storeAs(
                            'profile_pictures',
                            $uniqueId . '.' . $request->file('profile_picture')->getClientOriginalExtension(),
                            'public'
                        )
                        : null,
                    'unique_id' => $uniqueId,
                    'token' => null,
                    'updated_at' => now(),
                ]);
            // Prepare data for email
            $attendee = DB::table('complimentary_delegates')
                ->where('token', null)
                ->where('unique_id', $uniqueId)
                ->first();
            $data = [
                'name' => trim($attendee->first_name . ' ' . ($attendee->middle_name ?? '') . ' ' . $attendee->last_name),
                'company_name' => $attendee->organisation_name,
                'email' => $attendee->email,
                'mobile' => $attendee->mobile,
                // 'qr_code_path' => $attendee->qr_code_path,
                'unique_id' => $attendee->unique_id,
                'ticket_type' => $attendee->badge_category ?? 'Exhibitor',
                'designation' => $attendee->job_title ?? '-',
                'registration_date' => $attendee->created_at,
                'registration_type' => 'Exhibitor',
            ];

            // Send the email
            Mail::to($attendee->email)->send(new ExhibitorMail($data));

            //route to inaugural.invitee.thankyou with unique_id
            return redirect()->route('inaugural.invitee.thankyou', ['token' => $uniqueId]);
        }



        //redirect to thank you page with success message
        //redirect to the invited with message of successful submission route('exhibition.invited', ['token' => $token]) with token as success
        return redirect()->route('exhibition.invited', ['token' => 'success']);
    }

    //thankyou inaugural invitee submitted
    public function inauguralInviteeSubmittedThankYou($token = null)
    {
        //query the complimentary_delegates table to find the unique_id
        $attendee = ComplimentaryDelegate::where('unique_id', $token)->first();
        // if not found then redirect to /invited/not-found
        if (!$attendee) {
            return redirect()->to('/invited/not-found');
        }
        //return view with thank you message
        return view('exhibitor.inaugural_thank_you', compact('attendee'));
    }


    //passes analytics to user how much allocated in different categories and how much used
    //and how many are remaining
    /**
     * @return JsonResponse
     */
    public function analytics(){
        $this->__Construct();
        $count = $this->checkCount();
        $used = $this->usedcount();

        $complimentaryAllocated = $count['complimentary_delegate_count'] ?? 0;
        $complimentaryUsed = $used['used_complimentary_delegates'] ?? 0;
        $stallAllocated = $count['stall_manning_count'] ?? 0;
        $exhibitionParticipantId = $count['exhibition_participant_id'] ?? null;

        // Always get used stall manning count from DB
        $stallUsed = 0;
        if ($exhibitionParticipantId !== null) {
            $stallUsed = DB::table('stall_manning')
                ->where('exhibition_participant_id', $exhibitionParticipantId)
                ->count();
        }

        $ticketAlloc = $count['ticket_allocation'] ?? [];

        $analytics = [
            'complimentary_delegate_count' => [
                'allocated' => $complimentaryAllocated,
                'used' => $complimentaryUsed,
                'remaining' => $complimentaryAllocated - $complimentaryUsed,
            ],
            'stall_manning_count' => [
                'allocated' => $stallAllocated,
                'used' => $stallUsed,
                'remaining' => $stallAllocated - $stallUsed,
            ],
            'ticket_allocation' => [],
        ];

        if (is_array($ticketAlloc)) {
            foreach ($ticketAlloc as $ticket) {
                $ticketId = $ticket['ticket_id'] ?? null;
                $allocated = $ticket['count'] ?? 0;
                $ticketName = null;
                if ($ticketId !== null) {
                    $ticketModel = \App\Models\Ticket::find($ticketId);
                    $ticketName = $ticketModel ? $ticketModel->ticket_type : null;
                }
                $usedCount = 0;
                if ($ticketName !== null && $exhibitionParticipantId !== null) {
                    $usedCount = DB::table('complimentary_delegates')
                        ->where('exhibition_participant_id', $exhibitionParticipantId)
                        ->where('ticketType', $ticketName)
                        ->count();
                }
                $analytics['ticket_allocation'][] = [
                    'ticket_id' => $ticketId,
                    'ticket_name' => $ticketName,
                    'allocated' => $allocated,
                    'used' => $usedCount,
                    'remaining' => $allocated - $usedCount,
                ];
            }
        }

        return response()->json($analytics);

    }

    public function add(Request $request)
    {

        try {

            /**
             * name: document.getElementById('name').value,
             * email: document.getElementById('email').value,
             * phone: fullPhoneNumber,
             * jobTitle: document.getElementById('jobTitle').value,
             * invite_type : document.getElementById('inviteType2').value
             */

            if (!in_array($request->invite_type, ['delegate', 'exhibitor'])) {
                // Assume invite_type is a slug for a custom ticket
                $ticket = \App\Models\Ticket::where('id', $request->invite_type)->first();
                if (!$ticket) {
                    return response()->json(['error' => 'Invalid ticket type'], 400);
                }
                $ticketId = $ticket->ticket_type;
            } elseif (in_array($request->invite_type, ['delegate', 'exhibitor'])) {
                // For 'delegate' or 'exhibitor', set ticketId to invite_type for validation
                $ticketId = $request->invite_type;
            } else {
                return response()->json(['error' => 'Invalid ticket type'], 400);
            }

            #Log::info($request->all());
            // Validate request and return JSON error messages
            $validatedData = $request->validate([
                'invite_type' => 'required',
                'email' => [
                    'required',
                    'email',
                    'unique:complimentary_delegates,email',
                    // 'unique:attendees,email',
                ],
                'name' => 'required',
                'phone' => 'required',
                'jobTitle' => 'required',
                'organisationName' => 'nullable|string|max:255',
                'idCardType' => 'nullable|string|max:255',
                'idCardNumber' => 'nullable|string|max:255',
            ]);


            // Fetch counts
            $count = $this->checkCount();
            $participantId = $count['exhibition_participant_id'];

            if ($request->invite_type == 'delegates') {
                $countComplimentaryDelegates = DB::table('complimentary_delegates')
                    ->where('exhibition_participant_id', $participantId)
                    ->count();

                if ($countComplimentaryDelegates >= $count['complimentary_delegate_count']) {
                    return response()->json(['error' => 'You have reached the maximum limit of Exhibitor Passes'], 422);
                }

                // Generate token and insert
                $token = (string) Str::uuid();
                DB::table('complimentary_delegates')->insert([

                    'email' => $request->email,
                    'exhibition_participant_id' => $participantId,
                    'first_name' => $request->name,
                    'mobile' => $request->phone,
                    'job_title' => $request->jobTitle,
                    'organisation_name' => $request->organisationName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return response()->json(['message' => 'Delegate added successfully!']);
            }

            if ($request->invite_type == 'exhibitor') {
                $countStallManning = DB::table('stall_manning')
                    ->where('exhibition_participant_id', $participantId)
                    ->count();

                if ($countStallManning >= $count['stall_manning_count']) {
                    return response()->json(['error' => 'You have reached the maximum limit of stall manning'], 422);
                }
                $uniqueId = $this->generateStallManningUniqueId();
                $pinNo = $this->generateCompPinNo();
                // do {
                //     $randomNumber = rand(3200, 9999);
                //     $regId = $this->generateCompPinNo() . $randomNumber;
                // } while (DB::table('stall_manning')->where('pinNo', $regId)->exists());

                // Generate token and insert
                $token = Str::random(32);
                DB::table('stall_manning')->insert([
                    'unique_id' => $uniqueId,
                    'first_name' => $request->name,
                    'mobile' => $request->phone,
                    'job_title' => $request->jobTitle,
                    'email' => $request->email,
                    'exhibition_participant_id' => $participantId,
                    'token' => null,
                    'organisation_name' => $request->organisationName,
                    'id_no' => $request->idCardNumber,
                    'id_type' => $request->idCardType,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'pinNo' => $pinNo,
                    'ticketType' => $ticketId ?? $request->invite_type,
                ]);

                $attendee = StallManning::where('unique_id', $uniqueId)->first(['first_name', 'last_name', 'organisation_name', 'email', 'mobile', 'unique_id', 'job_title', 'created_at', 'id_no', 'id_type']);

                $data = [
                    'name' => trim($attendee->first_name . ' ' . ($attendee->last_name ?? '')),
                    'company_name' => $attendee->organisation_name,
                    'email' => $attendee->email,
                    'mobile' => $attendee->mobile,
                    'unique_id' => $attendee->unique_id,
                    'registration_type' => 'Exhibitor',
                    'designation' => $attendee->job_title,
                    'registration_date' => $attendee->created_at ? $attendee->created_at->format('Y-m-d') : null,
                    'id_card_number' => $attendee->id_no,
                    'id_card_type' => $attendee->id_type,
                    'dates' => 'All',
                    'type' => $attendee->ticketType ?? 'Exhibitor',
                ];
                Mail::to($attendee->email)
                    ->bcc('test.interlinks@gmail.com')
                    ->queue(new ExhibitorMail($data));

                return response()->json(['message' => 'Exhibitor Delegate added  successfully!']);
            } else {

                //check the count of ticket allocation from the exhibition table
                $ticketAllocation  = $count['ticket_allocation'];


                $ticket = collect($ticketAllocation)->firstWhere('ticket_id', $request->invite_type);

                if ($ticket) {
                    $allocatedCount = $ticket['count'];
                    $usedCount = DB::table('complimentary_delegates')
                        ->where('exhibition_participant_id', $participantId)
                        ->where('ticketType', $ticketId ?? $request->invite_type)
                        ->count();

                    //

                    Log::info('Ticket Type ' . $request->invite_type . 'Used count: ' . $usedCount . ', Allocated count: ' . $allocatedCount);

                    if ($usedCount >= $allocatedCount) {
                        // Handle limit reached (e.g., return error)
                        return response()->json(['error' => 'You have reached the maximum limit for this ticket type'], 422);
                    }
                    // Proceed with invite logic
                } else {
                    return response()->json(['error' => 'Invalid ticket type'], 400);
                }

                DB::table('complimentary_delegates')->insert([
                    'unique_id' => strtoupper($this->generateUniqueId()),
                    'pinNo' => strtoupper($this->generateCompPinNo()),
                    'ticketType' => $ticketId ?? $request->invite_type,
                    'email' => $request->email,
                    'exhibition_participant_id' => $participantId,
                    'first_name' => $request->name,
                    'mobile' => $request->phone,
                    'job_title' => $request->jobTitle,
                    'organisation_name' => $request->organisationName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return response()->json(['message' => 'Pass Information received successfully!'], 200);
            }


            return response()->json(['error' => 'Invalid request'], 400);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors in JSON format
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Log error and return JSON response
            Log::error('Invite error: ' . $e->getMessage());
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    //list all invoices of the exhibitor
    public function invoices(Request $request)
    {
        $this->__Construct();
        //        $this->checkCount();

        $sortField = $request->input('sort', 'created_at'); // Default sort by 'created_at'
        $sortDirection = $request->input('direction', 'desc'); // Default sort 'desc'
        $perPage = $request->input('per_page', 50); // Default 10 items per page

        $user_id = auth()->user()->id;



        //find the application id of the user from the applicatiosn table then find the invoices of the user
        //model is defind already in the application model and invoices model
        //find the invoices of the user from the invoices table

        //find the appliocantion id of the user
        $application = Application::where('user_id', $user_id)->first();
        //if not application then redirect to /dashboard
        if (!$application) {
            return redirect('/dashboard');
        }
        //find the invoices of the user
        $invoices = Invoice::where('application_id', $application->id)
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);

        // dd($invoices);

        //if invoices is empty then redirect to /dashbaord
        if ($invoices->isEmpty()) {
            return redirect('/dashboard');
        }

        $in_id = $invoices->pluck('id');

        //store id in a variable
        $in_id = $in_id[0];




        $payments = Payment::where('invoice_id',  $in_id)->get();

        //from this payment check the status of the payment

        //dd($application->invoices);

        // Check if it's an API request
        if ($request->wantsJson()) {
            return response()->json($invoices);
        }

        return view('applications.invoices', compact('invoices', 'application', 'payments'));
    }

    /**
     * Send SEMICON India 2025 email in batches of 500.
     *
     * @param array  $recipients  Array of recipient email addresses.
     * @param string $subject     Subject of the email.
     * @param string $attachment  (Optional) Full path to attachment file.
     */
    function sendSemiconEmail(array $recipients, string $subject = 'Important Information – SEMICON India 2025', string $attachment = null)
    {
        // Set max execution time to 0 (unlimited) or adjust as needed
        ini_set('max_execution_time', 0);

        $emailBody = <<<HTML
            <p>Dear Participant,</p>

            <p>Thank you for your interest in <strong>SEMICON India 2025</strong>, scheduled for <strong>2nd–4th September 2025</strong>. Entry passes (e-Badges) are now being issued.</p>

            <p>Please note the following important information:</p>
            <ul>
            <li>Entry for the <strong>Inaugural Session on 2nd September</strong> will be restricted.</li>
            <li>Only <strong>Inaugural Badge</strong> holders with QR codes may collect their physical badges and enter the venue between <strong>6:30 AM and 8:00 AM</strong> on 2nd September.</li>
            <li><strong>Non-Inaugural and Exhibitor Badge</strong> holders may enter after <strong>12:00 noon</strong> on 2nd September.</li>
            <li>Participants who have not yet received e-Badges will get them from <strong>3rd September</strong> onwards.</li>
            <li>The Inaugural Session will also be streamed live at:
                <a href="https://www.youtube.com/@IndiaSemiconductorMission/streams" target="_blank" rel="noopener">https://www.youtube.com/@IndiaSemiconductorMission/streams</a>
            </li>
            <li>Shuttle service details are attached.</li>
            </ul>

            <p>We regret any inconvenience caused. These arrangements are as per ISM guidelines, and we appreciate your cooperation.</p>

            <p>We look forward to welcoming you to SEMICON India 2025.</p>

            <p>
            <strong>Click Here to View Shuttle Service Plan:</strong><br>
            <a href="https://portal.semiconindia.org/storage/pdf/Semicon-2025-Shuttle_Plan.pdf" target="_blank" rel="noopener">
                https://portal.semiconindia.org/storage/pdf/Semicon-2025-Shuttle_Plan.pdf
            </a>
            </p>

            <p>Best regards,<br>
            SEMICON India Team</p>
            HTML;

        // Split recipients into batches of 500
        $chunks = array_chunk($recipients, 500);

        foreach ($chunks as $batch) {
            try {
                Mail::send([], [], function ($message) use ($batch, $subject, $emailBody, $attachment) {
                    $message->to($batch)
                        ->subject($subject)
                        ->html($emailBody);

                    // if ($attachment && file_exists($attachment)) {
                    //     $message->attach($attachment);
                    // }
                });

                echo "Batch of " . count($batch) . " emails sent successfully.\n";
            } catch (\Throwable $e) {
                echo "Error sending batch: " . $e->getMessage() . "\n";
            }
            // Optional: sleep for a few seconds to avoid rate-limits
            sleep(2);
        }
    }

    public function sendShuttleEmail(Request $request)
    {


        ini_set('max_execution_time', 0);
        //get all the emails from the stall_manning and complimentary_delegates table where first_name is not null and email is not null
        $emailsStallManning = DB::table('stall_manning')
            ->whereNotNull('first_name')
            ->whereNotNull('email')
            ->pluck('email')
            ->toArray();

        $emailsComplimentaryDelegates = DB::table('complimentary_delegates')
            ->whereNotNull('first_name')
            ->whereNotNull('email')
            ->pluck('email')
            ->toArray();

        //mark them as emailSent =1 
        DB::table('stall_manning')
            ->whereNotNull('first_name')
            ->whereNotNull('email')
            ->update(['emailSent' => 1, 'updated_at' => now()]);

        DB::table('complimentary_delegates')
            ->whereNotNull('first_name')
            ->whereNotNull('email')
            ->update(['emailSent' => 1, 'updated_at' => now()]);


        $emails = array_merge($emailsStallManning, $emailsComplimentaryDelegates);

        // Send the emails

        $this->sendSemiconEmail($emails, 'Important Information on Entry Passes & Access for SEMICON India 2025');


        // Path to the attachment file
        // $attachmentPath = storage_path('pdf/Semicon-2025-Shuttle_Plan.pdf');

        // Call the function to send emails in batches


        return response()->json(['message' => 'Emails are being sent in batches.']);
    }

    //select all email from stall_manning where emailSent is null or 0 and first_name is not null and email is not null
    public function emailSent(Request $request)
    {
        $emails = DB::table('complimentary_delegates')
            ->where(function ($query) {
                $query->whereNull('emailSent')
                    ->orWhere('emailSent', 0);
            })
            ->whereNotNull('first_name')
            ->whereNotNull('email')
            ->pluck('email');

        //mark them as emailSent =1 and sleep for 1 second after every 100 updates
        $count = 0;
        foreach ($emails as $email) {
            DB::table('complimentary_delegates')->where('email', $email)->update(['emailSent' => 1]);
            $count++;

            if ($count % 100 === 0) {
                sleep(1);
            }
        }

        $emails = array_values(array_filter($emails->toArray(), function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }));

        $subject = 'Important Information on Entry Passes & Access for SEMICON India 2025';

        // Single source of truth for the email body
        $emailBody =
            <<<HTML
                    <p>Dear Participant,</p>

                    <p>Thank you for your interest in <strong>SEMICON India 2025</strong>, scheduled for <strong>2nd–4th September 2025</strong>. Entry passes (e-Badges) are now being issued.</p>

                    <p>Please note the following important information:</p>
                    <ul>
                    <li>Entry for the <strong>Inaugural Session on 2nd September</strong> will be restricted.</li>
                    <li>Only <strong>Inaugural Badge</strong> holders with QR codes may collect their physical badges and enter the venue between <strong>6:30 AM and 8:00 AM</strong> on 2nd September.</li>
                    <li><strong>Non-Inaugural and Exhibitor Badge</strong> holders may enter after <strong>12:00 noon</strong> on 2nd September.</li>
                    <li>Participants who have not yet received e-Badges will get them from <strong>3rd September</strong> onwards.</li>
                    <li>The Inaugural Session will also be streamed live at:
                        <a href="https://www.youtube.com/@IndiaSemiconductorMission/streams" target="_blank" rel="noopener">https://www.youtube.com/@IndiaSemiconductorMission/streams</a>
                    </li>
                    <li>Shuttle service details are attached.</li>
                    </ul>

                    <p>We regret any inconvenience caused. These arrangements are as per ISM guidelines, and we appreciate your cooperation.</p>

                    <p>We look forward to welcoming you to SEMICON India 2025.</p>

                    <p>
                    <strong>Click Here to View Shuttle Service Plan:</strong><br>
                    <a href="https://portal.semiconindia.org/storage/pdf/Semicon-2025-Shuttle_Plan.pdf" target="_blank" rel="noopener">
                        https://portal.semiconindia.org/storage/pdf/Semicon-2025-Shuttle_Plan.pdf
                    </a>
                    </p>

                    <p>Best regards,<br>
                    SEMICON India Team</p>
                    HTML;

        // Convert the collection to an array and filter out invalid emails
        // $emails = array_values(array_filter($emails->toArray(), function ($email) {
        //     return filter_var($email, FILTER_VALIDATE_EMAIL);
        // }));
        // send email to all the emails in bcc with with this subject and email body
        Mail::send([], [], function ($message) use ($emails, $subject, $emailBody) {
            $message->bcc($emails)
                ->subject($subject)
                ->html($emailBody);
        });

        // Mark emails as sent
        foreach ($emails as $email) {
            DB::table('complimentary_delegates')->where('email', $email)->update(['emailSent' => 1]);
        }

        return response()->json($emails);
    }


    //select from attendee table where emailSent is null or 0 and first_name is not null and email is not null
    //send first 5000 emails in bcc with subject and email body and mark them as emailSent =1 and sleep for 1 second after every 100 updates
    public function attendeeEmailSent(Request $request)
    {
        $emails = DB::table('attendees')
            ->where(function ($query) {
                $query->whereNull('emailSent')
                    ->orWhere('emailSent', 0);
            })
            ->whereNotNull('first_name')
            ->whereNotNull('email')
            ->pluck('email')
            ->take(5000);

        //mark them as emailSent =1 and sleep for 1 second after every 100 updates
        $count = 0;
        foreach ($emails as $email) {
            DB::table('attendees')->where('email', $email)->update(['emailSent' => 1]);
            $count++;

            if ($count % 100 === 0) {
                sleep(1);
            }
        }

        $subject = 'Important Information on Entry Passes & Access for SEMICON India 2025';

        // Single source of truth for the email body
        $emailBody = <<<HTML
        <p>Dear Participant,</p>

        <p>Thank you for your interest in <strong>SEMICON India 2025</strong>, scheduled for <strong>2nd–4th September 2025</strong>. Entry passes (e-Badges) are now being issued.</p>

        <p>Please note the following important information:</p>
        <ul>
        <li>Entry for the <strong>Inaugural Session on 2nd September</strong> will be restricted.</li>
        <li>Only <strong>Inaugural Badge</strong> holders with QR codes may collect their physical badges and enter the venue between <strong>6:30 AM and 8:00 AM</strong> on 2nd September.</li>
        <li><strong>Non-Inaugural and Exhibitor Badge</strong> holders may enter after <strong>12:00 noon</strong> on 2nd September.</li>
        <li>Participants who have not yet received e-Badges will get them from <strong>3rd September</strong> onwards.</li>
        <li>The Inaugural Session will also be streamed live at:
            <a href="https://www.youtube.com/@IndiaSemiconductorMission/streams" target="_blank" rel="noopener">https://www.youtube.com/@IndiaSemiconductorMission/streams</a>
        </li>
        <li>Shuttle service details are attached.</li>
        </ul>

        <p>We regret any inconvenience caused. These arrangements are as per ISM guidelines, and we appreciate your cooperation.</p>

        <p>We look forward to welcoming you to SEMICON India 2025.</p>

        <p>
        <strong>Click Here to View Shuttle Service Plan:</strong><br>
        <a href="https://portal.semiconindia.org/storage/pdf/Semicon-2025-Shuttle_Plan.pdf" target="_blank" rel="noopener">
            https://portal.semiconindia.org/storage/pdf/Semicon-2025-Shuttle_Plan.pdf
        </a>
        </p>

        <p>Best regards,<br>
        SEMICON India Team</p>
        HTML;

        //skip emails which is not valid email address
        $emails = array_filter($emails->toArray(), function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });

        // send email to all the emails in bcc with with this subject and email body
        // Send emails individually to avoid hitting BCC limits
        foreach ($emails as $email) {
            try {
                // Dispatch email sending to the queue
                Mail::to($email)
                    ->queue((new \Illuminate\Support\HtmlString($emailBody))
                        ->withSubject($subject));

                // Mark email as sent after dispatching to queue
                DB::table('attendees')->where('email', $email)->update(['emailSent' => 1]);
                exit;
            } catch (\Exception $e) {
                // Optionally log the error or handle failed emails
                Log::error("Failed to queue email to $email: " . $e->getMessage());
            }
        }

        //retunr that all mails are sent
        return response()->json(['message' => 'Emails are being sent in batches.']);
    }
}
