<?php

namespace App\Services;

use App\Models\AIBusinessMemory;
use App\Models\AIConversation;
use App\Models\AIMarketInsight;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Central orchestrator for all AI memory dimensions:
 *   1. Per-session conversation history (multi-turn context)
 *   2. Per-business persistent memory (extracted facts, preferences, goals)
 *   3. Cross-business platform benchmarks (anonymised)
 *   4. Kenyan market intelligence (from cached RSS feeds)
 */
class AIMemoryService
{
    // Keep at most this many turns in a session context window
    private const MAX_HISTORY_TURNS = 10;

    // ─────────────────────────────────────────────────────────────────────────
    // 1. CONVERSATION HISTORY
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Persist a single chat turn so future requests can replay the context.
     */
    public function storeConversationTurn(
        string $businessId,
        string $sessionId,
        string $role,   // 'user' | 'assistant'
        string $content
    ): void {
        AIConversation::create([
            'business_id' => $businessId,
            'session_id'  => $sessionId,
            'role'        => $role,
            'content'     => $content,
            'tokens'      => (int) ceil(mb_strlen($content) / 4), // rough estimate
        ]);

        // Keep sessions lean; prune anything older than 50 turns per session
        $oldest = AIConversation::where('business_id', $businessId)
            ->where('session_id', $sessionId)
            ->orderBy('created_at', 'desc')
            ->skip(50)
            ->first();

        if ($oldest) {
            AIConversation::where('business_id', $businessId)
                ->where('session_id', $sessionId)
                ->where('created_at', '<=', $oldest->created_at)
                ->delete();
        }
    }

    /**
     * Returns the last N turns formatted as Claude's messages array.
     * [['role' => 'user', 'content' => '...'], ['role' => 'assistant', ...], ...]
     */
    public function getConversationHistory(
        string $businessId,
        string $sessionId,
        int $limit = self::MAX_HISTORY_TURNS
    ): array {
        return AIConversation::historyFor($businessId, $sessionId, $limit);
    }

    /**
     * Wipe the conversation turns for a specific browser session.
     */
    public function clearSession(string $businessId, string $sessionId): void
    {
        AIConversation::where('business_id', $businessId)
            ->where('session_id', $sessionId)
            ->delete();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. BUSINESS MEMORY (persistent facts)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Upsert a single memory facet — creates or updates the stored value.
     * Only writes if inbound confidence >= existing; prevents overwriting
     * high-confidence facts with weaker inferences.
     */
    public function upsertMemory(
        string $businessId,
        string $key,
        string $value,
        int    $confidence = 70,
        string $source = 'chat'
    ): void {
        try {
            AIBusinessMemory::where('business_id', $businessId)
                ->where('facet_key', $key)
                ->where('confidence', '>', $confidence)
                ->delete(); // Remove if current confidence is lower; allows upsert to replace it

            AIBusinessMemory::updateOrCreate(
                ['business_id' => $businessId, 'facet_key' => $key],
                ['facet_value' => $value, 'confidence' => $confidence, 'source' => $source]
            );
        } catch (\Exception $e) {
            Log::warning("AIMemoryService::upsertMemory failed for {$key}", ['error' => $e->getMessage()]);
        }
    }

    /**
     * All persistent memory facets for a business, highest confidence first.
     * Returns ['preference_channel' => 'Instagram', 'goal' => 'expand...', ...]
     */
    public function getBusinessMemory(string $businessId): array
    {
        return AIBusinessMemory::forBusiness($businessId);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. PLATFORM BENCHMARKS (cross-business, anonymised)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Build anonymised benchmark statistics from OTHER Shopybook businesses
     * of the same type.  All figures are averages — no individual data leaks.
     */
    public function getPlatformBenchmarks(string $businessId, string $businessType): array
    {
        try {
            // Revenue benchmarks — only include businesses with enough data
            $revenueStats = DB::table('orders')
                ->join('businesses', 'orders.business_id', '=', 'businesses.id')
                ->where('businesses.business_type', $businessType)
                ->where('businesses.id', '!=', $businessId)
                ->where('orders.payment_status', 'paid')
                ->where('orders.created_at', '>=', now()->subDays(90))
                ->selectRaw('
                    COUNT(DISTINCT orders.business_id) as business_count,
                    AVG(orders.total_amount)           as avg_order_value,
                    SUM(orders.total_amount) / COUNT(DISTINCT orders.business_id) as avg_revenue_90d
                ')
                ->first();

            // Top-selling product categories within business type
            $topCategories = DB::table('products')
                ->join('businesses', 'products.business_id', '=', 'businesses.id')
                ->where('businesses.business_type', $businessType)
                ->where('businesses.id', '!=', $businessId)
                ->whereNotNull('products.category')
                ->where('products.category', '!=', '')
                ->selectRaw('products.category, COUNT(*) as count')
                ->groupBy('products.category')
                ->orderByDesc('count')
                ->limit(5)
                ->pluck('category')
                ->all();

            $benchmarks = [];

            if ($revenueStats && $revenueStats->business_count >= 3) {
                $benchmarks['peer_businesses_count']   = (int) $revenueStats->business_count;
                $benchmarks['avg_order_value_ksh']     = round((float) $revenueStats->avg_order_value, 0);
                $benchmarks['avg_revenue_90d_ksh']     = round((float) $revenueStats->avg_revenue_90d, 0);
            }

            if ($topCategories) {
                $benchmarks['popular_product_categories'] = $topCategories;
            }

            return $benchmarks;
        } catch (\Exception $e) {
            Log::warning('AIMemoryService::getPlatformBenchmarks failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. MARKET INTELLIGENCE (cached from Kenyan news/RSS)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Pull the freshest cached Kenyan market insights for this business type.
     */
    public function getRelevantMarketInsights(string $businessType, int $limit = 4): array
    {
        // Try exact category match first; fall back to 'general'
        $insights = AIMarketInsight::forCategory($businessType, $limit);

        if (empty($insights)) {
            $insights = AIMarketInsight::forCategory('general', $limit);
        }

        return $insights;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5. CONTEXT BUILDER (assembles all 4 sources into prompt sections)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns an enriched context block (array of strings) to be appended to
     * the system prompt. Each section is non-empty only when data is available.
     */
    public function buildContextBlock(
        string $businessId,
        string $businessType,
        string $sessionId
    ): array {
        $sections = [];

        // ── Business owner memory ──────────────────────────────────────────
        $memory = $this->getBusinessMemory($businessId);
        if ($memory) {
            $lines = [];
            foreach ($memory as $key => $value) {
                $label = ucwords(str_replace('_', ' ', $key));
                $lines[] = "- {$label}: {$value}";
            }
            $sections['memory'] = "### What I Know About This Business Owner\n" . implode("\n", $lines);
        }

        // ── Platform benchmarks ────────────────────────────────────────────
        $benchmarks = $this->getPlatformBenchmarks($businessId, $businessType);
        if ($benchmarks) {
            $lines = [];
            if (isset($benchmarks['peer_businesses_count'])) {
                $lines[] = "- Peer businesses analysed: {$benchmarks['peer_businesses_count']} similar Shopybook businesses";
            }
            if (isset($benchmarks['avg_order_value_ksh'])) {
                $lines[] = "- Average order value (peers): KSh " . number_format($benchmarks['avg_order_value_ksh']);
            }
            if (isset($benchmarks['avg_revenue_90d_ksh'])) {
                $lines[] = "- Average 90-day revenue (peers): KSh " . number_format($benchmarks['avg_revenue_90d_ksh']);
            }
            if (!empty($benchmarks['popular_product_categories'])) {
                $cats = implode(', ', $benchmarks['popular_product_categories']);
                $lines[] = "- Popular product categories in this sector: {$cats}";
            }
            if ($lines) {
                $sections['benchmarks'] = "### Shopybook Platform Benchmarks (Anonymised)\n" . implode("\n", $lines);
            }
        }

        // ── Kenyan market intelligence ─────────────────────────────────────
        $insights = $this->getRelevantMarketInsights($businessType);
        if ($insights) {
            $lines = [];
            foreach ($insights as $item) {
                $date   = $item['published'] ?? '';
                $source = $item['source']    ?? '';
                $lines[] = "- **{$item['title']}** ({$source}, {$date}): {$item['summary']}";
            }
            $sections['market'] = "### Recent Kenyan Market Intelligence\n" . implode("\n", $lines);
        }

        return $sections;
    }
}
