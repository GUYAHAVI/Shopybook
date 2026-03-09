<?php

namespace App\Jobs;

use App\Services\AIMemoryService;
use App\Services\ClaudeAPIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Runs in the background after every chat turn.
 * Calls Claude with a tiny extraction prompt to pull out key business facts
 * from the conversation (owner goals, preferences, challenges) and upserts
 * them into ai_business_memory so future AI responses are more personalised.
 */
class ExtractBusinessMemoryJob implements ShouldQueue

{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries   = 2;
    public int $timeout = 30;

    public function __construct(
        private readonly string $businessId,
        private readonly string $userMessage,
        private readonly string $aiResponse
    ) {}

    public function handle(ClaudeAPIService $claude, AIMemoryService $memory): void
    {
        $userMsg   = $this->userMessage;
        $aiResp    = $this->aiResponse;

        $extractionPrompt =
            "You are an analyst reading a business chat excerpt. Extract 2-5 concrete, reusable facts about this business owner's preferences, goals, strategies, or challenges.\n\n" .
            "RULES:\n" .
            "- Focus only on durable, factual insights (not one-off questions)\n" .
            "- Skip generic phrases; be specific\n" .
            "- Output ONLY valid JSON — an array of objects\n\n" .
            "EXAMPLE OUTPUT:\n" .
            "[\n" .
            "  {\"key\": \"preferred_marketing_channel\", \"value\": \"WhatsApp and Instagram\", \"confidence\": 85},\n" .
            "  {\"key\": \"primary_goal\", \"value\": \"Increase walk-in customers\", \"confidence\": 80}\n" .
            "]\n\n" .
            "Chat excerpt:\n" .
            "USER: {$userMsg}\n" .
            "AI: {$aiResp}\n\n" .
            "Respond ONLY with a JSON array (no markdown, no explanation):";

        try {
            $raw = $claude->extractJSON($extractionPrompt);

            if (empty($raw)) {
                return;
            }

            $facts = json_decode($raw, true);

            if (! is_array($facts)) {
                return;
            }

            foreach ($facts as $fact) {
                if (empty($fact['key']) || empty($fact['value'])) {
                    continue;
                }

                $confidence = (int) ($fact['confidence'] ?? 70);

                // Sanitise key: lowercase, underscores, no spaces
                $key = preg_replace('/[^a-z0-9_]/', '_', strtolower(trim($fact['key'])));
                $key = preg_replace('/_+/', '_', trim($key, '_'));

                if (strlen($key) < 3 || strlen($key) > 100) {
                    continue;
                }

                $memory->upsertMemory(
                    $this->businessId,
                    $key,
                    trim($fact['value']),
                    max(0, min(100, $confidence))
                );
            }
        } catch (\Exception $e) {
            Log::warning('ExtractBusinessMemoryJob failed', [
                'business_id' => $this->businessId,
                'error'       => $e->getMessage(),
            ]);
            // Don't re-throw — a failed extraction is non-critical
        }
    }
}