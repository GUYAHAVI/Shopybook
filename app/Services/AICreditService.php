<?php

namespace App\Services;

use App\Models\Business;
use Illuminate\Support\Facades\Log;

/**
 * Manages AI credits — monthly allowances, purchased top-ups, and usage tracking.
 *
 * Credit costs per feature:
 *  - Logo generation (premium):     2 credits
 *  - Marketing image (premium):     1 credit
 *  - AI chat message:               0 credits (text only, cheap)
 *  - Website section image:         1 credit
 *
 * Monthly allowances:
 *  - Free:      0 credits (Pollinations only)
 *  - Basic:     10 credits/month
 *  - Premium:   30 credits/month
 *  - Enterprise: 100 credits/month
 *
 * Top-up packs (in-app purchase):
 *  - 10 credits:  $2
 *  - 25 credits:  $4
 *  - 50 credits:  $7
 *  - 100 credits: $12
 */
class AICreditService
{
    protected ReplicateImageService $replicate;

    public function __construct(ReplicateImageService $replicate)
    {
        $this->replicate = $replicate;
    }

    /**
     * Credit costs per feature.
     */
    public const CREDIT_COSTS = [
        'logo_premium'       => 2,
        'marketing_image_premium' => 1,
        'website_image_premium'   => 1,
        'logo_basic'         => 0,  // Free Pollinations
        'marketing_image_basic'   => 0,  // Free Pollinations
    ];

    /**
     * Available top-up packs.
     */
    public const TOP_UP_PACKS = [
        10  => ['credits' => 10,  'price_usd' => 2],
        25  => ['credits' => 25,  'price_usd' => 4],
        50  => ['credits' => 50,  'price_usd' => 7],
        100 => ['credits' => 100, 'price_usd' => 12],
    ];

    /**
     * Ensure monthly credits are up to date.
     */
    public function ensureMonthlyCredits(Business $business): void
    {
        $business->resetMonthlyAiCreditsIfNeeded();
    }

    /**
     * Check if a business can use a premium AI feature.
     */
    public function canUsePremium(Business $business, string $feature): bool
    {
        $this->ensureMonthlyCredits($business);
        $cost = self::CREDIT_COSTS[$feature] ?? 1;
        return $business->isPremium() && $business->totalAiCredits() >= $cost;
    }

    /**
     * Consume credits for a feature. Returns false if insufficient credits.
     */
    public function consume(Business $business, string $feature): bool
    {
        $this->ensureMonthlyCredits($business);
        $cost = self::CREDIT_COSTS[$feature] ?? 1;

        if ($business->totalAiCredits() < $cost) {
            return false;
        }

        for ($i = 0; $i < $cost; $i++) {
            $business->consumeAiCredit($feature);
        }

        Log::info('AI credits consumed', [
            'business_id' => $business->id,
            'feature' => $feature,
            'cost' => $cost,
            'remaining' => $business->fresh()->totalAiCredits(),
        ]);

        return true;
    }

    /**
     * Add purchased credits to a business.
     */
    public function addPurchasedCredits(Business $business, int $amount): void
    {
        $business->addPurchasedCredits($amount);
        Log::info('AI credits purchased', [
            'business_id' => $business->id,
            'amount' => $amount,
            'total' => $business->fresh()->totalAiCredits(),
        ]);
    }

    /**
     * Get credit summary for a business.
     */
    public function getSummary(Business $business): array
    {
        $this->ensureMonthlyCredits($business);
        $fresh = $business->fresh();

        return [
            'monthly' => $fresh->ai_credits ?? 0,
            'purchased' => $fresh->ai_credits_purchased ?? 0,
            'total' => $fresh->totalAiCredits(),
            'monthly_allowance' => $fresh->monthlyAiCredits(),
            'plan' => $fresh->plan ?? 'free',
            'is_premium' => $fresh->isPremium(),
            'is_enterprise' => $fresh->isEnterprise(),
            'replicate_configured' => $this->replicate->isConfigured(),
        ];
    }
}
