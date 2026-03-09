<?php

namespace App\Console\Commands;

use App\Models\AIMarketInsight;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Scheduled daily to fetch and cache Kenyan business/market news from public
 * RSS feeds.  The cached insights are then injected into AI prompts so the
 * chatbot can reference real, current Kenyan business trends without hitting
 * external APIs at chat-request time.
 *
 * Sources (all publicly accessible RSS / Atom feeds):
 *   - Business Daily Africa
 *   - Standard Digital — Business
 *   - Nation.co.ke — Business
 *   - Kenya Revenue Authority (tax & trade notices)
 */
class FetchMarketInsights extends Command
{
    protected $signature   = 'ai:fetch-market-insights {--dry-run : Parse but do not persist}';
    protected $description = 'Fetch Kenyan business news and cache as AI market intelligence';

    // ── RSS sources ──────────────────────────────────────────────────────────
    private array $feeds = [
        [
            'url'    => 'https://www.businessdailyafrica.com/rss/business',
            'source' => 'Business Daily Africa',
        ],
        [
            'url'    => 'https://www.standardmedia.co.ke/rss/business.xml',
            'source' => 'Standard Digital',
        ],
        [
            'url'    => 'https://nation.africa/kenya/business/-/1954186.rss',
            'source' => 'Daily Nation',
        ],
    ];

    // ── Keyword → business_type mapping ─────────────────────────────────────
    // If none match, the insight is filed under 'general'.
    private array $categoryMap = [
        'salon'     => ['salon', 'beauty', 'hairdress', 'barber', 'spa', 'nail'],
        'retail'    => ['retail', 'shop', 'boutique', 'supermarket', 'store', 'fmcg'],
        'restaurant'=> ['restaurant', 'cafe', 'coffee', 'food', 'catering', 'hotel', 'eatery'],
        'pharmacy'  => ['pharmacy', 'chemist', 'medicine', 'health', 'medical'],
        'hardware'  => ['hardware', 'construction', 'building', 'iron sheet', 'cement'],
        'logistics' => ['logistics', 'transport', 'delivery', 'courier', 'shipping'],
        'technology'=> ['tech', 'software', 'digital', 'startup', 'app', 'fintech'],
        'finance'   => ['bank', 'sacco', 'microfinance', 'loan', 'credit', 'mpesa', 'mobile money'],
        'agriculture'=> ['farm', 'agri', 'crop', 'livestock', 'export', 'horticulture'],
        'education' => ['school', 'education', 'training', 'tutor', 'college'],
        'fashion'   => ['fashion', 'clothing', 'apparel', 'textile', 'design'],
        'real_estate'=> ['real estate', 'property', 'land', 'rental', 'housing'],
    ];

    public function handle(): int
    {
        $this->info('Fetching Kenyan market intelligence...');

        $saved   = 0;
        $skipped = 0;
        $errors  = 0;

        foreach ($this->feeds as $feed) {
            try {
                $items = $this->parseFeed($feed['url'], $feed['source']);
                $this->line("  {$feed['source']}: " . count($items) . " items parsed");

                foreach ($items as $item) {
                    if ($this->option('dry-run')) {
                        $this->line("    [DRY] {$item['category']} — {$item['title']}");
                        continue;
                    }

                    $exists = AIMarketInsight::where('source_url', $item['source_url'])->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    AIMarketInsight::create($item);
                    $saved++;
                }
            } catch (\Exception $e) {
                $this->warn("  Failed: {$feed['source']} — {$e->getMessage()}");
                Log::warning('FetchMarketInsights: feed fetch failed', [
                    'source' => $feed['source'],
                    'error'  => $e->getMessage(),
                ]);
                $errors++;
            }
        }

        if (! $this->option('dry-run')) {
            // Prune stale records (> 60 days old)
            $pruned = AIMarketInsight::where('published_at', '<', now()->subDays(60))->delete();
            if ($pruned) {
                $this->line("  Pruned {$pruned} stale insights (> 60 days old)");
            }
        }

        $this->info("Done. Saved: {$saved}, Skipped: {$skipped}, Errors: {$errors}");

        return self::SUCCESS;
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function parseFeed(string $url, string $sourceName): array
    {
        $response = Http::timeout(15)
            ->withHeaders(['User-Agent' => 'ShopybookAI/1.0'])
            ->get($url);

        if (! $response->successful()) {
            throw new \RuntimeException("HTTP {$response->status()} for {$url}");
        }

        $xml  = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
        $items = [];

        if ($xml === false) {
            throw new \RuntimeException("Invalid XML from {$url}");
        }

        // Support both RSS 2.0 (channel/item) and Atom (entry)
        $entries = $xml->channel->item ?? $xml->entry ?? [];

        foreach ($entries as $entry) {
            $title       = trim((string) ($entry->title ?? ''));
            $description = trim(strip_tags((string) ($entry->description ?? $entry->summary ?? '')));
            $link        = trim((string) ($entry->link ?? $entry->id ?? ''));
            $pubDate     = (string) ($entry->pubDate ?? $entry->published ?? $entry->updated ?? '');

            if (empty($title) || empty($description)) {
                continue;
            }

            $publishedAt = null;
            try {
                if ($pubDate) {
                    $publishedAt = \Carbon\Carbon::parse($pubDate);
                }
            } catch (\Exception $e) {
                // leave null
            }

            // Skip articles older than 60 days
            if ($publishedAt && $publishedAt->lt(now()->subDays(60))) {
                continue;
            }

            $combined    = strtolower($title . ' ' . $description);
            $category    = $this->classifyCategory($combined);
            $keywords    = $this->extractKeywords($combined);
            $relevance   = $this->scoreRelevance($combined, $keywords);

            // Normalise summary to ≤ 300 chars for prompt efficiency
            $summary = mb_strlen($description) > 300
                ? mb_substr($description, 0, 297) . '...'
                : $description;

            $items[] = [
                'category'        => $category,
                'title'           => mb_substr($title, 0, 300),
                'summary'         => $summary,
                'source_url'      => mb_substr($link, 0, 500),
                'source_name'     => $sourceName,
                'keywords'        => $keywords,
                'relevance_score' => $relevance,
                'published_at'    => $publishedAt,
            ];
        }

        return $items;
    }

    private function classifyCategory(string $text): string
    {
        foreach ($this->categoryMap as $category => $keywords) {
            foreach ($keywords as $kw) {
                if (str_contains($text, $kw)) {
                    return $category;
                }
            }
        }
        return 'general';
    }

    private function extractKeywords(string $text): array
    {
        $allKeywords = array_merge(...array_values($this->categoryMap));
        $found = [];
        foreach ($allKeywords as $kw) {
            if (str_contains($text, $kw) && ! in_array($kw, $found, true)) {
                $found[] = $kw;
            }
        }
        return array_slice($found, 0, 10);
    }

    private function scoreRelevance(string $text, array $keywords): int
    {
        // Base score; add +5 per matched keyword, bonus for Kenya and SME signals
        $score = 40;
        $score += min(count($keywords) * 5, 30);

        $highValueTerms = ['kenya', 'nairobi', 'business', 'entrepreneur', 'sme', 'growth', 'ksh', 'revenue'];
        foreach ($highValueTerms as $term) {
            if (str_contains($text, $term)) {
                $score += 3;
            }
        }

        return min(100, $score);
    }
}
