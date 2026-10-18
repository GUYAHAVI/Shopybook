<?php

namespace App\Services;

use App\Models\Business;
use App\Models\MpesaStkRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * M-Pesa Daraja STK push using the aggregator model:
 * the platform's org shortcode + passkey (env) sign every request,
 * while PartyB is each business's own till/paybill where funds settle.
 */
class MpesaService
{
    /**
     * OAuth access token for the Daraja app (org credentials).
     */
    public function accessToken(): ?string
    {
        $key    = config('services.mpesa.consumer_key');
        $secret = config('services.mpesa.consumer_secret');

        if (empty($key) || empty($secret)) {
            Log::error('M-Pesa consumer credentials missing');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($key . ':' . $secret),
            ])->get(config('services.mpesa.auth_url'), [
                'grant_type' => 'client_credentials',
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('Daraja token request failed', [
                'status'   => $response->status(),
                'response' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('Daraja token request error', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Send an STK push to a customer phone on behalf of a business.
     * Funds settle to the business's own till/paybill (PartyB).
     *
     * @return array{success: bool, reference?: string, message?: string, error?: string}
     */
    public function stkPush(Business $business, string $phone, float $amount, ?string $accountRef = null, ?int $orderId = null, ?int $rentalPaymentId = null): array
    {
        $settings = $business->getSettingsOrCreate()->mpesaConfig();

        if (!$settings['ready']) {
            return ['error' => 'M-Pesa is not configured for this business. Set your till/paybill number in Settings.'];
        }

        $phone = self::normalizePhone($phone);
        if (!$phone) {
            return ['error' => 'Invalid phone number. Use 0712345678 or 254712345678.'];
        }

        $orgShortcode = config('services.mpesa.business_shortcode');
        $passkey      = config('services.mpesa.passkey');

        if (empty($orgShortcode) || empty($passkey)) {
            Log::error('M-Pesa org shortcode/passkey missing');
            return ['error' => 'M-Pesa is not configured on this platform.'];
        }

        $token = $this->accessToken();
        if (!$token) {
            return ['error' => 'Failed to authenticate with M-Pesa.'];
        }

        $type   = $settings['type'];
        $isTill = $type === 'till';
        $isBank = $type === 'bank';

        // Resolve PartyB (who receives) + AccountReference per mode:
        //   bank    → PartyB = bank's public deposit paybill, ref = their bank account number
        //   paybill → PartyB = their own paybill,              ref = account ref or business name
        //   till    → PartyB = their own till,                 no account ref
        if ($isBank) {
            $partyB = config("mpesa_banks.{$settings['bank']}.paybill");
            if (!$partyB) {
                return ['error' => 'The selected bank is not supported for M-Pesa deposits.'];
            }
            $reference = $accountRef ?: $settings['account_ref'];
            $accountRef = preg_replace('/\D/', '', (string) $reference); // bank acct no — digits only, full length
            if (!$accountRef) {
                return ['error' => 'A bank account number is required for M-Pesa deposits.'];
            }
        } elseif ($type === 'paybill') {
            $partyB     = $settings['shortcode'];
            $reference  = $accountRef ?: ($settings['account_ref'] ?: $business->name);
            $accountRef = substr(preg_replace('/[^A-Za-z0-9]/', '', (string) $reference), 0, 12) ?: 'Payment';
        } else {
            $partyB     = $settings['shortcode'];
            $accountRef = null;
        }

        $timestamp  = now()->format('YmdHis');
        $password   = base64_encode($orgShortcode . $passkey . $timestamp);

        $payload = [
            'BusinessShortCode' => $orgShortcode,
            'Password'          => $password,
            'Timestamp'         => $timestamp,
            'TransactionType'   => $isTill ? 'CustomerBuyGoodsOnline' : 'CustomerPayBillOnline',
            'Amount'            => (int) round($amount),
            'PartyA'            => $phone,
            'PartyB'            => $partyB,
            'PhoneNumber'       => $phone,
            'CallBackURL'       => $this->callbackUrl(),
            'TransactionDesc'   => 'Payment to ' . $business->name,
        ];

        if ($accountRef !== null) {
            $payload['AccountReference'] = $accountRef;
        }

        try {
            $response = Http::withToken($token)
                ->post(config('services.mpesa.stk_push_url'), $payload);

            $data = $response->json();

            if ($response->successful() && ($data['ResponseCode'] ?? null) === '0') {
                $stk = MpesaStkRequest::create([
                    'business_id'         => $business->id,
                    'order_id'            => $orderId,
                    'rental_payment_id'   => $rentalPaymentId,
                    'phone'               => $phone,
                    'amount'              => $amount,
                    'transaction_type'    => $payload['TransactionType'],
                    'party_b'             => $partyB,
                    'account_reference'   => $payload['AccountReference'] ?? null,
                    'merchant_request_id' => $data['MerchantRequestID'] ?? null,
                    'checkout_request_id' => $data['CheckoutRequestID'],
                    'status'              => 'pending',
                ]);

                return [
                    'success'   => true,
                    'reference' => $stk->checkout_request_id,
                    'message'   => $data['CustomerMessage'] ?? 'STK push sent. Ask the customer to enter their M-Pesa PIN.',
                ];
            }

            Log::warning('M-Pesa STK push rejected', [
                'business_id' => $business->id,
                'party_b'     => $partyB,
                'response'    => $data,
            ]);

            return ['error' => $data['CustomerMessage'] ?? $data['errorMessage'] ?? 'Failed to send STK push.'];
        } catch (\Exception $e) {
            Log::error('M-Pesa STK push error', [
                'business_id' => $business->id,
                'error'       => $e->getMessage(),
            ]);
            return ['error' => 'M-Pesa request failed. Please try again.'];
        }
    }

    /**
     * Public callback URL guarded by a shared token (same pattern as the
     * friend's kejapay implementation). Host is taken from MPESA_CALLBACK_URL
     * when set, otherwise APP_URL — path is always /mpesa/stk-callback.
     */
    protected function callbackUrl(): string
    {
        $base  = config('services.mpesa.callback_url') ?: config('app.url');
        $parts = parse_url($base) ?: [];

        $url = ($parts['scheme'] ?? 'https') . '://' . ($parts['host'] ?? 'localhost')
            . (isset($parts['port']) ? ':' . $parts['port'] : '')
            . '/mpesa/stk-callback';

        $token = config('services.mpesa.callback_token');

        return $token ? $url . '?token=' . $token : $url;
    }

    /**
     * Normalize Kenyan phone numbers to 2547XXXXXXXX / 2541XXXXXXXX.
     */
    public static function normalizePhone(?string $raw): ?string
    {
        $phone = preg_replace('/\D/', '', (string) $raw);

        if (strlen($phone) === 10 && str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        } elseif (strlen($phone) === 9 && (str_starts_with($phone, '7') || str_starts_with($phone, '1'))) {
            $phone = '254' . $phone;
        } elseif (strlen($phone) === 13 && str_starts_with($phone, '0254')) {
            $phone = substr($phone, 1);
        }

        return preg_match('/^254[71]\d{8}$/', $phone) ? $phone : null;
    }
}
