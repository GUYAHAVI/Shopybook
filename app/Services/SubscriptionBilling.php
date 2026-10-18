<?php

namespace App\Services;

use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Completes subscription payments (any gateway) and credits what was bought.
 */
class SubscriptionBilling
{
    /**
     * Mark a pending subscription payment completed and credit it.
     * Idempotent: only the call that flips pending → completed credits anything,
     * so duplicate callbacks / webhook + polling races can't double-credit.
     */
    public static function complete(int $paymentId, ?string $receipt, string $via, string $desc = 'Payment successful'): bool
    {
        $flipped = DB::table('subscription_payments')
            ->where('id', $paymentId)
            ->where('status', 'pending')
            ->update([
                'status'               => 'completed',
                'mpesa_receipt_number' => $receipt,
                'transaction_date'     => now(),
                'result_desc'          => $desc,
                'updated_at'           => now(),
            ]);

        if (!$flipped) {
            return false;
        }

        self::credit(DB::table('subscription_payments')->find($paymentId), $receipt ?? '', $via);

        return true;
    }

    public static function fail(int $paymentId, ?string $desc): void
    {
        DB::table('subscription_payments')
            ->where('id', $paymentId)
            ->where('status', 'pending')
            ->update(['status' => 'failed', 'result_desc' => $desc ?: 'Payment failed', 'updated_at' => now()]);
    }

    /**
     * Credit a completed payment: plan upgrades set business.plan;
     * 'business_addon' grants the user an extra owned-business slot.
     */
    public static function credit(object $payment, string $reference, string $via): void
    {
        if ($payment->plan === 'business_addon') {
            if ($user = User::find($payment->user_id)) {
                $user->increment('extra_business_slots');
                Log::info("Extra business slot credited via {$via}", [
                    'user_id'   => $user->id,
                    'slots'     => $user->fresh()->extra_business_slots,
                    'reference' => $reference,
                ]);
            }
            return;
        }

        if ($business = Business::find($payment->business_id)) {
            $business->update(['plan' => $payment->plan, 'upgraded_at' => now()]);
            Log::info("Business subscription upgraded via {$via}", [
                'business_id' => $business->id,
                'plan'        => $payment->plan,
                'reference'   => $reference,
            ]);
        }
    }
}
