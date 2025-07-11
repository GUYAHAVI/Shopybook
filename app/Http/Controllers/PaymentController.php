<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Business;

class PaymentController extends Controller
{
    // M-Pesa Configuration
    private $mpesaConfig = [
        'consumer_key' => 'your_mpesa_consumer_key',
        'consumer_secret' => 'your_mpesa_consumer_secret',
        'passkey' => 'your_mpesa_passkey',
        'shortcode' => '174379', // Test shortcode
        'environment' => 'sandbox', // or 'live'
        'callback_url' => 'https://your-domain.com/api/mpesa/callback',
    ];

    // PayPal Configuration
    private $paypalConfig = [
        'client_id' => 'your_paypal_client_id',
        'client_secret' => 'your_paypal_client_secret',
        'mode' => 'sandbox', // or 'live'
        'currency' => 'USD',
    ];

    public function checkout(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:mpesa,paypal',
        ]);

        $order = Order::findOrFail($request->order_id);
        
        // Check if order belongs to current business
        if ($order->business_id !== auth()->user()->business->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        switch ($request->payment_method) {
            case 'mpesa':
                return $this->initiateMpesaPayment($order);
            case 'paypal':
                return $this->initiatePayPalPayment($order);
            default:
                return response()->json(['error' => 'Invalid payment method'], 400);
        }
    }

    public function initiateMpesaPayment(Order $order)
    {
        try {
            // Generate M-Pesa STK Push request
            $timestamp = date('YmdHis');
            $password = base64_encode($this->mpesaConfig['shortcode'] . $this->mpesaConfig['passkey'] . $timestamp);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->getMpesaAccessToken(),
                'Content-Type' => 'application/json',
            ])->post($this->getMpesaUrl('stkpush'), [
                'BusinessShortCode' => $this->mpesaConfig['shortcode'],
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => $order->total_amount,
                'PartyA' => '254700000000', // Customer phone number (dummy)
                'PartyB' => $this->mpesaConfig['shortcode'],
                'PhoneNumber' => '254700000000', // Customer phone number (dummy)
                'CallBackURL' => $this->mpesaConfig['callback_url'],
                'AccountReference' => 'Shopybook-' . $order->id,
                'TransactionDesc' => 'Payment for Order #' . $order->id,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Create payment record
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => 'mpesa',
                    'amount' => $order->total_amount,
                    'currency' => 'KES',
                    'transaction_id' => $data['CheckoutRequestID'] ?? 'MPESA_' . uniqid(),
                    'status' => 'pending',
                    'gateway_response' => $data,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'M-Pesa payment initiated',
                    'checkout_request_id' => $data['CheckoutRequestID'],
                    'merchant_request_id' => $data['MerchantRequestID'],
                    'instructions' => 'Please check your phone for M-Pesa prompt and enter your PIN to complete payment.',
                ]);
            } else {
                Log::error('M-Pesa STK Push failed', [
                    'order_id' => $order->id,
                    'response' => $response->json(),
                ]);

                return response()->json([
                    'error' => 'Failed to initiate M-Pesa payment',
                    'details' => $response->json(),
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('M-Pesa payment error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Payment service temporarily unavailable',
            ], 500);
        }
    }

    public function initiatePayPalPayment(Order $order)
    {
        try {
            // Get PayPal access token
            $accessToken = $this->getPayPalAccessToken();

            // Create PayPal order
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->getPayPalUrl('orders'), [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => 'Shopybook-' . $order->id,
                        'amount' => [
                            'currency_code' => $this->paypalConfig['currency'],
                            'value' => number_format($order->total_amount, 2),
                        ],
                        'description' => 'Payment for Order #' . $order->id,
                    ],
                ],
                'application_context' => [
                    'return_url' => route('payment.paypal.success'),
                    'cancel_url' => route('payment.paypal.cancel'),
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Create payment record
                Payment::create([
                    'order_id' => $order->id,
                    'payment_method' => 'paypal',
                    'amount' => $order->total_amount,
                    'currency' => $this->paypalConfig['currency'],
                    'transaction_id' => $data['id'],
                    'status' => 'pending',
                    'gateway_response' => $data,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'PayPal payment initiated',
                    'paypal_order_id' => $data['id'],
                    'approval_url' => $data['links'][1]['href'] ?? null,
                ]);
            } else {
                Log::error('PayPal order creation failed', [
                    'order_id' => $order->id,
                    'response' => $response->json(),
                ]);

                return response()->json([
                    'error' => 'Failed to initiate PayPal payment',
                    'details' => $response->json(),
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('PayPal payment error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Payment service temporarily unavailable',
            ], 500);
        }
    }

    public function mpesaCallback(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('M-Pesa callback received', $data);

            // Verify the callback
            if (isset($data['Body']['stkCallback'])) {
                $callback = $data['Body']['stkCallback'];
                $resultCode = $callback['ResultCode'];
                $checkoutRequestId = $callback['CheckoutRequestID'];

                // Find payment by checkout request ID
                $payment = Payment::where('transaction_id', $checkoutRequestId)->first();

                if ($payment) {
                    if ($resultCode === 0) {
                        // Payment successful
                        $payment->update([
                            'status' => 'completed',
                            'gateway_response' => array_merge($payment->gateway_response ?? [], ['callback' => $data]),
                        ]);

                        // Update order status
                        $payment->order->update(['status' => 'paid']);

                        Log::info('M-Pesa payment completed', [
                            'payment_id' => $payment->id,
                            'order_id' => $payment->order_id,
                        ]);
                    } else {
                        // Payment failed
                        $payment->update([
                            'status' => 'failed',
                            'gateway_response' => array_merge($payment->gateway_response ?? [], ['callback' => $data]),
                        ]);

                        Log::warning('M-Pesa payment failed', [
                            'payment_id' => $payment->id,
                            'result_code' => $resultCode,
                        ]);
                    }
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('M-Pesa callback error', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);

            return response()->json(['status' => 'error'], 500);
        }
    }

    public function paypalSuccess(Request $request)
    {
        try {
            $token = $request->query('token');
            
            if (!$token) {
                return redirect()->route('payment.failed')->with('error', 'Invalid payment token');
            }

            // Find payment by PayPal order ID
            $payment = Payment::where('transaction_id', $token)->first();

            if (!$payment) {
                return redirect()->route('payment.failed')->with('error', 'Payment not found');
            }

            // Capture PayPal payment
            $accessToken = $this->getPayPalAccessToken();
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->getPayPalUrl("orders/{$token}/capture"));

            if ($response->successful()) {
                $data = $response->json();
                
                $payment->update([
                    'status' => 'completed',
                    'gateway_response' => array_merge($payment->gateway_response ?? [], ['capture' => $data]),
                ]);

                $payment->order->update(['status' => 'paid']);

                return redirect()->route('payment.success')->with('success', 'Payment completed successfully!');
            } else {
                $payment->update(['status' => 'failed']);
                return redirect()->route('payment.failed')->with('error', 'Payment capture failed');
            }
        } catch (\Exception $e) {
            Log::error('PayPal success callback error', [
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('payment.failed')->with('error', 'Payment processing error');
        }
    }

    public function paypalCancel(Request $request)
    {
        $token = $request->query('token');
        
        if ($token) {
            $payment = Payment::where('transaction_id', $token)->first();
            if ($payment) {
                $payment->update(['status' => 'cancelled']);
            }
        }

        return redirect()->route('payment.failed')->with('error', 'Payment was cancelled');
    }

    public function paymentSuccess()
    {
        return view('payment.success');
    }

    public function paymentFailed()
    {
        return view('payment.failed');
    }

    public function paymentHistory()
    {
        $payments = auth()->user()->business->payments()
            ->with('order')
            ->latest()
            ->paginate(15);

        return view('payment.history', compact('payments'));
    }

    // Helper methods for M-Pesa
    private function getMpesaAccessToken()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->mpesaConfig['consumer_key'] . ':' . $this->mpesaConfig['consumer_secret']),
        ])->get($this->getMpesaUrl('oauth1/v1/generate'));

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        throw new \Exception('Failed to get M-Pesa access token');
    }

    private function getMpesaUrl($endpoint)
    {
        $baseUrl = $this->mpesaConfig['environment'] === 'live' 
            ? 'https://api.safaricom.co.ke' 
            : 'https://sandbox.safaricom.co.ke';

        return $baseUrl . '/' . $endpoint;
    }

    // Helper methods for PayPal
    private function getPayPalAccessToken()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($this->paypalConfig['client_id'] . ':' . $this->paypalConfig['client_secret']),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->asForm()->post($this->getPayPalUrl('v1/oauth2/token'), [
            'grant_type' => 'client_credentials',
        ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        throw new \Exception('Failed to get PayPal access token');
    }

    private function getPayPalUrl($endpoint)
    {
        $baseUrl = $this->paypalConfig['mode'] === 'live' 
            ? 'https://api-m.paypal.com' 
            : 'https://api-m.sandbox.paypal.com';

        return $baseUrl . '/' . $endpoint;
    }
}
