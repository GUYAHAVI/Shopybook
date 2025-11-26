<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HostPinnacleSmsService
{
    protected $userId;
    protected $password;
    protected $senderId;
    protected $apiUrl;
    protected $apiKey; // Optional

    public function __construct()
    {
        $this->userId = config('services.hostpinnacle.user_id');
        $this->password = config('services.hostpinnacle.password');
        $this->senderId = config('services.hostpinnacle.sender_id', 'SHOPYBOOK');
        $this->apiUrl = config('services.hostpinnacle.api_url', 'https://smsportal.hostpinnacle.co.ke/SMSApi/send');
        $this->apiKey = config('services.hostpinnacle.api_key'); // Optional
        
        // Log credential status (without exposing actual values)
        Log::info('HostPinnacleSmsService initialized', [
            'user_id_set' => !empty($this->userId),
            'user_id_value' => $this->userId ? substr($this->userId, 0, 3) . '***' : 'NOT SET',
            'password_set' => !empty($this->password),
            'password_length' => $this->password ? strlen($this->password) : 0,
            'sender_id' => $this->senderId,
            'sender_id_set' => !empty($this->senderId),
            'api_url' => $this->apiUrl,
            'api_key_set' => !empty($this->apiKey),
            'configured' => $this->isConfigured(),
        ]);
    }

    /**
     * Check if SMS service is properly configured
     */
    public function isConfigured(): bool
    {
        $isConfigured = !empty($this->userId) && !empty($this->password) && !empty($this->senderId);
        
        if (!$isConfigured) {
            Log::warning('SMS service configuration incomplete', [
                'user_id_missing' => empty($this->userId),
                'password_missing' => empty($this->password),
                'sender_id_missing' => empty($this->senderId),
                'help' => 'Add HOSTPINNACLE_USER_ID, HOSTPINNACLE_PASSWORD, and HOSTPINNACLE_SENDER_ID to your .env file',
            ]);
        }
        
        return $isConfigured;
    }

    /**
     * Send SMS to a single recipient
     * 
     * @param string $phone Phone number (e.g., 254712345678)
     * @param string $message Message content
     * @param array $options Additional options (msgType, scheduleTime, etc.)
     * @return array Response with success status
     */
    public function sendSms(string $phone, string $message, array $options = []): array
    {
        return $this->sendBulkSms([$phone], $message, $options);
    }

    /**
     * Send bulk SMS to multiple recipients
     * 
     * @param array $phones Array of phone numbers
     * @param string $message Message content
     * @param array $options Additional options (msgType, scheduleTime, duplicatecheck, etc.)
     * @return array Response with success status
     */
    public function sendBulkSms(array $phones, string $message, array $options = []): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'SMS service not configured. Please set HOSTPINNACLE_USER_ID, HOSTPINNACLE_PASSWORD, and HOSTPINNACLE_SENDER_ID in your .env file.',
                'error' => 'Missing credentials'
            ];
        }

        if (empty($phones)) {
            return [
                'success' => false,
                'message' => 'No phone numbers provided',
                'error' => 'Empty recipients list'
            ];
        }

        // Format phone numbers (remove spaces, ensure proper format)
        $phones = array_map(function($phone) {
            return $this->formatPhoneNumber($phone);
        }, $phones);

        // Remove invalid numbers
        $phones = array_filter($phones);

        if (empty($phones)) {
            return [
                'success' => false,
                'message' => 'No valid phone numbers found',
                'error' => 'All phone numbers are invalid'
            ];
        }

        try {
            // Build request data according to Host Pinnacle API
            $data = [
                'userid' => $this->userId,
                'password' => $this->password,
                'mobile' => implode(',', $phones), // Comma-separated phone numbers
                'senderid' => $this->senderId,
                'msg' => $message,
                'sendMethod' => $options['sendMethod'] ?? 'quick', // quick, group, or bulkupload
                'msgType' => $options['msgType'] ?? 'text', // text or unicode
                'output' => $options['output'] ?? 'json', // json, xml, or plain
                'duplicatecheck' => $options['duplicatecheck'] ?? 'true', // Remove duplicates
            ];

            // Add optional parameters
            if (!empty($options['scheduleTime'])) {
                $data['scheduleTime'] = $options['scheduleTime']; // Format: YYYY-MM-DD HH:MM
            }

            if (!empty($options['trackLink']) && $options['trackLink'] === true) {
                $data['trackLink'] = 'true';
                if (!empty($options['smartLinkTitle'])) {
                    $data['smartLinkTitle'] = $options['smartLinkTitle'];
                }
            }

            // For testing without actual delivery
            if (!empty($options['test']) && $options['test'] === true) {
                $data['test'] = 'true';
            }

            Log::info('Sending SMS via Host Pinnacle', [
                'recipients_count' => count($phones),
                'message_length' => strlen($message),
                'send_method' => $data['sendMethod'],
            ]);

            // Make the API request
            $response = Http::asForm()->post($this->apiUrl, $data);

            $responseBody = $response->body();
            $statusCode = $response->status();

            Log::info('Host Pinnacle SMS Response', [
                'status' => $statusCode,
                'response_preview' => substr($responseBody, 0, 200),
                'response_length' => strlen($responseBody),
            ]);

            // Parse response based on output format
            if ($data['output'] === 'json') {
                $result = $response->json() ?? ['raw' => $responseBody];
            } else {
                $result = ['raw' => $responseBody];
            }

            // Check if request was successful
            if ($response->successful()) {
                // Check for error messages in successful responses
                $errorIndicators = ['error', 'fail', 'invalid', 'authentication', 'unauthorized'];
                $responseLower = strtolower($responseBody);
                
                foreach ($errorIndicators as $indicator) {
                    if (strpos($responseLower, $indicator) !== false) {
                        // Success status but error in response body
                        $this->logCredentialError($responseBody, $result);
                        
                        return [
                            'success' => false,
                            'message' => 'API returned an error: ' . ($result['message'] ?? $responseBody),
                            'error' => $responseBody,
                            'data' => $result,
                        ];
                    }
                }
                
                Log::info('SMS sent successfully', [
                    'recipients' => count($phones),
                    'api_response' => $result,
                ]);
                
                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'recipients' => count($phones),
                    'data' => $result,
                    'cost_estimate' => count($phones) * 1, // Estimate: 1 KSh per SMS (adjust based on your rate)
                ];
            }

            // Handle API errors with detailed logging
            $this->logCredentialError($responseBody, $result, $statusCode);

            return [
                'success' => false,
                'message' => 'Failed to send SMS. ' . ($result['message'] ?? 'API Error'),
                'error' => $responseBody,
                'data' => $result,
            ];

        } catch (\Exception $e) {
            Log::error('SMS service exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'SMS service error: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send SMS to a contact group (requires group to be created in Host Pinnacle dashboard)
     * 
     * @param string $groupName Group name created in Host Pinnacle
     * @param string $message Message content
     * @param array $options Additional options
     * @return array Response with success status
     */
    public function sendGroupSms(string $groupName, string $message, array $options = []): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'SMS service not configured',
                'error' => 'Missing credentials'
            ];
        }

        try {
            $data = [
                'userid' => $this->userId,
                'password' => $this->password,
                'group' => $groupName,
                'senderid' => $this->senderId,
                'msg' => $message,
                'sendMethod' => 'group',
                'msgType' => $options['msgType'] ?? 'text',
                'output' => $options['output'] ?? 'json',
                'duplicatecheck' => $options['duplicatecheck'] ?? 'true',
            ];

            if (!empty($options['scheduleTime'])) {
                $data['scheduleTime'] = $options['scheduleTime'];
            }

            $response = Http::asForm()->post($this->apiUrl, $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'SMS sent to group successfully',
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to send group SMS',
                'error' => $response->body(),
            ];

        } catch (\Exception $e) {
            Log::error('Group SMS exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Group SMS error',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Format phone number to Kenyan format
     * Converts: 0712345678 -> 254712345678
     *          +254712345678 -> 254712345678
     *          712345678 -> 254712345678
     */
    protected function formatPhoneNumber(string $phone): ?string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Skip if empty
        if (empty($phone)) {
            return null;
        }

        // Convert Kenyan format
        if (substr($phone, 0, 1) === '0') {
            // 0712345678 -> 254712345678
            $phone = '254' . substr($phone, 1);
        } elseif (strlen($phone) === 9) {
            // 712345678 -> 254712345678
            $phone = '254' . $phone;
        }

        // Validate: should be 12 digits for Kenyan numbers (254XXXXXXXXX)
        if (strlen($phone) < 10) {
            Log::warning('Invalid phone number', ['phone' => $phone]);
            return null;
        }

        return $phone;
    }

    /**
     * Get account balance (if API supports it)
     * You can implement this based on Host Pinnacle's account status endpoint
     */
    public function getAccountStatus(): array
    {
        try {
            $response = Http::get('https://smsportal.hostpinnacle.co.ke/SMSApi/account/readstatus', [
                'userid' => $this->userId,
                'password' => $this->password,
                'output' => 'json',
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to get account status',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error getting account status',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Log detailed credential error information
     * 
     * @param string $responseBody API response body
     * @param array $result Parsed result
     * @param int|null $statusCode HTTP status code
     */
    protected function logCredentialError(string $responseBody, array $result, ?int $statusCode = null): void
    {
        $responseLower = strtolower($responseBody);
        $errorType = 'Unknown Error';
        $solution = 'Check your credentials and API configuration';
        
        // Detect specific error types
        if (strpos($responseLower, 'authentication') !== false || strpos($responseLower, 'unauthorized') !== false) {
            $errorType = 'AUTHENTICATION FAILED';
            $solution = 'Your username or password is incorrect. Check HOSTPINNACLE_USER_ID and HOSTPINNACLE_PASSWORD in .env file';
        } elseif (strpos($responseLower, 'invalid user') !== false || strpos($responseLower, 'invalid userid') !== false) {
            $errorType = 'INVALID USERNAME';
            $solution = 'Username (USER_ID) is wrong. Check HOSTPINNACLE_USER_ID in .env file';
        } elseif (strpos($responseLower, 'invalid password') !== false || strpos($responseLower, 'wrong password') !== false) {
            $errorType = 'INVALID PASSWORD';
            $solution = 'Password is wrong. Check HOSTPINNACLE_PASSWORD in .env file';
        } elseif (strpos($responseLower, 'invalid sender') !== false || strpos($responseLower, 'sender') !== false) {
            $errorType = 'INVALID SENDER ID';
            $solution = 'Sender ID is not approved or invalid. Check HOSTPINNACLE_SENDER_ID in .env file and ensure it is approved in Host Pinnacle dashboard';
        } elseif (strpos($responseLower, 'insufficient') !== false || strpos($responseLower, 'credit') !== false || strpos($responseLower, 'balance') !== false) {
            $errorType = 'INSUFFICIENT CREDITS';
            $solution = 'Your SMS account has insufficient credits. Top up your account at Host Pinnacle dashboard';
        } elseif (strpos($responseLower, 'invalid mobile') !== false || strpos($responseLower, 'invalid number') !== false) {
            $errorType = 'INVALID PHONE NUMBER';
            $solution = 'One or more phone numbers are invalid or in wrong format';
        } elseif ($statusCode === 401) {
            $errorType = 'UNAUTHORIZED ACCESS';
            $solution = 'Authentication failed. Check your username and password';
        } elseif ($statusCode === 403) {
            $errorType = 'ACCESS FORBIDDEN';
            $solution = 'Your account may be blocked or suspended. Contact Host Pinnacle support';
        }
        
        Log::error('SMS API Error Detected: ' . $errorType, [
            'error_type' => $errorType,
            'http_status' => $statusCode,
            'credentials_used' => [
                'user_id' => $this->userId ? substr($this->userId, 0, 3) . '***' : 'NOT SET',
                'password_length' => $this->password ? strlen($this->password) : 0,
                'sender_id' => $this->senderId,
            ],
            'api_response' => substr($responseBody, 0, 500),
            'parsed_result' => $result,
            'solution' => $solution,
            'help_url' => 'https://smsportal.hostpinnacle.co.ke',
        ]);
        
        // Additional specific warning for credentials
        if (in_array($errorType, ['AUTHENTICATION FAILED', 'INVALID USERNAME', 'INVALID PASSWORD', 'UNAUTHORIZED ACCESS'])) {
            Log::critical('🔴 CREDENTIAL ERROR - SMS CANNOT BE SENT', [
                'problem' => 'Your Host Pinnacle credentials are WRONG or MISSING',
                'current_config' => [
                    'user_id' => $this->userId ?? 'NOT SET',
                    'password_set' => !empty($this->password) ? 'YES (length: ' . strlen($this->password) . ')' : 'NO',
                    'sender_id' => $this->senderId ?? 'NOT SET',
                ],
                'action_required' => [
                    '1. Check your .env file',
                    '2. Verify credentials at https://smsportal.hostpinnacle.co.ke',
                    '3. Run: php artisan config:clear',
                    '4. Restart your server',
                ],
            ]);
        }
    }
}




