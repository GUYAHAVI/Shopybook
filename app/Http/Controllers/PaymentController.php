<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Business;
use App\Models\MpesaStkRequest;
use App\Services\MpesaService;

class PaymentController extends Controller
{

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
                return $this->initiateMpesaPayment($order, $request->input('phone'));
            case 'paypal':
                return $this->initiatePayPalPayment($order);
            default:
                return response()->json(['error' => 'Invalid payment method'], 400);
        }
    }

    public function initiateMpesaPayment(Order $order, ?string $phone = null)
    {
        $phone = $phone ?: ($order->customer->phone ?? null);

        if (empty($phone)) {
            return response()->json([
                'error' => 'A customer phone number is required for M-Pesa payment.',
            ], 422);
        }

        $result = app(MpesaService::class)->stkPush(
            $order->business,
            $phone,
            (float) $order->total_amount,
            'Order' . $order->id,
            $order->id
        );

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'M-Pesa payment initiated',
            'checkout_request_id' => $result['reference'],
            'instructions' => 'Please check your phone for M-Pesa prompt and enter your PIN to complete payment.',
        ]);
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
    /**
     * POS: send an M-Pesa STK push to a customer. Funds settle to the
     * business's own till/paybill via the platform's Daraja org credentials.
     */
    public function mpesaCharge(Request $request)
    {
        $request->validate([
            'phone'        => 'required|string|min:9|max:15',
            'amount'       => 'required|numeric|min:1',
            'account_ref'  => 'nullable|string|max:50',
        ]);

        $business = auth()->user()->business;

        $result = app(MpesaService::class)->stkPush(
            $business,
            $request->phone,
            (float) $request->amount,
            $request->input('account_ref')
        );

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 400);
        }

        return response()->json([
            'success'   => true,
            'reference' => $result['reference'],
            'status'    => 'pending',
            'message'   => $result['message'] ?? 'STK push sent. Ask customer to enter M-Pesa PIN.',
        ]);
    }

    /**
     * POS: poll for the result of an STK push (updated by the Daraja callback).
     */
    public function mpesaChargeStatus(Request $request, string $checkoutRequestId)
    {
        if (!preg_match('/^[a-zA-Z0-9_\-]{4,80}$/', $checkoutRequestId)) {
            return response()->json(['error' => 'Invalid reference.'], 400);
        }

        $stk = MpesaStkRequest::where('checkout_request_id', $checkoutRequestId)
            ->where('business_id', auth()->user()->business->id)
            ->first();

        if (!$stk) {
            return response()->json(['error' => 'Transaction not found.'], 404);
        }

        return response()->json([
            'success'     => true,
            'status'      => $stk->status,
            'reference'   => $stk->checkout_request_id,
            'amount'      => $stk->amount,
            'receipt'     => $stk->mpesa_receipt_number,
            'result_desc' => $stk->result_desc,
        ]);
    }

    /**
     * Daraja STK callback — public endpoint guarded by a shared token.
     * Matches CheckoutRequestID back to the pending STK request.
     */
    public function mpesaStkCallback(Request $request)
    {
        $token = config('services.mpesa.callback_token');
        if ($token && !hash_equals($token, (string) $request->query('token'))) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        Log::info('M-Pesa STK callback received', ['data' => $request->all()]);

        $callback = $request->input('Body.stkCallback');
        if (!$callback) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $checkoutRequestId = $callback['CheckoutRequestID'] ?? null;
        $resultCode        = $callback['ResultCode'] ?? null;
        $resultDesc        = $callback['ResultDesc'] ?? null;

        $stk = MpesaStkRequest::where('checkout_request_id', $checkoutRequestId)->first();

        if (!$stk) {
            Log::warning('STK callback for unknown request', ['checkout_request_id' => $checkoutRequestId]);
            return response()->json(['message' => 'OK']);
        }

        if ((int) $resultCode === 0) {
            $items = collect($callback['CallbackMetadata']['Item'] ?? [])->keyBy('Name');

            $stk->update([
                'status'                => 'success',
                'result_code'           => $resultCode,
                'result_desc'           => $resultDesc,
                'mpesa_receipt_number'  => $items['MpesaReceiptNumber']['Value'] ?? null,
                'amount'                => $items['Amount']['Value'] ?? $stk->amount,
            ]);

            Log::info('M-Pesa payment confirmed', [
                'checkout_request_id' => $checkoutRequestId,
                'receipt'             => $stk->mpesa_receipt_number,
                'amount'              => $stk->amount,
                'business_id'         => $stk->business_id,
            ]);
        } else {
            $stk->update([
                'status'      => 'failed',
                'result_code' => $resultCode,
                'result_desc' => $resultDesc,
            ]);

            Log::info('M-Pesa payment failed', [
                'checkout_request_id' => $checkoutRequestId,
                'result_code'         => $resultCode,
                'result_desc'         => $resultDesc,
            ]);
        }

        return response()->json(['message' => 'OK']);
    }
}
