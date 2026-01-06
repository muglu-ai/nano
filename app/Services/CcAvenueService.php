<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CcAvenueService
{
    /**
     * Get CCAvenue credentials based on environment
     */
    public function getCredentials()
    {
        $env = config('constants.ccavenue.environment', 'production');
        return config("constants.ccavenue.{$env}", []);
    }

    /**
     * Get API URL based on environment
     */
    public function getApiUrl()
    {
        $env = config('constants.ccavenue.environment', 'production');
        return config("constants.ccavenue.{$env}.api_url", 'https://api.ccavenue.com/apis/servlet/DoWebTrans');
    }

    /**
     * Encrypt data using AES-128-CBC
     */
    public function encrypt($plainText, $key)
    {
        $key = pack('H*', md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = bin2hex(openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector));
        return $encryptedText;
    }

    /**
     * Decrypt data using AES-128-CBC
     */
    public function decrypt($encryptedText, $key)
    {
        $key = pack('H*', md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d, 0x0e, 0x0f);
        $encryptedText = pack("H*", $encryptedText);
        return openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
    }

    /**
     * Extract TIN number from order_id
     * Format: BTS-2026-EXH-123456_timestamp
     * Returns: BTS-2026-EXH-123456
     */
    public function extractTinFromOrderId($orderId)
    {
        if (strpos($orderId, '_') !== false) {
            $parts = explode('_', $orderId);
            return $parts[0]; // Return TIN part before underscore
        }
        return $orderId; // Return as-is if no underscore
    }

    /**
     * Initiate transaction via CCAvenue API
     * 
     * @param array $orderData Order data including merchant_id, order_id, amount, etc.
     * @return array Response with payment URL or error
     */
    public function initiateTransaction($orderData)
    {
        try {
            $credentials = $this->getCredentials();
            $apiUrl = $this->getApiUrl();

            // Validate credentials
            if (empty($credentials['merchant_id']) || empty($credentials['access_code']) || empty($credentials['working_key'])) {
                Log::error('CCAvenue API - Missing credentials', [
                    'has_merchant_id' => !empty($credentials['merchant_id']),
                    'has_access_code' => !empty($credentials['access_code']),
                    'has_working_key' => !empty($credentials['working_key']),
                    'credentials_keys' => array_keys($credentials),
                ]);
                throw new \Exception('CCAvenue credentials not configured. Please check your configuration.');
            }

            Log::info('CCAvenue API - Using credentials', [
                'merchant_id' => $credentials['merchant_id'],
                'access_code' => substr($credentials['access_code'], 0, 5) . '...', // Partial for security
                'api_url' => $apiUrl,
            ]);

            // Build request data
            $requestData = [
                'merchant_id' => $credentials['merchant_id'],
                'order_id' => $orderData['order_id'],
                'amount' => $orderData['amount'],
                'currency' => $orderData['currency'] ?? 'INR',
                'redirect_url' => $orderData['redirect_url'] ?? config('constants.CCAVENUE_REDIRECT_URL'),
                'cancel_url' => $orderData['cancel_url'] ?? config('constants.CCAVENUE_REDIRECT_URL'),
                'language' => $orderData['language'] ?? 'EN',
            ];

            // Add billing details if provided
            if (isset($orderData['billing_name'])) {
                $requestData['billing_name'] = $orderData['billing_name'];
                $requestData['billing_address'] = $orderData['billing_address'] ?? '';
                $requestData['billing_city'] = $orderData['billing_city'] ?? '';
                $requestData['billing_state'] = $orderData['billing_state'] ?? '';
                $requestData['billing_zip'] = $orderData['billing_zip'] ?? '';
                $requestData['billing_country'] = $orderData['billing_country'] ?? 'India';
                $requestData['billing_tel'] = $orderData['billing_tel'] ?? '';
                $requestData['billing_email'] = $orderData['billing_email'] ?? '';
            }

            // Build query string and encrypt
            $queryString = http_build_query($requestData);
            $encryptedData = $this->encrypt($queryString, $credentials['working_key']);

            // Prepare API request
            $apiRequest = [
                'enc_request' => $encryptedData,
                'access_code' => $credentials['access_code'],
                'command' => 'initiateTransaction',
                'request_type' => 'JSON',
                'response_type' => 'JSON',
                'version' => '1.1',
            ];

            // Log request details (without sensitive data)
            Log::info('CCAvenue API Request', [
                'api_url' => $apiUrl,
                'order_id' => $orderData['order_id'],
                'amount' => $orderData['amount'],
                'currency' => $orderData['currency'] ?? 'INR',
                'has_access_code' => !empty($credentials['access_code']),
                'has_working_key' => !empty($credentials['working_key']),
            ]);

            // Make API call
            $response = Http::timeout(30)
                ->asForm()
                ->post($apiUrl, $apiRequest);

            // Log raw response
            $responseBody = $response->body();
            Log::info('CCAvenue API Raw Response', [
                'status_code' => $response->status(),
                'successful' => $response->successful(),
                'body_length' => strlen($responseBody),
                'body_preview' => substr($responseBody, 0, 500), // First 500 chars
            ]);

            if ($response->successful()) {
                // Try to parse as JSON
                try {
                    $responseData = $response->json();
                } catch (\Exception $e) {
                    Log::error('CCAvenue API - Failed to parse JSON response', [
                        'error' => $e->getMessage(),
                        'body' => $responseBody,
                    ]);
                    return [
                        'success' => false,
                        'error' => 'Invalid response format from payment gateway: ' . $e->getMessage(),
                    ];
                }
                
                // Check if response data is valid
                if (empty($responseData)) {
                    Log::error('CCAvenue API - Empty response data', [
                        'status_code' => $response->status(),
                        'body' => $responseBody,
                    ]);
                    return [
                        'success' => false,
                        'error' => 'Empty response from payment gateway',
                    ];
                }
                
                // Log full response for debugging
                Log::info('CCAvenue API Response', [
                    'status' => $responseData['status'] ?? null,
                    'has_enc_response' => isset($responseData['enc_response']),
                    'enc_error_code' => $responseData['enc_error_code'] ?? null,
                    'full_response' => $responseData,
                ]);
                
                // Check if API call was successful
                if (isset($responseData['status']) && $responseData['status'] == '0') {
                    // Decrypt response
                    try {
                        $decryptedResponse = $this->decrypt($responseData['enc_response'], $credentials['working_key']);
                        $responseArray = json_decode($decryptedResponse, true);
                        
                        if (isset($responseArray['payment_url'])) {
                            return [
                                'success' => true,
                                'payment_url' => $responseArray['payment_url'],
                                'order_id' => $orderData['order_id'],
                                'data' => $responseArray,
                            ];
                        } else {
                            Log::error('CCAvenue API - Missing payment_url in response', [
                                'response_array' => $responseArray,
                            ]);
                            return [
                                'success' => false,
                                'error' => 'Payment URL not received from gateway',
                            ];
                        }
                    } catch (\Exception $e) {
                        Log::error('CCAvenue API - Decryption failed', [
                            'error' => $e->getMessage(),
                        ]);
                        return [
                            'success' => false,
                            'error' => 'Failed to decrypt gateway response: ' . $e->getMessage(),
                        ];
                    }
                }
                
                // Handle error response
                // Try to decrypt error response if it's encrypted
                $errorMessage = 'Unknown error';
                
                if (isset($responseData['enc_response']) && !empty($responseData['enc_response'])) {
                    try {
                        // Try to decrypt the error response
                        $decryptedError = $this->decrypt($responseData['enc_response'], $credentials['working_key']);
                        $errorMessage = $decryptedError;
                    } catch (\Exception $e) {
                        // If decryption fails, use the raw response
                        $errorMessage = $responseData['enc_response'];
                    }
                } elseif (isset($responseData['message'])) {
                    $errorMessage = $responseData['message'];
                } elseif (isset($responseData['error'])) {
                    $errorMessage = $responseData['error'];
                }
                
                if (isset($responseData['enc_error_code'])) {
                    $errorMessage = "Error Code: {$responseData['enc_error_code']} - {$errorMessage}";
                }
                
                Log::error('CCAvenue API - Error response', [
                    'status' => $responseData['status'] ?? null,
                    'error_code' => $responseData['enc_error_code'] ?? null,
                    'error_message' => $errorMessage,
                    'has_enc_response' => isset($responseData['enc_response']),
                    'enc_response_length' => isset($responseData['enc_response']) ? strlen($responseData['enc_response']) : 0,
                    'full_response' => $responseData,
                ]);
                
                return [
                    'success' => false,
                    'error' => $errorMessage,
                ];
            }

            $errorBody = $response->body();
            $errorMessage = 'API request failed: HTTP ' . $response->status();
            
            // Try to parse error if it's JSON
            try {
                $errorData = $response->json();
                if (isset($errorData['message'])) {
                    $errorMessage .= ' - ' . $errorData['message'];
                } elseif (isset($errorData['error'])) {
                    $errorMessage .= ' - ' . $errorData['error'];
                }
            } catch (\Exception $e) {
                // If not JSON, use body as is
                if (!empty($errorBody)) {
                    $errorMessage .= ' - ' . substr($errorBody, 0, 200); // Limit length
                }
            }

            Log::error('CCAvenue API - HTTP request failed', [
                'status' => $response->status(),
                'body' => $errorBody,
                'error_message' => $errorMessage,
            ]);

            return [
                'success' => false,
                'error' => $errorMessage,
            ];

        } catch (\Exception $e) {
            Log::error('CCAvenue API Error', [
                'error' => $e->getMessage(),
                'order_id' => $orderData['order_id'] ?? null,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check transaction status using Status API
     */
    public function checkTransactionStatus($orderId, $referenceNo = null)
    {
        try {
            $credentials = $this->getCredentials();
            $apiUrl = $this->getApiUrl();

            $requestData = [
                'order_no' => $orderId,
            ];

            if ($referenceNo) {
                $requestData['reference_no'] = $referenceNo;
            }

            $queryString = http_build_query($requestData);
            $encryptedData = $this->encrypt($queryString, $credentials['working_key']);

            $apiRequest = [
                'enc_request' => $encryptedData,
                'access_code' => $credentials['access_code'],
                'command' => 'orderStatusTracker',
                'request_type' => 'JSON',
                'response_type' => 'JSON',
                'version' => '1.1',
            ];

            $response = Http::timeout(30)
                ->asForm()
                ->post($apiUrl, $apiRequest);

            if ($response->successful()) {
                $responseData = $response->json();
                
                if (isset($responseData['status']) && $responseData['status'] == '0') {
                    $decryptedResponse = $this->decrypt($responseData['enc_response'], $credentials['working_key']);
                    return json_decode($decryptedResponse, true);
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('CCAvenue Status API Error', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
            ]);
            return null;
        }
    }
}
