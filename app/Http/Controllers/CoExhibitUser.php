<?php

namespace App\Http\Controllers;

use App\Models\CoExhibitor;
use App\Models\User;
use App\Models\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\CoExhibitorApprovalMail;
use App\Models\ComplimentaryDelegate;
use Illuminate\Http\Request;
use App\Models\ExhibitionParticipant;
use Illuminate\Routing\Controller as BaseController;
use App\Models\StallManning;

class CoExhibitUser extends BaseController
{
    protected $user;
    protected $coExhibitor;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->user = \Auth::user();
            if (!$this->user || $this->user->role !== 'co-exhibitor') {
                return redirect('/')->with('error', 'You do not have permission to access this page.');
            }
            $this->coExhibitor = CoExhibitor::where('user_id', $this->user->id)->first();
            if (!$this->coExhibitor || $this->coExhibitor->status !== 'approved') {
                return redirect('/')->with('error', 'Your co-exhibitor account is not approved yet.');
            }
            return $next($request);
        });
    }

    public function getMainExhibitorInfo()
    {
        if ($this->coExhibitor && $this->coExhibitor->application) {
            return $this->coExhibitor->application->company_name;
        }
        return null;
    }

    public function index()
    {
        $applicationId = $this->coExhibitor->application_id ?? null;
        $application = Application::find($applicationId);
        $this->coExhibitor->application = $application;

        //get the ExhibitionParticipant for the co-exhibitor
        $passes = $this->attachExhibitionParticipantData();

        // Access the data:
        $totalPasses = $this->coExhibitor->passes['total'];
        $usedPasses = $this->coExhibitor->passes['used'];
        $remainingPasses = $this->coExhibitor->passes['remaining'];



        return view('co_exhibitor.dashboard', ['coExhibitor' => $this->coExhibitor]);
    }

    public function passes()
    {
        $applicationId = $this->coExhibitor->application_id ?? null;
        $application = Application::find($applicationId);
        $this->coExhibitor->application = $application;
        //call the used and total passes
        $counts = $this->attachExhibitionParticipantData();
        // dd($counts);

        $exhibitors = $this->getStallManningList();


        return view('co_exhibitor.passes', ['coExhibitor' => $this->coExhibitor, 'exhibitors' => $exhibitors, 'counts' => $counts]);
    }
    public function inauguralPasses()
    {
        $applicationId = $this->coExhibitor->application_id ?? null;
        $application = Application::find($applicationId);
        $this->coExhibitor->application = $application;
        //call the used and total passes
        $counts = $this->attachExhibitionParticipantData();
        // dd($counts);

        // $exhibitors = $this->getStallManningList();
        $inauguralPassesCount = $this->attachExhibitionParticipantDataInaugural();
        $inauguralPasses = $this->getInauguralPassesList();


        // dd($inauguralPasses);


        return view('co_exhibitor.inauguralPasses', ['coExhibitor' => $this->coExhibitor, 'exhibitors' => $inauguralPasses, 'counts' => $inauguralPassesCount]);
    }

    /**
     * Attaches exhibition participant data including pass counts to the co-exhibitor
     * Returns [total_passes, used_passes, remaining_passes]
     */
    protected function attachExhibitionParticipantData()
    {
        $exhibitionParticipant = ExhibitionParticipant::where('coExhibitor_id', $this->coExhibitor->id)->first();

        // dd($exhibitionParticipant);

        if ($exhibitionParticipant) {
            // Get counts from ticketAllocation JSON using helper
            $countsData = \App\Helpers\TicketAllocationHelper::getCountsFromAllocation(
                null, // application_id is null for co-exhibitors
                $this->coExhibitor->id // coExhibitor_id
            );
            $total_passes = $countsData['stall_manning_count'] ?? 0;

            // Get used passes (non-cancelled)
            $used_passes = StallManning::where('exhibition_participant_id', $exhibitionParticipant->id)
                ->where('status', '!=', 'cancelled')
                ->count();

            // Calculate remaining passes
            $remaining_passes = max(0, $total_passes - $used_passes);

            // Attach the participant data and pass counts
            $this->coExhibitor->exhibitionParticipant = $exhibitionParticipant;
            $this->coExhibitor->passes = [
                'total' => $total_passes,
                'used' => $used_passes,
                'remaining' => $remaining_passes
            ];
        } else {
            $this->coExhibitor->exhibitionParticipant = null;
            $this->coExhibitor->passes = [
                'total' => 0,
                'used' => 0,
                'remaining' => 0
            ];
        }

        return $this->coExhibitor->passes;
    }
    protected function attachExhibitionParticipantDataInaugural()
    {
        $exhibitionParticipant = ExhibitionParticipant::where('coExhibitor_id', $this->coExhibitor->id)->first();

        // dd($exhibitionParticipant);

        if ($exhibitionParticipant) {
            // Get counts from ticketAllocation JSON using helper
            $countsData = \App\Helpers\TicketAllocationHelper::getCountsFromAllocation(
                null, // application_id is null for co-exhibitors
                $this->coExhibitor->id // coExhibitor_id
            );
            $total_passes = $countsData['complimentary_delegate_count'] ?? 0;

            // Get used passes (non-cancelled)
            $used_passes = ComplimentaryDelegate::where('exhibition_participant_id', $exhibitionParticipant->id)
                ->where('status', '!=', 'cancelled')
                ->count();

            // Calculate remaining passes
            $remaining_passes = max(0, $total_passes - $used_passes);

            // Attach the participant data and pass counts
            $this->coExhibitor->exhibitionParticipant = $exhibitionParticipant;
            $this->coExhibitor->passes = [
                'total' => $total_passes,
                'used' => $used_passes,
                'remaining' => $remaining_passes
            ];
        } else {
            $this->coExhibitor->exhibitionParticipant = null;
            $this->coExhibitor->passes = [
                'total' => 0,
                'used' => 0,
                'remaining' => 0
            ];
        }

        return $this->coExhibitor->passes;
    }


    //get the list of stall manning for the co-exhibitor
    public function getStallManningList()
    {
        //get the exhibition participant for the co-exhibitor
        $exhibitionParticipant = ExhibitionParticipant::where('coExhibitor_id', $this->coExhibitor->id)->first();
        if ($exhibitionParticipant) {
            return StallManning::where('exhibition_participant_id', $exhibitionParticipant->id)->get();
        }
        return collect(); // Return an empty collection if no exhibition participant found

    }

    //get the list of inaugural passes for the co-exhibitor from ComplimentaryDelegate
    public function getInauguralPassesList()
    {
        //get the exhibition participant for the co-exhibitor
        $exhibitionParticipant = ExhibitionParticipant::where('coExhibitor_id', $this->coExhibitor->id)->first();
        if ($exhibitionParticipant) {
            return ComplimentaryDelegate::where('exhibition_participant_id', $exhibitionParticipant->id)->get();
        }
        return collect(); // Return an empty collection if no exhibition participant found
    }
}
