<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Services\AICreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AICreditController extends Controller
{
    protected AICreditService $creditService;

    public function __construct(AICreditService $creditService)
    {
        $this->creditService = $creditService;
        $this->middleware('auth');
    }

    /**
     * Get the current business's AI credit summary (AJAX).
     */
    public function summary(Request $request)
    {
        $business = $request->user()->business;
        if (!$business) {
            return response()->json(['error' => 'No business found'], 404);
        }

        return response()->json($this->creditService->getSummary($business));
    }

    /**
     * Show the AI credits / top-up page.
     */
    public function index(Request $request)
    {
        $business = $request->user()->business;
        if (!$business) {
            return redirect()->route('dashboard')->with('error', 'You need a business first.');
        }

        $summary = $this->creditService->getSummary($business);
        $packs = AICreditService::TOP_UP_PACKS;

        return view('ai-credits.index', compact('summary', 'packs'));
    }

    /**
     * Purchase credit top-up (initiates payment).
     */
    public function purchase(Request $request)
    {
        $validated = $request->validate([
            'pack_id' => 'required|in:10,25,50,100',
        ]);

        $business = $request->user()->business;
        if (!$business) {
            return back()->with('error', 'No business found.');
        }

        $packId = (int) $validated['pack_id'];
        $pack = AICreditService::TOP_UP_PACKS[$packId] ?? null;
        if (!$pack) {
            return back()->with('error', 'Invalid credit pack.');
        }

        // For now, we'll use the existing Paystack integration
        // The payment callback will call addPurchasedCredits
        $reference = 'ai_credits_' . $business->id . '_' . time() . '_' . $packId;

        // Store the intended purchase in session for the callback
        session([
            'ai_credit_purchase' => [
                'reference' => $reference,
                'pack_id' => $packId,
                'credits' => $pack['credits'],
                'amount' => $pack['price_usd'] * 100, // cents
                'business_id' => $business->id,
            ],
        ]);

        // Redirect to Paystack payment
        $paystackKey = config('services.paystack.public_key');
        $email = $business->email ?? $request->user()->email;
        $amountInKobo = $pack['price_usd'] * 100; // Paystack uses NGN kobo by default; adjust if using USD

        // If using Paystack in USD, amount is in cents
        $callbackUrl = route('ai-credits.payment.callback');

        return view('ai-credits.payment', [
            'pack' => $pack,
            'packId' => $packId,
            'reference' => $reference,
            'paystackKey' => $paystackKey,
            'email' => $email,
            'amount' => $amountInKobo,
            'callbackUrl' => $callbackUrl,
        ]);
    }

    /**
     * Payment callback — verify payment and add credits.
     */
    public function paymentCallback(Request $request)
    {
        $reference = $request->query('reference') ?? $request->query('trxref');
        if (!$reference) {
            return redirect()->route('ai-credits.index')->with('error', 'Payment reference not found.');
        }

        $purchase = session('ai_credit_purchase');
        if (!$purchase || $purchase['reference'] !== $reference) {
            return redirect()->route('ai-credits.index')->with('error', 'Payment session expired. Please try again.');
        }

        // Verify with Paystack
        $secretKey = config('services.paystack.secret_key');
        if ($secretKey) {
            try {
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => 'Bearer ' . $secretKey,
                ])->timeout(30)->get("https://api.paystack.co/transaction/verify/{$reference}");

                if ($response->successful() && ($response->json()['data']['status'] ?? '') === 'success') {
                    $business = Business::find($purchase['business_id']);
                    if ($business) {
                        $this->creditService->addPurchasedCredits($business, $purchase['credits']);
                        session()->forget('ai_credit_purchase');
                        Log::info('AI credits purchased successfully', [
                            'business_id' => $business->id,
                            'credits' => $purchase['credits'],
                            'reference' => $reference,
                        ]);
                        return redirect()->route('ai-credits.index')->with('success', "Added {$purchase['credits']} AI credits to your account.");
                    }
                }
            } catch (\Throwable $e) {
                Log::error('AI credit payment verification failed: ' . $e->getMessage());
            }
        }

        // If Paystack isn't configured (dev mode), add credits directly
        if (!config('services.paystack.secret_key')) {
            $business = Business::find($purchase['business_id']);
            if ($business) {
                $this->creditService->addPurchasedCredits($business, $purchase['credits']);
                session()->forget('ai_credit_purchase');
                return redirect()->route('ai-credits.index')->with('success', "Added {$purchase['credits']} AI credits (dev mode — no payment required).");
            }
        }

        return redirect()->route('ai-credits.index')->with('error', 'Payment verification failed. Please contact support if you were charged.');
    }
}
