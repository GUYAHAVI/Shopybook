<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CloudLTXVideoService
{
    private $apiEndpoint;
    private $apiKey;
    private $outputPath;

    public function __construct()
    {
        $this->apiEndpoint = config('services.ltx_video.api_endpoint');
        $this->apiKey = config('services.ltx_video.api_key');
        $this->outputPath = storage_path('app/public/generated_videos');
        
        // Ensure directories exist
        if (!file_exists($this->outputPath)) {
            mkdir($this->outputPath, 0755, true);
        }
    }

    /**
     * Generate video from text content and business branding using cloud API
     *
     * @param string $content The post content
     * @param array $businessBranding Business branding information
     * @param string|null $productImage Optional product image for image-to-video
     * @param array $options Generation options
     * @return array
     */
    public function generateMarketingVideo($content, $businessBranding, $productImage = null, $options = [])
    {
        try {
            // Choose deployment strategy based on environment
            if ($this->shouldUseCloudAPI()) {
                return $this->generateViaCloudAPI($content, $businessBranding, $productImage, $options);
            } else {
                return $this->generateViaLocalAPI($content, $businessBranding, $productImage, $options);
            }
            
        } catch (\Exception $e) {
            Log::error('LTX Video generation failed', [
                'error' => $e->getMessage(),
                'content' => $content,
                'business' => $businessBranding['name'] ?? 'Unknown'
            ]);
            
            return [
                'success' => false,
                'error' => 'Video generation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generate video using cloud-based API (Replicate, Fal.ai, etc.)
     */
    private function generateViaCloudAPI($content, $businessBranding, $productImage, $options)
    {
        $startTime = microtime(true);
        
        // Generate enhanced prompt
        $prompt = $this->createEnhancedPrompt($content, $businessBranding, $options);
        
        // Prepare API payload
        $payload = [
            'prompt' => $prompt,
            'height' => $options['height'] ?? 704,
            'width' => $options['width'] ?? 1216,
            'num_frames' => $options['num_frames'] ?? 121,
            'seed' => $options['seed'] ?? rand(1000, 9999),
            'model' => $options['model'] ?? 'ltxv-13b-0.9.7-distilled'
        ];

        // Add image if provided (for image-to-video)
        if ($productImage) {
            $payload['image'] = base64_encode(file_get_contents($productImage));
        }

        // Choose API provider based on configuration
        $provider = config('services.ltx_video.provider', 'replicate');
        
        switch ($provider) {
            case 'replicate':
                return $this->generateViaReplicate($payload, $startTime);
            case 'fal':
                return $this->generateViaFal($payload, $startTime);
            case 'huggingface':
                return $this->generateViaHuggingFace($payload, $startTime);
            default:
                return $this->generateViaMockAPI($payload, $startTime);
        }
    }

    /**
     * Generate video using Replicate API
     */
    private function generateViaReplicate($payload, $startTime)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Token ' . config('services.replicate.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.replicate.com/v1/predictions', [
                'version' => config('services.ltx_video.replicate_version'),
                'input' => $payload
            ]);

            if (!$response->successful()) {
                throw new \Exception('Replicate API error: ' . $response->body());
            }

            $prediction = $response->json();
            $predictionId = $prediction['id'];

            // Poll for completion
            $maxAttempts = 60; // 5 minutes max
            $attempts = 0;
            
            do {
                sleep(5); // Wait 5 seconds between checks
                $statusResponse = Http::withHeaders([
                    'Authorization' => 'Token ' . config('services.replicate.api_key'),
                ])->get("https://api.replicate.com/v1/predictions/{$predictionId}");

                $status = $statusResponse->json();
                $attempts++;

                if ($status['status'] === 'succeeded') {
                    return $this->downloadAndSaveVideo($status['output'], $startTime);
                } elseif ($status['status'] === 'failed') {
                    throw new \Exception('Video generation failed: ' . ($status['error'] ?? 'Unknown error'));
                }
            } while ($status['status'] === 'starting' || $status['status'] === 'processing' && $attempts < $maxAttempts);

            throw new \Exception('Video generation timed out');

        } catch (\Exception $e) {
            Log::error('Replicate API error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate video using Fal.ai API
     */
    private function generateViaFal($payload, $startTime)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Key ' . config('services.fal.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://fal.run/fal-ai/ltx-video-13b-distilled/image-to-video', $payload);

            if (!$response->successful()) {
                throw new \Exception('Fal.ai API error: ' . $response->body());
            }

            $result = $response->json();
            
            if (isset($result['video']['url'])) {
                return $this->downloadAndSaveVideo($result['video']['url'], $startTime);
            }

            throw new \Exception('No video URL returned from Fal.ai');

        } catch (\Exception $e) {
            Log::error('Fal.ai API error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate video using HuggingFace Inference API
     */
    private function generateViaHuggingFace($payload, $startTime)
    {
        try {
            // Prepare HuggingFace-specific input format
            $hfPayload = [
                'inputs' => $payload['prompt']
            ];

            // Add optional parameters based on LTX-Video model capabilities
            $parameters = [];
            if (isset($payload['height'])) $parameters['height'] = $payload['height'];
            if (isset($payload['width'])) $parameters['width'] = $payload['width'];
            if (isset($payload['num_frames'])) $parameters['num_frames'] = $payload['num_frames'];
            if (isset($payload['seed'])) $parameters['seed'] = $payload['seed'];
            
            if (!empty($parameters)) {
                $hfPayload['parameters'] = $parameters;
            }

            // Add image input for image-to-video generation
            if (isset($payload['image'])) {
                $hfPayload['image'] = $payload['image'];
            }

            Log::info('Sending request to HuggingFace', [
                'payload' => $hfPayload,
                'prompt' => $payload['prompt']
            ]);

            $response = Http::timeout(300) // 5 minutes timeout for video generation
                ->withHeaders([
                    'Authorization' => 'Bearer REMOVED_SECRET_TOKEN',
                    'Content-Type' => 'application/json',
                ])->post('https://api-inference.huggingface.co/models/Lightricks/LTX-Video', $hfPayload);

            if (!$response->successful()) {
                $errorBody = $response->body();
                Log::error('HuggingFace API HTTP error', [
                    'status' => $response->status(),
                    'body' => $errorBody
                ]);
                throw new \Exception('HuggingFace API error: ' . $errorBody);
            }

            // Check if response is binary video data or JSON
            $contentType = $response->header('Content-Type');
            
            if (strpos($contentType, 'video/') === 0 || strpos($contentType, 'application/octet-stream') === 0) {
                // Direct video binary response
                return $this->saveVideoFromBinary($response->body(), $startTime);
            } else {
                // JSON response
                $result = $response->json();
                
                Log::info('HuggingFace response', ['result' => $result]);
                
                // Handle different possible response formats
                if (isset($result['video_url'])) {
                    return $this->downloadAndSaveVideo($result['video_url'], $startTime);
                } elseif (isset($result[0]['generated_video'])) {
                    return $this->downloadAndSaveVideo($result[0]['generated_video'], $startTime);
                } elseif (isset($result['url'])) {
                    return $this->downloadAndSaveVideo($result['url'], $startTime);
                } elseif (isset($result['error'])) {
                    throw new \Exception('HuggingFace API error: ' . $result['error']);
                } else {
                    Log::error('Unexpected HuggingFace response format', ['result' => $result]);
                    throw new \Exception('Unexpected response format from HuggingFace API');
                }
            }

        } catch (\Exception $e) {
            Log::error('HuggingFace API error', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Mock API for development/testing
     */
    private function generateViaMockAPI($payload, $startTime)
    {
        // Simulate processing time
        sleep(2);
        
        // Generate a mock video ID
        $videoId = Str::uuid();
        $outputFile = $this->outputPath . '/' . $videoId . '.mp4';
        
        // Try to use existing sample videos
        $sampleVideos = [
            public_path('images/Graduation.mp4'),
            public_path('images/running_jump.mp4')
        ];
        
        $mockVideoPath = null;
        foreach ($sampleVideos as $samplePath) {
            if (file_exists($samplePath)) {
                $mockVideoPath = $samplePath;
                break;
            }
        }
        
        if ($mockVideoPath && file_exists($mockVideoPath)) {
            // Copy sample video as mock generated video
            copy($mockVideoPath, $outputFile);
            
            // Log for debugging
            Log::info('Mock video generated', [
                'source' => $mockVideoPath,
                'destination' => $outputFile,
                'file_exists' => file_exists($outputFile),
                'file_size' => file_exists($outputFile) ? filesize($outputFile) : 0
            ]);
            
            $endTime = microtime(true);
            
            // Use Storage::url() for proper URL generation
            $publicUrl = Storage::url('generated_videos/' . $videoId . '.mp4');
            
            return [
                'success' => true,
                'video_id' => $videoId,
                'file_path' => $outputFile,
                'public_url' => $publicUrl,
                'duration' => 4.0, // Mock duration
                'prompt_used' => $payload['prompt'],
                'generation_time' => round($endTime - $startTime, 2),
                'provider' => 'mock',
                'debug_info' => [
                    'output_file' => $outputFile,
                    'public_url' => $publicUrl,
                    'source_video' => $mockVideoPath
                ]
            ];
        } else {
            // If no sample video found, create a simple HTML5 compatible video using FFmpeg if available
            // For now, return success but indicate no video was created
            return [
                'success' => false,
                'error' => 'Mock video generation failed: No sample video available. Please add a sample video to public/images/ or configure a real API provider.'
            ];
        }
    }

    /**
     * Download video from URL and save locally
     */
    private function downloadAndSaveVideo($videoUrl, $startTime)
    {
        try {
            $videoId = Str::uuid();
            $outputFile = $this->outputPath . '/' . $videoId . '.mp4';
            
            // Download the video
            $videoContent = Http::get($videoUrl)->body();
            file_put_contents($outputFile, $videoContent);
            
            $endTime = microtime(true);
            
            return [
                'success' => true,
                'video_id' => $videoId,
                'file_path' => $outputFile,
                'public_url' => Storage::url('generated_videos/' . $videoId . '.mp4'),
                'duration' => $this->getVideoDuration($outputFile),
                'generation_time' => round($endTime - $startTime, 2)
            ];
            
        } catch (\Exception $e) {
            throw new \Exception('Failed to download video: ' . $e->getMessage());
        }
    }

    /**
     * Save video from binary data
     */
    private function saveVideoFromBinary($binaryData, $startTime)
    {
        try {
            $videoId = Str::uuid();
            $outputFile = $this->outputPath . '/' . $videoId . '.mp4';
            
            // Save binary data directly
            file_put_contents($outputFile, $binaryData);
            
            $endTime = microtime(true);
            
            return [
                'success' => true,
                'video_id' => $videoId,
                'file_path' => $outputFile,
                'public_url' => Storage::url('generated_videos/' . $videoId . '.mp4'),
                'duration' => $this->getVideoDuration($outputFile),
                'generation_time' => round($endTime - $startTime, 2),
                'provider' => 'huggingface'
            ];
            
        } catch (\Exception $e) {
            throw new \Exception('Failed to save video from binary data: ' . $e->getMessage());
        }
    }

    /**
     * Generate video using local API (fallback for development)
     */
    private function generateViaLocalAPI($content, $businessBranding, $productImage, $options)
    {
        // Fallback to local LTX-Video service if available
        $localService = new LTXVideoService();
        return $localService->generateMarketingVideo($content, $businessBranding, $productImage, $options);
    }

    /**
     * Determine if we should use cloud API based on environment
     */
    private function shouldUseCloudAPI()
    {
        // Use cloud API in production or when local path is not available
        return app()->environment('production') || 
               !config('services.ltx_video.path') || 
               !file_exists(config('services.ltx_video.path'));
    }

    /**
     * Create enhanced prompt for video generation
     */
    private function createEnhancedPrompt($content, $businessBranding, $options)
    {
        $businessName = $businessBranding['name'] ?? 'Business';
        $businessType = $businessBranding['type'] ?? 'retail';
        $style = $options['style'] ?? 'professional';
        
        // Extract key themes from content
        $themes = $this->extractContentThemes($content);
        
        // Style-based prompt templates
        $stylePrompts = [
            'professional' => "Professional business video showcasing {$businessName}, a {$businessType} company. Clean, modern aesthetic with corporate styling.",
            'dynamic' => "Dynamic, energetic marketing video for {$businessName}. Fast-paced with smooth transitions and vibrant colors.",
            'minimal' => "Minimalist, elegant video presentation for {$businessName}. Simple, clean design with focus on content.",
            'creative' => "Creative, artistic video content for {$businessName}. Unique visual storytelling with innovative elements.",
            'social' => "Social media optimized video for {$businessName}. Engaging, shareable content perfect for platforms."
        ];
        
        $basePrompt = $stylePrompts[$style] ?? $stylePrompts['professional'];
        
        // Add theme-specific elements
        $themeElements = [];
        if (in_array('product', $themes)) {
            $themeElements[] = "featuring product showcase with detailed close-ups";
        }
        if (in_array('promotion', $themes)) {
            $themeElements[] = "highlighting special offers and promotional content";
        }
        if (in_array('service', $themes)) {
            $themeElements[] = "demonstrating service capabilities and benefits";
        }
        if (in_array('announcement', $themes)) {
            $themeElements[] = "announcing important news and updates";
        }
        
        // Build final prompt
        $prompt = $basePrompt;
        if (!empty($themeElements)) {
            $prompt .= " " . implode(", ", $themeElements) . ".";
        }
        
        // Add visual specifications
        $prompt .= " High-quality cinematic footage with professional lighting and composition.";
        $prompt .= " Brand colors incorporated naturally throughout the scene.";
        $prompt .= " Duration optimized for social media engagement.";
        $prompt .= " Camera movements: smooth pans and subtle zooms.";
        $prompt .= " Text overlay areas left clear for post-production editing.";
        
        return $prompt;
    }

    /**
     * Extract themes from content
     */
    private function extractContentThemes($content)
    {
        $themes = [];
        $content = strtolower($content);
        
        // Product-related keywords
        if (preg_match('/\b(product|item|buy|purchase|shop|store|sale)\b/', $content)) {
            $themes[] = 'product';
        }
        
        // Promotion-related keywords
        if (preg_match('/\b(discount|offer|deal|promo|special|save|\d+%|free)\b/', $content)) {
            $themes[] = 'promotion';
        }
        
        // Service-related keywords
        if (preg_match('/\b(service|help|support|consultation|booking|appointment)\b/', $content)) {
            $themes[] = 'service';
        }
        
        // Announcement keywords
        if (preg_match('/\b(announce|news|update|launch|new|coming soon)\b/', $content)) {
            $themes[] = 'announcement';
        }
        
        return $themes;
    }

    /**
     * Calculate video duration in seconds
     */
    private function calculateDuration($numFrames, $fps = 30)
    {
        return round($numFrames / $fps, 1);
    }

    /**
     * Get actual video duration from file
     */
    private function getVideoDuration($filePath)
    {
        // Simple fallback - you can integrate FFmpeg here for accurate duration
        return 4.0; // Default to 4 seconds
    }

    /**
     * Get available video styles
     */
    public function getAvailableStyles()
    {
        return [
            'professional' => [
                'name' => 'Professional',
                'description' => 'Clean, corporate styling perfect for business content',
                'icon' => 'fas fa-briefcase',
                'color' => '#007bff'
            ],
            'dynamic' => [
                'name' => 'Dynamic',
                'description' => 'Energetic and fast-paced for exciting announcements',
                'icon' => 'fas fa-bolt',
                'color' => '#fd7e14'
            ],
            'minimal' => [
                'name' => 'Minimal',
                'description' => 'Simple and elegant for sophisticated brands',
                'icon' => 'fas fa-minus',
                'color' => '#6c757d'
            ],
            'creative' => [
                'name' => 'Creative',
                'description' => 'Artistic and unique for creative businesses',
                'icon' => 'fas fa-palette',
                'color' => '#e83e8c'
            ],
            'social' => [
                'name' => 'Social Media',
                'description' => 'Optimized for social platforms and engagement',
                'icon' => 'fas fa-share-alt',
                'color' => '#20c997'
            ]
        ];
    }

    /**
     * Clean up old generated videos
     */
    public function cleanupOldVideos($daysOld = 7)
    {
        $files = glob($this->outputPath . '/*.mp4');
        $cutoffTime = time() - ($daysOld * 24 * 60 * 60);
        $deletedCount = 0;
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoffTime) {
                unlink($file);
                $deletedCount++;
            }
        }
        
        return $deletedCount;
    }
}
