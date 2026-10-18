<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Image generation via Replicate (Black Forest Labs Flux models).
 *
 * Tiers (models configurable in config/services.php → replicate):
 *  - standard: fast + cheap (default Flux Schnell) — all plans, replaces Pollinations
 *  - premium:  best quality (default Flux 1.1 Pro) — paid plans, consumes AI credits
 *  - logo:     logo model (default Flux 1.1 Pro)
 *
 * Requires REPLICATE_API_TOKEN in .env
 */
class ReplicateImageService
{
    /** Aspect ratios accepted by every Flux model we use. */
    protected const RATIOS = ['1:1', '16:9', '3:2', '2:3', '4:5', '5:4', '9:16', '3:4', '4:3'];

    protected string $apiToken;
    protected string $baseUrl = 'https://api.replicate.com/v1';

    public function __construct()
    {
        $this->apiToken = (string) config('services.replicate.token', '');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiToken);
    }

    /** Whether free-plan users get Replicate (standard tier) instead of Pollinations. */
    public function freeTierEnabled(): bool
    {
        return $this->isConfigured() && (bool) config('services.replicate.free_tier', true);
    }

    public function generateLogo(string $prompt, string $businessName, string $subdirectory = 'logos', string $tier = 'logo'): ?array
    {
        return $this->generate($prompt, $businessName, $subdirectory, 'logo', '1024x1024', $tier);
    }

    public function generateMarketingImage(string $prompt, string $businessName, string $size = '1024x1024', string $subdirectory = 'images', string $tier = 'standard'): ?array
    {
        return $this->generate($prompt, $businessName, $subdirectory, 'marketing', $size, $tier);
    }

    protected function modelFor(string $tier): string
    {
        return match ($tier) {
            'premium' => config('services.replicate.premium_model', 'black-forest-labs/flux-1.1-pro'),
            'logo'    => config('services.replicate.logo_model', 'black-forest-labs/flux-1.1-pro'),
            default   => config('services.replicate.standard_model', 'black-forest-labs/flux-schnell'),
        };
    }

    /** Closest supported aspect ratio for a "WxH" size string. */
    protected function aspectRatio(string $size): string
    {
        [$w, $h] = array_map('intval', explode('x', $size) + [1024, 1024]);
        $target = $h > 0 ? $w / $h : 1;

        return collect(self::RATIOS)->sortBy(function ($r) use ($target) {
            [$a, $b] = explode(':', $r);
            return abs(log(($a / $b) / $target));
        })->first();
    }

    protected function generate(string $prompt, string $businessName, string $subdirectory, string $type, string $size, string $tier): ?array
    {
        if (!$this->isConfigured()) {
            Log::warning('Replicate API token not configured');
            return null;
        }

        $model = $this->modelFor($tier);

        try {
            $input = [
                'prompt'         => $prompt,
                'aspect_ratio'   => $this->aspectRatio($size),
                'output_format'  => 'webp',
                'output_quality' => 90,
            ];

            if (str_contains($model, 'schnell')) {
                $input['num_inference_steps'] = 4;
            } elseif (str_contains($model, 'flux-dev')) {
                $input['num_inference_steps'] = 28;
                $input['guidance'] = 3.5;
            } else {
                // Pro models: moderate safety tolerance avoids false-positive blocks on business imagery
                $input['safety_tolerance'] = 2;
            }

            $response = Http::withToken($this->apiToken)
                ->withHeaders(['Prefer' => 'wait=60'])
                ->timeout(90)
                ->post("{$this->baseUrl}/models/{$model}/predictions", ['input' => $input]);

            if (!$response->successful()) {
                Log::error('Replicate API error', [
                    'model'  => $model,
                    'status' => $response->status(),
                    'body'   => substr($response->body(), 0, 500),
                ]);
                return null;
            }

            $data   = $response->json();
            $status = $data['status'] ?? 'processing';

            if (in_array($status, ['starting', 'processing']) && ($url = $data['urls']['get'] ?? null)) {
                $data   = $this->pollPrediction($url);
                $status = $data['status'] ?? 'failed';
            }

            if ($status !== 'succeeded' || empty($data['output'])) {
                Log::error('Replicate prediction failed', ['model' => $model, 'status' => $status, 'error' => $data['error'] ?? null]);
                return null;
            }

            $imageUrl = is_array($data['output']) ? ($data['output'][0] ?? null) : $data['output'];
            if (!$imageUrl) {
                return null;
            }

            $stored = $this->downloadAndStore($imageUrl, $businessName, $subdirectory, $type);
            if ($stored) {
                $stored['tier']  = $tier;
                $stored['model'] = $model;
                Log::info('Replicate image generated', ['model' => $model, 'tier' => $tier, 'path' => $stored['relative_path']]);
            }

            return $stored;
        } catch (\Throwable $e) {
            Log::error('Replicate generation exception: ' . $e->getMessage(), ['model' => $model]);
            return null;
        }
    }

    protected function pollPrediction(string $url, int $maxAttempts = 30, int $interval = 2): array
    {
        for ($i = 0; $i < $maxAttempts; $i++) {
            sleep($interval);

            $response = Http::withToken($this->apiToken)->timeout(30)->get($url);
            if (!$response->successful()) {
                continue;
            }

            $data = $response->json();
            if (in_array($data['status'] ?? 'processing', ['succeeded', 'failed', 'canceled'])) {
                return $data;
            }
        }

        return ['status' => 'timeout'];
    }

    /** Download the generated image (Replicate URLs expire after ~1h) and store it locally. */
    protected function downloadAndStore(string $imageUrl, string $businessName, string $subdirectory, string $type): ?array
    {
        try {
            $imageResponse = Http::timeout(60)->get($imageUrl);
            if (!$imageResponse->successful()) {
                Log::error('Failed to download Replicate image', ['url' => $imageUrl]);
                return null;
            }

            $ext = pathinfo(parse_url($imageUrl, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION) ?: 'webp';
            $slug = \Illuminate\Support\Str::slug($businessName) ?: 'business';
            $filename = "ai-{$type}-{$slug}-" . time() . '-' . \Illuminate\Support\Str::random(8) . ".{$ext}";
            $relativePath = "marketing/{$subdirectory}/{$filename}";

            Storage::disk('public')->put($relativePath, $imageResponse->body());

            return [
                'public_url'    => asset('storage/' . $relativePath),
                'relative_path' => $relativePath,
                'local_path'    => Storage::disk('public')->path($relativePath),
                'filename'      => $filename,
                'provider'      => 'replicate',
            ];
        } catch (\Throwable $e) {
            Log::error('Replicate image download failed: ' . $e->getMessage());
            return null;
        }
    }
}
