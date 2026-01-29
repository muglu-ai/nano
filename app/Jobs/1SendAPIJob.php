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

class SendAPIJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Raw input (from controller/service) */
    public array $input;

    /** Optional: tie this job to an existing OutboundRequest row id */
    public ?int $outboundId;

    /** Retries & backoff */
    public int $tries = 5;
    public $backoff = [5, 15, 30, 60, 120];

    /** Timeouts (seconds) */
    protected int $connectTimeout;
    protected int $timeout;

    /** Base + key from config */
    protected string $baseUrl;
    protected string $apiKey;

    /**
     * @param array $input       Payload provided by caller
     * @param int|null $outboundId If you already created an OutboundRequest row, pass its id
     */
    public function __construct(array $input, ?int $outboundId = null)
    {
        $this->input      = $input;
        $this->outboundId = $outboundId;

        // Pull from config/env
        $this->baseUrl        = 'https://www.semiconindiammactiv.com/SemiconIndia2025GetAPIVD/api/participant/';
        $this->apiKey         = 'F3A9D2B7-8C41-4E5F-9A6B-1D2E3F4A5B6C'; // use your actual API key here
        $this->timeout        = 20;
        $this->connectTimeout = 10;

        // Keep traffic isolated
        $this->onQueue('sendapi');
    }

    /**
     * Prevent duplicate in-flight jobs for the same idempotency key or RegID.
     */
    public function middleware(): array
    {
        $key = $this->input['unique_id']
            ?? $this->input['RegID']
            ?? md5(json_encode($this->input));

        return [new WithoutOverlapping('semicon:' . $key)];
    }

    public function handle(): void
    {
        // 1) Resolve endpoint: create vs update
        [$endpoint, $payload] = $this->buildEndpointAndPayload($this->input);

        // 2) Create or load the OutboundRequest log row
        $outbound = $this->loadOrCreateOutbound($endpoint, $payload);

        try {
            // 3) Send request using Guzzle
            $client = new Client([
                'connect_timeout' => $this->connectTimeout,
                'timeout'         => $this->timeout,
                'http_errors'     => false, // we handle non-2xx manually
            ]);

            $url = $this->baseUrl . ltrim($endpoint, '/');
            // $url = 'https://www.semiconindiammactiv.com/SemiconIndia2025GetAPIVD/api/participant/visitor';

            // Log intent with masked key
            Log::info('[SendAPIJob] Dispatching', [
                'outbound_id' => $outbound->id,
                'url'         => $url,
                'headers'     => [
                    'Content-Type' => 'application/json',
                    'X-API-KEY'    => $this->mask($this->apiKey),
                ],
                'payload'     => $payload,
            ]);

            $response = $client->post($url, [
                'headers' => [
                    'Accept'            => 'application/json',
                    'Content-Type'      => 'application/json',
                    'X-API-KEY'    => $this->apiKey,
                ],
                'json' => $payload, // safer than manual json_encode
            ]);

            Log::info('[SendAPIJob] Response received', [
                'outbound_id' => $outbound->id,
                'status'      => $response->getStatusCode(),
                'body'        => (string) $response->getBody(),
            ]);

            $status = $response->getStatusCode();
            $body   = (string) $response->getBody();
            $json   = $this->safeJsonDecode($body);

            // Normalize & parse (handles "OR00017" or JSON error)
            [$ok, $regId, $errorText] = $this->parseApiResponse($status, $body);

            // Prevent duplicate sending if 200 response is received
            if ($ok && $outbound->status === 'success') {
                Log::info('[SendAPIJob] Duplicate success response, skipping further processing.', [
                    'outbound_id' => $outbound->id,
                ]);
                return;
            }

            // Concatenate reg_id and response_body if they already exist
            $prevBody = $outbound->response_body ?? [];
            if (is_string($prevBody)) {
                $prevBody = json_decode($prevBody, true) ?: [];
            }
            $newBody = $json ?: ['raw' => $body];

            // Concatenate as an array of responses
            $combinedBody = [];
            if (!empty($prevBody)) {
                $combinedBody = is_array($prevBody) && isset($prevBody[0])
                    ? $prevBody
                    : [$prevBody];
            }
            $combinedBody[] = $newBody;

            $prevRegId = $outbound->reg_id ?? [];
            if (is_string($prevRegId)) {
                $prevRegId = json_decode($prevRegId, true) ?: [];
            }
            if (!is_array($prevRegId)) {
                $prevRegId = [$prevRegId];
            }
            if ($regId && !in_array($regId, $prevRegId)) {
                $prevRegId[] = $regId;
            }

            // 4) Update log row
            $outbound->update([
                'status'        => $ok ? 'success' : ($status >= 400 && $status < 500 ? 'failed' : 'failed'),
                'attempts'      => $outbound->attempts + 1,
                'response_code' => $status,
                'response_body' => $combinedBody,
                'last_error'    => $ok ? null : ($errorText ?: "HTTP {$status}"),
                'responded_at'  => now(),
                 'reg_id' => $prevRegId,
            ]);

            // Store RegID into payload for convenience
            if ($regId) {
                $outbound->payload = array_merge($outbound->payload ?? [], ['RegID' => $regId]);
                $outbound->save();
            }

            if (!$ok) {
                // Optional policy: do not retry validation (4xx) errors
                if ($status >= 400 && $status < 500) {
                    return; // stop; treat as terminal failure (no retry)
                }
                // 5xx / network etc — trigger retry
                throw new \RuntimeException($errorText ?: "SEMICON API returned HTTP {$status}");
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
        $type   = strtolower($in['RegistrationType'] ?? '');
        $hasReg = !empty($in['RegID']);

        // Normalize Inaugural spelling
        $inaugural = $in['Inaugural'] ?? $in['Inaugral'] ?? '0';

        if ($hasReg) {
            // UPDATE flow
            $endpoint = $type === 'exhibitor' ? 'UpdateExhibitor' : 'UpdateVisitor';
            $payload  = [
                'Inaugural' => (string) $inaugural,
                'RegID'     => (string) $in['RegID'],
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
                "Inaugural"        => (string) $inaugural,
                "LunchStatus"      => (string) ($in['LunchStatus'] ?? '0'),
                // Intentionally omit ProductDetails, ProfileDetails, pointofcontact
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
        Log::error('[SendAPIJob] ' . $e->getMessage(), ['outbound_id' => $row->id]);
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

    /** Return [isSuccess(bool), regId(?string), errorText(?string)] */
    protected function parseApiResponse(int $status, string $body): array
    {
        // 2xx: could be a plain string like "OR00017" (maybe quoted), or JSON
        if ($status >= 200 && $status < 300) {
            $trim = trim($body);

            // If body is quoted JSON string: "OR00017"
            if (($trim !== '') &&
                ((str_starts_with($trim, '"') && str_ends_with($trim, '"')) ||
                 (str_starts_with($trim, "'") && str_ends_with($trim, "'")))
            ) {
                $trim = substr($trim, 1, -1);
            }

            // Treat non-empty plain text as RegID
            if ($trim !== '' && $this->looksLikeRegId($trim)) {
                return [true, $trim, null];
            }

            // If JSON success also includes RegID
            $json = $this->safeJsonDecode($body);
            if (is_array($json)) {
                $reg = $json['RegID'] ?? $json['reg_id'] ?? null;
                if (is_string($reg) && $reg !== '') {
                    return [true, $reg, null];
                }
            }

            // 2xx but no reg? still mark success, no error
            return [true, null, null];
        }

        // Non-2xx: try to extract useful error text
        $json = $this->safeJsonDecode($body);
        if (is_array($json)) {
            $msg = [];
            if (!empty($json['Message']) && is_string($json['Message'])) {
                $msg[] = $json['Message'];
            }
            if (!empty($json['ModelState']) && is_array($json['ModelState'])) {
                foreach ($json['ModelState'] as $field => $errors) {
                    if (is_array($errors)) {
                        foreach ($errors as $e) {
                            $msg[] = "{$field}: {$e}";
                        }
                    } elseif (is_string($errors)) {
                        $msg[] = "{$field}: {$errors}";
                    }
                }
            }
            $errorText = $msg ? implode("; ", $msg) : $body;
            return [false, null, $errorText];
        }

        // Not JSON; pass raw text back
        return [false, null, $body !== '' ? $body : "HTTP {$status}"];
    }

    /** Very light heuristic; tweak if their format changes */
    protected function looksLikeRegId(string $s): bool
    {
        // Example: OR00017 (letters + digits). Accept 2–5 letters + 1–10 digits.
        return (bool) preg_match('/^[A-Z]{2,5}\d{1,10}$/', $s);
    }

    /**
     * Returns a preview of headers/body that would be sent for an OutboundRequest id.
     */
    public static function debugPreview(int $outboundId): array
    {
        $row    = OutboundRequest::findOrFail($outboundId);
        $apiKey = (string) config('services.semicon.key');
        $base   = rtrim((string) config('services.semicon.base'), '/') . '/';

        $self = new self([]); // for mask()
        return [
            'url'     => $base . $row->endpoint,
            'method'  => 'POST',
            'headers' => [
                'Content-Type' => 'application/json',
                'X-API-KEY'    => $self->mask($apiKey),
            ],
            'json'    => $row->payload,
        ];
    }

    /** Mask helper for logging/preview */
    protected function mask(?string $val, int $head = 6, int $tail = 4): string
    {
        if (!$val) return '';
        $len = strlen($val);
        if ($len <= ($head + $tail)) return str_repeat('•', max(4, $len));
        return substr($val, 0, $head) . '…' . substr($val, -$tail);
    }
}
