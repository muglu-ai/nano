<?php

namespace App\Services;

use App\Models\Enquiry;
use App\Models\MetroLeadsApiLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetroLeadsService
{
    /**
     * MetroLeads API endpoint
     */
    private string $endpoint;

    /**
     * Whether the API is enabled
     */
    private bool $enabled;

    public function __construct()
    {
        $this->endpoint = config('services.metroleads.endpoint', '');
        $this->enabled = config('services.metroleads.enabled', true);
    }

    /**
     * Send enquiry data to MetroLeads API
     */
    public function sendEnquiry(Enquiry $enquiry): array
    {
        // Check if API is enabled
        if (!$this->enabled) {
            Log::info('MetroLeads API is disabled, skipping', ['enquiry_id' => $enquiry->id]);
            return [
                'success' => false,
                'message' => 'MetroLeads API is disabled',
            ];
        }

        // Check if endpoint is configured
        if (empty($this->endpoint)) {
            Log::warning('MetroLeads API endpoint not configured', ['enquiry_id' => $enquiry->id]);
            return [
                'success' => false,
                'message' => 'MetroLeads API endpoint not configured',
            ];
        }

        // Map enquiry data to MetroLeads format
        $data = $this->mapEnquiryData($enquiry);

        // Send the request
        return $this->sendRequest($data, $enquiry->id);
    }

    /**
     * Map enquiry data to MetroLeads API format
     */
    private function mapEnquiryData(Enquiry $enquiry): array
    {
        // Load interests if not already loaded
        if (!$enquiry->relationLoaded('interests')) {
            $enquiry->load('interests');
        }

        // Get interest types as comma-separated string
        $interests = $enquiry->interests
            ->pluck('interest_type')
            ->map(function ($type) {
                return ucfirst(str_replace('_', ' ', $type));
            })
            ->implode(', ');

        return [
            'name' => $enquiry->full_name,
            'phone' => $enquiry->phone_full ?? $enquiry->phone_number,
            'email' => $enquiry->email,
            'sector_d2f' => $enquiry->sector,
            'event_source_28e' => 'Bengaluru Tech Summit 2026',
            'form_name' => 'Enquiry Form',
            'want_information_about_ffb' => $interests,
            'source_tags' => $enquiry->referral_source,
            'organisation_926' => $enquiry->organisation,
            'designation_8bc' => $enquiry->designation,
            'country_f0a' => $enquiry->country,
            'state_bb4' => $enquiry->state,
            'city_e1b' => $enquiry->city,
            'comment_a5a' => $enquiry->comments,
        ];
    }

    /**
     * Send request to MetroLeads API and log the result
     */
    private function sendRequest(array $data, int $enquiryId): array
    {
        // Create log entry
        $log = MetroLeadsApiLog::create([
            'enquiry_id' => $enquiryId,
            'request_data' => $data,
            'status' => 'pending',
        ]);

        try {
            // Send POST request to MetroLeads API
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->endpoint, $data);

            $httpCode = $response->status();
            $responseBody = $response->json() ?? $response->body();

            if ($response->successful()) {
                // Update log with success
                $log->update([
                    'response_data' => is_array($responseBody) ? $responseBody : ['raw' => $responseBody],
                    'status' => 'success',
                    'http_code' => $httpCode,
                ]);

                Log::info('MetroLeads API call successful', [
                    'enquiry_id' => $enquiryId,
                    'log_id' => $log->id,
                ]);

                return [
                    'success' => true,
                    'response' => $responseBody,
                    'log_id' => $log->id,
                ];
            } else {
                // Update log with error
                $log->update([
                    'response_data' => is_array($responseBody) ? $responseBody : ['raw' => $responseBody],
                    'status' => 'error',
                    'http_code' => $httpCode,
                    'error_message' => "HTTP {$httpCode}: " . ($response->body() ?? 'Unknown error'),
                ]);

                Log::warning('MetroLeads API returned error', [
                    'enquiry_id' => $enquiryId,
                    'log_id' => $log->id,
                    'http_code' => $httpCode,
                    'response' => $responseBody,
                ]);

                return [
                    'success' => false,
                    'message' => "API returned HTTP {$httpCode}",
                    'response' => $responseBody,
                    'log_id' => $log->id,
                ];
            }
        } catch (\Exception $e) {
            // Update log with exception
            $log->update([
                'status' => 'error',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('MetroLeads API call failed', [
                'enquiry_id' => $enquiryId,
                'log_id' => $log->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'API call failed: ' . $e->getMessage(),
                'log_id' => $log->id,
            ];
        }
    }
}
