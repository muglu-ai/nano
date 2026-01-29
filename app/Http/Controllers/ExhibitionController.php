<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Helpers\TicketAllocationHelper;
use Illuminate\Http\Request;
use App\Models\ExhibitionParticipant;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;
use App\Models\ExhibitorInfo;

class ExhibitionController extends Controller
{
    //

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
            ->limit(2)
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

        dd($payload);
            $this->sendExhibitorData($payload);
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
    /**
     * Handle exhibitor payment success: allocate tickets from rules (ticketAllocation JSON)
     * so exhibitor registration matches startup-zone behaviour and does not fill
     * stall_manning_count / complimentary_delegate_count only.
     */
    public function handlePaymentSuccess($applicationId)
    {
        $application = Application::where(function ($query) use ($applicationId) {
            $query->where('application_id', $applicationId)
                ->orWhere('id', $applicationId);
        })->first();

        if (!$application) {
            Log::warning('handlePaymentSuccess: application not found', ['application_id' => $applicationId]);
            return response()->json(['error' => 'Application not found'], 404);
        }

        $boothArea = $application->allocated_sqm ?? $application->interested_sqm ?? null;
        $exhibitionParticipant = TicketAllocationHelper::autoAllocateAfterPayment(
            $application->id,
            $boothArea,
            $application->event_id ?? null,
            $application->application_type ?? null
        );

        if ($exhibitionParticipant && !empty($exhibitionParticipant->ticketAllocation)) {
            $countsData = TicketAllocationHelper::getCountsFromAllocation($application->id);
            Log::info('Exhibitor allocation after payment (ticketAllocation)', [
                'application_id' => $application->id,
                'exhibition_participant_id' => $exhibitionParticipant->id,
                'counts' => $countsData,
            ]);
            return response()->json([
                'stall_manning_count' => $countsData['stall_manning_count'] ?? 0,
                'complimentary_delegate_count' => $countsData['complimentary_delegate_count'] ?? 0,
                'ticketAllocation' => $exhibitionParticipant->ticketAllocation,
            ]);
        }

        // No rules matched: do not create record with old columns only (would set ticketAllocation = null)
        Log::info('Exhibitor allocation: no rules matched, no participant created', [
            'application_id' => $application->id,
            'booth_area' => $boothArea,
        ]);
        return response()->json(['stall_manning_count' => 0, 'complimentary_delegate_count' => 0]);
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
