<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InterlinxAPIController extends Controller
{
    // API Configuration
    private $apiUrl = 'https://www.bengalurutechsummit.com/web/bts-interlinx/api/nano/register.php';
    private $bearerToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6IldrQ29DUGVydGc4NTIxQUdERyIsImV4cCI6MTY5MjcyNTE3OX0.vnHj8kkQCqlTRMeN4YsufEiLddKl11Q7j0qQcBCsASY';
    private $apiKey = 'AIzaSDyD51Q_7VGymsxVBgD3Py4_8ibV3SO0';

    /**
     * Send user registration data to Interlinx API
     * 
     * @param array $userData Array containing user information
     * @return array Response from the API
     */
    public function registerUser(array $userData)
    {
        $payload = []; // Initialize to avoid undefined variable in catch blocks
        try {
            // Map our input to API expected format
            // API expects: first_name, last_name, but we accept fname/lname or first_name/last_name
            $firstName = $userData['first_name'] ?? $userData['fname'] ?? null;
            $lastName = $userData['last_name'] ?? $userData['lname'] ?? null;
            
            // Validate required fields
            if (empty($userData['email'])) {
                return [
                    'success' => false,
                    'error' => 'Missing required field: email',
                    'status_code' => 400
                ];
            }
            if (empty($firstName)) {
                return [
                    'success' => false,
                    'error' => 'Missing required field: first_name or fname',
                    'status_code' => 400
                ];
            }
            if (empty($lastName)) {
                return [
                    'success' => false,
                    'error' => 'Missing required field: last_name or lname',
                    'status_code' => 400
                ];
            }
            if (empty($userData['designation'])) {
                return [
                    'success' => false,
                    'error' => 'Missing required field: designation',
                    'status_code' => 400
                ];
            }
            if (empty($userData['organisation'])) {
                return [
                    'success' => false,
                    'error' => 'Missing required field: organisation',
                    'status_code' => 400
                ];
            }
            if (empty($userData['mobile'])) {
                return [
                    'success' => false,
                    'error' => 'Missing required field: mobile',
                    'status_code' => 400
                ];
            }

            // Prepare request payload matching API expectations
            $payload = [
                'email' => strtolower(trim($userData['email'])),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'designation' => $userData['designation'],
                'organisation' => $userData['organisation'],
                'mobile' => $userData['mobile'],
            ];

            // Add optional fields if provided (matching API field names)
            if (!empty($userData['title'])) {
                $payload['title'] = $userData['title'];
            }
            if (!empty($userData['country_code'])) {
                $payload['country_code'] = $userData['country_code'];
            }
            if (!empty($userData['addr1'])) {
                $payload['addr1'] = $userData['addr1'];
            } elseif (!empty($userData['address'])) {
                // Support 'address' as alias for 'addr1'
                $payload['addr1'] = $userData['address'];
            }
            if (!empty($userData['addr2'])) {
                $payload['addr2'] = $userData['addr2'];
            }
            if (!empty($userData['city'])) {
                $payload['city'] = $userData['city'];
            }
            if (!empty($userData['state'])) {
                $payload['state'] = $userData['state'];
            }
            if (!empty($userData['country'])) {
                $payload['country'] = $userData['country'];
            }
            if (!empty($userData['pin'])) {
                $payload['pin'] = $userData['pin'];
            }
            if (!empty($userData['reg_cata'])) {
                $payload['reg_cata'] = $userData['reg_cata'];
            }

            // Log the payload being sent (for debugging)
            Log::info('Interlinx API: Sending request', [
                'email' => $payload['email'],
                'payload' => $payload
            ]);

            // Make API request with better error handling
            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->bearerToken,
                    'x-api-key' => $this->apiKey,
                    'Accept' => 'application/json',
                ])->timeout(30)->post($this->apiUrl, $payload);
            } catch (\Illuminate\Http\Client\RequestException $e) {
                Log::error('Interlinx API: Request exception', [
                    'email' => $payload['email'],
                    'error' => $e->getMessage(),
                    'response' => $e->response ? [
                        'status' => $e->response->status(),
                        'body' => $e->response->body(),
                        'headers' => $e->response->headers()
                    ] : null
                ]);
                throw $e;
            }

            // Get response data
            $statusCode = $response->status();
            $rawBody = $response->body();
            $responseData = $response->json();
            
            // If JSON decode failed, try to get error info
            if ($responseData === null && !empty($rawBody)) {
                $responseData = ['raw_body' => $rawBody];
            }
            
            // Log raw response for debugging
            Log::info('Interlinx API: Response received', [
                'email' => $payload['email'],
                'status_code' => $statusCode,
                'response' => $responseData,
                'raw_response' => $rawBody,
                'response_headers' => $response->headers(),
                'request_url' => $this->apiUrl
            ]);
            
            // If we got a 500 with empty body, check for PHP errors
            if ($statusCode == 500 && empty($rawBody)) {
                Log::error('Interlinx API: Empty response body with 500 status', [
                    'email' => $payload['email'],
                    'url' => $this->apiUrl,
                    'headers_sent' => $response->headers()
                ]);
                
                // Try to get more info about the error
                return [
                    'success' => false,
                    'status_code' => 500,
                    'message' => 'Server returned 500 error with empty response. This usually indicates a PHP error on the server side. Check server error logs.',
                    'error' => 'Empty response body',
                    'response' => null,
                    'debug_info' => [
                        'url' => $this->apiUrl,
                        'status_code' => $statusCode,
                        'headers' => $response->headers()
                    ]
                ];
            }

            // Check if request was successful
            // Note: The API might return 200 even on errors, so check the status field
            if ($statusCode == 200 || $statusCode == 201) {
                // Parse response structure - API returns: {message, status, status_code}
                $apiStatus = $responseData['status'] ?? null;
                $apiMessage = $responseData['message'] ?? 'Unknown response';
                $apiStatusCode = $responseData['status_code'] ?? $statusCode;

                if ($apiStatus === 'success' || $apiStatus === true) {
                    Log::info('Interlinx API: User registered successfully', [
                        'email' => $payload['email'],
                        'status_code' => $statusCode,
                        'response' => $responseData
                    ]);

                    return [
                        'success' => true,
                        'status_code' => $apiStatusCode,
                        'message' => $apiMessage,
                        'response' => $responseData
                    ];
                } else {
                    // API returned 200 but status indicates failure
                    Log::warning('Interlinx API: Registration failed', [
                        'email' => $payload['email'],
                        'status_code' => $statusCode,
                        'response' => $responseData
                    ]);

                    return [
                        'success' => false,
                        'status_code' => $apiStatusCode ?? $statusCode,
                        'message' => $apiMessage,
                        'response' => $responseData
                    ];
                }
            } else {
                // HTTP error occurred
                $errorMessage = 'API request failed';
                
                // Map status codes to messages
                switch ($statusCode) {
                    case 401:
                        $errorMessage = 'Authorization failure - Invalid token or API key';
                        break;
                    case 403:
                        $errorMessage = 'Server down or URL expired';
                        break;
                    case 500:
                        $errorMessage = 'Server internal error';
                        break;
                    default:
                        $errorMessage = $responseData['message'] ?? $responseData['error'] ?? "HTTP Error: {$statusCode}";
                }

                Log::error('Interlinx API: Registration failed', [
                    'email' => $payload['email'] ?? $userData['email'] ?? 'unknown',
                    'status_code' => $statusCode,
                    'error' => $errorMessage,
                    'response' => $responseData,
                    'raw_response' => $rawBody ?? $response->body(),
                    'request_url' => $this->apiUrl
                ]);

                return [
                    'success' => false,
                    'status_code' => $statusCode,
                    'message' => $errorMessage,
                    'response' => $responseData ?? null
                ];
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Interlinx API: Connection error', [
                'email' => $payload['email'] ?? $userData['email'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'status_code' => 503,
                'message' => 'Connection timeout or network error',
                'error' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            Log::error('Interlinx API: Unexpected error', [
                'email' => $payload['email'] ?? $userData['email'] ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'status_code' => 500,
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Register user from request (convenience method for routes)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerUserFromRequest(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            // Accept both formats
            'fname' => 'required_without:first_name|string|max:255',
            'first_name' => 'required_without:fname|string|max:255',
            'lname' => 'required_without:last_name|string|max:255',
            'last_name' => 'required_without:lname|string|max:255',
            'designation' => 'required|string|max:255',
            'organisation' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'title' => 'nullable|string|max:50',
            'country_code' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'addr1' => 'nullable|string|max:500',
            'address' => 'nullable|string|max:500',
            'addr2' => 'nullable|string|max:500',
            'pin' => 'nullable|string|max:20',
            'reg_cata' => 'nullable|string|max:255',
        ]);

        $result = $this->registerUser($validated);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'data' => $result['response'] ?? null
            ], $result['status_code']);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'error' => $result['error'] ?? null,
                'response' => $result['response'] ?? null
            ], $result['status_code'] ?? 500);
        }
    }

    /**
     * Test the API connection with a simple request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function testConnection()
    {
        // Use a more realistic test email that won't conflict
        $testData = [
            'email' => 'test_' . time() . '@example.com',
            'first_name' => 'Test',
            'last_name' => 'User',
            'designation' => 'Test Role',
            'organisation' => 'Test Organisation',
            'mobile' => '1234567890'
        ];

        $result = $this->registerUser($testData);

        return response()->json([
            'test_result' => $result,
            'api_url' => $this->apiUrl,
            'test_data' => $testData
        ], $result['status_code'] ?? 500);
    }
    
    /**
     * Check if API endpoint is accessible (simple connectivity test)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkEndpoint()
    {
        try {
            // Try a simple OPTIONS or GET request to check if endpoint exists
            // Note: POST endpoint might not support OPTIONS, so we'll catch the error
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->bearerToken,
                'x-api-key' => $this->apiKey,
            ])->timeout(10)->get($this->apiUrl);
            
            return response()->json([
                'endpoint' => $this->apiUrl,
                'accessible' => true,
                'status_code' => $response->status(),
                'headers' => $response->headers()
            ]);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            // If GET fails (expected for POST-only endpoint), try to see what error we get
            $statusCode = $e->response ? $e->response->status() : null;
            $body = $e->response ? $e->response->body() : null;
            
            return response()->json([
                'endpoint' => $this->apiUrl,
                'accessible' => $statusCode !== null, // If we got a response, endpoint exists
                'status_code' => $statusCode,
                'note' => 'Endpoint exists but may only accept POST requests',
                'body' => $body
            ], $statusCode ?? 500);
        } catch (\Exception $e) {
            return response()->json([
                'endpoint' => $this->apiUrl,
                'accessible' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

