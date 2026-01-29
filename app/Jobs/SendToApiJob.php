<?php

namespace App\Jobs;

use App\Models\OutboundRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;   // enables ::dispatch()
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use Throwable;

class SendToApiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Raw input (from controller/service) */
    public array $input;

    /** Optional: tie this job to an existing OutboundRequest row id */
    public ?int $outboundId;

    /** Retries & backoff */
    public int $tries = 3;
    public int $backoff = 30;

    /** Timeouts (seconds) */
    protected int $connectTimeout = 10;
    protected int $timeout = 20;

    /** Base + key from env */
    protected string $baseUrl;
    protected string $apiKey;

    /**
     * @param array $input       Payload provided by caller
     * @param int|null $outboundId If you already created an OutboundRequest row, pass its id
     */
    public function __construct(array $input, ?int $outboundId = null)
    {
        $this->input = $input;
        $this->outboundId = $outboundId;

        $this->baseUrl = 'https://www.semiconindiammactiv.com/SemiconIndia2025GetAPIVD/api/participant/';
        $this->apiKey  = "F3A9D2B7-8C41-4E5F-9A6B-1D2E3F4A5B6C"; // use your actual API key here
    }

    /**
     * Prevent duplicate in-flight jobs for the same idempotency key or RegID.
     */
    public function middleware(): array
    {
        $key = $this->input['unique_id']
            ?? $this->input['RegID']
            ?? md5(json_encode($this->input));

        return [new WithoutOverlapping('semicon:'.$key)];
    }

    public function handle(): void
    {
        // 1) Resolve endpoint: create vs update
        [$endpoint, $payload] = $this->buildEndpointAndPayload($this->input);

        // 2) Create or load the OutboundRequest log row
        $outbound = $this->loadOrCreateOutbound($endpoint, $payload);

        try {
            

            $payload = json_encode($payload, JSON_UNESCAPED_SLASHES);
            // 3) Send request using Guzzle
            $client = new Client([
                'connect_timeout' => $this->connectTimeout,
                'timeout'         => $this->timeout,
                'http_errors'     => false, // we handle non-2xx manually
            ]);

            $response = $client->post($this->baseUrl . $endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-API-KEY'    => $this->apiKey,
                ],
                'json' => $payload,
            ]);

            $status = $response->getStatusCode();
            $body   = (string) $response->getBody();
            $json   = $this->safeJsonDecode($body);

            // 4) Capture RegID if present
            $regId = is_array($json) && array_key_exists('RegID', $json) ? (string)$json['RegID'] : ($this->input['RegID'] ?? null);

            // 5) Update log row
            $outbound->update([
                'status'        => ($status >= 200 && $status < 300) ? 'success' : 'failed',
                'attempts'      => $outbound->attempts + 1,
                'response_code' => $status,
                'response_body' => $json ?: ['raw' => $body],
                'last_error'    => ($status >= 200 && $status < 300) ? null : $body,
                'responded_at'  => now(),
                'reg_id'        => $regId,
            ]);

            // tuck RegID into payload history for convenience
            if ($regId) {
                $outbound->payload = array_merge($outbound->payload ?? [], ['RegID' => $regId]);
                $outbound->save();
            }

            if ($status < 200 || $status >= 300) {
                // Throw to trigger retry
                throw new \RuntimeException("SEMICON API returned HTTP {$status}");
            }

        } catch (ConnectException|RequestException $e) {
            $this->markError($outbound, $e);
            throw $e; // rethrow so Laravel will retry
        } catch (Throwable $e) {
            $this->markError($outbound, $e);
            throw $e;
        }
    }

    /** Decide endpoint + normalize payload */
    protected function buildEndpointAndPayload(array $in): array
    {
        $type = strtolower($in['RegistrationType'] ?? '');
        $hasReg = !empty($in['RegID']);

        // Normalize Inaugural spelling
        $inaugural = $in['Inaugural'] ?? $in['Inaugral'] ?? '0';

        if ($hasReg) {
            // UPDATE flow
            $endpoint = $type === 'exhibitor' ? 'UpdateExhibitor' : 'UpdateVisitor';
            $payload  = [
                'Inaugural' => (string)$inaugural,
                'RegID'     => (string)$in['RegID'],
            ];
        } else {
            // CREATE flow (exhibitor vs visitor)
            $endpoint = $type === 'exhibitor' ? 'exhibitor' : 'visitor';

            $payload = [
                "RegistrationType" => $in['RegistrationType'] ?? '',
                "Name"             => $in['Name'] ?? '',
                "Designation"      => $in['Designation'] ?? '',
                "CompanyName"      => $in['CompanyName'] ?? '',
                "Email"            => $in['Email'] ?? '',
                "Mobile"           => $in['Mobile'] ?? '',
                "Country"          => $in['Country'] ?? '',
                "State"            => $in['State'] ?? '',
                "City"             => $in['City'] ?? '',
                "Idtype"           => $in['Idtype'] ?? '',
                "Idpath"           => $in['Idpath'] ?? '',
                "Imagepath"        => $in['Imagepath'] ?? '',
                "Inaugural"        => (string)$inaugural,
                "LunchStatus"      => $in['LunchStatus'] ?? '0',
                // Do NOT include ProductDetails, ProfileDetails, pointofcontact
            ];
        }

        return [$endpoint, $payload];
    }

    /** Create or load OutboundRequest */
    protected function loadOrCreateOutbound(string $endpoint, array $payload): OutboundRequest
    {
        if ($this->outboundId) {
            $existing = OutboundRequest::find($this->outboundId);
            if ($existing) {
                // overwrite endpoint/payload with normalized payload we’re about to send
                $existing->update([
                    'endpoint' => $endpoint,
                    'payload'  => $payload,
                    'status'   => 'queued',
                ]);
                return $existing;
            }
        }

        return OutboundRequest::create([
            'endpoint'        => $endpoint,
            'idempotency_key' => $this->input['unique_id'] ?? ($this->input['RegID'] ?? null),
            'payload'         => $payload,
            'status'          => 'queued',
            'attempts'        => 0,
        ]);
    }

    /** Mark row error on exception */
    protected function markError(OutboundRequest $row, Throwable $e): void
    {
        $row->update([
            'status'       => 'error',
            'attempts'     => $row->attempts + 1,
            'last_error'   => $e->getMessage(),
            'responded_at' => now(),
        ]);
        Log::error('[SendToApiJob] ' . $e->getMessage(), ['outbound_id' => $row->id]);
    }

    /** Safe JSON decode */
    protected function safeJsonDecode(string $body): ?array
    {
        try {
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
            return is_array($decoded) ? $decoded : null;
        } catch (\JsonException) {
            return null;
        }
    }

    /**
     * Handy debug helper you referenced earlier:
     * Returns a preview of headers/body that would be sent for an OutboundRequest id.
     */
    public static function debugPreview(int $outboundId): array
    {
        $row = OutboundRequest::findOrFail($outboundId);

        $apiKey  = "F3A9D2B7-8C41-4E5F-9A6B-1D2E3F4A5B6C"; // use your actual API key here
        $baseUrl = "https://www.semiconindiammactiv.com/SemiconIndia2025GetAPIVD/api/participant/";

        return [
            'url'     => $baseUrl . $row->endpoint,
            'method'  => 'POST',
            'headers' => [
                'Content-Type' => 'application/json',
                'X-API-KEY'    => $apiKey,
            ],
            'json'    => $row->payload,
        ];
    }
}
