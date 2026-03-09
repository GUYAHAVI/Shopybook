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

    /**
     * Initiate a Paystack M-Pesa STK push for the authenticated business's customer.
     * The business's own Paystack credentials are used — each business collects into their own account.
     */
    public function paystackMpesaCharge(Request $request)
    {
        $request->validate([
            'phone'  => 'required|string|min:9|max:15',
            'amount' => 'required|numeric|min:1',
            'email'  => 'nullable|email|max:255',
        ]);

        $business = auth()->user()->business;
        $settings = $business->getSettingsOrCreate();
        $config   = $settings->paystackConfig();

        if (!$config['enabled'] || empty($config['secret_key'])) {
            return response()->json(['error' => 'Paystack is not configured for this business.'], 422);
        }

        // Normalize to +254XXXXXXXXX — Paystack M-Pesa requires E.164 format with + prefix
        $raw   = $request->phone;
        $phone = preg_replace('/\D/', '', $raw); // strip +, spaces, dashes

        if (strlen($phone) === 10 && str_starts_with($phone, '0')) {
            // 07XXXXXXXX or 01XXXXXXXX → 254...
            $phone = '254' . substr($phone, 1);
        } elseif (strlen($phone) === 9 && (str_starts_with($phone, '7') || str_starts_with($phone, '1'))) {
            // 7XXXXXXXX → 254...
            $phone = '254' . $phone;
        } elseif (strlen($phone) === 13 && str_starts_with($phone, '0254')) {
            // 02547XXXXXXXX → trim leading 0
            $phone = substr($phone, 1);
        }
        // Prepend + for E.164 format Paystack requires
        $phone = '+' . ltrim($phone, '+');

        if (!preg_match('/^\+254[71]\d{8}$/', $phone)) {
            Log::warning('Paystack phone normalization failed', [
                'raw_input'  => $raw,
                'normalized' => $phone,
            ]);
            return response()->json(['error' => 'Invalid phone number. Use 0712345678 or +254712345678.'], 422);
        }

        Log::info('Paystack charge attempt', [
            'raw_phone'    => $raw,
            'normalized'   => $phone,
            'amount_kes'   => $request->amount,
            'amount_kobo'  => (int) round((float) $request->amount * 100),
            'key_prefix'   => substr($config['secret_key'] ?? '', 0, 10) . '...',
            'test_mode'    => $config['test_mode'],
        ]);

        // Build a unique reference for every charge so Paystack never sees a duplicate
        $reference = 'POS-' . strtoupper(str_replace('.', '-', uniqid('', true)));

        // Build a unique email per transaction.
        // Paystack blocks repeat charges with the same email, so we always append the
        // reference suffix — but we preserve the real customer/business domain when available.
        if (!empty($request->email)) {
            // Use customer's actual email with a unique suffix so Paystack treats each
            // transaction as distinct: customer+REF@domain.com
            [$localPart, $domain] = explode('@', $request->email, 2);
            $email = $localPart . '+' . strtolower($reference) . '@' . $domain;
        } else {
            // No customer email — build a synthetic one scoped to this business + reference
            $businessSlug = preg_replace('/[^a-z0-9]/', '', strtolower($business->name ?? 'pos'));
            $email = 'pos.' . $businessSlug . '.' . strtolower($reference) . '@shopybook.app';
        }

        // Amount must be in kobo (KES × 100)
        $amount = (int) round((float) $request->amount * 100);

        $payload = [
            'email'        => $email,
            'amount'       => $amount,
            'currency'     => 'KES',
            'reference'    => $reference,
            'mobile_money' => [
                'phone'    => $phone,
                'provider' => 'mpesa',
            ],
        ];

        Log::info('Paystack full payload', array_merge($payload, ['email_masked' => substr($email, 0, 6) . '***']));

        $response = Http::withToken($config['secret_key'])
            ->post("{$config['base_url']}/charge", $payload);

        $data = $response->json();

        if ($response->successful() && ($data['status'] ?? false)) {
            return response()->json([
                'success'   => true,
                'reference' => $data['data']['reference'] ?? $reference,
                'status'    => $data['data']['status'],
                'message'   => $data['data']['display_text'] ?? 'STK push sent. Ask customer to enter M-Pesa PIN.',
            ]);
        }

        Log::warning('Paystack M-Pesa charge failed', [
            'business_id' => $business->id,
            'phone_sent'  => $phone,
            'amount_sent' => $amount,
            'response'    => $data,
        ]);

        return response()->json([
            'error' => $data['message'] ?? 'Failed to send STK push. Check your Paystack credentials.',
        ], 400);
    }

    /**
     * Poll Paystack for the status of an in-flight charge reference.
     */
    public function paystackChargeStatus(Request $request, string $reference)
    {
        $business = auth()->user()->business;
        $settings = $business->getSettingsOrCreate();
        $config   = $settings->paystackConfig();

        if (empty($config['secret_key'])) {
            return response()->json(['error' => 'Paystack not configured.'], 422);
        }

        // Validate reference to prevent SSRF-style path injection
        if (!preg_match('/^[a-zA-Z0-9_\-]{4,60}$/', $reference)) {
            return response()->json(['error' => 'Invalid reference.'], 400);
        }

        $response = Http::withToken($config['secret_key'])
            ->get("{$config['base_url']}/charge/{$reference}");

        $data = $response->json();

        return response()->json([
            'success'   => $response->successful() && ($data['status'] ?? false),
            'status'    => $data['data']['status'] ?? 'unknown',
            'reference' => $reference,
            'amount'    => isset($data['data']['amount']) ? $data['data']['amount'] / 100 : null,
        ]);
    }
}
