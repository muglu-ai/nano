<?php

namespace App\Http\Controllers;
//use country and states model
use App\Models\Country;
use App\Models\State;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\RequirementsOrder;
use App\Models\CoExhibitor;
use App\Models\ExhibitionParticipant;
use App\Models\Application;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;



class NewMisController extends Controller
{
    //
    //there is two fields country and state in the application form so we need to fetch the country and state from the database and send it to the view
    public function getCountryAndState()
    {
        // Fetch all countries
        $countries = Country::all();
        return view('test.import_states', compact('countries'));
    }

    public function getStates(Request $request)
    {
        if (!$request->has('country_id')) {
            return response()->json(['error' => 'Country ID is missing'], 400);
        }

        //validate the country id and get the states
        $validated = $request->validate([
            'country_id' => 'required|integer',
        ]);



        $states = State::where('country_id', $request->country_id)->get();

        return response()->json($states);
    }

    


    public function activeUsersAnalytics()
    {
        $counts = $this->getActiveInactiveUserCounts();
        $activeCount = $counts['activeCount'] ?? 0;
        $inactiveCount = $counts['inactiveCount'] ?? 0;
        // 1. Users who placed extra requirement orders
        $extraOrderUserIds = RequirementsOrder::distinct()->pluck('user_id')->toArray();
        $uniqueExtraOrder = count($extraOrderUserIds); // count of unique users

        // 2. Users who used stall manning or complimentary passes
        $participantUserIds = \App\Models\ExhibitionParticipant::with(['application', 'stallManning', 'complimentaryDelegates'])
            ->get()
            ->filter(function ($participant) {
                return ($participant->stallManning->count() > 0 || $participant->complimentaryDelegates->count() > 0)
                    && $participant->application !== null;
            })
            ->pluck('application.user_id')
            ->unique()
            ->toArray();

        // 3. Applications with invitation letter generated (PDF exists)
        $applicationsWithInvitation = \App\Models\Application::with('user')
            ->get()
            ->filter(function ($app) {
                $pdfPath = public_path('/storage/invitation_letters/' . $app->application_id . '_invitation_letter.pdf');
                //dd("Checking PDF path: $pdfPath");
                return file_exists($pdfPath);
            });

        //SEC-2025_Stallbooking_App_9d9e_invitation_letters.pdf
        // this exist or not check for it

        // Check for invitation letter PDFs with alternate naming pattern
        $applicationsWithAltInvitation = \App\Models\Application::with('user')
            ->get()
            ->filter(function ($app) {
                $altPdfPath = public_path('/storage/invitation_letters/' . $app->application_id . '_invitation_letter.pdf');
                // dd("Checking alternate PDF path: $altPdfPath");
                return file_exists($altPdfPath);
            });

        // Merge both sets of applications with invitation letters
        $applicationsWithInvitation = $applicationsWithInvitation->merge($applicationsWithAltInvitation)->unique('application_id');
        $invitationLetterCount = $applicationsWithInvitation->count();
        $invitationLetterUserIds = $applicationsWithInvitation->pluck('user.id')->unique()->toArray();

        // 4. Combine all to identify active users
        $activeUserIds = array_unique(array_merge(
            $extraOrderUserIds,
            $participantUserIds,
            $invitationLetterUserIds
        ));

        $activeUserCount = count($activeUserIds);
        $activeUsers = \App\Models\User::whereIn('id', $activeUserIds)->get();

        // 5. Count of users with paid applications
        // Count users with paid/partial applications (main exhibitor)
        $usersWithPaidApps = \App\Models\User::whereHas('applications', function ($q) {
            $q->where('submission_status', 'approved')
                ->whereHas('invoices', function ($iq) {
                    $iq->where('type', 'Stall Booking')
                        ->whereIn('payment_status', ['paid', 'partial']);
                });
        })->count();

        // Count approved co-exhibitors with paid/partial invoices
        // $coExhibitorPaidCount = \App\Models\CoExhibitor::where('status', 'approved')->count();
        $coExhibitorPaidCount = 0;

        // Combine both counts
        $usersWithPaidApps += $coExhibitorPaidCount;


        // 6. Participant stats
        $participants = \App\Models\ExhibitionParticipant::with(['application', 'coExhibitor', 'stallManning', 'complimentaryDelegates'])->get();
        $participantStats = $participants->map(function ($participant) {
            $companyName = 'N/A';
            if ($participant->coExhibitor_id) {
                $companyName = CoExhibitor::where('id', $participant->coExhibitor_id)->value('co_exhibitor_name') ?? 'Co-Exhibitor';
            } elseif ($participant->application) {
                $companyName = $participant->application->company_name ?? 'Exhibitor';
            }
            return [
                'company_name' => $companyName,
                'stall_manning_used' => $participant->stallManning->count(),
                'complimentary_passes_used' => $participant->complimentaryDelegates->count(),
            ];
        });


        $stallTypeCounts = $this->getStallCategoryCounts();

        // dd("Stall Type Counts: " . ($stallTypeCounts));
        // print_r($stallTypeCounts);
        // exit;
        $shellCount = $stallTypeCounts['shell_scheme_count'] ?? 0;
        $rawCount = $stallTypeCounts['bare_space_count'] ?? 0;

        // dd("Shell Count: $shellCount, Raw Count: $rawCount");

        $fasciaName = $this->getFasciaNameCounts();

        $logoCount = $this->getApplicationsWithLogo();

        // dd("Fascia Name: $fasciaName, Logo Count: $logoCount");





        return view('admin.activeUserAnalytics', [
            'activeExhibitorUsers' => $activeUserCount,
            'inactiveExhibitorUsers' => $inactiveCount,
            'activeUsers' => $activeUsers,
            'invitationLetterCount' => $invitationLetterCount,
            'applicationsWithInvitation' => $applicationsWithInvitation,
            'usersWithPaidApps' => $usersWithPaidApps,
            'participantStats' => $participantStats,
            'uniqueExtraOrder' => $uniqueExtraOrder,
            'stallTypeCounts' => $stallTypeCounts,
            'shellCount' => $fasciaName,
            'logoCount' => $logoCount,
        ]);
    }

    public function exportUsersV2(Request $request)
    {
        $type = $request->get('type', 'active'); // 'active' or 'not_active'

        // --- Gather user IDs exactly as in analytics ---
        $extraOrderUserIds = RequirementsOrder::distinct()->pluck('user_id')->toArray();

        $participantUserIds = ExhibitionParticipant::with(['application', 'stallManning', 'coExhibitor', 'complimentaryDelegates'])
            ->get()
            ->filter(function ($participant) {
                return ($participant->stallManning->count() > 0 || $participant->complimentaryDelegates->count() > 0)
                    && $participant->application;
            })
            ->pluck('application.user_id')
            ->unique()
            ->toArray();

        //dd("Participant User IDs: " . implode(', ', $participantUserIds));

        $applications = Application::with(['user', 'eventContact', 'exhibitionParticipant.coExhibitor'])->get();

        $applicationsWithLetter = $applications->filter(function ($app) {
            $path1 = public_path('/storage/invitation_letters/' . $app->application_id . '_invitation_letter.pdf');
            $path2 = public_path('/storage/invitation_letters/' . $app->application_id . '_invitation_letters.pdf');
            return file_exists($path1) || file_exists($path2);
        });
        $invitationLetterUserIds = $applicationsWithLetter->pluck('user.id')->unique()->toArray();

        $activeUserIds = array_unique(array_merge(
            $extraOrderUserIds,
            $participantUserIds,
            $invitationLetterUserIds
        ));


        $users = ($type === 'active')
            ? User::whereIn('id', $activeUserIds)
            : User::whereNotIn('id', $activeUserIds);

        $users = $users->with([
            'applications.eventContact',
            'applications.exhibitionParticipant.coExhibitor'
        ])->get();

        // === Build rows as an array for Excel ===
        $rows = [];
        foreach ($users as $user) {
            $company = 'N/A';
            $contactName = 'N/A';
            $contactEmail = 'N/A';
            $contactPhone = 'N/A';
            $passesUsed = 'No';

            // Find best application (prefer one with co-exhibitor)
            $mainApp = null;
            $coExhibitor = null;

            foreach ($user->applications as $application) {
                //dd("Processing application: " . $application);
                $participant = $application->exhibitionParticipant;
                if ($participant && $participant->co_exhibitor) {

                    $mainApp = $application;
                    // fetch from the CoExhibitor where application_id = $application->id
                    $coExhibitor = CoExhibitor::where('application_id', $application->id)->get();
                    // dd("Processing co-exhibitor: " . $coExhibitor->co_exhibitor_name);
                    $coExhibitor = $participant->co_exhibitor;
                    break;
                }
            }
            //
            if (!$mainApp) {
                foreach ($user->applications as $application) {
                    if ($application->exhibitionParticipant) {
                        $mainApp = $application;
                        break;
                    }
                }
            }
            if (!$mainApp && $user->applications->count()) {
                $mainApp = $user->applications->first();
            }

            if ($mainApp) {

                if ($coExhibitor) {
                    // dd("Processing co-exhibitor: " . $coExhibitor->co_exhibitor_name);
                    $company = $coExhibitor->co_exhibitor_name ?? 'Co-Exhibitor';
                    $contactName = $coExhibitor->contact_person ?? 'N/A';
                    $contactEmail = $coExhibitor->email ?? 'N/A';
                    $contactPhone = $coExhibitor->phone ?? 'N/A';
                } else {
                    $company = $mainApp->company_name ?? 'Exhibitor';
                    $eventContact = $mainApp->eventContact;
                    $contactName = trim(($eventContact->first_name ?? '') . ' ' . ($eventContact->last_name ?? ''));
                    if (empty($contactName)) $contactName = 'N/A';
                    $contactEmail = $eventContact->email ?? 'N/A';
                    $contactPhone = $eventContact->contact_number ?? 'N/A';
                }

                $participant = $mainApp->exhibitionParticipant;
                if ($participant && (
                    ($participant->stallManning && $participant->stallManning->count() > 0)
                    || ($participant->complimentaryDelegates && $participant->complimentaryDelegates->count() > 0)
                )) {
                    $passesUsed = 'Yes';
                }
            }

            if ($passesUsed === 'No' && in_array($user->id, $participantUserIds)) {
                $passesUsed = 'Yes';
            }

            $extraOrder    = in_array($user->id, $extraOrderUserIds) ? 'Yes' : 'No';
            $hasInvitation = in_array($user->id, $invitationLetterUserIds) ? 'Yes' : 'No';

            //if $company is N/A then try to search CoExhibitor model with email
            if ($company === 'N/A') {
                $coExhibitor = CoExhibitor::where('email', $user->email)->first();
                if ($coExhibitor) {
                    $company = $coExhibitor->co_exhibitor_name ?? 'Co-Exhibitor';
                    $contactName = $coExhibitor->contact_person ?? 'N/A';
                    $contactEmail = $coExhibitor->email ?? 'N/A';
                    $contactPhone = $coExhibitor->phone ?? 'N/A';
                }
            }

            $rows[] = [
                'Company Name'               => $company,
                'Registered User'            => $user->name,
                'Registered Email'           => $user->email,
                'Contact Person Name'        => $contactName,
                'Contact Email'              => $contactEmail,
                'Contact Phone'              => $contactPhone,
                'Extra Item Order Placed'    => $extraOrder,
                'Invitation Letter Issued'   => $hasInvitation,
                'Passes Claimed'             => $passesUsed
            ];
        }

        // ==== EXPORT USING MAATWEBSITE/EXCEL ====
        $filename = $type . '_users_export.csv';

        return Excel::download(new class($rows) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $rows;
            public function __construct($rows)
            {
                $this->rows = $rows;
            }
            public function array(): array
            {
                return $this->rows;
            }
            public function headings(): array
            {
                return [
                    'Company Name',
                    'Registered User',
                    'Registered Email',
                    'Contact Person Name',
                    'Contact Email',
                    'Contact Phone',
                    'Extra Item Order Placed',
                    'Invitation Letter Issued',
                    'Passes Claimed'
                ];
            }
        }, $filename);
    }

    public function exportUsers(Request $request)
    {

        $counts = $this->count();
        echo "Active: " . $counts['active_count'] . "\n";
echo "Inactive: " . $counts['inactive_count'] . "\n";
dd("Exporting " . count($counts) . " users");


        $type = $request->get('type', 'active'); // 'active' or 'not_active'

        // --- Gather criteria lists as in analytics ---
        $extraOrderUserIds = RequirementsOrder::distinct()->pluck('user_id')->toArray();

        // Passes Used User IDs
        $participantUserIds = ExhibitionParticipant::with(['application', 'stallManning', 'coExhibitor', 'complimentaryDelegates'])
            ->get()
            ->filter(function ($participant) {
                return ($participant->stallManning->count() > 0 || $participant->complimentaryDelegates->count() > 0)
                    && $participant->application;
            })
            ->pluck('application.user_id')
            ->unique()
            ->toArray();

        // Applications with Invitation Letter
        $applications = Application::with(['user', 'eventContact', 'exhibitionParticipant.coExhibitor'])->get();
        $applicationsWithLetter = $applications->filter(function ($app) {
            $path1 = public_path('/storage/invitation_letters/' . $app->application_id . '_invitation_letter.pdf');
            $path2 = public_path('/storage/invitation_letters/' . $app->application_id . '_invitation_letters.pdf');
            return file_exists($path1) || file_exists($path2);
        });
        $invitationLetterUserIds = $applicationsWithLetter->pluck('user.id')->unique()->toArray();

        // Users with Paid/Partial Invoice
        $paidPartialUserIds = User::whereHas('applications', function ($q) {
            $q->where('submission_status', 'approved')
                ->whereHas('invoices', function ($iq) {
                    $iq->where('type', 'Stall Booking')
                        ->whereIn('payment_status', ['paid', 'partial']);
                });
        })->pluck('id')->toArray();

        // "Dashboard Active" users (as before)
        $activeUserIds = array_unique(array_merge(
            $extraOrderUserIds,
            $participantUserIds,
            $invitationLetterUserIds
        ));

        // --- Now, build the correct not_active set ---
        $notActiveCriteriaUserIds = array_unique(array_merge(
            $extraOrderUserIds,
            $invitationLetterUserIds,
            $paidPartialUserIds
        ));

        if ($type === 'active') {
            $userIds = $activeUserIds;
        } else {
            // "not_active" defined as: (criteria union) minus active
            $userIds = array_diff($notActiveCriteriaUserIds, $activeUserIds);
        }

        $users = User::whereIn('id', $userIds)
            ->with([
                'applications.eventContact',
                'applications.exhibitionParticipant.coExhibitor'
            ])->get();

        // === Build rows ===
        $rows = [];
        foreach ($users as $user) {
            $company = 'N/A';
            $contactName = 'N/A';
            $contactEmail = 'N/A';
            $contactPhone = 'N/A';
            $passesUsed = 'No';

            $mainApp = null;
            $coExhibitor = null;
            foreach ($user->applications as $application) {
                $participant = $application->exhibitionParticipant;
                if ($participant && $participant->coExhibitor) {
                    $mainApp = $application;
                    $coExhibitor = $participant->coExhibitor;
                    break;
                }
            }
            if (!$mainApp) {
                foreach ($user->applications as $application) {
                    if ($application->exhibitionParticipant) {
                        $mainApp = $application;
                        break;
                    }
                }
            }
            if (!$mainApp && $user->applications->count()) {
                $mainApp = $user->applications->first();
            }

            if ($mainApp) {
                if ($coExhibitor) {
                    $company = $coExhibitor->co_exhibitor_name ?? 'Co-Exhibitor';
                    $contactName = $coExhibitor->contact_person ?? 'N/A';
                    $contactEmail = $coExhibitor->email ?? 'N/A';
                    $contactPhone = $coExhibitor->phone ?? 'N/A';
                } else {
                    $company = $mainApp->company_name ?? 'Exhibitor';
                    $eventContact = $mainApp->eventContact;
                    $contactName = trim(($eventContact->first_name ?? '') . ' ' . ($eventContact->last_name ?? ''));
                    if (empty($contactName)) $contactName = 'N/A';
                    $contactEmail = $eventContact->email ?? 'N/A';
                    $contactPhone = $eventContact->contact_number ?? 'N/A';
                }
                $participant = $mainApp->exhibitionParticipant;
                if ($participant && (
                    ($participant->stallManning && $participant->stallManning->count() > 0)
                    || ($participant->complimentaryDelegates && $participant->complimentaryDelegates->count() > 0)
                )) {
                    $passesUsed = 'Yes';
                }
            }
            if ($passesUsed === 'No' && in_array($user->id, $participantUserIds)) {
                $passesUsed = 'Yes';
            }

            $extraOrder    = in_array($user->id, $extraOrderUserIds) ? 'Yes' : 'No';
            $hasInvitation = in_array($user->id, $invitationLetterUserIds) ? 'Yes' : 'No';

            if ($company === 'N/A') {
                $coExhibitor = CoExhibitor::where('email', $user->email)
                    ->where('status', 'approved')
                    ->first();
                if ($coExhibitor) {
                    $company = $coExhibitor->co_exhibitor_name ?? 'Co-Exhibitor';
                    $contactName = $coExhibitor->contact_person ?? 'N/A';
                    $contactEmail = $coExhibitor->email ?? 'N/A';
                    $contactPhone = $coExhibitor->phone ?? 'N/A';
                }
            }

            $rows[] = [
                'Company Name'               => $company,
                'Registered User'            => $user->name,
                'Registered Email'           => $user->email,
                'Contact Person Name'        => $contactName,
                'Contact Email'              => $contactEmail,
                'Contact Phone'              => $contactPhone,
                'Extra Item Order Placed'    => $extraOrder,
                'Invitation Letter Issued'   => $hasInvitation,
                'Passes Claimed'             => $passesUsed
            ];
        }



        dd("Exporting " . count($rows) . " users of type: $type");



        $filename = $type . '_users_export_' . date('Ymd_His') . '.csv';

        //add log in useractivity.json with user id and action
        // Ensure useractivity log file exists before logging
        $logPath = storage_path('logs/useractivity.log');
        if (!file_exists($logPath)) {
            file_put_contents($logPath, '');
        }

        Log::channel('useractivity')->info('Exported users', [
            'user_id' => auth()->id(),
            'action' => 'export_users',
            'type' => $type,
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);

        return Excel::download(new class($rows) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $rows;
            public function __construct($rows)
            {
                $this->rows = $rows;
            }
            public function array(): array
            {
                return $this->rows;
            }
            public function headings(): array
            {
                return [
                    'Company Name',
                    'Registered User',
                    'Registered Email',
                    'Contact Person Name',
                    'Contact Email',
                    'Contact Phone',
                    'Extra Item Order Placed',
                    'Invitation Letter Issued',
                    'Passes Claimed'
                ];
            }
        }, $filename);
    }


    // function to get count
    public function count(){
        $extraOrderUserIds = RequirementsOrder::distinct()->pluck('user_id')->toArray();

        // Passes Used User IDs
        $participantUserIds = ExhibitionParticipant::with(['application', 'stallManning', 'coExhibitor', 'complimentaryDelegates'])
            ->get()
            ->filter(function ($participant) {
                return ($participant->stallManning->count() > 0 || $participant->complimentaryDelegates->count() > 0)
                    && $participant->application;
            })
            ->pluck('application.user_id')
            ->unique()
            ->toArray();

        // Applications with Invitation Letter
        $applications = Application::with(['user', 'eventContact', 'exhibitionParticipant.coExhibitor'])->get();
        $applicationsWithLetter = $applications->filter(function ($app) {
            $path1 = public_path('/storage/invitation_letters/' . $app->application_id . '_invitation_letter.pdf');
            $path2 = public_path('/storage/invitation_letters/' . $app->application_id . '_invitation_letters.pdf');
            return file_exists($path1) || file_exists($path2);
        });
        $invitationLetterUserIds = $applicationsWithLetter->pluck('user.id')->unique()->toArray();

        // Users with Paid/Partial Invoice
        $paidPartialUserIds = User::whereHas('applications', function ($q) {
            $q->where('submission_status', 'approved')
                ->whereHas('invoices', function ($iq) {
                    $iq->where('type', 'Stall Booking')
                        ->whereIn('payment_status', ['paid', 'partial']);
                });
        })->pluck('id')->toArray();

        // Active Users
        $activeUserIds = array_unique(array_merge(
            $extraOrderUserIds,
            $participantUserIds,
            $invitationLetterUserIds
        ));

        // Not Active Criteria
        $notActiveCriteriaUserIds = array_unique(array_merge(
            $extraOrderUserIds,
            $invitationLetterUserIds,
            $paidPartialUserIds
        ));

        // Inactive Users = criteria - active
        $inactiveUserIds = array_diff($notActiveCriteriaUserIds, $activeUserIds);

        return [
            'active_count'   => count($activeUserIds),
            'inactive_count' => count($inactiveUserIds),
        ];
    }


    public function getActiveInactiveUserCounts()
    {
        // Gather criteria lists as in analytics
        $extraOrderUserIds = RequirementsOrder::distinct()->pluck('user_id')->toArray();

        $participantUserIds = ExhibitionParticipant::with(['application', 'stallManning', 'complimentaryDelegates'])
            ->get()
            ->filter(function ($participant) {
                return ($participant->stallManning->count() > 0 || $participant->complimentaryDelegates->count() > 0)
                    && $participant->application;
            })
            ->pluck('application.user_id')
            ->unique()
            ->toArray();

        $applications = Application::with('user')->get();
        $applicationsWithLetter = $applications->filter(function ($app) {
            $path1 = public_path('/storage/invitation_letters/' . $app->id . '_invitation_letter.pdf');
            $path2 = public_path('/storage/invitation_letters/' . $app->id . '_invitation_letters.pdf');
            return file_exists($path1) || file_exists($path2);
        });
        $invitationLetterUserIds = $applicationsWithLetter->pluck('user.id')->unique()->toArray();

        $paidPartialUserIds = User::whereHas('applications', function ($q) {
            $q->where('submission_status', 'approved')
                ->whereHas('invoices', function ($iq) {
                    $iq->where('type', 'Stall Booking')
                        ->whereIn('payment_status', ['paid', 'partial']);
                });
        })->pluck('id')->toArray();

        $activeUserIds = array_unique(array_merge(
            $extraOrderUserIds,
            $participantUserIds,
            $invitationLetterUserIds
        ));
        $notActiveCriteriaUserIds = array_unique(array_merge(
            $extraOrderUserIds,
            $invitationLetterUserIds,
            $paidPartialUserIds
        ));
        $inactiveUserIds = array_diff($notActiveCriteriaUserIds, $activeUserIds);

        return [
            'activeCount' => count($activeUserIds),
            'inactiveCount' => count($inactiveUserIds),
        ];
    }
    // get the application stall_category count for Bare Space and Shell Scheme from application table
    public function getStallCategoryCounts()
    {
        $stallCategoryCounts = Application::select('stall_category', DB::raw('count(*) as count'))
            ->where('submission_status', 'approved')
            ->whereHas('invoices', function ($iq) {
                $iq->where('type', 'Stall Booking')
                    ->whereIn('payment_status', ['paid', 'partial']);
            })
            ->groupBy('stall_category')
            ->pluck('count', 'stall_category')
            ->toArray();

        return [
            'stall_category_counts' => $stallCategoryCounts,
            'bare_space_count' => $stallCategoryCounts['Bare Space'] ?? 0,
            'shell_scheme_count' => $stallCategoryCounts['Shell Scheme'] ?? 0,
        ];
    }
    // get how many stall_category = Shell Scheme have filled the fascia_name and how many have not filled it
    public function getFasciaNameCounts()
    {
        // Get all applications with Shell Scheme stall category
        $shellSchemeApps = Application::where('stall_category', 'Shell Scheme')
            ->where('submission_status', 'approved')
            ->whereHas('invoices', function ($iq) {
                $iq->where('type', 'Stall Booking')
                    ->whereIn('payment_status', ['paid', 'partial']);
            })
            ->get();
        $fasciaFilledCount = $shellSchemeApps->filter(function ($app) {
            return !empty($app->fascia_name);
        })->count();
        $fasciaNotFilledCount = $shellSchemeApps->count() - $fasciaFilledCount;
        return [
            'fascia_filled_count' => $fasciaFilledCount,
            'fascia_not_filled_count' => $fasciaNotFilledCount,
        ];
    }

    // get the application who has uploaded logo_link count
    public function getApplicationsWithLogo()
    {
        $logoCount = Application::whereNotNull('logo_link')
            ->where('submission_status', 'approved')
            ->whereHas('invoices', function ($iq) {
                $iq->where('type', 'Stall Booking')
                    ->whereIn('payment_status', ['paid', 'partial']);
            })
            ->count();

        // dd("Logo Count: $logoCount");
        return $logoCount;
    }

    // export fascia name and logo link of all applications with stall category
    public function exportFasciaAndLogo(Request $request)
    {
        $applications = Application::where('submission_status', 'approved')
            ->whereNotNull('logo_link')
            ->whereHas('invoices', function ($iq) {
                $iq->where('type', 'Stall Booking')
                    ->whereIn('payment_status', ['paid', 'partial']);
            })
            ->get();

        $rows = [];
        foreach ($applications as $app) {
            $companyName = $app->company_name ?? 'N/A';
            $fasciaName = $app->fascia_name ?? 'N/A';
            $logoLink = $app->logo_link ?? 'N/A';

            $rows[] = [
                'Company Name' => $companyName,
                // 'Fascia Name' => $fasciaName,
                'Logo Link' => $logoLink
            ];
        }
        $filename = 'fascia_logo_export_' . date('Ymd_His') . '.csv';
        Log::channel('useractivity')->info('Exported Fascia', [
            'user_id' => auth()->id(),
            'action' => 'Exported Fascia and Logo',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);
        return Excel::download(new class($rows) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $rows;
            public function __construct($rows)
            {
                $this->rows = $rows;
            }
            public function array(): array
            {
                return $this->rows;
            }
            public function headings(): array
            {
                return [
                    'Company Name',
                    // 'Fascia Name',
                    'Logo Link'
                ];
            }
        }, $filename);
    }

    //export just fascia name of all applications with stall category
    public function exportFasciaName(Request $request)
    {
        // Get all applications with Shell Scheme stall category
        $shellSchemeApps = Application::where('stall_category', 'Shell Scheme')
            ->where('submission_status', 'approved')
            ->whereHas('invoices', function ($iq) {
                $iq->where('type', 'Stall Booking')
                    ->whereIn('payment_status', ['paid', 'partial']);
            })
            ->get();
        $rows = [];
        foreach ($shellSchemeApps as $app) {
            $companyName = $app->company_name ?? 'N/A';
            $fasciaName = $app->fascia_name ?? 'N/A';

            $rows[] = [
                'Company Name' => $companyName,
                'Fascia Name' => $fasciaName
            ];
        }
        $filename = 'fascia_name_export_' . date('Ymd_His') . '.csv';
        Log::channel('useractivity')->info('Exported Fascia Names', [
            'user_id' => auth()->id(),
            'action' => 'Exported Fascia Names',
            'ip_address' => request()->ip(),
            'timestamp' => now()
        ]);
        return Excel::download(new class($rows) implements \Maatwebsite\Excel\Concerns\FromArray, \Maatwebsite\Excel\Concerns\WithHeadings {
            private $rows;
            public function __construct($rows)
            {
                $this->rows = $rows;
            }
            public function array(): array
            {
                return $this->rows;
            }
            public function headings(): array
            {
                return [
                    'Company Name',
                    'Fascia Name'
                ];
            }
        }, $filename);
    }
}
