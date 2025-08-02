<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LTXVideoService
{
    private $pythonScriptPath;
    private $ltxVideoPath;
    private $outputPath;

    public function __construct()
    {
        $this->pythonScriptPath = storage_path('app/python');
        $this->ltxVideoPath = config('services.ltx_video.path');
        $this->outputPath = storage_path('app/public/generated_videos');
        
        // Ensure directories exist
        if (!file_exists($this->pythonScriptPath)) {
            mkdir($this->pythonScriptPath, 0755, true);
        }
        
        if (!file_exists($this->outputPath)) {
            mkdir($this->outputPath, 0755, true);
        }
    }

    /**
     * Generate video from text content and business branding
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
            // Generate enhanced prompt based on content and branding
            $prompt = $this->createEnhancedPrompt($content, $businessBranding, $options);
            
            // Prepare generation parameters
            $params = array_merge([
                'prompt' => $prompt,
                'height' => 704,
                'width' => 1216,
                'num_frames' => 121, // ~4 seconds at 30fps
                'seed' => rand(1000, 9999),
                'model_config' => 'ltxv-13b-0.9.7-distilled', // Fast model for real-time generation
            ], $options);
            
            // Generate unique output filename
            $videoId = Str::uuid();
            $outputFile = $this->outputPath . '/' . $videoId . '.mp4';
            
            if ($productImage) {
                // Image-to-video generation
                $result = $this->generateImageToVideo($productImage, $params, $outputFile);
            } else {
                // Text-to-video generation
                $result = $this->generateTextToVideo($params, $outputFile);
            }
            
            if ($result['success']) {
                return [
                    'success' => true,
                    'video_id' => $videoId,
                    'file_path' => $outputFile,
                    'public_url' => Storage::url('generated_videos/' . $videoId . '.mp4'),
                    'duration' => $this->calculateDuration($params['num_frames']),
                    'prompt_used' => $prompt,
                    'generation_time' => $result['generation_time'] ?? null
                ];
            }
            
            return [
                'success' => false,
                'error' => $result['error'] ?? 'Unknown error occurred'
            ];
            
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
     * Create enhanced prompt for video generation
     */
    private function createEnhancedPrompt($content, $businessBranding, $options)
    {
        $businessName = $businessBranding['name'] ?? 'Business';
        $businessType = $businessBranding['type'] ?? 'retail';
        $brandColors = $businessBranding['colors'] ?? ['#007bff', '#ffffff'];
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
     * Generate video from text prompt
     */
    private function generateTextToVideo($params, $outputFile)
    {
        $startTime = microtime(true);
        
        // Create Python script for text-to-video generation
        $scriptContent = $this->createPythonScript('text_to_video', $params, $outputFile);
        $scriptFile = $this->pythonScriptPath . '/generate_video_' . uniqid() . '.py';
        file_put_contents($scriptFile, $scriptContent);
        
        try {
            // Execute Python script
            $command = "cd {$this->ltxVideoPath} && python {$scriptFile}";
            $output = shell_exec($command . ' 2>&1');
            
            // Clean up script file
            unlink($scriptFile);
            
            // Check if video was generated successfully
            if (file_exists($outputFile)) {
                $endTime = microtime(true);
                return [
                    'success' => true,
                    'generation_time' => round($endTime - $startTime, 2)
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Video file not generated. Output: ' . $output
                ];
            }
            
        } catch (\Exception $e) {
            // Clean up script file
            if (file_exists($scriptFile)) {
                unlink($scriptFile);
            }
            
            throw $e;
        }
    }

    /**
     * Generate video from image
     */
    private function generateImageToVideo($imagePath, $params, $outputFile)
    {
        $startTime = microtime(true);
        
        // Create Python script for image-to-video generation
        $scriptContent = $this->createPythonScript('image_to_video', $params, $outputFile, $imagePath);
        $scriptFile = $this->pythonScriptPath . '/generate_i2v_' . uniqid() . '.py';
        file_put_contents($scriptFile, $scriptContent);
        
        try {
            // Execute Python script
            $command = "cd {$this->ltxVideoPath} && python {$scriptFile}";
            $output = shell_exec($command . ' 2>&1');
            
            // Clean up script file
            unlink($scriptFile);
            
            // Check if video was generated successfully
            if (file_exists($outputFile)) {
                $endTime = microtime(true);
                return [
                    'success' => true,
                    'generation_time' => round($endTime - $startTime, 2)
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Video file not generated. Output: ' . $output
                ];
            }
            
        } catch (\Exception $e) {
            // Clean up script file
            if (file_exists($scriptFile)) {
                unlink($scriptFile);
            }
            
            throw $e;
        }
    }

    /**
     * Create Python script for video generation
     */
    private function createPythonScript($type, $params, $outputFile, $inputImage = null)
    {
        $script = "#!/usr/bin/env python3\n";
        $script .= "import sys\n";
        $script .= "import os\n";
        $script .= "from ltx_video.inference import infer, InferenceConfig\n\n";
        
        if ($type === 'image_to_video' && $inputImage) {
            $script .= "# Image-to-Video Generation\n";
            $script .= "config = InferenceConfig(\n";
            $script .= "    pipeline_config=\"configs/{$params['model_config']}.yaml\",\n";
            $script .= "    prompt=\"{$params['prompt']}\",\n";
            $script .= "    conditioning_media_paths=[\"{$inputImage}\"],\n";
            $script .= "    conditioning_start_frames=[0],\n";
            $script .= "    height={$params['height']},\n";
            $script .= "    width={$params['width']},\n";
            $script .= "    num_frames={$params['num_frames']},\n";
            $script .= "    seed={$params['seed']},\n";
            $script .= "    output_path=\"{$outputFile}\"\n";
            $script .= ")\n\n";
        } else {
            $script .= "# Text-to-Video Generation\n";
            $script .= "config = InferenceConfig(\n";
            $script .= "    pipeline_config=\"configs/{$params['model_config']}.yaml\",\n";
            $script .= "    prompt=\"{$params['prompt']}\",\n";
            $script .= "    height={$params['height']},\n";
            $script .= "    width={$params['width']},\n";
            $script .= "    num_frames={$params['num_frames']},\n";
            $script .= "    seed={$params['seed']},\n";
            $script .= "    output_path=\"{$outputFile}\"\n";
            $script .= ")\n\n";
        }
        
        $script .= "try:\n";
        $script .= "    print(\"Starting video generation...\")\n";
        $script .= "    infer(config)\n";
        $script .= "    print(f\"Video generated successfully: {$outputFile}\")\n";
        $script .= "except Exception as e:\n";
        $script .= "    print(f\"Error generating video: {str(e)}\")\n";
        $script .= "    sys.exit(1)\n";
        
        return $script;
    }

    /**
     * Calculate video duration in seconds
     */
    private function calculateDuration($numFrames, $fps = 30)
    {
        return round($numFrames / $fps, 1);
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

