<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use App\Models\ExhibitionParticipant;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;

class ExhibitionController extends Controller
{
    //
    public function handlePaymentSuccess($applicationId)
    {
        $application = Application::where(function ($query) use ($applicationId) {
            $query->where('application_id', $applicationId)
                ->orWhere('id', $applicationId);
        })->first();

        Log::info('Application: ' . $application);

        $stallSize = $application->allocated_sqm;

        $count = $this->calculateStallManningAndComplimentaryDelegateCount($stallSize);
        $stallManningCount = $count['stallManningCount'];
        $complimentaryDelegateCount = $count['complimentaryDelegateCount'];

        //add new entries in stall manning and complimentary delegate tables
        $exhibitionParticipant = ExhibitionParticipant::updateOrCreate(
            ['application_id' => $application->id],
            [
                'stall_manning_count' => $stallManningCount,
                'complimentary_delegate_count' => $complimentaryDelegateCount
            ]
        );

        return response()->json(['stall_manning_count' => $stallManningCount, 'complimentary_delegate_count' => $complimentaryDelegateCount]);
    }



    //function to calculate the stall manning count and complimentary delegate count
    public function calculateStallManningAndComplimentaryDelegateCount_old($stallSize)
    {
        // Get stall size from allocated_sqm and calculate stall manning count
        $stallManningCount = min(7, ceil($stallSize / 9));

        // Get stall size from allocated_sqm and calculate complimentary delegate count
        $complimentaryDelegateCount = min(7, ceil($stallSize / 9));

        return ['stallManningCount' => $stallManningCount, 'complimentaryDelegateCount' => $complimentaryDelegateCount];
    }

    public function calculateStallManningAndComplimentaryDelegateCount($stallSize)
    {
        // Define pass allocation based on stall size
        $passAllocation = [
            ['min' => 9, 'max' => 17, 'passes' => 5],
            ['min' => 18, 'max' => 26, 'passes' => 10],
            ['min' => 27, 'max' => 54, 'passes' => 20],
            ['min' => 55, 'max' => 100, 'passes' => 30],
            ['min' => 101, 'max' => 400, 'passes' => 40],
            ['min' => 401, 'max' => PHP_INT_MAX, 'passes' => 50], // Maximum limit for more than 400 sqm
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

        return [
            'stallManningCount' => $allocatedPasses,
            'complimentaryDelegateCount' => $complimentaryDelegateCount,
        ];
    }
}
