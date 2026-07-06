<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    /**
     * Show upgrade plans page
     */
    public function upgrade()
    {
        $user = Auth::user();
        $business = $user->business;
        
        return view('billing.upgrade', compact('business'));
    }

    /**
     * Handle plan upgrade with M-Pesa payment
     */
    public function processUpgrade(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|min:10|max:12',
            'platform' => 'nullable|string'
        ]);

        // Format phone number to 254 format
        $phoneNumber = $this->formatPhoneNumber($request->input('phone_number'));
        
        if (!$phoneNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number format. Please use format: 07XXXXXXXX or 254XXXXXXXXX'
            ], 422);
        }

        $user = Auth::user();
        $business = $user->business;
        $platform = $request->input('platform');

        try {
            // Step 1: Get access token from Daraja
            $accessToken = $this->getDarajaAccessToken();
            
            if (!$accessToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to authenticate with M-Pesa'
                ], 500);
            }

            // Step 2: Initiate STK Push
            $stkResponse = $this->initiateSTKPush($accessToken, $phoneNumber);
            
            if (!$stkResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $stkResponse['message']
                ], 500);
            }

            // Step 3: Update business plan (for testing, we'll upgrade immediately)
            $business->update([
                'plan' => 'premium',
                'upgraded_at' => now()
            ]);

            Log::info('Business upgraded to premium', [
                'business_id' => $business->id,
                'user_id' => $user->id,
                'phone_number' => $phoneNumber,
                'platform' => $platform,
                'checkout_request_id' => $stkResponse['checkout_request_id']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment initiated successfully. Please check your phone for M-Pesa prompt.',
                'checkout_request_id' => $stkResponse['checkout_request_id']
            ]);

        } catch (\Exception $e) {
            Log::error('M-Pesa payment failed', [
                'error' => $e->getMessage(),
                'business_id' => $business->id,
                'phone_number' => $phoneNumber
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Daraja access token
     */
    private function getDarajaAccessToken()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode(
                    config('services.mpesa.consumer_key') . ':' . config('services.mpesa.consumer_secret')
                )
            ])->get(config('services.mpesa.auth_url', 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'));

            if ($response->successful()) {
                $data = $response->json();
                if (empty($data['access_token'])) {
                    Log::error('Daraja access token missing from successful response', [
                        'response' => $response->body(),
                    ]);
                }
                return $data['access_token'] ?? null;
            }

            Log::error('Failed to get Daraja access token', [
                'response' => $response->body(),
                'status' => $response->status()
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Exception getting Daraja access token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Initiate STK Push
     */
    private function initiateSTKPush($accessToken, $phoneNumber)
    {
        try {
            $timestamp = date('YmdHis');
            $password = base64_encode(
                config('services.mpesa.business_shortcode') . 
                config('services.mpesa.passkey') . 
                $timestamp
            );

            // Log the request for debugging
            Log::info('Initiating STK Push', [
                'phone_number' => $phoneNumber,
                'business_shortcode' => config('services.mpesa.business_shortcode'),
                'timestamp' => $timestamp,
                'callback_url' => config('services.mpesa.callback_url')
            ]);

            $requestData = [
                'BusinessShortCode' => config('services.mpesa.business_shortcode'),
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => config('services.mpesa.transaction_type'),
                'Amount' => 1,
                'PartyA' => $phoneNumber,
                'PartyB' => config('services.mpesa.business_shortcode'),
                'PhoneNumber' => $phoneNumber,
                'CallBackURL' => config('services.mpesa.callback_url'),
                'AccountReference' => config('services.mpesa.account_reference'),
                'TransactionDesc' => config('services.mpesa.transaction_desc')
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->post(config('services.mpesa.stk_push_url', 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'), $requestData);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('STK Push response', [
                    'response_data' => $data,
                    'status_code' => $response->status()
                ]);
                
                if (isset($data['CheckoutRequestID'])) {
                    return [
                        'success' => true,
                        'checkout_request_id' => $data['CheckoutRequestID'],
                        'message' => 'STK Push sent successfully'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Invalid response from M-Pesa: ' . ($data['errorMessage'] ?? 'Unknown error')
                    ];
                }
            }

            Log::error('STK Push failed', [
                'response' => $response->body(),
                'status' => $response->status()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to initiate payment'
            ];

        } catch (\Exception $e) {
            Log::error('Exception in STK Push', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Payment initiation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Show billing dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        $business = $user->business;
        
        return view('billing.dashboard', compact('business'));
    }

    /**
     * Cancel subscription
     */
    public function cancel()
    {
        $user = Auth::user();
        $business = $user->business;

        $business->update([
            'plan' => 'free',
            'cancelled_at' => now()
        ]);

        return redirect()->route('billing.dashboard')
            ->with('success', 'Subscription cancelled successfully.');
    }

    /**
     * Handle M-Pesa callback
     */
    public function mpesaCallback(Request $request)
    {
        Log::info('M-Pesa callback received', [
            'request_data' => $request->all()
        ]);

        try {
            $callbackData = $request->all();
            
            if (isset($callbackData['Body']['stkCallback'])) {
                $stkCallback = $callbackData['Body']['stkCallback'];
                $resultCode = $stkCallback['ResultCode'] ?? null;
                $checkoutRequestId = $stkCallback['CheckoutRequestID'] ?? null;
                
                Log::info('Processing M-Pesa callback', [
                    'result_code' => $resultCode,
                    'checkout_request_id' => $checkoutRequestId
                ]);

                if ($resultCode === 0) {
                    // Payment successful
                    $callbackMetadata = $stkCallback['CallbackMetadata']['Item'] ?? [];
                    $amount = null;
                    $mpesaReceiptNumber = null;
                    $phoneNumber = null;
                    
                    foreach ($callbackMetadata as $item) {
                        switch ($item['Name']) {
                            case 'Amount':
                                $amount = $item['Value'];
                                break;
                            case 'MpesaReceiptNumber':
                                $mpesaReceiptNumber = $item['Value'];
                                break;
                            case 'PhoneNumber':
                                $phoneNumber = $item['Value'];
                                break;
                        }
                    }

                    Log::info('M-Pesa payment successful', [
                        'amount' => $amount,
                        'receipt_number' => $mpesaReceiptNumber,
                        'phone_number' => $phoneNumber,
                        'checkout_request_id' => $checkoutRequestId
                    ]);

                    // Here you could update the business plan if not already done
                    // For now, we'll just log the success
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Payment processed successfully'
                    ]);
                } else {
                    // Payment failed
                    $resultDesc = $stkCallback['ResultDesc'] ?? 'Unknown error';
                    
                    Log::warning('M-Pesa payment failed', [
                        'result_code' => $resultCode,
                        'result_desc' => $resultDesc,
                        'checkout_request_id' => $checkoutRequestId
                    ]);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment failed: ' . $resultDesc
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid callback data'
            ]);

        } catch (\Exception $e) {
            Log::error('Error processing M-Pesa callback', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

                         return response()->json([
                 'success' => false,
                 'message' => 'Error processing callback'
             ], 500);
         }
     }

     /**
      * Format phone number to 254 format
      */
     private function formatPhoneNumber($phoneNumber)
     {
         // Remove any non-digit characters
         $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
         
         // If it's already in 254 format, return as is
         if (strlen($phoneNumber) === 12 && substr($phoneNumber, 0, 3) === '254') {
             return $phoneNumber;
         }
         
         // If it's in 07 format, convert to 254
         if (strlen($phoneNumber) === 10 && substr($phoneNumber, 0, 1) === '0') {
             return '254' . substr($phoneNumber, 1);
         }
         
         // If it's in 7 format, convert to 254
         if (strlen($phoneNumber) === 9 && substr($phoneNumber, 0, 1) === '7') {
             return '254' . $phoneNumber;
         }
         
         // If it's already 9 digits starting with 7, add 254
         if (strlen($phoneNumber) === 9 && substr($phoneNumber, 0, 1) === '7') {
             return '254' . $phoneNumber;
         }
         
         // Invalid format
         return null;
     }
 }
