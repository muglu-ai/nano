<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendee;
use App\Models\StallManning;
use App\Models\ComplimentaryDelegate;

class IntegrationAPIController extends Controller
{
    //
    public function getAttendees()
{
    // Eager load relations for efficiency
    $attendees = \App\Models\Attendee::with(['countryRelation', 'stateRelation'])->get();

    // Transform each attendee to include country and state names
    $attendees = $attendees->map(function ($attendee) {
        $data = $attendee->toArray();
        $data['country'] = $attendee->countryRelation ? $attendee->countryRelation->name : null;
        $data['state'] = $attendee->stateRelation ? $attendee->stateRelation->name : null;
        return $data;
    });

    return response()->json($attendees);
}

    public function getStallMannings()
{
    $stallMannings = StallManning::with(['countryRelation', 'stateRelation'])
        ->whereNotNull('first_name')
        ->where('first_name', '!=', '')
        ->get();

    $stallMannings = $stallMannings->map(function ($stallManning) {
        $data = $stallManning->toArray();
        $data['country'] = $stallManning->countryRelation ? $stallManning->countryRelation->name : null;
        $data['state'] = $stallManning->stateRelation ? $stallManning->stateRelation->name : null;
        return $data;
    });

    return response()->json($stallMannings);
}

    public function getComplimentaryDelegates()
    {
        $complimentaryDelegates = ComplimentaryDelegate::with(['countryRelation', 'stateRelation'])
            ->whereNotNull('first_name')
            ->where('first_name', '!=', '')
            ->get();

        $complimentaryDelegates = $complimentaryDelegates->map(function ($delegate) {
            $data = $delegate->toArray();
            $data['country'] = $delegate->countryRelation ? $delegate->countryRelation->name : null;
            $data['state'] = $delegate->stateRelation ? $delegate->stateRelation->name : null;
            $data['profile_pic'] = $delegate->profile_pic ? asset('storage/' . $delegate->profile_pic) : null;
            return $data;
        });

        return response()->json($complimentaryDelegates);
    }


    // send to api endpoint
    public function sendToApiEndpoint(Request $request)
    {
        $data = $request->all();
        $apiUrl = config('app.api_endpoint'); // Ensure this is set in your .env file
        $response = \Http::post($apiUrl, $data);
        if ($response->successful()) {
            return response()->json(['message' => 'Data sent successfully'], 200);
        } else {
            return response()->json(['message' => 'Failed to send data'], $response->status());
        }
    }

}
