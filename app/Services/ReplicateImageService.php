<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Premium image generation via Replicate (Flux Pro).
 * Used for paid users only — higher quality than the free Pollinations fallback.
 *
 * Costs approximately $0.03-0.06 per image depending on model.
 * Requires REPLICATE_API_TOKEN in .env
 */
class ReplicateImageService
{
    protected string $apiToken;
    protected string $baseUrl = 'https://api.replicate.com/v1';

    public function __construct()
    {
        $this->apiToken = env('REPLICATE_API_TOKEN', config('services.replicate.token', ''));
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiToken);
    }

    /**
     * Generate a high-quality logo via Flux Pro on Replicate.
     */
    public function generateLogo(string $prompt, string $businessName, string $subdirectory = 'logos'): ?array
    {
        return $this->generate($prompt, $businessName, $subdirectory, 'logo', '1024x1024');
    }

    /**
     * Generate a marketing image via Flux Pro on Replicate.
     */
    public function generateMarketingImage(string $prompt, string $businessName, string $size = '1024x1024', string $subdirectory = 'marketing/images'): ?array
    {
        return $this->generate($prompt, $businessName, $subdirectory, 'marketing', $size);
    }

    /**
     * Core generation method.
     */
    protected function generate(string $prompt, string $businessName, string $subdirectory, string $type, string $size = '1024x1024'): ?array
    {
        if (!$this->isConfigured()) {
            Log::warning('Replicate API token not configured');
            return null;
        }

        try {
            [$width, $height] = explode('x', $size) + [1024, 1024];

            // Flux Pro Schnell — fast, high quality, ~$0.003 per image
            // For logos we use Flux Pro (slower, better detail) — ~$0.055 per image
            $model = $type === 'logo'
                ? 'black-forest-labs/flux-pro'
                : 'black-forest-labs/flux-schnell';

            $input = [
                'prompt' => $prompt,
                'width' => (int) $width,
                'height' => (int) $height,
                'output_format' => 'png',
            ];

            if ($type === 'logo') {
                $input['guidance'] = 3.5;
                $input['num_inference_steps'] = 28;
            } else {
                $input['num_inference_steps'] = 4;
            }

            // Create prediction
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
                'Prefer' => 'wait', // Wait for completion (up to 60s)
            ])->timeout(120)->post("{$this->baseUrl}/models/{$model}/predictions", [
                'input' => $input,
            ]);

            if (!$response->successful()) {
                Log::error('Replicate API error', [
                    'status' => $response->status(),
                    'body' => substr($response->body(), 0, 500),
                ]);
                return null;
            }

            $data = $response->json();

            // If prediction is still processing, poll
            $predictionUrl = $data['urls']['get'] ?? null;
            $status = $data['status'] ?? 'processing';

            if ($status === 'processing' && $predictionUrl) {
                $data = $this->pollPrediction($predictionUrl);
                $status = $data['status'] ?? 'failed';
            }

            if ($status !== 'succeeded' || empty($data['output'])) {
                Log::error('Replicate prediction failed', ['status' => $status, 'data' => $data]);
                return null;
            }

            $imageUrl = is_array($data['output']) ? ($data['output'][0] ?? null) : $data['output'];
            if (!$imageUrl) {
                return null;
            }

            // Download and store the image
            return $this->downloadAndStore($imageUrl, $businessName, $subdirectory, $type);

        } catch (\Throwable $e) {
            Log::error('Replicate generation exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Poll a prediction until it completes or times out.
     */
    protected function pollPrediction(string $url, int $maxAttempts = 30, int $interval = 2): array
    {
        for ($i = 0; $i < $maxAttempts; $i++) {
            sleep($interval);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->timeout(30)->get($url);

            if (!$response->successful()) {
                continue;
            }

            $data = $response->json();
            $status = $data['status'] ?? 'processing';

            if (in_array($status, ['succeeded', 'failed', 'canceled'])) {
                return $data;
            }
        }

        return ['status' => 'timeout'];
    }

    /**
     * Download the generated image and store it locally.
     */
    protected function downloadAndStore(string $imageUrl, string $businessName, string $subdirectory, string $type): ?array
    {
        try {
            $imageResponse = Http::timeout(60)->get($imageUrl);
            if (!$imageResponse->successful()) {
                Log::error('Failed to download Replicate image', ['url' => $imageUrl]);
                return null;
            }

            $imageData = $imageResponse->body();
            $slug = \Illuminate\Support\Str::slug($businessName);
            $filename = "ai-{$type}-{$slug}-" . time() . '-' . \Illuminate\Support\Str::random(8) . '.png';
            $relativePath = "marketing/{$subdirectory}/{$filename}";
            $fullPath = "public/{$relativePath}";

            Storage::disk('local')->put($fullPath, $imageData);

            return [
                'public_url' => url('storage/' . $relativePath),
                'relative_path' => $relativePath,
                'local_path' => storage_path('app/' . $fullPath),
                'filename' => $filename,
                'provider' => 'replicate',
            ];
        } catch (\Throwable $e) {
            Log::error('Replicate image download failed: ' . $e->getMessage());
            return null;
        }
    }
}
