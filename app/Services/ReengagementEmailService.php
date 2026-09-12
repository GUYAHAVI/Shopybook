<?php

namespace App\Services;

use App\Models\User;
use App\Models\PageVisit;
use App\Models\Business;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReengagementEmailService
{
    protected ClaudeAPIService $claude;
    protected string $apiKey;
    protected string $baseUrl = 'https://api.anthropic.com/v1/messages';
    protected string $model = 'claude-haiku-4-5-20251001';

    public function __construct(ClaudeAPIService $claude)
    {
        $this->claude = $claude;
        $this->apiKey = config('services.claude.api_key', env('CLAUDE_API_KEY'));
        $this->model = config('services.claude.model', $this->model);
    }

    /**
     * Gather context about a user's engagement for the AI prompt.
     */
    public function gatherUserContext(User $user): array
    {
        $business = $user->business;
        $lastVisit = PageVisit::where('user_id', $user->id)->latest()->first();
        $visitCount = PageVisit::where('user_id', $user->id)->count();
        $topPages = PageVisit::where('user_id', $user->id)
            ->selectRaw("COALESCE(route_name, path) as page, COUNT(*) as visits")
            ->groupBy('page')
            ->orderByDesc('visits')
            ->limit(5)
            ->pluck('visits', 'page')
            ->toArray();

        $lastPage = $lastVisit ? ($lastVisit->route_name ?? $lastVisit->path) : null;
        $lastSeen = $lastVisit?->created_at?->diffForHumans() ?? 'never';
        $daysSinceLastVisit = $lastVisit
            ? now()->diffInDays($lastVisit->created_at)
            : null;

        // What features has the user used?
        $featuresUsed = [];
        if ($business) {
            if ($business->products()->count() > 0) $featuresUsed[] = 'added products';
            if ($business->orders()->count() > 0) $featuresUsed[] = 'made sales';
            if ($business->customers()->count() > 0) $featuresUsed[] = 'added customers';
            if ($business->services()->count() > 0) $featuresUsed[] = 'added services';
            if ($business->website) $featuresUsed[] = 'built a website';
            if ($business->costs()->count() > 0) $featuresUsed[] = 'tracked expenses';
        }

        return [
            'user_name' => $user->name,
            'user_email' => $user->email,
            'business_name' => $business?->name ?? 'their business',
            'business_type' => $business?->type ?? 'small business',
            'last_seen' => $lastSeen,
            'days_since_last_visit' => $daysSinceLastVisit,
            'last_page' => $lastPage,
            'visit_count' => $visitCount,
            'top_pages' => $topPages,
            'features_used' => $featuresUsed,
            'has_business' => (bool) $business,
            'registered_at' => $user->created_at?->format('M j, Y'),
        ];
    }

    /**
     * Draft a personalized re-engagement email from the founder.
     */
    public function draftEmail(User $user): array
    {
        $context = $this->gatherUserContext($user);

        if (empty($this->apiKey)) {
            return $this->fallbackDraft($context);
        }

        $prompt = $this->buildPrompt($context);

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => 800,
                'temperature' => 0.8,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if ($response->successful()) {
                $text = $response->json()['content'][0]['text'] ?? '';
                return $this->parseResponse($text, $context);
            }

            Log::error('Reengagement AI error', ['status' => $response->status(), 'body' => $response->body()]);
            return $this->fallbackDraft($context);
        } catch (\Throwable $e) {
            Log::error('Reengagement AI exception: ' . $e->getMessage());
            return $this->fallbackDraft($context);
        }
    }

    protected function buildPrompt(array $ctx): string
    {
        $featuresList = $ctx['features_used']
            ? implode(', ', $ctx['features_used'])
            : 'signed up but has not set up their business yet';

        $lastPageHint = $ctx['last_page']
            ? "The last page they visited was '{$ctx['last_page']}'."
            : "They have not visited any pages yet.";

        $topPagesHint = '';
        if (!empty($ctx['top_pages'])) {
            $topPagesHint = "Pages they visited most: " . implode(', ', array_keys($ctx['top_pages'])) . ".";
        }

        $daysHint = $ctx['days_since_last_visit'] ?? 'unknown';

        return <<<PROMPT
You are Elvis Havi, the founder of Shopybook, a business management platform for small businesses in Kenya. You are writing a personal, one-to-one email to a user who signed up but has gone quiet. The goal is to bring them back — not to sell, but to genuinely check in as a founder who cares.

Write a warm, personal email to {$ctx['user_name']}.

Context about this user:
- Business name: {$ctx['business_name']}
- Business type: {$ctx['business_type']}
- Registered: {$ctx['registered_at']}
- Last active: {$ctx['last_seen']} ({$daysHint} days ago)
- {$lastPageHint}
- {$topPagesHint}
- What they have done so far: {$featuresList}

New features they may not know about:
- Guided page tours (an advisor walks them through each page the first time)
- Improved dashboard with plain-language labels
- Quick-add product flow
- AI assistant for business questions

Rules for the email:
1. Write as Elvis Havi, first person. Sign off as "Elvis Havi, Founder, Shopybook".
2. Tone: warm, genuine, not salesy. Like a founder emailing a user directly — short paragraphs, conversational.
3. Reference something specific about their situation (their business name, how long they have been away, where they got stuck, or what they have done so far).
4. Mention one or two new features that would help them specifically.
5. Ask what made them stop using it — genuinely invite feedback.
6. Keep it under 200 words. No bullet points. No headers. Just plain email text.
7. Do NOT include a subject line — only the email body.
8. Do NOT include the greeting "Hi {$ctx['user_name']}" — start directly with the first sentence.

Return ONLY the email body text. Nothing else.
PROMPT;
    }

    protected function parseResponse(string $text, array $ctx): array
    {
        $text = trim($text);
        // Strip any stray markdown
        $text = preg_replace('/\*\*(.+?)\*\*/', '$1', $text);
        $text = preg_replace('/\*(.+?)\*/', '$1', $text);

        return [
            'subject' => 'A quick note from Elvis at Shopybook',
            'body' => "Hi {$ctx['user_name']},\n\n" . $text . "\n\nElvis Havi\nFounder, Shopybook",
            'context' => $ctx,
            'source' => 'ai',
        ];
    }

    protected function fallbackDraft(array $ctx): array
    {
        $name = $ctx['user_name'];
        $business = $ctx['business_name'];
        $days = $ctx['days_since_last_visit'] ?? 'a while';

        $stuckHint = $ctx['last_page']
            ? "I noticed you were last on the {$ctx['last_page']} page — if something there was confusing or didn't work, I'd genuinely like to know."
            : "I noticed you signed up but haven't had a chance to explore much yet.";

        $features = $ctx['features_used']
            ? "It's great that you've already " . implode(' and ', $ctx['features_used']) . "."
            : "I'd love to help you get your first product or sale recorded — it takes about a minute.";

        $body = "Hi {$name},\n\nThis is Elvis, the founder of Shopybook. I'm reaching out personally because I noticed you haven't been back in {$days}.\n\n{$stuckHint} {$features} We've just added guided page tours that walk you through each screen the first time you visit it, and a simpler dashboard that's easier to follow.\n\nIf something about Shopybook made you stop using it, I'd genuinely like to hear what it was — reply to this email and it comes straight to me. Your feedback shapes what we build next.\n\nEither way, I'd love to see {$business} growing with us.\n\nElvis Havi\nFounder, Shopybook";

        return [
            'subject' => 'A quick note from Elvis at Shopybook',
            'body' => $body,
            'context' => $ctx,
            'source' => 'fallback',
        ];
    }
}
