<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Process subscription upgrade with Paystack M-Pesa
     */
    public function upgrade(Request $request)
    {
        try {
            $request->validate([
                'plan' => 'required|in:premium,enterprise',
                'phone_number' => 'required|string|min:10|max:12',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please fill all required fields correctly.'
                ], 422);
            }
            throw $e;
        }

        $user = Auth::user();
        $business = $user->business;
        
        if (!$business) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business not found.'
                ], 404);
            }
            return back()->with('error', 'Business not found.');
        }

        // Check if business is on trial
        if ($business->isOnTrial()) {
            // End trial and upgrade to paid plan
            $business->update(['on_trial' => false]);
            Log::info('Ending trial for upgrade', [
                'business_id' => $business->id,
                'trial_ended_at' => now()
            ]);
        }

        $plan = $request->input('plan');
        $phoneNumber = $this->formatPhoneNumber($request->input('phone_number'));
        
        if (!$phoneNumber) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid phone number format. Please use format: 07XXXXXXXX or 254XXXXXXXXX'
                ], 422);
            }
            return back()->with('error', 'Invalid phone number format. Please use format: 07XXXXXXXX or 254XXXXXXXXX');
        }
        
        // TEST MODE: Using small amounts for testing
        $amount = $plan === 'enterprise' ? 1000 : 500 ; // KES: 1000 for Enterprise, 500 for Premium

        try {
            // Initialize Paystack M-Pesa charge
            Log::info('Starting Paystack M-Pesa initialization', [
                'business_id' => $business->id,
                'plan' => $plan,
                'phone_number' => $phoneNumber,
                'amount' => $amount
            ]);
            
            $response = $this->initializePaystackMpesa($phoneNumber, $amount, $plan, $business, $user);
            
            Log::info('Paystack initialization response', [
                'success' => $response['success'],
                'message' => $response['message'],
                'reference' => $response['reference'] ?? null
            ]);
            
            if (!$response['success']) {
                Log::error('Paystack M-Pesa initialization failed', [
                    'business_id' => $business->id,
                    'plan' => $plan,
                    'error' => $response['message']
                ]);
                
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $response['message']
                    ], 400);
                }
                return back()->with('error', $response['message']);
            }

            // Store pending payment in database
            DB::table('subscription_payments')->insert([
                'business_id' => $business->id,
                'user_id' => $user->id,
                'plan' => $plan,
                'amount' => $amount,
                'phone_number' => $phoneNumber,
                'checkout_request_id' => $response['reference'],
                'merchant_request_id' => null,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('Paystack M-Pesa charge initiated', [
                'business_id' => $business->id,
                'user_id' => $user->id,
                'plan' => $plan,
                'amount' => $amount,
                'phone_number' => $phoneNumber,
                'reference' => $response['reference']
            ]);

            // Return JSON response for AJAX handling (no page redirect)
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'M-Pesa payment request sent! Please check your phone and enter your M-Pesa PIN to complete the payment.',
                    'reference' => $response['reference']
                ]);
            }

            // Fallback for non-AJAX requests
            return back()
                ->with('success', 'M-Pesa payment request sent! Please check your phone and enter your M-Pesa PIN to complete the payment.')
                ->with('payment_reference', $response['reference']);

        } catch (\Exception $e) {
            Log::error('Subscription upgrade exception', [
                'error' => $e->getMessage(),
                'business_id' => $business->id,
                'plan' => $plan,
                'phone_number' => $phoneNumber,
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment initialization failed. Please try again or contact support.'
                ], 500);
            }

            return back()->with('error', 'Payment initialization failed. Please try again or contact support.');
        }
    }

    /**
     * Paystack webhook handler for M-Pesa payments
     */
    public function paystackWebhook(Request $request)
    {
        // Verify webhook signature
        $signature = $request->header('x-paystack-signature');
        $secretKey = config('services.paystack.secret_key');
        
        $computedSignature = hash_hmac('sha512', $request->getContent(), $secretKey);
        
        if ($signature !== $computedSignature) {
            Log::error('Invalid Paystack webhook signature');
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
        }

        $event = $request->input('event');
        $data = $request->input('data');
        
        Log::info('Paystack Webhook Received', ['event' => $event, 'data' => $data]);

        try {
            if ($event === 'charge.success') {
                $reference = $data['reference'] ?? null;
                
                if (!$reference) {
                    Log::warning('Paystack webhook missing reference');
                    return response()->json(['status' => 'error'], 400);
                }

                // Find the pending payment
                $payment = DB::table('subscription_payments')
                    ->where('checkout_request_id', $reference)
                    ->where('status', 'pending')
                    ->first();

                if (!$payment) {
                    Log::warning('Payment record not found for Paystack webhook', [
                        'reference' => $reference
                    ]);
                    return response()->json(['status' => 'ok'], 200);
                }

                // Update payment record
                DB::table('subscription_payments')
                    ->where('id', $payment->id)
                    ->update([
                        'status' => 'completed',
                        'mpesa_receipt_number' => $data['reference'],
                        'transaction_date' => now(),
                        'result_desc' => 'Payment successful via Paystack M-Pesa',
                        'updated_at' => now()
                    ]);

                // Upgrade business plan
                $business = \App\Models\Business::find($payment->business_id);
                if ($business) {
                    $business->update([
                        'plan' => $payment->plan,
                        'upgraded_at' => now()
                    ]);

                    Log::info('Business subscription upgraded via Paystack M-Pesa webhook', [
                        'business_id' => $business->id,
                        'plan' => $payment->plan,
                        'amount' => $payment->amount,
                        'reference' => $reference
                    ]);
                }

                return response()->json(['status' => 'success'], 200);

            } elseif ($event === 'charge.failed') {
                $reference = $data['reference'] ?? null;
                
                if ($reference) {
                    DB::table('subscription_payments')
                        ->where('checkout_request_id', $reference)
                        ->where('status', 'pending')
                        ->update([
                            'status' => 'failed',
                            'result_desc' => $data['gateway_response'] ?? 'Payment failed',
                            'updated_at' => now()
                        ]);

                    Log::warning('Paystack M-Pesa payment failed', [
                        'reference' => $reference,
                        'gateway_response' => $data['gateway_response'] ?? 'Unknown'
                    ]);
                }
            }

            return response()->json(['status' => 'ok'], 200);

        } catch (\Exception $e) {
            Log::error('Paystack webhook exception', [
                'error' => $e->getMessage(),
                'event' => $event,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Check payment status - Verify with Paystack API directly
     */
    public function checkPaymentStatus(Request $request)
    {
        $request->validate([
            'reference' => 'required|string'
        ]);

        $reference = $request->input('reference');
        
        // First check local database
        $payment = DB::table('subscription_payments')
            ->where('checkout_request_id', $reference)
            ->first();

        if (!$payment) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Payment record not found'
            ], 404);
        }

        // If already completed or failed in database, return that status
        if ($payment->status === 'completed' || $payment->status === 'failed') {
            return response()->json([
                'status' => $payment->status,
                'plan' => $payment->plan,
                'amount' => $payment->amount,
                'reference' => $payment->checkout_request_id,
                'result_desc' => $payment->result_desc,
                'updated_at' => $payment->updated_at
            ]);
        }

        // If still pending, verify with Paystack API directly
        try {
            $secretKey = config('services.paystack.secret_key');
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey,
            ])->get("https://api.paystack.co/transaction/verify/{$reference}");

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Paystack verification response', [
                    'reference' => $reference,
                    'data' => $data
                ]);

                if (isset($data['data'])) {
                    $transactionData = $data['data'];
                    $status = $transactionData['status'] ?? 'pending';
                    
                    // Update local database based on Paystack status
                    if ($status === 'success') {
                        DB::table('subscription_payments')
                            ->where('id', $payment->id)
                            ->update([
                                'status' => 'completed',
                                'mpesa_receipt_number' => $transactionData['reference'] ?? $reference,
                                'transaction_date' => now(),
                                'result_desc' => 'Payment successful via Paystack M-Pesa',
                                'updated_at' => now()
                            ]);

                        // Upgrade business plan
                        $business = \App\Models\Business::find($payment->business_id);
                        if ($business) {
                            $business->update([
                                'plan' => $payment->plan,
                                'upgraded_at' => now()
                            ]);

                            Log::info('Business subscription upgraded via polling', [
                                'business_id' => $business->id,
                                'plan' => $payment->plan,
                                'reference' => $reference
                            ]);
                        }

                        return response()->json([
                            'status' => 'completed',
                            'plan' => $payment->plan,
                            'amount' => $payment->amount,
                            'reference' => $reference,
                            'result_desc' => 'Payment successful',
                            'updated_at' => now()
                        ]);
                        
                    } elseif ($status === 'failed') {
                        DB::table('subscription_payments')
                            ->where('id', $payment->id)
                            ->update([
                                'status' => 'failed',
                                'result_desc' => $transactionData['gateway_response'] ?? 'Payment failed',
                                'updated_at' => now()
                            ]);

                        return response()->json([
                            'status' => 'failed',
                            'plan' => $payment->plan,
                            'amount' => $payment->amount,
                            'reference' => $reference,
                            'result_desc' => $transactionData['gateway_response'] ?? 'Payment failed',
                            'updated_at' => now()
                        ]);
                    }
                }
            }
            
            // If API call failed or status is still pending, return pending
            return response()->json([
                'status' => 'pending',
                'plan' => $payment->plan,
                'amount' => $payment->amount,
                'reference' => $payment->checkout_request_id,
                'result_desc' => 'Payment processing',
                'updated_at' => $payment->updated_at
            ]);

        } catch (\Exception $e) {
            Log::error('Error verifying payment with Paystack', [
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);

            // Return current database status on error
            return response()->json([
                'status' => $payment->status,
                'plan' => $payment->plan,
                'amount' => $payment->amount,
                'reference' => $payment->checkout_request_id,
                'result_desc' => $payment->result_desc,
                'updated_at' => $payment->updated_at
            ]);
        }
    }

    /**
     * Initialize Paystack M-Pesa charge (STK Push)
     */
    private function initializePaystackMpesa($phoneNumber, $amount, $plan, $business, $user)
    {
        try {
            $secretKey = config('services.paystack.secret_key');
            
            if (!$secretKey) {
                return [
                    'success' => false,
                    'message' => 'Paystack secret key not configured'
                ];
            }

            $planName = $plan === 'enterprise' ? 'Enterprise' : 'Premium';
            $reference = 'SB-' . strtoupper($plan) . '-' . time() . '-' . $business->id;
            
            // Convert amount to kobo (Paystack uses smallest currency unit)
            // For KES, 1 KES = 100 cents
            $amountInCents = $amount * 100;

            $requestData = [
                'email' => $user->email, // Email is required by Paystack even for mobile money
                'amount' => $amountInCents,
                'currency' => 'KES',
                'reference' => $reference,
                'mobile_money' => [
                    'phone' => $phoneNumber,
                    'provider' => 'mpesa' // Specify M-Pesa as provider
                ],
                'metadata' => [
                    'business_id' => $business->id,
                    'business_name' => $business->name,
                    'plan' => $plan,
                    'plan_name' => $planName,
                    'user_id' => $user->id,
                    'phone_number' => $phoneNumber
                ]
            ];

            Log::info('Initializing Paystack M-Pesa charge', [
                'phone_number' => $phoneNumber,
                'amount' => $amount,
                'plan' => $plan,
                'reference' => $reference
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey,
                'Content-Type' => 'application/json'
            ])->post('https://api.paystack.co/charge', $requestData);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Paystack M-Pesa charge response', [
                    'data' => $data,
                    'status_isset' => isset($data['status']),
                    'status_value' => $data['status'] ?? 'not_set',
                    'data_isset' => isset($data['data'])
                ]);
                
                // Paystack returns status: true/1 for success at root level
                // The actual charge status is in data.status (e.g., 'pay_offline', 'success', 'pending')
                if (isset($data['status']) && ($data['status'] === true || $data['status'] === 1)) {
                    // Check if we have charge data
                    if (isset($data['data'])) {
                        $chargeData = $data['data'];
                        $chargeStatus = $chargeData['status'] ?? 'unknown';
                        
                        Log::info('M-Pesa charge initiated', [
                            'reference' => $reference,
                            'charge_status' => $chargeStatus,
                            'display_text' => $chargeData['display_text'] ?? null,
                            'taking_success_path' => in_array($chargeStatus, ['pay_offline', 'pending', 'success'])
                        ]);
                        
                        // pay_offline means STK push was sent successfully
                        if (in_array($chargeStatus, ['pay_offline', 'pending', 'success'])) {
                            return [
                                'success' => true,
                                'reference' => $reference,
                                'message' => 'M-Pesa STK Push sent successfully'
                            ];
                        }
                        
                        // If charge status is not in expected states, log and continue
                        Log::warning('Unexpected charge status', [
                            'charge_status' => $chargeStatus,
                            'full_charge_data' => $chargeData
                        ]);
                    }
                    
                    // Fallback success response if status is true but no specific charge status matched
                    Log::info('Returning fallback success response');
                    return [
                        'success' => true,
                        'reference' => $reference,
                        'message' => 'Payment initiated successfully'
                    ];
                } else {
                    Log::warning('Paystack M-Pesa charge returned unsuccessful status', [
                        'full_response' => $data,
                        'status' => $data['status'] ?? 'not_set'
                    ]);
                    
                    return [
                        'success' => false,
                        'message' => $data['message'] ?? 'Failed to initiate M-Pesa payment'
                    ];
                }
            }

            $errorBody = $response->json();
            $errorMessage = $errorBody['message'] ?? 'Failed to initiate M-Pesa payment. Please try again.';
            
            Log::error('Paystack M-Pesa charge request failed', [
                'response' => $response->body(),
                'status' => $response->status(),
                'error_data' => $errorBody
            ]);

            return [
                'success' => false,
                'message' => $errorMessage
            ];

        } catch (\Exception $e) {
            Log::error('Paystack M-Pesa initialization exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Payment system error. Please try again.'
            ];
        }
    }

    /**
     * Format phone number to international format
     */
    private function formatPhoneNumber($phone)
    {
        // Remove any spaces, dashes, or special characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Convert 07XXXXXXXX to 254XXXXXXXXX
        if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            $phone = '254' . substr($phone, 1);
        }
        
        // Ensure we have 254 format (12 digits starting with 254)
        if (strlen($phone) === 12 && substr($phone, 0, 3) === '254') {
            return '+' . $phone; // Paystack requires +254 format
        }
        
        // Invalid format
        Log::warning('Invalid phone number format', ['phone' => $phone]);
        return null;
    }
}
