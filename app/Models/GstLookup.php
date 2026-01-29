<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GstLookup extends Model
{
    protected $fillable = [
        'gst_number',
        'company_name',
        'billing_address',
        'state_code',
        'state_name',
        'pincode',
        'pan',
        'city',
        'trade_name',
        'registration_type',
        'registration_date',
        'status',
        'raw_response',
        'api_calls',
        'last_verified_at',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'registration_date' => 'date',
        'last_verified_at' => 'datetime',
        'api_calls' => 'integer',
    ];

    /**
     * Find or fetch GST details
     */
    public static function findOrFetch($gstNumber)
    {
        // First check if exists in database
        $lookup = self::where('gst_number', $gstNumber)->first();
        
        if ($lookup) {
            // Update last verified timestamp (don't increment api_calls - it's from cache)
            $lookup->update(['last_verified_at' => now()]);
            return $lookup;
        }
        
        // If not found, fetch from API
        return self::fetchFromApi($gstNumber);
    }

    /**
     * Fetch GST details from API
     */
    public static function fetchFromApi($gstNumber)
    {
        // Validate GST format first
        if (!preg_match('/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/', $gstNumber)) {
            return null;
        }

        try {
            // Using GST API from config file
            $apiUrl = config('constants.GST_API_URL');
            $apiKey = config('constants.GST_API_KEY');
            
            // Build request headers - GST Zen API uses "Token" header for authentication
            $headers = [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'User-Agent' => 'Mozilla/5.0'
            ];
            
            if ($apiKey) {
                $headers['Token'] = $apiKey; // GST Zen API uses "Token" header
            }
            
            // Make API request - POST with JSON body
            $response = Http::timeout(15)
                ->withHeaders($headers)
                ->post($apiUrl, [
                    'gstin' => $gstNumber
                ]);

            if (!$response->successful()) {
                Log::warning('GST API request failed', [
                    'gst_number' => $gstNumber,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();

            // Check if response is valid
            if (!$data || !isset($data['status']) || $data['status'] != 1 || !isset($data['valid']) || !$data['valid']) {
                Log::warning('GST API returned invalid response', [
                    'gst_number' => $gstNumber,
                    'response' => $data
                ]);
                return null;
            }

            // Extract data from the provided API response structure
            $companyDetails = $data['company_details'] ?? null;
            
            if (!$companyDetails) {
                return null;
            }

            // Extract company information
            $companyName = $companyDetails['legal_name'] ?? null;
            $tradeName = $companyDetails['trade_name'] ?? null;
            $status = $companyDetails['company_status'] ?? 'Active';
            
            // Extract state information
            $stateName = null;
            $stateCode = null;
            if (isset($companyDetails['state_info'])) {
                $stateInfo = $companyDetails['state_info'];
                $stateName = $stateInfo['name'] ?? null;
                $stateCode = $stateInfo['code'] ?? null;
            } elseif (isset($companyDetails['state'])) {
                // Fallback: parse state from "29 - Karnataka KA" format
                $stateStr = $companyDetails['state'];
                if (preg_match('/\d+\s*-\s*(.+?)(?:\s+[A-Z]{2})?$/', $stateStr, $matches)) {
                    $stateName = trim($matches[1]);
                }
                if (preg_match('/^(\d+)/', $stateStr, $codeMatches)) {
                    $stateCode = $codeMatches[1];
                }
            }
            
            // Extract principal address (billing address)
            $billingAddress = null;
            $pincode = null;
            $city = null;
            if (isset($companyDetails['pradr'])) {
                $pradr = $companyDetails['pradr'];
                $billingAddress = $pradr['addr'] ?? null;
                $pincode = $pradr['pincode'] ?? null;
                $city = $pradr['loc'] ?? null; // City from location field
            }
            
            // Extract PAN number
            $pan = $companyDetails['pan'] ?? null;
            
            // Extract registration date
            $registrationDate = null;
            if (isset($companyDetails['registration_date'])) {
                try {
                    $registrationDate = date('Y-m-d', strtotime($companyDetails['registration_date']));
                } catch (\Exception $e) {
                    // Invalid date format, leave as null
                }
            }
            
            // Extract registration type
            $registrationType = $companyDetails['gst_type'] ?? null;

            // If no essential data extracted, return null
            if (!$companyName && !$billingAddress) {
                return null;
            }

            // Extract and store data
            $lookup = new self();
            $lookup->gst_number = $gstNumber;
            $lookup->company_name = $companyName;
            $lookup->trade_name = $tradeName;
            $lookup->billing_address = $billingAddress;
            $lookup->state_code = $stateCode;
            $lookup->state_name = $stateName;
            $lookup->pincode = $pincode;
            $lookup->pan = $pan;
            $lookup->city = $city;
            $lookup->registration_type = $registrationType;
            $lookup->registration_date = $registrationDate;
            $lookup->status = $status;
            $lookup->raw_response = $data;
            $lookup->api_calls = 1;
            $lookup->last_verified_at = now();
            $lookup->save();

            return $lookup;

        } catch (\Exception $e) {
            Log::error('GST API Error: ' . $e->getMessage(), [
                'gst_number' => $gstNumber,
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}
