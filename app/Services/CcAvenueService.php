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
     * Get Hosted Payment Page URL based on environment
     */
    public function getHostedPaymentUrl()
    {
        $env = config('constants.ccavenue.environment', 'production');
        if ($env === 'test') {
            return 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
        }
        return 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
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
     * Initiate transaction using hosted payment page (same approach as PaymentGatewayController)
     * Returns encrypted data that should be used in a form POST to CCAvenue hosted page
     * 
     * @param array $orderData Order data including merchant_id, order_id, amount, etc.
     * @return array Response with encrypted_data, access_code, and hosted_payment_url
     */
    public function initiateTransaction($orderData)
    {
        try {
            $credentials = $this->getCredentials();

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

            // Build query string and encrypt (same as PaymentGatewayController)
            $queryString = http_build_query($requestData);
            $encryptedData = $this->encrypt($queryString, $credentials['working_key']);

            Log::info('CCAvenue - Transaction prepared', [
                'order_id' => $orderData['order_id'],
                'amount' => $orderData['amount'],
                'currency' => $orderData['currency'] ?? 'INR',
            ]);

            // Return encrypted data and access code for form submission
            // The caller should use this to create a form POST to the hosted payment page
            return [
                'success' => true,
                'encrypted_data' => $encryptedData,
                'access_code' => $credentials['access_code'],
                'hosted_payment_url' => $this->getHostedPaymentUrl(),
                'order_id' => $orderData['order_id'],
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
